<ul class="sidebar-nav" id="sidebar-nav">

    @foreach($blocks ?? [] as $block)
        @if($block->id == $page->block_id)
            @foreach($block->topics ?? [] as $topic)
                @if($workspace->indicates_public_access || \Illuminate\Support\Facades\Gate::allows('allows_access_topic', [$workspace, $topic]))
                    @if(! $topic->indicates_sublevel)
                        @php
                            $singlePage = $topic->pages->where('workspace_topic_id', $topic->id)->get(0);
                            $urlParameters = null;
                            if($singlePage){
                                $urlParameters = [
                                   \Illuminate\Support\Str::slug($workspace->name, '-'),
                                   $workspace->uid,
                                   \Illuminate\Support\Str::slug($block->name, '-'),
                                   $block->id,
                                   $singlePage?->uid,
                                   $singlePage?->id
                               ];
                            }
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link @if($topic->id != $page->topic_id) collapsed @endif" @if(! $singlePage) href="#" @else href="{{route('guide_page', $urlParameters)}}" @endif>
                                @if(! empty($topic->icon))
                                    {!! $topic->icon !!}
                                @endif
                                <span>{{$singlePage?->page_name ?? $topic->name}}</span>
                            </a>
                        </li><!-- End Dashboard Nav -->
                    @else

                        <li class="nav-item">
                            <a class="nav-link @if($topic->id != $page->workspace_topic_id) collapsed @endif " data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                                @if(! empty($topic->icon))
                                    {!! $topic->icon !!}
                                @endif
                                <span>{{$topic->name}}</span><i class="bi bi-chevron-down ms-auto"></i>
                            </a>
                            <ul id="components-nav" class="nav-content collapse @if($topic->id == $page->topic_id) show @endif" data-bs-parent="#sidebar-nav">
                                @foreach($topic->pages ?? [] as $pageMenu)
                                    <li>
                                        @php
                                            $urlParameters = [
                                               \Illuminate\Support\Str::slug($workspace->name, '-'),
                                               $workspace->uid,
                                               \Illuminate\Support\Str::slug($block->name, '-'),
                                               $block->id,
                                               $pageMenu->uid,
                                               $pageMenu->id
                                           ];
                                        @endphp
                                        <a href="{{route('guide_page', $urlParameters)}}" class="@if($page->id == $pageMenu->id) active @endif" >
                                            <i class="bi bi-circle"></i><span>{{$pageMenu->page_name}}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li><!-- End Components Nav -->

                    @endif
                @endif
            @endforeach
        @endif
    @endforeach






</ul>
