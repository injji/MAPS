@extends('layouts.client')

@section('content')

<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<style>
    .col-md-12 {
        padding: 0;
    }
</style>

<script>
    var popup_list = @json($popup_list);
    var popup_idx = 0;

    console.log(popup_list);

    //쿠키설정
    function setCookie(name, value, expiredays) {
        var todayDate = new Date();
        todayDate.setDate(todayDate.getDate() + expiredays);
        document.cookie = name + '=' + escape(value) + '; path=/; expires=' + todayDate.toGMTString() + ';'
    }

    //쿠키 불러오기
    function getCookie(name) {
        var obj = name + "=";
        var x = 0;
        while (x <= document.cookie.length) {
            var y = (x + obj.length);
            if (document.cookie.substring(x, y) == obj) {
                if ((endOfCookie = document.cookie.indexOf(";", y)) == -1)
                    endOfCookie = document.cookie.length;
                return unescape(document.cookie.substring(y, endOfCookie));
            }
            x = document.cookie.indexOf(" ", x) + 1;

            if (x == 0) break;
        }
        return "";
    }

    //닫기 버튼 클릭시
    function closeWin(id) {
        if ($("#todaycloseyn"+id).prop("checked")) {
            setCookie('divpop' + id, 'Y', 1);
        }

        $("#divpop"+id).hide();
    }

    function popupZindex(element){
        const divpop = document.querySelectorAll('.divpop');
        for ( var i = 0; i < divpop.length; i++ ) {
            divpop[i].style.zIndex = 999;
        }
        element.style.zIndex = 1000;
    }

    function showPopup(idx) {

        var str = JSON.stringify(popup_list[idx]).replaceAll("\"","'");

        var html = '<div id="divpop'+popup_list[idx].id+'" class="divpop" onclick="popupZindex(this)" ondrag="popupZindex(this)">';
        html += '<div>';
        html += '<a href="javascript:detail(' + str + ')">';

        if (popup_list[idx].img != null && popup_list[idx].img != '') {
            var img = "{!! Storage::url('"+popup_list[idx].img+"') !!}";
            html += '<img src="'+img+'">';
        }

        html += '</a>';
        html += '</div>';
        html += '<div class="button_area">';
        html += '<input type="checkbox" name="chkbox" id="todaycloseyn'+popup_list[idx].id+'" value="Y">오늘 하루 이 창을 열지 않음';
        html += '<a href="javascript:closeWin('+popup_list[idx].id+')">닫기</a>';
        html += '</div>';
        html += '</div>';

        $("#popup_list").append(html);
        $('#divpop'+popup_list[idx].id).show();
        $('#divpop'+popup_list[idx].id).draggable();
        $('#divpop'+popup_list[idx].id).css('top', (50 + popup_idx * 20)+'px');
        $('#divpop'+popup_list[idx].id).css('left', (50 + popup_idx * 20)+'px');
        popup_idx++;
    }

    $(function () {
        $("#popup_list").empty();

        for (var i = 0; i < popup_list.length; i++) {
            if (getCookie("divpop"+popup_list[i].id) != "Y") {
                showPopup(i);
            }
        }
    });


</script>

<!-- 팝업 -->
<div id="popup_list"></div>

<!-- 스크립트 삽입되지 않은 상태라면 노출 -->
@if(\Auth::user()->current_site)
    @if(\Auth::user()->current_site->header == 0)
        <div class="service_script">
            <p>@lang('client.client_script') <a href="{{ route('client.guide') }}">@lang('client.client_script_btn')</a></p>
        </div>
    @endif
@endif
<div class="dash_item">
    <div class="my_site_select">
        <div class="select_box_site">
            <div class="box">
                <div class="select2">
                    @if(Auth::user()->current_site)
                        {{ Auth::user()->current_site->name }}
                    @else
                        @lang('client.client_sel_site')
                    @endif
                </div>
                <ul class="list">
                    @foreach(Auth::user()->site as $key => $item)
                        <li onclick="changeSite({{ $item->id }})" class="{{ Auth::user()->current_site && Auth::user()->current_site->id == $item->id ? 'selected' : '' }}">{{ $item->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="site_btn">
            <p>@lang('client.client_app')  {{ Auth::user()->site->count() }} @lang('client.client_app2')</p>
            <a href="javascript:createSite()">@lang('client.client_app_btn')</a>
        </div>
    </div>

    <div class="using_service">
        <h2>@lang('client.client_AS')</h2>

        @if($use_service_list->count() > 0)
            <ul class="using_service_ul">
                @foreach($use_service_list as $key => $item)
                    <li>
                        <div>
                            <a href="{{ route('store.service.detail' , ['service' => $item->service_id]) }}">
                                <img src="{{ Storage::url($item->service->icon) }}">
                            </a>

                            <div>
                                <a href="{{ route('store.service.detail' , ['service' => $item->service_id]) }}">
                                    <h3>{{ $item->service->name }}</h3>
                                </a>
                                @if($item->service->free_term == 99999)
                                    <span>∞</span>
                                @else
                                    @if($item->service_start_at && $item->service_end_at)
                                    <span>
                                        {{ ($item->service_end_at ?? false) ? $item->service_start_at->format('Y-m-d') : '-' }} - {{ ($item->service_end_at ?? false) ? $item->service_end_at->format('Y-m-d') : '-' }}
                                        <em>D-{{ strtotime($item->service_end_at) - strtotime(date('Y-m-d')) < 0 ? 0 : ceil((strtotime($item->service_end_at) - strtotime(date('Y-m-d'))) / 86400) }}</em>
                                    </span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <ul>
                            @if($item->process == 2)
                                <li id="using_process" style="color: #007C4F;">{{ $item->process_text ?? '' }}</li>
                            @else
                                <li id="using_process" style="color: #666;">{{ $item->process_text ?? '' }}</li>
                            @endif
                            <li>
                                @if($item->service->url)
                                    <a href="{{ $item->service->url."?".$hmac_query."&hmac=".$hmac }}" target="_blank">@lang('client.client_set')</a>
                                    <input type="hidden" value="{{ Auth::user()->current_site->client_sid }}">
                                @else
                                    <a href="javascript:void(0)">@lang('client.client_set')</a>
                                @endif
                            </li>
                        </ul>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="no">
                <p>@lang('client.client_AS_no')</p>
                <a href="{{ route('store.home') }}" target="_blank">@lang('client.client_go')</a>
            </div>
        @endif
    </div>

    <div class="swiper dashboard_swiper">
        <h2>@lang('client.client_my')</h2>
        <div class="mySwiper" style="height: 160px;">
        @if($my_pick_list->count() > 0)
            <div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide"
                aria-controls="swiper-wrapper-da926bf66877a92a" aria-disabled="false">
                <img src="/assets/images/store/next_btn.svg">
            </div>
            <div class="swiper-button-prev swiper-button-disabled" tabindex="-1" role="button" aria-label="Previous slide"
                aria-controls="swiper-wrapper-da926bf66877a92a" aria-disabled="true">
                <img src="/assets/images/store/prev_btn.svg">
            </div>
            <div class="swiper-wrapper">
                @foreach($my_pick_list as $key => $item)
                    <a href="{{ route('store.service.detail', ['service' => $item->service->id]) }}" class="swiper-slide" target="_blank">
                        <img src="{{ Storage::url($item->service->icon) }}">
                        <h3>{{ $item->service->name }}</h3>
                    </a>
                @endforeach
            </div>
        @else
            <div class="no">
                <p>@lang('client.client_my_no')</p>
            </div>
        @endif
        </div>
    </div>

    <div class="d_banner">
		<h2>@lang('client.client_notice')
		<span><a href="{{ route('client.notice') }}">@lang('sub.sub-more')</a></span>
		</h2>

		<div class="notice_banner">
			<div class="notice_dash" style="width: 50%;">
				<div class="notice_dash2">
					<div>
                        <ul>
                            @foreach($notice_list as $key => $item)
                                <li>
                                    <a href="javascript:detail({{ $item }})">
                                        <span>{!! str_replace('-', '.', substr($item->created_at, 0, 10)) !!}</span> {{ $item->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
					</div>
				</div>
			</div>
            @if($banner_img)
			    <a href="{{ $banner->url }}" target="_blank" class="banner_img_dash" style="background-image:url({{ $banner_img }});background-size: cover;width: 50%;"></a>
            @endif
		</div>
    </div>
</div>

<div class="modal fade client_notice_detail" id="detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="m_title"></h5>
				<span id="m_time"></span>
			</div>
            <div class="modal-body">
                <div id="m_content"></div>
				<button data-bs-dismiss="modal" class="notice_close">@lang('button.close')</button>
            </div>

        </div>
    </div>
</div>

<script>
    var swiper = new Swiper(".mySwiper", {
        loop: false,
        slidesPerView: 5,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        speed: 1000,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
</script>

@endsection


@push('scripts')
<script>
	function detail(obj) {
        closeWin(obj.id);
		$("#m_title").html(obj.title);
		$("#m_time").html(getCorrectDateTime(obj.created_at).replace(/-/g, '.'));
		$("#m_content").html(obj.content);
		$("#detail").modal('show');

        $.ajax({
			url: '/notice/hits',
			method: 'post',
			data: {id: obj.id},
			success: (response) => {}
		});
	}

    $(function () {
        var select = new CustomSelectBox('.select_box_site');
    });

    function CustomSelectBox(selector) {
        this.$selectBox = null,
            this.$select = null,
            this.$list = null,
            this.$listLi = null;
        CustomSelectBox.prototype.init = function (selector) {
            this.$selectBox = $(selector);
            this.$select = this.$selectBox.find('.box .select2');
            this.$list = this.$selectBox.find('.box .list');
            this.$listLi = this.$list.children('li');
        }
        CustomSelectBox.prototype.initEvent = function (e) {
            var that = this;
            this.$select.on('click', function (e) {
                that.listOn();
            });
            this.$listLi.on('click', function (e) {
                that.listSelect($(this));
            });
            $(document).on('click', function (e) {
                that.listOff($(e.target));
            });
        }
        CustomSelectBox.prototype.listOn = function () {
            this.$selectBox.toggleClass('on');
            if (this.$selectBox.hasClass('on')) {
                this.$list.css('display', 'block');
            } else {
                this.$list.css('display', 'none');
            };
        }
        CustomSelectBox.prototype.listSelect = function ($target) {
            $target.addClass('selected').siblings('li').removeClass('selected');
            this.$selectBox.removeClass('on');
            this.$select.text($target.text());
            this.$list.css('display', 'none');
        }
        CustomSelectBox.prototype.listOff = function ($target) {
            if (!$target.is(this.$select) && this.$selectBox.hasClass('on')) {
                this.$selectBox.removeClass('on');
                this.$list.css('display', 'none');
            };
        }
        this.init(selector);
        this.initEvent();
    }
</script>
@endpush

<style>
    #m_content {
        text-align: left;
        width: 100%;
        white-space: pre-line;
    }
    #m_content img {
        max-width: 100% !important;
    }
</style>
