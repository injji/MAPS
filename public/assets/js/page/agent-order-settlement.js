let datatable = $("#datatable").DataTable({
    ajax: {
        url: '/order/payment/settlement.data',
        data: (data) => {
            if ('columns' in data) {

            }
            return data
        },
        dataSrc: (response) => {
            response.data.forEach((item, i) => {
                try {
                    item.created_at = moment(item.created_at).format("YYYY-MM-DD<br>HH:mm:ss")
                    item.service_start_at = moment(item.service_start_at).format('YYYY-MM-DD')
                    item.service_end_at = moment(item.service_end_at).format('YYYY-MM-DD')
                    item.service_term = `${item.service_start_at - item.service_end_at}`

                    item.service_term = `${item.service_start_at} ~ ${item.service_end_at}`

                    item.option_name = `${item.service.description[0].name} - ${item.plan}`

                } catch (e) {
                    console.error(e);
                }
            });
            return response.data
        }
    },
    columns: [
        {data: 'no', name: 'no', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'created_at', name: 'created_at', searchable:!1},
        {data: 'option_name', name: 'option_name', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'site.name', name: 'site.name', className: 'd-none d-sm-table-cell'},
        {data: 'type_text', name: 'type', orderable: !1, searchable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'service_term', name: 'service_start_at', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'amount', name: 'amount', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'settlement', name: 'settlement', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
    ],
    order: [1, 'desc'],
    initComplete: () => {
        $('#datatable_filter label').prepend(`<span><i class="far fa-calendar-alt"></i><input
         class="form-control input-daterange-datepicker text-center"
         style="height:30px"
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
        $('#datatable_wrapper [name=type]').change((event) => {
            datatable.search($('#datatable_filter [type=search]').val()).ajax.reload(null, true)
        })
        $('#datatable_wrapper [name=payment_type]').change((event) => {
            datatable.search($('#datatable_filter [type=search]').val()).ajax.reload(null, true)
        })
    }
})
