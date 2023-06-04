function parseErrors(request, status, error, overlayFieldDisable = null) {
    if (overlayFieldDisable !== null) {
        $(overlayFieldDisable).LoadingOverlay('hide');
    }

    // se a sessão estiver expirado redireciona para a tela de login
    if (request.status === 301) {
        alert('Sua sessão expirou. Faça login novamente');
        document.location.href = '/';
    }

    var errors = [];
    if (typeof request.responseJSON.errors !== 'undefined') {
        $.each(request.responseJSON.errors, function (key, value) {

            if (typeof value === 'object') {
                for (var i in value) {
                    errors.push('- ' + value[i] + '<br/>');
                }
            } else {
                errors.push('- ' + value + '<br/>');
            }
        });
    } else {
        if (typeof request.responseJSON.success !== 'undefined' && request.responseJSON.success === false) {
            errors.push(request.responseJSON.message);
        }
    }

    var message = (errors.length < 1) ? 'Erro: ' + request.status + '<br/>Por favor, tente mais tarde'
        : errors.join('') + 'Por favor, verifique os erros e tente novamente!';

    return message;
}

// Mensagens de confirmação com JQUERY CONFIRM
function confirmErrors(message, title = 'Ocorreram Erros!', reload = false, redirect = null) {
    $.confirm({
        title: title,
        content: message,
        type: 'red',
        typeAnimated: true,
        buttons: {
            ok: {
                text: 'Ok',
                btnClass: 'btn btn-danger',
                action: function () {
                    if (reload) {
                        document.location.reload();
                    }
                    if ($.trim(redirect) !== '') {
                        document.location = redirect;
                    }
                }
            }
        }
    });
}

function confirmSuccess(message, title = 'Que ótimo!', reload = false, redirect = null) {
    $.confirm({
        title: title,
        content: message,
        type: 'green',
        typeAnimated: true,
        buttons: {
            ok: {
                text: 'ok',
                btnClass: 'theme-btn',
                action: function () {
                    if (reload) {
                        document.location.reload();
                    }
                    if ($.trim(redirect) !== '') {
                        document.location = redirect;
                    }
                }
            },
        }
    });
}
