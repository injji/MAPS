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
        <ul>

            <li><a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                <h2><img src="/assets/images/store/map_content1.png"></h2>
                <h3>마테크를 써야 하는 이유</h3>
                <p>마케터에게 "마테크(Martch, Marketing + Technology)는 더 이상 선택이 아닌 필수의 영역이 되었습니다. 마테크의 시대 별 변화를 통해 디지털 마케팅</p>
                <span>2022.04.29</span></a>
            </li>

            <li><a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                <h2><img src="/assets/images/store/map_content2.png"></h2>
                <h3>MAPS 사용 팁</h3>
                <p>1. 간편검색을 통해 내가 맞는 서비스를 찾아보세요! 2. 마테크 사 클릭 후 서비스 신청을 해보세요! 3. 한 공간에서 손쉽게 관리해 보세요!</p>
                <span>2022.04.29</span></a>
            </li>

            <li><a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                <h2><img src="/assets/images/store/map_content3.png"></h2>
                <h3>앱 사용자를 충성고객으로 거듭나게 하기 위한 세가지 비법</h3>
                <p>여러분은 마테크(Martch)에 대해 잘 알고 게신가요? 마테크는 마케팅 Marketing + 기술 Technology를 접목시켜 탄생한 용어로 2010년 초반부터</p>
                <span>2022.04.29</span></a>
            </li>

            <li><a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                <h2><img src="/assets/images/store/map_content1.png"></h2>
                <h3>마테크를 써야 하는 이유</h3>
                <p>마케터에게 "마테크(Martch, Marketing + Technology)는 더 이상 선택이 아닌 필수의 영역이 되었습니다. 마테크의 시대 별 변화를 통해 디지털 마케팅</p>
                <span>2022.04.29</span></a>
            </li>

            <li><a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                <h2><img src="/assets/images/store/map_content2.png"></h2>
                <h3>MAPS 사용 팁</h3>
                <p>1. 간편검색을 통해 내가 맞는 서비스를 찾아보세요! 2. 마테크 사 클릭 후 서비스 신청을 해보세요! 3. 한 공간에서 손쉽게 관리해 보세요!</p>
                <span>2022.04.29</span></a>
            </li>

            <li><a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                <h2><img src="/assets/images/store/map_content3.png"></h2>
                <h3>앱 사용자를 충성고객으로 거듭나게 하기 위한 세가지 비법</h3>
                <p>여러분은 마테크(Martch)에 대해 잘 알고 게신가요? 마테크는 마케팅 Marketing + 기술 Technology를 접목시켜 탄생한 용어로 2010년 초반부터</p>
                <span>2022.04.29</span></a>
            </li>

        </ul>
    </div>

    <div class="mcl_mid">
        <div class="base_wrap">
            <h2><img src="/assets/images/store/map_content3.png"></h2>
            <div class="mcl_txt">
                <h3>마테크가 생긴 이유</h3>
                <p>여러분은 마테크(Martech)에 대해 잘 알고 계신가요? 마테크는 마케팅 Marketing + 기술 Technology를 접목시켜 탄생한 용어로 2010년 초반부터 등장한 마케팅 기법입니다.</p>
                <a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">@lang('sub.maps-more')</a>

                <div>
                    <h5>@lang('sub.maps-with')</h5>
                    <div class="mcl_swiper">
                    <div class="swiper-wrapper">

                        <div class="swiper-slide">
                        <a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                            <h2><img src="/assets/images/store/map_content1.png"></h2>
                            <h3>마테크를 써야 하는 이유</h3></a>
                        </div>

                        <div class="swiper-slide"><a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                            <h2><img src="/assets/images/store/map_content2.png"></h2>
                            <h3>MAPS 사용 팁</h3></a>
                        </div>

                        <div class="swiper-slide"><a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                            <h2><img src="/assets/images/store/map_content5.jpg"></h2>
                            <h3>앱 사용자를 충성고객으로 거듭나게 하기 위한 세가지 비법</h3></a>
                        </div>
                        <div class="swiper-slide">
                        <a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                            <h2><img src="/assets/images/store/map_content1.png"></h2>
                            <h3>마테크를 써야 하는 이유</h3></a>
                        </div>

                        <div class="swiper-slide"><a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                            <h2><img src="/assets/images/store/map_content2.png"></h2>
                            <h3>MAPS 사용 팁</h3></a>
                        </div>

                        <div class="swiper-slide"><a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                            <h2><img src="/assets/images/store/map_content5.jpg"></h2>
                            <h3>앱 사용자를 충성고객으로 거듭나게 하기 위한 세가지 비법</h3></a>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="mcl base_wrap" style="margin-bottom:120px">
        <ul class="mcl_ul">
            <li class="mcl_item"><a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                <h2><img src="/assets/images/store/map_content1.png"></h2>
                <h3>마테크를 써야 하는 이유</h3>
                <p>마케터에게 "마테크(Martch, Marketing + Technology)는 더 이상 선택이 아닌 필수의 영역이 되었습니다. 마테크의 시대 별 변화를 통해 디지털 마케팅</p>
                <span>2022.04.29</span></a>
            </li>

            <li class="mcl_item"><a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                <h2><img src="/assets/images/store/map_content2.png"></h2>
                <h3>MAPS 사용 팁</h3>
                <p>1. 간편검색을 통해 내가 맞는 서비스를 찾아보세요! 2. 마테크 사 클릭 후 서비스 신청을 해보세요! 3. 한 공간에서 손쉽게 관리해 보세요!</p>
                <span>2022.04.29</span></a>
            </li>

            <li class="mcl_item"><a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                <h2><img src="/assets/images/store/map_content3.png"></h2>
                <h3>앱 사용자를 충성고객으로 거듭나게 하기 위한 세가지 비법</h3>
                <p>여러분은 마테크(Martch)에 대해 잘 알고 게신가요? 마테크는 마케팅 Marketing + 기술 Technology를 접목시켜 탄생한 용어로 2010년 초반부터</p>
                <span>2022.04.29</span></a>
            </li>
            <li class="mcl_item"><a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                <h2><img src="/assets/images/store/map_content1.png"></h2>
                <h3>마테크를 써야 하는 이유</h3>
                <p>마케터에게 "마테크(Martch, Marketing + Technology)는 더 이상 선택이 아닌 필수의 영역이 되었습니다. 마테크의 시대 별 변화를 통해 디지털 마케팅</p>
                <span>2022.04.29</span></a>
            </li>

            <li class="mcl_item"><a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                <h2><img src="/assets/images/store/map_content2.png"></h2>
                <h3>MAPS 사용 팁</h3>
                <p>1. 간편검색을 통해 내가 맞는 서비스를 찾아보세요! 2. 마테크 사 클릭 후 서비스 신청을 해보세요! 3. 한 공간에서 손쉽게 관리해 보세요!</p>
                <span>2022.04.29</span></a>
            </li>

            <li class="mcl_item"><a href="{{ route('store.mapscontent_detail', ['content' => 3]) }}">
                <h2><img src="/assets/images/store/map_content3.png"></h2>
                <h3>앱 사용자를 충성고객으로 거듭나게 하기 위한 세가지 비법</h3>
                <p>여러분은 마테크(Martch)에 대해 잘 알고 게신가요? 마테크는 마케팅 Marketing + 기술 Technology를 접목시켜 탄생한 용어로 2010년 초반부터</p>
                <span>2022.04.29</span></a>
            </li>
        </ul>
    </div>
</div>

<script>
    var swiper = new Swiper(".mcl_swiper", {
    slidesPerView: "auto",
    spaceBetween: 38,
    pagination: false,
});
</script>

<script>
$(document).scroll(function() {
if ($(document).height() <= $(window).scrollTop() + $(window).height() + 10) {
for(let i=0; i<6; i++){
    $('.mcl_ul').append("<li class='mcl_item'><a href='{{ route('store.mapscontent_detail', ['content' => 3]) }}'><h2><img src='/assets/images/store/map_content2.png'></h2><h3>MAPS 사용 팁</h3><p>1. 간편검색을 통해 내가 맞는 서비스를 찾아보세요! 2. 마테크 사 클릭 후 서비스 신청을 해보세요! 3. 한 공간에서 손쉽게 관리해 보세요!</p><span>2022.04.29</span></a></li>")
}

}
})
</script>


@endsection


