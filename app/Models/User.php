<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'specialty', 'crm', 'status', 'bio', 'preferences'])]
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

    /** Padrões das configurações de acessibilidade (4 grupos). */
    public const PREFERENCES_DEFAULT = [
        'visual' => ['enabled' => false, 'contrast' => false, 'daltonism' => 'off', 'bigfont' => false],
        'auditiva' => ['enabled' => false, 'visual_alerts' => false, 'vibrate' => false, 'text_alerts' => false],
        'motora' => ['enabled' => false, 'keyboard' => false, 'bigclick' => false, 'safe_click' => false],
        'cognitiva' => ['enabled' => false, 'no_animations' => false, 'underline_links' => false, 'reading_mode' => false],
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'array',
        ];
    }

    /** Preferências de acessibilidade completas (padrão + o que o usuário salvou). */
    public function a11yPreferences(): array
    {
        return array_replace_recursive(self::PREFERENCES_DEFAULT, $this->preferences ?? []);
    }

    /** Classes CSS aplicadas no <html> conforme as preferências salvas. */
    public function a11yClass(): string
    {
        $p = $this->a11yPreferences();
        $classes = [];

        if ($p['visual']['enabled']) {
            if ($p['visual']['contrast']) {
                $classes[] = 'a11y-contrast';
            }
            if ($p['visual']['bigfont']) {
                $classes[] = 'a11y-bigfont';
            }
            $daltonism = $p['visual']['daltonism'] ?? 'off';
            if ($daltonism !== 'off') {
                $classes[] = 'a11y-daltonism-'.preg_replace('/[^a-z]/', '', $daltonism);
            }
        }

        if ($p['auditiva']['enabled']) {
            if ($p['auditiva']['visual_alerts']) {
                $classes[] = 'a11y-flash';
            }
            if ($p['auditiva']['vibrate']) {
                $classes[] = 'a11y-vibrate';
            }
            if ($p['auditiva']['text_alerts']) {
                $classes[] = 'a11y-toast';
            }
        }

        if ($p['motora']['enabled']) {
            if ($p['motora']['keyboard']) {
                $classes[] = 'a11y-keyboard';
            }
            if ($p['motora']['bigclick']) {
                $classes[] = 'a11y-bigclick';
            }
            if ($p['motora']['safe_click']) {
                $classes[] = 'a11y-safe-click';
            }
        }

        if ($p['cognitiva']['enabled']) {
            if ($p['cognitiva']['no_animations']) {
                $classes[] = 'a11y-noanim';
            }
            if ($p['cognitiva']['underline_links']) {
                $classes[] = 'a11y-underline';
            }
            if ($p['cognitiva']['reading_mode']) {
                $classes[] = 'a11y-reading';
            }
        }

        return implode(' ', $classes);
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
