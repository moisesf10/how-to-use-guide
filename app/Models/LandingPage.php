<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id', 'uid', 'name', 'uri', 'content', 'screenshot', 'order', 'status'
    ];

    protected $casts = [
        'created_at'    =>  'datetime',
        'updated_at'    =>  'datetime',
        'content'    =>  'object',
        'screenshot'    =>  'object',
    ];
}
