<?php

namespace App\Services\UserChat;

use App\Models\Admin;
use App\Models\UserChat;
use Illuminate\Support\Collection;

class UserChatFormatter
{
    /**
     * Resolve unique actor identity across Admin and User tables.
     *
     * @param mixed $actor
     * @return array{type: string, id: int, role: string, is_admin_side: bool}
     */
    public static function resolveActorIdentity($actor): array
    {
        if ($actor instanceof Admin) {
            return [
                'type'          => 'admin',
                'id'            => (int) $actor->id,
                'role'          => 'admin',
                'is_admin_side' => true,
            ];
        }

        $role = $actor->role ?? ($actor->user_type ?? 'user');
        $isEmployee = $role === 'employee';

        return [
            'type'          => $isEmployee ? 'employee' : 'user',
            'id'            => (int) $actor->id,
            'role'          => $role,
            'is_admin_side' => $isEmployee || $role === 'admin',
        ];
    }

    /**
     * Group raw reaction rows into summary format.
     */
    public static function formatReactions(Collection $reactions, ?string $viewerType = null, ?int $viewerId = null): array
    {
        $myReaction = null;

        if ($viewerType !== null && $viewerId !== null) {
            $mine = $reactions->first(function ($r) use ($viewerType, $viewerId) {
                return $r->actor_type === $viewerType && (int) $r->actor_id === (int) $viewerId;
            });
            $myReaction = $mine ? $mine->emoji : null;
        }

        $grouped = $reactions->groupBy('emoji')->map(function (Collection $group, string $emoji) {
            return [
                'emoji'  => $emoji,
                'count'  => $group->count(),
                'actors' => $group->map(function ($r) {
                    return [
                        'type' => $r->actor_type,
                        'id'   => (int) $r->actor_id,
                    ];
                })->values()->all(),
            ];
        })->values()->all();

        return [
            'reactions'   => $grouped,
            'my_reaction' => $myReaction,
        ];
    }

    /**
     * Format a UserChat message into a single canonical JSON structure.
     */
    public static function formatMessage(UserChat $chat, ?string $viewerType = null, ?int $viewerId = null): array
    {
        $isDeleted = !is_null($chat->deleted_for_everyone_at);

        $deliveryStatus = 'sent';
        if ($chat->is_admin) {
            if ($chat->is_user_read) {
                $deliveryStatus = 'read';
            } elseif ($chat->is_delivered) {
                $deliveryStatus = 'delivered';
            }
        } else {
            if ($chat->is_admin_read) {
                $deliveryStatus = 'read';
            } elseif ($chat->is_delivered) {
                $deliveryStatus = 'delivered';
            }
        }

        $replyTo = null;
        if ($chat->relationLoaded('replyTo') && $chat->replyTo) {
            $parentDeleted = !is_null($chat->replyTo->deleted_for_everyone_at);
            $replyTo = [
                'id'         => (int) $chat->replyTo->id,
                'is_admin'   => (bool) $chat->replyTo->is_admin,
                'is_deleted' => $parentDeleted,
                'msg'        => $parentDeleted ? null : $chat->replyTo->msg,
                'image'      => $parentDeleted ? null : $chat->replyTo->resolveMediaUrl($chat->replyTo->image),
                'voice'      => $parentDeleted ? null : $chat->replyTo->resolveMediaUrl($chat->replyTo->voice),
            ];
        }

        $reactionsCollection = $chat->relationLoaded('reactions') ? $chat->reactions : collect();
        $reactionData = self::formatReactions($reactionsCollection, $viewerType, $viewerId);

        $isFavorite = false;
        if ($viewerType !== null && $viewerId !== null && $chat->relationLoaded('favorites')) {
            $isFavorite = $chat->favorites->contains(function ($fav) use ($viewerType, $viewerId) {
                return $fav->actor_type === $viewerType && (int) $fav->actor_id === (int) $viewerId;
            });
        }

        return [
            'id'              => (int) $chat->id,
            'client_msg_id'   => $chat->client_msg_id,
            'user_id'         => (int) $chat->user_id,
            'admin_id'        => $chat->admin_id ? (int) $chat->admin_id : null,
            'is_admin'        => (bool) $chat->is_admin,
            'msg'             => $isDeleted ? null : $chat->msg,
            'image'           => $isDeleted ? null : $chat->resolveMediaUrl($chat->image),
            'voice'           => $isDeleted ? null : $chat->resolveMediaUrl($chat->voice),
            'delivery_status' => $deliveryStatus,
            'is_delivered'    => (bool) $chat->is_delivered,
            'delivered_at'    => $chat->delivered_at ? $chat->delivered_at->toIso8601String() : null,
            'is_admin_read'   => (bool) $chat->is_admin_read,
            'admin_read_at'   => $chat->admin_read_at ? $chat->admin_read_at->toIso8601String() : null,
            'is_user_read'    => (bool) $chat->is_user_read,
            'user_read_at'    => $chat->user_read_at ? $chat->user_read_at->toIso8601String() : null,
            'reply_to'        => $replyTo,
            'is_forwarded'    => (bool) $chat->is_forwarded,
            'is_edited'       => (bool) $chat->is_edited,
            'edited_at'       => $chat->edited_at ? $chat->edited_at->toIso8601String() : null,
            'is_deleted'      => $isDeleted,
            'deleted_at'      => $chat->deleted_for_everyone_at ? $chat->deleted_for_everyone_at->toIso8601String() : null,
            'is_pinned'       => (bool) $chat->is_pinned,
            'pinned_at'       => $chat->pinned_at ? $chat->pinned_at->toIso8601String() : null,
            'is_favorite'     => $isFavorite,
            'reactions'       => $reactionData['reactions'],
            'my_reaction'     => $reactionData['my_reaction'],
            'expires_at'      => $chat->expires_at ? $chat->expires_at->toIso8601String() : null,
            'date'            => $chat->created_at ? $chat->created_at->format('Y-m-d') : null,
            'time'            => $chat->created_at ? $chat->created_at->format('h:i:s A') : null,
            'created_at'      => $chat->created_at ? $chat->created_at->toIso8601String() : null,
        ];
    }
}
