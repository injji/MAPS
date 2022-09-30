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
        url: '/passport/clients.data',
        dataSrc: (response) => {
            return response.data
        }
    },
    columns: [
        {data: 'id', name: 'id', searchable: !1},
        {data: 'name', name: 'name'},
        {data: 'scope', name: 'scope', searchable: !1, orderable: !1},
    ],
    order: [[2, 'desc']],
})

let scopeView = (key) => {
    $.ajax({
        url: `/passport/client/scope/${key}`,
        success: (response) => {
            $('#scopes').val(JSON.stringify(response))
            $('#client-scope-modal').modal('show')
            setScopeInvalid()
        }
    })
}

let setScopeInvalid = () => {
    let scopes = null
    try {
        scopes = JSON.parse($('#scopes').val())
    } catch (e) {
        return
    }
    $('#client-scope-modal .table-responsive [scope]').removeClass('table-success')
    $('#client-scope-modal .table-responsive [scope]').addClass('table-danger')
    let obj = $('#client-scope-modal')
    scopes.forEach((scope, i) => {
        setScopeValidClass(obj.find(`[scope="${scope}"]`))
        if (scope.match(/.write/)) {
            setScopeValidClass(obj.find(`[scope="${scope.replace(/.write/, '.read')}"]`))
        }
    });
}

let setScopeValidClass = (scopeObj) => {
    scopeObj.addClass('table-success')
    scopeObj.removeClass('table-danger')
}

$('#client-scope-modal .table-responsive [scope]').click(() => {
    
})

$('#scopes').on('keyup change', setScopeInvalid)
