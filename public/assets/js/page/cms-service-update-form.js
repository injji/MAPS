$('#service-update').submit((event) => {
    console.log(event.target);
    let request = new FormData(event.target)
    $.ajax({
        method: 'post',
        data: request,
        processData: false,
        contentType: false,
        success: (response) => {
            if ('message' in response) {
                toastr.success(response.message)
                location.href = response.redirect
            }
        }
    })
})
