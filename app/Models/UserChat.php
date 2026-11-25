<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChat extends Model
{
    use HasFactory;

    protected $table = 'user_chats';

    protected $fillable = [
        'client_msg_id',
        'user_id',
        'admin_id',
        'reply_to_id',
        'is_forwarded',
        'forwarded_from_id',
        'is_admin',
        'msg',
        'is_edited',
        'edited_at',
        'is_delivered',
        'delivered_at',
        'is_admin_read',
        'admin_read_at',
        'is_user_read',
        'user_read_at',
        'deleted_for_user',
        'deleted_for_admin',
        'deleted_for_everyone_at',
        'deleted_by_type',
        'deleted_by_id',
        'is_pinned',
        'pinned_at',
        'pinned_by_type',
        'pinned_by_id',
        'expires_at',
        'image',
        'voice',
    ];

    protected $casts = [
        'is_admin'                => 'boolean',
        'is_forwarded'            => 'boolean',
        'is_edited'               => 'boolean',
        'is_delivered'            => 'boolean',
        'is_admin_read'           => 'boolean',
        'is_user_read'            => 'boolean',
        'deleted_for_user'        => 'boolean',
        'deleted_for_admin'       => 'boolean',
        'is_pinned'               => 'boolean',
        'edited_at'               => 'datetime',
        'delivered_at'            => 'datetime',
        'admin_read_at'           => 'datetime',
        'user_read_at'            => 'datetime',
        'deleted_for_everyone_at' => 'datetime',
        'pinned_at'               => 'datetime',
        'expires_at'              => 'datetime',
        'created_at'              => 'datetime',
        'updated_at'              => 'datetime',
    ];

    /**
     * Target/owner user of the chat.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Admin/employee who sent or replied to the message.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Quoted/replied-to message.
     */
    public function replyTo()
    {
        return $this->belongsTo(self::class, 'reply_to_id');
    }

    /**
     * Original message if forwarded.
     */
    public function forwardedFrom()
    {
        return $this->belongsTo(self::class, 'forwarded_from_id');
    }

    /**
     * Emoji reactions on this message.
     */
    public function reactions()
    {
        return $this->hasMany(UserChatReaction::class, 'user_chat_id');
    }

    /**
     * Favorites on this message.
     */
    public function favorites()
    {
        return $this->hasMany(UserChatFavorite::class, 'user_chat_id');
    }

    /**
     * Resolve full storage URL for a stored relative path.
     */
    public function resolveMediaUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return url('storage/' . ltrim($path, '/'));
    }

    /**
     * Scope: messages visible to the requesting side (not deleted for them, not expired).
     */
    public function scopeVisibleFor($query, bool $isAdminSide)
    {
        $query->where($isAdminSide ? 'deleted_for_admin' : 'deleted_for_user', false);

        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope: unread messages for admin/employee.
     */
    public function scopeUnreadForAdmin($query)
    {
        return $query->where('is_admin', false)
            ->where('is_admin_read', false)
            ->where('deleted_for_admin', false)
            ->whereNull('deleted_for_everyone_at');
    }

    /**
     * Scope: unread messages for user.
     */
    public function scopeUnreadForUser($query)
    {
        return $query->where('is_admin', true)
            ->where('is_user_read', false)
            ->where('deleted_for_user', false)
            ->whereNull('deleted_for_everyone_at');
    }
}
