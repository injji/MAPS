$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

let showErr = (arr) => {
    $('.invalid-message').remove();
    $('.is-invalid').removeClass('is-invalid');
    for (var field in arr) {
        let message = arr[field].join('<br>');
        let isMailForm = $(`[name=${field}]`).parent('.mail_form').addClass('is-invalid').length > 0;
        let isPhoneForm = $(`[name=${field}]`).parent('.managercall_box').addClass('is-invalid').length > 0;
        
        if (!isMailForm && !isPhoneForm)
            $(`[name=${field}]`).addClass('is-invalid');
                    
        $(`[name=${field}]`)
            .change((event) => {
                let name = $(event.target).attr('name');
                $(event.target).parents('.mail_form').removeClass('is-invalid');
                $(event.target).parents('.managercall_box').removeClass('is-invalid');
                $(event.target).removeClass('is-invalid').parents('div.field').parent().find(`[data-field=${name}]`).remove().off('change');
            })
            .parents('div.field')
            .after(`<p class="invalid-message" data-field="${field}">${message}</p>`);
    }
};

// toastr 커스텀
toastr.options = {positionClass: 'toast-top-full-width'};
// toastr 다음 페이지에서 받기
toastr.keepMessage = (type, ...option) => {
    let message = [];

    if (Cookie.get('keep_toastr'))
        message = JSON.parse(Cookie.get('keep_toastr'));
    
    message.push({type: type, option: option});    
    Cookie.set('keep_toastr', JSON.stringify(message));    
}
((keep) => {    
    if (!keep) return;

    let messages = JSON.parse(keep);
        
    messages.forEach((message, i) => {
        toastr[message.type](...message['option']);
    });
})(Cookie.pull('keep_toastr'));

let changeLang = (lang) => {
    $.ajax({
        url: '/lang/change',
        type : 'get',
        data : {lang: lang},
        success : (response) => {
            if(response.code == 200){
                toastr.keepMessage('success', response.message);
                location.reload();
            }
        }
    });
};

let copyText = (text) => {
    var tempElem = document.createElement('textarea');
    tempElem.value = text;
    document.body.appendChild(tempElem);

    tempElem.select();
    document.execCommand("copy");
    document.body.removeChild(tempElem);
    toastr.success('copyed');
};

let numberWithCommas = (num) => {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

let getCorrectDateTime = (str_date) => {
    let now_date = new Date(str_date);
    let year = now_date.getFullYear();
    let month = now_date.getMonth() + 1;
    let day = now_date.getDate();
    let hour = now_date.getHours();
    let minute = now_date.getMinutes();
    let second = now_date.getSeconds();
    let t_month = month < 10 ? '0'+month : month;
    let t_day = day < 10 ? '0'+day : day;
    let t_hour = hour < 10 ? '0'+hour : hour;
    let t_minute = minute < 10 ? '0'+minute : minute;
    let t_second = second < 10 ? '0'+second : second;
    let date_time = year+"-"+t_month+"-"+t_day+" "+t_hour+":"+t_minute+":"+t_second;
    
    return date_time;
};