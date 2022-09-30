$('#form-tab-menu a').click(function (e) {
    e.preventDefault();
    $(this).tab('show');
})

$('#payment-table').toggleClass('d-none', $('#payment-table tbody tr').length == 0)
$('#faq-table').toggleClass('d-none', $('#faq-table tbody tr').length == 0)

let contactChange = (val) => {
    $('.contact-area>div').addClass('d-none')
    $('.contact-area').find(`[data-value=${val}]`).removeClass('d-none')
}

let setScope = () => {
    $('#api-scope-table tbody').html('')
    apiScopes.forEach((item, i) => {
        $(`#scope-${item.split('.')[0]}`).prop("checked", true)
        addScope(item)
    });
}

let addScope = (item) => {
    $('#api-scope-table tbody').append(`<tr data-scope="${item.split('.')[0]}">
        <td>${$('#api-scope-table tbody tr').length + 1}</td>
        <td>${item.split('.')[0]}</td>
        <td>${item.split('.')[1]}</option>
        </select></td>
        <td>API SCOPE 설명하는 내용</td>
    </tr>`)
    $('.selectpicker').selectpicker()
}

$(document).ready(setScope)
