<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesChatActor;
use App\Services\UserChat\UserChatActionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class UserChatActionController extends Controller
{
    use ResolvesChatActor;

    protected $actionService;

    public function __construct(UserChatActionService $actionService)
    {
        $this->actionService = $actionService;
    }

    /**
     * Mark incoming messages as delivered.
     */
    public function markDelivered(Request $request): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'user_id'       => ['nullable', 'exists:users,id'],
            'message_ids'   => ['nullable', 'array'],
            'message_ids.*' => ['integer'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        return $this->runAction(fn () => $this->actionService->markDelivered(
            $actor,
            $request->filled('user_id') ? (int) $request->input('user_id') : null,
            $request->input('message_ids', [])
        ));
    }

    /**
     * Toggle, add, or change an emoji reaction on a message.
     */
    public function react(Request $request, int $id): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'emoji' => ['nullable', 'string', 'max:32'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        return $this->runAction(fn () => $this->actionService->react($actor, $id, $request->input('emoji')));
    }

    /**
     * Edit message text (Admin anytime; User/Employee within configured window).
     */
    public function edit(Request $request, int $id): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'msg' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        return $this->runAction(fn () => $this->actionService->editMessage($actor, $id, $request->input('msg')));
    }

    /**
     * Delete message for me ("me") or for everyone ("everyone").
     */
    public function delete(Request $request, int $id): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'scope' => ['required', 'in:me,everyone'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        return $this->runAction(fn () => $this->actionService->deleteMessage($actor, $id, $request->input('scope')));
    }

    /**
     * Forward message to target conversation(s).
     */
    public function forward(Request $request, int $id): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'target_user_ids'   => ['nullable', 'array'],
            'target_user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        return $this->runAction(fn () => $this->actionService->forwardMessage(
            $actor,
            $id,
            $request->input('target_user_ids', [])
        ));
    }

    /**
     * Pin or unpin a message (allowed for both User and Admin/Employee).
     */
    public function togglePin(Request $request, int $id): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $pin = $request->has('is_pinned') ? filter_var($request->input('is_pinned'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;

        return $this->runAction(fn () => $this->actionService->togglePin($actor, $id, $pin));
    }

    /**
     * List pinned messages in a conversation.
     */
    public function pinnedMessages(Request $request): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        return $this->runAction(fn () => $this->actionService->getPinnedMessages(
            $actor,
            $request->filled('user_id') ? (int) $request->input('user_id') : null
        ));
    }

    /**
     * Toggle favorite/star on a message (private to caller).
     */
    public function toggleFavorite(Request $request, int $id): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        return $this->runAction(fn () => $this->actionService->toggleFavorite($actor, $id));
    }

    /**
     * List caller's favorite messages.
     */
    public function favoriteMessages(Request $request): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        return $this->runAction(fn () => $this->actionService->getFavoriteMessages(
            $actor,
            $request->filled('user_id') ? (int) $request->input('user_id') : null,
            (int) $request->input('per_page', 30)
        ));
    }

    /**
     * Update disappearing messages timer for a conversation (null disables).
     */
    public function updateDisappearingSettings(Request $request): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'user_id'              => ['nullable', 'exists:users,id'],
            'disappearing_seconds' => ['nullable', 'integer', 'min:60'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $seconds = $request->filled('disappearing_seconds') ? (int) $request->input('disappearing_seconds') : null;

        return $this->runAction(fn () => $this->actionService->updateDisappearingSettings(
            $actor,
            $request->filled('user_id') ? (int) $request->input('user_id') : null,
            $seconds
        ));
    }

    /**
     * Broadcast typing dots status.
     */
    public function typing(Request $request): JsonResponse
    {
        $actor = $this->resolveActor($request);
        if (!$actor) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'user_id'   => ['nullable', 'exists:users,id'],
            'is_typing' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        return $this->runAction(fn () => $this->actionService->sendTyping(
            $actor,
            $request->filled('user_id') ? (int) $request->input('user_id') : null,
            filter_var($request->input('is_typing'), FILTER_VALIDATE_BOOLEAN)
        ));
    }

    /**
     * Execute service callback with unified HTTP/validation error handling.
     */
    protected function runAction(callable $callback): JsonResponse
    {
        try {
            return response()->json([
                'status' => true,
                'data'   => $callback(),
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (HttpExceptionInterface $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'الرسالة غير موجودة.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
