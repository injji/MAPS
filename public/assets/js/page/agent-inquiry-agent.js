let datatable = $("#datatable").DataTable({
    rowId: (response) => {
        return `order-row-${response.id}`
    },
    ajax: {
        url: '/inquiry/agent.data',
        data: (data) => {
            return data
        },
        dataSrc: (response) => {
            response.data.forEach((item, i) => {
                try {
                    item.created_at = moment(item.created_at).format('YYYY-MM-DD')
                    item.subject = `<a href="javascript:getRow(${item.id})">${item.subject}</a>`
                    item.isAnswered = item.answered_at == null ? __('messages.unanswered') : __('messages.answered') + "\n" + `(${moment(item.answered_at).format('YYYY-MM-DD')})`
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
        {data: 'type', name: 'type', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'subject', name: 'subject', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
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

let getRow = (id) => {
    $.ajax({
        url: `//${domain.api}/inquiry/agent/${id}`,
        success: () => {

        }
    })
}
