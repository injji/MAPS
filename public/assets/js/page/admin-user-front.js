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
        url: '/admin/user/front.data',
        dataSrc: (response) => {
            response.data.forEach((item, i) => {
                try {
                    item.agent = item.agent ? '제휴사' : '고객사'
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
        {data: 'login_id', name: 'login_id'},
        {data: 'company', name: 'company'},
        {data: 'manager', name: 'manager', className: 'd-none d-sm-table-cell'},
        {data: 'managercall', name: 'managercall', orderable: !1},
        {data: 'manageremail', name: 'manageremail', orderable: !1, className: 'd-none d-sm-table-cell'},
        {data: 'agent', name: 'agent', orderable: !1},
        {data: 'created_at', name: 'created_at', searchable: !1, className: 'd-none d-sm-table-cell'},
    ],
    order: [[2, 'desc']],
})
