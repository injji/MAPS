let fees = 2.0

let datatable = $("#datatable").DataTable({
    ajax: {
        url: '/order/refund.data',
        data: (data) => {
            if ('columns' in data) {
                data.columns[4].search.value = 'payment_type-'+$('#datatable_filter .input-daterange-datepicker').val()
                data.columns[8].search.value = 'process-'+$('#datatable_wrapper [name=process]').val()
                data.columns[1].search.value = $('#datatable_filter [name=daterange]').val()
            }
            return data
        },
        dataSrc: (response) => {
            response.data.forEach((item, i) => {
                try {
                    item.created_at = moment(item.created_at).format('YYYY-MM-DD HH:mm:ss')
                    item.payment.created_at = moment(item.payment.created_at).format('YYYY-MM-DD')
                    item.payment.service_start_at = moment(item.payment.service_start_at).format('YYYY-MM-DD')
                    item.payment.service_end_at = moment(item.payment.service_end_at).format('YYYY-MM-DD')

                    item.payment.service_term = `${item.payment.service_start_at} ~ ${item.payment.service_end_at}`

                    item.process_text = `<a href="javascript:process(${item.id}, ${item.process}, ${i})">${item.process_text}</a>`

                    item.payment.currency = currency[item.payment.currency]
                } catch (e) {
                    console.error(e);
                }
            });
            return response.data
        }
    },
    columns: [
        {data: 'no', name: 'no', searchable:!1, orderable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'payment.created_at', name: 'payment.created_at', searchable:!1, className: 'd-none d-sm-table-cell'},
        {data: 'service.default_description.name', name: 'service.default_description.name', orderable:!1},
        {data: 'payment.site.name', name: 'payment.site.name', orderable:!1},
        {data: 'payment.payment_type_text', name: 'payment.payment_type', searchable:!1, orderable:!1},
        {data: 'payment.amount_text', name: 'payment.amount', searchable:!1, orderable:!1},
        {data: 'payment.service_term', name: 'payment.service_term', searchable:!1, orderable:!1},
        {data: 'created_at', name: 'created_at', searchable:!1},
        {data: 'process_text', name: 'process', searchable:!1, orderable:!1},
    ],
    order: [7, 'desc'],
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

        $('#datatable_wrapper [name=payment_type]').change((event) => {
            datatable.search($('#datatable_filter [type=search]').val()).ajax.reload(null, true)
        })
        $('#datatable_wrapper [name=process]').change((event) => {
            datatable.search($('#datatable_filter [type=search]').val()).ajax.reload(null, true)
        })
    }
})

let process = (id, process, no) => {
    let payment = datatable.ajax.json().data[no].payment
    switch (process) {
        case 0:
        case 2:
            $(`#refund-form [name=process][value=${process}]`).prop('checked', true).change()
            $(`#refund-form #refund_reason`).text(datatable.ajax.json().data[no].client_comment || '-')
            $('#refund-form [name=refund_date]').val(payment.service_start_at)
            $('#refund-form [name=refund_date]').attr('data-no', no)
            $('#refund-form [name=refund_amount]').attr('max', datatable.ajax.json().data[0].payment.amount)
            $('#refund_amount').attr('helper', payment.currency)
            $('#refund-form [name=id]').val(datatable.ajax.json().data[no].id)
            $('#refund-form [name=comment]').val(datatable.ajax.json().data[no].agent_comment)
            $('#refund-form').modal('show')
            break
        case 1:
        case 3:
            Swal.fire({
                html: process == 1 ? MESSAGES_LANG.refund_progress : MESSAGES_LANG.refund_done.replace("\n", "<br>").replace(':amount', `${datatable.ajax.json().data[no].refund_amount} ${payment.currency}`).replace(':date', moment(datatable.ajax.json().data[no].refunded_at).format('YYYY-MM-DD')),
            })
            break
    }
}

let refundProcess = (process) => {
    document.getElementById('comment').layout()
    document.getElementById('refund_date').layout()
    $('#refund-form [data-process]').addClass('d-none')
    $(`#refund-form [data-process=${process}]`).removeClass('d-none')
}

$('#refund-form [name=refund_date]').change((event) => {
    let payment = datatable.ajax.json().data[$(event.target).attr('data-no')].payment
    let paymentTerm = (new Date(payment.service_end_at) - new Date(payment.service_start_at)) / (60 * 60 * 24 * 1000) + 1
    let refundTerm = Math.ceil((new Date(payment.service_end_at) - new Date($(event.target).val())) / (60 * 60 * 24 * 1000))
    $('#refund-form [name=refund_amount]').val(Math.round(payment.amount * refundTerm / paymentTerm))
    $('.fees').html((fees / 100) * $('#refund-form [name=refund_amount]').val())
})

$('#refund-form [name=refund_amount]').on('propertychange change keyup paste input', (event) => {
    if ($(event.target).val() * 1 > $(event.target).attr('max') * 1) {
        $(event.target).val($(event.target).attr('max'))
    }
    $('.fees').html((fees / 100) * $(event.target).val())
})

$('#refund-form form').submit((event) => {
    let process = $(event.target).find('[name=process]:checked').val() * 1
    let amount = $(event.target).find('[name=refund_amount]').val() * 1
    let currency = $(event.target).find('.currency').text()
    if ([1, 2].includes(process)) {
        Swal.fire({
            html: (process === 2 ? MESSAGES_LANG.refund_hold : MESSAGES_LANG.refund_approve).replace("\n", '<br>').replace(':amount', `${((100 - fees) / 100) * amount} ${currency}`),
            showCancelButton: true,
            cancelButtonText: BUTTON_LANG.cancle,
            confirmButtonText: BUTTON_LANG.save,
        }).then(function(t) {
            if (t.value) {
                storeRefund(event)
            }
        })
    } else {
        storeRefund(event)
    }
})

let storeRefund = (event) => {
    let request = new FormData(event.target)
    request.set('refund_amount', $('[name=refund_amount]').val())
    $.ajax({
        url: `//${domain.api}/payment/refund/${request.get('id')}`,
        method: 'post',
        data: request,
        contentType: false,
        processData: false,
        success: (response) => {
            if ('message' in response) {
                toastr.success(response.message)
            }
            $('#refund-form').modal('hide')
            datatable.ajax.reload()
        }
    })
}
