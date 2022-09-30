@extends('layouts.default')

@section('body')
<nav class="top-app-bar navbar navbar-expand navbar-dark bg-dark">
    <div class="container-fluid px-4">

        <button class="btn btn-lg btn-icon order-1 order-lg-0" id="drawerToggle" href="javascript:void(0);"><i class="material-icons">menu</i></button>
        <a class="navbar-brand me-auto" href="/"><div class="text-uppercase font-monospace">
            <img src="{{ asset('images/logo_w.png') }}" alt="" height="38">
        </div></a>
        <div class="d-flex align-items-center mx-3 me-lg-0">
            <div class="d-flex">
                <div class="help_btn">
                    <button id="help_btn">HELP</button>
                    <ul class="help_list">
                        <li><a href="{{ route('client.guide') }}">스크립트 설치 가이드</a></li>
                        <li><a href="">관리자 매뉴얼</a></li>
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
                    <button class="btn btn-lg dropdown-toggle mdc-ripple-upgraded" id="dropdownMenuProfile" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="--mdc-ripple-fg-size:28px; --mdc-ripple-fg-scale:2.78151; --mdc-ripple-fg-translate-start:17px, 15px; --mdc-ripple-fg-translate-end:10px, 10px;"><i class="material-icons">person</i><!-- <span style="font-size:14px;">{{ Auth::user()->company }}</span> --></button>
                    <ul class="dropdown-menu dropdown-menu-end mt-3" aria-labelledby="dropdownMenuProfile">
                        <li>
                            <a class="dropdown-item mdc-ripple-upgraded" href="{{ route('client.my') }}" >
                                <i class="material-icons leading-icon">person</i>
                                <div class="me-3">@lang('button.profile')</div>
                            </a>
                        </li>
						<li>
                            <a class="dropdown-item mdc-ripple-upgraded" href="{{ route('client.site.set') }}" >
                                <i class="material-icons leading-icon">settings</i>
                                <div class="me-3">@lang('button.site_set')</div>
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
    @include('partials.client.navigation', [
        'menus' => config('menu')
    ])
    <div id="layoutDrawer_content">
        <main class="h-100">
            <div class="col-md-12 bg-light h-100">
                <div>
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var selSiteId = 0;

    function createSite(first = false){

        //20220810 HJY
        //모달 위에 모달을 띄울 때 에러발생으로 인해
        //모달을 띄울때 기존 모달이 있으면 제거 후 띄운다
        $('.modal-backdrop').remove();
        $('.modal').hide();

        if ($('#site-create-popup').length) {
            $('#site-create-popup').modal('show')
            return;
        }

        let request = {}
        if (first)
            request.first = true;

        $.ajax({
            url: '/partials/create/site',
            cache: true,
            data: request,
            success: (response) => {
                $('body').append(response);
                $('#site-create-popup').modal('show');
            }
        })
    }

    function editSite(row){
        selSiteId = row.id;

        if ($('#site-edit-popup').length) {
            $("#site_name").val(row.name);
            $("#site_url").val(row.url);
            $("#site_type").val(row.type);
            if (row.type > 0) {
                $("#hostname").addClass('d-none');
            } else {
                $("#site_hostname").val(row.hostname);
            }
            $('#site-edit-popup').modal('show');
            return;
        }

        let request = {};

        $.ajax({
            url: '/partials/edit/site',
            cache: true,
            data: request,
            success: (response) => {
                $('body').append(response);
                $("#site_name").val(row.name);
                $("#site_url").val(row.url);
                $("#site_type").val(row.type);
                if (row.type > 0) {
                    $("#hostname").addClass('d-none');
                } else {
                    $("#site_hostname").val(row.hostname);
                }
                $('#site-edit-popup').modal('show');
            }
        })
    }

    function createSiteSend(){
        let request = new FormData($('#site-create-form')[0])
        if (request.get('site[url]'))
            request.set('site[url]', request.get('site[url]'))

        request.set('site_name', request.get('site[name]'));
        request.set('site_url', request.get('site[url]'));
        request.set('site_type', request.get('site[type]'));
        request.set('site_hostname', request.get('site[hostname]'));

        let data = {};
        request.forEach((value, key) => (data[key] = value));

        $.ajax({
            url: '/site/create',
            type : 'post',
            data : data,
            success : (response) => {
                if(response.code == 200){
                    $('#site-create-popup').on('hidden.bs.modal', (e) => {
                        $(e.target).remove()
                    })
                    $('#site-create-popup').modal('hide')

                    siteReload();

                    if (response.redirect){
                        toastr.keepMessage('success', response.message);
                        location.href = "{{ route('client.dashboard') }}" + "/" +response.site.id;
                    }
                    else {
                        toastr.success(response.message);
                        location.href = document.URL;
                    }
                }
                else{
                    for (var field in response.error){
                        if(field == 'site_name'){
                            if(response.error[field] == "{{ __('validation.required')}}")
                                alert("{{ __('form.site.name.placeholder')}}");
                            else
                                alert(response.error[field]);

                            return;
                        }
                        else if(field == 'site_url'){
                            if(response.error[field] == "{{ __('validation.required')}}")
                                alert("{{ __('form.site.url.placeholder')}}");
                            else
                                alert(response.error[field]);

                            return;
                        }
                    }
                }
            }
        });
    }

    function updateSiteSend(){
        let request = new FormData($('#site-edit-form')[0])

        request.set('id', selSiteId);

        let data = {};
        request.forEach((value, key) => (data[key] = value));

        $.ajax({
            url: '/site/store',
            type : 'post',
            data : data,
        success : (response) => {
                if(response.code == 200){
                    $('#site-edit-popup').on('hidden.bs.modal', (e) => {
                        $(e.target).remove()
                    })
                    $('#site-edit-popup').modal('hide')

                    siteReload();

                    if (response.redirect){
                        toastr.keepMessage('success', response.message);
                        location.href = "{{ route('client.dashboard') }}" + "/" +response.site.id;
                    }
                    else {
                        toastr.success(response.message);
                        location.href = document.URL;
                    }
                }
                else{
                    for (var field in response.error){
                        if(field == 'site_name'){
                            if(response.error[field] == "{{ __('validation.required')}}")
                                alert("{{ __('form.site.name.placeholder')}}");
                            else
                                alert(response.error[field]);

                            return;
                        }
                        else if(field == 'site_url'){
                            if(response.error[field] == "{{ __('validation.required')}}")
                                alert("{{ __('form.site.url.placeholder')}}");
                            else
                                alert(response.error[field]);

                            return;
                        }
                    }
                }
            }
        });
    }

    function siteReload(){
        $.ajax({
            url: '/partials/create/select',
            data: {site: getSiteId()},
            success: (response) => {
                $('#site-select').html(response);
            }
        });
    }

    function changeSite(id){
        location.href = `/${id}`;
    }

    function getSiteId(){
        return location.pathname.split('/')[1];
    }

</script>

<script>
    $('#help_btn').click(function(){
        $('.help_list').slideToggle();
    })
</script>
@endpush
