<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DatabaseErrorStates;
use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Models\WorkspaceBlock;
use App\Models\WorkspaceTopic;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TopicController extends Controller
{
    public function index($uid){

        $workspace = Workspace::where('uid', $uid)
            ->where(function ($q){
                $q->where('user_id', auth()->user()->id)
                    ->orWhereHas('workspaceEditors', function ($q2){
                        $q2->where('user_id', auth()->user()->id);
                    });
            })
            ->with(['blocks.topics:id,workspace_block_id'])
            ->first();


        if(! $workspace ){
            abort(404);
        }

        return view('admin.topics.choose-block')
            ->with('workspace', $workspace)
            ;
    }

    public function listTopics($uid, $blockId){

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
            ->with(['topics' => function($q){
                $q->orderBy('order','asc');
            }])
            ->first();

        if(! $block){
            abort(404);
        }

        return view('admin.topics.list-topics')
            ->with('workspace', $workspace)
            ->with('block', $block)
            ;

    }

    public function editTopic($uid, $blockId, $topicId = null){

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

        if(! $block){
            abort(404);
        }

        $topic = null;

        if(! empty($topicId)){
            $topic = WorkspaceTopic::where('id', $topicId)
                ->where('workspace_block_id', $block->id)
                ->first();

            if(! $topic){
                abort(404);
            }

        }

        return view('admin.topics.edit-topic')
            ->with('workspace', $workspace)
            ->with('block', $block)
            ->with('topic', $topic)
            ;

    }

    public function saveTopic(Request $request, $uid, $blockId){

        $rules = [
            'id'    =>  'nullable|integer|exists:workspace_topics,id',
            'name'  =>  'required|max:60',
            'indicates_sublevel' =>  'nullable|in:yes',
            'indicates_enabled' =>  'nullable|in:yes',
        ];

        $messages = [
            'id.integer'    =>  'Parâmetro incorreto',
            'id.exists'    =>  'Parâmetro incorreto',
            'name.required' =>  'Por favor, informar o nome do tópico',
            'name.max' =>  'O tamanho máximo do nome é de :max caracteres',
            'indicates_sublevel.in' =>  'O valor para o campo de indica subnível é inválido',
            'indicates_enabled.in' =>  'O valor para o campo de indica ativo é inválido',
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

        $block = WorkspaceBlock::where('id', $blockId)
            ->where('workspace_id', $workspace->id)
            ->first();

        if(! $block){
            abort(404);
        }

        $topic = WorkspaceTopic::where('id', ($request->id ?? -1) )->where('workspace_block_id', $block->id)->first();

        if(! $topic && ! empty($request->id)){
            abort(404);
        }



        try{
            if(! $topic){
                $topic = WorkspaceTopic::create([
                    'workspace_block_id'    =>  $block->id,
                    'name'  =>  $request->name,
                    'language'  =>  'pt',
                    'icon'  =>  $request->icon,
                    'order' =>  1000000,
                    'indicates_sublevel'    =>  (($request->indicates_sublevel ?? 0) == 'yes') ? 1 : 0,
                    'indicates_enabled'    =>  (($request->indicates_enabled ?? 0) == 'yes') ? 1 : 0,
                ]);
            }else{
                $topic->name = $request->name;
                $topic->icon = $request->icon;
                $topic->indicates_sublevel = (($request->indicates_sublevel ?? 0) == 'yes') ? 1 : 0;
                $topic->indicates_enabled = (($request->indicates_enabled ?? 0) == 'yes') ? 1 : 0;
                $topic->save();
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

        return redirect()->route('admin_edit_topic', [$workspace->uid, $block->id, $topic->id])
            ->with('success', true);
    }

    public function orderTopic(Request $request, $uid, $blockId){

        $rules = [
            'order'  =>  'required|array'
        ];

        $messages = [
            'order.required' =>  'Os parâmetros não foram informados',
            'order.array' =>  'O tipo informado é inválido',
        ];

        $workspace = null;

        $validate = validator($request->except('_token'), $rules, $messages);
        $validate->after(function ($validator) use ($uid, $blockId){
            if(empty($uid)){
                $validator->errors()->add(['uid' => 'Uid não informado']);
            }
            if(empty($blockId)){
                $validator->errors()->add(['uid' => 'Bloco não informado']);
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

        $block = WorkspaceBlock::where('workspace_id', $workspace->id)
            ->where('id', $blockId)
            ->first();

        if(! $block){
            return response()->json([
                'success'   =>  false,
                'errors'    =>  ['Bloco não localizado']
            ]);
        }

        DB::beginTransaction();
        try{

            foreach ($request->order ?? [] as $order => $id){
                WorkspaceTopic::where('workspace_block_id', $block->id)
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

    public function deleteTopic(Request $request, $uid){

        $rules = [
            'id'  =>  'required|exists:workspace_topics,id'
        ];

        $messages = [
            'id.required' =>  'Os parâmetros não foram informados',
            'id.exists' =>  'O item informado não existe',
        ];



        $validate = validator($request->except('_token'), $rules, $messages);

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

        if(! $workspace){
            abort(404);
        }

        $topic = WorkspaceTopic::join('workspace_blocks as b', 'b.id', '=', 'workspace_topics.workspace_block_id')
            ->where('workspace_topics.id', $request->id)
            ->where('b.workspace_id', $workspace->id)
            ->select(['workspace_topics.*'])
            ->first();

        if(! $topic){
            abort(404);
        }

        DB::beginTransaction();
        try{
            $topic->delete();

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
            'success'   =>  true,
            'message'   =>  'O tópico foi removido com sucesso'
        ]);



    }


}
