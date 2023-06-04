<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            [
                'uid'   =>  'general',
                'name'    =>  'Configurações Gerais',
                'description'    =>  'Configurações gerais do sistema',
                'content'   =>  ['status_create_account' => 'pending']
            ],
        ];



        foreach ($configs as $data){
            if(! \App\Models\Configuration::where('uid', $data['uid'])->exists()){
                \App\Models\Configuration::create($data);
            }
        }
    }
}
