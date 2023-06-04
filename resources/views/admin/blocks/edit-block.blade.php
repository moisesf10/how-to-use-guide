@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item">Pages</li>
                <li class="breadcrumb-item active">Edit Block</li>
            </ol>
        </nav>
        <h1>Configuração do Bloco @if($workspace?->user?->id <> auth()->user()->id) <span class="badge bg-info">compartilhada</span> @endif</h1>
        <h6 class="text-black-50">{{$workspace->name}}</h6>
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
                    <p class="mt-3"> Você está configurando um bloco para a workspace <b>{{$workspace->name}}</b> </p>

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
                                        $oldInputs['enabled'] = null;
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
                    <form method="post" class="row g-3" action="{{route('admin_save_block', $workspace->uid)}}">
                        {{csrf_field()}}
                        @if(! empty($block))
                            <input type="hidden" name="id" value="{{$block->id}}">
                        @endif
                        <div class="col-md-8">
                            <label for="name" class="form-label">Nome do Bloco</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{old('name') ?? $block?->name}}" placeholder="Informe o nome do bloco" required>
                            @error('name')
                            <span class="invalid-feedback d-block">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="yes" name="indicates_enabled" id="indicates-enabled" @if((old('indicates_enabled') ?? $block?->indicates_enabled) == 'yes') checked @endif>
                                <label class="form-check-label" for="indicates-enabled">
                                    Ativo
                                </label>
                                @error('indicates_enabled')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">Salvar</button>
                            <a href="{{route('admin_list_blocks', $workspace->uid)}}" class="btn btn-light">Voltar para listagem</a>
                        </div>
                    </form>
                    <!-- End Browser Default Validation -->

                </div>
            </div>

        </div>
    </section>
@stop
