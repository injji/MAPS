$('#agent-auth-form').submit((event) => {
    $.ajax({
        url: `//${domain.client}/password/check`,
        method: 'POST',
        data: {
            password: $(event.target).find('[name=password]').val()
        },
        success: (response) => {
            if ('message' in response) {
                toastr.keepMessage('success', response.message)
                location.reload()
            }
        }
    })
})
