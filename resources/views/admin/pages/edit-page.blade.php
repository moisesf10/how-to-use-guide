@extends('admin.template-admin')

@section('pagetitle')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin_index')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_manage_workspace', [$workspace->uid])}}">Workspace</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin_list_pages', [$workspace->uid])}}">Páginas</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h1>Página @if($workspace?->user?->id <> auth()->user()->id) <span class="badge bg-info">compartilhada</span> @endif</h1>
        <h6 class="text-black">{{$workspace->name}}</h6>
    </div><!-- End Page Title -->
@stop

@section('css')
    <meta name="id" @if(! empty($page->id)) content="{{$page->id}}" @else content="" @endif>

    {{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />--}}
{{--    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />--}}



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
        // $('#block').select2();
        $('#block').change(function (){
            var uri = '{{route("admin_block_list_page", [$workspace->uid])}}/' + $(this).val() ;

            $('#topic').html('<option value="" selected>Selecione...</option>');
            $('#block, #topic').LoadingOverlay('show');
            $.ajax({
                url: uri,
                type: "GET",
                processData: false,
                contentType: false,
                //data: formData ,
                dataType: "json",
                success: function (json) {
                    $('#block, #topic').LoadingOverlay('hide');
                    if(json.success){
                        for(var i in json.data){
                            var html = '<option value="'+ json.data[i].id + '">'+ json.data[i].name + '</option>';
                            $('#topic').append(html)
                        }
                    }
                },
                error: function (request, status, error) {
                    $('#block, #topic').LoadingOverlay('hide');
                    var message = parseErrors(request, status, error);
                    confirmErrors(message);
                }


            }).done(function () {
                $('#block, #topic').LoadingOverlay('hide');
            });
        });

        @if(! empty($page))
            var uri = '{{route('admin_load_code_page', [$workspace->uid, $page->id])}}';
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
        var block = $('#block').val();
        var topic = $('#topic').val();
        var name =  $.trim($('#name').val());

        isError = false;
        if($.trim(block) === ''){
            $('.invalid-block').addClass('d-block').html('Por favor, informar o bloco');
            isError = true;
        }
        if($.trim(topic) === ''){
            $('.invalid-topic').addClass('d-block').html('Por favor, informar o tópico');
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

        var uri = '{{route("admin_save_page", $workspace->uid)}}';
        var formData = new FormData();
        if($.trim($('meta[name="id"]').attr('content')) !== ""){
            formData.append('id', $('meta[name="id"]').attr('content') );
        }
        formData.append('block_id', block);
        formData.append('topic_id', topic);
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
                    <a href="{{route('admin_edit_page', $workspace->uid)}}" class="btn btn-secondary">+ Nova Página</a>
                    <a href="{{route('admin_list_pages', $workspace->uid)}}" class="btn btn-light">Listar Páginas</a>
                </div>
            </div>
        </div>

        <div class="row mt-4">

                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3">
                            <div class="row">

                                <div class="col-md-3">
                                    <label for="block" class="form-label">Bloco</label>
                                    <select id="block" class="form-select">
                                        <option value="" selected>Selecione...</option>
                                        @foreach($blocks ?? [] as $block)
                                            <option value="{{$block->id}}" @if(! empty($page) && $block->id == $page->topic->block->id ) selected @endif >{{$block->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback invalid-block @error('block') d-block @enderror">@error('block') {{$message}} @enderror</span>
                                </div>
                                <div class="col-md-3">
                                    <label for="topic" class="form-label">Tópico</label>
                                    <select id="topic" class="form-select">
                                        <option value="" selected>Selecione...</option>
                                        @foreach($topics ?? [] as $topic)
                                            <option value="{{$topic->id}}" @if(! empty($page) && $topic->id == $page->topic->id ) selected @endif >{{$topic->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback invalid-topic @error('topic') d-block @enderror">@error('topic') {{$message}} @enderror</span>
                                </div>

                                <div class="col-md-3">
                                    <label for="name" class="form-label">Nome da Página</label>
                                    <input type="text" class="form-control" id="name" placeholder="Informe o nome da página" value="{{old('name') ?? ($page?->page_name ?? null) }}">
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
