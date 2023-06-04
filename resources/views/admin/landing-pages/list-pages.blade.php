@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin_index')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_manage_workspace', [$workspace->uid])}}">Gerenciar Workspace</a></li>
                <li class="breadcrumb-item"><a class="active" href="{{route('admin_list_landingpages', [$workspace->uid])}}">Landing Pages</a></li>
            </ol>
        </nav>
        <h1>Landing Pages @if($workspace?->user?->id <> auth()->user()->id) <span class="badge bg-info">compartilhada</span> @endif</h1>
        <h6>{{$workspace->name}}</h6>
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
            var el = document.getElementById('page-items');
            var sortable = Sortable.create(el, {
                onSort: function( event ) {
                    var order = [];
                    $('#page-items .page-item').each(function () {
                        order.push($(this).data('id'));
                    });

                    if(order.length < 1){
                        return false;
                    }
                    var workspace_id = '{{$workspace->id ?? null}}';
                    var uri = '{{route("admin_order_landingpage", $workspace->uid)}}';
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
                                alert('Sua sessão expirou, faça login novamente');
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

        function deletePage($row){
            $.confirm({
                title: 'Remover Página',
                content: 'Deseja realmente remover a página <b>'+ $row.find('a').html() +
                    '</b>? <br />A ação não poderá ser desfeita <input type="hidden" id="page-remove-id" value="'+ $row.data('id') +'">',
                buttons: {
                    confirm: {
                        text: 'SIM REMOVER',
                        btnClass: 'btn-danger',
                        action: function(){
                            var uri = '{{route('admin_delete_landingpage', [$workspace->uid])}}';
                            var id = this.$content.find('#page-remove-id').val();
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

        <div class="row mb-5 mt-4">
            <div class="col-md-7">
                <div class="btn-group" role="group" aria-label="">
                    <a href="{{route('admin_edit_landingpage', $workspace->uid)}}" class="btn btn-secondary">+ Nova Página</a>
                    <a href="{{route('admin_manage_workspace', $workspace->uid)}}" class="btn btn-light">Gerenciar Workspace</a>
                </div>
            </div>
            <div class="col-md-5 text-end">
                {{-- Pode colocar filtros de pesquisa --}}
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <small class="text-black"><b>OBS: </b>Arraste as páginas para definir qual página será a principal. O sistema irá considerar como página principal a primeira da ordem</small>
            </div>
        </div>
        <div id="page-items" class="row mt-3" >
            @foreach($pages ?? [] as $page)
                <div class="col-md-3 page-item" data-id="{{$page->id}}">
                    <div class="card card-page ps-1 pe-1 pt-1">
                        <img class="card-img-top" style="border: 1px solid #f3f2f2;" src="@if(! empty($page->screenshot->filenameToStore)) {{asset('storage/landing-pages/thumbs/'. $page->screenshot->filenameToStore)}} @else data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22297%22%20height%3D%22180%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20297%20180%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_18728ce155d%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A15pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_18728ce155d%22%3E%3Crect%20width%3D%22297%22%20height%3D%22180%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22110.125%22%20y%3D%2296.71999988555908%22%3E297x180%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E @endif" alt="Card image cap">
                        <div class="card-body">
                            <h5 class="card-title"><a href="{{route('admin_edit_landingpage', [$workspace->uid, $page->id])}}">{{$page->name}}</a> </h5>
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
                                            <li><a class="dropdown-item" href="{{route('admin_edit_landingpage', [$workspace->uid, $page->id])}}">Editar</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="deletePage($(this).parents('.page-item'))">Excluir</a></li>
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
