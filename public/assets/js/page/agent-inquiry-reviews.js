let datatable = $("#datatable").DataTable({
    rowId: (response) => {
        return `order-row-${response.id}`
    },
    ajax: {
        url: '/inquiry/store/review.data',
        data: (data) => {
            return data
        },
        dataSrc: (response) => {
            response.data.forEach((item, i) => {
                try {
                    item.created_at = moment(item.created_at).format('YYYY-MM-DD')
                    item.lang = __('lang.'+item.lang)
                    item.isAnswered = item.answered_at == null ? __('messages.unanswered') : __('messages.answered') + "\n" + `(${moment(item.answered_at).format('YYYY-MM-DD')})`
                    item.content = `<a href="javascript:replyToReview(${item.id})">${item.content}</a>`
                } catch (e) {
                    console.error(e);
                }
            })
            return response.data
        }
    },
    columns: [
        {data: 'no', name: 'no', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'created_at', name: 'created_at', searchable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'lang', name: 'lang', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'author.login_id', name: 'author.login_id', orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'service.default_description.name', name: 'service.default_description.name', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'rating', name: 'rating', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'content', name: 'content', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'isAnswered', name: 'isAnswered', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
    ],
    initComplete: () => {
        $('#datatable_filter label').prepend(`<span><i class="far fa-calendar-alt"></i><input
         class="form-control input-daterange-datepicker text-center"
         style="height:30px"
         type="text"
         name="daterange"/></span>`)
        $('#datatable_filter [name=daterange]').change(() => {
            datatable.search($('#datatable_filter [type=search]').val()).ajax.reload()
        })
        let search = (event) => {
            if (event.code == 'Enter') {
                $(event.target).off(event)
                datatable.search($(event.target).val()).ajax.reload(() => {
                    $(event.target).keyup(search)
                })
            }
        }
        $('#datatable_filter [type=search]').unbind().keyup(search)
        new Litepicker({
            element: $('#datatable_filter .input-daterange-datepicker')[0],
            singleMode: false,
            numberOfMonths: 2,
            numberOfColumns: 2,
            format: 'YYYY-M-DD',
            onHide: (...params) => {
                datatable.ajax.reload(null, true)
            }
        })
    }
})

let replyToReview = (id) => {
    $.ajax({
        url: `/inquiry/store/review/${id}`,
        success: (response) => {
            $('#reply-review-modal [data-role=lang] td').html(__('lang.'+response.lang))
            $('#reply-review-modal [data-role=author_id] td').html(response.author.login_id)
            $('#reply-review-modal [data-role=created_at] td').html(moment(response.created_at).format('YYYY-MM-DD HH:mm:ss'))
            $('#reply-review-modal [data-role=rating] td').html(response.rating)
            $('#reply-review-modal [data-role=content] td').html(response.content)
            $('#reply-review-form textarea').val(response.answer)
            $('#reply-review-form').attr('data-id', id)
            $('#reply-review-modal').modal('show')
        }
    })
}

$('#reply-review-form').submit((event) => {
    $.ajax({
        url: `//${domain.api}/inquiry/review/${$(event.target).attr('data-id')}/reply`,
        method: 'post',
        data: new FormData(event.target),
        contentType: false,
        processData: false,
        success: (response) => {
            $('#reply-review-modal').modal('hide')
            datatable.ajax.reload()
        }
    })
})
