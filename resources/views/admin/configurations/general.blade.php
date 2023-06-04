@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Administração</li>
                <li class="breadcrumb-item active"><a href="{{route('admin_list_general_setting')}}">Configurações Gerais</a></li>
            </ol>
        </nav>
        <h1>Configurações Gerais</h1>
    </div><!-- End Page Title -->
@stop

@section('css')

@stop

@section('js')

@stop

@section('content')
    <section class="section">
        <div class="row">
            <div class="col-12">
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
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Configurações Gerais</h5>

                        <!-- Browser Default Validation -->
                        <form method="post" class="row g-3" enctype="multipart/form-data" action="{{route('admin_save_general_setting')}}">
                            {{csrf_field()}}
                            @php
                                $general = $configurations->where('uid', 'general')->first();

                            @endphp
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nome do Sistema</label>
                                <input type="text" class="form-control" name="system_name" id="system-name" value="{{old('system_name') ?? ($general?->content->system_name ?? null) }}" placeholder="Informe o nome do sistema">
                                @error('system_name')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Copyright</label>
                                <input type="text" class="form-control" name="copyright" id="copyright" value="{{old('copyright') ?? $general?->content->copyright ?? null}}" placeholder="Informe o texto de copyright">
                                @error('copyright')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="formFile" class="form-label">Altere o Logotipo</label>
                                    <input class="form-control" type="file" id="logo-file" name="logo_file" accept=".jpg,.png,.svg,.webp">
                                </div>
                                @error('description')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="d-block">Defina o modelo de status para contas de usuários criadas pela página externa</label>
                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check" name="status_create_account" id="btnradio1" value="active" autocomplete="off"
                                           @if(($general?->content?->status_create_account ?? null) == 'active' || empty($general?->content?->status_create_account)  ) checked @endif>
                                    <label class="btn btn-outline-secondary" for="btnradio1" title="O usuário não passará por nenhum tipo de aprovação">Ativo</label>

                                    <input type="radio" class="btn-check" name="status_create_account" id="btnradio2" value="email_pending" autocomplete="off"
                                           @if(($general?->content?->status_create_account ?? null) == 'email_pending'  ) checked @endif>
                                    <label class="btn btn-outline-secondary" for="btnradio2" title="O usuário precisará confirmar seu e-mail">Validar E-mail</label>

                                    <input type="radio" class="btn-check" name="status_create_account" id="btnradio3" value="pending" autocomplete="off"
                                           @if(($general?->content?->status_create_account ?? null) == 'pending'  ) checked @endif>
                                    <label class="btn btn-outline-secondary" for="btnradio3" title="Um administrador do sistema deverá aprovar a criação da conta">Aguardar Aprovação</label>

                                    <input type="radio" class="btn-check" name="status_create_account" id="btnradio4" value="create_inside" autocomplete="off"
                                           @if(($general?->content?->status_create_account ?? null) == 'create_inside'  ) checked @endif>
                                    <label class="btn btn-outline-secondary" for="btnradio4" title="Usuários só poderão ser criados por um administrador">Criar Internamente</label>
                                </div>
                            </div>
                            <div class="col-12 mt-5">
                                <button class="btn btn-primary" type="submit">Salvar</button>
                            </div>
                        </form>
                        <!-- End Browser Default Validation -->

                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Configurações do Servidor SMTP</h5>

                        <!-- Browser Default Validation -->
                        <form method="post" class="row g-3" action="{{route('admin_save_general_smtp')}}">
                            {{csrf_field()}}
                            @php
                            $smtp = $configurations->where('uid', 'smtp')->first();
                            @endphp
                            <div class="col-md-6">
                                <label for="host" class="form-label">Host</label>
                                <input type="text" class="form-control" name="host" id="smtp-host" value="{{old('host') ?? $smtp?->content?->host}}" placeholder="Ex: smtp.google.com">
                                @error('host')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="port" class="form-label">Porta</label>
                                <input type="text" class="form-control" name="port" id="smtp-port" value="{{old('port') ?? $smtp?->content?->port}}" placeholder="Ex: 587">
                                @error('port')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="port" class="form-label">Segurança</label>
                                <input type="text" class="form-control" name="security" id="smtp-security" value="{{old('security') ?? $smtp?->content?->security}}" placeholder="Ex: SSL">
                                @error('security')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="host" class="form-label">Login</label>
                                <input type="text" class="form-control" name="login" id="smtp-login" value="{{old('login') ?? $smtp?->content?->login}}" placeholder="Informe o login">
                                @error('login')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="host" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="smtp-password" value="{{old('password') ?? ((! empty($smtp?->content?->password)) ? \Illuminate\Support\Facades\Crypt::decrypt($smtp?->content?->password) : null)   }}" placeholder="Informe a senha">
                                @error('password')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>


                            <div class="col-12 mt-5">
                                <button class="btn btn-primary" type="submit">Salvar</button>
                            </div>
                        </form>
                        <!-- End Browser Default Validation -->
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Google APIs</h5>
                        <!-- Browser Default Validation -->
                        <form method="post" class="row g-3" action="{{route('admin_save_general_google')}}">
                            {{csrf_field()}}
                            @php
                                $google = $configurations->where('uid', 'google')->first();
                            @endphp
                            <div class="col-md-6">
                                <label for="google-client-id" class="form-label">ID do Cliete</label>
                                <input type="text" class="form-control" name="client_id" id="google-client-id" value="{{old('client_id') ?? $google?->content?->client_id}}" placeholder="Id do cliente OAuth">
                                @error('client_id')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="google-secret-key" class="form-label">Chave Secreta do Cliente</label>
                                <input type="text" class="form-control" name="secret_key" id="google-secret-key" value="{{old('secret_key') ?? $google?->content?->secret_key}}" placeholder="Chave secreta do cliente OAuth">
                                @error('secret_key')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="google-url-callback" class="form-label">Url de Callback Login Admin</label>
                                <input type="text" class="form-control" disabled value="{{route('admin_login_google_callback')}}">
                            </div>
                            <div class="col-12">
                                <label for="google-url-callback" class="form-label">Url de Callback Login Comum</label>
                                <input type="text" class="form-control" disabled value="{{route('guide_login_google_callback')}}">
                            </div>
                            <div class="col-12">
                                <label for="google-api-key" class="form-label">Chave de API</label>
                                <input type="text" class="form-control" name="api_key" id="google-api-key" value="{{old('api_key') ?? $google?->content?->api_key}}" placeholder="Chave para as APIs">
                                @error('api_key')
                                <span class="invalid-feedback d-block">{{$message}}</span>
                                @enderror
                            </div>

                            <div class="col-12 mt-5">
                                <button class="btn btn-primary" type="submit">Salvar</button>
                            </div>
                        </form>
                        <!-- End Browser Default Validation -->
                    </div>
                </div>
            </div>
        </div>

    </section>
@stop
