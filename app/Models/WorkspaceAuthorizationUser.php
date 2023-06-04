<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceAuthorizationUser extends Model
{
    use HasFactory;

    protected $table = 'workspace_authorization_users';

    protected $fillable = [
        'workspace_user_id', 'workspace_topic_id'
    ];

    protected $casts = [
        'created_at'    =>  'datetime',
        'updated_at'    =>  'datetime',
    ];
}
