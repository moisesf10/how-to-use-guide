@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item">Workspace</li>
                <li class="breadcrumb-item active">Authorizations</li>
            </ol>
        </nav>
        <h1>Editar Workspace @if($workspace?->user?->id <> auth()->user()->id) <span class="badge bg-info">compartilhada</span> @endif</h1>
        <h6 class="text-black-50">{{$workspace->name}}</h6>
    </div><!-- End Page Title -->
@stop

@section('css')

@stop

@section('js')
<script>
    function accept(){
        $('#action').val('');
        $.confirm({
            title: 'Aceitar Convite',
            content: 'Deseja realmente <b>aceitar</b> o convite para se tornar editor da workspace?',
            buttons: {
                confirm: {
                    text: 'Sim',
                    btnClass: 'btn btn-primary',
                    keys: ['enter'],
                    action: function(){
                        $('#action').val('accepted');
                        document.form1.submit();

                    }
                },
                reject: {
                    text: 'Não',
                    btnClass: 'btn btn-light',
                    keys: ['esc']
                }
            }
        });
    }

    function reject(){
        $('#action').val('');
        $.confirm({
            title: 'Rejeitar Convite',
            content: 'Deseja realmente <b>rejeitar</b> o convite para se tornar editor da workspace?',
            buttons: {
                confirm: {
                    text: 'Sim',
                    btnClass: 'btn btn-danger',
                    keys: ['enter'],
                    action: function(){
                        $('#action').val('rejected');
                        document.form1.submit();
                    }
                },
                reject: {
                    text: 'Não',
                    btnClass: 'btn btn-light',
                    keys: ['esc']
                }
            }
        });
    }
</script>
@stop

@section('content')
    <section class="section">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Autorização de Editor</h5>
                    <p>Você foi convidado para se tornar um editor da workspace <b>{{$workspace->name}}</b> do usuário <i>{{toShortName($workspace->user->name)}}</i> </p>

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
                    <form name="form1" method="post" class="row g-3" action="{{route('admin_save_invitation_mail_editor_authorization', $workspace->uid)}}">
                        {{csrf_field()}}
                        <input type="hidden" name="id" value="{{$workspace->id}}">
                        <input type="hidden" name="authorization" value="{{$token->token}}">
                        <input type="hidden" id="action" name="action" value="">
                        <div class="col-md-8">
                            <label for="name" class="form-label">Nome da Workspace</label>
                            <input type="text" class="form-control" name="name" id="name" disabled value="{{old('name') ?? $workspace->name}}" placeholder="Informe o nome da workspace" required>
                            @error('name')
                            <span class="invalid-feedback d-block">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="col-12 mt-4">
                            <div class="btn-group">
                                <button class="btn btn-primary" type="button" onclick="accept()">Aceitar</button>
                                <button class="btn btn-light" type="button" onclick="reject()">Rejeitar</button>
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
