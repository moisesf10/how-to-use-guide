@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin_index')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_manage_workspace', [$workspace->uid])}}">Workspace</a></li>
                <li class="breadcrumb-item active"><a href="{{route('admin_list_landingpages', [$workspace->uid])}}">Landing Page</a></li>
            </ol>
        </nav>
        <h1>Landing Page @if($workspace?->user?->id <> auth()->user()->id) <span class="badge bg-info">compartilhada</span> @endif</h1>
        <h6 class="text-black">{{$workspace->name}}</h6>
    </div><!-- End Page Title -->
@stop

@section('css')
    <meta name="id" @if(! empty($page->id)) content="{{$page->id}}" @else content="" @endif>



@stop

@section('js')
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js" integrity="sha512-RtZU3AyMVArmHLiW0suEZ9McadTdegwbgtiQl5Qqo9kunkVg1ofwueXD8/8wv3Af8jkME3DDe3yLfR8HSJfT2g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    @vite([
        'resources/js/app.js',
        //'resources/js/select2.js'
    ])

    <script>
        $(function (){
            $('body').addClass('toggle-sidebar');


            @if(! empty($page))
            var uri = '{{route('admin_load_code_landingpage', [$workspace->uid, $page->id])}}';
            $('.gjs-frame').LoadingOverlay('show');
            $.ajax({
                url: uri,
                type: "GET",
                processData: false,
                contentType: false,
                //data: formData ,
                dataType: "json",
                success: function (json) {
                    $('.gjs-frame').LoadingOverlay('hide');
                    if(json.success){
                        editor.setComponents(JSON.parse(json.components));
                        editor.setStyle(json.css);
                        $('#url').val(json.uri);
                    }
                },
                error: function (request, status, error) {
                    $('.gjs-frame').LoadingOverlay('hide');
                    var message = parseErrors(request, status, error);
                    confirmErrors(message);
                }


            }).done(function () {
                $('.gjs-frame').LoadingOverlay('hide');
            });
            @endif

        });

        async function save($button){
            var url = $.trim($('#url').val());
            var name =  $.trim($('#name').val());

            isError = false;
            if($.trim(url) === ''){
                $('.invalid-url').addClass('d-block').html('Por favor, informar a url');
                isError = true;
            }
            if(name === ''){
                $('.invalid-name').addClass('d-block').html('Por favor, informar o nome');
                isError = true;
            }

            if(isError){
                return false;
            }

            $button.LoadingOverlay('show');

            var html = editor.getHtml();
            var css = editor.getCss();
            var js = editor.getJs();
            var components = JSON.stringify(editor.DomComponents.getWrapper());
            var base64ScreenShot = null;
            var iframeBody = $('.gjs-frame').contents().find('body')[0];
            await html2canvas(iframeBody, {allowTaint     : true, useCORS: true}).then(function (canvas){
                base64ScreenShot = canvas.toDataURL("image/jpeg", 0.8);

            });

            var uri = '{{route("admin_save_landingpage", $workspace->uid)}}';
            var formData = new FormData();
            if($.trim($('meta[name="id"]').attr('content')) !== ""){
                formData.append('id', $('meta[name="id"]').attr('content') );
            }

            formData.append('url', url);
            formData.append('name', name);

            formData.append('css', css);
            formData.append('js', js);
            formData.append('html', html);
            formData.append('components', components);
            formData.append('base64ScreenShot', base64ScreenShot);


            $.ajax({
                url: uri,
                type: "POST",
                processData: false,
                contentType: false,
                data: formData ,
                dataType: "json",
                success: function (json) {
                    $button.LoadingOverlay('hide');
                    if(json.success){
                        $('meta[name="id"]').attr('content', json.id);
                        $('#url').val(json.uri);
                        confirmSuccess('A página foi salva com sucesso');
                    }
                },
                error: function (request, status, error) {
                    $button.LoadingOverlay('hide');
                    var message = parseErrors(request, status, error);
                    confirmErrors(message);
                }


            }).done(function () {
                $button.LoadingOverlay('hide');
            });

        }

    </script>

@stop

@section('content')
    <section class="section mt-4">
        <div class="row mb-5">
            <div class="col-12">
                <div class="btn-group" role="group" aria-label="">
                    <a href="{{route('admin_edit_landingpage', $workspace->uid)}}" class="btn btn-secondary">+ Nova Página</a>
                    <a href="{{route('admin_list_landingpages', $workspace->uid)}}" class="btn btn-light">Listar Páginas</a>
                </div>
            </div>
        </div>

        <div class="row mt-4">

            <div class="col-12">
                <div class="card">
                    <div class="card-body pt-3">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="basic-url" class="form-label">URL da página</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon3">{{route('landing_page_guide', [ \Illuminate\Support\Str::slug($workspace->name, '-')  ,$workspace->uid])}}/</span>
                                        <input type="text" class="form-control" name="url" id="url" aria-describedby="basic-addon3 basic-addon4" placeholder="Complete a URL de acesso à página" value="{{old('url') ?? ($page->uri ?? null)}}">
                                    </div>
                                    <span class="invalid-feedback invalid-url @error('url') d-block @enderror">@error('url') {{$message}} @enderror</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="name" class="form-label">Nome da Página</label>
                                <input type="text" class="form-control" id="name" placeholder="Informe o nome da página" value="{{old('name') ?? ($page?->name ?? null) }}">
                                <span class="invalid-feedback invalid-name @error('name') d-block @enderror">@error('name') {{$message}} @enderror</span>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success" onclick="save($(this))">Salvar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div id="gjs">

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@stop
