<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_topic_id', 'uid', 'page_name', 'content', 'screenshot', 'order', 'status'
    ];

    protected $casts = [
        'created_at'    =>  'datetime',
        'updated_at'    =>  'datetime',
        'content'    =>  'object',
        'screenshot'    =>  'object',
    ];

    public function topic(){
        return $this->hasOne(WorkspaceTopic::class, 'id', 'workspace_topic_id');
    }

}
