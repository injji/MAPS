@extends('layouts.cms')

@section('content')

<form action="{{ route('order.refund') }}">
<div class="board_search_ex">
    <div class="b_s_ex b_s_ex2">
        <label class="mr-2">
            <span>기간</span>
            <input type="text" value="{{$st_date}} ~ {{$ed_date}}" id="data_range" name="data_range" />
        </label>
        <label class="mr-2">
            <select name="agent_id" onchange="changeAgent(this.value)">
                <option value="">제휴사선택</option>
                @foreach($agent_list as $key => $item)
                    <option value="{{ $item->id }}" {{ $agent_id == $item->id ? 'selected' : ''}}>{{ $item->company_name }}</option>
                @endforeach
            </select>
        </label>
        <label class="mr-2">
            <select name="service_id">
                <option value="">서비스선택</option>
                @foreach($service_list as $key => $item)
                    <option value="{{ $item->id }}" {{ $service_id == $item->id ? 'selected' : ''}}>{{ $item->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="mr-2">
            <select name="category_id">
                <option value="">카테고리선택</option>
                @foreach($category_list as $key => $item)
                    <option value="{{ $item->id }}" {{ $category_id == $item->id ? 'selected' : ''}}>{{ $item->text }}</option>
                @endforeach
            </select>
        </label>

        <input type="text" value="{{$keyword}}" name="keyword" placeholder="주문번호/서비스명/사이트명" />
        <button type="submit">
            <img src="/assets/images/store/search_on.svg">
        </button>
    </div>

    <div class="total_ex">

        <a href="{{ route('cms_order_export', [
                'st_date' => $st_date,
                'ed_date' => $ed_date,
                'keyword' => $keyword,
                'agent_id'    => $agent_id,
                'service_id'  => $service_id,
                'category_id' => $category_id,
                'type'        => 'refund'
            ]) }}" >다운로드</a>
    </div>
</div>

<div class="board_table_ex">
    <table class="table_no8">
        <thead>
            <tr>
                <th>No</th>
                <th>주문번호</th>
                <th>결제일</th>
                <th>제휴사</th>
                <th>서비스명</th>
                <th>사이트</th>
                <th>결제방식</th>
                <th>결제금액</th>
                <th>이용기간</th>
                <th>환불요청일</th>
                <th>처리상태</th>
            </tr>
        </thead>

        <tbody>
            @foreach($results as $key => $item)
            <tr class="tabletr">
                <td>{{ $results->total() - ($results->currentPage() - 1) * $results->perPage() - $key }}</td>
                <td>{{ $item->order_no ?? '' }}</td>
                <td>{!! substr($item->created_at, 0, 10) !!}</td>
                <td>{{ $item->service->user->company_name }}</td>
                <td>{{ $item->service->name }}</td>
                <td>{{ $item->site->name }}</td>
                <td>{{ $item->payment_type_text ?? '' }}</td>
                <td>{{ '('.$item->currency.') '.number_format($item->amount) }}</td>
                <td><span>{{ $item->service_start_at ? $item->service_start_at->format('Y.m.d'). ' ~ ' : '' }}{{ $item->service_end_at ? $item->service_end_at->format('Y.m.d') : '' }}</span></td>
                <td>{!! substr($item->refund_request_at, 0, 10).'<br/>'.substr($item->refund_request_at, 11) !!}</td>
                @if ($write_permission)
                    <td class="pointer" onclick='showModal("{{$item->id}}", "{{$item->agent_id}}")'><a href="javascript:void(0)">{{ $item->refund_status_text ?? '' }}</a></td>
                @else
                    <td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')"><a href="javascript:void(0)">{{ $item->refund_status_text ?? '' }}</a></td>
                @endif
            </tr>
            @endforeach

            @if(count($results) == 0)
            <tr class="tabletr">
                <td colspan="10">@lang('sub.agent-no_data')</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="list_btn_ex">
        {!! $results->appends(Request::except('page'))->render() !!}
    </div>

</div>
</form>

<div class="modal fade process_modal" id="process01" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">

                <div>
                    <h1 class="service_name">@lang('sub.agent-service')</h1>
                </div>

                <div class="p_check_radio">
                    <div>
                        <label><input type="radio" name="process_check" checked="checked"
                                value="receipt"><em>@lang('sub.agent-accept')</em></label>
                        <label><input type="radio" name="process_check" value="hold"><em>@lang('sub.agent-return')</em></label>
                        <label><input type="radio" name="process_check" value="refundend"><em>@lang('sub.agent-refund_action')</em></label>
                    </div>
                </div>

                <div id="receipt_div">
                    <textarea id="refund_reason"></textarea>

                    <div class="modal_btn">
                        <button data-bs-dismiss="modal" class="notice_close">@lang('sub.sub-close')</button>
                    </div>
                </div>

                <div id="hold_div">
                    <textarea id="refusal_reason"></textarea>

                    <div class="modal_btn">
                        <button data-bs-dismiss="modal" class="notice_close">@lang('sub.sub-close')</button>
                        <button onclick='$("#hold_save").modal("show")'>@lang('sub.agent-save_action')</button>
                    </div>
                </div>

                <div id="refund_div">
                    <ul>
                        <li>
                            <h2>@lang('sub.agent-service_stop')</h2> <em> <input type="date" id="service_stop_at" value=""></em>
                        </li>
                        <li>
                            <h2>@lang('sub.agent-return_amount')</h2><em> <input type="text" id="refund_amount" value=""><span class="currency"></span></em>
                        </li>
                        <li>
                            <h2>@lang('sub.agent-return_fee')</h2><em> <span id="refund_fee"></span><span class="currency"></span></em>
                        </li>
                    </ul>

                    <div class="modal_btn">
                        <button data-bs-dismiss="modal" class="notice_close">@lang('sub.sub-close')</button>
                        <button onclick='showRefundConfirm()'>@lang('sub.agent-save_action')</button>
                    </div>
                </div>


            </div>

        </div>
    </div>
</div>

<div class="modal fade process_modal" id="process02" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">


            <div class="modal-body">

                <div>
                    <h1 class="service_name">@lang('sub.agent-service')</h1>
                    <p class="status_txt">@lang('sub.agent-return_complete')</p>
                </div>

                <div>
                    <ul>
                        <li>
                            <h2>@lang('sub.agent-return_complete')</h2> <span class="refund_complete_at"></span>
                        </li>
                        <li>
                            <h2>@lang('sub.agent-return_amount2')</h2> <span class="refund_amount"></span>
                        </li>
                    </ul>
                </div>

                <div>
                    <div class=" modal_btn modal_btn2">
                        <button data-bs-dismiss="modal" class="notice_close">@lang('sub.sub-close')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade process_modal" id="hold_save" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">


            <div class="modal-body">

                <div>
                    <h3>@lang('sub.agent-hold_save_txt')</h3>
                </div>

                <div>
                    <div class=" modal_btn modal_btn2">
                        <button data-bs-dismiss="modal" class="notice_close">@lang('sub.agent-return_cancel')</button>
                        <button onclick="changeRefund()">@lang('sub.agent-complete')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade process_modal" id="refund_save" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">


            <div class="modal-body">

                <div>
                    <h3>@lang('sub.agent-refund_save_txt1') <span id="refund_amount2"></span>@lang('sub.agent-refund_save_txt2')<br>
                        @lang('sub.agent-refund_save_txt3')</h3>
                </div>

                <div>
                    <div class=" modal_btn modal_btn2">
                        <button data-bs-dismiss="modal" class="notice_close">@lang('sub.agent-return_cancel')</button>
                        <button onclick="changeRefund()">@lang('sub.agent-complete')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">

    $(document).ready(function() {
        $("#data_range").data('daterangepicker').setStartDate('{{$st_date}}');
        $("#data_range").data('daterangepicker').setEndDate('{{$ed_date}}');
    });
</script>


<script>
    var chkValue = $('input[type=radio][name=process_check]:checked').val();
    var cv = document.getElementById(chkValue + '_div');
    cv.style.display = "block";

    $('input[type=radio][name=process_check]').on('click', function () {
        var chkValue = $('input[type=radio][name=process_check]:checked').val();

        if (chkValue == 'receipt') {
            $('#receipt_div').show();
            $('#hold_div').hide();
            $('#refund_div').hide();
        } else if (chkValue == 'hold') {
            $('#receipt_div').hide();
            $('#hold_div').show();
            $('#refund_div').hide();
        } else {
            $('#receipt_div').hide();
            $('#hold_div').hide();
            $('#refund_div').show();
        }
    });
    //
    var payment_id  = '';
    var agent_id    = '';
    function showModal(id, agentId) {
        payment_id  = id;
        agent_id    = agentId;
        //
        $.ajax({
            url: '/order/get_refund',
            method: 'post',
            data: {id},
            success: (response) => {
                if(response.code == 200){
                    let data = response.data;
                    if(data.refund_status == 5) {   // 완료
                        $("#process02 .service_name").text(data.service_name);
                        $("#process02 .refund_complete_at").text(data.refund_complete_at);
                        $("#process02 .refund_amount").text(data.refund_amount.toLocaleString() + data.currency);
                        $("#process02").modal("show");
                    }
                    else {   //
                        $("#process01 .service_name").text(data.service_name);
                        $("#refund_reason").val(data.refund_reason);
                        $("#refusal_reason").val(data.refusal_reason);
                        $("#service_stop_at").val(data.service_stop_at ? data.service_stop_at.substr(0, 10) : '');
                        $("#refund_amount").val(data.refund_amount.toLocaleString());
                        $("#refund_fee").text(data.service_start==1 ? parseInt(data.refund_amount*0.2).toLocaleString() : 0);
                        $("#process01 .currency").text(data.currency);

                        if(data.refund_status == 0) {
                            $('#process01 input[value="receipt"]').trigger('click');
                        }
                        else if(data.refund_status == 3) {  // 보류
                            $('#process01 input[value="hold"]').trigger('click');
                        }
                        else if(data.refund_status == 5) {  // 완료
                            $('#process01 input[value="refundend"]').trigger('click');
                        }

                        $("#process01").modal("show");
                    }
                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });
    }

    function changeRefund() {
        let tab = $('#process01 input[name="process_check"]:checked').val();
        let refusal_reason  = $('#refusal_reason').val();
        let service_stop_at = $('#service_stop_at').val();
        let refund_amount   = $('#refund_amount').val();

        if(tab == 'hold' && !refusal_reason) {
            return alert("@lang('sub.agent-no_refund_reason')");
        }
        else if(tab == 'refundend') {
            if(!service_stop_at) {
                return alert("@lang('sub.agent-no_service_stop')");
            }
            if(!refund_amount) {
                return alert("@lang('sub.agent-no_refund_amount')");
            }
        }

        let data = {
            payment_id, agent_id, tab, refusal_reason, service_stop_at, refund_amount: refund_amount.replaceAll(',', '')
        }
        $.ajax({
            url: '/order/change_refund',
            method: 'post',
            data: data,
            success: (response) => {
                if(response.code == 200){
                    location.reload();
                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });
    }

    function showRefundConfirm() {
        $("#refund_amount2").text($("#refund_amount").val() + $(".currency").text()[0]);
        $("#refund_save").modal("show");
    }

    $(document).ready(function() {
        $("#data_range").data('daterangepicker').setStartDate('{{$st_date}}');
        $("#data_range").data('daterangepicker').setEndDate('{{$ed_date}}');
    });

    function changeAgent(id) {
		var list = @json($total_service_list);

		if (id != "")
			list = list.filter(obj => obj.agent_id == id);

		var html = '<option value="">서비스선택</option>';

		for(var i = 0; i < list.length; i++) {
			html += '<option value="'+list[i].id+'">'+list[i].name+'</option>';
		}

		$("select[name='service_id']").html(html);
	}
</script>
@endpush
