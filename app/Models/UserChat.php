<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChat extends Model
{
    use HasFactory;

    protected $table = 'user_chats';

    protected $fillable = [
        'user_id',
        'admin_id',
        'is_admin',
        'msg',
        'is_admin_read',
        'is_user_read',
        'image',
        'voice',
    ];

    protected $casts = [
        'is_admin'      => 'boolean',
        'is_admin_read' => 'boolean',
        'is_user_read'  => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    protected $appends = [
        'image_url',
        'voice_url',
        'date',
        'time',
    ];

    /**
     * Relationship: target/owner user of the chat.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: admin/employee who sent or replied to the message.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Accessor: Full URL for uploaded image.
     */
    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return null;
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return url('storage/' . ltrim($this->image, '/'));
    }

    /**
     * Accessor: Full URL for uploaded voice file.
     */
    public function getVoiceUrlAttribute()
    {
        if (empty($this->voice)) {
            return null;
        }

        if (filter_var($this->voice, FILTER_VALIDATE_URL)) {
            return $this->voice;
        }

        return url('storage/' . ltrim($this->voice, '/'));
    }

    /**
     * Accessor: formatted date.
     */
    public function getDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d') : null;
    }

    /**
     * Accessor: formatted time.
     */
    public function getTimeAttribute()
    {
        return $this->created_at ? $this->created_at->format('h:i:s A') : null;
    }

    /**
     * Scope: unread messages for admin/employee.
     */
    public function scopeUnreadForAdmin($query)
    {
        return $query->where('is_admin', false)->where('is_admin_read', false);
    }

    /**
     * Scope: unread messages for user.
     */
    public function scopeUnreadForUser($query)
    {
        return $query->where('is_admin', true)->where('is_user_read', false);
    }
}
