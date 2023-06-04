<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'uid', 'name', 'description', 'indicates_public_access', 'indicates_enabled'
    ];

    protected $casts = [
        'created_at'    =>  'datetime',
        'updated_at'    =>  'datetime',
        'indicates_public_access'   =>  'boolean',
        'indicates_enabled' =>  'boolean'
    ];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function workspaceEditors(){
        return $this->hasMany(WorkspaceEditor::class, 'workspace_id', 'id');
    }

    public function blocks(){
        return $this->hasMany(WorkspaceBlock::class, 'workspace_id', 'id');
    }

    public function userEditors(){
        return $this->hasManyThrough(User::class, WorkspaceEditor::class, 'workspace_id', 'id', 'id', 'user_id');
    }

    public function editors(){
        return $this->hasMany(WorkspaceEditor::class, 'workspace_id', 'id');
    }

    public function authorizedUsers(){
        return $this->hasMany(WorkspaceUser::class, 'workspace_id', 'id');
    }

}
