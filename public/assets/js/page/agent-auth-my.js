let save = (form) => {
    $.ajax({
        url: `//${domain.api}/user`,
        method: 'post',
        data: new FormData(form),
        contentType: false,
        processData: false,
        success: (response) => {
            if ('message' in response && response.message != '') {
                toastr.success(response.message)
            }
        },
        error: () => {
            toastr.error('입력을 학인하세요.')
        }
    })
}
