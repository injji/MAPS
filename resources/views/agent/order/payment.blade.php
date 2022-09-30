@extends('layouts.agent')

@section('content')

<form action="{{ route('agent.payment.list') }}">
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
	            'type'    => 'payment_list',
	        ]) }}" >@lang('sub.agent-download')</a>
	</div>
</div>

<div class="board_table_ex">
    @if(\Auth::user()->self_payment == 1)
	<button type="button" data-toggle="modal" data-target="#info_pay" class="info_pay">@lang('button.pay_sudong')</button>
    @endif
	<table class="table_no8">
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
			<col />
			<col width="196px" />
		</colgroup>

		<thead>
			<tr>
				<th>No</th>
                <th>@lang('payment.field10')</th>
				<th>@lang('sub.agent-pay_date')</th>
				<th><a href="{{ route('agent.payment.list', ['sort' => $sort == 2 ? 1 : 2]) }}">@lang('sub.agent-service') <img src="/assets/images/store/{{ $sort == 2 ? 'top_up.png' : 'top_down_b.png'}}"></a></th>
				<th>@lang('sub.agent-site')</th>
				<th><a href="{{ route('agent.payment.list', ['sort' => $sort == 3 ? 1 : 3]) }}">@lang('sub.agent-pay_type') <img src="/assets/images/store/{{ $sort == 3 ? 'top_up.png' : 'top_down_b.png'}}"></a></th>
				<th>@lang('sub.agent-pay_method')</th>
                <th>@lang('sub.agent-pay_option')</th>
                <th>@lang('sub.agent-service_option')</th>
				<th><a href="{{ route('agent.payment.list', ['sort' => $sort == 4 ? 1 : 4]) }}">@lang('sub.agent-pay_amount') <img src="/assets/images/store/{{ $sort == 4 ? 'top_up.png' : 'top_down_b.png'}}"></a></th>
                <th>@lang('sub.agent-pay_term')</th>
			</tr>
		</thead>

		<tbody>
			@foreach($results as $key => $item)
                <?php
                    $term = "";
                    if($item->plan){
                        switch($item->plan->term_unit){
                            case 0 : $term = $item->plan->term.' 개월'; break;
                            case 1 : $term = $item->plan->term.' 일'; break;
                            case 2 : $term = $item->plan->term; break;
                            default : break;
                        }
                    }
                ?>
				<tr>
                    <td>{{ $results->total() - ($results->currentPage() - 1) * $results->perPage() - $key }}</td>
                    <td>{{ $item->order_no ?? '' }}</td>
					<td>{!! substr($item->created_at, 0, 10).'<br/>'.substr($item->created_at, 11) !!}</td>
					<td>{{ $item->service->name ?? '' }}</td>
					<td>{{ $item->site->name ?? '' }}</td>
					<td>{{ $item->type_text ?? '' }}</td>
					<td>{{ $item->payment_type_text ?? '' }}</td>
					<td>{{ $item->plan->name ?? '' }}</td>
                    <td>{{ $term }}</td>
					<td>{{ '('.$item->currency.') '.number_format($item->amount) }}</td>
					<td><span>{{ $item->service_start_at ? $item->service_start_at->format('Y.m.d'). ' ~ ' : '' }}{{ $item->service_end_at ? $item->service_end_at->format('Y.m.d') : '' }}</span></td>
				</tr>
			@endforeach

			@if(count($results) == 0)
            <tr>
                <td colspan="9" class="nonelist">@lang('sub.agent-no_data')</td>
            </tr>
            @endif
		</tbody>
	</table>

	<div class="list_btn_ex">
		{!! $results->appends(Request::except('page'))->render() !!}
	</div>
</div>
</form>


<div class="modal fade service_stop" id="info_pay" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="dataForm">
                <div class="modal-body">
                    <input type="hidden" id="client_service_id" name="client_service_id" value=""/>
                    <input type="hidden" id="client_id" name="client_id" value=""/>
                    <input type="hidden" id="agent_id" name="agent_id" value=""/>
                    <input type="hidden" id="service_id" name="service_id" value=""/>
                    <input type="hidden" id="site_id" name="site_id" value=""/>
                    <input type="hidden" id="plan_id" name="plan_id" value=""/>
                    <h2>@lang('button.pay_sudong')</h2>
                    <ol>
                        <li class="tr1">
                            <p>@lang('table.sold_num')</p>
                            <div>
                                <input type="text" placeholder="주문번호를 입력하세요." class="order_num" name="order_num">
                                <button type="button" class="matching_num">@lang('table.sold_match')</button>
                            </div>
                            <em class="matching_text">@lang('table.sold_no')</em>
                        </li>

                        <li class="tr1">
                            <p>@lang('table.pay_time')</p>
                            <div>
                                <h3>
                                    <input type="text" class="pay_date" name="pay_date"> <i class="material-icons">event</i>
                                </h3>

                                <span>
                                    <select class="form-select" name="pay_date_hour">
                                        <option value="00">00</option>
                                        @for($i=1; $i<=23; $i++)
                                            <option value="{{ ($i < 10) ? '0'.$i : $i }}">{{ ($i < 10) ? '0'.$i : $i }}</option>
                                        @endfor
                                    </select>

                                    :

                                    <select class="form-select" name="pay_date_min">
                                        <option value="00">00</option>
                                        @for($i=1; $i<=59; $i++)
                                            <option value="{{ ($i < 10) ? '0'.$i : $i }}">{{ ($i < 10) ? '0'.$i : $i }}</option>
                                        @endfor
                                    </select>
                                </span>
                            </div>
                        </li>

                        <li class="tr2">
                            <div style="width:25%">
                                <p>@lang('table.pay.line')</p>
                                <select class="form-select" name="type">
                                    <option value="">@lang('table.pay.select')</option>
                                    <option value="0">@lang('table.pay.new')</option>
                                    <option value="1">@lang('table.pay.more')</option>
                                </select>
                            </div>

                            <div style="width:25%">
                                <p>@lang('table.pay.how')</p>
                                <select class="form-select" name="payment_type">
                                    <option value="">@lang('table.pay.select')</option>
                                    <option value="0">@lang('table.pay.card')</option>
                                    <option value="1">@lang('table.pay.moo')</option>
                                </select>
                            </div>

                            <div style="width:50%">
                                <p>@lang('table.pay.much')</p>
                                <label><input type="text" placeholder="결제금액을 입력하세요." name="amount">@lang('table.pay.won')</label>
                            </div>
                        </li>


                        <li class="tr3">
                            <div>
                                <p>@lang('table.service.line')</p>
                                <label><input type="radio" value="기간상품" name="service_type" checked><em></em>@lang('table.service.item1')</label>
                                <label><input type="radio" value="일회성상품" name="service_type"><em></em>@lang('table.service.item2')</label>
                            </div>
                        </li>

                        <li class="tr1" id="payment_range" style="margin-bottom:0">
                            <p>@lang('table.service.time')</p>
                            <div>
                                <h3>
                                    <input type="text" class="pay_date pay_date_reset" name="pay_date_start"> <i class="material-icons">event</i>
                                </h3>
                                <b>~</b>
                                <h3>
                                    <input type="text" class="pay_date pay_date_reset" name="pay_date_end"> <i class="material-icons">event</i>
                                </h3>
                            </div>
                        </li>
                    </ol>
                    <ul>
                        <li><button type="button" data-dismiss="modal" aria-label="Close" onclick="resetForm()">@lang('button.close')</button></li>
                        <li><button type="button" onclick="createPayment()">@lang('button.register')</button></li>
                    </ul>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">

    $(document).ready(function() {
        $("#data_range").data('daterangepicker').setStartDate('{{$st_date}}');
        $("#data_range").data('daterangepicker').setEndDate('{{$ed_date}}');

        $('input[name=service_type]').on('change', function(){
            if($(this).val() == '일회성상품'){
                $('#payment_range').hide();
            }else{
                $('#payment_range').show();
            }
        })
    });

	$(function() {
        setDatepicker();
	});

    function setDatepicker(){
        $('.pay_date').daterangepicker({
                locale: {
                    "format": 'YYYY-MM-DD',     // 일시 노출 포맷
                    "daysOfWeek": ["일", "월", "화", "수", "목", "금", "토"],
                    "monthNames": ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"]
                },
                singleDatePicker: true,
                showDropdowns: true,
                autoApply: true,
                minYear: 1901,
                maxYear: parseInt(moment().format('YYYY'),10)
            });

            $('.pay_date_reset').val('');
    }


	$('.order_num').on('keyup',function(){

        let url = "{{ route('agent.payment.order_no', ':order_no') }}";
        url = url.replace(':order_no', $('.order_num').val());

        $.ajax({
            url: url,
            method: 'get',
            success: (response) => {
                console.log(response);
                if(response.code == 200){
                    $('.matching_num').addClass('active');
                    $('.matching_text').text('주문번호가 매칭 되었습니다.');
                    $('.matching_text').css('color', 'green');
                }else{
                    $('.matching_num').removeClass('active');
                    $('.matching_text').text('존재하지 않는 주문번호 입니다.');
                    $('.matching_text').css('color', 'red');
                }
                // if(response.service){
                //     $('.matching_num').addClass('active');
                //     $('.matching_text').text('주문번호가 매칭 되었습니다.');
                //     $('.matching_text').css('color', 'green');
                // }else{
                //     $('.matching_num').removeClass('active');
                //     $('.matching_text').text('존재하지 않는 주문번호 입니다.');
                //     $('.matching_text').css('color', 'red');
                // }
                $('.matching_text').show();
                $('#client_service_id').val((response.service) ? response.service.id : '');
                $('#client_id').val((response.service) ? response.service.client_id : '');
                $('#agent_id').val(response.agent);
                $('#service_id').val((response.service) ? response.service.service_id : '');
                $('#site_id').val((response.service) ? response.service.site_id : '');
                $('#plan_id').val(response.plan);
            }
        })
	});

    function resetForm(){
        $('#dataForm').clearForm();
        $('.matching_text').hide();
        setDatepicker();
    }

    $.fn.clearForm = function () {
        return this.each(function () {
            var type = this.type,
            tag = this.tagName.toLowerCase();
            if (tag === 'form') {
            return $(':input', this).clearForm();
            }
            if (
            type === 'text' ||
            type === 'password' ||
            type === 'hidden' ||
            tag === 'textarea'
            ) {
            this.value = '';
            } else if (type === 'checkbox' || type === 'radio') {
            this.checked = false;
            } else if (tag === 'select') {
            this.selectedIndex = -1;
            }
        });
    };

    function createPayment(){

        if($('.matching_num').hasClass('active')){

            let request = new FormData($('#dataForm')[0]);
            let form = $('#dataForm')[0];
            console.log(form.order_num.value);

            if(!form.pay_date.value){
                alert('결제일시를 선택하세요.');
                return;
            }
            if(!form.type.value){
                alert('결제구분을 선택하세요.');
                return
            }
            // if(!form.payment_type.value){
            //     alert('결제방식을 선택하세요.');
            //     return
            // }
            if(form.service_type.value == '기간상품'){
                if(!form.pay_date_start.value){
                    alert('서비스시작일을 선택하세요.');
                    return
                }
                if(!form.pay_date_end.value){
                    alert('서비스시작일을 선택하세요.');
                    return
                }
            }

            $.ajax({
                url: '/order/payment/payment',
                method: 'post',
                data: request,
                contentType: false,
                processData: false,
                success: (response) => {
                    console.log(response);
                    alert('등록 되었습니다.');
                    location.reload();
                }
            })

        }else{
            alert('일치하는 주문번호가 없습니다.');
        }
    }

</script>

@endpush
