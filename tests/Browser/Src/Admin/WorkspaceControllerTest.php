<?php

namespace Tests\Browser\Src\Admin;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class WorkspaceControllerTest extends DuskTestCase
{
    public function test_index(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag name',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspaces = Workspace::create($workspacePayload);

        $uri = route('admin_index', $workspaces);

        $response = $this->actingAs($user, 'admin')->get($uri);

        $response->assertStatus(200);
        $response->assertViewIs('admin.workspaces.list-workspaces');
    }

    public function test_new_workspace(): void{
        $user = User::find(1);
        $uri = route('admin_new_workspace');

        $response = $this->actingAs($user, 'admin')->get($uri);

        $response->assertViewIs('admin.workspaces.create-workspace');
    }

    public function test_save_new_workspace(): void{
        $user = User::find(1);

        $uri = route('admin_save_new_workspace');

        $response = $this->actingAs($user, 'admin')->post($uri, [
            'name' => 'New name flag',
            'description' => 'New description flag',
            'indicates_public_access' => 'yes'
        ]);

        $createdWorkspace = Workspace::where('name', 'New name flag')->first();

        $response->assertStatus(302);
        $response->assertRedirect(route('admin_manage_workspace', ['uid' => $createdWorkspace->uid]));
    }

    public function test_manage_workspace(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $uri = route('admin_manage_workspace', $workspace->uid);

        $response = $this->actingAs($user, 'admin')->get($uri);

        $response->assertStatus(200);
        $response->assertViewIs('admin.workspaces.manage-workspace');
        $response->assertViewHas('workspace', $workspace);
    }

    public function test_edit_workspace(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $uri = route('admin_edit_workspace', $workspace->uid);

        $response = $this->actingAs($user, 'admin')->get($uri);

        $response->assertStatus(200);
        $response->assertViewIs('admin.workspaces.edit-workspace');
        $response->assertViewHas('workspace', $workspace);
    }

    public function test_save_edit_workspace(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $uri = route('admin_save_edit_workspace', $workspace->uid);

        $response = $this->actingAs($user, 'admin')->post($uri, [
            'name'  =>  'New name flag',
            'description'   =>  'New description flag',
            'indicates_public_access'   =>  'yes'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success', true);
    }

    public function test_delete_workspace(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        Workspace::create($workspacePayload);

        $uri = route('admin_delete_workspace');

        $response = $this->actingAs($user, 'admin')->post($uri, [
            'id' => 1
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'   =>  true,
            'message'    =>  'A workspace foi removida com sucesso'
        ]);
        $this->assertDatabaseEmpty(Workspace::class);
    }
}
