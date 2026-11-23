<?php

namespace App\Http\Controllers;

use App\Events\UserCallEvent;
use App\Models\Admin;
use App\Models\User;
use App\Services\Agora\AgoraService;
use App\Services\UserChatService;
use App\Traits\GeneralTrait;
use App\Traits\imageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserChatController extends Controller
{
    use GeneralTrait, imageTrait;

    protected $chatService;
    protected $agoraService;

    public function __construct(UserChatService $chatService, AgoraService $agoraService)
    {
        $this->chatService = $chatService;
        $this->agoraService = $agoraService;
    }

    /**
     * Resolve authenticated actor from any active guard/token.
     *
     * @param Request $request
     * @return mixed
     */
    protected function resolveActor(Request $request)
    {
        // 1. Sanctum token or default Auth
        $actor = auth('sanctum')->user() ?? auth('api')->user() ?? auth('admin')->user() ?? Auth::user();
        if ($actor) {
            return $actor;
        }

        // 2. Token header (Mazoum custom token authentication)
        $token = $request->header('token') ?? $request->bearerToken();
        if ($token) {
            $user = User::withoutGlobalScopes()->where('token', $token)->first();
            if ($user) {
                Auth::setUser($user);
                return $user;
            }

            $admin = Admin::where('remember_token', $token)->first();
            if ($admin) {
                Auth::setUser($admin);
                return $admin;
            }
        }

        return null;
    }

    /**
     * Check if actor is an Admin or Employee.
     *
     * @param mixed $actor
     * @return bool
     */
    protected function isAdminOrEmployee($actor): bool
    {
        if (!$actor) {
            return false;
        }
        $role = $actor->role ?? ($actor->user_type ?? 'user');
        return in_array($role, ['admin', 'employee']);
    }

    /*
    |--------------------------------------------------------------------------
    | User Side Endpoints
    |--------------------------------------------------------------------------
    */

    /**
     * User sends a message to admin/support.
     * Accepts: msg (text), image (file), voice (file as audio).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user_send_msg(Request $request): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'msg'   => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:20480'],
            'voice' => ['nullable', 'file', 'max:51200'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        if (!$request->filled('msg') && !$request->hasFile('image') && !$request->hasFile('voice')) {
            return response()->json([
                'status' => false,
                'errors' => 'يجب إرسال نص أو صورة أو ملف صوتي (You must provide msg, image, or voice file).'
            ], 400);
        }

        try {
            $chat = $this->chatService->sendMessage($request, $actor);

            return response()->json([
                'status'  => true,
                'message' => 'تم إرسال الرسالة بنجاح',
                'data'    => $chat,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get chat conversation messages for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user_messages(Request $request): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $perPage = (int) $request->input('per_page', 50);
        $data = $this->chatService->getConversationMessages($actor->id, $actor, $perPage);

        return response()->json([
            'status' => true,
            'data'   => $data,
        ], 200);
    }

    /**
     * User marks all admin messages in their conversation as read.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function make_user_read(Request $request): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $result = $this->chatService->markAllAsRead($actor->id, $actor);

        return response()->json($result, 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin / Employee Side Endpoints
    |--------------------------------------------------------------------------
    */

    /**
     * Get list of conversations for Admin or Employee.
     * - Admin gets all users who have chats.
     * - Employee only gets users matching:
     *   (events.sub_user or events.user_id) where events.assistant_id = Auth::user()->id.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function admin_conversations(Request $request): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor || !$this->isAdminOrEmployee($actor)) {
            return response()->json(['status' => false, 'message' => 'Unauthorized. Admin or Employee access required.'], 403);
        }

        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 30);
        $conversations = $this->chatService->getAdminConversationsList($actor, $search, $perPage);

        return response()->json([
            'status' => true,
            'data'   => $conversations,
        ], 200);
    }

    /**
     * Admin or Employee sends a message to a user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function admin_send_msg(Request $request): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor || !$this->isAdminOrEmployee($actor)) {
            return response()->json(['status' => false, 'message' => 'Unauthorized. Admin or Employee access required.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
            'msg'     => ['nullable', 'string'],
            'image'   => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:20480'],
            'voice'   => ['nullable', 'file', 'max:51200'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        if (!$request->filled('msg') && !$request->hasFile('image') && !$request->hasFile('voice')) {
            return response()->json([
                'status' => false,
                'errors' => 'يجب إرسال نص أو صورة أو ملف صوتي (You must provide msg, image, or voice file).'
            ], 400);
        }

        $targetUserId = (int) $request->input('user_id');

        // Check permission if employee
        $role = $actor->role ?? ($actor->user_type ?? 'user');
        if ($role === 'employee' && !$this->chatService->employeeCanAccessUser($actor->id, $targetUserId)) {
            return response()->json([
                'status'  => false,
                'message' => 'غير مصرح لك بمراسلة هذا المستخدم حيث أنه ليس ضمن الفعاليات المسندة إليك.'
            ], 403);
        }

        try {
            $chat = $this->chatService->sendMessage($request, $actor, $targetUserId);

            return response()->json([
                'status'  => true,
                'message' => 'تم إرسال الرسالة بنجاح',
                'data'    => $chat,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Admin or Employee gets messages for a specific conversation.
     *
     * @param Request $request
     * @param int|string $user_id
     * @return JsonResponse
     */
    public function admin_messages(Request $request, $user_id): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor || !$this->isAdminOrEmployee($actor)) {
            return response()->json(['status' => false, 'message' => 'Unauthorized. Admin or Employee access required.'], 403);
        }

        $targetUserId = (int) $user_id;

        // Check permission if employee
        $role = $actor->role ?? ($actor->user_type ?? 'user');
        if ($role === 'employee' && !$this->chatService->employeeCanAccessUser($actor->id, $targetUserId)) {
            return response()->json([
                'status'  => false,
                'message' => 'غير مصرح لك بعرض محادثة هذا المستخدم حيث أنه ليس ضمن الفعاليات المسندة إليك.'
            ], 403);
        }

        $perPage = (int) $request->input('per_page', 50);
        $data = $this->chatService->getConversationMessages($targetUserId, $actor, $perPage);

        return response()->json([
            'status' => true,
            'data'   => $data,
        ], 200);
    }

    /**
     * Admin or Employee marks all user messages in the conversation as read.
     *
     * @param Request $request
     * @param int|string|null $user_id
     * @return JsonResponse
     */
    public function make_admin_read(Request $request, $user_id = null): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor || !$this->isAdminOrEmployee($actor)) {
            return response()->json(['status' => false, 'message' => 'Unauthorized. Admin or Employee access required.'], 403);
        }

        $targetUserId = (int) ($user_id ?? $request->input('user_id'));
        if (empty($targetUserId)) {
            return response()->json(['status' => false, 'message' => 'user_id is required.'], 422);
        }

        // Check permission if employee
        $role = $actor->role ?? ($actor->user_type ?? 'user');
        if ($role === 'employee' && !$this->chatService->employeeCanAccessUser($actor->id, $targetUserId)) {
            return response()->json([
                'status'  => false,
                'message' => 'غير مصرح لك بالوصول لمحادثة هذا المستخدم.'
            ], 403);
        }

        $result = $this->chatService->markAllAsRead($targetUserId, $actor);

        return response()->json($result, 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Unified Flexible Action (Convenience)
    |--------------------------------------------------------------------------
    */

    /**
     * Unified get messages endpoint.
     *
     * @param Request $request
     * @param int|string|null $user_id
     * @return JsonResponse
     */
    public function getMessages(Request $request, $user_id = null): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if ($this->isAdminOrEmployee($actor)) {
            $targetUserId = $user_id ?? $request->input('user_id');
            if (empty($targetUserId)) {
                return response()->json(['status' => false, 'message' => 'user_id is required for admin/employee.'], 422);
            }
            return $this->admin_messages($request, $targetUserId);
        }

        return $this->user_messages($request);
    }

    /**
     * Unified send message endpoint for both user and admin.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if ($this->isAdminOrEmployee($actor) && $request->has('user_id')) {
            return $this->admin_send_msg($request);
        }

        return $this->user_send_msg($request);
    }

    /**
     * Unified mark all as read endpoint.
     *
     * @param Request $request
     * @param int|string|null $user_id
     * @return JsonResponse
     */
    public function make_all_read(Request $request, $user_id = null): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if ($this->isAdminOrEmployee($actor)) {
            return $this->make_admin_read($request, $user_id);
        }

        return $this->make_user_read($request);
    }

    /*
    |--------------------------------------------------------------------------
    | Voice & Video Calling (Agora.io)
    |--------------------------------------------------------------------------
    */

    /**
     * Start a Voice or Video Call session.
     * Generates Agora channel & token for caller, and broadcasts incoming_call event to receiver.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function startCall(Request $request): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'user_id'   => ['nullable', 'exists:users,id'],
            'call_type' => ['nullable', 'in:voice,video'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $callType = $request->input('call_type', 'voice');
        $isAdminOrEmployee = $this->isAdminOrEmployee($actor);

        if ($isAdminOrEmployee) {
            $targetUserId = (int) $request->input('user_id');
            if (empty($targetUserId)) {
                return response()->json(['status' => false, 'message' => 'user_id is required when initiating a call as admin/employee.'], 422);
            }

            // Check employee permission
            $role = $actor->role ?? ($actor->user_type ?? 'user');
            if ($role === 'employee' && !$this->chatService->employeeCanAccessUser($actor->id, $targetUserId)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'غير مصرح لك بالاتصال بهذا المستخدم حيث أنه ليس ضمن الفعاليات المسندة إليك.'
                ], 403);
            }

            $receiverId = $targetUserId;
            $callerRole = $role;
        } else {
            $targetUserId = (int) $actor->id;
            $receiverId = 'admin';
            $callerRole = 'user';
        }

        try {
            $channelName = $this->agoraService->generateChannelName($targetUserId);
            $callerUid = (int) $actor->id;
            $token = $this->agoraService->generateToken($channelName, $callerUid);

            // Broadcast incoming call event
            UserCallEvent::dispatch($targetUserId, 'incoming', [
                'channel_name' => $channelName,
                'call_type'    => $callType,
                'caller_id'    => (int) $actor->id,
                'caller_name'  => $actor->name ?? 'User',
                'caller_role'  => $callerRole,
                'receiver_id'  => $receiverId,
            ]);

            return response()->json([
                'status'       => true,
                'message'      => 'Call session created successfully',
                'channel_name' => $channelName,
                'token'        => $token,
                'app_id'       => $this->agoraService->getAppId(),
                'uid'          => $callerUid,
                'call_type'    => $callType,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Agora token to join an ongoing call channel (Accept Call).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCallToken(Request $request): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'channel_name' => ['required', 'string'],
            'user_id'      => ['nullable', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $channelName = $request->input('channel_name');
        $targetUserId = (int) ($request->input('user_id') ?? $actor->id);

        try {
            $receiverUid = (int) $actor->id;
            $token = $this->agoraService->generateToken($channelName, $receiverUid);

            // Broadcast accepted event so caller knows receiver picked up
            UserCallEvent::dispatch($targetUserId, 'accepted', [
                'channel_name' => $channelName,
                'accepter_id'  => (int) $actor->id,
            ]);

            return response()->json([
                'status'       => true,
                'token'        => $token,
                'app_id'       => $this->agoraService->getAppId(),
                'channel_name' => $channelName,
                'uid'          => $receiverUid,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * End, reject, or cancel a call session.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function endCall(Request $request): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'channel_name' => ['required', 'string'],
            'reason'       => ['nullable', 'in:ended,rejected,missed,busy,cancelled'],
            'user_id'      => ['nullable', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $channelName = $request->input('channel_name');
        $reason = $request->input('reason', 'ended');
        $targetUserId = (int) ($request->input('user_id') ?? $actor->id);

        UserCallEvent::dispatch($targetUserId, $reason, [
            'channel_name' => $channelName,
            'ended_by'     => (int) $actor->id,
            'reason'       => $reason,
        ]);

        return response()->json([
            'status'  => true,
            'message' => "Call {$reason} successfully",
        ], 200);
    }
}
