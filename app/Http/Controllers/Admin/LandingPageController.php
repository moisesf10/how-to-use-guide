<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DatabaseErrorStates;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\FileTrait;
use App\Models\LandingPage;
use App\Models\Workspace;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LandingPageController extends Controller
{
    use FileTrait;

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

        $pages = LandingPage::where('workspace_id', $workspace->id)
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->select(['id', 'name', 'uri', 'screenshot', 'order', 'status', 'created_at'])
            ->get();


        return view('admin.landing-pages.list-pages')
            ->with('workspace', $workspace)
            ->with('pages', $pages)
            ;

    }

    public function editPage($uid, $pageId = null)
    {

        $workspace = Workspace::where('uid', $uid)
            ->where(function ($q) {
                $q->where('user_id', auth()->user()->id)
                    ->orWhereHas('workspaceEditors', function ($q2) {
                        $q2->where('user_id', auth()->user()->id);
                    });
            })
            ->first();

        if (!$workspace) {
            abort(404);
        }

        $page = null;
        if(! empty($pageId)){
            $page = LandingPage::where('id', $pageId)
                ->where('workspace_id', $workspace->id)
                ->select(['id', 'workspace_id', 'name', 'uri', 'screenshot', 'order', 'status', 'created_at'])
                ->first();

            if(! $page){
                abort(404);
            }

        }


        return view('admin.landing-pages.edit-page')
            ->with('workspace', $workspace)
            ->with('page', $page)
            ;

    }

    public function savePage(Request $request, $uid){
        $rules = [
            'id'    =>  'nullable',
            'name'  =>  'required|max:60',
            'url'   =>  'required|max:200',
            'html'   =>  'nullable',
            'css'   =>  'nullable',
            'js'   =>  'nullable',
            'base64ScreenShot'   =>  'nullable',
        ];

        $messages = [
            'name.required' =>  'Por favor, informar o nome',
            'name.max' =>  'A quantidade máxima é de :max caracteres',
            'url.required' =>  'Por favor, informar a URL',
            'url.max' =>  'A quantidade máxima é de :max caracteres',
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
            $page = LandingPage::where('workspace_id', $workspace->id)
                ->where('id', $request->id)
                ->first();
        }

        $file = null;
        $oldFile = (empty($page->screenshot)) ? null : $page?->screenshot?->filenameToStore ?? null;
        $dirFile = storage_path('app/public/landing-pages/thumbs');

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
                $page = LandingPage::create([
                    'workspace_id'    =>  $workspace->id,
                    'uid'   =>  Str::slug($request->name),
                    'name' =>  ucwords(mb_strtolower($request->name)),
                    'uri'   =>  mb_strtolower(preg_replace('/[ ]/', '-', $request->url)),
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
                $page->workspace_id = $workspace->id;
                $page->uid = Str::slug($request->name);
                $page->name = ucwords(mb_strtolower($request->name));
                $page->uri   =  mb_strtolower(preg_replace('/[ ]/', '-', $request->url));
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
            'uri'   =>  $page->uri,
            'id' =>  $page->id
        ]);

    }

    public function deletePage(Request $request, $uid){

        $rules = [
            'id'    =>  'required|numeric',
        ];

        $messages = [
            'id.required' =>  'Parâmetro não fornecido',
            'id.numeric' =>  'O tipo do parâmetro é inválido',
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

        $page = LandingPage::where('id', $request->id)
            ->where('workspace_id', $workspace->id)
            ->first();

        if(! $page){
            abort(404);
        }

        $oldFile = (empty($page->screenshot)) ? null : $page?->screenshot?->filenameToStore ?? null;
        $dirFile = storage_path('app/public/landing-pages/thumbs');

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

        $page = LandingPage::where('workspace_id', $workspace->id)
            ->where('id', $pageId)
            ->first();

        if(! $page){
            abort(404);
        }


        return response()->json([
            'success'   =>  true,
            'uri'   =>  $page->uri,
            'html' => $page?->content?->html ?? null,
            'css' => $page?->content?->css ?? null,
            // 'gjs-js' => $page?->content?->js,
            'components' => $page?->content?->components ?? null,
        ]);

    }

    public function orderPage(Request $request, $uid){

        $rules = [
            'order'  =>  'required|array'
        ];

        $messages = [
            'order.required' =>  'Os parâmetros não foram informados',
            'order.array' =>  'O tipo informado é inválido',
        ];

        $workspace = null;

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
            return response()->json([
                'success'   =>  false,
                'errors'    =>  ['Workspace não localizada']
            ]);
        }

        DB::beginTransaction();
        try{

            foreach ($request->order ?? [] as $order => $id){
                LandingPage::where('workspace_id', $workspace->id)
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


}
