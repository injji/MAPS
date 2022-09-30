let datatable = $("#datatable").DataTable({
    dom: "Bfrtip",
    scrollY: "600px",
    scrollX: !0,
    scrollCollapse: !0,
    buttons: [{
        extend: 'excel',
        className: 'btn-sm',
        text: 'download',
    }],
    rowId: (response) => {
        return `${response.id}`
    },
    serverSide: true,
    ajax: {
        url: '/service/stat.data',
        data: (data) => {
            data.columns[0].search.value = $('#datatable_filter [name=daterange]').val()
            data.columns[2].search.value = 'type-'+$('#datatable_wrapper [name=type]').val()
            return data
        },
        dataSrc: (response) => {
            response.data.forEach((item, i) => {
                try {
                    let createdAt = new Date(item.created_at)
                    item.created_at = createdAt.format('Y-m-d')
                } catch (e) {
                    console.error(e);
                }
            });
            return response.data
        }
    },
    columns: [
        {data: 'created_at', name: 'created_at', searchable:!1},
        {data: 'service.default_description.name', name: 'service.default_description.name', orderable:!1},
        {data: 'type_text', name: 'type', searchable:!1, orderable:!1,},
        {data: 'cnt', name: 'cnt', searchable:!1},
        {data: 'sum_amount', name: 'sum_amount', searchable:!1,},
    ],
    pageLength: 50,
    responsive: !0,
    language: {
        search: ``
    },
    initComplete: () => {
        $('#datatable_filter label').prepend(`<input
         class="form-control input-daterange-datepicker text-center"
         style="height:30px"
         type="text"
         name="daterange"/>`)
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
        $('#datatable_filter .input-daterange-datepicker').click((event) => {
            $(event.target).off(event)
            $(event.target).daterangepicker({
                buttonClasses: ["btn", "btn-sm"],
                applyClass: "btn-success",
                cancelClass: "btn-secondary",
                startDate: '01/01/2021',
                endDate: moment().endOf('month'),
            }).focus()
        })
        $('#datatable_wrapper [name=process]').change((event) => {
            datatable.search($('#datatable_filter [type=search]').val()).ajax.reload(null, true)
        })
    }
})
