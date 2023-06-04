@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin_index')}}">Home</a></li>
                <li class="breadcrumb-item active">Gerenciar Workspaces</li>
            </ol>
        </nav>
        <h1>Gerenciar Workspace @if($workspace?->user?->id <> auth()->user()->id) <span class="badge bg-info">compartilhada</span> @endif</h1>
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
                        <h5 class="card-title"><a target="_blank" href="{{route('guide_start', [$workspace->name, $workspace->uid])}}">Clique aqui para ver a versão final da workspace</a> </h5>
                        <p>Utilize os menus em bloco abaixo para gerenciar sua Workspace</p>

                        <!-- Browser Default Validation -->
                        <div class="mt-4">
                            <div class="row gx-lg-5">
                                <div class="col-lg-6 col-xxl-4 mb-5 submenu-link" data-uri="{{route('admin_edit_workspace', $workspace->uid)}}">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-grid"></i></div>
                                            <h2 class="fs-4 fw-bold">Editar Workspace</h2>
                                            <p class="mb-0">Edite as configurações básicas da workspace</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-xxl-4 mb-5 submenu-link" data-uri="{{route('admin_list_blocks', $workspace->uid)}}">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-box-fill"></i></div>
                                            <h2 class="fs-4 fw-bold">Blocos</h2>
                                            <p class="mb-0">Blocos são áreas separadas dentro da workspace. Um exemplo é a criação de um bloco chamado de <i>referência</i> e outro chamado <i>sandbox</i></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-xxl-4 mb-5 submenu-link" data-uri="{{route('admin_choose_block_topics', $workspace->uid)}}">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-menu-app"></i></div>
                                            <h2 class="fs-4 fw-bold">Tópicos</h2>
                                            <p class="mb-0">Tópicos podem ser vistos como níveis do menu da documentação.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-xxl-4 mb-5 submenu-link" data-uri="{{route('admin_list_pages', $workspace->uid)}}">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-book-half"></i></div>
                                            <h2 class="fs-4 fw-bold">Páginas</h2>
                                            <p class="mb-0">Aqui serão criadas as páginas que fazem parte da documentação</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-xxl-4 mb-5 submenu-link" data-uri="{{route('admin_list_landingpages', $workspace->uid)}}">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-code"></i></div>
                                            <h2 class="fs-4 fw-bold">Landing Pages</h2>
                                            <p class="mb-0">Você pode querer criar páginas de apresentação da documentação, porém não quer que elas fiquem organizadas através de tópicos</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-xxl-4 mb-5 submenu-link" data-uri="{{route('admin_change_type_authorization', $workspace->uid)}}">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-person-fill-lock"></i></div>
                                            <h2 class="fs-4 fw-bold">Autorizações</h2>
                                            <p class="mb-0">Defina quem está autorizado a acessar a documentação ou gerenciar sua workspace</p>
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
