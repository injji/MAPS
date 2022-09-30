@extends('layouts.auth')

@section('title', __('page.register_done'))

@section('body')
<div class="login_wrap ">

    <div class="login join">
        <h1>
            <img src="{{ asset('images/logo_b.png') }}">
        </h1>

        <div class="welcome">
            <p>@lang('messages.register_welcome')</p>

            <div class="user_id_end">
                <span>@lang('form.account.label')</span>
                <h3>{{ $account }}</h3>
            </div>
        </div>


        <a class="login_go" href="{{ route('login') }}">@lang('button.login')</a>

    </div>
</div>
@endsection