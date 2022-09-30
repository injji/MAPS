@extends('layouts.agent')

@section('content')

<form action="{{ route('agent.order.home') }}">
<div class="board_search_ex">
	<div class="b_s_ex" >
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
		<ul>
			<li><span>@lang('button.total')</span>{{ number_format($total_cnt) }}</li>
			<li><span>@lang('sub.agent-search_result')</span>{{ number_format($results->total()) }}</li>
		</ul>

		<a href="{{ route('agent.order_export', [
	            'st_date' => $st_date,
	            'ed_date' => $ed_date,
	            'keyword' => $keyword,
	            'type'    => 'order',
	        ]) }}" >@lang('sub.agent-download')</a>
	</div>
</div>

<div class="board_table_ex board_table_ex2">
	<table>
		<colgroup>
			<col width="56px" />
			<col  />
			<col />
            <col />
			<col width="300px" />
			<col />
            <col />
            <col />
			<col />
			<col />
			<col width="105px" />
		</colgroup>

		<thead>
			<tr>
                <th></th>
				<th>No</th>
                <th>@lang('sub.agent-order_no')</th>
				<th>@lang('sub.agent-created')</th>
				<th><a href="{{ route('agent.order.home', ['sort' => $sort == 2 ? 1 : 2]) }}">@lang('sub.agent-service') <img src="/assets/images/store/{{ $sort == 2 ? 'top_up.png' : 'top_down_b.png'}}"></a></th>
                <th>@lang('sub.agent-site')</th>
                <th>@lang('sub.agent-product_option')</th>
                <th>@lang('sub.agent-service_option')</th>
				<th><a href="{{ route('agent.order.home', ['sort' => $sort == 3 ? 1 : 3]) }}">@lang('sub.agent-status') <img src="/assets/images/store/{{ $sort == 3 ? 'top_up.png' : 'top_down_b.png'}}"></a></th>
				<th>@lang('sub.agent-expire')</th>
                <th>@lang('sub.agent-add_setting')</th>
			</tr>
		</thead>

		<tbody>
			@foreach($results as $key => $item)
            <?php
                $period_type = "";
                $period = "";
                switch($item->period_type){
                    case 0 : $period = $item->period.' 개월'; break;
                    case 1 : $period = $item->period.' 일'; break;
                    case 2 : $period = $item->period; break;
                    default : break;
                }

            ?>
            <tr class="tabletr">
                <td class="table_input_radio">
					<label><input type="checkbox" name="service_select_input" data-id="{{ $item->id }}" /><em></em></label>
                </td>
				<td>{{ $results->total() - ($results->currentPage() - 1) * $results->perPage() - $key }}</td>
                <td>{{ $item->order_no }}</td>
				<td>{!! substr($item->created_at, 0, 10) !!}</td>
				<td>{{ $item->service->name }}</td>
                <td>{{ $item->site->name }}</td>
                <td>{{ $item->service_option }}</td>
                <td>{{ ($item->service_option == '인앱') ? '' : $period }}</td>
				<td>
                    <select data-id="{{ $item->id }}">
                        <option value="0" {{$item->process1=='0' ? 'selected' : ''}}>@lang('process.wait_request')</option>
                        <option value="1" {{$item->process1=='1' ? 'selected' : ''}}>@lang('process.apply')</option>
                        <option value="2" {{$item->process1=='2' ? 'selected' : ''}}>@lang('process.using')</option>
                        <option value="3" {{$item->process1=='3' ? 'selected' : ''}}>@lang('process.expired')</option>
                        <option value="4" {{($item->process1=='4' || $item->process1=='5') ? 'selected' : ''}}>@lang('process.stop')</option>
                    </select>
                </td>
				<td>{!! substr($item->service_end_at, 0, 10) !!}</td>
                <td>
                	<?php
	                	if($item->status == 0) {
	                		echo '<button type="button" onclick="showSetRequest('.$item->id.')" class="set1">'.__("sub.agent-set_req").'</button>';
	                	}
	                	else if($item->status == 1) {
	                		echo '<button type="button" onclick="showRequesting('.$item->id.')" class="set2">'.__("sub.agent-requesting").'</button>';
	                	}
	                	else {
	                		echo '<button type="button" class="set3">'.__("sub.agent-complete").'</button>';
	                	}
                	?>
                </td>
			</tr>
			@endforeach

			@if(count($results) == 0)
            <tr class="tabletr">
                <td colspan="8" class="nonelist">@lang('sub.agent-no_data')</td>
            </tr>
            @endif
		</tbody>
	</table>

	<div class="list_btn_ex">
	    <div class="end_data">
	        <p>@lang('sub.agent-service_expire')</p>
	        <input type="text" id="single_date" name="data_single" value="" placeholder="@lang('sub.agent-expire')" />
	        <button type="button" onclick="changeExpire()" >@lang('sub.agent-change')</button>
	    </div>
		{!! $results->appends(Request::except('page'))->render() !!}
	</div>
</div>
</form>

<div class="modal fade setStyle" id="set1" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitlde"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h1>@lang('sub.agent-add_setting_txt')</h1>
                <input type="text" name="MYurl" id="MyUrl" required placeholder="@lang('sub.agent-set_req_url')">
                <p>@lang('sub.agent-set_txt2')</p>

                <div class="btn2">
                    <button data-bs-dismiss="modal" class="notice_close">@lang('sub.sub-close')</button>
                    <button onclick="reqUrl()">@lang('sub.agent-btn_request')</button>
                </div>

            </div>

        </div>
    </div>
</div>

<div class="modal fade setStyle" id="set2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitlde"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p>@lang('sub.agent-areyoucomplete')</p>

                <div class="btn2">
                    <button data-bs-dismiss="modal" class="notice_close">@lang('sub.sub-close')</button>
                    <button onclick="reqComplete()">@lang('sub.agent-confirm')</button>
                </div>

            </div>

        </div>
    </div>
</div>

<style type="text/css">
	.change_expire {
		margin-bottom: 1rem;
	    display: flex;
	    justify-content: start;
	    align-items: center;
	    max-width: 400px;
	}
	.change_expire span {width: 100%;}
	.change_expire input {
		height: 50px;
	    border-radius: 5px;
	    font-size: 16px;
	    padding: 0 15px;
	    margin: 0 15px;
	    width: 100%;
	    min-width: 120px;
	    background-color: #f7f7f7 !important;
	}
	.change_expire button {
	    font-size: 16px;
	    display: flex;
	    align-items: center;
	    justify-content: center;
	    background: #007C4F;
	    color: #fff;
	    border-radius: 5px;
	    width: 180px;
	    height: 50px;
	    margin-left: 10px;
	}
</style>
@endsection

@push('scripts')
<script type="text/javascript">

	$(function(){
	    $('table select').change(function() {
	    	let id 		= $(this).data('id');
	    	let pross 	= $(this).val();

	    	changeProcess({id, pross, type: 'process'});
	    })
	});
    $(document).ready(function () {
    	//check 중복 체크 불가
        $('input[name="service_select_input"]').click(function () {
            if ($(this).prop('checked')) {
                $('input[name="service_select_input"]').prop('checked', false);
                $(this).prop('checked', true);
            }
        });
        //
        $("#data_range").data('daterangepicker').setStartDate('{{$st_date}}');
        $("#data_range").data('daterangepicker').setEndDate('{{$ed_date}}');
    });

	function changeExpire() {
		let ids = [];
		$('table input:checked').each(function() {
			let id = $(this).data('id');
			ids.push(id);
		});
		//
		if(ids.length > 0) {
			let expire = $('#single_date').val();
			if(!expire) {
				return alert("@lang('sub.agent-no_expire')");
			}
			expire = expire.replaceAll('.', '-');
			changeProcess({ids, expire, type: 'expire'});
		}
		else {
			alert("@lang('sub.agent-no_select')");
		}
	}

	let service_id = 0;
	function showSetRequest(id) {
		service_id = id;
		$('#set1').modal('show');
	}
	function reqUrl() {
		let url = $('#MyUrl').val();
		if(!url) {
			return alert("@lang('sub.agent-set_req_url')");
		}
		changeProcess({id: service_id, url, type: 'reqUrl'});
	}

	function showRequesting(id) {
		service_id = id;
		$('#set2').modal('show');
	}
	function reqComplete() {
		changeProcess({id: service_id, type: 'reqComplete'});
	}

	function changeProcess(data) {

		$.ajax({
			url: '/order/change_order',
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
</script>
@endpush
