<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id', 'name', 'indicates_enabled', 'order'
    ];

    protected $casts = [
        'created_at'    =>  'datetime',
        'updated_at'    =>  'datetime',
        'indicates_enabled' =>  'boolean'
    ];

    public function topics(){
        return $this->hasMany(WorkspaceTopic::class, 'workspace_block_id', 'id');
    }


}
