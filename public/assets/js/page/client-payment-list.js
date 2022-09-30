let datatable = $("#datatable").DataTable({
    rowId: (response) => {
        return `order-row-${response.id}`
    },
    ajax: {
        url: '/payment/list.data',
        data: (data) => {
            return data
        },
        dataSrc: (response) => {
            response.data.forEach((item, i) => {
                try {
                    item.created_at = moment(item.created_at).format('YYYY-MM-DD')
                    item.lang = __('lang.'+item.lang)
                    item.isAnswered = item.answered_at == null ? __('messages.unanswered') : __('messages.answered') + "\n" + `(${moment(item.answered_at).format('YYYY-MM-DD')})`
                    item.subject = `<a href="javascript:replyToReview(${item.id})">${item.subject}</a>`
                    item.service_start_at = moment(item.service_start_at).format('YYYY-MM-DD')
                    item.service_end_at = moment(item.service_end_at).format('YYYY-MM-DD')
                    item.service_term = `${item.service_start_at} ~ ${item.service_end_at}`
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
        {data: 'site_client.name', name: 'site_client.name', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'service.default_description.name', name: 'service.default_description.name', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'type', name: 'type', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'payment_type', name: 'payment_type', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'plan', name: 'plan', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'amount', name: 'amount', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'service_term', name: 'service_term', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
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
