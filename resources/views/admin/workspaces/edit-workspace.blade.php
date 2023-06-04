@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin_index')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_manage_workspace', $workspace->uid)}}">Workspace</a></li>
                <li class="breadcrumb-item active">Editar Workspace</li>
            </ol>
        </nav>
        <h1>Editar Workspace @if($workspace?->user?->id <> auth()->user()->id) <span class="badge bg-info">compartilhada</span> @endif</h1>
        <h6 class="text-black">{{$workspace->name}}</h6>
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
                    <h5 class="card-title">Workspace</h5>
                    <p>Workspaces são áreas que contém tudo o que é relacionado a um determinado guia, como por exemplo landing pages, menus, páginas, etc.</p>

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
                    <form method="post" class="row g-3" action="{{route('admin_save_edit_workspace', $workspace->uid)}}">
                        {{csrf_field()}}
                        <input type="hidden" name="id" value="{{$workspace->id}}">
                        <div class="col-md-8">
                            <label for="name" class="form-label">Nome da Workspace</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{old('name') ?? $workspace->name}}" placeholder="Informe o nome da workspace" required>
                            @error('name')
                            <span class="invalid-feedback d-block">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control" style="min-height: 8rem;" name="description" id="description" placeholder="Informe uma breve descrição para a workspace" required>{{old('description') ?? $workspace->description}}</textarea>
                            @error('description')
                            <span class="invalid-feedback d-block">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="yes" name="indicates_public_access" id="indicates-public-access" @if(old('indicates_public_access') == 'yes' || $workspace->indicates_public_access) checked @endif>
                                <label class="form-check-label" for="indicates-public-access">
                                    Permite acesso público
                                </label>
                                @error('indicates_public_access')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="btn-group">
                                <button class="btn btn-primary" type="submit">Salvar</button>
                                <a href="{{route('admin_manage_workspace', $workspace->uid)}}" class="btn btn-light">Gerenciar Workspace</a>
                            </div>
                        </div>
                    </form>
                    <!-- End Browser Default Validation -->

                </div>
            </div>

        </div>
    </section>
@stop
