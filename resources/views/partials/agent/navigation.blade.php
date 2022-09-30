<div id="layoutDrawer_nav">
    <nav class="drawer accordion drawer-light bg-white" id="drawerAccordion">
        <div class="drawer-menu">
            <div class="nav">
                @foreach ($menus as $name1 => $menu1)
                    <?php $active = isset($activeMenu) && count(MenuRepository::findMenu($activeMenu)->getParents()) > 0 && MenuRepository::findMenu($activeMenu)->getParents()[0]->getName() == $name1; ?>
                    @if (count($menu1['sub']) > 0)                        
                        <a class="nav-link" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#{{ str_replace('.', '-', $name1) }}" aria-expanded="@json($active)" aria-controls="collapseDashboards">
                            <div class="nav-link-icon"><i class="material-icons">{{ $menu1['icon'] }}</i></div>
                            @lang('menu.'.$name1)
                            <div class="drawer-collapse-arrow"><i class="material-icons">expand_more</i></div>
                        </a>
                        <div class="collapse {{ $active ? 'show' : '' }}" id="{{ str_replace('.', '-', $name1) }}" aria-labelledby="headingOne" data-bs-parent="#drawerAccordion">
                            <nav class="drawer-menu-nested nav">
                                @foreach ($menu1['sub'] as $name2 => $menu2)                                
                                    @continue(!$menu2['display'])
                                    <a class="nav-link {{ ($activeMenu ?? '') == $name2 ? 'active' : '' }}" href="{{ Route::has($name2) ? route($name2) : 'javascript:void(0)' }}">@lang('menu.'.$name2)</a>
                                @endforeach
                            </nav>
                        </div>
                    @else              
                        <a class="nav-link" target="{{ $menu1['target'] }}" href="{{ Route::has($name1) ? route($name1) : 'javascript:void(0)' }}" aria-expanded="@json($active)">
                            <div class="nav-link-icon"><i class="material-icons">{{ $menu1['icon'] }}</i></div>
                            @lang('menu.'.$name1)
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </nav>
</div>
