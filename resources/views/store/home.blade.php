@extends('layouts.auth')

@section('body')

@include('layouts.header')

<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<div class="swiper mySwiper">
    <div class="swiper-wrapper only_pc">
        @foreach ($topBanner as $banner)
        <?php $banner = get_object_vars($banner); ?>
            <div class="swiper-slide"><a target="_blank" href="{{ $banner['url'] }}"><img src="{{ Storage::url($banner[App::getLocale()]) }}"></a></div>
        @endforeach
    </div>
    <div class="swiper-pagination"></div>
</div>
<div class="swiper mySwiper">
    <div class="swiper-wrapper only_tab">
        @foreach ($topBanner as $banner)
        <?php $banner = get_object_vars($banner); ?>
            <div class="swiper-slide"><a target="_blank" href="{{ $banner['url'] }}"><img src="{{ Storage::url($banner[App::getLocale()]) }}"></a></div>
        @endforeach
    </div>
    <div class="swiper-pagination"></div>
</div>

<div class="swiper mySwiperm">
    <div class="swiper-wrapper only_m">
        @foreach ($topBanner as $banner)
        <?php $banner = get_object_vars($banner); ?>
            <div class="swiper-slide"><a target="_blank" href="{{ $banner['url'] }}"><img src="{{ Storage::url($banner[App::getLocale()]) }}"></a></div>
        @endforeach
    </div>
    <div class="swiper-pagination"></div>
</div>


<script>
        var swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            speed: 1000,
            pagination: {
                el: ".swiper-pagination",
            },
        });

        var swiper = new Swiper(".mySwiperm", {
            loop: true,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            speed: 1000,
            pagination: {
                el: ".swiper-pagination",
                type: "fraction",
            },
        });
</script>

<div class="search_wrap base_wrap">
    <h3>@lang('main.search_service')</h3>

    <div class="only_pctab">
        <form class="fsearch_pc" action="/search" onsubmit="searchType(this)">
            <div class="search_div">
                <ul class="search_btn">
                    <li><a href="#tab1"></a></li>
                    <li><a href="#tab2"></a></li>
                </ul>
                <div class="search_content">
                    <div id="tab1">
                        <input type="search" name="keyword" placeholder="@lang('messages.search_find')" onkeypress="if( event.keyCode == 13 ){return;}">
                    </div>

                    <div id="tab2">
                        @include('layouts.pay')
                    </div>
                </div>
            </div>
            <button type="submit">
                <img src="/assets/images/store/search_btn.svg">
            </button>
        </form>
    </div>

    <div class="only_m">
        <div class="search_div">
            <ul class="search_btn">
                <li><a href="#tab1"></a></li>
                <li><a href="#tab2"></a></li>
            </ul>

			<form class="fsearch_m" action="/search" onsubmit="searchType(this)">
				<div class="sear_border">
					<div class="search_content">
						<div id="tab1">
							<input type="search" name="keyword" placeholder="@lang('messages.search_find')" onkeypress="if( event.keyCode == 13 ){return;}">
						</div>
						<div id="tab2">
							@include('layouts.pay')
						</div>
					</div>
					<button type="submit" >
						<img src="/assets/images/store/search_b.svg">
					</button>
				</div>
			</form>
        </div>

    </div>

</div>


<div class="item_wrap base_wrap" id="function">
    <div class="item_tit">
        <h3>@lang('main.function_item')</h3>
        <em><a href="/allfuntion">@lang('button.moremore')</a></em>
    </div>

    <div class="item_list item_list_event">
        @foreach ($funcKind as $service)
            @include('partials.store.func_kind', compact('service'))
        @endforeach
    </div>
</div>

<div class="item_wrap base_wrap">
    <div class="item_tit">
        <h3>@lang('main.best')</h3>
    </div>

    <!-- <div class="service_wrap css_tab"> -->
    <div class="item_list css_m">
        @foreach ($best as $service)
            @include('partials.store.best', compact('service'))
        @endforeach
    </div>
</div>

<!-- mid banner -->
<div class="mid_banner base_wrap">
    <div class="swiper mySwiper2">
        <div class="swiper-wrapper only_pctab">
            @foreach ($midBanner as $banner)
            <?php $banner = get_object_vars($banner); ?>
                <div class="swiper-slide">
                <a target="_blank" href="{{ $banner['url'] }}"><img src="{{ Storage::url($banner[App::getLocale()]) }}"></a>
                </div>
            @endforeach
        </div>
        <div class="swiper-pagination"></div>
    </div>

    <div class="swiper mySwiperm2">
        <div class="swiper-wrapper only_m">
            @foreach ($midBanner as $banner)
            <?php $banner = get_object_vars($banner); ?>
                <div class="swiper-slide">
                <a target="_blank" href="{{ $banner['url'] }}"><img src="{{ Storage::url($banner[App::getLocale()]) }}"></a>
                </div>
            @endforeach
        </div>
        <div class="swiper-pagination"></div>
    </div>
</div>
<script>
    var swiper = new Swiper(".mySwiper2", {
        loop: true,
        autoplay: {
            delay: 2000,
            disableOnInteraction: false,
        },
        speed: 1000,
        pagination: {
            el: ".swiper-pagination",
        },
    });

    var swiper = new Swiper(".mySwiperm2", {
        loop: true,
        autoplay: false,
        speed: 1000,
        pagination: {
            el: ".swiper-pagination",
            type: "fraction",
        },
    });
</script>
<!-- 새로운 서비스를 만나보세요. -->
<div class="service new_service">
    <div class="base_wrap">
        <h3>@lang('main.new_service')</h3>

        <div>
            <ul class="tabnav2 position-relative">
                <li
                    data-num="1"
                    class="tab wave2">
                    <a href="#tab-1" active>
                        @lang('process.all')
                    </a>
                </li>
                 
                <li>
                    <ul>
                @foreach ($newSVCcatagorys as $code => $services)
                    <li
                     data-num="{{ $loop->iteration + 1 }}"
                     class="tab wave2">
                        {{-- <a href="#tab-{{ $services->id }}"> --}}
                        <a href="#tab-{{ $loop->iteration + 1 }}">
                            {{$services->text}}
                        </a>
                    </li>
                @endforeach
                    </ul>
                </li>

                <div class="indicator2" ></div>
            </ul>
        </div>


        <div class="tabcontent">
            @foreach ($newsvcs as $code => $newctgSVCS)
                {{-- <div id="tab-{{$code}}" class="service_wrap"> --}}
                <div id="tab-{{$loop->iteration}}" class="service_wrap">
                    @foreach ($newctgSVCS as $service)
                        @include('partials.store.service_banner', compact('service'))
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- 만족율 200% -->
<div class="item_wrap base_wrap per200" id="satisfy">
    <div class="item_tit">
        <h3>@lang('main.percent')</h3>
        <em><a href="/satisfy">@lang('button.moremore')</a></em>
    </div>

    <div class="item_list">
        @foreach ($review as $service)
        @include('partials.store.service', compact('service'))
        @endforeach
    </div>
</div>

<!-- 필수 마테크 -->
<div class="item_wrap base_wrap per200" id="matech">
    <div class="item_tit">
        <h3>@lang('main.matech')</h3>
        <em><a href="/matech">@lang('button.moremore')</a></em>
    </div>

    <div class="service_wrap css_tab">
        @foreach ($adminSort as $service)
        @include('partials.store.service_banner', compact('service'))
        @endforeach
    </div>
</div>


{{-- <div class="bottom_banner only_pc">
    <?php $banner = get_object_vars($botBanner); ?>
    <a target="_blank" href="{{ $banner['url'] }}"><img src="{{ Storage::url($banner[App::getLocale()]) }}"></a>
</div>

<div class="bottom_banner only_tab">
    <?php $banner = get_object_vars($botBanner); ?>
    <a target="_blank" href="{{ $banner['url'] }}"><img src="{{ Storage::url($banner[App::getLocale()]) }}"></a>
</div> --}}

<div class="bottom_banner only_m">
    <h1>@lang('main.look')</h1>
    <p>@lang('main.mban')</p>
</div>


{{-- @include('layouts.footer') --}}

@endsection

@push('scripts')

<script>
    let searchType = (form) => {
    if ($('[href="#tab2"]').hasClass('active')) {
        $(form).append(`<input type="hidden" name="search_type" value="amount" />`)
    }
}
    $(document).ready(function () {

        $('.main_slider').slick({
            autoplay: true,
            autoplaySpeed: 3000,
            infinite: true,
            slidesToShow: 1,
            dots: true,
            arrows: false,
            swipe : true,
            delay : 3000,
            pauseOnHover : false,
            pauseOnFocus : false,
            pauseOnDotsHover : false,
            responsive: [ // 반응형 웹 구현 옵션
                {
                    breakpoint: 768, //화면 사이즈 768px
                    settings: {
                        //위에 옵션이 디폴트 , 여기에 추가하면 그걸로 변경
                        slidesToShow: 1,
                        dots: true,
                        customPaging: function (slider, i) {
                            return '<span>' + (i + 1) + '</span>' + '/' + slider.slideCount;
                        }
                    }
                }
            ]

        });

        @if($position)
            window.location = "#{{ $position }}";
        @endif
    });

    $('.main_slider').on('touchcancel touchmove', function(){
        $('.main_slider').slick('slickPlay');
    });

    var waveBtn = (function () {
        'use strict';
        var btn = document.querySelectorAll('.wave2'),
            tab = document.querySelector('.tabnav2'),
            indicator = document.querySelector('.indicator2'),
            indi = 0;
        indicator.style.marginLeft = indi + 'px';

        for (var i = 0; i < btn.length; i++) {
            btn[i].onmousedown = function (e) {
                var newRound = document.createElement('div'), x, y;

                var nav_width = $(this).outerWidth();
                nav_width = Number(nav_width);


                var nav_width = 0;
                var waveNum = $(this).data('num');
                for(var i=0; i<waveNum - 1; i++){
                    nav_width += $($(".wave2").get(i)).outerWidth();
                }

                var indi_width =  $(this).outerWidth();

                indicator.style.width = indi_width + 'px';
                indicator.style.marginLeft = indi + (this.dataset.num - 1 ) * 22 + nav_width + 'px';

                setTimeout(function () {
                    newRound.remove();
                }, 1200);
            };
        }
    }());

    var waveWidth2 = $('.tabnav2 li:first-child').width();
    $('.indicator2').width(waveWidth2);


    $(function () {
        $('.tabcontent > div').hide();
        $('.tabnav2 a').click(function () {
            $('.tabcontent > div').hide().filter(this.hash).fadeIn();
            $('.tabnav2 a').removeClass('active');
            $(this).addClass('active');
            $('.new_service .service_wrap').slick('setPosition');
            return false;
        }).filter(':eq(0)').click();

        var btn = document.querySelectorAll('.wave2'),
            indicator = document.querySelector('.indicator2');
            var indi_width =  $(btn[0]).outerWidth();
            indicator.style.marginLeft = '0px';
            indicator.style.width = indi_width + 'px';
    });

    $(function () {
        $('.search_content > div').hide();
        $('.search_btn a').click(function () {
            $('.search_content > div').hide().filter(this.hash).fadeIn();
            $('.search_btn a').removeClass('active');
            $(this).addClass('active');
            return false;
        }).filter(':eq(0)').click();
    });

    $(document).ready(function () {

        $('.new_service  .service_wrap').slick({
            setPosition: 0,
            variableWidth: true,
            autoplay: false,
            autoplaySpeed: 3000,
            infinite: false,
            slidesToShow: 4.5,
			slidesToScroll : 1,
            initialSlide:0,
            dots: false,
            arrows: true,
            prevArrow: '<button class="prev"><img src="/assets/images/store/next_new_service_p.svg"></button>',
            nextArrow: '<button class="next"><img src="/assets/images/store/next_new_service.svg"></button>',
            swipe: true,
            delay: 3000,
            pauseOnHover: false,
            pauseOnFocus: false,
            pauseOnDotsHover: false,
            responsive: [ // 반응형 웹 구현 옵션
                {
                    breakpoint: 1400, //화면 사이즈 768px
                    settings: {
                        //위에 옵션이 디폴트 , 여기에 추가하면 그걸로 변경
                        slidesToShow: 2.5,
                    }
                },
                {
                    breakpoint: 768, //화면 사이즈 768px
                    settings: {
                        //위에 옵션이 디폴트 , 여기에 추가하면 그걸로 변경
                        slidesToShow:2.5,
                        arrows: false,
                    }
                }
            ]

        });
        });
</script>
@endpush
