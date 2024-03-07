<?php

namespace Tests\Browser\Src\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class GeneralSettingsControllerTest extends DuskTestCase
{
    public function test_index(): void{
        $user = User::find(1);

        $uri = route('admin_list_general_setting');

        $response = $this->actingAs($user, 'admin')->get($uri);

        $response->assertStatus(200);
    }

    public function test_save_general(): void{
        $user = User::find(1);

        $uri = route('admin_save_general_setting');

        $response = $this->actingAs($user, 'admin')->post($uri, [
           'status_create_account' => 'active'
        ]);

        $response->assertSessionHas('success', true);
    }

    public function test_save_smtp(): void{
        $user = User::find(1);

        $uri = route('admin_save_general_smtp');

        $response = $this->actingAs($user, 'admin')->post($uri, [
            'port' => 7171
        ]);

        $response->assertSessionHas('success', true);
        $response->assertSessionHas('message', 'As configurações do SMTP foram salvas com sucesso');
    }

    public function test_save_google(): void{
        $user = User::find(1);

        $uri = route('admin_save_general_google');

        $response = $this->actingAs($user, 'admin')->post($uri);

        $response->assertSessionHas('success', true);
        $response->assertSessionHas('message', 'As configurações do google foram salvas com sucesso');
    }
}
