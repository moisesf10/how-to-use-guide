<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_block_id', 'name', 'language', 'indicates_sublevel', 'icon', 'order', 'indicates_enabled'
    ];

    protected $casts = [
        'created_at'    =>  'datetime',
        'updated_at'    =>  'datetime',
        'indicates_sublevel'    => 'boolean',
        'indicates_enabled'    => 'boolean',
    ];

    public function block(){
        return $this->hasOne(WorkspaceBlock::class, 'id', 'workspace_block_id');
    }

    public function pages(){
        return $this->hasMany(Page::class, 'workspace_topic_id', 'id');
    }


}
