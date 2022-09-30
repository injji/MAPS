let menuSubmit = () => {
    let form = $('#menu-form')
    if (form.length == 0) {
        return
    }
    $.ajax({
        url: '/admin/menu',
        method: 'POST',
        contentType: false,
        processData: false,
        data: new FormData(form[0]),
        success: (response) => {
            toastr.success(response.message)
        },
        error: (response) => {
        }
    })
}
