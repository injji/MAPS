@extends('layouts.cms')

@section('content')

<form action="{{ route('order.payment') }}">
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
	            'type'    	  => 'payment_list'
	        ]) }}" >다운로드</a>
	</div>
</div>

<div class="board_table_ex">
	<table class="table_no8">
		<colgroup>
			<col width="56px" />
			<col />
			<col />
			<col width="200px" />
			<col/>
			<col />
			<col />
			<col />
            <col />
			<col />
			<col />
			<col width="195px" />
		</colgroup>

		<thead>
			<tr>
				<th>No</th>
                <th>주문번호</th>
				<th>결제일</th>
				<th>제휴사</th>
				<th>서비스명</th>
				<th>사이트</th>
                <th>구분</th>
				<th>결제방식</th>
                <th>상품옵션</th>
                <th>서비스옵션</th>
				<th>결제금액</th>
                <th>이용기간</th>
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
            <tr class="tabletr">
				<td>{{ $results->total() - ($results->currentPage() - 1) * $results->perPage() - $key }}</td>
                <td>{{ $item->order_no ?? '' }}</td>
				<td>{!! substr($item->created_at, 0, 10) !!}</td>
                <td>{{ $item->service->user->company_name }}</td>
				<td>{{ $item->service->name }}</td>
                <td>{{ $item->site->name }}</td>
				<td>{{ $item->type_text ?? '' }}</td>
				<td>{{ $item->payment_type_text ?? '' }}</td>
				<td>{{ $item->plan->name ?? '' }}</td>
                {{-- <td>{{ (isset($item->plan->term)) ? $item->plan->term.' 개월' : '' }}</td> --}}
                <td>{{ $term }}</td>
				<td>{{ '('.$item->currency.') '.number_format($item->amount) }}</td>
				<td><span>{{ $item->service_start_at ? $item->service_start_at->format('Y.m.d'). ' ~ ' : '' }}{{ $item->service_end_at ? $item->service_end_at->format('Y.m.d') : '' }}</span></td>
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

@endsection

@push('scripts')
<script type="text/javascript">

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
