@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin_index')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_manage_workspace', $workspace->uid)}}">Workspace</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_change_type_authorization', [$workspace->uid])}}">Autorizações</a></li>
                <li class="breadcrumb-item active">Listar Tópicos</li>
            </ol>
        </nav>
        <h1>Listagem de Editores @if($workspace?->user?->id <> auth()->user()->id) <span class="badge bg-info">compartilhada</span> @endif</h1>
        <h6>{{$workspace->name}}</h6>
    </div><!-- End Page Title -->
@stop

@section('css')

@stop

@section('js')
<script>
    function deleteEditor($row){
        $.confirm({
            title: 'Remover Página',
            content: 'Deseja realmente remover o editor <b>'+ $row.find('a').html() +
                '</b>? <br />A ação não poderá ser desfeita <input type="hidden" id="editor-remove-id" value="'+ $row.data('editor-id') +'">',
            buttons: {
                confirm: {
                    text: 'SIM REMOVER',
                    btnClass: 'btn-danger',
                    action: function(){
                        var uri = '{{route('admin_delete_editor_authorization', [$workspace->uid])}}';
                        var id = this.$content.find('#editor-remove-id').val();
                        var formData = new FormData();
                        formData.append('id', id );

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
                    <a href="{{route('admin_edit_editor_authorization', [$workspace->uid])}}" class="btn btn-secondary">+ Adicionar Usuário</a>
                    <a href="{{route('admin_manage_workspace', $workspace->uid)}}" class="btn btn-light">Gerenciar Workspace</a>
                </div>
            </div>
        </div>

        <div id="blocks">
            @foreach($workspace->editors ?? [] as $editor)
                <div class="row editor-box" data-editor-id="{{$editor->id}}">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><a class="text-dark" href="{{route('admin_edit_editor_authorization', [$workspace->uid, $editor->id])}}">{{$editor->name}}</a></h5>
                                @php
                                    $badgeClass = 'badge bg-secondary';
                                    switch ($editor->status){
                                        case 'pending':
                                            $badgeClass = 'badge bg-secondary';
                                            break;
                                        case 'accepted':
                                            $badgeClass = 'badge bg-success';
                                            break;
                                        case 'rejected':
                                            $badgeClass = 'badge bg-danger';
                                            break;
                                        case 'disabled':
                                            $badgeClass = 'badge bg-dark';
                                            break;

                                    }
                                @endphp
                                <p><span class="{{$badgeClass}}">{{__($editor->status)}}</span>&nbsp;{{$editor->email}}</p>
                                <div class="dropdown">
                                    <button class="btn btn-light  dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Ações
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{route('admin_edit_editor_authorization', [$workspace->uid, $editor->id])}}">Editar</a></li>
                                        @if(in_array($editor->status, ['accepted', 'disabled']))
                                            @if($editor->status == 'accepted')
                                                <li><a class="dropdown-item" href="#">Desativar</a></li>
                                            @else
                                                <li><a class="dropdown-item" href="#">Ativar</a></li>
                                            @endif
                                        @endif
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="deleteEditor($(this).parents('.editor-box'))">Excluir</a></li>
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
