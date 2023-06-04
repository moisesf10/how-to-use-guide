<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DatabaseErrorStates;
use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{
    public function index(){

        $workspaces = Workspace::where('user_id', auth()->user()->id)
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.workspaces.list-workspaces')
            ->with('workspaces', $workspaces)
            ;
    }

    public function newWorkspace(){


        return view('admin.workspaces.create-workspace');
    }

    public function saveNewWorkspace(Request $request){

        $rules = [
            'name'  =>  'required|max:60',
            'description'   =>  'required|max:300',
            'indicates_public_access'   =>  'in:yes'
        ];

        $messages = [
            'name.required' =>  'Por favor, informar o nome',
            'name.max' =>  'A quantidade máxima é de :max caracteres',
            'description.required' =>  'Por favor, informar a descrição',
            'description.max' =>  'A quantidade máxima é de :max caracteres',
            'indicates_public_access.in'    =>  'Valor inválido'
        ];

        $validate = validator($request->except('_token'), $rules, $messages);

        if ($validate->fails())
        {
            return redirect()->back()
                ->with('success', false)
                ->withInput($request->all())
                ->withErrors($validate);
        }

        $uid = substr(preg_replace('/[^a-zA-Z0-9]/', '', Str::uuid()->toString()) , 0, 14);


        while (Workspace::where('uid', $uid)->exists() ){
            $uid = substr(preg_replace('/[^a-zA-Z0-9]/', '', Str::uuid()->toString()) , 0, 14);
        }

        $workspace = null;
        try{

            $workspace = Workspace::create([
                'user_id'   =>  auth()->user()->id,
                'uid'   =>  $uid,
                'name'  =>  ucwords(mb_strtolower($request->name)),
                'description'   =>  $request->description,
                'indicates_public_access' => ( ($request?->indicates_public_access ?? 0) == 'yes') ? 1 : 0
            ]);
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

        return redirect()->route('admin_manage_workspace', $workspace->uid);
    }

    public function manageWorkspace(Request $request, $uid){

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

        return view('admin.workspaces.manage-workspace')
            ->with('workspace', $workspace)
            ;
    }

    public function editWorkspace($uid){

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

        return view('admin.workspaces.edit-workspace')
            ->with('workspace', $workspace)
            ;

    }

    public function saveEditWorkspace(Request $request, $uid){

        $rules = [
            'name'  =>  'required|max:60',
            'description'   =>  'required|max:300',
            'indicates_public_access'   =>  'in:yes'
        ];

        $messages = [
            'name.required' =>  'Por favor, informar o nome',
            'name.max' =>  'A quantidade máxima é de :max caracteres',
            'description.required' =>  'Por favor, informar a descrição',
            'description.max' =>  'A quantidade máxima é de :max caracteres',
            'indicates_public_access.in'    =>  'Valor inválido'
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

        try{

            $workspace->name = ucwords(mb_strtolower($request->name));
            $workspace->description = $request->description;
            $workspace->indicates_public_access = ( ($request?->indicates_public_access ?? 0) == 'yes') ? 1 : 0;
            $workspace->save();

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

        return redirect()->back()
            ->with('success', true);
    }


    public function deleteWorkspace(Request $request){

        $rules = [
            'id'  =>  'required|exists:workspaces,id',
        ];

        $messages = [
            'id.required' =>  'Por favor, informar a identificação',
            'id.exists' =>  'A workspace informada não existe',
        ];

        $validate = validator($request->except('_token'), $rules, $messages);

        if ($validate->fails())
        {
            return response()->json([
                'success'   =>  false,
                'errors'    =>  $validate->errors()->toArray()
            ], 422);
        }

        $workspace = Workspace::where('id', $request->id)
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

        DB::beginTransaction();
        try{

            $workspace->delete();

        }catch (QueryException $ex){
            DB::rollBack();
            Log::error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
            $errorMessage = DatabaseErrorStates::toString($ex->errorInfo[1]);
            if(app()->environment() == 'production' ){
                return response()->json([
                    'success'   =>  false,
                    'errors'    =>  [$errorMessage ?? 'Falha ao processar requisição. Contacte o suporte' ]
                ], 422);
            }else{
                return response()->json([
                    'success'   =>  false,
                    'errors'    =>  [$ex->getMessage() ]
                ], 422);

            }
        }

        DB::commit();

        return response()->json([
            'success'   =>  true,
            'message'    =>  'A workspace foi removida com sucesso'
        ]);

    }


}
