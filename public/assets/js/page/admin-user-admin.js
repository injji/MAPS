let datatable = $("#datatable").DataTable({
    scrollY: "400px",
    scrollX: !0,
    scrollCollapse: !0,
    pageLength: 50,
    fixedColumns: {
        leftColumns: 1,
        rightColumns: 1
    },

    serverSide: true,
    ajax: {
        url: '/admin/user/admin.data',
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
        {data: 'name', name: 'name'},
        {data: 'level', name: 'level', searchable: !1},
        {data: 'created_at', name: 'created_at', searchable: !1, className: 'd-none d-sm-table-cell'},
        {data: 'action', name: 'action', searchable: !1, orderable: !1},
    ],
    order: [[2, 'desc']],
})

let blockUser = (object) => {
    $('#user-block-modal #user-name').html($(object).attr('user-name'))
    $('#user-block-modal').attr('user-id', $(object).attr('user-id'))
    $('#user-block-modal').modal('show')
}
let blockUserSend = () => {
    $.ajax({
        url: '/admin/user/admin/block',
        method: 'POST',
        data: {
            id: [$('#user-block-modal').attr('user-id')]
        },
        success: (response) => {
            toastr.success(response.message)
            datatable.ajax.reload()
            $('#user-block-modal').modal('hide')
        },
        error: (response) => {
        }
    })
}
let approveUser = (object) => {
    $('#user-approve-modal #user-name').html($(object).attr('user-name'))
    $('#user-approve-modal').attr('user-id', $(object).attr('user-id'))
    $('#user-approve-modal').modal('show')
}
let approveUserSend = () => {
    $.ajax({
        url: '/admin/user/admin/approve',
        method: 'POST',
        data: {
            id: [$('#user-approve-modal').attr('user-id')]
        },
        success: (response) => {
            toastr.success(response.message)
            datatable.ajax.reload()
            $('#user-approve-modal').modal('hide')
        },
        error: (response) => {
            toastr.error(response.responseJSON.message)
        }
    })
}
let editLevelUser = (object) => {
    $('#user-level-modal #user-name').html($(object).attr('user-name'))
    $('#user-level-modal').attr('user-id', $(object).attr('user-id'))
    $('#user-level-modal').find('[name=level]').val($(object).attr('user-level')).change()
    $('#user-level-modal').modal('show')
}
$('#level').on('keyup change', (event) => {
    $.each($('#menu-level-table tbody tr'), (i, object) => {
        $(object).attr('class', ((event.target.value * 1) >= ($(object).attr('level') * 1)) ? 'table-success' : 'table-danger')
    })
})
let editLevelUserSend = () => {
    $.ajax({
        url: '/admin/user/admin/approve',
        method: 'POST',
        data: {
            id: [$('#user-level-modal').attr('user-id')],
            level: $('#user-level-modal #level').val(),
        },
        success: (response) => {
            toastr.success(response.message)
            datatable.ajax.reload()
            $('#user-level-modal').modal('hide')
        },
        error: (response) => {
            toastr.error(response.responseJSON.message)
        }
    })
}
