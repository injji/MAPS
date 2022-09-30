@extends('layouts.client')

@section('content')

<div class="guide_script_wrap">
    <div class="guide_script">
        <h1>스크립트 설치 가이드</h1>
        <div>
            <ul>
                <li>· MAPS 공통 스크립트는 서비스신청 및 이용을 위해 사이트 별 최초 1회 삽입해야 하며, 이용하는 모든 페이지에 설치되어야 합니다. </li>
                <li>· 스크립트 코드는 다음과 같습니다. (&lt;!-- --&gt; 주석 영역)<button data-toggle="modal" data-target="#script_btn">스크립트 복사</button></li>
                <li>· 스크립트의 삽입 위치는 어디든 상관없으나, HTML의 body 부분의 끝인 &lt;/body&gt; 바로 위에 삽입하는 것을 추천드립니다.</li>
            </ul>

            <p>
                · 솔루션 별 스크립트 설치 방법
            </p>
            <ol>
                <li><em>1</em>스트립트 관리 : cafe24 / 고도몰</li>
                <li><em>2</em>디자인 편집 : cafe24 / 고도몰 / 메이크샵</li>
                <li><em>3</em>HTML의 body 부분의 끝인 &lt;/body&gt; 바로 위에 직접 삽입 : 기타 홈페이지</li>
            </ol>
        </div>

    </div>

    <div class="shop_tab">
        <div class="status_shop">
            <ul class="tabnav">
                <li><a href="#tab01">cafe24</a></li>
                <li><a href="#tab02">고도몰</a></li>
                <li><a href="#tab03">메이크샵</a></li>
            </ul>
            <p>스크립트 설치는 해당 쇼핑몰 관리자 로그인 후 진행 가능합니다.</p>

            <button data-toggle="modal" data-target="#script_setting">설치요청</button>
        </div>

        <div class="tabcontent">
        <div id="tab01">
            <img src="{{ asset('images\agent\cafe1.png') }}">
            <img src="{{ asset('images\agent\cafe2.png') }}">
        </div>
        <div id="tab02">
            <img src="{{ asset('images\agent\godo1.png') }}">
            <img src="{{ asset('images\agent\godo2.png') }}">
        </div>
        <div id="tab03">
            <img src="{{ asset('images\agent\ms1.png') }}">
        </div>
        </div>
    </div><!--tab-->

</div>


<!-- Modal 문의 하기 -->
<div class="modal fade contact_modal" id="script_btn" tabindex="-1" role="dialog"
aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <h2>스크립트</h2>
            <textarea id="script_txt"  style="resize: none;" readonly><!-- MAPS TREND SCRIPT 삭제하지 마세요 --> <script async src="{{ config('app.pre_url').'://'.config('app.domain.api') }}/common/js?id={{ \Auth::user()->account }}&sid={{ Auth::user()->current_site->client_sid }}"></script> <!-- MAPS TREND SCRIPT 삭제하지 마세요 --></textarea>
            <div class="two_btn">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('sub.sub-close')</button>
                <button id="copy_script"  >복사</button>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Modal 안내 -->
<div class="modal fade contact_modal" id="script_setting" tabindex="-1" role="dialog"
aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <h2>안내</h2>
            <p>
                스크립트 설치 요청은 직접 스크립트를 삽입하기 어려울 경우 요청해 주셔야 하며 요청 시 관리자 확인 후 진행되므로 시간이 소요됩니다.<br><br> 빠른 서비스 이용 및 구매를 원하시면 안내된 가이드를 따라 직접 스트립트 삽입을 진행하시면 보다 빠른 이용이 가능합니다.
            </p>
            <div class="two_btn">
                <button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
                <button  data-toggle="modal" data-target="#script_setting2" data-dismiss="modal" >다음</button>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Modal 신청완료 -->
<div class="modal fade contact_modal" id="script_setting" tabindex="-1" role="dialog"
aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <h2>신청완료 되었습니다.</h2>
            <p>
                스크립트 요청은 관리자 확인 후 진행되며<br>
                영업시간 이외에는 확인이 불가능합니다.<br>
                완료 시, 회원정보에 입력된 메일,sms를 통해 안내됩니다.
            </p>
            <div class="two_btn">
                {{-- <button type="button" class="btn btn-default" data-dismiss="modal">확인</button> --}}
                <button type="button" data-dismiss="modal">확인</button>
            </div>
        </div>
    </div>
</div>
</div>


<!-- Modal 솔루션 정보 -->
<div class="modal fade contact_modal" id="script_setting2" tabindex="-1" role="dialog"
aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <h2>솔루션 정보를 입력해주세요.</h2>
            <p>
                스크립트 삽입을 위해 솔루션 로그인 정보가 필요합니다.
            </p>

            <form id="dataForm">
                <ul>
                    <li>
                        <h3>사이트</h3>
                        <select class="form-select" name="site_name">
                            @if( $sites->count() > 0 )
                                @foreach($sites as $site)
                                    <option value="{{ $site->name }}" {{ ($site->name == \Auth::user()->current_site->name ) ? 'selected' : '' }}>{{ $site->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </li>

                    <li>
                        <h3>솔루션</h3>
                        <select class="form-select" id="solution_shop" name="solution_shop">
                            <option value="1" {{ (1 == \Auth::user()->current_site->hostname ) ? 'selected' : '' }}>Cafe24</option>
                            <option value="3" {{ (3 == \Auth::user()->current_site->hostname ) ? 'selected' : '' }}>고도몰</option>
                            <option value="2" {{ (2 == \Auth::user()->current_site->hostname ) ? 'selected' : '' }}>메이크샵</option>
                            <option value="0" {{ (0 == \Auth::user()->current_site->hostname ) ? 'selected' : '' }}>기타</option>
                        </select>
                    </li>

                    <li>
                        <h3>관리자 URL</h3>
                        <input type="text" name="admin_url" placeholder="URL을 입력하세요.">
                    </li>

                    <li style="position: relative">
                        <h3>부운영자 아이디<em class="bubble_btn1">?</em></h3>
                        <input type="text" name="account" placeholder="아이디를 입력하세요.">

                        <div class="bubble_txt">
                            <div class="solution1">
                                <h5>Cafe24 (메뉴권한설정)</h5>
                                <p>상점관리 : 검색엔진 최적화(SEO) / 디자인 관리 모바일 쇼핑몰</p>
                            </div>

                            <div class="solution2">
                                <h5>고도몰 (메뉴권한설정)</h5>
                                <p>기본설정 : 외부서비스 설정 / 디자인 / 모바일 샵</p>
                            </div>

                            <div class="solution3">
                                <h5>메이크샵 (메뉴권한설정)</h5>
                                <p>모바일샵 / 개별디자인</p>
                            </div>
                        </div>
                    </li>

                    <li>
                        <h3>부운영자 비밀번호</h3>
                        <input type="text" name="password" placeholder="비밀번호를 입력하세요.">
                    </li>
                </ul>
                <div class="two_btn">
                    <button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
                    <button type="button" onclick="script_request()">요청</button>
                </div>
            </form>

        </div>
    </div>
</div>
</div>

@endsection


@push('scripts')
<script>


    $('.bubble_btn1').mouseover(function(){
        var state = $('#solution_shop option:selected').val();
        if ( state == 1 ) {
            $('.bubble_txt').html('<h5>Cafe24 (메뉴권한설정)</h5><p>상점관리 : 검색엔진 최적화(SEO) / 디자인 관리 모바일 쇼핑몰</p>');
        } else if(state == 2) {
            $('.bubble_txt').html('<h5>메이크샵 (메뉴권한설정)</h5><p>모바일샵 / 개별디자인</p>');
        } else if(state == 3) {
            $('.bubble_txt').html('<h5>고도몰 (메뉴권한설정)</h5><p>기본설정 : 외부서비스 설정 / 디자인 / 모바일 샵</p>');
        }

        $('.bubble_txt').fadeIn();
    });

    $('.bubble_btn1').mouseout(function(){
        $('.bubble_txt').fadeOut();
    });


    $('#solution_shop').on('change', function(){
        var state = $('#solution_shop option:selected').val();
        if(state == 0){
            $('.bubble_btn1').hide();
        } else {
            $('.bubble_btn1').show();
        }
    });




    $(function(){
    $('.bubble_btn1').hide();
    $('.tabcontent > div').hide();
    $('.tabnav a').click(function () {
        $('.tabcontent > div').hide().filter(this.hash).fadeIn();
        $('.tabnav a').removeClass('active');
        $(this).addClass('active');
        return false;
    }).filter(':eq(0)').click();
    });

    document.getElementById("copy_script").onclick = function(){
        const textArea = document.getElementById("script_txt");
        textArea.select();
        document.execCommand('copy');
        alert('복사되었습니다.');
    }

    function script_request(){

        let request = new FormData($('#dataForm')[0]);

        $.ajax({
            url: '/script/request',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                console.log(response);
                if(response.code == 200){
                    alert('등록 되었습니다.');
                    location.reload();
                }else{
                    alert(response.error);
                }

            }
        })

    }


</script>
@endpush

