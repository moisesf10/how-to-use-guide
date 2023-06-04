@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin_index')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_manage_workspace', $workspace->uid)}}">Workspace</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_change_type_authorization', [$workspace->uid])}}">Autorizações</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_list_authorized_users_authorization', $workspace->uid)}}">Usuários Autorizados</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
        <h1>Editar Usuário Autorizado @if($workspace?->user?->id <> auth()->user()->id) <span class="badge bg-info">compartilhada</span> @endif</h1>
        <h6 class="text-black">{{$workspace->name}}</h6>
    </div><!-- End Page Title -->
@stop

@section('css')

@stop

@section('js')
<script>
    $(function (){
        $('#btn-radio-full').change(function (){
            $('.topic-item, #check-all').prop('disabled', true);
            $('table tbody tr td').addClass('text-black-50');
            $(this).parents().find('label').addClass('text-black-50');
        });
        $('#btn-radio-partial').change(function (){
            $('.topic-item, #check-all').prop('disabled', false);
            $('table tbody tr td').removeClass('text-black-50');
            $(this).parents().find('label').removeClass('text-black-50');
        });
        $('#check-all').change(function (){
            if($(this).is(':checked')){
                $('.topic-item').prop('checked', true);
            }else{
                $('.topic-item').prop('checked', false);
            }
        })
    });
</script>
@stop

@section('content')
    <section class="section">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Workspace {{$workspace->name}}</h5>

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

                    <!-- Browser Default Validation -->
                    <form method="post" class="row g-3" autocomplete="off" action="{{route('admin_save_authorized_user_authorization', $workspace->uid)}}">
                        {{csrf_field()}}
                        @if(! empty($user))
                            <input type="hidden" name="id" value="{{$user->id}}">
                        @else
                            <input type="hidden" name="indicates_resend_mail" value="yes">
                        @endif
                        <div class="row">
                            <div class="col-12">
                                <p><small>Informe os dados de acesso para o usuário que irá visualizar o seu guia de utilização</small></p>
                                <p><small class="text-success">OBS: E-mails do Google poderão se autenticar usando a autenticação com o Google</small></p>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nome do Usuário</label>
                                <input type="text" class="form-control" name="name" id="name" value="{{old('name') ?? ($user?->name ?? $user->name ?? null ) }}" placeholder="Informe o nome do usuário" required>
                                @error('name')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="text" class="form-control" name="email" id="email" value="{{old('email') ?? ($user->email ?? null ) }}" placeholder="Ex: nome@email.com.br" required>
                                @error('email')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-3">
                            @if( $user)
                                <div class="col-12">
                                    <div class="cbx-group">
                                        <div class="">
                                            <input class="form-check-input" type="checkbox" value="yes" name="indicates_enabled" id="indicates-enabled" @if(old('indicates_enabled') == 'yes' || $user?->indicates_enabled ?? false) checked @endif>
                                            <label class="form-check-label" for="indicates-enabled">
                                                Usuário Ativo?
                                            </label>
                                            @error('indicates_enabled')
                                            <span class="invalid-feedback d-block">{{$message}}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="cbx-group">
                                        <div class="">
                                            <input class="form-check-input" type="checkbox" value="yes" name="indicates_resend_mail" id="indicates-resend-mail" @if(old('indicates_resend_mail') == 'yes') checked @endif>
                                            <label class="form-check-label" for="indicates-resend-mail">
                                                Quero reenviar o e-mail de convite para o usuário
                                            </label>
                                            @error('indicates_resend_mail')
                                            <span class="invalid-feedback d-block">{{$message}}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            @endif
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 ps-4 pe-4">
                                <hr />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h5 class="card-title">Autorizações de Acesso</h5>
                            </div>
                            <div class="col-12">
                                <small>Configure abaixo os tópicos que o usuário terá autorização para visualizar</small>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="btn-group" role="group" aria-label="Método de Autorização">
                                    <input type="radio" class="btn-check" name="authorization_type" id="btn-radio-full" autocomplete="off" value="full" @if( old('authorization_type') == 'full' || ($user->authorization_type ?? null) == 'full') checked @endif>
                                    <label class="btn btn-outline-primary" for="btn-radio-full">Autorização Total</label>

                                    <input type="radio" class="btn-check" name="authorization_type" id="btn-radio-partial" value="partial" autocomplete="off" @if( old('authorization_type') != 'full' && ($user->authorization_type ?? null) != 'full') checked @endif>
                                    <label class="btn btn-outline-primary" for="btn-radio-partial">Autorização Parcial</label>
                                </div>
                                @error('authorization_type')
                                <p><span class="invalid-feedback d-block">{{$message}}</span></p>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <input type="checkbox" id="check-all" @if($user?->authorization_type ?? null == 'full') disabled @endif>&nbsp;<label @if($user?->authorization_type ?? null == 'full') class="text-black-50" @endif>marcar/desmarcar todos</label>
                            </div>
                        </div>

                        @foreach($blocks as $block)
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th colspan="2" class="">{{$block->name}}</th>
                                        </tr>
                                        <tr>
                                            <th scope="col" style="width: 2rem;">#</th>
                                            <th scope="col">Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($block->topics ?? [] as $topic )
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="topic-item" name="authorizations[]" value="{{$topic->id}}" @if(in_array($topic->id, $authorizationsUser?->pluck('workspace_topic_id')?->toArray() ?? [])) checked @endif  @if( old('authorization_type') == 'full' || ($user->authorization_type ?? null) == 'full') disabled @endif >
                                                    </td>
                                                    <td class="@if($user?->authorization_type ?? null == 'full') text-black-50 @endif">{{$topic->name}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach


                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="btn-group">
                                    <button class="btn btn-primary" type="submit">Salvar</button>
                                    <a href="{{route('admin_edit_authorized_user_authorization', $workspace->uid)}}" class="btn btn-light">+Novo Usuário</a>
                                    <a href="{{route('admin_list_authorized_users_authorization', $workspace->uid)}}" class="btn btn-light">Listar Usuários</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@stop
