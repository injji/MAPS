@extends('layouts.auth')

@section('title', __('page.register_byapps'))

@section('body')
<div class="login_wrap">
    <div class="login join">
        <h1>
            <img src="{{ asset('images/logo_b.png') }}">
        </h1>

        <div class="by_tit">
            <h3>@lang('page.register_byapps')</h3>
            <p>@lang('messages.register.description.byapps')</p>

        </div>

        <form id="byapps-register-form">
            <input type="hidden" name="app_id" value="{{ (isset($app_id)) ? $app_id : '' }}">
            <div class="field">
                <p>@lang('form.byapps_account.label')</p>
                <input type="text" name="account" placeholder="@lang('form.byapps_account.placeholder')" value="{{ (isset($byapps_id)) ? $byapps_id->mem_id : '' }}">
            </div>

            <div class="field">
                <p>@lang('form.byapps_password.label')</p>
                <input type="password" name="password" placeholder="@lang('form.byapps_password.placeholder')">
            </div>

            <div class="next">
                <a href="{{ route('login') }}">로그인</a>
                {{-- <a href="javascript:register()">가입 여부 확인</a> --}}
                <a href="javascript:register()">연동 가입 인증</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    function register(){
        let request = new FormData($('#byapps-register-form')[0]);
        let data = {};
        request.forEach((value, key) => (data[key] = value));
        console.log(data);
        $.ajax({
            url : "{{ route('register.byapps') }}",
            type : 'post',
            data : data,
            success : (response) => {
                if(response.code == 200){
                    alert(response.message);
                    location.href = response.redirect;
                }else if(response.code == 422){
                    alert(response.message);
                }else{
                    // showErr(response.message);
                    alert(response.message);
                }
            }
        });
    }
</script>
@endpush
