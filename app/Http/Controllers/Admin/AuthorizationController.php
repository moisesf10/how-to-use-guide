<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DatabaseErrorStates;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessEmail;
use App\Mail\AuthorizeEditorMail;
use App\Mail\InviteUserGuideMail;
use App\Models\Token;
use App\Models\Workspace;
use App\Models\WorkspaceAuthorizationUser;
use App\Models\WorkspaceBlock;
use App\Models\WorkspaceEditor;
use App\Models\WorkspaceUser;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthorizationController extends Controller
{
    public function index(Request $request, $uid){

        $workspace = Workspace::where('uid', $uid)
            ->where(function ($q){
                $q->where('user_id', auth()->user()->id)
                    ->orWhereHas('workspaceEditors', function ($q2){
                        $q2->where('user_id', auth()->user()->id);
                    });
            })
            ->first();

        if(! $workspace){
            abort(404);
        }

        return view('admin.authorizations.change-type')
            ->with('workspace', $workspace)
            ;

    }

    public function editorsPage(Request $request, $uid){

        $workspace = Workspace::where('uid', $uid)
            ->where(function ($q){
                $q->where('user_id', auth()->user()->id)
                    ->orWhereHas('workspaceEditors', function ($q2){
                        $q2->where('user_id', auth()->user()->id);
                    });
            })
            ->with(['editors'])
            ->first();

        if(! $workspace){
            abort(404);
        }

        return view('admin.authorizations.list-editors')
            ->with('workspace', $workspace)
            ;

    }

    public function editEditorPage(Request $request, $uid, $id = null){
        $workspace = Workspace::where('uid', $uid)
            ->where(function ($q){
                $q->where('user_id', auth()->user()->id)
                    ->orWhereHas('workspaceEditors', function ($q2){
                        $q2->where('user_id', auth()->user()->id);
                    });
            })
            ->first();

        if(! $workspace){
            abort(404);
        }

        $editor = null;

        if(! empty($id)){
            $editor = WorkspaceEditor::where('workspace_id', $workspace->id)
                ->where('id', $id)
                ->with('user:id,name,email')
                ->first();

            if(! $editor){
                abort(404);
            }
        }

        return view('admin.authorizations.edit-editor')
            ->with('workspace', $workspace)
            ->with('editor', $editor)
            ;

    }


    public function saveEditor(Request $request, $uid){

        $rules = [
            'id'    =>  'nullable|integer',
            'name'  =>  'required|max:120',
            'email'  =>  'nullable|email|required_without:id|max:80',
            'status' =>  'nullable|required_with:id|in:pending,enabled,disabled',
            'indicates_resend_mail' =>  'nullable|in:yes'
        ];

        $messages = [
            'id.integer'    =>  'Parâmetro incorreto',
            'name.required' =>  'Por favor, informar o nome do usuário',
            'name.max' =>  'o tamanho máximo do nome é de :max caracteres',
            'email.required_without'    =>  'Por favor, informar o e-mail',
            'email.email'    =>  'O e-mail informado é inválido',
            'email.max'    =>  'O tamanho máximo do e-mail é de :max caracteres',
            'status.required_with' => 'Por favor, informar o status',
            'status.in' => 'O status informado é inválido',
            'indicates_resend_mail.in'  =>  'O valor informado para o reenvio de e-mail é inválido'
        ];


        $validate = validator($request->except('_token'), $rules, $messages);

        if ($validate->fails())
        {
            return redirect()->back()
                ->with('success', false)
                ->withInput($request->all())
                ->withErrors($validate);
        }

        $workspace = Workspace::where('uid', $uid)
            ->where(function ($q){
                $q->where('user_id', auth()->user()->id)
                    ->orWhereHas('workspaceEditors', function ($q2){
                        $q2->where('user_id', auth()->user()->id);
                    });
            })
            ->with('user:id,email,name')
            ->first();

        if(! $workspace){
            abort(404);
        }

        $editor = null;
        if(! empty($request->id)){
            $editor = WorkspaceEditor::where('id', ($request->id ?? -1) )
                ->where('workspace_id', $workspace->id)
                ->first();

            if(! $editor){
                abort(404);
            }
        }


        $token = null;
        DB::beginTransaction();
        try{

            if(! $editor){

                $editor = WorkspaceEditor::create([
                    'workspace_id'  =>  $workspace->id,
                    'name'  =>  ucwords(mb_strtolower($request->name)),
                    'email' =>  $request->email,
                    'status' =>  'pending'
                ]);
                $token = Token::create([
                    'token'   =>  mb_strtolower(Str::uuid()->toString()),
                    'destination_table_id'  =>  $editor->id,
                    'destination_table' =>  'App\\Models\\WorkspaceEditor',
                    'indicates_enabled' =>  1,
                    'expires_in'    =>  now()->addMonths(1)->format('Y-m-d H:i:s')
                ]);

            }else{

                $editor->name = ucwords(mb_strtolower($request->name));
                $editor->status = ( $request?->status == 'pending') ? $editor->status : mb_strtolower($request->status);
                $editor->save();

                if($request->indicates_resend_mail == 'yes'){
                    $token = Token::create([
                        'token'   =>  mb_strtolower(Str::uuid()->toString()),
                        'destination_table_id'  =>  $editor->id,
                        'destination_table' =>  'App\\Models\\WorkspaceEditor',
                        'indicates_enabled' =>  1,
                        'expires_in'    =>  now()->addMonths(1)->format('Y-m-d H:i:s')
                    ]);
                }
            }

        }catch (QueryException $ex){
            DB::rollBack();
            Log::error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
            $errorMessage = DatabaseErrorStates::toString($ex->errorInfo[1]);
            if(app()->environment() == 'production' ){
                return redirect()->back()
                    ->with('success', false)
                    ->withInput($request->except('token'))
                    ->withErrors([$errorMessage ?? 'Falha ao processar requisição. Contacte o suporte' ]);
            }else{
                return redirect()->back()
                    ->with('success', false)
                    ->withInput($request->except('token'))
                    ->withErrors([$ex->getMessage()]);
            }
        }
        DB::commit();


        if(! empty(config('mail.from.address')) && $token && ( $request->indicates_resend_mail == 'yes' || (  $editor?->status_send_mail == null ||  $editor?->status_send_mail == 'error'  ) ) ){
            $email = (new AuthorizeEditorMail(workspace: $workspace, editor:  $editor, token:  $token))
                ->onQueue('emails');
            $when = now();
            Mail::to($editor->email)->later($when, $email  );
        }

        return redirect()->route('admin_edit_editor_authorization', [$workspace->uid, $editor->id])
            ->with('success', true)
        ;

    }

    public function deleteEditor(Request $request, $uid){

        $rules = [
            'id'    =>  'required|integer',
        ];

        $messages = [
            'id.required'    =>  'Um parâmetro não foi informado',
            'id.integer' =>  'O tipo do parâmetro é inválido',
        ];


        $validate = validator($request->except('_token'), $rules, $messages);

        if ($validate->fails())
        {
            return redirect()->back()
                ->with('success', false)
                ->withInput($request->all())
                ->withErrors($validate);
        }

        $workspace = Workspace::where('uid', $uid)
            ->where(function ($q){
                $q->where('user_id', auth()->user()->id)
                    ->orWhereHas('workspaceEditors', function ($q2){
                        $q2->where('user_id', auth()->user()->id);
                    });
            })
            ->first();

        if(! $workspace){
            abort(404);
        }

        $editor = WorkspaceEditor::where('id', $request->id)
            ->where('workspace_id', $workspace->id)
            ->first();

        if(! $editor){
            abort(404);
        }

        DB::beginTransaction();
        try{

            Token::whereRaw("destination_table = 'App\\Models\\WorkspaceEditor'")
                ->where('destination_table_id', $editor->id )
                ->delete();

            $editor->delete();

        }catch (QueryException $ex){
            DB::rollBack();
            Log::error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
            $errorMessage = DatabaseErrorStates::toString($ex->errorInfo[1]);
            if(app()->environment() == 'production' ){
                return redirect()->back()
                    ->with('success', false)
                    ->withInput($request->except('token'))
                    ->withErrors([$errorMessage ?? 'Falha ao processar requisição. Contacte o suporte' ]);
            }else{
                return redirect()->back()
                    ->with('success', false)
                    ->withInput($request->except('token'))
                    ->withErrors([$ex->getMessage()]);
            }
        }
        DB::commit();

        return response()->json([
            'success'   =>  true,
            'message'   =>  'O editor foi removido com sucesso'
        ]);


    }


    public function loadInvitationMail(Request $request, $uid, $authorization){


        $workspace = Workspace::where('uid', $uid)
            ->with('user:id,email,name')
            ->first();

        $token = Token::where('token', $authorization)
            ->whereRaw('indicates_enabled = 1')
            ->where('expires_in', '>=', date('Y-m-d H:i'))
            ->with(['item'])
            ->first();

        if(! $workspace || ! $token){
            abort(404);
        }


        if($token?->item->getTable()  != 'workspace_editors'  ){
            abort(500, 'table is not workspace_editors');
        }

        $editor = $token->item;

        if($editor->workspace_id != $workspace->id){
            abort(404);
        }

        if(! auth()->guard('admin')->check()){
            return redirect()->route('admin_login',['redirect-after-login' => urlencode($request->fullUrl())  ]);
        }

        return view('admin.authorizations.invitation-mail-editor')
            ->with('workspace', $workspace)
            ->with('token', $token)
            ->with('editor', $editor)
            ;

    }

    public function saveInvitationMail(Request $request, $uid){

        $rules = [
            'id'    =>  'required', // workspace_id
            'authorization'  =>  'required', // token->token
            'action'  =>  'required|in:accepted,rejected',
        ];

        $messages = [
            'id.required'    =>  'Parâmetro incorreto',
            'authorization.required'    =>  'Autorização não informada',
            'action.required'   =>  'A ação não foi informada',
            'action.in' =>  'O valor da ação é inválido'
        ];


        $validate = validator($request->except('_token'), $rules, $messages);

        if ($validate->fails())
        {
            return redirect()->back()
                ->with('success', false)
                ->withInput($request->all())
                ->withErrors($validate);
        }


        $workspace = Workspace::where('uid', $uid)
            ->where('id', $request->id)
            ->first();

        if(! $workspace){
            abort(404, 'Workspace não localizada');
        }

        $token = Token::where('token', $request->authorization)
            ->whereRaw('indicates_enabled = 1')
            ->where('expires_in', '>=', date('Y-m-d H:i'))
            ->with(['item'])
            ->first();

        if( ! $token){
            abort(404, 'A autorização não é válida');
        }


        if($token?->item->getTable()  != 'workspace_editors'  ){
            abort(500, 'table is not workspace_editors');
        }

        $editor = $token->item;

        if($editor->workspace_id != $workspace->id){
            abort(404, 'Workspace id not same editor values');
        }

        DB::beginTransaction();
        try{
            $editor->user_id = auth()->user()->id;
            $editor->status = $request->action;
            $editor->save();

            $token->indicates_enabled = 0;
            $token->save();



        }catch (QueryException $ex){
            DB::rollBack();
            Log::error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
            $errorMessage = DatabaseErrorStates::toString($ex->errorInfo[1]);
            if(app()->environment() == 'production' ){
                return redirect()->back()
                    ->with('success', false)
                    ->withInput($request->except('token'))
                    ->withErrors([$errorMessage ?? 'Falha ao processar requisição. Contacte o suporte' ]);
            }else{
                return redirect()->back()
                    ->with('success', false)
                    ->withInput($request->except('token'))
                    ->withErrors([$ex->getMessage()]);
            }
        }
        DB::commit();

        $message = ($request->action == 'accepted') ? 'Seu convite foi aceito com sucesso' : 'Seu convite foi rejeitado com sucesso';

        return redirect()->route('admin_invitations_editor_authorization', $workspace->uid)
            ->with('success', true)
            ->with('message', $message);

    }

    public function authorizationAccessPage(Request $request, $uid){

        $workspace = Workspace::where('uid', $uid)
            ->where(function ($q){
                $q->where('user_id', auth()->user()->id)
                    ->orWhereHas('workspaceEditors', function ($q2){
                        $q2->where('user_id', auth()->user()->id);
                    });
            })
            ->with(['authorizedUsers'])
            ->first();

        if(! $workspace){
            abort(404);
        }

        return view('admin.authorizations.list-authorized-users')
            ->with('workspace', $workspace)
            ;


    }

    public function editAuthorizationAccessPage(Request $request, $uid, $id = null){
        $workspace = Workspace::where('uid', $uid)
            ->where(function ($q){
                $q->where('user_id', auth()->user()->id)
                    ->orWhereHas('workspaceEditors', function ($q2){
                        $q2->where('user_id', auth()->user()->id);
                    });
            })
            ->first();

        if(! $workspace){
            abort(404);
        }

        $user = null;
        if(! empty($id)){
            $user = WorkspaceUser::where('workspace_id',$workspace->id)
                ->where('id', $id)
                ->first();

            if(! $user){
                abort(404);
            }
        }

        $blocks = WorkspaceBlock::where('workspace_id', $workspace->id)
            ->with('topics', function ($q){
                $q->orderBy('order', 'asc');
            })
            ->get();

        $authorizationsUser = WorkspaceAuthorizationUser::where('workspace_user_id', $user?->id ?? -1)
            ->get();


        return view('admin.authorizations.edit-authorized-user')
            ->with('workspace', $workspace)
            ->with('user', $user)
            ->with('blocks', $blocks)
            ->with('authorizationsUser', $authorizationsUser)
            ;


    }

    public function saveAuthorizationUser(Request $request, $uid){

        $rules = [
            'id'    =>  'nullable', // workspace_id
            'name'  =>  'required|max:120',
            'email'  =>  'required|email|max:80',
            'password'  =>  'nullable',
            'indicates_enabled' =>  'nullable|in:yes',
            'authorization_type'    =>  'required|in:full,partial',
            'authorizations'    =>  'nullable|array|required_if:authorization_type,partial',
            'authorizations.*'  =>  'required|numeric'
        ];

        $messages = [
            'name.required'    =>  'Por favor, informar o nome',
            'name.max'    =>  'O tamanho máximo do nome é de :max caracteres',
            'email.required'   =>  'Por favor, informar o e-mail',
            'email.email' =>  'O e-mail informado é inválido',
            'email.max' =>  'O tamanho máximo do e-mail é de :max caracteres',
            'indicates_enabled.in' =>  'O valor é inválido',
            'authorization_type.required'    => 'Informe um tipo de autorização',
            'authorization_type.in'    => 'O tipo de autorização é inválido',
            'authorizations.array'    => 'O formato das autorizações são inválidos',
            'authorizations.required_if'    => 'Para autorização parcial é obrigatório informar uma autorização',
            'authorizations.*.required'    => 'Por favor, informar um item de autorização',
            'authorizations.*.numeric'    => 'O item de autorização possui o tipo inválido',
        ];


        $validate = validator($request->except('_token'), $rules, $messages);

        if ($validate->fails())
        {
            return redirect()->back()
                ->with('success', false)
                ->withInput($request->all())
                ->withErrors($validate);
        }



        $workspace = Workspace::where('uid', $uid)
            ->where(function ($q){
                $q->where('user_id', auth()->user()->id)
                    ->orWhereHas('workspaceEditors', function ($q2){
                        $q2->where('user_id', auth()->user()->id);
                    });
            })
            ->with('user:id,email,name')
            ->first();

        if(! $workspace){
            abort(404);
        }

        $userAuthorized = null;
        if(! empty($request->id)){
            $userAuthorized = WorkspaceUser::where('id', ($request->id ?? -1) )
                ->where('workspace_id', $workspace->id)
                ->first();

            if(! $userAuthorized){
                abort(404);
            }
        }



        DB::beginTransaction();
        try{

            if(! $userAuthorized){
                $userAuthorized = WorkspaceUser::create([
                    'workspace_id'  =>  $workspace->id,
                    'name'  =>  ucwords(mb_strtolower($request->name)),
                    'email' =>  $request->email,
                    'authorization_token'   =>  Str::uuid()->toString(),
                    'indicates_enabled' =>  1,
                    'authorization_type'    => $request->authorization_type
                ]);

                if($request->authorization_type == 'partial'){
                    foreach ($request->authorizations ?? [] as $topicId){
                        WorkspaceAuthorizationUser::create([
                            'workspace_user_id' =>  $userAuthorized->id,
                            'workspace_topic_id'    =>  $topicId
                        ]);
                    }
                }

            }else{
                $userAuthorized->name = ucwords(mb_strtolower($request->name));
                $userAuthorized->indicates_enabled = ( $request?->indicates_enabled == 'yes') ? 1 : 0;
                $userAuthorized->authorization_type = $request->authorization_type;
                $userAuthorized->indicates_enabled = ($request->indicates_enabled == 'yes') ? 1 : 0;
                $userAuthorized->save();

                if($request->authorization_type == 'partial') {
                    WorkspaceAuthorizationUser::where('workspace_user_id', $userAuthorized->id)
                        ->whereNotIn('workspace_topic_id', $request->authorizations ?? [-1])
                        ->delete();

                    foreach ($request->authorizations ?? [] as $topicId){
                        WorkspaceAuthorizationUser::updateOrCreate([
                            'workspace_user_id' =>  $userAuthorized->id,
                            'workspace_topic_id'    =>  $topicId
                        ]);
                    }

                }else{
                    WorkspaceAuthorizationUser::where('workspace_user_id', $userAuthorized->id)
                        ->delete();
                }


            }

        }catch (QueryException $ex){
            DB::rollBack();
            Log::error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
            $errorMessage = DatabaseErrorStates::toString($ex->errorInfo[1]);
            if(app()->environment() == 'production' ){
                return redirect()->back()
                    ->with('success', false)
                    ->withInput($request->except('token'))
                    ->withErrors([$errorMessage ?? 'Falha ao processar requisição. Contacte o suporte' ]);
            }else{
                return redirect()->back()
                    ->with('success', false)
                    ->withInput($request->except('token'))
                    ->withErrors([$ex->getMessage()]);
            }
        }
        DB::commit();

        if(! empty(config('mail.from.address'))  && ( $request->indicates_resend_mail == 'yes' ) ){
            $email = (new InviteUserGuideMail(authorizedUser: $userAuthorized, workspace:  $workspace))
                ->onQueue('emails');
            $when = now();
            Mail::to($userAuthorized->email)->later($when, $email  );
        }

        return redirect()->route('admin_edit_authorized_user_authorization', [$workspace->uid, $userAuthorized->id])
            ->with('success', true)
            ;

    }

    public function deleteAuthorizationUser(Request $request, $uid){

        $rules = [
            'id'    =>  'required|integer',
        ];

        $messages = [
            'id.required'    =>  'Um parâmetro não foi informado',
            'id.integer'    =>  'O tipo de um parâmetro é inválido',
        ];


        $validate = validator($request->except('_token'), $rules, $messages);

        if ($validate->fails())
        {
            return response()->json([
                'success'   =>  false,
                'errors'    => $validate->errors()->toArray()
            ], 422);
        }


        $workspace = Workspace::where('uid', $uid)
            ->where(function ($q){
                $q->where('user_id', auth()->user()->id)
                    ->orWhereHas('workspaceEditors', function ($q2){
                        $q2->where('user_id', auth()->user()->id);
                    });
            })
            ->first();

        if(! $workspace){
            abort(404);
        }

        $authorizedUser = WorkspaceUser::where('workspace_id', $workspace->id)
            ->where('id', $request->id)
            ->first();

        if(! $authorizedUser){
            abort(404);
        }

        DB::beginTransaction();
        try{
            $authorizedUser->delete();

        }catch (QueryException $ex){
            DB::rollBack();
            Log::error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
            $errorMessage = DatabaseErrorStates::toString($ex->errorInfo[1]);
            if(app()->environment() == 'production' ){
                return response()->json([
                    'success'   =>  false,
                    'errors'    => [$errorMessage ?? 'Falha ao processar requisição. Contacte o suporte' ]
                ], 422);
            }else{
                return response()->json([
                    'success'   =>  false,
                    'errors'    => [$ex->getMessage()]
                ], 422);
            }
        }
        DB::commit();

        return response()->json([
            'success'   =>  true,
            'message'   =>  'O usuário foi removido com sucesso'
        ]);


    }


}
