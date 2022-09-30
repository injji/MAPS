@extends('layouts.auth')

@section('title', __('page.register_client'))

@section('body')
<div class="login_wrap join_wrap">

    <div class="login join">
        <progress value="2" max="2"></progress>
        <h1>
            <img src="{{ asset('images/logo_b.png') }}">
        </h1>

        <form id="register-form">
            @csrf

            <div class="field">
                <p>@lang('form.account.label')<em>*</em></p>
                <input type="text" name="account" placeholder="@lang('form.account.placeholder')">
            </div>

            <div class="field">
                <p>@lang('form.password.label')<em>*</em></p>
                <input type="password" name="password" placeholder="@lang('form.password.label') @lang('form.password.regex')">
            </div>

            <div class="field">
                <p>@lang('form.c_password.label')<em>*</em></p>
                <input type="password" name="c_password" placeholder="@lang('form.c_password.placeholder')">
            </div>

            <div class="field">
                <p>@lang('form.company_name.label')<em>*</em></p>
                <input type="text" name="company_name" placeholder="@lang('form.company_name.placeholder')" value="{{ (isset($byapps_user)) ? $byapps_user->mem_job : '' }}">
            </div>

            <div class="field">
                <p>@lang('form.manager_name.label')<em>*</em></p>
                <input type="text" name="manager_name" placeholder="@lang('form.manager_name.placeholder')" value="{{ (isset($byapps_user)) ? $byapps_user->mem_name : '' }}">
            </div>

            <?php
                if(isset($byapps_user)){
                    $phone = explode('-',$byapps_user->phoneno);
                    $email = explode('@',$byapps_user->mem_email);
                    $email_arr = ['naver.com','daum.net','nate.com','gmail.com','hanmail.net'];
                }
            ?>
            <input type="hidden" name="byapps_id" value="{{ (isset($byapps_user)) ? $byapps_user->mem_id : '' }}">
            <div class="field">
                <p>@lang('form.manager_phone.label')<em>*</em></p>
				<div class="managercall_box">
                    <input type="hidden" name="manager_phone">
					<input type="text" value="{{ isset($phone) ? $phone[0] : '010' }}" maxlength="3" id="phoneInputA" onkeypress="return event.keyCode === 8 || event.charCode >= 48 && event.charCode <= 57">
					<em>-</em>
					<input type="text" value="{{ isset($phone) ? $phone[1] : '' }}" maxlength="4" id="phoneInputB" onkeypress="return event.keyCode === 8 || event.charCode >= 48 && event.charCode <= 57">
					<em>-</em>
					<input type="text" value="{{ isset($phone) ? $phone[2] : '' }}" maxlength="4" id="phoneInputC" onkeypress="return event.keyCode === 8 || event.charCode >= 48 && event.charCode <= 57">
				</div>
            </div>

            <div class="field">
                <p>@lang('form.manager_email.label')<em>*</em></p>

                <div class="mail_form">
                    <input type="text" name="manager_email" value="{{ isset($email) ? $email[0] : '' }}">
                    @
                    <div class="mail_select">
                        <input type="text" name="manageremail-domain" id="str_email02" style="display: none;" value="{{ isset($email) ? $email[1] : '' }}">
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
                <a href="javascript:register()">@lang('button.register_done')</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        if($("#str_email02").val()){
            $("#str_email02").show();
            $('#selectEmail').val(1).prop('selected',true);
        }
    })
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

        request.set('manager_phone', $("#phoneInputA").val() + '-' + $("#phoneInputB").val() + '-' + $("#phoneInputC").val());

        let emailEnd = '';

        if ($('#selectEmail').val() == 1)
            emailEnd = $('#str_email02').val();
        else
            emailEnd = $('#selectEmail').val() ? $('#selectEmail').val() : '';

        request.set('manager_email', request.get('manager_email')+'@'+emailEnd);

        let data = {};
        request.forEach((value, key) => (data[key] = value));

        $.ajax({
            url : "{{ route('register.client.save') }}",
            type : 'post',
            data : data,
            success : (response) => {
                if(response.code == 200){
                    var url = "{{ route('register.done', ':account') }}";
                    url = url.replace(':account', response.account);
                    location.replace(url);
                    // console.log(response);
                }
                else
                    showErr(response.error);
            }
        });
    }
</script>
@endpush
