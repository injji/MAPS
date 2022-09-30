@extends('layouts.auth')

@section('body')
<style>
    footer{display: none !important; }
</style>

<div id="maps_content">
    @include('layouts.sub_header')

    <div class="sub_main">
        <h1>@lang('sub.maps-contents')</h1>
        <p>@lang('sub.maps-contents_p')</p>
    </div>
</div>

<div id="maps_content_list">
    <div class="mcl base_wrap" >
        <input type="hidden" id="top_list" value="{{ $top_array }}">
        <ul>
            @if($top_list)
                @foreach($top_list as $key => $item)
                    <li><a href="{{ route('store.mapscontent_detail', ['content' => $item->id]) }}">
                        <h2><img src="{{ Storage::url($item->img) }}"></h2>
                        <h3>{{ $item->title }}</h3>
                        <p>{{ $item->description }}</p>
                        <span>{{ $item->created_at->format('Y.m.d') }}</span></a>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>

    @if($banner_item)
        <div class="mcl_mid">
            <div class="base_wrap">
                <h2><img src="{{ Storage::url($banner_item->img) }}"></h2>
                <div class="mcl_txt">
                    <h3>{{ $banner_item->title }}</h3>
                    <p>{{ $banner_item->description }}</p>
                    <a href="{{ route('store.mapscontent_detail', ['content' => $banner_item->id]) }}">@lang('sub.maps-more')</a>

                    @if($banner_list)
                        <div>
                            <h5>@lang('sub.maps-with')</h5>
                            <div class="mcl_swiper">
                            <div class="swiper-wrapper">
                                @foreach ($banner_list as $key => $item)
                                    <div class="swiper-slide">
                                        <a href="{{ route('store.mapscontent_detail', ['content' => $item->id]) }}">
                                            <h2><img src="{{ Storage::url($item->img) }}"></h2>
                                            <h3>{{ $item->title }}</h3>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    @endif



    <div class="mcl base_wrap" style="margin-bottom:120px">
        <ul class="mcl_ul">
            @if($bottom_list)
                @foreach($bottom_list as $key => $item)
                    <li><a href="{{ route('store.mapscontent_detail', ['content' => $item->id]) }}">
                        <h2><img src="{{ Storage::url($item->img) }}"></h2>
                        <h3>{{ $item->title }}</h3>
                        <p>{{ $item->description }}</p>
                        <span>{{ $item->created_at->format('Y.m.d') }}</span></a>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
</div>

<script>

let offset = 0;

let swiper = new Swiper(".mcl_swiper", {
    slidesPerView: "auto",
    spaceBetween: 38,
    pagination: false,
});

$(document).scroll(function() {
    if ($(document).height() <= $(window).scrollTop() + $(window).height() + 10) {

        offset = offset + 6;
        let top_list = $('#top_list').val();
        let request = new FormData();

        request.set('top_list', top_list);
        request.set('offset', offset);

        $.ajax({
            url: '/store/maps_content_more',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                $('.mcl_ul').append(response.html);
            }
        })
    }
});
</script>
@endsection


