<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    public function connect(Request $request, $workspaceName, $uid){

        $workspace = Workspace::where('uid', $uid)
            ->whereRaw("indicates_enabled = 1")
            ->first();

        if(! $workspace){
            abort(404);
        }

        if($workspace?->indicates_public_access){
            return redirect()->route('guide_start', [$workspace->name, $workspace->uid]);
        }

        $workspaceUser = null;
        if(! auth()->check()){
            $code = '';
            if(! empty($request->query('code'))){
                try{
                   $code = Crypt::decrypt($request->query('code'));
                }catch (\Exception $e){}
            }

            $userData = json_decode($code);
            $workspaceUser = WorkspaceUser::where('email', ($userData->email ?? -1)  )
                ->where('authorization_token', ($userData->authorization_token ?? -1) )
                ->where('workspace_id', $workspace->id)
                ->first();

            if(! $workspaceUser){
                return redirect()->route('guide_login', [$workspace->name, $workspace->uid ]);
            }

            auth()->login($workspaceUser);
        }


        if(! auth()->check()){
            return redirect()->route('guide_login', [$workspace->name, $workspace->uid ]);
        }

        return redirect()->route('guide_start', [$workspace->name, $workspace->uid]);

    }

    public function login($workspaceName, $uid){

        $workspace = Workspace::where('uid', $uid)
            ->whereRaw("indicates_enabled = 1")
            ->first();

        if(! $workspace){
            abort(404);
        }

        return view('guide.login')
            ->with('workspace', $workspace)
            ;
    }

    public function logout(Request $request, $workspaceName, $uid){
        auth()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect()->route('guide_login', [$workspaceName, $uid]);
    }


}
