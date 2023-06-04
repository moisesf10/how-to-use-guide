<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//PT" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>HUG</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="margin: 0; padding: 0;">
<div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all;">Você foi solicitado paa gerenciar a workspace...</div>
@php
$aux = explode(' ', $editor->name);
$shortName = array_shift($aux) . ( (count($aux) > 1)  ? ' '. array_pop($aux) : '');
@endphp
Olá {{$shortName}}<br/>
Você recebeu uma solicitação para gerenciar a workspace {{$workspace->name}} de {{$workspace->user->name}}<br /><br />
Clique no link abaixo para aceitar o convite: <br/>
<a target="_blank" rel="noopener nofollow norefer" href="{{route('admin_load_invitation_mail_editor_authorization', [$workspace->uid, $token->token])}}">{{route('admin_load_invitation_mail_editor_authorization', [$workspace->uid, $token->token])}}</a>



</body>
</html>
