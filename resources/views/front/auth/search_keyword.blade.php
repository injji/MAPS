@extends('layouts.auth')

@section('title',  __('inquiry.agent_txt2'))

@section('body')

@include('layouts.sub_header')
<!-- CCC 20220516 -->
<div class="search_box search_box2">
    <div class="base_wrap">

        @if(request()->search_type == 'amount')
            <h1>
                <span><img src="/assets/images/store/search_icon_black.svg"></span>
                {{ number_format(request()->min_price) }} <em>~</em> {{ number_format(request()->max_price) }}
            </h1>
		@elseif(request()->keyword == null && request()->max_price != null)
            <h1>
                <span><img src="/assets/images/store/search_icon_black.svg"></span>
                {{ number_format(request()->min_price) }} <em>~</em> {{ number_format(request()->max_price) }}
            </h1>
        @else
            <h1>
                <button><img src="/assets/images/store/search_icon_black.svg"></button>
                <input type="text" value="{{ request()->keyword }}">
            </h1>
        @endif

        <div class="search_wrap base_wrap">
            <div class="only_pctab">
                <form id="fsearch" action="/search" onsubmit="searchType(this)">
                    <input type="hidden" id="sort" name="sort" value="{{$sort}}" />
                    <input type="hidden" id="limit" name="limit" value="{{$limit}}" />
                    <input type="hidden" id="category" name="category" value="{{$category}}" />
                    <input type="hidden" id="cateindex" name="cateindex" value="{{$cateindex}}" />
                    <input type="hidden" id="freecost" name="freecost" value="{{$freecost}}" />
                    <input type="hidden" id="filtercatory" name="filtercatory" value="{{$filtercatory}}" />
                    <div class="search_div">
                        <ul class="search_btn">
                            <li><a href="#tab1"></a></li>
                            <li><a href="#tab2"></a></li>
                        </ul>
                        <div class="search_content">
                            <div id="tab1">
                                <input type="search" name="keyword" value="{{ request()->keyword }}" placeholder="@lang('messages.search_find')"  onkeypress="if( event.keyCode == 13 ){enterKey();}">
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
                <form id="fsearch" action="/search" onsubmit="searchType(this)">
                    <input type="hidden" id="sort" name="sort" value="{{$sort}}" />
                    <input type="hidden" id="limit" name="limit" value="{{$limit}}" />
                    <input type="hidden" id="category" name="category" value="{{$category}}" />
                    <input type="hidden" id="cateindex" name="cateindex" value="{{$cateindex}}" />
                    <input type="hidden" id="freecost" name="freecost" value="{{$freecost}}" />
                    <input type="hidden" id="filtercatory" name="filtercatory" value="{{$filtercatory}}" />
                    <div class="search_div">
                        <ul class="search_btn">
                            <li><a href="#tab1"></a></li>
                            <li><a href="#tab2"></a></li>
                        </ul>

                        <div class="sear_border">
                            <div class="search_content">
                                <div id="tab1"><form action>
                                    <input type="search" name="keyword" value="{{ request()->keyword }}" placeholder="@lang('messages.search_find')"  onkeypress="if( event.keyCode == 13 ){enterKey();}">
                                </form></div>

                                <div id="tab2">
                                    @include('layouts.pay')
                                </div>
                            </div>

                            <button type="submit">
                                <img src="/assets/images/store/search_b.svg">
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="service search_service">
    <div class="base_wrap">

        <div class="only_pctab">
            <ul class="tabnav catetab">
                <li class="tab wave {{ $category == 0?'dark':''}}">
                    <a href="#tab-all" data-id="0" data-index="0" class="{{ $category == 0?'active':''}}">@lang('process.all')</a>
                </li>
                <li>
                    <ul>
                @foreach ($search_categorys as $code => $c)
                    <li class="tab wave {{ $category == $c->id ?'dark':''}}">
                        <a href="#tab-all" data-id="{{ $c->id }}" data-index="{{ $code + 1 }}" class="{{ $category == $c->id?'active':''}}">{{ $c->text }}</a>
                    </li>
                @endforeach

                    </ul>
                </li>
                <div class="indicator" ></div>
            </ul>
        </div>

        <div class="only_m">
            <ul class="tabnav3 catetab">
                <li class="tab wave3 {{ $category == 0 ?'dark':''}}">
                    <a data-id="0" data-index="0" class="{{ $category == 0?'active':''}}">@lang('process.all')</a>
                </li>
                @foreach ($search_categorys as $code => $c)
                    <li class="tab wave3 {{ $category == $c->id ?'dark':''}}">
                        <a data-id="{{ $c->id }}" data-index="{{ $code + 1 }}" class="{{ $category == $c->id?'active':''}}">{{ $c->text }}</a>
                    </li>
                @endforeach
                <div class="indicator3"></div>
            </ul>
        </div>


        <div class="search_filter">
            <button class="fliterbox_open only_pctab">
                <img src="/assets/images/store/filter.svg">
                @lang('sub.sub-filter')
            </button>

            <button class="fliterbox_open_m only_m">
                <img src="/assets/images/store/filter.svg">
                @lang('sub.sub-filter')
            </button>

            <div class="select_box search_select">
                <div class="box">
					<div class="select2">
                    @if($sort == 1)
                        @lang('sub.sub-new_list')
                    @elseif($sort == 2)
                        @lang('sub.sub-sale_list')
                    @elseif($sort == 3)
                        @lang('sub.sub-lowprice_list')
                    @else
                        @lang('sub.sub-star_list')
                    @endif
                    </div>
                    <ul class="list">
                    @if($sort == 1)
                        <li class="selected" value="1">
                    @else
                        <li value="1">
                    @endif
                        @lang('sub.sub-new_list')</li>
                    @if($sort == 2)
                        <li class="selected" value="2">
                    @else
                        <li value="2">
                    @endif
                        @lang('sub.sub-sale_list')</li>
                    @if($sort == 3)
                        <li class="selected" value="3">
                    @else
                        <li value="3">
                    @endif
                        @lang('sub.sub-lowprice_list')</li>
                    @if($sort == 4)
                        <li class="selected" value="4">
                    @else
                        <li value="4">
                    @endif
                        @lang('sub.sub-star_list')</li>
                    </ul>
                </div>
            </div>
        </div>


        <div class="filter_box only_pctab">
            <div id="fliter" class="tab-pane">
                <div>
                    <p>@lang('sub.sub-filter_price')</p>

                    <div>
                        <div id="ck-button">
                            <label>
                                <input type="checkbox" class="chkbox" name="filter_content" data-type="pay" {{ $freecost % 100 > 9?"checked":""}} value="10">
                                <span>@lang('sub.sub-filter_free')</span>
                            </label>
                        </div>
                        <div id="ck-button">
                            <label>
                                <input type="checkbox" class="chkbox" name="filter_content" data-type="pay" {{ $freecost % 10 > 0?"checked":""}} value="1">
                                <span>@lang('sub.sub-filter_pay')</span>
                            </label>
                        </div>
                        <div id="ck-button">
                            <label>
                                <input type="checkbox" class="chkbox" name="filter_content" data-type="pay" {{ $freecost > 99?"checked":""}} value="100">
                                <span>@lang('sub.sub-filter_inapp')</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <p>@lang('sub.sub-filter_content')</p>

                    <div>
                        @foreach ($filtercates as $code => $cate)
                            <div id="ck-button">
                                <label>
                                    <input type="checkbox" id="chkbox_pc" class="chkbox" name="filter_content" {{ strpos($filtercatory, ",".$cate->id.",")!== false?"checked":""}} value="{{$cate->id}}">
                                    <span>{{$cate->text}}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div id="items">
                <ul id="itemList"></ul>
                <button class="all_checkout">@lang('sub.sub-filter_initialization')</button>
                <button onclick="searchService()" style="right:100px;">@lang('sub.sub-filter_apply')</button>
            </div>

        </div>

        <div class="filter_box2 only_m" >

			<h3>
				<img src="/assets/images/store/filter.svg">
                @lang('sub.sub-filter')
			</h3>

            <button class="xx_close"><img src="/assets/images/store/xx.svg"></button>

            <div id="fliter" class="tab-pane">
                <div>
                    <p>@lang('sub.sub-filter_price')</p>

                    <div>
                        <div id="ck-button">
                            <label>
                                <input type="checkbox" class="chkbox" name="filter_content" value="10">
                                <span>@lang('sub.sub-filter_free')</span>
                            </label>
                        </div>
                        <div id="ck-button">
                            <label>
                                <input type="checkbox" class="chkbox" name="filter_content" value="1">
                                <span>@lang('sub.sub-filter_pay')</span>
                            </label>
                        </div>
                        <div id="ck-button">
                            <label>
                                <input type="checkbox" class="chkbox" name="filter_content" value="100">
                                <span>@lang('sub.sub-filter_inapp')</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <p>@lang('sub.sub-filter_content')</p>

                    <div>
                        @foreach ($filtercates as $code => $cate)
                            <div id="ck-button">
                                <label>
                                    <input type="checkbox" id="chkbox_m" class="chkbox" name="filter_content" {{ strpos($filtercatory, ",".$cate->id.",")!== false?"checked":""}} value="{{$cate->id}}">
                                    <span>{{$cate->text}}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div id="items2">
                <ul id="itemList2"></ul>
            </div>

			<div class="filter_btn">
			<button class="all_checkout">@lang('sub.sub-filter_initialization')</button>
			<button onclick="searchService()">@lang('sub.sub-filter_apply')</button>
			</div>

        </div>

        <div class="black_bg only_m"></div>

        <div class="tabcontent search_tabcontent">
            <div id="tab-all" class="service_wrap">
                @foreach ($services as $service)
                    @include('partials.store.service_banner', compact('service'))
                @endforeach

                @if(count($services) == $limit)
                <a id="load" class="tab_more"><span class="only_pctab">@lang('sub.sub-more')</span><span class="only_m">+ @lang('sub.sub-search') @lang('sub.sub-result')
                        @lang('sub.sub-more')</span></a>
                @endif
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    let searchType = (form) => {
        if ($('[href="#tab2"]').hasClass('active')) {
            $(form).append(`<input type="hidden" name="search_type" value="amount" />`)
        }
    }
    var t_filterctg = "{{ $filtercatory }}";
    var g_filterctg = t_filterctg.split(",");
    g_filterctg = g_filterctg.filter((item) => item.trim() != "");

    function searchService() {
        $(`#fsearch`).submit();
    }

    $(function () {
        // $('.tabcontent > div').hide();

        $('.tabnav a').click(function (e) {
            $("#limit").val(8);
            $("#category").val($(e.target).data('id'));
            $("#cateindex").val($(e.target).data('index'));
            searchService();
            return false;
        })

        var btn = document.querySelectorAll('.wave'),
        indicator = document.querySelector('.indicator');
        var indi_width =  $(btn[0]).outerWidth();
        indicator.style.marginLeft = indi_width*{{ $cateindex }} + 'px';
        indicator.style.width = indi_width + 'px';
    });

    $(function () {
        // $('.tabcontent > div').hide();
        $('.tabnav3 a').click(function (e) {
            $("#category").val($(e.target).data('id'));
            $("#cateindex").val($(e.target).data('index'));
            searchService();
            return false;
        })

        var btn = document.querySelectorAll('.wave3'),
        indicator = document.querySelector('.indicator3');
        var indi_width =  $(btn[0]).outerWidth();
        indicator.style.marginLeft = indi_width*{{ $cateindex }} + 'px';
        indicator.style.width = indi_width + 'px';
    });
</script>

<script>
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
            $("#sort").val($target[0].value);
            $target.addClass('selected').siblings('li').removeClass('selected');
            this.$selectBox.removeClass('on');
            this.$select.text($target.text());
            this.$list.css('display', 'none');
            searchService();
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

<script>
    $(function () {
        var select = new CustomSelectBox('.select_box');
    });
</script>

<script>
    $(document).ready(function () {
        var $list = $("#itemList");
        var $list2 = $("#itemList2");
        $(".chkbox").change(function () {
            if($(this).data('type') == "pay")
            {
                var new_val = $("#freecost").val();
                if(this.checked)
                {
                    new_val = parseInt(new_val)  + parseInt(this.value);
                }else{
                    new_val = parseInt(new_val) - parseInt(this.value);
                }
                $("#freecost").val(new_val);
            }else{
                var a = $(this).next().text();
                if (this.checked) {
                    g_filterctg.push(this.value);
                    $("#filtercatory").val(","+g_filterctg.join()+",");
                    $('<li ><a>' + a + '</a><button class="closebutton" value="' + a +
                        '"><img src="/assets/images/store/close_tag.svg"></button></li>').appendTo(
                        $list).data('src', this);
                    $('<li ><a>' + a + '</a><button class="closebutton" value="' + a +
                        '"><img src="/assets/images/store/close_tag.svg"></button></li>').appendTo(
                            $list2).data('src', this);

                } else {
                    g_filterctg = g_filterctg.filter((item)=> item != this.value);
                    $("#filtercatory").val(","+g_filterctg.join()+",");
                    $("#itemList li:contains('" + a + "')").remove();
                    $("#itemList2 li:contains('" + a + "')").remove();
                }
            }

        })
        $(document).on('click', '.closebutton', function () {
            var $li = $(this).closest('li');
            $($li.data('src')).prop('checked', false);
            var t_val = $($li.data('src')).val();
            g_filterctg = g_filterctg.filter((item)=> item != t_val);
            $("#filtercatory").val(","+g_filterctg.join()+",");
            $li.remove();
        });

        var chkbtn = document.querySelectorAll('#chkbox_pc');
        for (var i = 0; i < chkbtn.length; i++) {
            if($(chkbtn[i]).data('type') != "pay")
            {
                if (chkbtn[i].checked) {
                    var a = $(chkbtn[i]).next().text();
                    $('<li ><a>' + a + '</a><button class="closebutton" value="' + a +
                        '"><img src="/assets/images/store/close_tag.svg"></button></li>').appendTo(
                        $list).data('src', chkbtn[i]);
                }
            }
        }

        var chkbtn = document.querySelectorAll('#chkbox_m');
        for (var i = 0; i < chkbtn.length; i++) {
            if($(chkbtn[i]).data('type') != "pay")
            {
                if (chkbtn[i].checked) {
                    var a = $(chkbtn[i]).next().text();
                    $('<li ><a>' + a + '</a><button class="closebutton" value="' + a +
                        '"><img src="/assets/images/store/close_tag.svg"></button></li>').appendTo(
                            $list2).data('src', chkbtn[i]);
                }
            }
        }
    });
</script>

<script>
    $(document).on('click', '.all_checkout', function () {
        $('.chkbox').prop('checked', false);
        $('#itemList li').hide();
    });

    $(document).on('click', '.all_checkout', function () {
        $('.chkbox').prop('checked', false);
        $('#itemList2 li').hide();
    });
</script>

<script>
    $(document).on('click', '.fliterbox_open', function () {
        $('.filter_box').slideToggle();
    })
</script>


<script>
    $(function () {
        var screenWidth = window.innerWidth;

        if (screenWidth >= 1400) {
            $("#load").click(function (e) { // click event for load more
                e.preventDefault();

                new_val = parseInt($("#limit").val())  + 8;
                $("#limit").val(new_val);
                searchService();
            });
        } else if (screenWidth >= 768) {
            $("#load").click(function (e) { // click event for load more
                e.preventDefault();

                new_val = parseInt($("#limit").val())  + 6;
                $("#limit").val(new_val);
                searchService();
            });
        } else {
            $("#load").click(function (e) { // click event for load more

                new_val = parseInt($("#limit").val())  + 8;
                $("#limit").val(new_val);
                searchService();
            });
        }

    });

</script>

<script>
$('.fliterbox_open_m').click(function(){
  $('.filter_box2').addClass('filter-open');
  $('.black_bg').show();
});

$('.xx_close').click(function(){
  $('.filter_box2').removeClass('filter-open');
  $('.black_bg').hide();
});
</script>
<script>
    $(function () {
        $('.search_content > div').hide();
        $('.search_btn a').click(function () {
            $('.search_content > div').hide().filter(this.hash).show();
            $('.search_btn a').removeClass('active');
            $(this).addClass('active');
            return false;
        }).filter(':eq(0)').click();
    });

</script>
<script src="{{ asset('js/store.js') }}" charset="utf-8"></script>
<style>

</style>
@endpush
