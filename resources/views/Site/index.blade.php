<!DOCTYPE html>
<html lang="pt-br" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>GoHub | Free Traveling Template</title>


    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicons/favicon-16x16.png">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicons/favicon.ico">

    <meta name="msapplication-TileImage" content="assets/img/favicons/mstile-150x150.png">
    <meta name="theme-color" content="#ffffff">


    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@400;500;600;700&amp;family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,300&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('build/plugins/bootstrap/css/bootstrap.min.css')}}">
    @vite([

        'resources/css/site/theme.css',
        'resources/css/site/user.css',
    ])



</head>


<body>

<!-- ===============================================-->
<!--    Main Content-->
<!-- ===============================================-->
<main class="main" id="top">
    <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" data-navbar-on-scroll="light">
        <div class="container"><a class="navbar-brand" href="index.html"><img src="{{Vite::image('site/icons/Logo.png')}}" height="35" alt="logo" /></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            {{--<div class="collapse navbar-collapse border-top border-lg-0 mt-4 mt-lg-0 " id="navbarSupportedContent">
                ul class="navbar-nav ms-auto pt-2 pt-lg-0 font-base align-items-center">
                    <li class="nav-item"><a class="nav-link px-3" href="#product">Product</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#customers">Customers</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#pricing">Pricing</a></li>
                    <li class="nav-item"><a class="nav-link pl-3 me-3" href="#docs">Docs </a></li>
                </ul>
            </div>--}}
            <a href="{{route('admin_login')}}" class="btn btn-primary align-items-end ">Começar</a>
        </div>
    </nav>
    <section class="py-7 py-lg-10" id="home">
        <div class="bg-holder bg-size" style="background-image:url({{Vite::image('site/illustration/2.png')}});background-position:left top;background-size:contain;">
        </div>
        <!--/.bg-holder-->

        <div class="bg-holder d-none d-xxl-block hero-bg" style="background-image:url({{Vite::image('site/illustration/1.png')}});background-position:right top;background-size:contain;">
        </div>
        <!--/.bg-holder-->

        <div class="container">
            <div class="row align-items-center h-100 justify-content-center justify-content-lg-start">
                <div class="col-md-9 col-xxl-5 text-md-start text-center py-6 pt-8">
                    <h1 class="fs-4 fs-md-5 fs-xxl-4" data-zanim-xs='{"delay":0.3}' data-zanim-trigger="scroll">Balance agility with stability</h1>
                    <p class="fs-1" data-zanim-xs='{"delay":0.5}' data-zanim-trigger="scroll">Gohub monitors application stability, so you can make data-driven decision on whether you should be building new features, or fixing bugs. </p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-md-start mt-5" data-zanim-xs='{"delay":0.5}' data-zanim-trigger="scroll"><a class="btn btn-sm btn-primary me-1" href="#!" role="button">Get started</a><a class="btn btn-sm btn-default" href="#" role="button">Questions? Talk to our team<i class="fas fa-arrow-right ms-2"></i></a></div>
                </div>
            </div>
        </div>
    </section>

</main>
<!-- ===============================================-->
<!--    End of Main Content-->
<!-- ===============================================-->


</body>

</html>
