<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class WorkspaceUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'workspace_id', 'name', 'email', 'authorization_token', 'oauth_provider', 'oauth_id', 'authorization_type', 'indicates_enabled'
    ];

    protected $hidden = [
        'authorization_token',
    ];

    protected $casts = [
        'created_at' =>  'datetime',
        'updated_at' =>  'datetime',
        'indicates_enabled' =>  'boolean',
    ];
}
