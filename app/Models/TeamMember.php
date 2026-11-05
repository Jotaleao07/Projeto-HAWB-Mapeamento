<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'name',
        'role',
        'bio',
        'photo',
        'skills',
        'linkedin',
        'github',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'order' => 'integer',
        ];
    }
}
