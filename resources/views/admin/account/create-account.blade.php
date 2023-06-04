<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Pages / Register - NiceAdmin Bootstrap Template</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{asset('build/plugins/bootstrap/css/bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('build/plugins/bootstrap-icons/bootstrap-icons.css')}}">
    <link rel="stylesheet" href="{{asset('build/plugins/boxicons/css/boxicons.min.css')}}">

    @vite([
        'resources/scss/admin/style.scss',

    ])
</head>

<body>

<main>
    <div class="container">

        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                        <div class="d-flex justify-content-center py-4">
                            <a href="index.html" class="logo d-flex align-items-center w-auto">
                                <img src="{{Vite::image('admin/logo.png')}}" alt="">
                                <span class="d-none d-lg-block">HUG</span>
                            </a>
                        </div><!-- End Logo -->

                        <div class="card mb-3">

                            <div class="card-body">

                                <div class="pt-4 pb-2">
                                    <h5 class="card-title text-center pb-0 fs-4">Criar Conta</h5>
                                    <p class="text-center small">Entre usando sua rede social</p>
                                </div>

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

                                <form method="post" class="row g-3 needs-validation" novalidate action="{{route('admin_save_create_account')}}">
                                    {{csrf_field()}}
                                    @if(! empty(request()->query('redirect-after-login') ))
                                        <input type="hidden" name="redirect_after_login" value="{{request()->query('redirect-after-login')}}">
                                    @endif
                                    <div class="col-12">
                                        <button type="button" class="btn btn-danger"><i class="fa-brands fa-google"></i> Google</button>
                                    </div>

                                    <div class="col-12 text-center">
                                        <span>ou crie uma conta</span>
                                        <hr class="hr" />
                                    </div>

                                    <div class="col-12">
                                        <label for="name" class="form-label">Nome Completo</label>
                                        <input type="text" name="name" class="form-control" id="name" placeholder="Informe seu nome completo" value="{{old('name')}}" required>
                                        @error('name')
                                        <div class="invalid-feedback d-block">{{$message}}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" id="email" placeholder="Ex: seunome@dominio.com.br" value="{{old('email')}}" required>
                                        @error('email')
                                        <div class="invalid-feedback d-block">{{$message}}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="password" class="form-label">Senha</label>
                                        <input type="password" name="password" class="form-control" id="password" placeholder="Crie uma senha" value="{{old('password')}}" required>
                                        @error('password')
                                        <div class="invalid-feedback d-block">{{$message}}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="password-confirmation" class="form-label">Confirmação da Senha</label>
                                        <input type="password" name="password_confirmation" class="form-control" id="password-confirmation" placeholder="Repita a senha criada" value="{{old('password_confirmation')}}" required>
                                        @error('password_confirmation')
                                        <div class="invalid-feedback d-block">{{$message}}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" name="terms" type="checkbox" value="yes" id="terms" required @if(old('terms') == 'yes') checked @endif>
                                            <label class="form-check-label" for="acceptTerms">Eu li e aceito os <a href="#">termos e condições</a></label>
                                            @error('terms')
                                            <div class="invalid-feedback d-block">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100" type="submit">Criar Conta</button>
                                    </div>
                                    <div class="col-12">
                                        <p class="small mb-0">Já tem uma conta? <a href="{{route('admin_login')}}">Log in</a></p>
                                    </div>
                                </form>

                            </div>
                        </div>



                    </div>
                </div>
            </div>

        </section>

    </div>
</main><!-- End #main -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/chart.js/chart.umd.js"></script>
<script src="assets/vendor/echarts/echarts.min.js"></script>
<script src="assets/vendor/quill/quill.min.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="assets/vendor/tinymce/tinymce.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>

<!-- Template Main JS File -->
<script src="assets/js/main.js"></script>

</body>

</html>
