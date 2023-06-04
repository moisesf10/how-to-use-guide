<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Models\Page;
use App\Models\Workspace;
use App\Models\WorkspaceBlock;
use App\Models\WorkspaceTopic;
use App\Models\WorkspaceUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class GuideController extends Controller
{
    public Workspace $workspace;
    public function __construct(Request $request){
        $uid = $request->route()->parameter('uid');
        $workspace =  Workspace::where('uid', $uid)
            ->first();

        if(! $workspace){
            abort(404);
        }

       // if(! $workspace->indicates_public_access){
           // $this->middleware('auth');
            // $this->middleware('auth:admin');
       // }

        $this->workspace = $workspace;

    }

    public function start(Request $request, $workspaceName, $uid){

        if(! $this->workspace->indicates_access_public  && (! auth()->check() && ! auth()->guard('admin')->check() )  ){
            return redirect()->route('guide_login', [Str::slug($this->workspace->name, '-'), $this->workspace->uid]  );
        }

       // if exists landing page redirect
        $landingPage = LandingPage::where('workspace_id', $this->workspace->id)
            ->whereRaw("status = 'published'")
            ->orderBy('order','asc')
            ->first();

       if($landingPage){
           if (! $this->workspace->indicates_public_access &&  Gate::denies('allows_access_workspace', [$this->workspace])) {
               abort(404);
           }
           return redirect()->route('landing_page_guide', [Str::slug($this->workspace->name, '-'), $this->workspace->uid, $landingPage->uri  ]);
       }


        $page = Page::join('workspace_topics as t', 't.id', '=', 'pages.workspace_topic_id')
            ->join('workspace_blocks as b', 'b.id', '=', 't.workspace_block_id')
            ->join('workspaces as w', 'w.id', '=', 'b.workspace_id')
            ->where('b.workspace_id', $this->workspace->id)
            ->whereRaw("b.indicates_enabled = 1 and t.indicates_enabled = 1 and pages.status = 'published'")
            ->where(function ($q){
                $q->whereRaw("w.indicates_public_access = 1")
                    ->orWhereExists(function ($q2){
                        $q2->select(DB::raw("1"))
                        ->from('workspace_users as u')
                        ->leftJoin('workspace_authorization_users as au', function ($join){
                            $join->on('au.workspace_user_id', '=', 'u.id')
                                ->whereRaw('au.workspace_topic_id = t.id');
                        })
                        ->whereRaw("u.authorization_type = 'full' or au.id is not null");
                        if(auth()->check()){
                            $q2->where('u.id', auth()->user()->id);
                        }

                    });
            })
            ->orderBy('b.order', 'asc')
            ->orderBy('t.order', 'asc')
            ->orderBy('pages.order', 'asc')
            ->select(['pages.id', 'pages.uid', 'pages.workspace_topic_id', 'b.name as block_name', 'b.id as block_id'])
            ->with('topic')
            ->first()
        ;


        if (! $this->workspace->indicates_public_access && Gate::denies('allows_access_topic', [$this->workspace, $page->topic])) {
            abort(404);
        }

       $urlParameters = [
           Str::slug($this->workspace->name, '-'),
           $this->workspace->uid,
           Str::slug($page->block_name, '-'),
           $page->block_id,
           $page->uid,
           $page->id
       ];

       return redirect()->route('guide_page', $urlParameters);

    }

    public function accessLandingPage(Request $request, $workspaceName, $uid, $page){

        if(! $this->workspace->indicates_access_public  && (! auth()->check() && ! auth()->guard('admin')->check() )  ){
            return redirect()->route('guide_login', [Str::slug($this->workspace->name, '-'), $this->workspace->uid]  );
        }

        if(! $this->workspace->indicates_public_access && Gate::denies('allows_access_landingpage', [$this->workspace]) ) {
            abort(404);
        }


        $landingPage = LandingPage::where('workspace_id', $this->workspace->id)
            ->whereRaw("status = 'published'")
            ->where('uri', $page)
            ->first();

        if(! $landingPage){
            abort(404);
        }

        return view('guide.landing-page')
            ->with('landingPage', $landingPage)
            ;

    }

    public function page(Request $request, $workspaceName, $uid, $blockName, $blockId, $pageUid = null, $pageId = null){

        if(! $this->workspace->indicates_access_public  && (! auth()->check() && ! auth()->guard('admin')->check() )  ){
            return redirect()->route('guide_login', [Str::slug($this->workspace->name, '-'), $this->workspace->uid]  );
        }

        $page = null;

        if($this->workspace->indicates_public_access){
            $page = Page::join('workspace_topics as t', 't.id', '=', 'pages.workspace_topic_id')
                ->join('workspace_blocks as b', 'b.id', '=', 't.workspace_block_id')
                ->join('workspaces as w', 'w.id', '=', 'b.workspace_id')
                ->where('b.workspace_id', $this->workspace->id)
                ->where('b.id', $blockId)
                ->where('pages.id', $pageId ?? -1 )
                ->whereRaw("b.indicates_enabled = 1 and t.indicates_enabled = 1 and pages.status = 'published'")
                ->orderBy('b.order', 'asc')
                ->orderBy('t.order', 'asc')
                ->orderBy('pages.order', 'asc')
                ->select(['pages.*', 'b.id as block_id', 'b.name as block_name', 't.id as topic_id'])
                ->first()
            ;

            if(! $page){
                // search default page for blockId
                $page = Page::join('workspace_topics as t', 't.id', '=', 'pages.workspace_topic_id')
                    ->join('workspace_blocks as b', 'b.id', '=', 't.workspace_block_id')
                    ->join('workspaces as w', 'w.id', '=', 'b.workspace_id')
                    ->where('b.workspace_id', $this->workspace->id)
                    ->where('b.id', $blockId)
                    ->whereRaw("b.indicates_enabled = 1 and t.indicates_enabled = 1 and pages.status = 'published'")
                    ->orderBy('b.order', 'asc')
                    ->orderBy('t.order', 'asc')
                    ->orderBy('pages.order', 'asc')
                    ->select(['pages.*', 'b.id as block_id', 'b.name as block_name', 't.id as topic_id'])
                    ->with('topic')
                    ->first()
                ;
            }

        }else{

            $page = Page::join('workspace_topics as t', 't.id', '=', 'pages.workspace_topic_id')
                ->join('workspace_blocks as b', 'b.id', '=', 't.workspace_block_id')
                ->join('workspaces as w', 'w.id', '=', 'b.workspace_id')
                ->where('b.workspace_id', $this->workspace->id)
                ->where('b.id', $blockId)
                ->where('pages.id', $pageId ?? -1 )
                ->whereRaw("b.indicates_enabled = 1 and t.indicates_enabled = 1 and pages.status = 'published'")
                ->where(function ($q){
                    $q->whereRaw("w.indicates_public_access = 1")
                        ->orWhereExists(function ($q2){
                            $q2->select(DB::raw("1"))
                                ->from('workspace_users as u')
                                ->leftJoin('workspace_authorization_users as au', function ($join){
                                    $join->on('au.workspace_user_id', '=', 'u.id')
                                        ->whereRaw('au.workspace_topic_id = t.id');
                                })
                                ->whereRaw("u.authorization_type = 'full' or au.id is not null");
                            if(auth()->check()){
                                $q2->where('u.id', auth()->user()->id);
                            }

                        });
                })
                ->orderBy('b.order', 'asc')
                ->orderBy('t.order', 'asc')
                ->orderBy('pages.order', 'asc')
                ->select(['pages.*', 'b.id as block_id', 'b.name as block_name', 't.id as topic_id'])
                ->first()
            ;


            if(! $page){
                // search default page for blockId
                $page = Page::join('workspace_topics as t', 't.id', '=', 'pages.workspace_topic_id')
                    ->join('workspace_blocks as b', 'b.id', '=', 't.workspace_block_id')
                    ->join('workspaces as w', 'w.id', '=', 'b.workspace_id')
                    ->where('b.workspace_id', $this->workspace->id)
                    ->where('b.id', $blockId)
                    ->whereRaw("b.indicates_enabled = 1 and t.indicates_enabled = 1 and pages.status = 'published'")
                    ->where(function ($q){
                        $q->whereRaw("w.indicates_public_access = 1")
                            ->orWhereExists(function ($q2){
                                $q2->select(DB::raw("1"))
                                    ->from('workspace_users as u')
                                    ->leftJoin('workspace_authorization_users as au', function ($join){
                                        $join->on('au.workspace_user_id', '=', 'u.id')
                                            ->whereRaw('au.workspace_topic_id = t.id');
                                    })
                                    ->whereRaw("u.authorization_type = 'full' or au.id is not null");
                                if(auth()->check()){
                                    $q2->where('u.id', auth()->user()->id);
                                }
                            });
                    })
                    ->orderBy('b.order', 'asc')
                    ->orderBy('t.order', 'asc')
                    ->orderBy('pages.order', 'asc')
                    ->select(['pages.*', 'b.id as block_id', 'b.name as block_name', 't.id as topic_id'])
                    ->with('topic')
                    ->first()
                ;
            }
        }



        if(! $page){
            abort(404);
        }


        if (! $this->workspace->indicates_public_access &&  Gate::denies('allows_access_topic', [$this->workspace, $page->topic])) {
            abort(404);
        }

        $blocks = WorkspaceBlock::where('workspace_id', $this->workspace->id)
            ->whereRaw('indicates_enabled = 1')
            ->orderBy('order', 'asc')
            ->with([
                'topics' => function($q){
                $q->whereRaw('indicates_enabled = 1')
                    ->orderBy('order', 'asc');
                },
                'topics.pages' => function($q){
                $q->whereRaw("status = 'published'")
                    ->orderBy('order', 'asc')
                    ->select(['id', 'workspace_topic_id', 'uid', 'page_name']);
                }
            ])
            ->get();

        $workspaceUser = null;

        return view('guide.load-pages')
            ->with('page', $page)
            ->with('blocks', $blocks)
            ->with('workspace', $this->workspace)
            ;
    }

    public function loadPage($workspaceName, $uid, $pageId){

        $page = null;

        if($this->workspace->indicates_public_access){
            $page = Page::join('workspace_topics as t', 't.id', '=', 'pages.workspace_topic_id')
                ->join('workspace_blocks as b', 'b.id', '=', 't.workspace_block_id')
                ->join('workspaces as w', 'w.id', '=', 'b.workspace_id')
                ->where('b.workspace_id', $this->workspace->id)
                ->where('pages.id', $pageId)
                ->whereRaw("b.indicates_enabled = 1 and t.indicates_enabled = 1 and pages.status = 'published'")
                ->orderBy('b.order', 'asc')
                ->orderBy('t.order', 'asc')
                ->orderBy('pages.order', 'asc')
                ->select(['pages.*'])
                ->with('topic')
                ->first()
            ;
        }else{
            $page = Page::join('workspace_topics as t', 't.id', '=', 'pages.workspace_topic_id')
                ->join('workspace_blocks as b', 'b.id', '=', 't.workspace_block_id')
                ->join('workspaces as w', 'w.id', '=', 'b.workspace_id')
                ->where('b.workspace_id', $this->workspace->id)
                ->where('pages.id', $pageId)
                ->whereRaw("b.indicates_enabled = 1 and t.indicates_enabled = 1 and pages.status = 'published'")
                ->where(function ($q){
                    $q->whereRaw("w.indicates_public_access = 1")
                        ->orWhereExists(function ($q2){
                            $q2->select(DB::raw("1"))
                                ->from('workspace_users as u')
                                ->leftJoin('workspace_authorization_users as au', function ($join){
                                    $join->on('au.workspace_user_id', '=', 'u.id')
                                        ->whereRaw('au.workspace_topic_id = t.id');
                                })
                                ->whereRaw("u.authorization_type = 'full' or au.id is not null");
                            if(auth()->check()){
                                $q2->where('u.id', auth()->user()->id);
                            }
                        });
                })
                ->orderBy('b.order', 'asc')
                ->orderBy('t.order', 'asc')
                ->orderBy('pages.order', 'asc')
                ->select(['pages.*'])
                ->with('topic')
                ->first()
            ;

            if (! $this->workspace->indicates_public_access && Gate::denies('allows_access_topic', [$this->workspace, $page->topic])) {
                abort(404);
            }
        }

        if(! $page){
            abort(404);
        }

        return view('guide.page')
            ->with('workspace', $this->workspace)
            ->with('page', $page)
            ;

    }



}
