<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'user_id',
        'doctor_id',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    /** Paciente que iniciou a conversa. */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /** Com quem estou conversando, dado o usuário atual. */
    public function otherParticipant(User $viewer): User
    {
        return $viewer->id === $this->user_id ? $this->doctor : $this->patient;
    }

    public function involves(User $user): bool
    {
        return $this->user_id === $user->id || $this->doctor_id === $user->id;
    }
}
