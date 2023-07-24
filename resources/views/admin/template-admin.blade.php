@php
    $config = config()->get('app.system');
@endphp
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
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
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="{{asset('build/plugins/bootstrap/css/bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('build/plugins/bootstrap-icons/bootstrap-icons.css')}}">
    <link rel="stylesheet" href="{{asset('build/plugins/boxicons/css/boxicons.min.css')}}">
{{--    <link rel="stylesheet" href="{{asset('build/plugins/quill/quill.snow.css')}}">--}}
{{--    <link rel="stylesheet" href="{{asset('build/plugins/quill/quill.bubble.css')}}">--}}
{{--    <link rel="stylesheet" href="{{asset('build/plugins/remixicon/remixicon.css')}}">--}}
    <link rel="stylesheet" href="{{asset('build/plugins/simple-datatables/style.css')}}">
    <!-- Vendor CSS Files -->
    @vite([
        'resources/scss/admin/style.scss'
    ])

    @yield('css')
</head>

<body class="">

<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <a href="{{route('admin_index')}}" class="logo d-flex align-items-center">
            @if(! empty($general?->content?->logo?->filenameToStore) && file_exists(storage_path('app/public/images/'. $general?->content?->logo?->filenameToStore)) )
                <img src="{{asset('storage/images/'. $general?->content?->logo?->filenameToStore)}}" alt="{{$general?->content?->system_name ?? 'HUG'}}">
            @else
                <img src="{{Vite::image('admin/logo.png')}}" alt="HUG">
            @endif
            <span class="d-none d-lg-block">{{$general?->content?->system_name ?? 'HUG'}}</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
        <form class="search-form d-flex align-items-center" method="POST" action="#">
            <input type="text" name="query" placeholder="Search" title="Enter search keyword">
            <button type="submit" title="Search"><i class="bi bi-search"></i></button>
        </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">

            <li class="nav-item d-block d-lg-none">
                <a class="nav-link nav-icon search-bar-toggle " href="#">
                    <i class="bi bi-search"></i>
                </a>
            </li><!-- End Search Icon-->

            <li class="nav-item dropdown pe-3">
                @php
                    $shortName = toShortName(auth()->user()->name);
                    $aux = explode(' ', $shortName);
                    if(count($aux) > 1){
                        $shortName = array_shift($aux). ' ' . substr( (array_pop($aux)), 0, 1) . '.';
                    }else{
                        $shortName = $aux[0];
                    }

                @endphp
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src="{{Vite::image('admin/profile-img.jpg')}}" alt="Profile" class="rounded-circle">
                    <span class="d-none d-md-block dropdown-toggle ps-2">{{$shortName}}</span>
                </a><!-- End Profile Iamge Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6>{{toShortName(auth()->user()->name)}}</h6>
                        <span>{{auth()->user()->email}}</span>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bi bi-person"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bi bi-gear"></i>
                            <span>Account Settings</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bi bi-question-circle"></i>
                            <span>Need Help?</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" data-global-action="logout" href="{{route('admin_logout')}}">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Sair</span>
                        </a>
                    </li>

                </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->

        </ul>
    </nav><!-- End Icons Navigation -->

</header><!-- End Header -->

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
    @include('admin.menu-sidebar')
</aside><!-- End Sidebar-->

<main id="main" class="main">
    @yield('pagetitle')

    @yield('content')

</main><!-- End #main -->

<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
    <div class="copyright">
        @php
        $general = $config?->where('uid', 'general')->first();
        @endphp
        {!! $general?->content?->copyright !!}
    </div>

</footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

<script
    src="https://code.jquery.com/jquery-3.6.4.min.js"
    integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8="
    crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- Vendor JS Files -->
<script src="{{asset('build/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('build/plugins/quill/quill.min.js')}}"></script>
<script src="{{asset('build/plugins/simple-datatables/simple-datatables.js')}}"></script>
<script src="{{asset('build/assets/functions.js')}}"></script>
@vite([
   // 'resources/plugins/apexcharts/apexcharts.min.js',
//    'resources/plugins/chart.js/chart.umd.js',
//    'resources/plugins/echarts/echarts.min.js',
//    'resources/plugins/quill/quill.min.js',
//    'resources/plugins/simple-datatables/simple-datatables.js',
//    'resources/plugins/tinymce/tinymce.min.js',
//    'resources/plugins/php-email-form/validate.js',

])

<!-- Template Main JS File -->
{{--<script src="assets/js/main.js"></script>--}}

<script>

    $(function (){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        /**
         * Sidebar toggle
         */
        if (select('.toggle-sidebar-btn')) {
            on('click', '.toggle-sidebar-btn', function(e) {
                select('body').classList.toggle('toggle-sidebar')
            })
        }
    });

    /**
     * Easy selector helper function
     */
    const select = (el, all = false) => {
        el = el.trim()
        if (all) {
            return [...document.querySelectorAll(el)]
        } else {
            return document.querySelector(el)
        }
    }

    /**
     * Easy event listener function
     */
    const on = (type, el, listener, all = false) => {
        if (all) {
            select(el, all).forEach(e => e.addEventListener(type, listener))
        } else {
            select(el, all).addEventListener(type, listener)
        }
    }

    "use strict";
    /**
     * Easy on scroll event listener
     */
    const onscroll = (el, listener) => {
        el.addEventListener('scroll', listener)
    }

</script>

@yield('js')
</body>

</html>
