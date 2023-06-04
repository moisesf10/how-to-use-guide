@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin_index')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_manage_workspace', [$workspace->uid])}}">Workspace</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_choose_block_topics', [$workspace->uid])}}">Escolher Bloco de Tópicos</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_list_topics', [$workspace->uid, $block->id])}}">Tópicos</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
        <h1>Editar Tópico @if($workspace?->user?->id <> auth()->user()->id) <span class="badge bg-info">compartilhada</span> @endif</h1>
        <h6 class="text-black">{{$workspace->name}}</h6>
    </div><!-- End Page Title -->
@stop

@section('css')

@stop

@section('js')

@stop

@section('content')
    <section class="section mt-3">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body pt-4">
                    <h6 class="fw-bold">Bloco &blacktriangleright; {{$block->name}}</h6>
                    <p>Tópicos são títulos de menus que serão apresentados no guia do usuário.</p>

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
                    <form method="post" class="row g-3" action="{{route('admin_save_topic', [$workspace->uid, $block->id])}}">
                        {{csrf_field()}}
                        @if(! empty($topic))
                            <input type="hidden" name="id" value="{{$topic->id}}">
                        @endif
                        <div class="col-md-8">
                            <label for="name" class="form-label">Nome do Tópico</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{old('name') ?? ($topic?->name)}}" placeholder="Informe o nome do tópico" required>
                            @error('name')
                            <span class="invalid-feedback d-block">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <small class="text-success">(OPCIONAL) Informe a TAG &lt;i&gt; de fontawesome 6.3 ou de bootstrap icon </small><br>
                            <label for="icon" class="form-label">Ícone</label>
                            <textarea class="form-control" style="min-height: 8rem;" name="icon" id="icon" maxlength="200" placeholder="Informe a tag do ícone de fontawesome ou bootstrap icon">{{old('icon') ?? $topic?->icon}}</textarea>
                            @error('icon')
                            <span class="invalid-feedback d-block">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <small class="text-success">Subnível indica se o tópico será apresentado como um menu de subnível ou um menu simples no guia do usuário</small>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="yes" name="indicates_sublevel" id="indicates-sublevel" @if(old('indicates_sublevel') == 'yes' || $topic?->indicates_sublevel) checked @endif>
                                <label class="form-check-label" for="indicates-sublevel">
                                    Indica Subnível
                                </label>
                                @error('indicates_sublevel')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="yes" name="indicates_enabled" id="indicates-enabled" @if(old('indicates_enabled') == 'yes' || $topic?->indicates_enabled) checked @endif>
                                <label class="form-check-label" for="indicates-enabled">
                                    Ativo
                                </label>
                                @error('indicates_enabled')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="btn-group">
                                <button class="btn btn-primary" type="submit">Salvar</button>
                                @if($topic)
                                <a href="{{route('admin_edit_topic', [$workspace?->uid, $block->id])}}" class="btn btn-light">Novo</a>
                                @endif
                                <a href="{{route('admin_manage_workspace', $workspace?->uid)}}" class="btn btn-light">Gerenciar Workspace</a>
                            </div>
                        </div>
                    </form>


                </div>
            </div>

        </div>
    </section>
@stop
