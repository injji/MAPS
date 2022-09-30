let datatable = $("#datatable").DataTable({
    rowId: (response) => {
        return `order-row-${response.id}`
    },
    ajax: {
        url: '/order.data',
        data: (data) => {
            if ('columns' in data) {
                data.columns[5].search.value = 'process-'+$('#datatable_filter .input-daterange-datepicker').val()
                data.columns[6].search.value = $('#datatable_filter [name=daterange]').val()
            }
            return data
        },
        dataSrc: (response) => {
            response.data.forEach((item, i) => {
                try {
                    let createdAt = new Date(item.created_at)
                    item.created_at = createdAt.format('Y-m-d H:i:s')

                    item.action = `<div class="radio" style="position: absolute; left: calc(50% - 5px);">
                                        <input type="radio" name="order" onchange="changeServiceExpriedTerm('${moment(item.service_end_at).format('MM/DD/YYYY')}')" name="selected" value="${item.service.id}/${item.site.id}" id="order-${item.id}">
                                        <label for="order-${item.id}">&zwnj;</label>
                                    </div>`

                    if (item.service_end_at) {
                        let createdAt = new Date(item.service_end_at)
                        item.service_end_at = createdAt.format('Y-m-d')
                    } else {
                        item.service_end_at = '-'
                    }
                } catch (e) {
                    console.error(e);
                }
            });
            return response.data
        }
    },
    columns: [
        {data: 'action', name: 'action', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell position-relative'},
        {data: 'no', name: 'no', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'created_at', name: 'created_at', searchable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'service.default_description.name', name: 'service.default_description.name', orderable:!1},
        {data: 'site.name', name: 'site.name', orderable:!1},
        {data: 'process_text', name: 'process', searchable:!1, orderable:!1},
        {data: 'service_end_at', name: 'service_end_at', searchable:!1},
    ],
    order: [1, 'desc'],
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
        $('#datatable_wrapper [name=process]').change((event) => {
            datatable.search($('#datatable_filter [type=search]').val()).ajax.reload(null, true)
        })
        $('#datatable_paginate').before(`
            <div class="form-group row col-md-8 float-left mt-1 d-none d-sm-block row m-0" id="datatable_changeform">
                <span style="line-height:36px;width:auto;" class="float-left">${FORM_LANG.expried_date.label}</span>
                <input
                 type="text"
                 readonly
                 name="service_end_at"
                 style="height:36px; width:120px;"
                 class="form-control col-md-2 col-4 float-left mr-2"
                 data-date-autoclose="true"
                 id="expried_date">
                <button type="button" style="width:120px;" id="changeServiceTerm" disabled onclick="changeServiceTerm()" class="float-left btn btn-primary">${BUTTON_LANG.change}</button>
            </div>
        `)
    }
})

let changeServiceExpriedTerm = (date) => {
    $('#changeServiceTerm').prop("disabled", false);
    $('#datatable_changeform [name=service_end_at]').prop("readonly", false).val(date).datepicker('update');
}

let changeServiceTerm = () => {
    $.ajax({
        url: `//${domain.api}/service/${$('[name=order]:checked').val()}`,
        method: 'post',
        data: {
            service_end_at: $('#expried_date').val()
        },
        success: (response) => {
            if ('message' in response) {
                toastr.success(response.message)
            }
            $(`#order-row-${response.service_client.id} td`).eq(6).html(moment(response.service_client.service_end_at).format('YYYY-MM-DD'))
        }
    })
}
