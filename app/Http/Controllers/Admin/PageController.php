<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DatabaseErrorStates;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\FileTrait;
use App\Models\Page;
use App\Models\Workspace;
use App\Models\WorkspaceBlock;
use App\Models\WorkspaceTopic;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PageController extends Controller
{
    use FileTrait;

    public function index(Request $request, $uid){

        $fTopics = $request->topics ?? [];
        $fBlocks = $request->blocks ?? [];

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

        $blocks = WorkspaceBlock::where('workspace_id', $workspace->id)
            ->orderBy('name', 'asc')
            ->get();

        $topics = WorkspaceTopic::whereIn('workspace_block_id', ($blocks->pluck('id') ?? [-1]) )
            ->with(['block:id,name'])
            ->orderBy('name', 'asc')
            ->get();



        $pages = Page::join('workspace_topics as t', 't.id', '=', 'pages.workspace_topic_id')
            ->join('workspace_blocks as b', 'b.id', '=', 't.workspace_block_id')
            ->where('b.workspace_id', $workspace->id)
            ->select(['pages.id', 'pages.workspace_topic_id', 'pages.uid', 'pages.page_name', 'pages.order',
                'pages.screenshot', 'pages.status', 'pages.created_at', 't.name as topic_name', 'b.name as block_name'
            ])
        ;

        if(! empty($fBlocks)){
            $pages = $pages->whereIn('b.id', $fBlocks);
        }

        if(! empty($fTopics)){
            $pages = $pages->whereIn('t.id', $fTopics);
        }

        $pages = $pages->get();



        return view('admin.pages.list-pages')
            ->with('workspace', $workspace)
            ->with('blocks', $blocks)
            ->with('topics', $topics)
            ->with('pages', $pages)
            ;
    }

    public function editPage($uid, $pageId = null){

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

        $blocks = WorkspaceBlock::where('workspace_id', $workspace->id)
            ->orderBy('name', 'asc')
            ->get();

        $page = null;
        $topics = null; // topics of page block
        if(! empty($pageId)){
            $page = Page::join('workspace_topics as t', 't.id', '=', 'pages.workspace_topic_id')
                ->join('workspace_blocks as b', 'b.id', '=', 't.workspace_block_id')
                ->where('pages.id', $pageId)
                ->where('b.workspace_id', $workspace->id)
                ->select('pages.*')
                ->with('topic.block')
                ->first();

            if(! $page){
                abort(404);
            }

            $topics = WorkspaceTopic::where('workspace_block_id', $page->topic->workspace_block_id)
                ->orderBy('order', 'asc')
                ->get();

        }


        return view('admin.pages.edit-page')
            ->with('workspace', $workspace)
            ->with('blocks', $blocks)
            ->with('page', $page)
            ->with('topics', $topics)
            ;

    }



    public function blockList($uid, $blockId = null){
        if(! $blockId){
            abort(404);
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
            ->where('id', $blockId)
            ->with(['topics' => function($q){
                $q->select(['id', 'workspace_block_id', 'name'])
                ->orderBy('order', 'asc')
                ;
            }])
            ->first();

        if(! $block){
            abort(404);
        }

        return response()->json([
            'success'   =>  true,
            'data'  =>  $block->topics->toArray()
        ]);

    }


    public function savePage(Request $request, $uid){
        $rules = [
            'id'    =>  'nullable',
            'name'  =>  'required|max:60',
            'block_id'   =>  'required',
            'topic_id'   =>  'required',
            'html'   =>  'nullable',
            'css'   =>  'nullable',
            'js'   =>  'nullable',
            'base64ScreenShot'   =>  'nullable',
        ];

        $messages = [
            'name.required' =>  'Por favor, informar o nome',
            'name.max' =>  'A quantidade máxima é de :max caracteres',
            'block_id.required' =>  'Por favor, informar o bloco',
            'topic_id.required' =>  'Por favor, informar o tópico',
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

        $page = null;

        if(! empty($request->id)){
            $page = Page::join('workspace_topics as t', 't.id', '=', 'pages.workspace_topic_id')
                ->join('workspace_blocks as b', 'b.id', '=', 't.workspace_block_id')
                ->where('pages.id', $request->id)
                ->where('b.workspace_id', $workspace->id)
                ->select(['pages.*'])
                ->first();
        }

        $file = null;
        $oldFile = (empty($page->screenshot)) ? null : $page?->screenshot?->filenameToStore ?? null;
        $dirFile = storage_path('app/public/pages/thumbs');

        if(! empty($request->base64ScreenShot)){
            $file = $this->convertBase64ToImage(
                base64_image: $request->base64ScreenShot,
                dirPath: $dirFile,
                fileName: date('YmdHis').'-thumb'. uniqid(),
                returnStdClassImg: true
            );
        }

        try{
            if(! $page){
                $page = Page::create([
                    'workspace_topic_id'    =>  $request->topic_id,
                    'uid'   =>  Str::slug($request->name),
                    'page_name' =>  ucwords(mb_strtolower($request->name)),
                    'status'    =>  'published',
                    'order' =>  1000000,
                    'content'   =>  [
                        'css'  =>  $request->css ?? null,
                       // 'js'  =>  $request->js ?? null,
                        'html'  =>  $request->html ?? null,
                        'components'    => $request->components ?? null
                    ],
                    'screenshot'    =>  (empty($file) || empty($file?->filenameToStore) ) ? null : $file
                ]);
            }else{
                $page->workspace_topic_id = $request->topic_id;
                $page->uid = Str::slug($request->name);
                $page->page_name = ucwords(mb_strtolower($request->name));
                $page->content   =  [
                    'css'  =>  $request->css ?? null,
                    //'js'  =>  $request->js ?? null,
                    'html'  =>  $request->html ?? null,
                    'components'  =>  $request->components ?? null,
                ];
                $page->screenshot = (empty($file) || empty($file?->filenameToStore) ) ? null : $file;
                $page->save();
            }
            if(! empty($oldFile)){
                @unlink($dirFile . '/'. $oldFile);
            }
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

        return response()->json([
           'success'    =>  true,
           'id' =>  $page->id
        ]);

    }

    public function deletePage(Request $request, $uid){

        $rules = [
            'id'    =>  'required|numeric',
            'topic_id'  =>  'required|numeric',
        ];

        $messages = [
            'id.required' =>  'Parâmetro não fornecido',
            'id.numeric' =>  'O tipo do parâmetro é inválido',
            'topic_id.required' =>  'Parâmetro não fornecido',
            'topic_id.numeric' =>  'O tipo do parâmetro é inválido',
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

        $page = Page::join('workspace_topics as t', 't.id', '=', 'pages.workspace_topic_id')
                ->join('workspace_blocks as b', 'b.id', '=', 't.workspace_block_id')
                ->where('pages.id', $request->id)
                ->where('t.id', $request->topic_id)
                ->where('b.workspace_id', $workspace->id)
                ->select(['pages.*'])
                ->first();

        if(! $page){
            abort(404);
        }

        $oldFile = (empty($page->screenshot)) ? null : $page?->screenshot?->filenameToStore ?? null;
        $dirFile = storage_path('app/public/pages/thumbs');

        DB::beginTransaction();
        try{
            $page->delete();

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

        if(! empty($oldFile)){
            @unlink($dirFile . '/'. $oldFile);
        }

        return response()->json([
            'success'    =>  true,
            'message' =>  'A página foi removida com sucesso'
        ]);

    }


    public function loadCodePage($uid, $pageId){

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

        $page = Page::join('workspace_topics as t', 't.id', '=', 'pages.workspace_topic_id')
            ->join('workspace_blocks as b', 'b.id', '=', 't.workspace_block_id')
            ->where('pages.id', $pageId)
            ->where('b.workspace_id', $workspace->id)
            ->select(['pages.*'])
            ->first();

        if(! $page){
            abort(404);
        }


        return response()->json([
            'success'   =>  true,
            'html' => $page?->content?->html ?? null,
            'css' => $page?->content?->css ?? null,
           // 'gjs-js' => $page?->content?->js,
            'components' => $page?->content?->components ?? null,
        ]);



    }


    public function getRendererContent($uid, $pageId = null){

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

        $page = Page::join('workspace_topics as t', 't.id', '=', 'pages.workspace_topic_id')
            ->join('workspace_blocks as b', 'b.id', '=', 't.workspace_block_id')
            ->where('pages.id', $pageId)
            ->where('b.workspace_id', $workspace->id)
            ->select(['pages.*'])
            ->first();

        if(! $page){
            abort(404);
        }

        $htnlTemplate = '<html><head><title>HUG</title><style>{css}</style></head>{body}</html>';
        return response()->json([
            'success'   =>  true,
            'id'    =>  $page->id,
            'html'  =>  str_replace(['{css}','{body}'], [($page?->content?->css ?? null), ($page?->content?->html ?? null) ], $htnlTemplate)
        ]);

    }


}
