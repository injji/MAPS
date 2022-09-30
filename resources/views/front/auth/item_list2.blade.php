@extends('layouts.auth')

@section('title', __('store.store'))

@section('body')

@include('layouts.sub_header')

<!-- CCC 20220526 -->
<div class="service list_service">
    <div class="base_wrap">
		<h1>@lang('main.function_item')</h1>

		<div class="service_wrap list_service_wrap2">
            @foreach ($funclists as $function)
                <div class="service_list">
                <a href="/funtioninf?id={{$function->id}}">
                    <div>
                        <h4><img src="{{ Storage::url($function->thumb) }}"></h4>
                        <div class="service_content">
                            <h5>{{$function->title}}</h5>
                        </div>
                    </div>
                </a>
                </div>
            @endforeach
		</div>
    </div>
</div>

<style>
    /* 추가된 css */
    .service_list h4 {
        position: relative;
        padding-top: 56.25%;
        overflow: hidden;

    }

    .service_list h4 em {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        -webkit-transform: translate(0%, 0%);
        -ms-transform: translate(0%, 0%);
        transform: translate(0%, 0%);
    }

    .service_list h4 img {
        position: absolute;
        top: 49.5%;
        left: 0;
        max-width: 100%;
        height: auto;
        -webkit-transform: translate(-0%, -50%);
        -ms-transform: translate(-0%, -50%);
        transform: translate(-0%, -50%);

    }
</style>
@endsection

@push('scripts')
<script>
    $(function () {
        $('.tabcontent > div').hide();
        $('.tabnav a').click(function () {
            $('.tabcontent > div').hide().filter(this.hash).fadeIn();
            $('.tabnav a').removeClass('active');
            $(this).addClass('active');
            return false;
        }).filter(':eq(0)').click();
    });

    $(function () {
        $('.tabcontent > div').hide();
        $('.tabnav3 a').click(function () {
            $('.tabcontent > div').hide().filter(this.hash).fadeIn();
            $('.tabnav3 a').removeClass('active');
            $(this).addClass('active');
            return false;
        }).filter(':eq(0)').click();
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

<script>
    $(function () {
        var select = new CustomSelectBox('.select_box');
    });
</script>

<script>
    $(document).ready(function () {
        var $list = $("#itemList");

        $(".chkbox").change(function () {
            var a = $(this).next().text();
            if (this.checked) {
                $('<li><a>' + a + '</a><button class="closebutton" value="' + a +
                    '"><img src="/assets/images/store/close_tag.svg"></button></li>').appendTo(
                    $list).data('src', this);

            } else {
                $("#itemList li:contains('" + a + "')").remove();
            }
        })
        $(document).on('click', '.closebutton', function () {
            var $li = $(this).closest('li');
            $($li.data('src')).prop('checked', false);
            $li.remove();
        });
    });

    $(document).ready(function () {
        var $list = $("#itemList2");

        $(".chkbox").change(function () {
            var a = $(this).next().text();
            if (this.checked) {
                $('<li><a>' + a + '</a><button class="closebutton" value="' + a +
                    '"><img src="/assets/images/store/close_tag.svg"></button></li>').appendTo(
                    $list).data('src', this);

            } else {
                $("#itemList2 li:contains('" + a + "')").remove();
            }
        })
        $(document).on('click', '.closebutton', function () {
            var $li = $(this).closest('li');
            $($li.data('src')).prop('checked', false);
            $li.remove();
        });
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
            $(".service_list").slice(0, 20).show(); // select the first ten
            $("#load").click(function (e) { // click event for load more
                e.preventDefault();
                $(".service_list:hidden").slice(0, 20)
                    .show(); // select next 10 hidden divs and show them
                if ($(".service_list:hidden").length ==
                    0) { // check if any hidden divs still exist
                    $("#load").hide();
                }
            });
        } else if (screenWidth >= 768) {
            $(".service_list").slice(0, 12).show(); // select the first ten
            $("#load").click(function (e) { // click event for load more
                e.preventDefault();
                $(".service_list:hidden").slice(0, 9)
                    .show(); // select next 10 hidden divs and show them
                if ($(".service_list:hidden").length ==
                    0) { // check if any hidden divs still exist
                    $("#load").hide();
                }
            });
        } else {
            $(".service_list").slice(0, 6).show(); // select the first ten
            $("#load").click(function (e) { // click event for load more
                e.preventDefault();
                $(".service_list:hidden").slice(0, 6)
                    .show(); // select next 10 hidden divs and show them
                if ($(".service_list:hidden").length ==
                    0) { // check if any hidden divs still exist
                    $("#load").hide();
                }
            });
        }

    });
</script>

<script>
    $('.fliterbox_open_m').click(function () {
        $('.filter_box2').addClass('filter-open');
        $('.black_bg').show();
    });

    $('.xx_close').click(function () {
        $('.filter_box2').removeClass('filter-open');
        $('.black_bg').hide();
    });
</script>
@endpush