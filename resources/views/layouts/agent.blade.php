@extends('layouts.default')

@section('body')

<nav class="top-app-bar navbar navbar-expand navbar-dark bg-secondary">
    <div class="container-fluid pl-4">

        <button class="btn btn-lg btn-icon order-1 order-lg-0" id="drawerToggle" href="javascript:void(0);"><i class="material-icons">menu</i></button>
        <a class="navbar-brand me-auto" href="/">
            <div class="text-uppercase font-monospace">
                <img src="{{ asset('images/logo_w.png') }}" alt="" height="38">
            </div>
        </a>
        <div class="d-flex align-items-center me-lg-0">
            <div class="d-flex">
                <div class="help_btn">
                    <button id="help_btn">HELP</button>
                    <ul class="help_list">
                        <li><a href="{{ config('app.pre_url').'://'.config('app.domain.api') }}/documentation" target="_blank">API가이드</a></li>
                        <li><a href="">관리자 매뉴얼</a></li>
                        <li><a href="{{ route('agent.inquiry_agent') }}">1:1문의</a></li>
                    </ul>
                </div>

                <div class="dropdown">
                    <button class="btn btn-lg dropdown-toggle mdc-ripple-upgraded" id="dropdownMenuProfile" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="--mdc-ripple-fg-size:28px; --mdc-ripple-fg-scale:2.78151; --mdc-ripple-fg-translate-start:17px, 15px; --mdc-ripple-fg-translate-end:10px, 10px;"><i class="material-icons">language</i><span style="font-size:14px;"></span></button>
                    <ul class="dropdown-menu dropdown-menu-end mt-3" aria-labelledby="dropdownMenuProfile">
                        @foreach (config('app.lang') as $lang => $key)
                            <li>
                                <a class="dropdown-item mdc-ripple-upgraded" href="javascript:changeLang('{{ $lang }}')">
                                    <div class="me-3">{{ config('app.lang_text.'.$lang) }}</div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="dropdown">
                    <button class="btn btn-lg dropdown-toggle mdc-ripple-upgraded" id="dropdownMenuProfile" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="--mdc-ripple-fg-size:28px; --mdc-ripple-fg-scale:2.78151; --mdc-ripple-fg-translate-start:17px, 15px; --mdc-ripple-fg-translate-end:10px, 10px;"><i class="material-icons">person</i></button>
                    <ul class="dropdown-menu dropdown-menu-end mt-3" aria-labelledby="dropdownMenuProfile">
                        <li>
                            <span class="dropdown-item mdc-ripple-upgraded">
                                {{ Auth::user()->company_name }}({{ Auth::user()->account }})
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item mdc-ripple-upgraded" href="{{ route('agent.my') }}">
                                <i class="material-icons leading-icon">person</i>
                                <div class="me-3">@lang('button.profile')</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item mdc-ripple-upgraded" href="{{ route('logout') }}">
                                <i class="material-icons leading-icon">logout</i>
                                <div class="me-3">@lang('button.logout')</div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<div id="layoutDrawer">
    @include('partials.agent.navigation', [
        'menus' => config('menu')
    ])
    <div id="layoutDrawer_content">
        <main class="h-100">
            <div class="col-md-12 bg-light h-100">
                <div class="p-md-5">
                    @include('partials.breadcrumb')
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $('#help_btn').click(function(){
        $('.help_list').slideToggle();
    })
</script>

@endpush
