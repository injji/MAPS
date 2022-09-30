@extends('layouts.front')

@section('content')
<section id="slide-banner">
    <div class="swiper">
        <div class="swiper-wrapper banners">
            <div id="sb-1" class="swiper-slide">
                <div class="banner-info">
                    <p class="banner-title">국내외 <b>마테크 기업의 서비스</b>를<br/> <b>한눈에</b> 보고 비교할 수 있다.</p>
                    <p class="banner-description">국내는 물론 전세계의 검증된 <b>마케팅 테크놀로지 서비스</b>를 경험해보세요!</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="py-10">
    <div class="container">
        <div class="container-title-center col-12 pb-5">쉽고 간단한 <b>설치 및 서비스 도입</b></div>
        <div id="easy-start" class="col-md-12 col-sm-12 ms-auto mt-5">
            <div class="row">
                <div class="col-md-4 col-sm-4 col-4">
                    <img src="{{ asset('images/front/icons/install.svg') }}" alt="">
                    <p>복잡한 설치 없음</p>
                </div>
                <div class="col-md-4 col-sm-4 col-4">
                    <img src="{{ asset('images/front/icons/dev.svg') }}" alt="">
                    <p>내부 개발자 불필요</p>
                </div>
                <div class="col-md-4 col-sm-4 col-4">
                    <img src="{{ asset('images/front/icons/agree.svg') }}" alt="">
                    <p>승인 시 즉시 서비스</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="py-3 pb-10" id="search">
    <div class="container">
        <!-- <mwc-textfield class="w-100" label="서비스 검색" outlined="" icontrailing="search" placeholder="마케팅 예산 금액으로 서비스를 검색하세요"></mwc-textfield> -->
        <form action="javascript:void(0);">
            <div class="col-md-12">
                <div class="row">
                    <div class="input-group">
                        <input class="form-control p-6" type="text" placeholder="마케팅 예산 금액으로 서비스를 검색하세요" name="budget" aria-describedby="search-btn">
                        <button class="btn btn-primary" id="search-btn" type="button"><i class="material-icons">search</i></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<section class="py-3 pb-10" id="service">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12 service mb-5">
                <div class="card">
                      <img class="card-img-top" src="{{ asset('images/front/services/service_1.png') }}" alt="Card image cap">
                      <div class="card-body">
                            <h5 class="card-title pt-3">팔찌 AR 가상착용, StyleAR</h5>
                            <p class="card-text mt-5">스타일AR은 구매자가 최신의 AR 가상 체험을 통해 비대면 쇼핑 환경에서 실제같은 착용을 구현하여 구매 전환을 유도하는 쇼핑몰 서비스입니다.</p>
                      </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12 service mb-5">
                <div class="card">
                      <img class="card-img-top" src="{{ asset('images/front/services/service_2.png') }}" alt="Card image cap">
                      <div class="card-body">
                            <h5 class="card-title pt-3">잘 되는 셀러들의 고객 응대 솔루션 '셀러게이트'</h5>
                            <p class="card-text mt-5">셀러님들, 이제 판매와 매출에만 집중하세요~ 관리하고 있는 쇼핑몰의 상담을 통합 관리할 수 있는 셀러 맞춤 고객 응대 솔루션, 셀러게이트</p>
                      </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12 service mb-5">
                <div class="card">
                      <img class="card-img-top" src="{{ asset('images/front/services/service_3.png') }}" alt="Card image cap">
                      <div class="card-body">
                            <h5 class="card-title pt-3">애드머스 (ADmerce)</h5>
                            <p class="card-text mt-5">게임, 경품 응모 등 쇼핑몰 방문자가 참여하는 이벤트를 만들어 보세요. 쇼핑몰 재방문 및 체류 시간이 늘고 상품 판매가 증대됩니다.</p>
                      </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12 service mb-5">
                <div class="card">
                      <img class="card-img-top" src="{{ asset('images/front/services/service_4.png') }}" alt="Card image cap">
                      <div class="card-body">
                            <h5 class="card-title pt-3">추천인 리워드</h5>
                            <p class="card-text mt-5">추천 기능을 통해 고객에게 다양한 혜택을 제공하여 고객 집객 효과를 상승시킬 수 있습니다. 합리적인 가격, 실용적인 서비스로 만족을 드리…</p>
                      </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12 service mb-5">
                <div class="card">
                      <img class="card-img-top" src="{{ asset('images/front/services/service_5.png') }}" alt="Card image cap">
                      <div class="card-body">
                            <h5 class="card-title pt-3">인플루언서 리워드</h5>
                            <p class="card-text mt-5">인플루언서로 지정된 고객에게 전용 URL이 발급되고 해당 URL을 통해서 접근한 다른 고객이 상품을 구입하면 리워드를 지급하는 추천인 리워드입니…</p>
                      </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12 service mb-5">
                <div class="card">
                      <img class="card-img-top" src="{{ asset('images/front/services/service_6.png') }}" alt="Card image cap">
                      <div class="card-body">
                            <h5 class="card-title pt-3">타임 매출 향상의 필수, 이벤트의 핵심 - 타임 세일</h5>
                            <p class="card-text mt-5">할인 프로모션 필수 APP 타임 세일 할인 기간, 할인 판매가 자동 설정 업데이트는 기본 설정 클릭만으로 다양한 스타일을 원하는 형태로 구현해보…</p>
                      </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12 service mb-5">
                <div class="card">
                      <img class="card-img-top" src="{{ asset('images/front/services/service_7.png') }}" alt="Card image cap">
                      <div class="card-body">
                            <h5 class="card-title pt-3">[15일 무료] 중소기업 통합 업무 시스템 (ERP)</h5>
                            <p class="card-text mt-5">구매의사 결정을 돕는 정보 시각화의 끝판왕! (옵션 선호도, 잔여 재고 수량, 최근 구매 알림, 프로모션 카운트다운)</p>
                      </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12 service mb-5">
                <div class="card">
                      <img class="card-img-top" src="{{ asset('images/front/services/service_8.png') }}" alt="Card image cap">
                      <div class="card-body">
                            <h5 class="card-title pt-3">하이버 연동</h5>
                            <p class="card-text mt-5">대한민국 1등 남성 앱 '하이버' 입점 쇼핑몰은 누구나 이용 가능한 상품 연동 서비스입니다.</p>
                      </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="py-10 bg-light text-center wt-company">
    <div class="container">
        <div class="container-title-center mb-5">우리와 함께하는 <b>제휴사</b></div>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-3 mb-5">
                <img src="{{ asset('images/front/companys/company_01.png') }}" alt="">
            </div>
            <div class="col-md-3 col-sm-6 col-3 mb-5">
                <img src="{{ asset('images/front/companys/company_02.png') }}" alt="">
            </div>
            <div class="col-md-3 col-sm-6 col-3 mb-5">
                <img src="{{ asset('images/front/companys/company_03.png') }}" alt="">
            </div>
            <div class="col-md-3 col-sm-6 col-3 mb-5">
                <img src="{{ asset('images/front/companys/company_01.png') }}" alt="">
            </div>
        </div>
    </div>
</section>
<section id="banner">
    <div class="bg-dark banners">
        <div class="banner-info">
            <p class="banner-title"><b>3,500여 고객사</b>를 대상으로<br/> 앱서비스 노출</p>
        </div>
        <img src="{{ asset('images/front/mid-banner_bg.png') }}" width="100%" alt="">
    </div>
</section>
<section class="py-10" id="agent">
    <div class="container">
        <div class="container-title-center col-12 pb-5">MAPS TREND <b>제휴 서비스</b></div>
        <div class="row col-12">
            <div class="col-md-12 col-sm-12 ms-auto feature mt-5">
                <div class="row">
                    <div class="col-md-6 col-sm-12 col-12 mb-3">
                        <span class="text-primary col-md-1 col-1 float-left">✓</span>
                        <p class="col-md-11 col-12"><b>아웃바운드</b> 영업팀 운영</p>
                    </div>
                    <div class="col-md-6 col-sm-12 col-12 mb-3">
                        <span class="text-primary col-md-1 col-1 float-left">✓</span>
                        <p class="col-md-11 col-12 ">바이앱스 앱 플랫폼 연동 API로 <b>차별화된 마케팅 서비스</b> 확대</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12 col-12 mb-3">
                        <span class="text-primary col-md-1 col-1 float-left">✓</span>
                        <p class="col-md-11 col-12 ">스크립트 설치 등을 위한 <b>리소스 Down</b></p>
                    </div>
                    <div class="col-md-6 col-sm-12 col-12 mb-3">
                        <span class="text-primary col-md-1 col-1 float-left">✓</span>
                        <p class="col-md-11 col-12">MAPS TREND 수집 데이터 <b>OPEN API 제공</b></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row pt-10 col-12 justify-content-center">
            <div class="col-md-3 col-sm-12 mb-3">
                <div class="card card-raised p-3">
                    <div class="card-body">
                        <h2 class="card-title">스크립트 어싸인</h2>
                        <h3 class="card-subtitle mb-3">Script Assign</h3>
                        <p class="card-text">제휴사 SDK 스크립트를 삽입하기 위해 고객사의 계정을 전달받거나 태그 작업 등의 리소스가 필요없습니다.</p>
                    </div>
                    <div class="card-actions ms-auto">
                        <div class="card-action-icons">
                            <button class="btn btn-outline-primary btn-sm" type="button"><i class="material-icons">add</i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12 mb-3">
                <div class="card card-raised p-3">
                    <div class="card-body">
                        <h2 class="card-title">알림 메시지</h2>
                        <h3 class="card-subtitle mb-3">Notification</h3>
                        <p class="card-text">앱을 운영하는 고객사의 고객에게 개별 알림 메시지를 발송할 수 있어 제휴사의 서비스 영역을 확장/개발할 수 있습니다.</p>
                    </div>
                    <div class="card-actions ms-auto">
                        <div class="card-action-icons">
                            <button class="btn btn-outline-primary btn-sm" type="button"><i class="material-icons">add</i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12 mb-3">
                <div class="card card-raised p-3">
                    <div class="card-body">
                        <h2 class="card-title">포스트</h2>
                        <h3 class="card-subtitle mb-3">Postback</h3>
                        <p class="card-text">고객사의 앱 또는 웹에서 발생되는 전환 이벤트 포스트백을 이용하여 성과에 대한 최적화를 가능하게 합니다.</p>
                    </div>
                    <div class="card-actions ms-auto">
                        <div class="card-action-icons">
                            <button class="btn btn-outline-primary btn-sm" type="button"><i class="material-icons">add</i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="py-10 bg-light text-center wt-company">
    <div class="container">
        <div class="container-title-center mb-5">우리와 함께하는 <b>고객사</b></div>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-3 mb-5">
                <img src="{{ asset('images/front/companys/company_01.png') }}" alt="">
            </div>
            <div class="col-md-3 col-sm-6 col-3 mb-5">
                <img src="{{ asset('images/front/companys/company_02.png') }}" alt="">
            </div>
            <div class="col-md-3 col-sm-6 col-3 mb-5">
                <img src="{{ asset('images/front/companys/company_03.png') }}" alt="">
            </div>
            <div class="col-md-3 col-sm-6 col-3 mb-5">
                <img src="{{ asset('images/front/companys/company_01.png') }}" alt="">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-3 mb-5">
                <img src="{{ asset('images/front/companys/company_01.png') }}" alt="">
            </div>
            <div class="col-md-3 col-sm-6 col-3 mb-5">
                <img src="{{ asset('images/front/companys/company_02.png') }}" alt="">
            </div>
            <div class="col-md-3 col-sm-6 col-3 mb-5">
                <img src="{{ asset('images/front/companys/company_03.png') }}" alt="">
            </div>
            <div class="col-md-3 col-sm-6 col-3 mb-5">
                <img src="{{ asset('images/front/companys/company_01.png') }}" alt="">
            </div>
        </div>
    </div>
</section>
<section class="py-10" id="qna">
    <div class="container">
        <div class="container-title-center col-12 pb-5">
            <b>서비스 고객 문의</b>
            <p>서비스 및 입점 문의를 작성하여 보내주시면 최대한 빨리 연락드리겠습니다.</p>
        </div>
        <form action="javascript:void(0);">
            <div class="col-md-9 col-sm-12 m-auto mt-5">
                <div class="row mb-3">
                    <input class="form-control p-6" value="" id="name" name="name" type="text" placeholder="담당자 이름을 입력하세요">
                </div>
                <div class="row mb-3">
                    <input class="form-control p-6" value="" id="email" name="email" type="text" placeholder="담당자 이메일을 입력하세요">
                </div>
                <div class="row mb-3">
                    <input class="form-control p-6" value="" id="tel" name="tel" type="text" placeholder="연락 받으실 전화번호를 입력하세요">
                </div>
                <div class="row mb-3">
                    <select class="form-select px-6 py-4" depth="1" name="category[]" onchange="" id="category">
                            <option>선택해주세요.</option>
                            <option onclick="" value="1">광고주</option>
                    </select>
                </div>
                <div class="row mb-3">
                    <textarea class="form-control p-6" id="exampleFormControlTextarea" rows="5" placeholder="문의하실 내용을 입력하세요"></textarea>
                </div>
                <div class="row mb-3">
                    <button type="button" onclick="javascript:void(0);" class="btn btn-primary p-4 font-weight-bold justify-content-center" disabled>보내기</button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
