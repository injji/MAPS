@extends('layouts.auth')

@section('body')

@include('layouts.sub_header')

<div class="detail_faq base_wrap dfaq">
    <h1>FAQ</h1>
    @foreach ( $faq as $item )
        <div class="d_faq">
            <div class="Qq" faq_id="{{ $item->id }}">
                <em>Q.</em>
                <p>
                    {{ $item->question }}
                </p>
                <span><img src="/assets/images/store/plus.svg"></span>
            </div>
            <div class="Aa">
                <div>
                    <em>A.</em>
                    <p>
                        {{ $item->answer }}
                    </p>
                </div>
            </div>
        </div>
    @endforeach


    {{-- <div class="d_faq">
        <div class="Qq">
            <em>Q.</em>

            <p>
                서비스를 이용하려면 어떻게 해야 하나요? 서비스를 이용하려면 어떻게 해야 하나요?
            </p>

            <span><img src="/assets/images/store/plus.svg"></span>
        </div>

        <div class="Aa">
            <div>
                <em>A.</em>
                <p>
                    누구나 쉽고 간편하게 사용할 수 있습니다. ‘앱 설치하기’ 클릭 한번으로 간편하게 설치해 보세요.<br><br>

                    [설치 방법]<br>
                    카페24 주계정으로 카페 24 앱스토어에 접속 -> 카페24 앱스토어 우측 상단 검색에 ‘펍플’ 또는 ‘PUPPLE’ 검색 -> ‘[180일무료] 펍플 (PUPPLE)’
                    클릭 후
                    ‘설치하기’ 버튼 클릭 -> 사용 권한 및 이용 동의에서 ‘동의함’ 클릭 -> 설치 완료 (상품데이터 연동까지 최소 30분에서 최대 2시간 소요)
                </p>
            </div>


        </div>

    </div> --}}
</div>



@endsection

@push('scripts')
<script src="{{ asset('js/page/store-service-detail.js') }}" charset="utf-8"></script>
<script>
    $('.Qq').on('click', function () {
        function slideDown(target) {
            slideUp();
            $(target).addClass('on').next().slideDown();
            console.log($(target).attr('faq_id'));

            let request = new FormData();
            request.set('id',$(target).attr('faq_id'));
            $.ajax({
                url: '/store/faq/hits',
                method: 'post',
                data: request,
                contentType: false,
                processData: false,
                success: (response) => {
                    if(response.code == 200){

                    }
                },
                error: (e) => {
                    console.log(e.responseJSON)
                }

            })
        }

        function slideUp() {
            $('.Qq').removeClass('on').next().slideUp();
        };
        $(this).hasClass('on') ? slideUp() : slideDown($(this));

    })
</script>
@endpush
