let next = (userID) => {
    var chk1 = $("#chk1").is(":checked");
    var chk2 = $("#chk2").is(":checked");

    if(!chk1 || !chk2){
        alert(__('messages.required_agreement'));
        return;
    }
    
    if (userID == null) {
        location.href = '/register'
    } else {
        location.href = '/register/byapps/'+userID
    }
}

let responseErrorHandle = (event, response) => {
    if (response.status == 419) {
        toastr.keepMessage('warning', '세션이 만료되어 새로고침합니다.')
        location.reload()
    } else if (response.status == 422) {
        if ('message' in response.responseJSON && response.responseJSON.message) {
            toastr.error(response.responseJSON.message)
        }
        $('.invalid-message').remove()
        $('.is-invalid').removeClass('is-invalid')
        for (var field in response.responseJSON.errors) {
            let message = response.responseJSON.errors[field].join('<br>')
            let isMailForm = $(`[name=${field}]`)
                .parent('.mail_form')
                .addClass('is-invalid')
                .length > 0
            if (!isMailForm) {
                $(`[name=${field}]`)
                    .addClass('is-invalid')
            }
            $(`[name=${field}]`)
                .change((event) => {
                    let name = $(event.target).attr('name')
                    $(event.target)
                        .parents('.mail_form')
                        .removeClass('is-invalid')
                    $(event.target)
                        .removeClass('is-invalid')
                        .parents('div.field')
                        .parent()
                        .find(`[data-field=${name}]`)
                        .remove()
                        .off('change')
                })
                .parents('div.field')
                .after(`<p class="invalid-message" data-field="${field}">${message}</p>`)
        }
    } else {
        toastr.error(response.responseJSON.message)
    }
}

$(document).ajaxError(responseErrorHandle)

$(document).ready(() => {
    if (message = Cookie.pull('validate_message')) {
        let response = {
            status: 422,
            responseJSON: {
                errors: JSON.parse(message)
            },
        }
        responseErrorHandle(null, response)
    }
})
