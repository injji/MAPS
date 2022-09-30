@extends('layouts.cms')

@section('content')

<form action="{{ route('order.list') }}">
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

        {{-- <input type="text" value="{{$keyword}}" name="keyword" placeholder="@lang('sub.agent-site')" /> --}}
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
	            'type'    	  => 'order'
	        ]) }}" >다운로드</a>
	</div>
</div>

<div class="board_table_ex board_table_ex2">
	<table>
		<thead>
			<tr>
                <th></th>
				<th>No</th>
                <th>주문번호</th>
				<th>신청일</th>
				<th>제휴사</th>
				<th>서비스명</th>
				{{-- <th>카테고리</th> --}}
                <th>상품옵션</th>
                <th>서비스옵션</th>
                <th>사이트명</th>
				<th>상태</th>
                <th>만료일</th>
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
                <td>{{ $item->service->user->company_name }}</td>
				<td>{{ $item->service->name }}</td>
				{{-- <td>{{ $item->service->cat1->text }} > {{ $item->service->cat2->text }}</td> --}}
                <td>{{ $item->service_option }}</td>
                <td>{{ ($item->service_option == '인앱') ? '' : $period }}</td>
                <td>{{ $item->site->name }}</td>
				<td>
                    <select data-id="{{ $item->id }}" {{ !$write_permission ? 'disabled' : '' }}>
                        <option value="0" {{$item->process=='0' ? 'selected' : ''}}>@lang('process.wait_request')</option>
                        <option value="1" {{$item->process=='1' ? 'selected' : ''}}>@lang('process.apply')</option>
                        <option value="2" {{$item->process=='2' ? 'selected' : ''}}>@lang('process.using')</option>
                        <option value="3" {{$item->process=='3' ? 'selected' : ''}}>@lang('process.expired')</option>
                        <option value="4" {{$item->process=='4' ? 'selected' : ''}}>@lang('process.stop')</option>
                    </select>
                </td>
				<td>{!! substr($item->service_end_at, 0, 10) !!}</td>
			</tr>
			@endforeach

			@if(count($results) == 0)
            <tr class="tabletr">
                <td colspan="9">@lang('sub.agent-no_data')</td>
            </tr>
            @endif
		</tbody>
	</table>

	<div class="list_btn_ex">
	    <div class="end_data">
	        <p>@lang('sub.agent-service_expire')</p>
	        <input type="text" id="single_date" name="data_single" value="" placeholder="@lang('sub.agent-expire')" />
			@if ($write_permission)
				<button type="button" onclick="changeExpire()">@lang('sub.agent-change')</button>
			@else
				<button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">@lang('sub.agent-change')</button>
			@endif
	    </div>
		{!! $results->appends(Request::except('page'))->render() !!}
	</div>
</div>
</form>

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

	function changeProcess(data) {

		$.ajax({
			url: '/order/change_order',
			method: 'post',
			data: data,
			success: (response) => {
				if(response.code == 200){
					location.reload();
				}else{
                    alert(response.error);
                }
			},
			error: (e) => {
				console.log(e.responseJSON)
			}
		});
	}

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
