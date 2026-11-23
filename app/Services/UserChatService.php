<?php

namespace App\Services;

use App\Events\UserChatEvent;
use App\Events\UserChatReadEvent;
use App\Models\Events;
use App\Models\User;
use App\Models\UserChat;
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
        $role = $actor->role ?? ($actor->user_type ?? 'user');
        $isAdminOrEmployee = in_array($role, ['admin', 'employee']);

        // Determine conversation user_id
        if ($isAdminOrEmployee) {
            $conversationUserId = $targetUserId ?? $request->input('user_id');
            if (empty($conversationUserId)) {
                throw ValidationException::withMessages([
                    'user_id' => ['The user_id field is required when sending as admin/employee.']
                ]);
            }

            // Check permission for employee
            if ($role === 'employee' && !$this->employeeCanAccessUser($actor->id, $conversationUserId)) {
                abort(403, 'غير مصرح لك بمراسلة هذا المستخدم / You are not authorized to message this user.');
            }

            $isAdmin = true;
            $adminId = $actor->id;
            $isAdminRead = true;
            $isUserRead = false;
        } else {
            $conversationUserId = $actor->id;
            $isAdmin = false;
            $adminId = null;
            $isAdminRead = false;
            $isUserRead = true;
        }

        // Validate presence of content
        $msg = $request->input('msg');
        $hasImage = $request->hasFile('image');
        $hasVoice = $request->hasFile('voice');

        if (empty($msg) && !$hasImage && !$hasVoice) {
            throw ValidationException::withMessages([
                'msg' => ['يجب إرسال نص أو صورة أو تسجيل صوتي / You must provide a message, image, or voice file.']
            ]);
        }

        // Handle Image upload
        $imagePath = null;
        if ($hasImage) {
            $imagePath = $this->upload($request, 'image', 'user_chats/images');
        }

        // Handle Voice upload
        $voicePath = null;
        if ($hasVoice) {
            $voiceFile = $request->file('voice');
            if ($voiceFile instanceof UploadedFile && $voiceFile->isValid()) {
                $voicePath = $voiceFile->store('user_chats/voices', 'public');
            }
        }

        // Create message record
        $chat = UserChat::create([
            'user_id'       => $conversationUserId,
            'admin_id'      => $adminId,
            'is_admin'      => $isAdmin,
            'msg'           => $msg,
            'is_admin_read' => $isAdminRead,
            'is_user_read'  => $isUserRead,
            'image'         => $imagePath,
            'voice'         => $voicePath,
        ]);

        // Dispatch realtime event
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
        $role = $actor->role ?? ($actor->user_type ?? 'user');
        $isAdminOrEmployee = in_array($role, ['admin', 'employee']);

        if ($isAdminOrEmployee) {
            if ($role === 'employee' && !$this->employeeCanAccessUser($actor->id, $conversationUserId)) {
                abort(403, 'غير مصرح لك بالوصول لمحادثة هذا المستخدم.');
            }

            // Admin/Employee reads messages sent by user (is_admin = false)
            $updated = UserChat::where('user_id', $conversationUserId)
                ->where('is_admin', false)
                ->where('is_admin_read', false)
                ->update(['is_admin_read' => true]);

            $reader = 'admin';
        } else {
            // User reads messages sent by admin/employee (is_admin = true)
            $conversationUserId = $actor->id;

            $updated = UserChat::where('user_id', $conversationUserId)
                ->where('is_admin', true)
                ->where('is_user_read', false)
                ->update(['is_user_read' => true]);

            $reader = 'user';
        }

        if ($updated > 0) {
            UserChatReadEvent::dispatch($conversationUserId, $reader, $updated);
        }

        return [
            'status'        => true,
            'user_id'       => (int) $conversationUserId,
            'reader'        => $reader,
            'updated_count' => $updated,
            'message'       => 'تم تحديث حالة القراءة بنجاح / Conversation marked as read successfully',
        ];
    }

    /**
     * Get paginated messages for a conversation.
     *
     * @param int|string $conversationUserId
     * @param mixed $actor
     * @param int $perPage
     * @return array
     */
    public function getConversationMessages($conversationUserId, $actor, int $perPage = 50): array
    {
        if (!$this->checkPermission($actor, $conversationUserId)) {
            abort(403, 'غير مصرح لك بعرض هذه المحادثة.');
        }

        $user = User::withoutGlobalScopes()->find($conversationUserId);

        $messages = UserChat::where('user_id', $conversationUserId)
            ->orderBy('id', 'asc')
            ->paginate($perPage);

        return [
            'user'     => $user ? [
                'id'     => $user->id,
                'name'   => $user->name,
                'mobile' => $user->mobile,
                'email'  => $user->email,
            ] : null,
            'messages' => $messages,
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
        $role = $actor->role ?? ($actor->user_type ?? 'user');

        $query = User::withoutGlobalScopes()
            ->whereHas('user_chats')
            ->withCount([
                'user_chats as unread_count' => function ($q) {
                    $q->where('is_admin', false)->where('is_admin_read', false);
                }
            ]);

        // Restrict employee to allowed users
        if ($role === 'employee') {
            $allowedIds = $this->getAllowedUserIdsForEmployee($actor->id);
            $query->whereIn('id', $allowedIds);
        }

        // Search by user name or mobile if supplied
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage)->through(function ($u) {
            $latestChat = UserChat::where('user_id', $u->id)->latest('id')->first();

            return [
                'user_id'            => $u->id,
                'name'               => $u->name,
                'mobile'             => $u->mobile,
                'email'              => $u->email,
                'unread_count'       => $u->unread_count,
                'last_message'       => $latestChat?->msg ?? ($latestChat?->image ? '[Image]' : ($latestChat?->voice ? '[Voice]' : '')),
                'last_message_voice' => $latestChat?->voice_url,
                'last_message_image' => $latestChat?->image_url,
                'last_message_date'  => $latestChat?->date,
                'last_message_time'  => $latestChat?->time,
                'last_sender'        => $latestChat ? ($latestChat->is_admin ? 'admin' : 'user') : null,
            ];
        });
    }
}
