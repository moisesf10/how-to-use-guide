<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;

class SharedWorkspaceController extends Controller
{
    public function index(){

        $workspaces = Workspace::join('workspace_editors as e', 'e.workspace_id', '=', 'workspaces.id')
            ->where('workspaces.user_id', '<>', auth()->guard('admin')->user()->id)
            ->where('e.user_id', '=', auth()->guard('admin')->user()->id)
            ->whereRaw("e.status = 'accepted'")
            ->select(['workspaces.*'])
            ->orderBy('workspaces.id', 'desc')
            ->with(['user:id,email,name,created_at'])
            ->get();

        if(! $workspaces){
            abort(404);
        }


        return view('admin.shared.list-workspaces')
            ->with('workspaces',$workspaces)
            ;
    }
}
