<?php

namespace App\Services\UserChat;

use App\Events\UserChatDeletedEvent;
use App\Events\UserChatDeliveredEvent;
use App\Events\UserChatDisappearingEvent;
use App\Events\UserChatEditedEvent;
use App\Events\UserChatEvent;
use App\Events\UserChatPinnedEvent;
use App\Events\UserChatReactionEvent;
use App\Events\UserChatTypingEvent;
use App\Models\UserChat;
use App\Models\UserChatFavorite;
use App\Models\UserChatReaction;
use App\Models\UserChatSetting;
use App\Services\UserChatService;
use App\Traits\imageTrait;
use Illuminate\Validation\ValidationException;

class UserChatActionService
{
    use imageTrait;

    protected $chatService;

    public function __construct(UserChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Find a visible message and assert the actor has access to its conversation.
     */
    protected function findAuthorizedMessage($actor, int $messageId): UserChat
    {
        $chat = UserChat::with(['replyTo', 'reactions', 'favorites'])->findOrFail($messageId);
        $identity = UserChatFormatter::resolveActorIdentity($actor);

        if (!$this->chatService->checkPermission($actor, $chat->user_id)) {
            abort(403, 'غير مصرح لك بالوصول لهذه الرسالة.');
        }

        $deletedForCaller = $identity['is_admin_side'] ? $chat->deleted_for_admin : $chat->deleted_for_user;
        if ($deletedForCaller || ($chat->expires_at && $chat->expires_at->isPast())) {
            abort(404, 'الرسالة غير موجودة.');
        }

        return $chat;
    }

    /**
     * Resolve conversation user_id and verify actor permission.
     */
    protected function resolveAuthorizedConversation($actor, ?int $targetUserId): array
    {
        $identity = UserChatFormatter::resolveActorIdentity($actor);
        $conversationUserId = $identity['is_admin_side'] ? $targetUserId : $identity['id'];

        if (empty($conversationUserId)) {
            throw ValidationException::withMessages([
                'user_id' => ['حقل user_id مطلوب للأدمن أو الموظف.']
            ]);
        }

        if (!$this->chatService->checkPermission($actor, $conversationUserId)) {
            abort(403, 'غير مصرح لك بالوصول لهذه المحادثة.');
        }

        return [$identity, (int) $conversationUserId];
    }

    /**
     * Mark incoming messages as delivered and notify sender in realtime.
     */
    public function markDelivered($actor, ?int $targetUserId = null, array $messageIds = []): array
    {
        [$identity, $conversationUserId] = $this->resolveAuthorizedConversation($actor, $targetUserId);

        $query = UserChat::where('user_id', $conversationUserId)
            ->where('is_admin', !$identity['is_admin_side'])
            ->where('is_delivered', false)
            ->whereNull('deleted_for_everyone_at');

        if (!empty($messageIds)) {
            $query->whereIn('id', $messageIds);
        }

        $idsToUpdate = $query->pluck('id')->all();
        $now = now();

        if (!empty($idsToUpdate)) {
            UserChat::whereIn('id', $idsToUpdate)->update([
                'is_delivered' => true,
                'delivered_at' => $now,
            ]);

            UserChatDeliveredEvent::dispatch(
                $conversationUserId,
                $idsToUpdate,
                $identity['is_admin_side'] ? 'admin' : 'user',
                $now->toIso8601String()
            );
        }

        return [
            'user_id'       => $conversationUserId,
            'message_ids'   => array_map('intval', $idsToUpdate),
            'updated_count' => count($idsToUpdate),
            'delivered_at'  => $now->toIso8601String(),
        ];
    }

    /**
     * Add, change, or remove (toggle) an emoji reaction on a message.
     */
    public function react($actor, int $messageId, ?string $emoji): array
    {
        $chat = $this->findAuthorizedMessage($actor, $messageId);
        if (!is_null($chat->deleted_for_everyone_at)) {
            abort(422, 'لا يمكن التفاعل على رسالة محذوفة.');
        }

        $identity = UserChatFormatter::resolveActorIdentity($actor);
        $existing = UserChatReaction::where('user_chat_id', $chat->id)
            ->where('actor_type', $identity['type'])
            ->where('actor_id', $identity['id'])
            ->first();

        $appliedEmoji = null;

        if (empty($emoji) || ($existing && $existing->emoji === $emoji)) {
            if ($existing) {
                $existing->delete();
            }
        } else {
            UserChatReaction::updateOrCreate(
                [
                    'user_chat_id' => $chat->id,
                    'actor_type'   => $identity['type'],
                    'actor_id'     => $identity['id'],
                ],
                ['emoji' => $emoji]
            );
            $appliedEmoji = $emoji;
        }

        $freshReactions = UserChatReaction::where('user_chat_id', $chat->id)->get();
        $formatted = UserChatFormatter::formatReactions($freshReactions, $identity['type'], $identity['id']);

        UserChatReactionEvent::dispatch(
            $chat->user_id,
            $chat->id,
            $identity['type'],
            $identity['id'],
            $appliedEmoji,
            $formatted['reactions']
        );

        return [
            'message_id'  => (int) $chat->id,
            'reactions'   => $formatted['reactions'],
            'my_reaction' => $formatted['my_reaction'],
        ];
    }

    /**
     * Edit message text. Admin can edit anytime; User/Employee within configured window.
     */
    public function editMessage($actor, int $messageId, string $newMsg): array
    {
        $chat = $this->findAuthorizedMessage($actor, $messageId);
        if (!is_null($chat->deleted_for_everyone_at)) {
            abort(422, 'لا يمكن تعديل رسالة محذوفة.');
        }

        $identity = UserChatFormatter::resolveActorIdentity($actor);

        if ($identity['role'] === 'admin') {
            if (!$chat->is_admin) {
                abort(403, 'لا يمكن تعديل رسالة مرسلة من العميل.');
            }
        } elseif ($identity['role'] === 'employee') {
            if (!$chat->is_admin || (int) $chat->admin_id !== $identity['id']) {
                abort(403, 'يمكنك تعديل رسائلك فقط.');
            }
            $this->assertWithinWindow($chat, (int) config('chat.edit_window_minutes', 15), 'تعديل');
        } else {
            if ($chat->is_admin || (int) $chat->user_id !== $identity['id']) {
                abort(403, 'يمكنك تعديل رسائلك فقط.');
            }
            $this->assertWithinWindow($chat, (int) config('chat.edit_window_minutes', 15), 'تعديل');
        }

        $now = now();
        $chat->update([
            'msg'       => $newMsg,
            'is_edited' => true,
            'edited_at' => $now,
        ]);

        UserChatEditedEvent::dispatch($chat->user_id, $chat->id, $newMsg, $now->toIso8601String());

        $chat->load(['replyTo', 'reactions', 'favorites']);

        return UserChatFormatter::formatMessage($chat, $identity['type'], $identity['id']);
    }

    /**
     * Delete message: "me" (hides for user or all admin staff) or "everyone".
     */
    public function deleteMessage($actor, int $messageId, string $scope): array
    {
        $chat = $this->findAuthorizedMessage($actor, $messageId);
        $identity = UserChatFormatter::resolveActorIdentity($actor);

        if ($scope === 'me') {
            $column = $identity['is_admin_side'] ? 'deleted_for_admin' : 'deleted_for_user';
            $chat->update([$column => true]);

            return [
                'message_id' => (int) $chat->id,
                'scope'      => 'me',
            ];
        }

        if (!is_null($chat->deleted_for_everyone_at)) {
            abort(422, 'الرسالة محذوفة بالفعل للجميع.');
        }

        if ($identity['role'] !== 'admin') {
            $isOwnMessage = $identity['is_admin_side']
                ? ($chat->is_admin && (int) $chat->admin_id === $identity['id'])
                : (!$chat->is_admin && (int) $chat->user_id === $identity['id']);

            if (!$isOwnMessage) {
                abort(403, 'لا يمكنك حذف رسالة الطرف الآخر للجميع.');
            }

            $this->assertWithinWindow($chat, (int) config('chat.delete_everyone_window_minutes', 60), 'حذف');
        }

        $this->deleteImage($chat->image);
        $this->deleteImage($chat->voice);

        $now = now();
        $chat->update([
            'msg'                     => null,
            'image'                   => null,
            'voice'                   => null,
            'deleted_for_everyone_at' => $now,
            'deleted_by_type'         => $identity['type'],
            'deleted_by_id'           => $identity['id'],
        ]);

        UserChatDeletedEvent::dispatch($chat->user_id, $chat->id, $now->toIso8601String());

        return [
            'message_id' => (int) $chat->id,
            'scope'      => 'everyone',
            'deleted_at' => $now->toIso8601String(),
        ];
    }

    /**
     * Forward a message to one or more target conversations.
     */
    public function forwardMessage($actor, int $messageId, array $targetUserIds): array
    {
        $source = $this->findAuthorizedMessage($actor, $messageId);
        if (!is_null($source->deleted_for_everyone_at)) {
            abort(422, 'لا يمكن إعادة توجيه رسالة محذوفة.');
        }

        $identity = UserChatFormatter::resolveActorIdentity($actor);

        if (!$identity['is_admin_side']) {
            $targetUserIds = [$identity['id']];
        }

        $targetUserIds = array_values(array_unique(array_map('intval', $targetUserIds)));
        if (empty($targetUserIds)) {
            throw ValidationException::withMessages([
                'target_user_ids' => ['يجب تحديد محادثة واحدة على الأقل لإعادة التوجيه.']
            ]);
        }

        foreach ($targetUserIds as $targetId) {
            if (!$this->chatService->checkPermission($actor, $targetId)) {
                abort(403, "غير مصرح لك بإعادة التوجيه إلى المستخدم رقم {$targetId}.");
            }
        }

        $created = [];
        foreach ($targetUserIds as $targetId) {
            $expiresAt = $this->chatService->resolveExpiresAt($targetId);

            $forwarded = UserChat::create([
                'user_id'           => $targetId,
                'admin_id'          => $identity['is_admin_side'] ? $identity['id'] : null,
                'is_admin'          => $identity['is_admin_side'],
                'is_forwarded'      => true,
                'forwarded_from_id' => $source->id,
                'msg'               => $source->msg,
                'image'             => $source->image,
                'voice'             => $source->voice,
                'is_admin_read'     => $identity['is_admin_side'],
                'admin_read_at'     => $identity['is_admin_side'] ? now() : null,
                'is_user_read'      => !$identity['is_admin_side'],
                'user_read_at'      => !$identity['is_admin_side'] ? now() : null,
                'expires_at'        => $expiresAt,
            ]);

            UserChatEvent::dispatch($forwarded);
            $created[] = UserChatFormatter::formatMessage($forwarded, $identity['type'], $identity['id']);
        }

        return $created;
    }

    /**
     * Pin or unpin a message (available to both User and Admin/Employee).
     */
    public function togglePin($actor, int $messageId, ?bool $pin = null): array
    {
        $chat = $this->findAuthorizedMessage($actor, $messageId);
        if (!is_null($chat->deleted_for_everyone_at)) {
            abort(422, 'لا يمكن تثبيت رسالة محذوفة.');
        }

        $identity = UserChatFormatter::resolveActorIdentity($actor);
        $nextState = is_null($pin) ? !$chat->is_pinned : $pin;
        $now = $nextState ? now() : null;

        $chat->update([
            'is_pinned'      => $nextState,
            'pinned_at'      => $now,
            'pinned_by_type' => $nextState ? $identity['type'] : null,
            'pinned_by_id'   => $nextState ? $identity['id'] : null,
        ]);

        UserChatPinnedEvent::dispatch(
            $chat->user_id,
            $chat->id,
            $nextState,
            $now ? $now->toIso8601String() : null
        );

        return [
            'message_id' => (int) $chat->id,
            'is_pinned'  => $nextState,
            'pinned_at'  => $now ? $now->toIso8601String() : null,
        ];
    }

    /**
     * Get pinned messages in a conversation.
     */
    public function getPinnedMessages($actor, ?int $targetUserId = null): array
    {
        [$identity, $conversationUserId] = $this->resolveAuthorizedConversation($actor, $targetUserId);

        return UserChat::where('user_id', $conversationUserId)
            ->visibleFor($identity['is_admin_side'])
            ->whereNull('deleted_for_everyone_at')
            ->where('is_pinned', true)
            ->with(['replyTo', 'reactions', 'favorites'])
            ->orderBy('pinned_at', 'desc')
            ->get()
            ->map(fn (UserChat $m) => UserChatFormatter::formatMessage($m, $identity['type'], $identity['id']))
            ->values()
            ->all();
    }

    /**
     * Toggle favorite/star on a message (private to the caller).
     */
    public function toggleFavorite($actor, int $messageId): array
    {
        $chat = $this->findAuthorizedMessage($actor, $messageId);
        $identity = UserChatFormatter::resolveActorIdentity($actor);

        $existing = UserChatFavorite::where('user_chat_id', $chat->id)
            ->where('actor_type', $identity['type'])
            ->where('actor_id', $identity['id'])
            ->first();

        if ($existing) {
            $existing->delete();
            $isFavorite = false;
        } else {
            UserChatFavorite::create([
                'user_chat_id' => $chat->id,
                'actor_type'   => $identity['type'],
                'actor_id'     => $identity['id'],
            ]);
            $isFavorite = true;
        }

        return [
            'message_id'  => (int) $chat->id,
            'is_favorite' => $isFavorite,
        ];
    }

    /**
     * Get caller's favorite messages.
     */
    public function getFavoriteMessages($actor, ?int $targetUserId = null, int $perPage = 30)
    {
        $identity = UserChatFormatter::resolveActorIdentity($actor);

        $query = UserChat::whereHas('favorites', function ($q) use ($identity) {
            $q->where('actor_type', $identity['type'])
              ->where('actor_id', $identity['id']);
        })
            ->visibleFor($identity['is_admin_side'])
            ->with(['replyTo', 'reactions', 'favorites']);

        if ($identity['is_admin_side']) {
            if (!empty($targetUserId)) {
                if (!$this->chatService->checkPermission($actor, $targetUserId)) {
                    abort(403, 'غير مصرح لك بعرض مفضلة هذه المحادثة.');
                }
                $query->where('user_id', $targetUserId);
            } elseif ($identity['role'] === 'employee') {
                $allowedIds = $this->chatService->getAllowedUserIdsForEmployee($identity['id']);
                $query->whereIn('user_id', $allowedIds);
            }
        } else {
            $query->where('user_id', $identity['id']);
        }

        return $query->orderBy('id', 'desc')
            ->paginate($perPage)
            ->through(fn (UserChat $m) => UserChatFormatter::formatMessage($m, $identity['type'], $identity['id']));
    }

    /**
     * Update disappearing messages timer for a conversation.
     */
    public function updateDisappearingSettings($actor, ?int $targetUserId, ?int $seconds): array
    {
        [$identity, $conversationUserId] = $this->resolveAuthorizedConversation($actor, $targetUserId);

        $setting = UserChatSetting::updateOrCreate(
            ['user_id' => $conversationUserId],
            [
                'disappearing_seconds' => $seconds,
                'updated_by_type'      => $identity['type'],
                'updated_by_id'        => $identity['id'],
            ]
        );

        UserChatDisappearingEvent::dispatch($conversationUserId, $setting->disappearing_seconds);

        return [
            'user_id'              => $conversationUserId,
            'disappearing_seconds' => $setting->disappearing_seconds,
        ];
    }

    /**
     * Broadcast typing dots status without touching the database.
     */
    public function sendTyping($actor, ?int $targetUserId, bool $isTyping): array
    {
        [$identity, $conversationUserId] = $this->resolveAuthorizedConversation($actor, $targetUserId);

        UserChatTypingEvent::dispatch($conversationUserId, $identity['type'], $identity['id'], $isTyping);

        return [
            'user_id'     => $conversationUserId,
            'sender_type' => $identity['type'],
            'sender_id'   => $identity['id'],
            'is_typing'   => $isTyping,
        ];
    }

    /**
     * Ensure action happens within allowed time window.
     */
    protected function assertWithinWindow(UserChat $chat, int $minutes, string $actionLabel): void
    {
        if ($chat->created_at && $chat->created_at->diffInMinutes(now()) > $minutes) {
            abort(403, "انتهت المدة المسموح بها لـ {$actionLabel} الرسالة ({$minutes} دقيقة).");
        }
    }
}
