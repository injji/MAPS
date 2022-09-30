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
        url: '/company/client.data',
        data: (data) => {
            data.columns[6].search.value = $('#datatable_filter [name=daterange]').val()
            return data
        },
        dataSrc: (response) => {
            response.data.forEach((item, i) => {
                try {
                    let createdAt = new Date(item.created_at)
                    item.created_at = createdAt.format('Y-m-d H:i:s')
                } catch (e) {
                    console.error(e);
                }
            });
            return response.data
        }
    },
    columns: [
        {data: 'no', name: 'no', searchable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'company', name: 'company.name', orderable:!1},
        {data: 'business_no', name: 'business_no', orderable:!1},
        {data: 'manager', name: 'manager', orderable:!1},
        {data: 'managercall', name: 'managercall', searchable:!1, orderable:!1},
        {data: 'manageremail', name: 'manageremail', searchable:!1, orderable:!1},
        {data: 'created_at', name: 'created_at', searchable:!1},
    ],
    pageLength: 20,
    responsive: !0,
    language: {
        search: ``
    },
    // fnDrawCallback: () => {
    //     $('#datatable').find('tbody tr td:not(.select-checkbox)').click(function() {
    //         localStorage.historyScroll = $(document).scrollTop();
    //         localStorage.historyReff = document.referrer;
    //         //페이지  이동 전이니까 +1
    //         localStorage.historyLength = history.length+1;
    //         location.href = `/company/agent/${$(this).parents('tr').attr('id')}`
    //     });
    // },
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
    }
})
