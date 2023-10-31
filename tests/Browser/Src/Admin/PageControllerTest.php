<?php

namespace Tests\Browser\Src\Admin;

use App\Models\Page;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceBlock;
use App\Models\WorkspaceTopic;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;


class PageControllerTest extends DuskTestCase
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


        $uri = route('admin_list_pages', $workspace->uid);

        $response = $this->actingAs($user, 'admin')->get($uri);

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.list-pages');
        $response->assertViewHas('workspace', $workspace);
    }

    public function test_edit_page(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $blocksPayload = [
            'workspace_id' => $workspace->id,
            'name' => 'Blocks name',
            'indicates_enabled' => 1,
            'order' => 1
        ];

        $blocks = WorkspaceBlock::create($blocksPayload);

        $workspaceTopicsPayload = [
            'workspace_block_id' => $blocks->id,
            'name' => 'Workspace Topics Name',
            'language' => 'PT-BR',
            'indicates_sublevel' => 0,
            'order' => 1,
            'indicates_enabled' => 1
        ];

        $topics = WorkspaceTopic::create($workspaceTopicsPayload);

        $pagesPayload = [
            'workspace_topic_id' => $topics->id,
            'uid' => Str::uuid()->toString(),
            'page_name' => 'loremipsumlazarel',
            'order' => 1
        ];

        $pages = Page::create($pagesPayload);

        $uri = route('admin_edit_page', [$workspace->uid, $pages->id]);

        $response = $this->actingAs($user, 'admin')->get($uri);

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.edit-page');
        $response->assertViewHas('workspace', $workspace);
    }

    public function test_block_list(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $blocksPayload = [
            'workspace_id' => $workspace->id,
            'name' => 'Blocks name',
            'indicates_enabled' => 1,
            'order' => 1
        ];

        $blocks = WorkspaceBlock::create($blocksPayload);

        $workspaceTopicsPayload = [
            'workspace_block_id' => $blocks->id,
            'name' => 'Workspace Topics Name',
            'language' => 'PT-BR',
            'indicates_sublevel' => 0,
            'order' => 1,
            'indicates_enabled' => 1
        ];

        $topics = WorkspaceTopic::create($workspaceTopicsPayload);

        $pagesPayload = [
            'workspace_topic_id' => $topics->id,
            'uid' => Str::uuid()->toString(),
            'page_name' => 'loremipsumlazarel',
            'order' => 1
        ];

        $pages = Page::create($pagesPayload);

        $uri = route('admin_block_list_page', [$workspace->uid, $pages->id]);

        $response = $this->actingAs($user, 'admin')->get($uri);

        $response->assertStatus(200);
        $response->assertJson([
            'success'    =>  true,
            'data' => [
                [
                    'id' => 1,
                    'workspace_block_id' => 1,
                    'name' => 'Workspace Topics Name',
                ]
            ]
            ]);
    }

    public function test_save_page(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $uri = route('admin_save_page', $workspace->uid);

        $response = $this->actingAs($user, 'admin')->post($uri, [
            'name'  =>  'Name new insert',
            'block_id'   =>  1,
            'topic_id'   =>  1
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'    =>  true,
            'id' => 1
        ]);
    }

    public function test_delete_page(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $blocksPayload = [
            'workspace_id' => $workspace->id,
            'name' => 'Blocks name',
            'indicates_enabled' => 1,
            'order' => 1
        ];

        $blocks = WorkspaceBlock::create($blocksPayload);

        $workspaceTopicsPayload = [
            'workspace_block_id' => $blocks->id,
            'name' => 'Workspace Topics Name',
            'language' => 'PT-BR',
            'indicates_sublevel' => 0,
            'order' => 1,
            'indicates_enabled' => 1
        ];

        $topics = WorkspaceTopic::create($workspaceTopicsPayload);

        $pagesPayload = [
            'workspace_topic_id' => $topics->id,
            'uid' => Str::uuid()->toString(),
            'page_name' => 'loremipsumlazarel',
            'order' => 1
        ];

        Page::create($pagesPayload);

        $uri = route('admin_delete_page', $workspace->uid);

        $response = $this->actingAs($user, 'admin')->post($uri, [
            'id'    =>  1,
            'topic_id'  =>  1
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'    =>  true,
            'message' =>  'A página foi removida com sucesso'
        ]);
        $this->assertDatabaseEmpty(Page::class);
    }

    public function test_load_code_page(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $blocksPayload = [
            'workspace_id' => $workspace->id,
            'name' => 'Blocks name',
            'indicates_enabled' => 1,
            'order' => 1
        ];

        $blocks = WorkspaceBlock::create($blocksPayload);

        $workspaceTopicsPayload = [
            'workspace_block_id' => $blocks->id,
            'name' => 'Workspace Topics Name',
            'language' => 'PT-BR',
            'indicates_sublevel' => 0,
            'order' => 1,
            'indicates_enabled' => 1
        ];

        $topics = WorkspaceTopic::create($workspaceTopicsPayload);

        $pagesPayload = [
            'workspace_topic_id' => $topics->id,
            'uid' => Str::uuid()->toString(),
            'page_name' => 'loremipsumlazarel',
            'order' => 1
        ];

        $pages = Page::create($pagesPayload);

        $uri = route('admin_load_code_page', [$workspace->uid, $pages->id]);

        $response = $this->actingAs($user, 'admin')->get($uri);

        $response->assertStatus(200);
        $response->assertJson([
            'success'   =>  true,
            'html' => $pages?->content?->html ?? null,
            'css' => $pages?->content?->css ?? null,
            'components' => $pages?->content?->components ?? null,
        ]);
    }
}
