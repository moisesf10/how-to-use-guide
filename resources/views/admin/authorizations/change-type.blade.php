@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item">Pages</li>
                <li class="breadcrumb-item active">Manage Workspaces</li>
            </ol>
        </nav>
        <h1>Gerenciar Autorizações @if($workspace?->user?->id <> auth()->user()->id) <span class="badge bg-info">compartilhada</span> @endif</h1>
        <h6 class="text-black">{{$workspace->name}}</h6>
    </div><!-- End Page Title -->
@stop

@section('css')
    <style>
        .feature {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 4rem;
            width: 4rem;
            font-size: 2rem;
        }

        .submenu-link{
            cursor: pointer;
        }
    </style>
@stop

@section('js')
    <script>
        $(function (){
            $('.submenu-link').click(function (){
                document.location = $(this).attr('data-uri');
            });
        });
    </script>
@stop

@section('content')
    <section class="section">
        <div class="row mt-4">
            <div class="col-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{$workspace->name}}</h5>
                        <p>Escolha abaixo qual tipo de autorização você deseja gerenciar</p>

                        <!-- Browser Default Validation -->
                        <div class="mt-4">
                            <div class="row gx-lg-5 justify-content-center">
                                <div class="col-lg-6 col-xxl-4 mb-5 submenu-link" data-uri="{{route('admin_list_editors_authorization', $workspace->uid)}}">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-pencil-fill"></i></div>
                                            <h2 class="fs-4 fw-bold">Editores</h2>
                                            <p class="mb-0">Forneça permissão para que outros usuários, além de você, possam gerenciar esta workspace</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-xxl-4 mb-5 @if(! $workspace->indicates_public_access) submenu-link @endif" @if(! $workspace->indicates_public_access) data-uri="{{route('admin_list_authorized_users_authorization', $workspace->uid)}}" @endif>
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-people-fill"></i></div>
                                            <h2 class="fs-4 fw-bold @if($workspace->indicates_public_access) text-black-50 @endif">Acesso ao Guia</h2>
                                            <p class="mb-0 @if($workspace->indicates_public_access) text-black-50 @endif">Defina quem são as pessoas que poderão acessar o guia criado por esta workspace</p>
                                            @if($workspace->indicates_public_access)
                                            <p class="mt-3">
                                                <small class="text-danger">Esta workspace está marcada para permitir o acesso público. Desmarque a opção para que possa definir as pessoas que poderão acessá-la</small>
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Browser Default Validation -->

                        </div>
                    </div>

                </div>


            </div>
    </section>
@stop
