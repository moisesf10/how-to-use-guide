@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <h1>Criar Workspace</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin_index')}}">Home</a></li>
                <li class="breadcrumb-item active">Criar Workspace</li>
            </ol>
        </nav>
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
                    <form method="post" class="row g-3" action="{{route('admin_save_new_workspace')}}">
                        {{csrf_field()}}
                        <div class="col-md-8">
                            <label for="name" class="form-label">Nome da Workspace</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{old('name')}}" placeholder="Informe o nome da workspace" required>
                            @error('name')
                            <span class="invalid-feedback d-block">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Informe uma breve descrição para a workspace" required>{{old('description')}}</textarea>
                            @error('description')
                            <span class="invalid-feedback d-block">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="yes" name="indicates_public_access" id="indicates-public-access" @if(old('indicates_public_access') == 'yes') checked @endif>
                                <label class="form-check-label" for="indicates-public-access">
                                    Permite acesso público
                                </label>
                                @error('indicates_public_access')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">Criar Workspace</button>
                        </div>
                    </form>
                    <!-- End Browser Default Validation -->

                </div>
            </div>

        </div>
    </section>
@stop
