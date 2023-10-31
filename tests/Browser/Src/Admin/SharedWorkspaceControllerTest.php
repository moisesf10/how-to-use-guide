<?php

namespace Tests\Browser\Src\Admin;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SharedWorkspaceControllerTest extends DuskTestCase
{
    public function test_index(): void{
        $user = User::find(1);

        $uri = route('admin_shared_workspace');

        $response = $this->actingAs($user, 'admin')->get($uri);

        $response->assertStatus(200);
        $response->assertViewIs('admin.shared.list-workspaces');
    }
}
