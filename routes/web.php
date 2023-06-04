<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/* quando for implementar internacionalização, mudar rotas ativas para atender ao prefixo lang conforme exemplo abaixo
Route::prefix('{lang?}/admin')->group(function(){
    Route::get('/', function () {
         return view('admin.template_admin');
    });
});*/



Route::prefix('admin')->group(function(){


    Route::get('/login', [\App\Http\Controllers\Admin\LoginController::class, 'index'])->name('admin_login');
    Route::post('/login/authenticate', [\App\Http\Controllers\Admin\LoginController::class, 'authenticate'])->name('admin_authenticate');
    Route::get('/create-account', [\App\Http\Controllers\Admin\AccountController::class, 'index'])->name('admin_create_account');
    Route::post('/create-account/save', [\App\Http\Controllers\Admin\AccountController::class, 'save'])->name('admin_save_create_account');
    Route::get('/logout', [\App\Http\Controllers\Admin\LoginController::class, 'logout'])->middleware('auth:admin')->name('admin_logout');
    Route::get('/login/google', [\App\Http\Controllers\LoginGoogleController::class, 'loginGoogleAdmin'])->name('admin_login_google');
    Route::get('/login/google/callback', [\App\Http\Controllers\LoginGoogleController::class, 'loginGoogleAdminCallback'])->name('admin_login_google_callback');

    Route::group(['middleware' => ['auth:admin']], function (){
        Route::get('/', [App\Http\Controllers\Admin\WorkspaceController::class, 'index'])->name('admin_index');

        Route::get('/configurations/general', [App\Http\Controllers\Admin\GeneralSettingsController::class, 'index'])->name('admin_list_general_setting');
        Route::post('/configurations/general/save-general', [App\Http\Controllers\Admin\GeneralSettingsController::class, 'saveGeneral'])->name('admin_save_general_setting');
        Route::post('/configurations/general/save-smtp', [App\Http\Controllers\Admin\GeneralSettingsController::class, 'saveSmtp'])->name('admin_save_general_smtp');
        Route::post('/configurations/general/save-google', [App\Http\Controllers\Admin\GeneralSettingsController::class, 'saveGoogle'])->name('admin_save_general_google');



        Route::get('/workspace/new', [App\Http\Controllers\Admin\WorkspaceController::class, 'newWorkspace'])->name('admin_new_workspace') ;
        Route::post('/workspace/new/create', [App\Http\Controllers\Admin\WorkspaceController::class, 'saveNewWorkspace'])->name('admin_save_new_workspace') ;
        Route::get('/workspace/manage/{uid}', [App\Http\Controllers\Admin\WorkspaceController::class, 'manageWorkspace'])->name('admin_manage_workspace');
        Route::get('/workspace/manage/{uid}/edit-workspace', [App\Http\Controllers\Admin\WorkspaceController::class, 'editWorkspace'])->name('admin_edit_workspace');
        Route::post('/workspace/manage/{uid}/edit-workspace/save', [App\Http\Controllers\Admin\WorkspaceController::class, 'saveEditWorkspace'])->name('admin_save_edit_workspace');
        Route::post('/workspace/delete', [App\Http\Controllers\Admin\WorkspaceController::class, 'deleteWorkspace'])->name('admin_delete_workspace');

        Route::get('/workspace/shared', [App\Http\Controllers\Admin\SharedWorkspaceController::class, 'index'])->name('admin_shared_workspace');

        Route::get('/workspace/manage/{uid}/blocks', [App\Http\Controllers\Admin\BlockController::class, 'index'])->name('admin_list_blocks');
        Route::get('/workspace/manage/{uid}/blocks/edit/{blockId?}', [App\Http\Controllers\Admin\BlockController::class, 'editBlock'])->name('admin_edit_block');
        Route::post('/workspace/manage/{uid}/blocks/save', [App\Http\Controllers\Admin\BlockController::class, 'save'])->name('admin_save_block');
        Route::post('/workspace/manage/{uid}/blocks/delete', [App\Http\Controllers\Admin\BlockController::class, 'delete'])->name('admin_delete_block');
        Route::post('/workspace/manage/{uid}/blocks/change-status', [App\Http\Controllers\Admin\BlockController::class, 'changeStatus'])->name('admin_change_block');
        Route::post('/workspace/manage/{uid}/blocks/reorder', [App\Http\Controllers\Admin\BlockController::class, 'reorder'])->name('admin_reorder_block');

        Route::get('/workspace/manage/{uid}/topics/choose-block', [App\Http\Controllers\Admin\TopicController::class, 'index'])->name('admin_choose_block_topics');
        Route::get('/workspace/manage/{uid}/topics/list/{blockId}', [App\Http\Controllers\Admin\TopicController::class, 'listTopics'])->name('admin_list_topics');
        Route::get('/workspace/manage/{uid}/topics/edit/{blockId}/{topicId?}', [App\Http\Controllers\Admin\TopicController::class, 'editTopic'])->name('admin_edit_topic');


        Route::post('/workspace/manage/{uid}/topics/save/{blockId}', [App\Http\Controllers\Admin\TopicController::class, 'saveTopic'])->name('admin_save_topic');
        Route::post('/workspace/manage/{uid}/topics/delete/{blockId}', [App\Http\Controllers\Admin\TopicController::class, 'deleteTopic'])->name('admin_delete_topic');
        Route::post('/workspace/manage/{uid}/topics/order/{blockId}', [App\Http\Controllers\Admin\TopicController::class, 'orderTopic'])->name('admin_order_topic');

        Route::get('/workspace/manage/{uid}/pages/list', [App\Http\Controllers\Admin\PageController::class, 'index'])->name('admin_list_pages');
        //Route::get('/workspace/manage/{uid}/pages/get-renderer-content/{pageId?}', [App\Http\Controllers\Admin\PageController::class, 'getRendererContent'])->name('admin_get_renderer_content_pages');
        Route::get('/workspace/manage/{uid}/pages/edit/{pageId?}', [App\Http\Controllers\Admin\PageController::class, 'editPage'])->name('admin_edit_page');
        Route::get('/workspace/manage/{uid}/pages/load-code-page/{pageId?}', [App\Http\Controllers\Admin\PageController::class, 'loadCodePage'])->name('admin_load_code_page');
        Route::post('/workspace/manage/{uid}/pages/save', [App\Http\Controllers\Admin\PageController::class, 'savePage'])->name('admin_save_page');
        Route::post('/workspace/manage/{uid}/pages/delete', [App\Http\Controllers\Admin\PageController::class, 'deletePage'])->name('admin_delete_page');
//        Route::post('/workspace/manage/{uid}/pages/order/{blockId}', [App\Http\Controllers\Admin\PageController::class, 'deletePage'])->name('admin_delete_page');


        Route::get('/workspace/manage/{uid}/pages/list-blocks/{blockId?}', [App\Http\Controllers\Admin\PageController::class, 'blockList'])->name('admin_block_list_page');


        Route::get('/workspace/manage/{uid}/landing-pages/list', [App\Http\Controllers\Admin\LandingPageController::class, 'index'])->name('admin_list_landingpages');
        Route::get('/workspace/manage/{uid}/landing-pages/edit/{pageId?}', [App\Http\Controllers\Admin\LandingPageController::class, 'editPage'])->name('admin_edit_landingpage');
        Route::get('/workspace/manage/{uid}/landing-pages/load-code-page/{pageId?}', [App\Http\Controllers\Admin\LandingPageController::class, 'loadCodePage'])->name('admin_load_code_landingpage');
        Route::post('/workspace/manage/{uid}/landing-pages/save', [App\Http\Controllers\Admin\LandingPageController::class, 'savePage'])->name('admin_save_landingpage');
        Route::post('/workspace/manage/{uid}/landing-pages/delete', [App\Http\Controllers\Admin\LandingPageController::class, 'deletePage'])->name('admin_delete_landingpage');
        Route::post('/workspace/manage/{uid}/lading-pages/order', [App\Http\Controllers\Admin\LandingPageController::class, 'orderPage'])->name('admin_order_landingpage');

        Route::get('/workspace/manage/{uid}/authorizations/change-type', [App\Http\Controllers\Admin\AuthorizationController::class, 'index'])->name('admin_change_type_authorization');
        Route::get('/workspace/manage/{uid}/authorizations/editors', [App\Http\Controllers\Admin\AuthorizationController::class, 'editorsPage'])->name('admin_list_editors_authorization');
        Route::get('/workspace/manage/{uid}/authorizations/editors/edit-editor/{id?}', [App\Http\Controllers\Admin\AuthorizationController::class, 'editEditorPage'])->name('admin_edit_editor_authorization');
        Route::get('/workspace/manage/{uid}/authorizations/editors/load-invitation-mail/{authorization}', [App\Http\Controllers\Admin\AuthorizationController::class, 'loadInvitationMail'])->withoutMiddleware(['auth:admin'])->name('admin_load_invitation_mail_editor_authorization');
        Route::get('/workspace/manage/{uid}/authorizations/editors/invitations', [App\Http\Controllers\Admin\AuthorizationController::class, 'invitations'])->name('admin_invitations_editor_authorization');
        Route::post('/workspace/manage/{uid}/authorizations/editors/save-invitation-mail', [App\Http\Controllers\Admin\AuthorizationController::class, 'saveInvitationMail'])->name('admin_save_invitation_mail_editor_authorization');

        Route::post('/workspace/manage/{uid}/authorizations/editors/save', [App\Http\Controllers\Admin\AuthorizationController::class, 'saveEditor'])->name('admin_save_editor_authorization');
        Route::post('/workspace/manage/{uid}/authorizations/editors/delete', [App\Http\Controllers\Admin\AuthorizationController::class, 'deleteEditor'])->name('admin_delete_editor_authorization');

        Route::get('/workspace/manage/{uid}/authorizations/guide', [App\Http\Controllers\Admin\AuthorizationController::class, 'authorizationAccessPage'])->name('admin_list_authorized_users_authorization');
        Route::get('/workspace/manage/{uid}/authorizations/guide/edit-guide-access/{id?}', [App\Http\Controllers\Admin\AuthorizationController::class, 'editAuthorizationAccessPage'])->name('admin_edit_authorized_user_authorization');
        Route::post('/workspace/manage/{uid}/authorizations/guide/save', [App\Http\Controllers\Admin\AuthorizationController::class, 'saveAuthorizationUser'])->name('admin_save_authorized_user_authorization');
        Route::post('/workspace/manage/{uid}/authorizations/guide/delete', [App\Http\Controllers\Admin\AuthorizationController::class, 'deleteAuthorizationUser'])->name('admin_delete_authorized_user_authorization');


    });
});

Route::prefix('guide')->group(function(){

    Route::get('/login/google', [\App\Http\Controllers\LoginGoogleController::class, 'loginGoogleAdmin'])->name('guide_login_google');
    Route::get('/login/google/callback', [\App\Http\Controllers\LoginGoogleController::class, 'loginGoogleAdminCallback'])->name('guide_login_google_callback');

    Route::get('/workspace/{workspacename}/{uid}', [App\Http\Controllers\Guide\LoginController::class, 'connect'])->name('guide_connect');
    Route::get('/workspace/{workspacename}/{uid}/login', [App\Http\Controllers\Guide\LoginController::class, 'login'])->name('guide_login');
    Route::get('/workspace/{workspacename}/{uid}/logout', [App\Http\Controllers\Guide\LoginController::class, 'logout'])->name('guide_logout');


    Route::get('/workspace/{workspacename}/{uid}/start', [App\Http\Controllers\Guide\GuideController::class, 'start'])->name('guide_start');
    Route::get('/workspace/{workspacename}/{uid}/page/{blockname}/{blockid}/{pageuid?}/{pageid?}', [App\Http\Controllers\Guide\GuideController::class, 'page'])->name('guide_page');
    Route::get('/workspace/{workspacename}/{uid}/load-page/{pageid}', [App\Http\Controllers\Guide\GuideController::class, 'loadPage'])->name('guide_load_page');
    Route::get('/lp/workspace/{workspacename}/{uid}/{page?}', [App\Http\Controllers\Guide\GuideController::class, 'accessLandingPage'])->name('landing_page_guide');



});

// Quando implementar lang, fazer um redirecionamento da rota / para a rota /en. Desta forma a requisição entrará nas rotas prefixadas acima
Route::prefix('/')->group(function(){
    Route::get('/', [App\Http\Controllers\Site\IndexController::class, 'index'])->name('index');
});

