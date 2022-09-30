//CCC
let createReview = (form) => {
    $.ajax({
        url: `//${domain.api}/service/create/${SERVICE.id}/review`,
        method: 'post',
        processData: false,
        contentType: false,
        data: new FormData(form),
        success: (response) => {
            $('#review_modal').modal('hide')
            loadReview()
        }
    })
}

let createInquiry = (form) => {
    $('#detail_btn2').modal('hide')
    $.ajax({
        url: `//${domain.api}/service/${SERVICE.id}/inquiry`,
        method: 'post',
        processData: false,
        contentType: false,
        data: new FormData(form),
        success: (response) => {
            loadInquiry()
        }
    })
}

let reviewPage = 1
$('.review-page').click(loadReview = (event = null) => {
    if (event) {
        reviewPage = $(event.target).data().page
    }
    $.ajax({
        url: `/service/detail/${SERVICE.id}/reviews`,
        data: {
            page: reviewPage,
        },
        success: (response) => {
            $('#review-content').html(response)
            $('.act.review-page').removeClass('act')
            $(event.target).addClass('act')
        }
    })
})

let inquiryPage = 1
$('.inquiry-page').click(loadInquiry = (event = null) => {
    if (event) {
        inquiryPage = $(event.target).data().page
    }
    $.ajax({
        url: `/service/detail/${SERVICE.id}/inquiries`,
        data: {
            page: inquiryPage,
        },
        success: (response) => {
            $('#inquiry-content').html(response)
            $('.act.inquiry-page').removeClass('act')
            $(event.target).addClass('act')
        }
    })
})
