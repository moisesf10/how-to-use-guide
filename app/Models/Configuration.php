<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

    protected $fillable = [
       'uid', 'name', 'description', 'content'
    ];

    protected $casts = [
        'created_at'    =>  'datetime',
        'updated_at'    =>  'datetime',
        'content'    =>  'object',
    ];
}
