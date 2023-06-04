@php
    $config = config()->get('app.system');
@endphp
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    @php
        $general = $config?->where('uid', 'general')->first();
    @endphp
    <title>{{$general?->content?->system_name ?? 'HUG - How to Use Guide'}}</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('build/plugins/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('build/plugins/bootstrap-icons/bootstrap-icons.css')}}">
    <link rel="stylesheet" href="{{asset('build/plugins/boxicons/css/boxicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('build/plugins/quill/quill.snow.css')}}">
    <link rel="stylesheet" href="{{asset('build/plugins/quill/quill.bubble.css')}}">
    <link rel="stylesheet" href="{{asset('build/plugins/remixicon/remixicon.css')}}">

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
                            <a href="{{route('index')}}" class="logo d-flex align-items-center w-auto">
                                @if(! empty($general?->content?->logo?->filenameToStore) && file_exists(storage_path('app/public/images/'. $general?->content?->logo?->filenameToStore)) )
                                    <img src="{{asset('storage/images/'. $general?->content?->logo?->filenameToStore)}}" alt="{{$general?->content?->system_name ?? 'HUG'}}">
                                @else
                                    <img src="{{Vite::image('admin/logo.png')}}" alt="HUG">
                                @endif
                                <span class="d-none d-lg-block">{{$general?->content?->system_name ?? 'HUG'}}</span>
                            </a>
                        </div><!-- End Logo -->

                        <div class="card mb-3">

                            <div class="card-body">

                                <div class="pt-4 pb-2">
                                    <h5 class="card-title text-center pb-0 mb-0 fs-4">Acesse O Guia </h5>
                                    <div class="text-center" style="font-size: 1.2rem;">{{$workspace->name}}</div>
                                    <p class="text-center small mt-4">Entre com sua rede social</p>
                                </div>

                                <div class="btn-group mb-3">
                                    <button type="button" class="btn btn-danger"><i class="bi bi-google"></i> Google</button>
                                </div>

                                <hr />

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

                                <form method="post" class="row g-3 needs-validation" action="{{route('admin_authenticate')}}" novalidate>
                                    {{csrf_field()}}
                                    @if(! empty(request()->query('redirect-after-login') ))
                                        <input type="hidden" name="redirect_after_login" value="{{request()->query('redirect-after-login')}}">
                                    @endif
                                    <div class="col-12">
                                        <label for="login" class="form-label">Login</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text" id="inputGroupPrepend">@</span>
                                            <input type="text" name="login" class="form-control" id="login" placeholder="Entre com seu e-mail" value="{{old('login')}}" required>
                                            <div class="invalid-feedback">Por favor, entre com seu login.</div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="code" class="form-label">Código</label>
                                        <textarea  name="code" class="form-control" id="code" placeholder="Código enviado por e-mail" required></textarea>
                                        <div class="invalid-feedback">Por favor, entre com seu código!</div>
                                    </div>

                                    <div class="col-12 mt-5">
                                        <button class="btn btn-primary w-100" type="submit">Entrar</button>
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



</body>

</html>
