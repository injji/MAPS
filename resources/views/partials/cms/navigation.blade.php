<div id="layoutDrawer_nav">
    <nav class="drawer accordion drawer-light bg-white" id="drawerAccordion">
        <div class="drawer-menu">
            <div class="nav">
                @foreach ($menus as $name1 => $menu1)
                    <?php $active = isset($activeMenu) && count(MenuRepository::findMenu($activeMenu)->getParents()) > 0 && MenuRepository::findMenu($activeMenu)->getParents()[0]->getName() == $name1; ?>
                    @if (count($menu1['sub']) > 0)
                        <a class="nav-link{{ $active ? '' : ' collapsed' }}" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#{{ str_replace('.', '-', $name1) }}" aria-expanded="@json($active)" aria-controls="collapseDashboards">
                            <div class="nav-link-icon"><i class="material-icons">{{ $menu1['icon'] }}</i></div>
                            @lang('menu.'.$name1)
                            <div class="drawer-collapse-arrow"><i class="material-icons">expand_more</i></div>
                        </a>
                        <div class="collapse {{ $active ? 'show' : '' }}" id="{{ str_replace('.', '-', $name1) }}" aria-labelledby="headingOne" data-bs-parent="#drawerAccordion">
                            <nav class="drawer-menu-nested nav" id="drawerAccordion2">
                                @foreach ($menu1['sub'] as $name2 => $menu2)                                    
                                    @continue(!$menu2['display'])                                    
                                    <?php $active2 = isset($activeMenu) && count(MenuRepository::findMenu($activeMenu)->getParents()) > 0 && (MenuRepository::findMenu($activeMenu)->getParents()[0]->getName() == $name2 || $activeMenu == "company.client_question" || $activeMenu == "company.agent_question" || $activeMenu == "order.settle_summary" || $activeMenu == "order.settle_detail"); ?>

                                    @if (count($menu2['sub']) > 0)
                                        <a class="nav-link{{ $active2 ? '' : ' collapsed' }}" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#{{ str_replace('.', '-', $name2) }}" aria-expanded="@json($active2)" aria-controls="collapseDashboards">
                                            @lang('menu.'.$name2)
                                            <div class="drawer-collapse-arrow"><i class="material-icons">expand_more</i></div>
                                        </a>
                                        <div class="collapse {{ $active2 ? 'show' : '' }}" id="{{ str_replace('.', '-', $name2) }}" aria-labelledby="headingOne" data-bs-parent="#drawerAccordion2">
                                            <nav class="drawer-menu-nested nav">
                                                @foreach ($menu2['sub'] as $name2 => $menu3)                                                    
                                                    @continue(!$menu3['display'])
                                                    <a class="nav-link {{ ($activeMenu ?? '') == $name2 ? 'active' : '' }}" href="{{ $menu3['permission'] ? Route::has($name2) ? route($name2) : 'javascript:void(0)' : 'javascript:alert(\'관리자에게 권한 요청해 주세요.\')' }}">@lang('menu.'.$name2)</a>
                                                @endforeach
                                            </nav>
                                        </div>
                                    @else
                                        <a class="nav-link {{ ($activeMenu ?? '') == $name2 ? 'active' : '' }}" href="{{ $menu2['permission'] ? Route::has($name2) ? route($name2) : 'javascript:void(0)' : 'javascript:alert(\'관리자에게 권한 요청해 주세요.\')' }}">@lang('menu.'.$name2)</a>
                                    @endif

                                @endforeach
                            </nav>
                        </div>
                    @else                        
                        <a class="nav-link" href="{{ $menu1['permission'] ? Route::has($name1) ? route($name1) : 'javascript:void(0)' : 'javascript:alert(\'관리자에게 권한 요청해 주세요.\')' }}" aria-expanded="@json($active)">
                            <div class="nav-link-icon"><i class="material-icons">{{ $menu1['icon'] }}</i></div>
                            @lang('menu.'.$name1)
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </nav>
</div>
