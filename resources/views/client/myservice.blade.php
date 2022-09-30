@extends('layouts.client')

@section('content')

<div class="my">
    <div class="my_tit">
        <h2 style="font-size: 24px;">@lang('client.client_myservice')</h2>
        <div>
            <ul>
                <li><a href="{{ route('client.myservice') }}">@lang('process.all') <span>{{ $cnt1 }}</span></a></li>
                <li><a href="{{ route('client.myservice.search', 2) }}">@lang('process.using') <span>{{ $cnt2 }}</span></a></li>
                <li><a href="{{ route('client.myservice.search', 1) }}">@lang('process.apply') <span>{{ $cnt3 }}</span></a></li>
                <li><a href="{{ route('client.myservice.search', 3) }}">@lang('process.expired') <span>{{ $cnt4 }}</span></a></li>
                <li><a href="{{ route('client.myservice.search', 4) }}">@lang('process.stop') <span>{{ $cnt5 }}</span></a></li>
                <li><a href="{{ route('client.myservice.search', 5) }}">@lang('process.dell') <span>{{ $cnt6 }}</span></a></li>
            </ul>
            <a href="{{ route('store.home') }}" target="_blank">@lang('service.txt1')</a>
        </div>
    </div>

    <div class="my_content">
        <div class="my_ct">

            @foreach ($services as $service)
                <div class="mcl" {!! ($service->process == 5) ? 'style="height: 333px"' : ''  !!}>
                    <div class="service_app">
                        <div class="img">
                            <a href="{{ route('store.service.detail' , $service->service_id) }}">
                                <img src="{{ $service->service->icon ? Storage::url($service->service->icon) : asset('images/xbox.png') }}">
                            </a>
                        </div>
                        <div class="app_con">
                            <ul class="app_ul">

                                <li>
                                    @if($service->status == 1)
                                        <div class="setting_mi">
                                            <a href="{{ $service->set_req_url }}" target="_blank">@lang('button.complete_setting')
                                                <svg width="6" height="10" viewBox="0 0 3 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1 1L2.5 2.5L1 4" stroke="#007C4F" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                            </a>
                                        </div>
                                    @endif

                                    @if($service->process == 1)
                                        <span id="app_pro" class="b_black">{{ $service->process_text ?? '' }}</span>
                                    @elseif($service->process == 2)
                                        <span id="app_pro" class="b_green">{{ $service->process_text ?? '' }}</span>
                                    @elseif($service->process == 3)
                                        <span id="app_pro" class="b_gray">{{ $service->process_text ?? '' }}</span>
                                    @elseif($service->process == 4)
                                        <span id="app_pro" class="b_red">{{ $service->process_text ?? '' }}</span>
                                    @elseif($service->process == 5)
                                        {{-- <span id="app_pro" class="b_red">{{ $service->process_text ?? '' }}</span> --}}
                                        <a href="{{ route('store.service.detail' , $service->service_id) }}"><span id="app_pro" class="b_green_line" style="padding: 0 20px">재신청</span></a>
                                    @else
                                        <span id="app_pro" class="b_gray">{{ $service->process_text ?? '' }}</span>
                                    @endif

                                    @if($service->process != 5)
                                        <button class="app_btn_0">
                                            <i class="material-icons leading-icon">settings</i>
                                        </button>
                                        <div id="app_btn_set" class="app_btn_set">
                                            <ul>
                                                <li><a href="javascript:alert('{{ $service->created_at->format('Y.m.d') }}')">@lang('service.txt2')</a></li>
                                                <li><a href="javascript:inquiryWrite({{ $service }})">@lang('service.txt3')</a></li>
                                                <li>
                                                    @if($service->payment->count() > 0)
                                                        <a href="javascript:servicePayModal1({{ $service->id }}, '{{ $service->service->icon }}', '{{ $service->service->name }}')">@lang('service.txt4')</a>
                                                    @else
                                                        <a href="javascript:serviceFreeModal({{ $service->id }})">@lang('service.txt4')</a>
                                                    @endif
                                                </li>
                                                @if($service->process == 4)
                                                <li>
                                                    {{-- <a data-toggle="modal" data-target="#del_btn" style="cursor: pointer">삭제</a> --}}
                                                    <a href="javascript:serviceDelModal({{ $service->id }})">@lang('service.txt24')</a>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif

                                </li>
                                @if($service->service_start_at && $service->service_end_at && $service->process != 5)
                                    <li>
                                        @if($service->service->free_term == 99999)
                                            ∞
                                        @else
                                            {{ $service->service_end_at->format('Y-m-d') }}<br>
                                            D-{{ strtotime($service->service_end_at) - strtotime(date('Y-m-d')) < 0 ? 0 : ceil((strtotime($service->service_end_at) - strtotime(date('Y-m-d'))) / 86400) }}
                                        @endif
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="service_app2">
                        <a href="{{ route('store.service.detail' , $service->service_id) }}">
                            <h3 style="font-size: 18px;">{{ $service->service->name }}</h3>
                            <p style="font-size: 12px;">{{ $service->service->service_info }}</p>
                        </a>


                        @if($service->process != 5)
                            <div class="my_btn_wrap">
                                <div class="my_btn" >
                                    @if($service->service->url)
                                        <a href="{{ $service->service->url }}" target="_blank"  style="font-size: 14px;">@lang('client.client_set')</a>
                                    @else
                                        <a href="javascript:void(0)">@lang('client.client_set')</a>
                                    @endif
                                </div>
                            @if($service->review_flag == 1)
                                <div class="my_btn" >
                                    <button type="button" onclick="reviewModal({{ $service }})">@lang('button.write_review')</button>
                                </div>
                            @endif
                            @if($service->service_start_at && ceil((strtotime($service->service_end_at) - strtotime(date('Y-m-d'))) / 86400) < 30)
                                <div class="my_btn" >
                                    @if($service->service->in_app_payment == 1)
                                        <a href="{{ $service->service->url }}" style="padding:0" target="_blank">
                                            <button type="button">@lang('button.extend')</button>
                                        </a>
                                    @else
                                        <button type="button" onclick="extend_service({{ $service->id }}, {{ $service->service_id }})">@lang('button.extend')</button>
                                    @endif
                                </div>
                            @elseif($service->process == 4)
                                <div class="my_btn 111">
                                    <button type="button" onclick="updateProcess({{ $service->id }}, 0)">@lang('button.apply')</button>
                                </div>
                            @endif

                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<script>

</script>

<div class="modal fade service_stop" id="del_btn" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                {{-- <h2>@lang('service.txt5')</h2> --}}
				<p>서비스 이용내역을 다시한번 확인해 주세요.<br>
                확인 시 '삭제' 항목으로 이동됩니다.</p>
				<ul>
					<li><button type="button" data-bs-dismiss="modal" aria-label="Close">@lang('button.cancel')</button></li>
					<li><button type="button" onclick="updateProcess(0, 5)">@lang('button.confirm')</button></li>
				</ul>
            </div>

        </div>
    </div>
</div>


<div class="modal fade service_stop" id="servicefree" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h2>@lang('service.txt5')</h2>
				<p>@lang('service.txt6')</p>
				<ul>
					<li><button type="button" data-bs-dismiss="modal" aria-label="Close">@lang('button.cancel')</button></li>
					<li><button type="button" onclick="updateProcess(0, 4)">@lang('button.confirm')</button></li>
				</ul>
            </div>

        </div>
    </div>
</div>

<div class="modal fade service_stop" id="servicepay1" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h2>@lang('service.txt7')</h2>
				<p>@lang('service.txt8')</p>
				<ul>
					<li><button type="button" data-bs-dismiss="modal" aria-label="Close">@lang('button.cancel')</button></li>
					<li>
						<button type="button" onclick="confirmServicePay1()">@lang('button.confirm')</button>
					</li>
				</ul>
            </div>
        </div>
    </div>
</div>

<div class="modal fade service_stop" id="servicepay2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h2>
                    <img src="" id="m_service_icon">
					<span id="m_service_name"></span>
				</h2>
				<p>@lang('service.txt21')</p>

				<textarea id="refund_reason"></textarea>

				<span>@lang('service.txt22')</span>
				<div class="service_refund">
					<select id="bank_name">
                        @foreach(config('app.banks') as $key => $item)
                            <option value="{{ $item }}">{{ $item }}</option>
						@endforeach
					</select>
					<input type="text" id="account_num" placeholder="@lang('service.placeholder4')">
					<input type="text" id="account_holder" placeholder="@lang('service.placeholder5')">
				</div>

				<ul>
					<li><button type="button" data-bs-dismiss="modal" aria-label="Close">@lang('button.cancel')</button></li>
					<li>
						<button type="button" onclick="confirmServicePay2()">@lang('button.confirm')</button>
					</li>
				</ul>
            </div>
        </div>
    </div>
</div>

<div class="modal fade userqa_modal" id="inquiry_write" tabindex="-1" role="dialog"  aria-labelledby="exampleModalCenterTitlde"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h3>@lang('service.txt3')</h3>
                <div>
                    <div>
                        <p>@lang('service.txt12')</p>
                        <div class="select_wrap">
                            <select id="inquiry_type">
                                @foreach(explode(',', App\Models\Cms\QuestionOption::where('type', 1)->first()->content) as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                            <span class="icoArrow"><img src="/assets/images/icons/select_t.png"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <div>
                        <p>@lang('service.txt13')</p>
                        <input type="text" id="inquiry_title" placeholder="@lang('service.placeholder2')">
                    </div>
                </div>

                <div>
                    <div>
                        <p>@lang('service.txt14')</p>
                        <textarea id="inquiry_content" placeholder="@lang('service.placeholder3')"></textarea>
                    </div>
                </div>

                <div>
                    <div>
                        <p>@lang('service.txt15')</p>
                        <div class="file_button">
                            <label for="chooseFile" class="chooseFile">
                                <p id="fileName2">@lang('service.txt16')</p>
                                <span>@lang('service.txt17')</span>
                            </label>
                        </div>
                        <input type="file" name="chooseFile" onchange="loadFile(this)">
                    </div>

                    <div>
                        <p>@lang('service.txt18')</p>
                        <div class="select_wrap">
                            <select id="inquiry_visible">
                                <option value="1">@lang('service.txt19')</option>
                                <option value="0">@lang('service.txt20')</option>
                            </select>
                            <span class="icoArrow"><img src="/assets/images/icons/select_t.png"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="modal_btn">
                        <button data-bs-dismiss="modal" class="notice_close">@lang('button.close')</button>
                        <button onclick="addInquiry()">@lang('button.register')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade review_modal_client" id="review_modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="javascript:void(0)" onsubmit="createReview(this)">
                <div class="modal-body ">
                    <h1>@lang('service.txt9')</h1>
                    <div class="modal_company review_company">
                        <img src="/assets/images/store/bylogo.jpg" id="review_icon" width="60" height="60">
                        <div>
                            <h3 id="review_name"></h3>
                            <div class="star_wrap">
                                <h4>@lang('service.txt10')</h4>
                                <span class="star">
                                    <img src="/assets/images/store/star_no.svg">
                                    <img src="/assets/images/store/star_no.svg">
                                    <img src="/assets/images/store/star_no.svg">
                                    <img src="/assets/images/store/star_no.svg">
                                    <img src="/assets/images/store/star_no.svg">
                                    <span>
                                        <img src="/assets/images/store/star_yes.svg">
                                        <img src="/assets/images/store/star_yes.svg">
                                        <img src="/assets/images/store/star_yes.svg">
                                        <img src="/assets/images/store/star_yes.svg">
                                        <img src="/assets/images/store/star_yes.svg">
                                    </span>
                                    <input type="range" id="rating" name="rating" oninput="drawStar(this)" value="5" step="0.5" min="0" max="5">
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="contact_list">
                        <h2>@lang('service.txt11')</h2>
                        <textarea name="content" id="review_content" placeholder="@lang('service.placeholder1')"></textarea>

                        <ul class="contact_buttons">
                            <li>
                                <button type="button" class="btn btn-default" data-bs-dismiss="modal">@lang('button.close')</button>
                            </li>
                            <li>
                                <button type="button" onclick="addReview()">@lang('button.register')</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade contact_modal" id="servicePay" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h1>@lang('store.svcpay')</h1>
                <div class="modal_company">
                    <img class="svclog" id="pay_service_icon" src="">
                    <div>
                        <h3 id="pay_service_name"></h3>
                        <ul>
                            <li id="pay_company_name"></li>
                            <li id="pay_manager_phone"></li>
                        </ul>
                    </div>
                </div>

                <div class="contact_list">
                    <table>
                        <tr>
                            <th>@lang('store.payoption')</th>
                            <th>@lang('store.payserviceoption')</th>
                            <th>@lang('store.payamount')</th>
                        </tr>
                        <tr>
                            <td>
                                <select id="pay_option" onchange="changePayOption()"></select>
                            </td>
                            <td>
                                <div id="pay_term"></div>
                            </td>
                            <td>
                                <div id="pay_cost"></div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="total_pay">
                    <h2>@lang('store.totalcost')</h2>
                    <p id="pay_totalcost"></p>
                </div>
                <ul class="contact_buttons">
                    <li>
                        <button type="button" onclick="pay()">@lang('store.dopayment')</button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<form name="order_info" id="order_info" method="post" action="">
    <input type="hidden" name="client_service_id" id="client_service_id" value="" />
    <input type="hidden" name="service_id" id="service_id" value="" />

    <input type="hidden" name="site_cd" value="{{ config('services.kcp.site_code') }}" />

    <input type="hidden" name="ordr_idxx" id="ordr_idxx" maxlength="40" />
    <!-- 신용카드 -->
    <input type="hidden" name="pay_method" value="100000000000" />
    <!-- 가상계좌 -->
    <!-- <input type="hidden" name="pay_method" value="001000000000" /> -->

    <input type="hidden" name="good_name" id="good_name" value="" />
    <input type="hidden" name="good_mny" id="good_mny" value="" maxlength="9" />
    <input type="hidden" name="currency" id="currency" value="WON" />
    <input type="hidden" name="shop_user_id" id="shop_user_id" value="" />
    <input type="hidden" name="buyr_name" value="{{ Auth::user()->manager_name }}" />
    <input type="hidden" name="buyr_mail" value="{{ Auth::user()->manager_email }}" />

    <input type="hidden" name="res_cd" value=""/>
    <input type="hidden" name="res_msg" value=""/>
    <input type="hidden" name="enc_info" value=""/>
    <input type="hidden" name="enc_data" value=""/>
    <input type="hidden" name="ret_pay_method" value=""/>
    <input type="hidden" name="tran_cd" value=""/>
    <input type="hidden" name="use_pay_method" value=""/>

    <input type="hidden" name="pay_option" id="payoption" value="" />
    <input type="hidden" name="c_s_id" id="c_s_id" value="" />
</form>
@endsection

@push('scripts')
<script type="text/javascript">
    /****************************************************************/
    /* m_Completepayment  설명                                      */
    /****************************************************************/
    /* 인증완료시 재귀 함수                                         */
    /* 해당 함수명은 절대 변경하면 안됩니다.                        */
    /* 해당 함수의 위치는 payplus.js 보다먼저 선언되어여 합니다.    */
    /* Web 방식의 경우 리턴 값이 form 으로 넘어옴                   */
    /****************************************************************/
    function m_Completepayment( FormOrJson, closeEvent )
    {
        var frm = document.order_info;

        /********************************************************************/
        /* FormOrJson은 가맹점 임의 활용 금지                               */
        /* frm 값에 FormOrJson 값이 설정 됨 frm 값으로 활용 하셔야 됩니다.  */
        /* FormOrJson 값을 활용 하시려면 기술지원팀으로 문의바랍니다.       */
        /********************************************************************/
        GetField( frm, FormOrJson );

        if( frm.res_cd.value == "0000" )
        {
            // alert("결제 승인 요청 전,\n\n반드시 결제창에서 고객님이 결제 인증 완료 후\n\n리턴 받은 ordr_chk 와 업체 측 주문정보를\n\n다시 한번 검증 후 결제 승인 요청하시기 바랍니다."); //업체 연동 시 필수 확인 사항.
            /*
                                    가맹점 리턴값 처리 영역
            */

            frm.submit();
        }
        else
        {
            alert( "[" + frm.res_cd.value + "] " + frm.res_msg.value );

            closeEvent();
        }
    }
</script>
<script type="text/javascript" src="{{ config('services.kcp.payplus_url') }}"></script>
<script type="text/javascript">

    /* 표준웹 실행 */
    function jsf__pay( form )
    {
        try
        {
            KCP_Pay_Execute( form );
        }
        catch (e)
        {
            /* IE 에서 결제 정상종료시 throw로 스크립트 종료 */
        }
    }
</script>
<script>
    var selId = 0;
    var selReviewId = 0;
    var selServiceId = 0;
    var question_file = null;
    var selIcon = '';
    var selName = '';

    $('.app_btn_0').on('click', function() {
		$(this).parent().find('.app_btn_set').fadeToggle('app_btn_set');
	});

    function loadFile(input) {
		question_file = input.files[0];
        var name = document.getElementById('fileName2');
		name.textContent = question_file.name;
	};

    function serviceFreeModal(id) {
        selId = id;
        $('.app_btn_set').hide();
        $("#servicefree").modal('show');
    }

    function serviceDelModal(id) {
        selId = id;
        $('.app_btn_set').hide();
        $("#del_btn").modal('show');
    }

    function servicePayModal1(id, icon, name) {
        selId = id;
        selIcon = icon;
        selName = name;
        $('.app_btn_set').hide();
        $("#servicepay1").modal('show');
    }

    function confirmServicePay1(){
        $("#m_service_icon").attr('src', '/storage/'+selIcon);
        $("#m_service_name").html(selName);
        $("#servicepay1").modal('hide');
        $("#servicepay2").modal('show');
    }

    function confirmServicePay2(){
        $("#servicepay2").modal('hide');
        $.ajax({
            url: '/service/refund',
            method: 'post',
            data: {
                client_service_id: selId,
                refund_reason: $("#refund_reason").val(),
                bank_name: $("#bank_name").val(),
                account_num: $("#account_num").val(),
                account_holder: $("#account_holder").val()
            },
            success: (response) => {
                if(response.code == 200)
                    location.href = document.URL;
            },
            error: (e) => {
                console.log(e.responseJSON);
            }
        });
    }

    function extend_service(id, service_id) {
        selId = id;
        $.ajax({
            url: '/get/service/info',
            method: 'post',
            data: {
                id: service_id
            },
            success: (response) => {
                if (response.code == 200) {
                    $("#pay_service_icon").attr('src', '/storage/'+response.content.icon);
                    $("#pay_service_name").html(response.content.name);
                    $("#pay_company_name").html(response.user.company_name + '<br/>' + response.cat2.text);
                    $("#pay_manager_phone").html(response.user.manager_phone + '<br/>' + response.user.manager_email);

                    var html = '';

                    for (let i = 0; i < response.plan.length; i++)
                        html += '<option value="'+response.plan[i].id+'_'+response.plan[i].term+'_'+response.plan[i].term_unit+'_'+response.plan[i].amount+'_'+response.plan[i].currency+'_'+response.plan[i].name+'">'+response.plan[i].name+'</option>';

                    $("#pay_option").empty();
                    $("#pay_option").append(html);

                    html = response.plan.length > 0 ? response.plan[0].term : '';
                    html += response.plan.length > 0 ? response.plan[0].term_unit == 0 ? "{{ __('store.unitmonth') }}" : response.plan[0].term_unit == 1 ? "{{ __('store.unitday') }}" : "{{ __('store.unittime') }}" : '';
                    $("#pay_term").html(html);

                    html = response.plan.length > 0 ? response.plan[0].amount : '';
                    html += response.plan.length > 0 ? currency[response.plan[0].currency] : '';
                    $("#pay_cost").html(html);

                    $("#pay_totalcost").html(html);

                    $("#servicePay").modal('show');

                    $('#client_service_id').val(id);
                }
            },
            error: (e) => {
                console.log(e.responseJSON);
            }
        });
    }

    function updateProcess(id, val) {
        $.ajax({
            url: '/update/service/process',
            method: 'post',
            data: {
                id: id > 0 ? id : selId,
                process: val
            },
            success: (response) => {
                if(response.code == 200)
                    location.href = document.URL;
            },
            error: (e) => {
                console.log(e.responseJSON);
            }
        });
    }

    function inquiryWrite(obj) {
        selServiceId = obj.service_id;
        question_file = null;
        $("#fileName2").html("{{ __('service.txt16') }}");
        $("#inquiry_type").val(0);
        $("#inquiry_title").val('');
        $("#inquiry_content").val('');
        $("#inquiry_visible").val(1);
        $("#inquiry_write").modal('show');
    }

    function addInquiry() {
        let request = new FormData();
        request.set('service_id', selServiceId);
        request.set('type', $("#inquiry_type").val());
        request.set('title', $("#inquiry_title").val());
        request.set('content', $("#inquiry_content").val());
        request.set('question_file', question_file);
        request.set('visible', $("#inquiry_visible").val());

        $.ajax({
            url: '/add/service/inquiry',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                if(response.code == 200) {
                    $("#inquiry_write").modal('hide');
                    toastr.success(response.message);
                }
            },
            error: (e) => {
                console.log(e.responseJSON);
            }
        });
    }

    function reviewModal(obj) {
        selServiceId = obj.service_id;
        // $.ajax({
        //     url: '/get/service/review',
        //     method: 'post',
        //     data: { client_id: obj.client_id, service_id: obj.service_id },
        //     success: (response) => {
        //         if (response.code == 200) {
        //             if (response.content != '') {
        //                 selReviewId = response.content.id;
        //                 $("#review_icon").attr('src', '/storage/'+response.service.icon);
        //                 $("#review_name").html(response.service.name);
        //                 drawStar({value: response.content.rating});
        //                 $("#review_content").val(response.content.content);
        //             } else {
                        selReviewId = 0;
                        $("#review_icon").attr('src', '/storage/'+obj.service.icon);
                        $("#review_name").html(obj.service.name);
                        drawStar({value: 5});
                        $("#review_content").val('');
                    // }

                    $("#review_modal").modal('show');
        //         }
        //     },
        //     error: (e) => {
        //         console.log(e.responseJSON);
        //     }
        // });
    }

    function addReview() {
        $.ajax({
            url: '/add/service/review',
            method: 'post',
            data: {
                review_id: selReviewId,
                service_id: selServiceId,
                rating: $("#rating").val(),
                content: $("#review_content").val()
            },
            success: (response) => {
                if(response.code == 200) {
                    // $("#review_modal").modal('hide');
                    // toastr.success(response.message);
                    location.href = document.URL;
                }
            },
            error: (e) => {
                console.log(e.responseJSON);
            }
        });
    }

    function changePayOption() {
        option_inf = $("#pay_option").val();
        const words = option_inf.split('_');
        let currency_unit = @json(config('app.currency'));
        var id = words[0];
        var term = words[1];
        var term_unit = words[2];
        var amount = words[3];
        var currency = words[4];

        if(term_unit == 0)
            $("#pay_term").html(term+"@lang('store.unitmonth')");
        else if(term_unit == 1)
            $("#pay_term").html(term+"@lang('store.unitday')");
        else if(term_unit == 2)
            $("#pay_term").html(term+"@lang('store.unittime')");

        $("#pay_cost").html(amount+currency_unit[currency]);
        $("#pay_totalcost").html(amount+currency_unit[currency]);
    }

    function pay() {
        let request = new FormData($('#order_info')[0]);
        request.set('client_service_id', $('#client_service_id').val());

        $.ajax({
            url: '/get/orderno',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                if(response.code == 200) {
                    $("#ordr_idxx").val(response.order_no);
                    $("#c_s_id").val(selId);
                    var pay_option = $("#pay_option").val();
                    console.log(pay_option)
                    var pay_infs = pay_option.split("_");
                    $("#good_name").val(pay_infs[5]);

                    if (pay_infs[4] == 0) {
                        $("#currency").val("WON");
                        $("#good_mny").val(pay_infs[3]);
                    } else if(pay_infs[4] == 1) {
                        $("#currency").val("USD");
                        $("#good_mny").val(pay_infs[3]+"00");
                    }

                    $("#payoption").val(pay_option);
                    document.order_info.action = "/reqextendpay";
                    $("#servicePay").modal('hide');
                    jsf__pay(document.order_info);
                }
            },
            error: (e) => {
                console.log(e.responseJSON);
            }
        });

        // $.ajax({
        //     url: '/update/service/process',
        //     method: 'post',
        //     data: {
        //         id: selId,
        //         process: 5,
        //         pay_option: $("#pay_option").val()
        //     },
        //     success: (response) => {
        //         if(response.code == 200)
        //             location.href = document.URL;
        //     },
        //     error: (e) => {
        //         console.log(e.responseJSON);
        //     }
        // });
    }

    const drawStar = (target) => {
        document.querySelector(`.star span`).style.width = `${target.value * 20}%`;
    }
</script>
@endpush

<style>
    .userqa_modal input[type='file'] {
        display: block !important;
        margin-top: -50px;
        height: 50px;
        width: 100%;
        opacity: 0;
        cursor: pointer;
    }
    .my_btn button:last-child {
        border-left: none !important;
        width: 100%;
    }
    .service_app2 p {
        height: 42px !important;
    }

    .service_app2 .my_btn a {
        background: none;
        color: #007C4F;
        font-size: 14px;
        padding: 10px 0;
        font-weight: 600;
        width: 100%;
        text-align: center;
    }
    .service_app2 .my_btn:last-child {margin-bottom: 0 !important;}
    .service_stop .service_refund select {
        width: 170px !important;
    }
    .contact_modal .modal-body {
        width: 400px;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }
    .contact_modal .modal-content {
        justify-content: center;
        align-items: center;
        border-radius: 10px;
    }
    .contact_modal .modal-content .modal-body {
        padding: 0 10px 10px 10px;
    }
    .contact_modal .modal-body img {
        margin-right: 20px;
    }
    .contact_modal h1 {
        font-size: 24px;
        font-weight: 500;
        text-align: center;
        margin-top: 50px;
    }
    .contact_modal h2 {
        font-size: 16px;
        font-weight: 500;
    }
    .contact_modal table {
        width: 100%;
        font-size: 16px;
        color: #000;
    }
    .contact_modal table tr:first-child {
        border-bottom: 1px solid #ccc;
    }
    .contact_modal table tr th {
        font-size: 14px;
        color: #888;
        padding: 10px 5px;
    }
    .contact_modal table tr th:last-child {
        text-align: right;
    }
    .contact_modal table tr td:last-child {
        text-align: right;
        padding-right: 5px;
    }
    .contact_modal table select {
        margin: 10px 5px;
        border: 1px solid #ccc;
    }
    .contact_modal .total_pay {
        width: 100%;
        margin-top: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 16px;
        color: #000;
        border-top: 1px solid #ccc;
        padding: 10px 5px;
    }
    .contact_modal .total_pay p {
        font-size: 18px;
        color: red;
    }
    .contact_modal .contact_list {
        width: 100%;
        margin-top: 20px;
    }
    .modal_company {
        display: flex;
        justify-content: start;
        align-content: center;
        width: 100%;
        margin-top: 40px;
        margin-bottom: 7px;
    }
    .svclog {
        width: 67px;
        height: 67px;
        background-size: contain;
    }
    .modal_company div h3 {
        font-size: 16px;
        color: #000;
    }
    .modal_company div ul {
        display: flex;
        margin-top: 10px;
        padding-left: 0;
    }
    .modal_company div ul li {
        color: #ccc;
        font-size: 12px;
        line-height: 17px;
    }
    .modal_company div ul li:first-child {
        border-right: 1px solid #eee;
        color: #888;
        margin-right: 20px;
        padding-right: 20px;
    }
    @media (min-width: 576px) {
        #servicepay2 .modal-dialog {
            max-width: 600px !important;
        }
    }
    #NAX_BLOCK {
        z-index: 1061 !important;
        top: calc((100vh - 570px) / 2) !important;
    }
</style>
