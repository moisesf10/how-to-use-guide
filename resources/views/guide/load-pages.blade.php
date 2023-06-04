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
    <title>{{$page->name}}</title>
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


</head>

<body class="">

<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        @php
            $urlParameters = [
                \Illuminate\Support\Str::slug($workspace->name, '-'),
                $workspace->uid,
                \Illuminate\Support\Str::slug($page->block_name, '-'),
                $page->block_id
            ];
        @endphp
        <a href="{{route('guide_page', $urlParameters)}}" class="logo d-flex align-items-center">
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
        <nav>
            <ol class="breadcrumb mb-0">
                @foreach($blocks ?? [] as $cont => $block)
                    @php
                        $urlParameters = [
                            \Illuminate\Support\Str::slug($workspace->name, '-'),
                            $workspace->uid,
                            \Illuminate\Support\Str::slug($block->name, '-'),
                            $block->id
                        ];
                    @endphp
                    @if( $workspace->indicates_public_access ||  \Illuminate\Support\Facades\Gate::allows('allows_access_block', [$workspace, $block])  )
                    <li class="breadcrumb-item "><a class="@if($block->id == $page->block_id ) text-black @endif" href="{{route('guide_page', $urlParameters)}}">{{mb_strtoupper($block->name)}}</a></li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">

            <li class="nav-item d-block d-lg-none">
                <a class="nav-link nav-icon search-bar-toggle " href="#">
                    <i class="bi bi-search"></i>
                </a>
            </li><!-- End Search Icon-->
            @auth
            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src="{{Vite::image('site/icons/user-default.png')}}" alt="Profile" class="rounded-circle">
                    @php
                        $auxName = toShortName(auth()->user()->name);
                        $auxName = explode(' ', $auxName);
                        $name = ($auxName[0] ?? '') . ( (count($auxName) > 1 ) ? ' ' . substr($auxName[1], 0, 1) . '.' : null );
                    @endphp
                    <span class="d-none d-md-block dropdown-toggle ps-2">{{$name}}</span>
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
                        <a class="dropdown-item d-flex align-items-center" href="{{route('guide_logout', [\Illuminate\Support\Str::slug($workspace->name, '-'), $workspace->uid])}}">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Sair</span>
                        </a>
                    </li>
                </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->
            @endauth
        </ul>
    </nav><!-- End Icons Navigation -->

</header><!-- End Header -->

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
    @include('guide.menu-sidebar')
</aside><!-- End Sidebar-->

<main id="main" class="main p-0">
    <iframe src="{{route('guide_load_page', [\Illuminate\Support\Str::slug($workspace->name, '-'), $workspace->uid, $page->id])}}" width="100%" height="100%" style="height: 100vh;"></iframe>
</main><!-- End #main -->

<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
    <div class="copyright">
        &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
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
