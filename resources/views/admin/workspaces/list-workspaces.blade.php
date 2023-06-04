@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin_index')}}">Home</a></li>
                <li class="breadcrumb-item">Workspaces</li>
            </ol>
        </nav>
        <h1>Workspaces</h1>
    </div><!-- End Page Title -->
@stop

@section('css')

@stop

@section('js')
<script>
    function remover($workspaceBox){
        $.confirm({
            title: 'Remover Workspace',
            content: 'Deseja realmente remover a workspace <b>'+ $workspaceBox.find('.workspace-name').text() +
                '</b>? <br />A ação não poderá ser desfeita <input type="hidden" id="workspace-remove-id" value="'+ $workspaceBox.data('id') +'">' ,
            buttons: {
                confirm: {
                    text: 'SIM REMOVER',
                    btnClass: 'btn-danger',
                    action: function(){
                        var uri = '{{route('admin_delete_workspace')}}';
                        var id = this.$content.find('#workspace-remove-id').val();
                        var formData = new FormData();
                        formData.append('id', id );

                        $box = $('.workspace-box[data-id="'+ id +'"]');
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
                                    confirmSuccess(json.message);
                                    $box.fadeOut(500, function (){
                                        $(this).remove();
                                    })
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
        <div class="row">
            <div class="col-12">
                <a href="{{route('admin_new_workspace')}}" class="btn btn-secondary">+ Nova Workspace</a>
            </div>
        </div>
        <div class="row mt-4">
            @foreach($workspaces as $workspace)
                <div class="col-lg-6 workspace-box" data-id="{{$workspace->id}}">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title pb-2">
                                @if($workspace->indicates_enabled)
                                    <span class="badge rounded-pill bg-success text-white">Ativo</span>
                                @else
                                    <span class="badge rounded-pill bg-secondary text-white">Desativado</span>
                                @endif
                                <a class="text-black workspace-name" href="{{route('admin_manage_workspace', $workspace->uid)}}">{{$workspace->name}}</a></h5>

                            <p>{{$workspace->description}}</p>
                        </div>
                        <div class="card-footer">
                            <div class="dropdown">
                                <button class="btn btn-light  dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ações
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{route('admin_manage_workspace', $workspace->uid)}}">Gerenciar</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="remover($(this).parents('.workspace-box'))">Excluir</a></li>
{{--                                    <li><a class="dropdown-item" href="#">Something else here</a></li>--}}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach


        </div>
    </section>
@stop
