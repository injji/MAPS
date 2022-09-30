@extends('layouts.auth')

@section('title', __('page.register_agent'))

@section('body')
<div class="login_wrap join_wrap">

    <div class="login join">
        <progress value="2" max="3"></progress>
        <h1>
            <img src="{{ asset('images/logo_b.png') }}">
        </h1>

        <h3 class="join_company_tit">@lang('page.register_agent')</h3>

        <form id="register-form">
            <div class="field">
                <p>@lang('form.account.label')<em>*</em></p>
                <input type="text" name="account" placeholder="@lang('form.account.placeholder')">
            </div>

            <div class="field">
                <p>@lang('form.password.label')<em>*</em></p>
                <input type="password" name="password" placeholder="@lang('form.password.placeholder')">
            </div>

            <div class="field">
                <p>@lang('form.c_password.label')<em>*</em></p>
                <input type="password" name="c_password" placeholder="@lang('form.password.label') @lang('form.password.regex')">
            </div>

            <div class="field">
                <p>@lang('form.company_name.label')<em>*</em></p>
                <input type="text" name="company_name" placeholder="@lang('form.company_name.placeholder')">
            </div>

            <div class="field">
                <p>@lang('form.business_no.label')<em>*</em></p>
                <input type="text" name="business_no" placeholder="@lang('form.business_no.placeholder')">
            </div>

            <div class="field">
                <p>@lang('form.director_name.label')<em>*</em></p>
                <input type="text" name="director_name" placeholder="@lang('form.director_name.placeholder')">
            </div>

            <div class="field">
                <p>@lang('form.address.label')<em>*</em></p>
                <input type="text" name="address" placeholder="@lang('form.address.placeholder')">
            </div>

            <div class="field">
                <p>@lang('form.homepage_url.label')<em>*</em></p>
                <input type="text" name="homepage_url" placeholder="@lang('form.homepage_url.placeholder')">
            </div>

			<div class="field">
                <p>@lang('form.director_phone.label')<em>*</em></p>
                
				<div class="managercall_box">
                    <input type="hidden" name="director_phone">
					<input type="text" value="010" maxlength="3" id="phoneInputA" onkeypress="return event.keyCode === 8 || event.charCode >= 48 && event.charCode <= 57">
					<em>-</em>
					<input type="text" value="" maxlength="4" id="phoneInputB" onkeypress="return event.keyCode === 8 || event.charCode >= 48 && event.charCode <= 57">
					<em>-</em>
					<input type="text" value="" maxlength="4" id="phoneInputC" onkeypress="return event.keyCode === 8 || event.charCode >= 48 && event.charCode <= 57">
				</div>
            </div>

            <div class="field">
                <p>@lang('form.manager_email.label')<em>*</em></p>

                <div class="mail_form">
                    <input type="text" name="director_email" value="">
                    @
                    <div class="mail_select">
                        <input type="text" name="manageremail-domain" id="str_email02" style="display: none;">
                        <select name="manageremail-domain-select" class="selectpicker" id="selectEmail">
                            <option value="" selected>이메일 선택</option>
                            <option class="option" value="naver.com">naver.com</option>
                            <option class="option" value="daum.net">daum.net</option>
                            <option class="option" value="nate.com">nate.com</option>
                            <option class="option" value="gmail.com">gmail.com</option>
                            <option class="option" value="hanmail.net">hanmail.net</option>
                            <option class="option" value="1">직접입력</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="next">
                <a href="{{ route('login') }}">@lang('button.login')</a>
                <a href="javascript:register()">@lang('button.next')</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $('#selectEmail').change(function(){        
        if ($(this).val() == '1'){ //직접입력일 경우
            $("#str_email02").val(''); //값 초기화                
            $("#str_email02").show();
        }
        else //직접입력이 아닐경우
            $("#str_email02").hide();
    });

	$("#phoneInputA").on("keyup", function(){
		if ($(this).val().length == 3)
			$("#phoneInputB").focus();
	});

	$("#phoneInputB").on("keyup", function(){
		if ($(this).val().length == 4)
            $("#phoneInputC").focus();
	});
    
    function register(){
        let request = new FormData($('#register-form')[0]);

        request.set('director_phone', $("#phoneInputA").val() + '-' + $("#phoneInputB").val() + '-' + $("#phoneInputC").val());

        let emailEnd = '';
        
        if ($('#selectEmail').val() == 1)
            emailEnd = $('#str_email02').val();
        else
            emailEnd = $('#selectEmail').val() ? $('#selectEmail').val() : '';
        
        request.set('director_email', request.get('director_email')+'@'+emailEnd);

        let data = {};
        request.forEach((value, key) => (data[key] = value));
  
        $.ajax({
            url : "{{ route('register.agent.save') }}",
            type : 'post',
            data : data,            
            success : (response) => {                
                if(response.code == 200){
                    var url = "{{ route('register.done', ':account') }}";
                    url = url.replace(':account', response.account);                    
                    location.replace(url);
                }                    
                else                    
                    showErr(response.error);                
            }
        });
    }
</script>
@endpush
