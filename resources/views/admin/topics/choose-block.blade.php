@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin_index')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_manage_workspace', $workspace->uid)}}">Workspace</a></li>
                <li class="breadcrumb-item active">Escolher Bloco de Tópico</li>
            </ol>
        </nav>
        <h1>Blocos de Tópicos @if($workspace?->user?->id <> auth()->user()->id) <span class="badge bg-info">compartilhada</span> @endif</h1>
        <h6 class="text-black">{{$workspace->name}}</h6>

    </div><!-- End Page Title -->
@stop

@section('css')

@stop

@section('js')

@stop

@section('content')
    <section class="section">
        <div class="row">
            <div class="col-12">
                <a href="{{route('admin_manage_workspace', $workspace->uid)}}" class="btn btn-secondary">Gerenciar Workspace</a>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-12">
                <p>Escolha de qual bloco você deseja gerenciar os tópicos</p>
            </div>
        </div>
        <div class="row mt-4">
            @forelse($workspace->blocks ?? [] as $block)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><a class="text-black" href="{{route('admin_list_topics', [$workspace->uid, $block->id])}}">{{$block->name}}</a></h5>
                            <p> O bloco se encontra com o status
                                @if($block->indicates_enabled)
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-black">Desativado</span>
                                @endif
                            </p>
                        </div>
                        <div class="card-footer">
                            <a class="btn btn-light" href="{{route('admin_list_topics', [$workspace->uid, $block->id])}}">Tópicos</a>
                            <small class="text-success">{{$block->topics?->count() ?? 0}} tópicos cadastrados</small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <h4>Esta workspace não possui nenhum bloco cadastrado</h4>
                </div>

            @endforelse


        </div>
    </section>
@stop
