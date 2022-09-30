<!-- CCC 20220613 -->
<!DOCTYPE html>
<html lang="{{ Lang::locale() }}" dir="ltr">
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
    <meta http-equiv="x-ua-compatible" content="ie=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=yes, target-densitydpi=medium-dpi">  
    <link rel="stylesheet" href="{{ asset('/css/store/mkcp_pay.css') }}">
    <script type="text/javascript">
        /* kcp web 결제창 호츨 (변경불가) */
        function call_pay_form()
        {
            var v_frm = document.order_info;
            var PayUrl = v_frm.PayUrl.value;
            // 인코딩 방식에 따른 변경 -- Start
            if(v_frm.encoding_trans == undefined)
            {
                v_frm.action = PayUrl;
            }
            else
            {
                // encoding_trans "UTF-8" 인 경우
                if(v_frm.encoding_trans.value == "UTF-8")
                {
                    v_frm.action = PayUrl.substring(0,PayUrl.lastIndexOf("/"))  + "/jsp/encodingFilter/encodingFilter.jsp";
                    v_frm.PayUrl.value = PayUrl;
                }
                else
                {
                    v_frm.action = PayUrl;
                }
            }
        
            if (v_frm.Ret_URL.value == "")
            {
                /* Ret_URL값은 현 페이지의 URL 입니다. */
                alert("연동시 Ret_URL을 반드시 설정하셔야 됩니다.");
                return false;
            }
            else
            {
                v_frm.submit();
            }
        }

        /* kcp 통신을 통해 받은 암호화 정보 체크 후 결제 요청 (변경불가) */
        function chk_pay()
        {
            self.name = "tar_opener";
            var pay_form = document.pay_form;
            
            if (pay_form.res_cd.value != "" )
            {   
                if (pay_form.res_cd.value != "0000" )
                {
                    if (pay_form.res_cd.value == "3001")
                    {
                        alert("사용자가 취소하였습니다.");
                    }
                    pay_form.res_cd.value = "";
                    location.href = "{{ route('store.service.detail', [ 
                        'service' => $service_id
                    ]) }}"; // 샘플에서는 거래등록 페이지로 이동
                }
            }
            if (pay_form.enc_info.value)
                pay_form.submit();
        }
    </script>
</head>
<body onload="chk_pay();">
<div class="wrap">
        <!-- 주문정보 입력 form : order_info -->
        <form name="order_info" method="post">
            <!-- header -->
            <div class="header">
                <a onclick="history.back();" class="btn-back"><span>뒤로가기</span></a>
                <h1 class="title">주문/결제</h1>
            </div>
            <!-- //header -->
            <!-- contents -->
            <div id="skipCont" class="contents">
                <!--
                    ==================================================================
                        1. 주문정보 입력                                                       
                    ------------------------------------------------------------------
                                      결제에 필요한 주문 정보를 입력 및 설정합니다.                           
                    ------------------------------------------------------------------
                -->
                <!-- 주문정보 -->
                <h2 class="title-type-3">주문정보</h2>
                <ul class="list-type-1">
                    <!-- 주문번호(ordr_idxx) -->
                    <input type="hidden" name="ordr_idxx" value="<?php echo $ordr_idxx; ?>" />
                    <!-- 상품명(good_name) -->
                    <li>
                        <div class="left"><p class="title">상품명</p></div>
                        <div class="right">
                            <div class="ipt-type-1 pc-wd-2">
                                <input type="text" name="good_name" value="<?php echo $good_name; ?>" readonly />
                            </div>
                        </div>
                    </li>
                    <!-- 결제금액(good_mny) - ※ 필수 : 값 설정시 ,(콤마)를 제외한 숫자만 입력하여 주십시오. -->
                    <li>
                        <div class="left"><p class="title">상품금액</p></div>
                        <div class="right">
                            <div class="ipt-type-1 gap-2 pc-wd-2">
                                <input type="text" name="good_mny" value="<?php echo $good_mny; ?>" maxlength="9" readonly />
                                <span class="txt-price">원</span>
                            </div>
                        </div>
                    </li>
                    <!-- 주문자명(buyr_name) -->
                    <li>
                        <div class="left"><p class="title">주문자명</p></div>
                        <div class="right">
                            <div class="ipt-type-1 pc-wd-2">
                                <input type="text" name="buyr_name" value="<?php echo $buyr_name;?>" readonly />
                            </div>
                        </div>
                    </li>
                    <!-- 주문자 연락처1(buyr_tel1) -->
                    <li>
                        <div class="left"><p class="title">전화번호</p></div>
                        <div class="right">
                            <div class="ipt-type-1 pc-wd-2">
                                <input type="text" name="buyr_tel1" value="" />
                            </div>
                        </div>
                    </li>
                    <!-- 휴대폰번호(buyr_tel2) -->
                    <li>
                        <div class="left"><p class="title">휴대폰번호</p></div>
                        <div class="right">
                            <div class="ipt-type-1 pc-wd-2">
                                <input type="text" name="buyr_tel2" value="" />
                            </div>
                        </div>
                    </li>
                    <!-- 주문자 E-mail(buyr_mail) -->
                    <li>
                        <div class="left"><p class="title">이메일</p></div>
                        <div class="right">
                            <div class="ipt-type-1 pc-wd-2">
                                <input type="text" name="buyr_mail" value="<?php echo $buyr_mail;?>" readonly/>
                            </div>
                        </div>
                    </li>
                </ul>
                <div Class="Line-Type-1"></div>
                <ul class="list-btn-2">
                    <li class="pc-only-show"><a onclick="history.back();" class="btn-type-3 pc-wd-2">뒤로</a></li>
                    <li><a href="#none" onclick="call_pay_form();" class="btn-type-2 pc-wd-3">결제요청</a></li> 
                </ul>
            </div>
            <!-- //contents -->

            <!--//footer-->
            <!-- 공통정보 -->
            <input type="hidden" name="req_tx"          value="pay" />              <!-- 요청 구분 -->
            <input type="hidden" name="shop_user_id" id="shop_user_id" value="" />
            <input type="hidden" name="shop_name"       value="" />        <!-- 사이트 이름 --> 
            <input type="hidden" name="site_cd"         value="<?php echo $site_cd; ?>" />    <!-- 사이트 코드 -->
            <input type="hidden" name="currency"        value="410"/>               <!-- 통화 코드 -->
            <!-- 인증시 필요한 파라미터(변경불가)-->
            <input type="hidden" name="escw_used"       value="N" />
            <input type="hidden" name="pay_method"      value="<?php echo $pay_method; ?>" />
            <input type="hidden" name="ActionResult"    value="<?php echo $actionResult; ?>" />
            <input type="hidden" name="van_code"        value="<?php echo $van_code; ?>" />
            <!-- 신용카드 설정 -->
            <input type="hidden" name="quotaopt"        value="12"/> <!-- 최대 할부개월수 -->
            <!-- 가상계좌 설정 -->
            <input type="hidden" name="ipgm_date"       value="" />
            <!-- 리턴 URL (kcp와 통신후 결제를 요청할 수 있는 암호화 데이터를 전송 받을 가맹점의 주문페이지 URL) -->
            <input type="hidden" name="Ret_URL"         value="<?php echo $Ret_URL; ?>" />
            <!-- 화면 크기조정 -->
            <input type="hidden" name="tablet_size"     value="1.0 " />
            <!-- 추가 파라미터 ( 가맹점에서 별도의 값전달시 param_opt 를 사용하여 값 전달 ) -->
            <input type="hidden" name="param_opt_1"    value="<?php echo $pay_option; ?>" />
            <input type="hidden" name="param_opt_2"    value="<?php echo $service_id; ?>" />
            <input type="hidden" name="param_opt_3"    value="<?php echo $site_id; ?>" />

            <!-- 거래등록 응답값 -->
            <input type="hidden" name="approval_key" id="approval" value="<?php echo $approvalKey; ?>"/>
            <input type="hidden" name="traceNo"                    value="<?php echo $traceNo; ?>" />
            <input type="hidden" name="PayUrl"                     value="<?php echo $PayUrl; ?>" />
            <!-- 인증창 호출 시 한글깨질 경우 encoding 처리 추가 (**인코딩 네임은 대문자)   -->
            <input type="hidden" name="encoding_trans" value="UTF-8" />
            
            <!-- 
            =======================================
             옵션 정보                                                               
            --------------------------------------
              ※ 옵션 - 결제에 필요한 추가 옵션 정보를 입력 및 설정합니다.
            --------------------------------------
            -->
            <!--  카드사 리스트 설정
            예) 비씨카드와 신한카드 사용 설정시 -->
            <!-- <input type="hidden" name="used_card"    value="CCBC:CCLG"> -->
            <!-- 무이자 옵션
                    ※ 설정할부    (가맹점 관리자 페이지에 설정 된 무이자 설정을 따른다) - "" 로 설정
                    ※ 일반할부    (KCP 이벤트 이외에 설정 된 모든 무이자 설정을 무시한다) - "N" 로 설정
                    ※ 무이자 할부 (가맹점 관리자 페이지에 설정 된 무이자 이벤트 중 원하는 무이자 설정을 세팅한다)   - "Y" 로 설정  -->
            <!-- <input type="hidden" name="kcp_noint"       value=""/> --> 
            <!-- 무이자 설정
                    ※ 주의 1 : 할부는 결제금액이 50,000 원 이상일 경우에만 가능
                    ※ 주의 2 : 무이자 설정값은 무이자 옵션이 Y일 경우에만 결제 창에 적용
                    예) BC 2,3,6개월, 국민 3,6개월, 삼성 6,9개월 무이자 : CCBC-02:03:06,CCKM-03:06,CCSS-03:06:04 -->
            <!-- <input type="hidden" name="kcp_noint_quota" value="CCBC-02:03:06,CCKM-03:06,CCSS-03:06:09"/> --> 
            
            <!-- KCP는 과세상품과 비과세상품을 동시에 판매하는 업체들의 결제관리에 대한 편의성을 제공해드리고자, 
               복합과세 전용 사이트코드를 지원해 드리며 총 금액에 대해 복합과세 처리가 가능하도록 제공하고 있습니다
               복합과세 전용 사이트 코드로 계약하신 가맹점에만 해당이 됩니다
               상품별이 아니라 금액으로 구분하여 요청하셔야 합니다
               총결제 금액은 과세금액 + 부과세 + 비과세금액의 합과 같아야 합니다. 
               (good_mny = comm_tax_mny + comm_vat_mny + comm_free_mny)
            -->
            <!-- <input type="hidden" name="tax_flag"       value="TG03"> -->  <!-- 변경불가    -->
            <!-- <input type="hidden" name="comm_tax_mny"   value=""    > -->  <!-- 과세금액    --> 
            <!-- <input type="hidden" name="comm_vat_mny"   value=""    > -->  <!-- 부가세     -->
            <!-- <input type="hidden" name="comm_free_mny"  value=""    > -->  <!-- 비과세 금액 --> 
            
                
            <!-- 결제창 한국어/영어 설정 옵션 (Y : 영어) -->
            <!-- <input type="hidden" name="eng_flag"        value="Y"/> -->                      
                  
            <!-- 가맹점에서 관리하는 고객 아이디 설정을 해야 합니다. 상품권 결제 시 반드시 입력하시기 바랍니다. -->
            <!-- <input type="hidden" name="shop_user_id"    value=""/> --> 
            
            <!-- 복지포인트 결제시 가맹점에 할당되어진 코드 값을 입력해야합니다. -->
            <!-- <input type="hidden" name="pt_memcorp_cd"   value=""/> --> 
                
            <!-- 결제창 현금영수증 노출 설정 옵션 (Y : 노출) -->
            <!-- <input type="hidden" name="disp_tax_yn"     value="Y"/> -->
        </form>
    </div>
    <form name="pay_form" method="post" action="/mreqsvcpay">
        <input type="hidden" name="req_tx"         value="<?php echo $req_tx; ?>" />               <!-- 요청 구분          -->
        <input type="hidden" name="res_cd"         value="<?php echo $res_cd; ?>" />               <!-- 결과 코드          -->
        <input type="hidden" name="site_cd"        value="<?php echo $site_cd; ?>" />              <!-- 사이트 코드      -->
        <input type="hidden" name="tran_cd"        value="<?php echo $tran_cd; ?>" />              <!-- 트랜잭션 코드      -->
        <input type="hidden" name="ordr_idxx"      value="<?php echo $ordr_idxx; ?>" />            <!-- 주문번호           -->
        <input type="hidden" name="good_mny"       value="<?php echo $good_mny; ?>" />             <!-- 휴대폰 결제금액    -->
        <input type="hidden" name="good_name"      value="<?php echo $good_name; ?>" />            <!-- 상품명             -->
        <input type="hidden" name="buyr_name"      value="<?php echo $buyr_name; ?>" />            <!-- 주문자명           -->
        <input type="hidden" name="buyr_tel1"      value="<?php echo $buyr_tel1; ?>" />            <!-- 주문자 전화번호    -->
        <input type="hidden" name="buyr_tel2"      value="<?php echo $buyr_tel2; ?>" />            <!-- 주문자 휴대폰번호  -->
        <input type="hidden" name="buyr_mail"      value="<?php echo $buyr_mail; ?>" />            <!-- 주문자 E-mail      -->
        <input type="hidden" name="enc_info"       value="<?php echo $enc_info; ?>" />
        <input type="hidden" name="enc_data"       value="<?php echo $enc_data; ?>" />
        <input type="hidden" name="use_pay_method" value="<?php echo $use_pay_method; ?>" />
        <input type="hidden" name="cash_yn"        value="<?php echo $cash_yn; ?>" />              <!-- 현금영수증 등록여부-->
        <input type="hidden" name="cash_tr_code"   value="<?php echo $cash_tr_code; ?>" />
        <!-- 추가 파라미터 -->
        <input type="hidden" name="param_opt_1"    value="<?php echo $pay_option; ?>" />
        <input type="hidden" name="param_opt_2"    value="<?php echo $service_id; ?>" />
        <input type="hidden" name="param_opt_3"    value="<?php echo $site_id; ?>" />
    </form>
<!--//wrap-->
</body>
</html>