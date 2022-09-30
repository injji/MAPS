let datatable = $("#datatable").DataTable({
    ajax: {
        url: '/order/payment.data',
        data: (data) => {
            if ('columns' in data) {
                data.columns[4].search.value = 'type-'+$('#datatable_wrapper [name=type]').val()
                data.columns[5].search.value = 'payment_type-'+$('#datatable_wrapper [name=payment_type]').val()
                data.columns[1].search.value = $('#datatable_filter .input-daterange-datepicker').val()
            }
            return data
        },
        dataSrc: (response) => {
            response.data.forEach((item, i) => {
                try {
                    item.created_at = moment(item.created_at).format('YYYY-MM-DD')
                    item.service_start_at = moment(item.service_start_at).format('YYYY-MM-DD')
                    item.service_end_at = moment(item.service_end_at).format('YYYY-MM-DD')

                    item.service_term = `${item.service_start_at} ~ ${item.service_end_at}`
                } catch (e) {
                    console.error(e);
                }
            });
            return response.data
        }
    },
    columns: [
        {data: 'no', name: 'no', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'created_at', name: 'created_at', searchable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'service.default_description.name', name: 'service.default_description.name', orderable:!1},
        {data: 'site.name', name: 'site.name', orderable:!1},
        {data: 'type_text', name: 'type', searchable:!1, orderable:!1},
        {data: 'payment_type_text', name: 'payment_type', searchable:!1, orderable:!1},
        {data: 'plan', name: 'plan', searchable:!1, orderable:!1},
        {data: 'amount', name: 'amount', searchable:!1},
        {data: 'service_term', name: 'service_term', searchable:!1, orderable:!1},
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
