<!-- CCC 20220606 -->
<header class="only_pctab sub">
    <div class="base_wrap">
        <div class="header_left sub">
            <ul class="gnb">
                <?php $colcount = 3; ?>
                <li>
                    <a>@lang('form.category.label')</a>
                    <ul class="lnb lnb01">
                        <li>
                            <a href="/categoryinf?category=0">@lang('process.all')</a>
                        </li>
                        <li>
                            @for ($i = 0; $i < count($categorys)/$colcount + 1; $i++)
                                <ul class="sub_navv">
                                    @for ($lp = 0; $lp < $colcount; $lp++)
                                        @if($i*$colcount + $lp < count($categorys))
                                        <li>
                                            <a href="/categoryinf?category={{ $categorys[$i*$colcount + $lp]->id }}">{{ $categorys[$i*$colcount + $lp]->text }}</a>
                                        </li>
                                        @endif
                                    @endfor
                                </ul>
                            @endfor
                        </li>

                    </ul>
                </li>

                <li>
                    <a>@lang('main.function_item')</a>
                    <ul class="lnb lnb02">
                        <li>
                            <a href="/allfuntion">@lang('process.all')</a>
                        </li>
                        <li>
                            @for ($i = 0; $i < count($funclists)/$colcount + 1; $i++)
                                <ul class="sub_navv">
                                    @for ($lp = 0; $lp < $colcount; $lp++)
                                        @if($i*$colcount + $lp < count($funclists))
                                        <li>
                                            <a href="/funtioninf?id={{ $funclists[$i*$colcount + $lp]->id }}">{{ $funclists[$i*$colcount + $lp]->title }}</a>
                                        </li>
                                        @endif
                                    @endfor
                                </ul>
                            @endfor
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('store.mapscontent') }}">@lang('main.mapscontents')</a>
                    
                </li>
            </ul>

            <a href="/"><h1></h1></a>
        </div>


        <div class="head_top sub">

            @if(!Auth::guard('user')->check())
                @if (Session::has('byapps_id'))
                    <h5><a href="{{ route('store.register.register3.appid' , ['app_id' => Session::has('byapps_app_id')]) }}">@lang('button.login')</a></h5>
                @else
                    <h5><a href="{{ route('store.login') }}">@lang('button.login')</a></h5>
                @endif
            @else
            <div class="user_wrap_top">
                <h5 id="people_name"><a><span>{{ Auth::guard('user')->user()->company_name }}</span> @lang('store.mirs')</a></h5>

                <ul class="people_menu">
                    <li><a href="{{ route('client.dashboard') }}">@lang('button.adminpage')</a></li>
                    <li><a href="{{ route('client.my') }}">@lang('button.profile')</a></li>
                    <li><a href="{{ route('store.logout') }}">@lang('button.logout')</a></li>
                </ul>
            </div>
            @endif
            <div class="select sub" id="select_kor">
                <input type="hidden" name="" value="">
                <div class="select-inner"></div>
            </div>

        </div>
    </div>
</header>

<div id="header" class="only_m">
    <button class="menu_btn"><img src="/assets/images/store/menu.svg"></button>
    <a href="/"><h1><img src="/assets/images/store/logo.png"></h1></a>
    @if(!Auth::guard('user')->check())
    <a href="{{ route('store.login') }}"><img src="/assets/images/store/log.svg"></a>
    @else
    <div class="user_wrap_top">
        <h5 id="people_name_m"><a><img src="/assets/images/store/log.svg"></a></h5>
        <ul class="people_menu_m">
            <li><span>{{ Auth::guard('user')->user()->company_name }}</span> @lang('store.mirs')</li>
            <li><a href="{{ route('client.my') }}">@lang('button.profile')</a></li>
            <li><a href="{{ route('store.logout') }}">@lang('button.logout')</a></li>
        </ul>
    </div>
    @endif
</div>
<div class="menu_bg only_m"></div>
<div class="menu_list only_m">
    <div class="close_btn"><a href="#">
            <img src="/assets/images/store/close.svg">
        </a></div>

    <div class="menu">
        <h3>@lang('form.category.label')</h3>
        <ul>
            <li>
                <a href="/categoryinf?category=0">@lang('process.all')</a>
            </li>
            @foreach ($categorys as $code => $category)
                <li>
                    <a href="/categoryinf?id={{ $category->category }}">{{ $category->text }}</a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="menu">
        <h3>@lang('main.function_item')</h3>
        <ul>
            <li>
                <a href="/allfuntion">@lang('process.all')</a>
            </li>
            @foreach ($funclists as $code => $func)
                <li><a href="/funtioninf?id={{ $func->id }}">{{ $func->title }}</a></li>
            @endforeach
        </ul>
    </div>

    <div class="menu">
        <h3>@lang('main.mapscontents')</h3>
        <ul>
            <li>
                <a href="{{ route('store.mapscontent') }}">@lang('main.mapscontents')</a>
            </li>
        </ul>
    </div>

    <div class="kor">
        <em>@lang('form.lang.label')</em>
        <ul>
            @foreach(config('app.lang') as $key => $item)
                <li class="{{ Lang::getLocale() == $key?'active':'' }}"><a href="">@lang('lang.'.$key)</a></li>
            @endforeach
        </ul>
    </div>
</div>

@push('scripts')
<script src="/assets/js/select.js"></script>
<script>
    //메뉴 슬라이드
    $(document).ready(
        function () {
            $('.gnb > li').hover(function () {
                $('ul', this).slideDown(100);
                $(this).children('a:first').addClass("hov1");
            },
            function () {
                $('ul', this).slideUp(100);
                $(this).children('a:first').removeClass("hov1");
            });
        }
    );

    // header_store 헤더부분
    $('header').mouseenter(function () {
        $(this).addClass('wbg');
    })

    $('header').mouseleave(function () {
        if ($cond == 0) {
            $(this).removeClass('wbg');
        }
    })
    var $cond = 0;
    jQuery(document).ready(function () {
        var bodyOffset = jQuery('body').offset();
        jQuery(window).scroll(function () {
            if (jQuery(document).scrollTop() > bodyOffset.top) {
                jQuery('header').addClass('wbg');
                $cond = 1;
            } else {
                jQuery('header').removeClass('wbg');
                $cond = 0;
            }
        });

        // change language
        $(document).on('click', '.select-option-item', function() {
            let value = $(this).data('value');

            changeLang(value);
        });
    });



    //select 관련
    const lang_data = [];
    @foreach(config('app.lang_text') as $key => $item)
        lang_data.push({name:"@lang('lang.'.$key)", value: "{{ $key }}" });
    @endforeach

    var selidx = 0;
    for(let i = 0; i < lang_data.length; i++){
        if(lang_data[i].value == "{{ app()->getLocale() }}")
        {
            selidx = i;
            break;
        }
    }

    $('#select_kor').select({
        index:selidx,
        data: lang_data
    });

	</script>
	<script>
    $(document).ready(function () {

        $('.menu_btn').on('click', function () {
            $('.menu_bg').show();
            $('.menu_list').show().animate({
                left: 0
            });
        });
        $('.close_btn').on('click', function () {
            $('.menu_bg').hide();
            $('.menu_list').animate({
                left: '-' + 90 + '%'
            }, function () {
                $('.menu_list').hide();
            });
        });

    });
</script>

<script>
    $( document ).ready( function() {
        $( '#people_name' ).click( function() {
          $( '.people_menu' ).slideToggle(300);
        } );
      } );

      $( document ).ready( function() {
        $( '#people_name_m' ).click( function() {
          $( '.people_menu_m' ).slideToggle(300);
        } );
      } );
</script>
@endpush
