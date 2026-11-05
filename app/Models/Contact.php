<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'notified',
    ];

    protected function casts(): array
    {
        return [
            'notified' => 'boolean',
        ];
    }
}
