<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'specialty', 'crm', 'status', 'bio'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_PATIENT = 'patient';
    public const ROLE_DOCTOR = 'doctor';
    public const ROLE_ADMIN = 'admin';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isDoctor(): bool
    {
        return $this->role === self::ROLE_DOCTOR && $this->status === self::STATUS_APPROVED;
    }

    public function isPendingDoctor(): bool
    {
        return $this->role === self::ROLE_DOCTOR && $this->status === self::STATUS_PENDING;
    }

    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }

    /** Conversas em que o usuário é o paciente. */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    /** Conversas em que o usuário é o médico. */
    public function doctorConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'doctor_id');
    }
}
