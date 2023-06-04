<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DatabaseErrorStates;
use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Models\WorkspaceBlock;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlockController extends Controller
{
    public function index($uid){

        $workspace = Workspace::where('uid', $uid)
            ->where(function ($q){
                $q->where('user_id', auth()->user()->id)
                    ->orWhereHas('workspaceEditors', function ($q2){
                        $q2->where('user_id', auth()->user()->id);
                    });
            })
            ->with(['blocks' => function($q){
                $q->orderBy('order', 'asc')
                ->orderBy('id', 'asc')
                ;
            }])
            ->first();

        if(! $workspace){
            abort(404);
        }

        return view('admin.blocks.list-blocks')
            ->with('workspace',$workspace)
            ;
    }

    public function editBlock($uid, $blockId = null){

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

        $block = WorkspaceBlock::where('id', $blockId)
            ->where('workspace_id', $workspace->id)
            ->first();

        if(! empty($blockId) && ! $block){
            abort(404);
        }

        return view('admin.blocks.edit-block')
            ->with('workspace',$workspace)
            ->with('block', $block)
            ;
    }

    public function save(Request $request, $uid){

        $rules = [
            'id'    =>  'nullable|integer',
            'name'  =>  'required|max:60',
            'indicates_enabled' =>  'nullable|in:yes'
        ];

        $messages = [
            'id.integer'    =>  'Parâmetro incorreto',
            'name.required' =>  'Por favor, informar o nome do bloco',
            'indicates_enabled.in' =>  'O valor para o campo de indica ativo é inválido'
        ];

        $workspace = null;

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

        $block = WorkspaceBlock::where('id', ($request->id ?? -1) )
            ->where('workspace_id', $workspace->id)
            ->first();

        try{

            if(! $block){
                $block = WorkspaceBlock::create([
                    'workspace_id'  =>  $workspace->id,
                    'name'  =>  ucwords(mb_strtolower($request->name)),
                    'indicates_enabled' =>  (($request->indicates_enabled ?? 0) == 'yes') ? 1 : 0,
                    'order' =>  1000000
                ]);
            }else{
                $block->name = ucwords(mb_strtolower($request->name));
                $block->indicates_enabled = (($request->indicates_enabled ?? 0) == 'yes') ? 1 : 0;
                $block->save();
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

        return redirect()->route('admin_edit_block', [$workspace->uid, $block->id])
            ->with('success', true);
    }


    public function reorder(Request $request, $uid){

        $rules = [
            'order'  =>  'required|array'
        ];

        $messages = [
            'order.required' =>  'Os parâmetros não foram informados',
            'order.array' =>  'O tipo informado é inválido',
        ];

        $workspace = null;

        $validate = validator($request->except('_token'), $rules, $messages);
        $validate->after(function ($validator) use ($uid){
            if(empty($uid)){
                $validator->errors()->add(['uid' => 'Uid não informado']);
            }
        });

        if ($validate->fails())
        {
            return response()->json([
                'success'   =>  false,
                'errors'    =>  $validate->errors()->toArray()
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

        DB::beginTransaction();
        try{

            foreach ($request->order ?? [] as $order => $id){
                WorkspaceBlock::where('workspace_id', $workspace->id)
                    ->where('id', $id)
                    ->update(['order' => ($order + 1)]);
            }

        }catch (QueryException $ex){
            DB::rollBack();
            Log::error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
            $errorMessage = DatabaseErrorStates::toString($ex->errorInfo[1]);
            if(app()->environment() == 'production' ){;
                return response()->json([
                    'success'   =>  false,
                    'errors' => [$errorMessage ?? 'Falha ao processar requisição. Contacte o suporte' ]
                ], 422);

            }else{
                return response()->json([
                    'success'   =>  false,
                    'errors' => [$ex->getMessage()]
                ], 422);

            }
        }

        DB::commit();

        return response()->json([
            'success'   =>  true
        ]);


    }

    public function delete(Request $request, $uid){

        $rules = [
            'id'    =>  'required|exists:workspace_blocks,id',
        ];

        $messages = [
            'id.required'    =>  'Identificação não inormada',
            'id.exists' =>  'O bloco não existe',
        ];

        $validate = validator($request->except('_token'), $rules, $messages);

        if ($validate->fails())
        {
            return response()->json([
                'success'   => false,
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

        $block = WorkspaceBlock::where('workspace_id', $workspace->id)
            ->where('id', $request->id)
            ->first();

        if(! $block){
            abort(404);
        }

        DB::beginTransaction();
        try{

            $block->delete();

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
            'message'    =>  'O bloco foi removido com sucesso'
        ]);

    }

    public function changeStatus(Request $request, $uid){

        $rules = [
            'id'    =>  'required|exists:workspace_blocks,id',
            'status'  =>  'required|in:1,0',
        ];

        $messages = [
            'id.required'    =>  'Identificação não inormada',
            'id.exists' =>  'O bloco não existe',
            'status.required' =>  'Por favor, informar o status',
            'status.in' =>  'O status é inválido',
        ];

        $validate = validator($request->except('_token'), $rules, $messages);

        if ($validate->fails())
        {
            return response()->json([
                'success'   => false,
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

        $block = WorkspaceBlock::where('workspace_id', $workspace->id)
            ->where('id', $request->id)
            ->first();

        if(! $block){
            abort(404);
        }

        DB::beginTransaction();
        try{

            $block->indicates_enabled = $request->status;
            $block->save();

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
            'message'    =>  'O status foi alterado com sucesso'
        ]);

    }


}
