@extends('layouts.auth')

@section('title', __('page.login'))

@section('body')
<div class="login_wrap">
    <div class="login">
        <h1>
            <img src="{{ asset('images/logo_b.png') }}">
        </h1>

        <form id="login-form">
            <div class="idpw field">
                <p>@lang('form.account.label')</p>
                <input type="text" placeholder="@lang('form.account.placeholder')" name="account"
                    @if(Cookie::has('account')) value="{{ Cookie::get('account')}}" @endif>
            </div>

            <div class="idpw field">
                <p>@lang('form.password.label')</p>
                <input type="password" placeholder="@lang('form.password.placeholder')" name="password" value="">
            </div>

            <div class="idpw_find">
                <label>
                    <input type="checkbox" name="remember" @if(Cookie::has('account')) checked @endif>
                    <span></span>@lang('button.id_save')
                </label>

                <ul>
                    <li>
                        <a href="{{ route('password.reset') }}">
                            <span>@lang('button.forgot_id')</span>
                            <span>@lang('button.forgot_password')</span>
                        </a>
                    </li>
                </ul>
            </div>

            <button type="button" onclick="login()">LOGIN</button>

        </form>

        <p>@lang('messages.register.question')</p>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#joinselect">@lang('page.register')</button>
    </div>
</div>


<!-- Modal -->
<div class="modal fade join_btns" id="joinselect" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-body">
                <ul>
                    <li>
                        <h3>@lang('messages.register.option.client')</h3>
                        <p>@lang('messages.register.description.client')</p>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#consumer_btn1"  data-dismiss="modal">@lang('button.register')</button>
                    </li>

                    <li>
                        <h3>@lang('messages.register.option.agent')</h3>
                        <p>@lang('messages.register.description.agent')</p>
                        <a href="{{ route('register.agree', ['type' => 2]) }}">@lang('button.register')</a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>

<div class="modal fade join_btns" id="consumer_btn1" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-body ">
                <ul>
                    <li>
                        <h3>@lang('messages.register.option.maps')</h3>
                        <p>@lang('messages.register.description.maps')</p>
                        <a href="{{ route('register.agree', ['type' => 1]) }}">@lang('button.register')</a>
                    </li>

                    <li>
                        <h3>@lang('messages.register.option.byapps')</h3>
                        <p>@lang('messages.register.description.byapps')</p>
                        <a href="{{ route('register.register3') }}">@lang('button.register')</a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">

    $('#login-form').keypress(function (e){
        if( e.keyCode === 13 ){
            login();
        }
    });

    function login(){
        let request = new FormData($('#login-form')[0]);
        let data = {};
        request.forEach((value, key) => (data[key] = value));

        $.ajax({
            url : "{{ route('plogin') }}",
            type : 'post',
            data : data,
            success : (response) => {
                if(response.code == 200){
                    // toastr.keepMessage('success', response.message);
                    location.href = response.redirect;
                }
                else if(response.code == 402)
                    alert(response.error);
                else
                    showErr(response.error);
            }
        });
    }
</script>
@endpush
