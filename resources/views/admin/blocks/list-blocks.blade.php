@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin_index')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_manage_workspace', $workspace->uid)}}">Workspace</a></li>
                <li class="breadcrumb-item active">Listar Blocos</li>
            </ol>
        </nav>
        <h1>Listagem de Blocos @if($workspace?->user?->id <> auth()->user()->id) <span class="badge bg-info">compartilhada</span> @endif</h1>
        <h6 class="text-black">{{$workspace->name}}</h6>
    </div><!-- End Page Title -->
@stop

@section('css')

@stop

@section('js')
{{--    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>--}}
    @vite([
        'resources/js/admin/sortable.js'
    ])
    <script>

        $(function (){
            var el = document.getElementById('blocks');
            var sortable = Sortable.create(el, {
                onSort: function( event ) {
                    var order = [];
                    $('#blocks .row').each(function () {
                        order.push($(this).data('block-id'));
                    });

                    if(order.length < 1){
                        return false;
                    }
                    var workspace_id = $('[name="workspace-id"]').attr('content');
                    var uri = '{{route("admin_reorder_block", $workspace->uid)}}';
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
                        },
                        error: function (request, status, error) {
                            //$button.LoadingOverlay('hide');
                            // se a sessão estiver expirado redireciona para a tela de login
                            if (request.status === 301) {
                                document.location.href = '{{route("admin_login")}}';
                            }
                            var message = processAjaxError(request);
                            confirmErrors(message);
                        }
                    }).done(function () {
                        //$button.LoadingOverlay('hide');
                    });
                }
            });
        });

        function deleteBlock($card){
            $.confirm({
                title: 'Remover Bloco',
                content: 'Deseja realmente remover o bloco <b>'+ $card.find('.card-title a').text() +
                    '</b>? <br />A ação não poderá ser desfeita <input type="hidden" id="block-remove-id" value="'+ $card.data('id') +'">' ,
                buttons: {
                    confirm: {
                        text: 'SIM REMOVER',
                        btnClass: 'btn-danger',
                        action: function(){
                            var uri = '{{route('admin_delete_block', $workspace->uid)}}';
                            var id = this.$content.find('#block-remove-id').val();
                            var formData = new FormData();
                            formData.append('id', id );

                            $box = $('.block-box[data-id="'+ id +'"]');
                            $row = $box.parents('.row');

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

        function changeStatus($box, status){

            var uri = '{{route('admin_change_block', $workspace->uid)}}';
            var id = $box.data('id');
            var formData = new FormData();
            formData.append('id', id );
            formData.append('status', status );

            $box = $('.block-box[data-id="'+ id +'"]');
            $box.LoadingOverlay('show');

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
                        confirmSuccess(json.message, 'Alteração de Status', true);
                    }
                },
                error: function (request, status, error) {
                    $box.LoadingOverlay('hide');

                    // se a sessão estiver expirado redireciona para a tela de login
                    if(request.status === 301){
                        alert('Sua sessão expirou. Faça login novamente');
                        document.location.href = '/admin/login';
                    }

                    var message = parseErrors(request, status, error);
                    confirmErrors(message);

                }


            }).done(function () {
                $box.LoadingOverlay('hide');
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
            <div class="row" >
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
                    <a href="{{route('admin_edit_block', $workspace->uid)}}" class="btn btn-secondary">+ Novo Bloco</a>
                    <a href="{{route('admin_manage_workspace', $workspace->uid)}}" class="btn btn-light">Gerenciar Workspace</a>
                </div>
            </div>
        </div>

        <div id="blocks">
            @foreach($workspace->blocks as $block)
                <div class="row" data-block-id="{{$block->id}}">
                    <div class="col-md-12">
                        <div class="card block-box" data-id="{{$block->id}}">
                            <div class="card-body">
                                <h5 class="card-title"><a class="text-dark" href="{{route('admin_edit_block', [$workspace->uid, $block->id])}}">{{$block->name}}</a></h5>
                                <p> O bloco se encontra com o status
                                    @if($block->indicates_enabled)
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-black">Desativado</span>
                                    @endif
                                    e a última alteração em {{toUserTimezone($block->updated_at)->format( auth()->user()->date_format ?? 'd/m/Y H:i') }}
                                </p>
                                <div class="dropdown">
                                    <button class="btn btn-light  dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Ações
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{route('admin_edit_block', [$workspace->uid, $block->id])}}">Editar</a></li>
                                        @if($block->indicates_enabled)
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeStatus($(this).parents('.block-box'), 0)">Desativar</a></li>
                                        @else
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeStatus($(this).parents('.block-box'), 1)">Ativar</a></li>
                                        @endif
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="deleteBlock($(this).parents('.block-box'))">Excluir</a></li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </section>
@stop
