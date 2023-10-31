<?php

namespace Tests\Browser\Src\Admin;

use App\Models\LandingPage;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LandingPageControllerTest extends DuskTestCase
{
    public function test_index(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag Name Workspace',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $uri = route('admin_list_landingpages', $workspace->uid);

        $response = $this->actingAs($user, 'admin')->get($uri);

        $response->assertViewIs('admin.landing-pages.list-pages');
        $response->assertStatus(200);
        $response->assertViewHas('workspace', $workspace);
    }

    public function test_edit_page(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag Name Workspace',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $landingPagesPayload = [
            'workspace_id' => $workspace->id,
            'uid' => Str::uuid()->toString(),
            'name' => 'Flag Name Landing Pages',
            'uri' => 'loremipsumlazarel',
            'content' => 1,
            'order' => 1,
            'status' => 1
        ];

        $page = LandingPage::create($landingPagesPayload);

        $uri = route('admin_edit_landingpage', [$workspace->uid, $page->id]);

        $response = $this->actingAs($user, 'admin')->get($uri);

        $response->assertViewIs('admin.landing-pages.edit-page');
        $response->assertViewHas('workspace', $workspace);
        $response->assertViewHas('page', $page);
        $response->assertStatus(200);
    }

    public function test_save_page(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag Name Workspace',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $landingPagesPayload = [
            'workspace_id' => $workspace->id,
            'uid' => Str::uuid()->toString(),
            'name' => 'Flag Name Landing Pages',
            'uri' => 'loremipsumlazarel',
            'content' => 1,
            'order' => 1,
            'status' => 1
        ];

        $page = LandingPage::create($landingPagesPayload);

        $uri = route('admin_save_landingpage', $workspace->uid, $page);

        $response = $this->actingAs($user, 'admin')->post($uri,[
            'name'  =>  'Flag New Name',
            'url'   => 'loremipsumlazarel-xambles',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'uri' => LandingPage::where('id', 2)->value('uri'),
                'id' => 2
            ]);
    }

    public function test_delete_page(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag Name Workspace',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $landingPagesPayload = [
            'workspace_id' => $workspace->id,
            'uid' => Str::uuid()->toString(),
            'name' => 'Flag Name Landing Pages',
            'uri' => 'loremipsumlazarel',
            'content' => 1,
            'order' => 1,
            'status' => 1
        ];

        LandingPage::create($landingPagesPayload);

        $uri = route('admin_delete_landingpage', $workspace->uid);

        $response = $this->actingAs($user, 'admin')->post($uri, [
            'id' => 1
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success'    =>  true,
                'message' =>  'A página foi removida com sucesso'
            ]);
        $this->assertDatabaseEmpty(LandingPage::class);
    }

    public function test_load_code_page(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag Name Workspace',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $landingPagesPayload = [
            'workspace_id' => $workspace->id,
            'uid' => Str::uuid()->toString(),
            'name' => 'Flag Name Landing Pages',
            'uri' => 'loremipsumlazarel',
            'content' => 1,
            'order' => 1,
            'status' => 1
        ];

        $page = LandingPage::create($landingPagesPayload);

        $uri = route('admin_load_code_landingpage', [$workspace->uid, $page->id]);

        $response = $this->actingAs($user, 'admin')->get($uri);

        $response->assertJson([
            'success'   =>  true,
            'uri'   =>  $page->uri,
            'html' => $page?->content?->html ?? null,
            'css' => $page?->content?->css ?? null,
            'components' => $page?->content?->components ?? null,
        ]);
    }

    public function test_order_page(): void {
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag Name Workspace',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $landingPagesPayload = [
            'workspace_id' => $workspace->id,
            'uid' => Str::uuid()->toString(),
            'name' => 'Flag Name Landing Pages',
            'uri' => 'loremipsumlazarel',
            'content' => 1,
            'order' => 1,
            'status' => 1
        ];

        LandingPage::create($landingPagesPayload);

        $uri = route('admin_order_landingpage', $workspace->uid);

        $response = $this->actingAs($user, 'admin')->post($uri, [
           'order' => ['1']
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success'   =>  true
            ]);
    }
}
