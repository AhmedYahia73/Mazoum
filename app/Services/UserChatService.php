<?php

namespace App\Services;

use App\Events\UserChatEvent;
use App\Events\UserChatReadEvent;
use App\Models\Events;
use App\Models\User;
use App\Models\UserChat;
use App\Models\UserChatSetting;
use App\Services\UserChat\UserChatFormatter;
use App\Traits\imageTrait;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserChatService
{
    use imageTrait;

    /**
     * Determine if an employee can access/chat with a specific user.
     * Condition: (events.sub_user or events.user_id) in UserChat.user_id
     * where employee has events where events.assistant_id = employee_id.
     *
     * @param int|string $employeeId
     * @param int|string $targetUserId
     * @return bool
     */
    public function employeeCanAccessUser($employeeId, $targetUserId): bool
    {
        if (empty($employeeId) || empty($targetUserId)) {
            return false;
        }

        // 1. Check if target user is the owner of an event assigned to the employee
        $isEventOwner = Events::withoutGlobalScopes()
            ->where('assistant_id', $employeeId)
            ->where('user_id', $targetUserId)
            ->exists();

        if ($isEventOwner) {
            return true;
        }

        // 2. Check if target user is a sub_user of an event assigned to the employee (users.event_id == events.id)
        $isSubUserByEvent = Events::withoutGlobalScopes()
            ->where('assistant_id', $employeeId)
            ->whereHas('sub_user', function ($query) use ($targetUserId) {
                $query->where('id', $targetUserId);
            })
            ->exists();

        if ($isSubUserByEvent) {
            return true;
        }

        // 3. Check if target user is a sub_user linked via parent user_id of an event owner
        $targetUser = User::withoutGlobalScopes()->find($targetUserId);
        if ($targetUser && !empty($targetUser->event_id)) {
            $eventAssigned = Events::withoutGlobalScopes()
                ->where('assistant_id', $employeeId)
                ->where('id', $targetUser->event_id)
                ->exists();

            if ($eventAssigned) {
                return true;
            }
        }

        if ($targetUser && !empty($targetUser->user_id)) {
            $parentOwnerAssigned = Events::withoutGlobalScopes()
                ->where('assistant_id', $employeeId)
                ->where('user_id', $targetUser->user_id)
                ->exists();

            if ($parentOwnerAssigned) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all allowed user IDs for an employee.
     *
     * @param int|string $employeeId
     * @return array
     */
    public function getAllowedUserIdsForEmployee($employeeId): array
    {
        if (empty($employeeId)) {
            return [];
        }

        $events = Events::withoutGlobalScopes()
            ->where('assistant_id', $employeeId)
            ->get(['id', 'user_id']);

        $eventIds = $events->pluck('id')->filter()->unique()->toArray();
        $ownerIds = $events->pluck('user_id')->filter()->unique()->toArray();

        $subUserIdsByEvent = empty($eventIds)
            ? []
            : User::withoutGlobalScopes()->whereIn('event_id', $eventIds)->pluck('id')->toArray();

        $subUserIdsByOwner = empty($ownerIds)
            ? []
            : User::withoutGlobalScopes()->whereIn('user_id', $ownerIds)->pluck('id')->toArray();

        return array_values(array_unique(array_merge($ownerIds, $subUserIdsByEvent, $subUserIdsByOwner)));
    }

    /**
     * Check if authenticated actor has permission to access a conversation with target user.
     *
     * @param mixed $actor Authenticated User or Admin model
     * @param int|string $targetUserId
     * @return bool
     */
    public function checkPermission($actor, $targetUserId): bool
    {
        if (empty($actor)) {
            return false;
        }

        $role = $actor->role ?? ($actor->user_type ?? 'user');

        if ($role === 'admin') {
            return true;
        }

        if ($role === 'employee') {
            return $this->employeeCanAccessUser($actor->id, $targetUserId);
        }

        // Regular user can only access their own conversation
        return (int) $actor->id === (int) $targetUserId;
    }

    /**
     * Resolve disappearing expiration timestamp for a conversation.
     */
    public function resolveExpiresAt($conversationUserId)
    {
        $seconds = UserChatSetting::where('user_id', $conversationUserId)->value('disappearing_seconds');
        return $seconds ? now()->addSeconds((int) $seconds) : null;
    }

    /**
     * Store and broadcast a chat message.
     *
     * @param Request $request
     * @param mixed $actor
     * @param int|null $targetUserId
     * @return UserChat
     * @throws ValidationException
     */
    public function sendMessage(Request $request, $actor, $targetUserId = null): UserChat
    {
        $identity = UserChatFormatter::resolveActorIdentity($actor);
        $isAdminOrEmployee = $identity['is_admin_side'];

        if ($isAdminOrEmployee) {
            $conversationUserId = $targetUserId ?? $request->input('user_id');
            if (empty($conversationUserId)) {
                throw ValidationException::withMessages([
                    'user_id' => ['The user_id field is required when sending as admin/employee.']
                ]);
            }

            if ($identity['role'] === 'employee' && !$this->employeeCanAccessUser($identity['id'], $conversationUserId)) {
                abort(403, 'غير مصرح لك بمراسلة هذا المستخدم / You are not authorized to message this user.');
            }

            $isAdmin = true;
            $adminId = $identity['id'];
        } else {
            $conversationUserId = $identity['id'];
            $isAdmin = false;
            $adminId = null;
        }

        // Idempotency guard for client retry
        $clientMsgId = $request->input('client_msg_id');
        if (!empty($clientMsgId)) {
            $existing = UserChat::where('user_id', $conversationUserId)
                ->where('client_msg_id', $clientMsgId)
                ->with(['replyTo', 'reactions', 'favorites'])
                ->first();
            if ($existing) {
                return $existing;
            }
        }

        // Validate reply_to_id belongs to the same conversation
        $replyToId = $request->input('reply_to_id');
        if (!empty($replyToId)) {
            $replyTargetExists = UserChat::where('id', $replyToId)
                ->where('user_id', $conversationUserId)
                ->visibleFor($isAdminOrEmployee)
                ->exists();
            if (!$replyTargetExists) {
                throw ValidationException::withMessages([
                    'reply_to_id' => ['الرسالة المراد الرد عليها غير موجودة في هذه المحادثة.']
                ]);
            }
        }

        $msg = $request->input('msg');
        $hasImage = $request->hasFile('image');
        $hasVoice = $request->hasFile('voice');

        if (empty($msg) && !$hasImage && !$hasVoice) {
            throw ValidationException::withMessages([
                'msg' => ['يجب إرسال نص أو صورة أو تسجيل صوتي / You must provide a message, image, or voice file.']
            ]);
        }

        $imagePath = $hasImage ? $this->upload($request, 'image', 'user_chats/images') : null;

        $voicePath = null;
        if ($hasVoice) {
            $voiceFile = $request->file('voice');
            if ($voiceFile instanceof UploadedFile && $voiceFile->isValid()) {
                $voicePath = $voiceFile->store('user_chats/voices', 'public');
            }
        }

        $now = now();
        $chat = UserChat::create([
            'client_msg_id' => $clientMsgId,
            'user_id'       => $conversationUserId,
            'admin_id'      => $adminId,
            'reply_to_id'   => $replyToId ?: null,
            'is_admin'      => $isAdmin,
            'msg'           => $msg,
            'is_admin_read' => $isAdmin,
            'admin_read_at' => $isAdmin ? $now : null,
            'is_user_read'  => !$isAdmin,
            'user_read_at'  => !$isAdmin ? $now : null,
            'image'         => $imagePath,
            'voice'         => $voicePath,
            'expires_at'    => $this->resolveExpiresAt($conversationUserId),
        ]);

        $chat->load(['replyTo', 'reactions', 'favorites']);

        UserChatEvent::dispatch($chat);

        return $chat;
    }

    /**
     * Mark all unread messages in conversation as read.
     *
     * @param int|string $conversationUserId
     * @param mixed $actor
     * @return array
     */
    public function markAllAsRead($conversationUserId, $actor): array
    {
        $identity = UserChatFormatter::resolveActorIdentity($actor);
        $isAdminOrEmployee = $identity['is_admin_side'];
        $now = now();

        if ($isAdminOrEmployee) {
            if ($identity['role'] === 'employee' && !$this->employeeCanAccessUser($identity['id'], $conversationUserId)) {
                abort(403, 'غير مصرح لك بالوصول لمحادثة هذا المستخدم.');
            }

            $updated = UserChat::where('user_id', $conversationUserId)
                ->where('is_admin', false)
                ->where('is_admin_read', false)
                ->visibleFor(true)
                ->update([
                    'is_admin_read' => true,
                    'admin_read_at' => $now,
                    'is_delivered'  => true,
                    'delivered_at'  => DB::raw('COALESCE(delivered_at, NOW())'),
                ]);

            $reader = 'admin';
        } else {
            $conversationUserId = $identity['id'];

            $updated = UserChat::where('user_id', $conversationUserId)
                ->where('is_admin', true)
                ->where('is_user_read', false)
                ->visibleFor(false)
                ->update([
                    'is_user_read' => true,
                    'user_read_at' => $now,
                    'is_delivered' => true,
                    'delivered_at' => DB::raw('COALESCE(delivered_at, NOW())'),
                ]);

            $reader = 'user';
        }

        if ($updated > 0) {
            UserChatReadEvent::dispatch($conversationUserId, $reader, $updated, $now->toIso8601String());
        }

        return [
            'status'        => true,
            'user_id'       => (int) $conversationUserId,
            'reader'        => $reader,
            'updated_count' => $updated,
            'read_at'       => $now->toIso8601String(),
            'message'       => 'تم تحديث حالة القراءة بنجاح / Conversation marked as read successfully',
        ];
    }

    /**
     * Get paginated messages for a conversation (page=1 is oldest).
     * Supports optional $aroundId to jump straight to the page containing a quoted message.
     *
     * @param int|string $conversationUserId
     * @param mixed $actor
     * @param int $perPage
     * @param int|null $aroundId
     * @return array
     */
    public function getConversationMessages($conversationUserId, $actor, int $perPage = 50, ?int $aroundId = null): array
    {
        if (!$this->checkPermission($actor, $conversationUserId)) {
            abort(403, 'غير مصرح لك بعرض هذه المحادثة.');
        }

        $identity = UserChatFormatter::resolveActorIdentity($actor);
        $user = User::withoutGlobalScopes()->find($conversationUserId);

        $baseQuery = UserChat::where('user_id', $conversationUserId)
            ->visibleFor($identity['is_admin_side']);

        $page = null;
        if (!empty($aroundId)) {
            $targetExists = (clone $baseQuery)->where('id', $aroundId)->exists();
            if ($targetExists) {
                $position = (clone $baseQuery)->where('id', '<=', $aroundId)->count();
                $page = max(1, (int) ceil($position / max(1, $perPage)));
            }
        }

        $messages = $baseQuery
            ->with(['replyTo', 'reactions', 'favorites'])
            ->orderBy('id', 'asc')
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn (UserChat $m) => UserChatFormatter::formatMessage($m, $identity['type'], $identity['id']));

        $disappearingSeconds = UserChatSetting::where('user_id', $conversationUserId)->value('disappearing_seconds');

        return [
            'user' => $user ? [
                'id'     => $user->id,
                'name'   => $user->name,
                'mobile' => $user->mobile,
                'email'  => $user->email,
            ] : null,
            'disappearing_seconds' => $disappearingSeconds !== null ? (int) $disappearingSeconds : null,
            'messages'             => $messages,
        ];
    }

    /**
     * Get conversations list for Admin or Employee with unread counts and latest message.
     *
     * @param mixed $actor
     * @param string|null $search
     * @param int $perPage
     * @return mixed
     */
    public function getAdminConversationsList($actor, $search = null, int $perPage = 30)
    {
        $identity = UserChatFormatter::resolveActorIdentity($actor);

        $query = User::withoutGlobalScopes()
            ->whereHas('user_chats', function ($q) {
                $q->where('deleted_for_admin', false)
                  ->where(function ($exp) {
                      $exp->whereNull('expires_at')->orWhere('expires_at', '>', now());
                  });
            })
            ->withCount([
                'user_chats as unread_count' => function ($q) {
                    $q->where('is_admin', false)
                      ->where('is_admin_read', false)
                      ->where('deleted_for_admin', false)
                      ->whereNull('deleted_for_everyone_at')
                      ->where(function ($exp) {
                          $exp->whereNull('expires_at')->orWhere('expires_at', '>', now());
                      });
                }
            ]);

        if ($identity['role'] === 'employee') {
            $allowedIds = $this->getAllowedUserIdsForEmployee($identity['id']);
            $query->whereIn('id', $allowedIds);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage)->through(function ($u) {
            $latestChat = UserChat::where('user_id', $u->id)
                ->visibleFor(true)
                ->latest('id')
                ->first();

            $isDeleted = $latestChat && !is_null($latestChat->deleted_for_everyone_at);
            $preview = '';
            if ($latestChat) {
                if ($isDeleted) {
                    $preview = 'تم حذف هذه الرسالة';
                } elseif (!empty($latestChat->msg)) {
                    $preview = $latestChat->msg;
                } elseif (!empty($latestChat->image)) {
                    $preview = '[Image]';
                } elseif (!empty($latestChat->voice)) {
                    $preview = '[Voice]';
                }
            }

            return [
                'user_id'            => $u->id,
                'name'               => $u->name,
                'mobile'             => $u->mobile,
                'email'              => $u->email,
                'unread_count'       => $u->unread_count,
                'last_message'       => $preview,
                'last_message_voice' => ($latestChat && !$isDeleted) ? $latestChat->resolveMediaUrl($latestChat->voice) : null,
                'last_message_image' => ($latestChat && !$isDeleted) ? $latestChat->resolveMediaUrl($latestChat->image) : null,
                'last_message_date'  => $latestChat?->created_at?->format('Y-m-d'),
                'last_message_time'  => $latestChat?->created_at?->format('h:i:s A'),
                'last_sender'        => $latestChat ? ($latestChat->is_admin ? 'admin' : 'user') : null,
            ];
        });
    }
}
