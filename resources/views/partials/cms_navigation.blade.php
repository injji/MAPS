<div id="layoutDrawer">
    @include('partials.cms.navigation', [
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
