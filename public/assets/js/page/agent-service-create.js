let appendInput = (event) => {
    if (event.keyCode == 13) {
        let index = $('#short_description input').index(event.target) + 1
        console.log(index);
        if (index == $('[name="short_description[]"]').length && $('[name="short_description[]"]').length < 5) {
            $('#short_description').append(`<div class="col-md-10 offset-md-2 mb-1">
                <div class="input-group">
                    <span class="input-group-text">${$('[name="short_description[]"]').length + 1}</span>
                    <input class="form-control" sequence="${$('[name="short_description[]"]').length}" name="short_description[]" type="text">
                </div>
            </div>`)
            $('#short_description input').unbind()
            $('#short_description input').keydown(appendInput)
        }
        $('#short_description input').eq(index).focus()
    } else if (event.keyCode == 8)  {
        if (($(event.target).attr('sequence') * 1) + 1 == $('[name="short_description[]"]').length && event.target.value === '') {
            $(event.target).parents('.mb-1').remove()
            $('#short_description input:last').focus()
            $('#short_description input:last')[0].setSelectionRange(999999, 999999);
            event.preventDefault()
        }
    }
}

$('#short_description input').keydown(appendInput)

let contactChange = (val) => {
    $('.contact-area>div').addClass('d-none')
    $('.contact-area').find(`[data-value=${val}]`).removeClass('d-none')
}

$('#service-create-form').submit((event) => {
    let request = new FormData(event.target)
    $.each($(event.target).find('.url-input'), (i, item) => {
        request.set($(item).find('input').attr('id'), `${$(item).find('[data-type=protocol]').val()}${$(item).find('input').val()}`)
    })

    let callno = []
    $.each($('#developer_call input'), (i, item) => {
        callno.push($(item).val())
    })

    if (request.get('contact_type') == 0) {
        request.delete('developer_email')
    } else {
        request.set('developer_email', `${$('#developer_email input').val()}@${$('#developer_email select').val()}`)
        request.append('developer_call', callno.join('-'))
    }

    request.append('scope', JSON.stringify(apiScopes))

    $.ajax({
        url: `/service/${$(event.target).attr('data-id') || 'create'}${deply ? '/review' : ''}`,
        method: 'post',
        data: request,
        contentType: false,
        processData: false,
        success: (response) => {
            if ('message' in response) {
                toastr.keepMessage('success', response.message)
            }
            if ('validate' in response) {
                keepValidateError(response.validate)
            }
            if ('redirect' in response) {
                location.href = response.redirect
            }
            if ('reload' in response && response.reload) {
                location.reload()
            }
        }
    })
})

$('#payment-form').submit((event) => {
    let form = $(event.target)
    form.find('.is-invalid').removeClass('is-invalid')
    // 유효성 검사
    let payment = {
        name: form.find('[name=product_name]').val(),
        term: form.find('[name="service_term[]"]').eq(1).val(),
        term_unit: form.find('[name="service_term[]"]').eq(0).val(),
        amount: form.find('[name=amount]').eq(0).val() * 1,
        currency: form.find('[name=currency]').eq(0).val(),
        description: form.find('[name=description]').val(),
    }
    try {
        if (
            !payment.name ||
            !payment.term_unit ||
            !payment.amount || payment.amount == 0 ||
            !payment.currency ||
            !payment.description ||
            (payment.term_unit != 2 && !payment.term)
        ) {
            throw ''
        }
    } catch (e) {
        return
    } finally {
        if (!payment.name) {
            form.find('[name=product_name]').addClass('is-invalid')
        }
        if (!payment.term_unit) {
            form.find('[name="service_term[]"]').eq(0).addClass('is-invalid').selectpicker('refresh')
        }
        if (!payment.amount || payment.amount == 0) {
            form.find('[name=amount]').addClass('is-invalid')
        }
        if (!payment.currency) {
            form.find('[name=currency]').addClass('is-invalid').selectpicker('refresh')
        }
        if (payment.term_unit != 2 && !payment.term) {
            form.find('[name="service_term[]"]').eq(1).addClass('is-invalid').selectpicker('refresh')
        }
        if (!payment.description) {
            form.find('[name=description]').addClass('is-invalid')
        }
    }
    if (form.find('[name=update]').val()) {
        let id = form.find('[name=update]').val()
        let no = $(`#payment-table tbody #${id}`).index($('#payment-table tbody tr'))
        $(`#payment-table tbody #${id}`).html(`
            <input type="hidden" class="name" name="plan[${id}][name]" value="${payment.name}">
            <input type="hidden" class="term" name="plan[${id}][term]" value="${payment.term}">
            <input type="hidden" class="term_unit" name="plan[${id}][term_unit]" value="${payment.term_unit}">
            <input type="hidden" class="amount" name="plan[${id}][amount]" value="${payment.amount}">
            <input type="hidden" class="currency" name="plan[${id}][currency]" value="${payment.currency}">
            <input type="hidden" class="description" name="plan[${id}][description]" value="${payment.description}">
            <th scope="row">${no + 1}</th>
            <td>${payment.name}</td>
            <td>${payment.term || '-'} ${term[payment.term_unit] || ''}</td>
            <td>${payment.amount} ${currency[payment.currency]}</td>`)
    } else {
        let id = `new-${Math.round(Math.random() * 100000000)}`
        $('#payment-table tbody').append(`<tr id="${id}">
            <input type="hidden" class="name" name="plan[${id}][name]" value="${payment.name}">
            <input type="hidden" class="term" name="plan[${id}][term]" value="${payment.term}">
            <input type="hidden" class="term_unit" name="plan[${id}][term_unit]" value="${payment.term_unit}">
            <input type="hidden" class="amount" name="plan[${id}][amount]" value="${payment.amount}">
            <input type="hidden" class="currency" name="plan[${id}][currency]" value="${payment.currency}">
            <input type="hidden" class="description" name="plan[${id}][description]" value="${payment.description}">
            <th scope="row">${$('#payment-table').find('tbody tr').length + 1}</th>
            <td>${payment.name}</td>
            <td>${payment.term || ''} ${term[payment.term_unit]}</td>
            <td>${payment.amount} ${currency[payment.currency]}</td>
        </tr>`)
    }
    paymentTable()
    event.target.reset()
    $('#payment-create').removeClass('update')
    $('#payment-form select').change()
    $('#payment-create').modal('hide')
})

let deletePlan = () => {
    $(`#payment-table #${$('#payment-create input[name=update]').val()}`).remove()
    $('#payment-form')[0].reset()
    $('#payment-form').find('.selectpicker').change()
    $('#payment-create').removeClass('update')
    $('#payment-create').modal('hide')
    $.each($('#payment-table tbody tr'), (i, item) => {
        $(item).find('th').eq(0).html(i + 1)
    })
}

let paymentTable = () => {
    $('#payment-table tbody tr').unbind()
    $('#payment-table tbody tr').click((event) => {
        $('#payment-create').addClass('update')
        $('#payment-create').find('[name=update]').val(event.currentTarget.id)
        $('#payment-create').find('[name=product_name]').val($(event.currentTarget).find('.name').val())
        $('#payment-create').find('[name="service_term[]"]').eq(0).val($(event.currentTarget).find('.term_unit').val()).change()
        $('#payment-create').find('[name="service_term[]"]').eq(1).val($(event.currentTarget).find('.term').val()).change()
        $('#payment-create').find('[name=amount]').eq(0).val($(event.currentTarget).find('.amount').val())
        $('#payment-create').find('[name=currency]').eq(0).val($(event.currentTarget).find('.currency').val()).change()
        $('#payment-create').find('[name=description]').val($(event.currentTarget).find('.description').val())
        $('#payment-create').modal('show')
    })
}

let faqFile = {}

$('#faq-create-form').submit((event) => {
    let form = new FormData(event.target)
    form.set('question', $('#question').val())
    form.set('answer', $('#answer').val())
    $(event.target).find('.is-invalid').removeClass('is-invalid')
    try {
        if (
            !form.get('question') ||
            !form.get('answer') ||
            !form.get('category')
        ) {
            throw '';
        }
    } catch (e) {
        console.error(e)
        return
    } finally {
        if (!form.get('question')) {
            $(event.target).find('[name=question]').addClass('is-invalid')
        }
        if (!form.get('answer')) {
            $(event.target).find('[name=answer]').addClass('is-invalid')
        }
        if (!form.get('category')) {
            $(event.target).find('[name=category]').addClass('is-invalid').selectpicker('refresh')
        }
    }

    if (form.get('update')) {
        let id = form.get('update')
        if (form.get('file')) {
            faqFile[id] = form.get('file')
        }
        $(`#faq-table tbody #${id}`).html(`
            <input type="hidden" class="category" name="faq[${id}][category]" value="${form.get('category')}">
            <input type="hidden" class="question" name="faq[${id}][question]" value="${form.get('question')}">
            <input type="hidden" class="answer" name="faq[${id}][answer]" value="${form.get('answer')}">
            <td>${$('#faq-table tbody tr').length + 1}</td>
            <td>${faqCategory[form.get('category')] || '-'}</td>
            <td>${form.get('question')}</td>
            <td>${form.get('answer')}</td>`)
    } else {
        let id = `new-${Math.round(Math.random() * 100000000)}`
        if (form.get('file')) {
            faqFile[id] = form.get('file')
        }
        $('#faq-table tbody').append(`
            <tr id="${id}">
                <input type="hidden" class="category" name="faq[${id}][category]" value="${form.get('category')}">
                <input type="hidden" class="question" name="faq[${id}][question]" value="${form.get('question')}">
                <input type="hidden" class="answer" name="faq[${id}][answer]" value="${form.get('answer')}">
                <td>${$('#faq-table tbody tr').length + 1}</td>
                <td>${faqCategory[form.get('category')].text || '-'}</td>
                <td>${form.get('question')}</td>
                <td>${form.get('answer')}</td>
            </tr>`)
    }
    event.target.reset()
    $(event.target).find('.selectpicker').change()
    $('#faq-create').removeClass('update')
    $('#faq-create').modal('hide')
    faqTable()
})

let deleteFaq = () => {
    $(`#faq-table #${$('#faq-create input[name=update]').val()}`).remove()
    $('#faq-create-form')[0].reset()
    $('#faq-create-form').find('.selectpicker').change()
    $('#faq-create').removeClass('update')
    $('#faq-create').modal('hide')
    $.each($('#faq-table tbody tr'), (i, item) => {
        $(item).find('th').eq(0).html(i + 1)
    })
}

let faqTable = () => {
    $('#faq-table tbody tr').unbind()
    $('#faq-table tbody tr').click((event) => {
        $('#faq-create').addClass('update')
        $('#faq-create').find('[name=update]').val(event.currentTarget.id)
        $('#faq-create').find('[name=category]').val($(event.currentTarget).find('.category').val()).change()
        $('#faq-create').find('[name=question]').val($(event.currentTarget).find('.question').val())
        $('#faq-create').find('[name=answer]').val($(event.currentTarget).find('.answer').val())
        $('#faq-create').modal('show')
    })
}

let setScope = () => {
    $('#api-scope-table tbody').html('')
    apiScopes.forEach((item, i) => {
        $(`#scope-${item.split('.')[0]}`).prop("checked", true)
        addScope(item)
    });
}

let addScope = (item) => {
    console.log(item);
    $('#api-scope-table tbody').append(`<tr data-scope="${item.split('.')[0]}">
        <td>${$('#api-scope-table tbody tr').length + 1}</td>
        <td>${item.split('.')[0]}</td>
        <td>
            <select onchange="apiScopeChange(event)" data-scope="${item.split('.')[0]}" class="form-select" name="category[]" id="category">
                <option value="read" ${item.split('.')[1] == 'read' ? 'selected' : ''}>Read</option>
                <option value="write" ${item.split('.')[1] == 'write' ? 'selected' : ''}>Write</option>
            </select>
        </td>
        <td class="description">${__(`scope.${item}`)}</td>
    </tr>`)
}

let createPaymentForm = () => {
    $('#payment-create').modal('show').find(['name=update']).val('');
    $('#payment-create form')[0].reset()
    $('#payment-create').removeClass('update')
    $('#payment-form select').change()
    $('#payment-create').modal('hide')
}

let createFaqForm = () => {
    $('#faq-create').modal('show').find(['name=update']).val('');
    $('#faq-create form')[0].reset()
    $('#faq-create').removeClass('update')
    $('#faq-create').removeClass('update')
    $('#faq-create').modal('hide')
}

let apiScopeChange = (event) => {
    let beforeScope = `${$(event.target).attr('data-scope')}.${$(event.target).val() == 'read' ? 'write' : 'read'}`
    let scope = `${$(event.target).attr('data-scope')}.${$(event.target).val()}`
    $(event.target).parents('tr').find('.description').html(__(`scope.${scope}`))
    if ((idx = apiScopes.findIndex(value => value == beforeScope)) !== -1) {
        apiScopes.splice(idx, 1, scope)
    }
}

let apiScopeCheck = (event) => {
    let scope = $(event.target).attr('value')
    if (event.target.checked) {
        addScope(`${scope}.read`)
        apiScopes.push(`${scope}.read`)
    } else {
        if ((idx = apiScopes.findIndex(value => value.startsWith(scope))) !== -1) {
            apiScopes.splice(idx, 1)
            $(`#api-scope-table tbody tr[data-scope=${scope}]`).remove()
        }
    }
}

let refreshClientSecret = () => {
    $.ajax({
        url: `//${domain.api}/refresh/${$('#client_id').val()}`,
        method: 'post',
        success: (response) => {
            if ('secret' in response) {
                $('#api_secret').val(response.secret)
            }
            if ('message' in response) {
                toastr.success(response.message)
            }
        }
    })
}

let addVersion = () => {
    $('#version-create').modal('show')
}

paymentTable()
faqTable()
$(document).ready(setScope)
