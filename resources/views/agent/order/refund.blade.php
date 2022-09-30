
@extends('layouts.agent')

@section('content')

<form action="{{ route('agent.payment.refund') }}">
<div class="board_search_ex">
    <div class="b_s_ex">
        <label>
            <span>@lang('sub.agent-period')</span>
            <input type="text" value="{{$st_date}} ~ {{$ed_date}}" id="data_range" name="data_range" />
        </label>

        <input type="text" value="{{$keyword}}" name="keyword" placeholder="@lang('sub.agent-keyword')" />
        <input type="hidden" value="{{$sort}}" name="sort" />
        <button type="submit">
            <img src="/assets/images/store/search_on.svg">
        </button>
    </div>

    <div class="total_ex">
        <?php $krw_total = 0; $usd_total = 0;
            foreach ($results as $key => $value) {
                if($value->currency=='$') {
                    $usd_total += $value->amount;
                }
                if($value->currency=='￦') {
                    $krw_total += $value->amount;
                }
            }
        ?>
        <ul>
            <li><span>TOTAL</span>{{ number_format($total_cnt) }}</li>
            <li><span>@lang('sub.agent-search_result')</span>{{ number_format($results->total()) }}</li>
            <li><span>@lang('sub.agent-pay_amount')</span>{{ number_format($krw_total) }} ￦ / {{ number_format($usd_total) }} $</li>
        </ul>

        <a href="{{ route('agent.order_export', [
                'st_date' => $st_date,
                'ed_date' => $ed_date,
                'keyword' => $keyword,
                'type'    => 'refund',
            ]) }}" >@lang('sub.agent-download')</a>
    </div>
</div>

<div class="board_table_ex board_table_ex3">
    <table class="refund_list table_no8" >
        <colgroup>
			<col width="56px" />
			<col />
			<col />
			<col />
			<col />
			<col />
			<col />
			<col />
			<col />
			<col width="110px" />
		</colgroup>

        <thead>
            <tr>
                <th>No</th>
                <th>@lang('payment.field10')</th>
                <th>@lang('sub.agent-pay_date')</th>
                <th><a href="{{ route('agent.payment.refund', ['sort' => $sort == 2 ? 1 : 2]) }}">@lang('sub.agent-service') <img src="/assets/images/store/{{ $sort == 2 ? 'top_up.png' : 'top_down_b.png'}}"></a></th>
                <th>@lang('sub.agent-site')</th>
                <th>@lang('sub.agent-pay_method')</th>
                <th><a href="{{ route('agent.payment.refund', ['sort' => $sort == 3 ? 1 : 3]) }}">@lang('sub.agent-pay_amount') <img src="/assets/images/store/{{ $sort == 3 ? 'top_up.png' : 'top_down_b.png'}}"></a></th>
                <th>@lang('sub.agent-pay_term')</th>
                <th>@lang('sub.agent-refund_date')</th>
                <th><a href="{{ route('agent.payment.refund', ['sort' => $sort == 4 ? 1 : 4]) }}">@lang('sub.agent-refund_status') <img src="/assets/images/store/{{ $sort == 4 ? 'top_up.png' : 'top_down_b.png'}}"></a></th>
            </tr>
        </thead>

        <tbody>
            @foreach($results as $key => $item)
                <tr>
                    <td>{{ $results->total() - ($results->currentPage() - 1) * $results->perPage() - $key }}</td>
                    <td>{{ $item->order_no ?? '' }}</td>
                    <td>{!! substr($item->created_at, 0, 10).'<br/>'.substr($item->created_at, 11) !!}</td>
                    <td>{{ $item->service->name ?? '' }}</td>
                    <td>{{ $item->site->name ?? '' }}</td>
                    <td>{{ $item->payment_type_text ?? '' }}</td>
                    <td>{{ '('.$item->currency.') '.number_format($item->amount) }}</td>
                    <td><span>{{ $item->service_start_at ? $item->service_start_at->format('Y.m.d'). ' ~ ' : '' }}{{ $item->service_end_at ? $item->service_end_at->format('Y.m.d') : '' }}</span></td>
                    <td>{!! substr($item->refund_request_at, 0, 10).'<br/>'.substr($item->refund_request_at, 11) !!}</td>
                    <td class="pointer" onclick='showModal("{{$item->id}}", "{{$item->agent_id}}")'>{{ $item->refund_status_text ?? '' }}</td>
                </tr>
            @endforeach

            @if(count($results) == 0)
            <tr>
                <td colspan="10" class="nonelist">@lang('sub.agent-no_data')</td>
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
</script>

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
<script>
    var Poin = document.querySelectorAll(".pointer")

    for (let i = 0; i < Poin.length; i++) {
        if (Poin[i].innerHTML != "완료") {
            Poin[i].style.color = "#007c4f";
        } else {
            Poin[i].style.color = "#666";
        }
    }
</script>

@endpush
