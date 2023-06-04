@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin_index')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_manage_workspace', $workspace->uid)}}">Workspace</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_choose_block_topics', [$workspace->uid])}}">Tópicos</a></li>
                <li class="breadcrumb-item active">Listar Tópicos</li>
            </ol>
        </nav>
        <h1>Listagem de Tópicos @if($workspace?->user?->id <> auth()->user()->id) <span class="badge bg-info">compartilhada</span> @endif</h1>
        <h6 class="text-black">{{$workspace->name}}</h6>
    </div><!-- End Page Title -->
@stop

@section('css')

@stop

@section('js')
    @vite([
        'resources/js/admin/sortable.js'
    ])
    <script>

        $(function (){
            var el = document.getElementById('topic-items');
            var sortable = Sortable.create(el, {
                onSort: function( event ) {
                    var order = [];
                    $('#topics tbody tr').each(function () {
                        order.push($(this).data('topic-id'));
                    });

                    if(order.length < 1){
                        return false;
                    }

                    var workspace_id = $('[name="workspace-id"]').attr('content');
                    var uri = '{{route("admin_order_topic", [$workspace->uid, $block->id])}}';
                    var formData = new FormData();
                    for(var i in order){
                        formData.append('order[]', order[i]);
                    }
                    $.ajax({
                        url: uri,
                        type: "POST",
                        processData: false,
                        contentType: false,
                        data: formData,
                        dataType: "json",
                        success: function (json) {
                            var i = 0;
                            $('#topics tbody tr').each(function (){
                                $(this).find('th').eq(0).html(++i);
                            });
                        },
                        error: function (request, status, error) {
                            //$button.LoadingOverlay('hide');
                            // se a sessão estiver expirado redireciona para a tela de login
                            if (request.status === 301) {
                                document.location.href = '{{route("admin_login")}}';
                            }
                            var message = parseErrors(request);
                            confirmErrors(message);
                        }
                    }).done(function () {
                        //$button.LoadingOverlay('hide');
                    });
                }
            });
        })

        function deleteTopic($row){
            $.confirm({
                title: 'Remover Tópico',
                content: 'Deseja realmente remover o tópico <b>'+ $row.find('a').html() +
                    '</b>? <br />A ação não poderá ser desfeita <input type="hidden" id="topic-remove-id" value="'+ $row.data('topic-id') +'">' ,
                buttons: {
                    confirm: {
                        text: 'SIM REMOVER',
                        btnClass: 'btn-danger',
                        action: function(){
                            var uri = '{{route('admin_delete_topic', [$workspace->uid, $block->id])}}';
                            var id = this.$content.find('#topic-remove-id').val();
                            var formData = new FormData();
                            formData.append('id', id );

                            $row = $('.topic-row[data-topic-id="'+ id +'"]');
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
            <div class="row" data-block-id="{{$block->id}}">
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

        <div class="row mb-5 mt-4">
            <div class="col-12">
                <div class="btn-group" role="group" aria-label="">
                    <a href="{{route('admin_edit_topic', [$workspace->uid, $block->id])}}" class="btn btn-secondary">+ Novo Tópico</a>
                    <a href="{{route('admin_manage_workspace', $workspace->uid)}}" class="btn btn-light">Gerenciar Workspace</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body pt-4">
                <h6 class="fw-bold">Bloco &blacktriangleright; {{$block->name}}</h6>
                <p class="mt-4">Você pode arrastar os tópicos para indicar a ordem em que aparecerão no menu</p>
                <table id="topics" class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Subnível?</th>
                            <th scope="col">Status</th>
                            <th scope="col">Atualizado em</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody id="topic-items">
                        @foreach($block->topics ?? [] as $i => $topic)
                            <tr class="topic-row" data-topic-id="{{$topic->id}}">
                                <th scope="row">{{$i + 1}}</th>
                                <td><a href="{{route('admin_edit_topic', [$workspace->uid, $block->id, $topic->id])}}">{{$topic->name}}</a></td>
                                <td>@if($topic->indicates_sublevel) Sim @else Não @endif</td>
                                <td>
                                    @if($topic->indicates_enabled)
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-black">Desativado</span>
                                    @endif
                                </td>
                                <td>{{toUserTimezone($topic->updated_at)->format(auth()->user()->date_format ?? 'd/m/Y H:i')}}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary  dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Ações
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{route('admin_edit_topic', [$workspace->uid, $block->id, $topic->id])}}">Editar</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="deleteTopic($(this).parents('.topic-row'))">Excluir</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>

    </section>
@stop
