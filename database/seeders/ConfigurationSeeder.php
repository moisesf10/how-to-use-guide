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
                'content'   =>  [
                    'system_name' => 'HUG',
                    'copyright' => '© Copyright <b>NIO</b>. All Rights Reserved',
                    'status_create_account' => 'pending'
                ]
            ],
            [
                'uid'   =>  'status-create-account',
                'name'    =>  'Status da conta criada',
                'description'    =>  'Define o status de novas contas criadas no sistema',
                'content'   =>  ['status' => 'pending']
            ],
        ];

        foreach ($configs as $data){
            if (! \App\Models\Configuration::where('uid', $data['uid'])->exists()){
                \App\Models\Configuration::create($data);
            }
        }
    }
}
