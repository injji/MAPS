@if (isset($activeMenu) && ($menu = MenuRepository::findMenu($activeMenu)))
    <div class="p-3 m-0 mb-3 pt-3 bg-white row" id="agent_nav">
        <div class="col-md-6 m-0 p-0 ">
            @include('partials.form.title', [
                'title' => $pageTitle ?? __('menu.'.($menu->getName()))
            ])
        </div>
        <div class="col-md-6 m-0 d-none d-none d-md-block d-lg-block text-right" style="line-height: 30px;">
            @foreach ($menu->getParents() as $row)
                @if ($loop->last)
                    <a href="{{ Route::has($row->getName()) ? route($row->getName()) : '' }}" class="subheading-2 text-black" display-6 style="text-decoration: none; vertical-align: middle;">
                        @lang('menu.'.$row->getName())
                    </a>
                @else
                    <a href="{{ Route::has($row->getName()) ? route($row->getName()) : '' }}" class="subheading-2 text-black" display-6 style="text-decoration: none; vertical-align: middle;">
                        @lang('menu.'.$row->getName())
                    </a>
                    <span class="material-icons text-muted" style="vertical-align: middle;opacity: .5;">chevron_right</span>
                @endif
            @endforeach
        </div>
    </div>
@endif
