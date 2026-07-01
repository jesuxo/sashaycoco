<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    protected $table = 'chat_conversations';
    protected $fillable = [
        'session_id',
        'user_id',
        'visitor_name',
        'visitor_email',
        'ip_address',
        'user_agent',
        'status',
        'started_at',
        'ended_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime'
    ];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSummary()
    {
        return [
            'id' => $this->id,
            'started' => $this->started_at->format('Y-m-d H:i:s'),
            'message_count' => $this->messages()->count(),
            'last_message' => $this->messages()->latest()->first()?->message
        ];
    }
}
