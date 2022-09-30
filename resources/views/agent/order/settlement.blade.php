@extends('layouts.agent')

@section('content')

<div class="board_tit board_tit2">
	<h1>@lang('table.calculate.care') <span class="information_icon">?</span>
        <div id="refund_information">
            <ul>
                <li><em>@lang('table.calculate.pay')</em> @lang('table.calculate.noo')<br>
                    (@lang('table.calculate.month'))</li>
                <li><em>@lang('table.calculate.mi')</em> @lang('table.calculate.pays') <br>
                    (@lang('table.calculate.mimonth'))</li>
            </ul>
        </div>
    </h1>

	<div>
		<a href="{{ route('agent.order.home') }}" class="subheading-2">@lang('menu.agent.payment.home')</a>
		<span class="material-icons text-muted" style="vertical-align: middle;opacity: .5;">chevron_right</span>
		<a href="{{ route('agent.payment.settlement') }}" class="subheading-2">@lang('table.calculate.care')</a>
	</div>
</div>

<form action="{{ route('agent.payment.settlement') }}">
<div class="board_search_ex ">
	<div class="b_s_ex b_s_ex2">
		<label>
			<select name="st_date">
                <?php for ($i=1; $i <= date('m'); $i++) {
                    $j = $i < 10 ? '0'.$i : $i;
                    $date = date("Y").'-'.$j;
                    $selected = $st_date == $date ? 'selected' : '';
                    echo '<option value="'.$date.'" '.$selected.'>'.$date.'</option>';
                }
                ?>
            </select>
		</label>

        <input type="text" value="{{$keyword}}" name="keyword" placeholder="@lang('sub.agent-keyword')" />

		<button type="submit">
			<img src="/assets/images/store/search_on.svg">
		</button>
	</div>

	<div class="total_ex">
        <a href="{{ route('agent.order_export', [
                'st_date' => $st_date,
                'keyword' => $keyword,
                'type'    => 'settlement',
            ]) }}" >@lang('sub.agent-download')</a>
	</div>
</div>

<div class="total_pay">
    <div class="total_ex total_ex2">
        <ul>
            <li><span>@lang('sub.agent-settle_total')</span>{{ number_format($total_cnt->total_won_sum - $total_cnt->total_won_sum * $fee) }}원 {{ number_format($total_cnt->total_daller_sum - $total_cnt->total_daller_sum * $fee) }}$</li>
            <li><span>@lang('sub.agent-settle_this_month')</span>{{ number_format($total_cnt->this_won_sum - $total_cnt->this_won_sum * $fee) }}원 {{ number_format($total_cnt->this_daller_sum - $total_cnt->this_daller_sum * $fee) }}$</li>
            <li><span>@lang('sub.agent-settle_prev_month')</span>{{ number_format($total_cnt->prev_won_sum - $total_cnt->prev_won_sum * $fee) }}원 {{ number_format($total_cnt->prev_daller_sum - $total_cnt->prev_daller_sum * $fee) }}$</li>
        </ul>

    </div>

    <div class="pay_link">
        <p><em onclick="$('#mi_publish').modal('show')">[@lang('sub.agent-settle_taxer')]</em></p>
        <!-- <p>정산 <em data-toggle="modal" data-target="#pay_none">[불가]</em></p> -->
    </div>
</div>


<div class="board_table_ex board_table_ex5">
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
			<col width="105px" />
		</colgroup>

		<thead>
			<tr>
                <th>No</th>
                <th>@lang('payment.field10')</th>
                <th>@lang('sub.agent-pay_date')</th>
                <th><a href="{{ route('agent.payment.settlement', ['sort' => $sort == 2 ? 1 : 2]) }}">@lang('sub.agent-service') <img src="/assets/images/store/{{ $sort == 2 ? 'top_up.png' : 'top_down_b.png'}}"></a></th>
                <th>@lang('sub.agent-site')</th>
                <th><a href="{{ route('agent.payment.settlement', ['sort' => $sort == 3 ? 1 : 3]) }}">@lang('sub.agent-pay_type') <img src="/assets/images/store/{{ $sort == 3 ? 'top_up.png' : 'top_down_b.png'}}"></a></th>
                <th>@lang('sub.agent-pay_term')</th>
                <th><a href="{{ route('agent.payment.settlement', ['sort' => $sort == 4 ? 1 : 4]) }}">@lang('sub.agent-pay_amount') <img src="/assets/images/store/{{ $sort == 4 ? 'top_up.png' : 'top_down_b.png'}}"></a></th>
                <th>@lang('sub.agent-settle_amount')</th>
			</tr>
		</thead>

		<tbody>
            <?php
            $total_dallor_sum = 0;
            $total_won_sum    = 0;
            foreach($results as $key => $item):
                if($item->currency == '$') {
                    $total_dallor_sum += $item->amount - $item->amount * $fee;
                }
                if($item->currency == '￦') {
                    $total_won_sum += $item->amount - $item->amount * $fee;
                }

            ?>
                <tr>
                    <td>{{ $results->total() - ($results->currentPage() - 1) * $results->perPage() - $key }}</td>
                    <td>{{ $item->order_no ?? '' }}</td>
                    <td>{!! substr($item->created_at, 0, 10).'<br/>'.substr($item->created_at, 11) !!}</td>
                    <td>{{ $item->site->name ?? '' }}</td>
                    <td>{{ $item->service->name ?? '' }}</td>
                    <td>{{ $item->type_text ?? '' }}</td>
                    <td><span>{{ ($item->service_start_at) ? $item->service_start_at->format('Y.m.d'). ' ~ ' : '' }} {{ ($item->service_end_at) ? $item->service_end_at->format('Y.m.d') : '' }}</span></td>
                    <td>{{ '('.$item->currency.') '.number_format($item->amount) }}</td>
                    <td>{{ '('.$item->currency.') '.number_format($item->amount - $item->amount * $fee) }}</td>
                </tr>
            @endforeach

            @if(count($results) == 0)
            <tr>
                <td colspan="8" class="nonelist">@lang('sub.agent-no_data')</td>
            </tr>
            @endif

		</tbody>
	</table>

	<div class="list_btn_ex">
        {!! $results->appends(Request::except('page'))->render() !!}
	</div>
</div>
</form>

<div class="board_table_ex " >
    <div class="month_total">
        <h2>{{ (int)explode('-', $st_date)[1] }}@lang('sub.agent-settle_amount2')</h2>
        <table>
            <thead>
                <tr>
                    <th>@lang('sub.agent-settle_price')</th>
                    <th>@lang('sub.agent-settle_add_price')</th>
                    <th>@lang('sub.agent-settle_total_amount')</th>
                </tr>
            </thead>
            <?php
            $st_won_sum     = $total_cnt->st_won_sum - $total_cnt->st_won_sum * $fee;
            $st_daller_sum  = $total_cnt->st_daller_sum - $total_cnt->st_daller_sum * $fee;
            ?>
            <tbody>
                <tr>
                    <td>{{ number_format($st_won_sum) }}@lang('table.pay.won') {{ number_format($st_daller_sum) }}$</td>
                    <td>{{ number_format($st_won_sum * 0.11) }}@lang('table.pay.won') {{ number_format($st_daller_sum * 0.11) }}$</td>
                    <td>{{ number_format($st_won_sum + $st_won_sum * 0.11) }}원 {{ number_format($st_daller_sum + $st_daller_sum * 0.11) }}$</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade process_modal" id="mi_publish" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">


            <div class="modal-body">

                <div>
                    <h1>@lang('sub.agent-settle_taxer')</h1>
                    <p>@lang('sub.agent-settle_tax_txt')</p>
                </div>

                <div>
                    <ul>
                        <li>
                            <h2>@lang('sub.agent-settle_company')</h2> {{$tax_info->company_name}}
                        </li>
                        <li>
                            <h2>@lang('sub.agent-settle_buss_num')</h2> {{$tax_info->buss_num}}
                        </li>
                        <li>
                            <h2>@lang('sub.agent-settle_officer')</h2> {{$tax_info->officer_name}}
                        </li>
                        <li>
                            <h2>@lang('sub.agent-settle_tax_mail')</h2> {{$tax_info->tax_mail}}
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


<div class="modal fade process_modal" id="publish" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">


            <div class="modal-body">

                <div>
                    <h1>@lang('table.calculate.tax')</h1>
                    <p>@lang('table.calculate.taxp')</p>
                </div>

                <div>
                    <ul>
                        <li>
                            <h2>@lang('table.calculate.publi_date')</h2> 2022.04.22
                        </li>
                        <li>
                            <h2>@lang('table.calculate.publi_pay')</h2> 52,000@lang('table.pay.won')
                        </li>
                    </ul>
                </div>

                <div>
                    <div class=" modal_btn modal_btn2">
                        <button data-dismiss="modal" class="notice_close">@lang('button.close')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade process_modal" id="pay_none" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">

                <div>
                    <h1>@lang('table.calculate.refuse')</h1>
                </div>
                <div>
                    <textarea>cms에서 등록한 불가 사유가 노출되는 곳.</textarea>

                    <div class="modal_btn">
                        <button data-dismiss="modal" class="notice_close">@lang('button.close')</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<style>
    #agent_nav {display: none;}
</style>

@endsection


@push('scripts')
<script>

    $(document).ready(function(){
        $('.information_icon').mouseover(function(){
            $('#refund_information').fadeIn();
        });

        $('.information_icon').mouseout(function(){
            $('#refund_information').fadeOut();
        });
    });

</script>
@endpush
