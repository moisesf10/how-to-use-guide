<?php

namespace Tests\Browser\Src\Admin;

use App\Http\Controllers\Admin\TopicController;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceBlock;
use App\Models\WorkspaceTopic;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TopicControllerTest extends DuskTestCase
{
    public function test_index(): void{
        $user = User::find(1);

        $payload= [
            'user_id' => $user->id,
            'uid' => Str::uuid()->toString(),
            'name' => 'Flag name',
            'description' => 'Hi Dusk test',
            'indicates_public_access' => 0,
        ];

        $workspace = Workspace::create($payload);

        $uri = route('admin_choose_block_topics', $workspace->uid);

        $response = $this->actingAs($user, 'admin')->get($uri);

        $response->assertViewIs('admin.topics.choose-block');
        $response->assertStatus(200);
        $response->assertViewHas('workspace', $workspace);
    }

    public function test_list_topics(): void {
        $user = User::find(1);

        $payload= [
            'user_id' => $user->id,
            'uid' => Str::uuid()->toString(),
            'name' => 'Flag name',
            'description' => 'Hi Dusk test',
            'indicates_public_access' => 0,
        ];

        $workspace = Workspace::create($payload);

        $blocksPayload = [
            'workspace_id' => $workspace->id,
            'name' => 'Blocks name',
            'indicates_enabled' => 1,
            'order' => 1
        ];

        $block = WorkspaceBlock::create($blocksPayload);

        $uri = route('admin_list_topics', [$workspace->uid, $block]);

        $response = $this->actingAs($user, 'admin')->get($uri);

        $response->assertStatus(200);
        $response->assertViewIs('admin.topics.list-topics');
        $response->assertViewHas('workspace', $workspace);
        $response->assertViewHas('block', $block);
    }

    public function test_edit_topic(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Melusi',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $blockPayload = [
            'workspace_id' => $workspace->id,
            'name' => 'Blocks name',
            'indicates_enabled' => 1,
            'order' => 1
        ];

        $block = WorkspaceBlock::create($blockPayload);

        $workspaceTopicsPayload = [
            'workspace_block_id' => $block->id,
            'name' => 'Flag name',
            'language' => 'PT-BR',
            'indicates_sublevel' => 0,
            'order' => 1,
            'indicates_enabled' => 1
        ];

        $topic = WorkspaceTopic::create($workspaceTopicsPayload);

        $uri = route('admin_edit_topic', [$workspace->uid, $block->id, $topic->id]);

        $response = $this->actingAs($user, 'admin')->get($uri);

        $response->assertStatus(200);
        $response->assertViewIs('admin.topics.edit-topic');
        $response->assertViewHas('workspace', $workspace);
        $response->assertViewHas('block', $block);
        $response->assertViewHas('topic', $topic);
    }

    public function test_save_topic(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Melusi',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $blockPayload = [
            'workspace_id' => $workspace->id,
            'name' => 'Blocks name',
            'indicates_enabled' => 1,
            'order' => 1
        ];

        $block = WorkspaceBlock::create($blockPayload);

        $workspaceTopicsPayload = [
            'workspace_block_id' => $block->id,
            'name' => 'Flag name',
            'language' => 'PT-BR',
            'indicates_sublevel' => 0,
            'order' => 1,
            'indicates_enabled' => 1
        ];

        WorkspaceTopic::create($workspaceTopicsPayload);

        $uri = route('admin_save_topic', [$workspace->uid, $block->id]);

        $response = $this->actingAs($user, 'admin')->post($uri, [
            'name'  =>  'Insert new name'
        ]);

        $realTopicId = 2;

        $response->assertStatus(302);
        $response->assertSessionHas('success', true);
        $response->assertRedirect(route('admin_edit_topic', [$workspace->uid, $block->id, $realTopicId]));
    }

    public function test_order_topic(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Melusi',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $blockPayload = [
            'workspace_id' => $workspace->id,
            'name' => 'Blocks name',
            'indicates_enabled' => 1,
            'order' => 1
        ];

        $block = WorkspaceBlock::create($blockPayload);

        $uri = route('admin_order_topic', [$workspace->uid, $block->id]);

        $response = $this->actingAs($user, 'admin')->post($uri, [
            'order' => [1]
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'   =>  true
        ]);
    }

    public function test_delete_topic(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Melusi',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $blockPayload = [
            'workspace_id' => $workspace->id,
            'name' => 'Blocks name',
            'indicates_enabled' => 1,
            'order' => 1
        ];

        $block = WorkspaceBlock::create($blockPayload);

        $workspaceTopicsPayload = [
            'workspace_block_id' => $block->id,
            'name' => 'Flag name',
            'language' => 'PT-BR',
            'indicates_sublevel' => 0,
            'order' => 1,
            'indicates_enabled' => 1
        ];

        $topic = WorkspaceTopic::create($workspaceTopicsPayload);

        $uri = route('admin_delete_topic', [$workspace->uid, $block->id]);

        $response = $this->actingAs($user, 'admin')->post($uri, [
            'id' => 1
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'   =>  true,
            'message'   =>  'O tópico foi removido com sucesso'
        ]);
        $this->assertDatabaseEmpty(WorkspaceTopic::class);
    }
}
