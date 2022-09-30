@extends('layouts.auth')

@section('title', __('page.register_client'))

@section('body')
<div class="login_wrap join_wrap">

    <div class="login join reset_idpw_wrap">
        <h1>
            <img src="{{ asset('images/logo_b.png') }}">
        </h1>

        <h3 class="reset_idpw">@lang('button.forgot_id')</h3>

        <form id="find-id-form">
            <div class="field">
                <p>@lang('form.manager_phone.label')</p>
                <div class="managercall_box">                    
                    <input type="text" value="010" maxlength="3" id="phone1InputA" onkeypress="return event.keyCode === 8 || event.charCode >= 48 && event.charCode <= 57">
                    <em>-</em>
                    <input type="text" value="" maxlength="4" id="phone1InputB" onkeypress="return event.keyCode === 8 || event.charCode >= 48 && event.charCode <= 57">
                    <em>-</em>
                    <input type="text" value="" maxlength="4" id="phone1InputC" onkeypress="return event.keyCode === 8 || event.charCode >= 48 && event.charCode <= 57">
                </div>
            </div>
        </form>

        <div class="next">
            <a href="javascript:findId()">@lang('button.forgot_id')</a>
        </div>

        <h3 class="reset_idpw">@lang('button.forgot_password')</h3>
        <form id="reset-password-form">            
            <div class="field">
                <p>@lang('form.account.label')</p>
                <input type="text" name="account" placeholder="@lang('form.account.placeholder')">
            </div>

            <div class="field">
                <p>@lang('form.manager_phone.label')</p>
                <div class="managercall_box">                    
                    <input type="text" value="010" maxlength="3" id="phone2InputA" onkeypress="return event.keyCode === 8 || event.charCode >= 48 && event.charCode <= 57">
                    <em>-</em>
                    <input type="text" value="" maxlength="4" id="phone2InputB" onkeypress="return event.keyCode === 8 || event.charCode >= 48 && event.charCode <= 57">
                    <em>-</em>
                    <input type="text" value="" maxlength="4" id="phone2InputC" onkeypress="return event.keyCode === 8 || event.charCode >= 48 && event.charCode <= 57">
                </div>
            </div>

            <div class="next">
                <a href="javascript:resetPassword()">@lang('button.forgot_password')</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $("#phone1InputA").on("keyup", function(){
		if ($(this).val().length == 3)
			$("#phone1InputB").focus();
	});

	$("#phone1InputB").on("keyup", function(){
		if ($(this).val().length == 4)
            $("#phone1InputC").focus();
	});

    $("#phone2InputA").on("keyup", function(){
		if ($(this).val().length == 3)
			$("#phone2InputB").focus();
	});

	$("#phone2InputB").on("keyup", function(){
		if ($(this).val().length == 4)
            $("#phone2InputC").focus();
	});

    function findId(){
        let request = new FormData($('#find-id-form')[0]);
        request.set('phone1', $("#phone1InputA").val() + '-' + $("#phone1InputB").val() + '-' + $("#phone1InputC").val());

        let data = {};
        request.forEach((value, key) => (data[key] = value));
          
        $.ajax({
            url : "{{ route('store.login.check.account') }}",
            type : 'post',
            data : data,
            success : (response) => {
                if(response.code == 200){
                    var url = "{{ route('store.login.find.account', ':account') }}";
                    url = url.replace(':account', response.account);
                    location.href = url;
                }                    
                else if(response.code == 402)
                    alert(response.error);                
                else
                    showErr(response.error);
            }
        });
    }

    function resetPassword(){
        let request = new FormData($('#reset-password-form')[0]);
        request.set('phone2', $("#phone2InputA").val() + '-' + $("#phone2InputB").val() + '-' + $("#phone2InputC").val());

        let data = {};
        request.forEach((value, key) => (data[key] = value));
  
        $.ajax({
            url : "{{ route('store.login.reset.pw') }}",
            type : 'post',
            data : data,
            success : (response) => {                
                if(response.code == 200){
                    var url = "{{ route('store.login.find.pw', ':phone') }}";
                    url = url.replace(':phone', response.phone);
                    location.href = url;
                }                    
                else if(response.code == 402)
                    alert(response.error);                
                else
                    showErr(response.error);
            },
            error : (e) => {
                console.log(e.responseJSON);
            }
        });
    }
</script>
@endpush

<style>
    .reset_idpw_wrap form>div input {
        width: 100% !important;
    }
    .join form>div .managercall_box {
        width: calc(100% - -16px) !important;
    }
</style>