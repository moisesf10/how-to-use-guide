<?php

namespace Tests\Browser\Src\Admin;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceBlock;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BlockControllerTest extends DuskTestCase
{
    public function test_index(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $response = $this->actingAs($user, 'admin')
            ->get(route('admin_list_blocks', $workspace->uid));
        $response->assertStatus(200)
            ->assertViewIs('admin.blocks.list-blocks') // Verifica se a view é a esperada
            ->assertViewHas('workspace', $workspace);
    }

    public function test_edit_block(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $response = $this->actingAs($user, 'admin')
            ->get(route('admin_edit_block', [$workspace->uid]));
        $response->assertStatus(200)
            ->assertViewIs('admin.blocks.edit-block') // Verifica se a view é a esperada
            ->assertViewHas('workspace', $workspace);
    }

    public function test_save(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $uri = route('admin_save_block', $workspace->uid);

        $request = $this->actingAs($user, 'admin');
        $request->followRedirects = true;

        $response = $request->post($uri,[
            'name'  =>  'Flag Name',
            'indicates_enabled' =>  'yes'
        ]);

        $this->assertDatabaseHas(WorkspaceBlock::class, [
            'name' => 'Flag Name',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('admin.blocks.edit-block');
    }

    public function test_reorder(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $workspaceBlocksPayload = [
            'workspace_id' => $workspace->id,
            'name' => 'Flag Name Block',
            'indicates_enabled' => 1,
            'order' => 1
        ];

        $WorkspaceBlock = WorkspaceBlock::create($workspaceBlocksPayload);

        $uri = route('admin_reorder_block', $workspace->uid);

        $request = $this->actingAs($user, 'admin');
        $response = $request->post($uri,[
            'order' => [3]
        ]);

        $response->assertStatus(200);
            //$this->assertDatabaseHas(WorkspaceBlock::class, [
            //    'order' => 4
            //]);

    }

    public function test_delete(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $workspaceBlocksPayload = [
            'workspace_id' => $workspace->id,
            'name' => 'Flag Name Block',
            'indicates_enabled' => 1,
            'order' => 1
        ];

        $WorkspaceBlock = WorkspaceBlock::create($workspaceBlocksPayload);

        $uri = route('admin_delete_block', $workspace->uid);

        $request = $this->actingAs($user, 'admin');
        $response = $request->post($uri, [
            'id' => 1
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseEmpty(WorkspaceBlock::class);
    }

    public function test_change_status(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $workspaceBlocksPayload = [
            'workspace_id' => $workspace->id,
            'name' => 'Flag Name Block',
            'indicates_enabled' => 1,
            'order' => 1
        ];

        $WorkspaceBlocks = WorkspaceBlock::create($workspaceBlocksPayload);

        $uri = route('admin_change_block', $workspace->uid);

        $request = $this->actingAs($user, 'admin');
        $response = $request->post($uri, [
            'id'    =>  $WorkspaceBlocks->id,
            'status'  =>  1,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'O status foi alterado com sucesso',
            ]);
    }
}
