<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid', 'destination_table_id', 'destination_table', 'token', 'content', 'indicates_enabled', 'expires_in'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'expires_in' => 'datetime',
        'indicates_enabled' => 'boolean',
    ];


    public function item(){
        /*
         * o name "item" indica o nome do atributo final da relations que será utilizado para recuperar os dados do model. ex: $produto->itens->item
         * caso você escolha outro valor para o name diferente de item, então o modelo final terá um atributo item com o valor null que é o nome desta função
         * e outro atributo com o nome que colocar ali no name
         */
        return $this->morphTo('item', 'destination_table', 'destination_table_id', 'id');
    }

}
