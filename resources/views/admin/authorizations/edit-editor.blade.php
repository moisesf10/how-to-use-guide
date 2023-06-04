@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin_index')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_manage_workspace', $workspace->uid)}}">Workspace</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_change_type_authorization', [$workspace->uid])}}">Autorizações</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_list_editors_authorization', $workspace->uid)}}">Editores</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
        <h1>Editar Editor @if($workspace?->user?->id <> auth()->user()->id) <span class="badge bg-info">compartilhada</span> @endif</h1>
        <h6>{{$workspace->name}}</h6>
    </div><!-- End Page Title -->
@stop

@section('css')

@stop

@section('js')

@stop

@section('content')
    <section class="section">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{$workspace->name}}</h5>
                    @if(empty($editor))
                        <p>Um e-mail será enviado ao usuário para que aceite o pedido de se tornar editor desta workspace</p>
                    @endif
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
                    <form method="post" class="row g-3" autocomplete="off" action="{{route('admin_save_editor_authorization', $workspace->uid)}}">
                        {{csrf_field()}}
                        @if(! empty($editor))
                        <input type="hidden" name="id" value="{{$editor->id}}">
                        @endif
                        <div class="col-12">
                            <label for="name" class="form-label">Nome do Editor</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{old('name') ?? ($editor?->user?->name ?? $editor->name ?? null ) }}" placeholder="Informe o nome do editor" required>
                            @error('name')
                            <span class="invalid-feedback d-block">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label">E-mail</label>
                            <input type="text" class="form-control" name="email" id="email" value="{{old('email') ?? ($editor->email ?? null ) }}" placeholder="Ex: nome@email.com.br" @if(($editor?->status ?? null) == 'pending') disabled @endif required>
                            @error('email')
                            <span class="invalid-feedback d-block">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label">Status</label>
                            <select @if(empty($editor)) name="status" @endif id="status" class="form-control" @if(empty($editor?->status) || $editor->status == 'pending') disabled @endif>
                                @if(empty($editor?->status))
                                <option value="" selected>Pendente</option>
                                @else
                                    @if($editor->status == 'pending')
                                        <option value="pending" selected>Pendente</option>
                                    @endif
                                @endif
                                <option value="enabled" @if(($editor?->status ?? null) == 'enabled') selected @endif>Habilitado</option>
                                <option value="disabled" @if(($editor?->status ?? null) == 'disabled') selected @endif>Desabilitado</option>
                            </select>
                            @error('status')
                            <span class="invalid-feedback d-block">{{$message}}</span>
                            @enderror
                        </div>
                        @if($editor)
                        <div class="col-12">
                            <input class="form-check-input" type="checkbox" value="yes" name="indicates_resend_mail" id="indicates-resend-mail" @if(old('indicates_resend_mail') == 'yes') checked @endif>
                            <label class="form-check-label" for="indicates-enabled">
                                Reenviar E-mail
                            </label>
                            @error('indicates_resend_mail')
                            <span class="invalid-feedback d-block">{{$message}}</span>
                            @enderror
                        </div>
                        @endif

                        <div class="col-12">
                            <div class="btn-group">
                                <button class="btn btn-primary" type="submit">Salvar</button>
                                <a href="{{route('admin_edit_editor_authorization', $workspace->uid)}}" class="btn btn-light">+Novo Editor</a>
                                <a href="{{route('admin_list_editors_authorization', $workspace->uid)}}" class="btn btn-light">Listar Editores</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@stop
