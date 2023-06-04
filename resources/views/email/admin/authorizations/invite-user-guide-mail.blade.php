<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//PT" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>HUG</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="margin: 0; padding: 0;">
<div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all;">Acesse o manual de utilização...</div>
@php
    $shortName = toShortName($authorizedUser->name)
@endphp
Olá {{$shortName}}<br/>
Você recebeu um convite para acessar o guia de uso para <b>{{$workspace->name}}</b><br /><br />
Utilize o link abaixo para acessar o guia: <br/>
@php
$token = $authorizedUser->token;
$data =[
    'email' => $authorizedUser->email,
    'authorization_token' =>  $authorizedUser->authorization_token
];
$encrypted =  \Illuminate\Support\Facades\Crypt::encrypt(json_encode($data))
@endphp
<a target="_blank" rel="noopener nofollow norefer" href="{{route('guide_connect', [\Illuminate\Support\Str::slug($workspace->name, '-') , $workspace->uid, 'code' => $encrypted ])}}">{{route('guide_connect', [$workspace->name, $workspace->uid, 'code' => $encrypted ])}}</a>



</body>
</html>
<?php
