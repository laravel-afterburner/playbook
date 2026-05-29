<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'current_team_id', 'is_system_admin'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'is_system_admin' => 'boolean',
        ];
    }

    public function isSystemAdmin(): bool
    {
        return (bool) $this->is_system_admin;
    }
}
