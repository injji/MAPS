<nav class="top-app-bar navbar navbar-expand navbar-dark bg-dark">
    <div class="container-fluid px-4">

        <button class="btn btn-lg btn-icon order-1 order-lg-0" id="drawerToggle" href="javascript:void(0);"><i class="material-icons">menu</i></button>
        <a class="navbar-brand me-auto" href=""><div class="text-uppercase font-monospace">
            <img src="{{ asset('images/logo.png') }}" alt="" height="70">
        </div></a>
        <div class="d-flex align-items-center mx-3 me-lg-0">
            <div class="d-flex">
                <a class="store_gogo" href="{{ config('app.pre_url').'://'.config('app.domain.store') }}" target="_blank"><i class="material-icons">storefront</i></a>
                <div class="dropdown">
                    <button class="btn btn-lg dropdown-toggle mdc-ripple-upgraded" id="dropdownMenuProfile" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="--mdc-ripple-fg-size:28px; --mdc-ripple-fg-scale:2.78151; --mdc-ripple-fg-translate-start:17px, 15px; --mdc-ripple-fg-translate-end:10px, 10px;">
                        <img src="{{ asset('images/icons/businessman.svg') }}" alt="user-image" class="rounded-circle" style="width: 35px;">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end mt-3" aria-labelledby="dropdownMenuProfile">
                        <li>
                            <a class="dropdown-item mdc-ripple-upgraded" href="{{ url('logout') }}">
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
