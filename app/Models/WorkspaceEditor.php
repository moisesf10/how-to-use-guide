<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceEditor extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id', 'user_id', 'email', 'name', 'status_send_mail', 'last_attemp_send_mail', 'mail_error', 'status'
    ];

    protected $casts = [
        'created_at'    =>  'datetime',
        'updated_at'    =>  'datetime',
    ];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
