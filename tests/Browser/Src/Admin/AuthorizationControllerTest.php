<?php

namespace Tests\Browser\Src\Admin;

use App\Http\Controllers\Admin\AuthorizationController;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Token;
use App\Models\WorkspaceAuthorizationUser;
use App\Models\WorkspaceBlock;
use App\Models\WorkspaceEditor;
use App\Models\WorkspaceTopic;
use App\Models\WorkspaceUser;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthorizationControllerTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function test_if_possible_navigate_to_user_page(): void
    {

        $user= User::find(1);

        $payload=[
            'user_id' => $user->id,
            'uid' => Str::uuid()->toString(),
            'name' => 'Test Workspace Dusk Start',
            'description' => 'Description Dusk Test Start',
            'indicates_public_access' => 0,
        ];

        $workspace = Workspace::create($payload);

        $this->browse(function (Browser $browser) use($workspace, $user) {
            $browser->loginAs($user, 'admin')
                ->visit(route('admin_change_type_authorization', [$workspace->uid]   ))
                ->click('div[data-uri="'. route('admin_list_editors_authorization', $workspace->uid) .'"]')
                ->assertRouteIs('admin_list_editors_authorization', $workspace->uid)
                ->visit(route('admin_change_type_authorization', [$workspace->uid]   ))
                ->click('div[data-uri="'. route('admin_list_authorized_users_authorization', $workspace->uid))
                ->assertRouteIs('admin_list_authorized_users_authorization', $workspace->uid);
        });
    }

    public function test_if_it_is_possible_to_browse_within_authorization(): void
    {
        $user= User::find(1);

        $payload= [
            'user_id' => $user->id,
            'uid' => Str::uuid()->toString(),
            'name' => 'Testing',
            'description' => 'Hi Dusk test',
            'indicates_public_access' => 0,
        ];

        $workspace = Workspace::create($payload);

        $this->browse(function (Browser $browser) use ($workspace, $user) {
            $browser->loginAs($user,'admin')
                ->visit(route('admin_list_editors_authorization', [$workspace->uid]))
                ->click(selector: 'a[href="'. route('admin_edit_editor_authorization', $workspace->uid) .'"]')
                ->assertRouteIs('admin_edit_editor_authorization', $workspace->uid)
                ->visit(route('admin_list_editors_authorization', [$workspace->uid]))
                ->click(selector: 'a[href="'. route('admin_manage_workspace', $workspace->uid) .'"]')
                ->assertRouteIs('admin_manage_workspace', $workspace->uid);
        });
    }

    public function test_editor_workspace(): void
    {
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => Str::uuid()->toString(),
            'name' => 'Quincas Del Gado',
            'indicates_public_access' => 0,
        ];
        $workspace = Workspace::create($workspacePayload);

        $editorPayload = [
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'uid' => Str::uuid()->toString(),
            'name' => 'Nombre del editor',
            'email' => 'elpatron@domain.com',
            'status' => 'enabled',
        ];
        $editor = WorkspaceEditor::create($editorPayload);

        $this->browse(function (Browser $browser) use ($editor, $workspace, $user) {
            $browser->loginAs($user, 'admin')
                ->visit(route('admin_edit_editor_authorization', ['id' => $editor->id, $workspace->uid]));
        });
    }

    public function test_if_possible_create_editor_workspace(): void {
        //arrange
        $user = User::find(1);
        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'igor',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $payload = [
            'user_id' => $user->id,
            'uid' => Str::uuid()->toString(),
            'name' => 'Igor',
            'email' => 'rengokuisded@backtoblack.com',
            'status' => 'pending',
        ];

        //act
        Workspace::create($workspacePayload);

        $this->actingAs($user, 'admin')->post('/admin/workspace/manage/1/authorizations/editors/save', $payload);

        //assert
        $success = session()->has('success');
        $this->assertEquals($success, true);
        $this->assertDatabaseHas(WorkspaceEditor::class,[
            'name' => 'Igor',
            'email' => 'rengokuisded@backtoblack.com',
            'status' => 'pending',
        ]);
    }

    public function test_if_possible_update_editor_workspace(): void {
        //arrange
        $user = User::find(1);
        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'igor',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $payload = [
            'user_id' => $user->id,
            'name' => 'Igor',
            'email' => 'rengokuisded@backtoblack.com',
            'status' => 'pending',
        ];

        //act
        $workspace = Workspace::create($workspacePayload);

        $this->actingAs($user, 'admin')->post('/admin/workspace/manage/1/authorizations/editors/save', $payload);

        $editor = WorkspaceEditor::where('workspace_id', $workspace->id)->whereRaw("email = 'rengokuisded@backtoblack.com'")->first();

        $payloadUpdate = [
            'id' => $editor->id,
            'user_id' => $user->id,
            'name' => 'Danzou',
            'email' => 'rengokuisded@backtoblack.com',
            'status' => 'pending',
        ];

        $this->actingAs($user, 'admin')->post('/admin/workspace/manage/1/authorizations/editors/save', $payloadUpdate);

        //assert
        $this->assertDatabaseHas(WorkspaceEditor::class,[
            'name' => 'Danzou',
            'email' => 'rengokuisded@backtoblack.com',
            'status' => 'pending',
        ]);

        $editor = WorkspaceEditor::where('id', $editor->id)->first();

        $this->assertNotEquals($editor->created_at, $editor->updated_at);
    }

    public function test_if_possible_delete_editor_workspace(): void{
        //***** Create
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'nightmare',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $payload = [
            'user_id' => $user->id,
            'uid' => Str::uuid()->toString(),
            'workspace_id' => $workspace->id,
            'name' => 'nightmare',
            'email' => 'avengedsevenfold@trust.com',
            'status' => 'pending',
        ];

        $workspaceEditor = WorkspaceEditor::create($payload);
        //***** Create

        //Act delete
        $this->actingAs($user, 'admin')->post(route('admin_delete_editor_authorization', ['id' => $workspaceEditor['id'], 'uid' => $workspace->uid]));

        $this->assertDatabaseEmpty('workspace_editors');
    }

    public function test_load_invitation_mail(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'nightmare',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $editorPayload = [
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'name' => 'Logan Edit',
            'email' => 'elpatron@domain.com',
            'status' => 'enabled',
        ];

        $editor = WorkspaceEditor::create($editorPayload);

        $tokenPayload = [
            'destination_table_id' => $editor->id,
            'destination_table' => 'App\\Models\\WorkspaceEditor',
            'token' => Str::uuid()->toString(),
            'indicates_enabled' => 1,
            'expires_in' => now()->addMinutes(5)
        ];

        $token = Token::create($tokenPayload);

        // Chamando o método loadInvitationMail
        $uri = route('admin_load_invitation_mail_editor_authorization', [$workspace->uid, $token->token]);

        $response = $this->actingAs($user, guard:'admin')->get($uri);

        $response->assertSee('Você foi convidado para se tornar um editor da workspace');
        //$this->assertEquals(404, $response->status());
    }

    public function test_save_invitation_mail(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'nightmare',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $editorPayload = [
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'name' => 'Logan Edit',
            'email' => 'elpatron@domain.com',
            'status' => 'enabled',
        ];

        $editor = WorkspaceEditor::create($editorPayload);

        $tokenPayload = [
            'destination_table_id' => $editor->id,
            'destination_table' => 'App\\Models\\WorkspaceEditor',
            'token' => Str::uuid()->toString(),
            'indicates_enabled' => 1,
            'expires_in' => now()->addMinutes(5)
        ];

        $token = Token::create($tokenPayload);

        // Chamando o método saveInvitationMail
        $uri = route('admin_save_invitation_mail_editor_authorization', [$workspace->uid, $token->token]);

        $response = $this->actingAs($user, 'admin')->post($uri,[
            'id'    =>  $workspace->id, // workspace_id
            'authorization'  =>  $token->token, // token->token
            'action'  =>  'accepted',
        ]);

        $response->assertHeader('location');

        $this->assertDatabaseHas(WorkspaceEditor::class, [
            'id' => $editor->id,
            'status' => 'accepted',
        ]);
    }

    public function test_authorization_access_page(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag Name',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $uri = route('admin_list_authorized_users_authorization', $workspace->uid);

        $response = $this->actingAs($user, 'admin')->get($uri,[
            'uid'    =>  $workspace->uid, // workspace_uid
        ]);

        //valida view
        $response->assertViewIs('admin.authorizations.list-authorized-users');
    }

    public function test_edit_authorization_access_page(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Melusi',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $blocks = [
            'workspace_id' => $workspace->id,
            'name' => 'Blocks name',
            'indicates_enabled' => 1,
            'order' => 1
        ];

        $workspaceBlocks = WorkspaceBlock::create($blocks);

        $workspaceTopicsPayload = [
            'workspace_block_id' => $workspaceBlocks->id,
            'name' => 'Workspace Topics Name',
            'language' => 'PT-BR',
            'indicates_sublevel' => 0,
            'order' => 1,
            'indicates_enabled' => 1
        ];

        $workspaceTopics = WorkspaceTopic::create($workspaceTopicsPayload);

        $workspaceUsersPayload = [
            'workspace_id' => $workspace->id,
            'name' => 'Melusi',
            'email' => 'melusi@rainbowsever.com',
            'authorization_token' => 'made-in-Narnia',
            'authorization_type' => 'type-one',
            'indicates_enabled' =>  1
        ];

        $workspaceUsers = WorkspaceUser::create($workspaceUsersPayload);

        $workspaceAuthorizationUserPayload = [
            'workspace_user_id' => $workspaceUsers->id,
            'workspace_topic_id' => $workspaceTopics->id,
        ];

        $authorizationsUser = WorkspaceAuthorizationUser::create($workspaceAuthorizationUserPayload);

        $uri = route('admin_edit_authorized_user_authorization', [$workspace->uid,$workspace]);

        $response = $this->actingAs($user, 'admin')->get($uri,[
            'workspace' => $workspace,
            'user' => $user,
            'blocks' => $blocks,
            'authorizationsUser' => $authorizationsUser,
        ]);

        //valida view
        $response->assertViewIs('admin.authorizations.edit-authorized-user');
    }

    public function test_save_authorization_user(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Flag Name',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        // Chamando o método saveAuthorizedUser
        $uri = route('admin_save_authorized_user_authorization', $workspace->uid);

        $request = $this->actingAs($user, 'admin');
        $request->followRedirects = true;

        $response = $request->post($uri,[
            'name'  =>  'Gods Doy', //required
            'email'  =>  'godsdoy@gmail.com', //required
            'authorization_type'    =>  'full', //required
        ]);

        $response->assertViewIs('admin.authorizations.edit-authorized-user');

        $this->assertDatabaseHas(WorkspaceUser::class, [
            'authorization_type' => 'full',
            'workspace_id' => $workspace->id
        ]);
    }

    public function test_delete_authorization_user(): void{
        $user = User::find(1);

        $workspacePayload = [
            'user_id' => $user->id,
            'uid' => '1',
            'name' => 'Melusi',
            'indicates_public_access' => 1,
            'indicates_enabled' => 1,
        ];

        $workspace = Workspace::create($workspacePayload);

        $blocks = [
            'workspace_id' => $workspace->id,
            'name' => 'Blocks name',
            'indicates_enabled' => 1,
            'order' => 1
        ];

        $workspaceBlocks = WorkspaceBlock::create($blocks);

        $workspaceTopicsPayload = [
            'workspace_block_id' => $workspaceBlocks->id,
            'name' => 'Workspace Topics Name',
            'language' => 'PT-BR',
            'indicates_sublevel' => 0,
            'order' => 1,
            'indicates_enabled' => 1
        ];

        $workspaceTopics = WorkspaceTopic::create($workspaceTopicsPayload);

        $workspaceUsersPayload = [
            'workspace_id' => $workspace->id,
            'name' => 'Melusi',
            'email' => 'melusi@rainbowsever.com',
            'authorization_token' => 'made-in-Narnia',
            'authorization_type' => 'type-one',
            'indicates_enabled' =>  1
        ];

        $workspaceUsers = WorkspaceUser::create($workspaceUsersPayload);

        $workspaceAuthorizationUserPayload = [
            'workspace_user_id' => $workspaceUsers->id,
            'workspace_topic_id' => $workspaceTopics->id,
        ];

        WorkspaceAuthorizationUser::create($workspaceAuthorizationUserPayload);

        $uri = route('admin_delete_authorized_user_authorization', $workspace->uid);

        $response = $this->actingAs($user, 'admin')->post($uri,[
            'id' => 1,
        ]);

        $response->assertStatus(200);
        }
}
