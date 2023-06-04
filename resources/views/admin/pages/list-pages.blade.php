@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle mb-4">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin_index')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_manage_workspace', [$workspace->uid])}}">Workspace</a></li>
                <li class="breadcrumb-item active"><a href="{{route('admin_list_pages', [$workspace->uid])}}">Páginas</a></li>
            </ol>
        </nav>
        <h1>Listagem de Paginas @if($workspace?->user?->id <> auth()->user()->id) <span class="badge bg-info">compartilhada</span> @endif</h1>
        <h6 class="text-black">{{$workspace->name}}</h6>
    </div><!-- End Page Title -->
@stop

@section('css')

@stop

@section('js')
<script>
    function deletePage($row){
        $.confirm({
            title: 'Remover Página',
            content: 'Deseja realmente remover a página <b>'+ $row.find('a').html() +
                '</b>? <br />A ação não poderá ser desfeita <input type="hidden" id="page-remove-id" value="'+ $row.data('page-id') +'">'+
                '<input type="hidden" id="topic-id" value="'+ $row.data('topic-id') +'">',
            buttons: {
                confirm: {
                    text: 'SIM REMOVER',
                    btnClass: 'btn-danger',
                    action: function(){
                        var uri = '{{route('admin_delete_page', [$workspace->uid])}}';
                        var id = this.$content.find('#page-remove-id').val();
                        var topicId = this.$content.find('#topic-id').val();
                        var formData = new FormData();
                        formData.append('id', id );
                        formData.append('topic_id', topicId );

                        $row.LoadingOverlay('show');

                        $.ajax({
                            url: uri,
                            type: "POST",
                            processData: false,
                            contentType: false,
                            data: formData ,
                            dataType: "json",
                            success: function (json) {
                                // redirecionar para a página de successo
                                if(json.success){
                                    confirmSuccess(json.message);
                                    $row.fadeOut(500, function (){
                                        $(this).remove();
                                    })
                                }
                            },
                            error: function (request, status, error) {
                                $row.LoadingOverlay('hide');

                                // se a sessão estiver expirado redireciona para a tela de login
                                if(request.status === 301){
                                    alert('Sua sessão expirou. Faça login novamente');
                                    document.location.href = '/admin/login';
                                }

                                var message = parseErrors(request, status, error);
                                confirmErrors(message);

                            }


                        }).done(function () {
                            $row.LoadingOverlay('hide');
                        });
                    }
                },
                cancelar: function () {

                },

            }
        });
    }
</script>

@stop

@section('content')
    <section class="section">
        @if(session()->get('success'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <h5>Que ótimo! Tudo ocorreu bem</h5>
                        @if(! empty(session()->get('message')))
                            <p>{{session()->get('message')}}</p>
                        @else
                            <p>A requisição foi processada com sucesso.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h5>Ocorreram erros!</h5>
                        <p>Por favor, verificar os erros abaixos</p>
                        @php
                            $oldInputs = session()->getOldInput();
                            $oldInputs['terms'] = null;
                        @endphp
                        @foreach($errors->keys() as $bag)
                            @if(! array_key_exists($bag, $oldInputs) )
                                @error($bag)<p>- {{$message}}</p>@enderror
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="row mb-5">
            <div class="col-md-7">
                <div class="btn-group" role="group" aria-label="">
                    <a href="{{route('admin_edit_page', $workspace->uid)}}" class="btn btn-secondary">+ Nova Página</a>
                    <a href="{{route('admin_manage_workspace', $workspace->uid)}}" class="btn btn-light">Gerenciar Workspace</a>
                </div>
            </div>
            <div class="col-md-5 text-end">
                <form>
                    <div class="row">
                        @php
                            $fBlocks = collect(request()->query('blocks') ?? [] );
                            $fTopics = collect(request()->query('topics') ?? [] );
                        @endphp
                        <div class="col-md-4">
                            <div class="btn-group">
                                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                    Filtrar Bloco
                                </button>
                                <ul class="dropdown-menu">
                                    @foreach($blocks ?? [] as $i => $block)
                                        <li class="dropdown-item">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="blocks[]" id="input-block-{{$i+1}}" value="{{$block->id}}" @if($fBlocks->contains($block->id) ) checked @endif >
                                                <label class="form-check-label" for="input-block-{{$i+1}}">
                                                    {{$block->name}}
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach

                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="btn-group">
                                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                    Filtrar Tópico
                                </button>
                                <ul class="dropdown-menu">
                                    @foreach($topics ?? [] as $i => $topic)
                                        <li class="dropdown-item">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="topics[]" id="input-topic-{{$i+1}}" value="{{$topic->id}}" @if($fTopics->contains($topic->id) ) checked @endif>
                                                <label class="form-check-label" for="input-topic-{{$i+1}}">
                                                    {{$topic->name}}<br><small class="text-black-50">{{$topic->block->name}}</small>
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach

                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4 text-start">
                            <button type="submit" class="btn btn-light bi-text-right">Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

            <div id="page-items" class="row" >
            @foreach($pages ?? [] as $page)
                <div class="col-md-3 page-box" data-page-id="{{$page->id}}" data-topic-id="{{$topic->id}}">
                    <div class="card card-page">
                        <img class="card-img-top" src="@if(! empty($page->screenshot->filenameToStore)) {{asset('storage/pages/thumbs/'. $page->screenshot->filenameToStore)}} @else data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22297%22%20height%3D%22180%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20297%20180%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_18728ce155d%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A15pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_18728ce155d%22%3E%3Crect%20width%3D%22297%22%20height%3D%22180%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22110.125%22%20y%3D%2296.71999988555908%22%3E297x180%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E @endif" alt="Card image cap">
                        <div class="card-body">
                            <h5 class="card-title"><a href="{{route('admin_edit_page', [$workspace->uid, $page->id])}}">{{$page->page_name}}</a> </h5>
                            <p class="card-text">
                            <p class="card-text">
                                <h6> Bloco &blacktriangleright; {{$page->block_name}} | Tópico &blacktriangleright; {{$page->topic_name}}  </h6>
                            </p>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="card-text">
                                        <small class="text-muted">{{toUserTimezone($page->created_at)->format('d/m/Y')}}</small>
                                    </p>
                                </div>
                                <div class="col-md-6 justify-content-end">
                                    <div class="dropdown">
                                        <button class="btn btn-light  dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Ações
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{route('admin_edit_page', [$workspace->uid, $page->id])}}">Editar</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="deletePage($(this).parents('.page-box'))">Excluir</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>

    </section>
@stop
