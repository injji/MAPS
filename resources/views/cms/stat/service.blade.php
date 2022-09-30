@extends('layouts.cms')

@section('content')

<form id="search_form" action="{{ route('stat.service') }}">
    <input type="hidden" id="sort_type" name="sort_type" value="{{ $sort_type }}">
    <div class="board_search_ex">
        <div class="b_s_ex b_s_ex2">
            <label>
                <span>기간</span>
                <input type="text" value="{{$st_date}} ~ {{$ed_date}}" id="data_range" name="data_range" />
            </label>
            <label class="mr-2 ml-2">
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

            <button type="submit">
                <img src="/assets/images/store/search_on.svg">
            </button>
        </div>

        <div class="total_ex">
            <a href="{{ route('stat_export', [
                    'st_date' => $st_date,
                    'ed_date' => $ed_date,                
                    'agent_id'    => $agent_id,
                    'service_id'  => $service_id,
                    'category_id' => $category_id,
                    'type'    => 'service'
                ]) }}" >다운로드</a>
        </div>
    </div>

    <div class="board_table_ex">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>제휴사</th>
                    <th>서비스명</th>
                    <th>카테고리</th>
                    <th class="pointer" onclick="javascript:order(1)">클릭수 
                        @if($sort_type == 1)
							<img src="/assets/images/store/top_up.png">
						@else
							<img src="/assets/images/store/top_down_b.png">
						@endif
                    </th>
                    <th class="pointer" onclick="javascript:order(2)">리뷰 
                        @if($sort_type == 2)
							<img src="/assets/images/store/top_up.png" id="order_img1">
						@else
							<img src="/assets/images/store/top_down_b.png" id="order_img1">
						@endif
                    </th>
                    <th class="pointer" onclick="javascript:order(3)">신규 
                        @if($sort_type == 3)
							<img src="/assets/images/store/top_up.png" id="order_img1">
						@else
							<img src="/assets/images/store/top_down_b.png" id="order_img1">
						@endif
                    </th>
                    <th class="pointer" onclick="javascript:order(4)">연장 
                        @if($sort_type == 4)
							<img src="/assets/images/store/top_up.png" id="order_img1">
						@else
							<img src="/assets/images/store/top_down_b.png" id="order_img1">
						@endif
                    </th>
                    <th class="pointer" onclick="javascript:order(5)">매출건수 
                        @if($sort_type == 5)
							<img src="/assets/images/store/top_up.png" id="order_img1">
						@else
							<img src="/assets/images/store/top_down_b.png" id="order_img1">
						@endif
                    </th>
                    <th class="pointer" onclick="javascript:order(6)">매출합계 
                        @if($sort_type == 6)
							<img src="/assets/images/store/top_up.png" id="order_img1">
						@else
							<img src="/assets/images/store/top_down_b.png" id="order_img1">
						@endif
                    </th>
                    <th>등록일</th>
                </tr>
            </thead>

            <tbody>
                @foreach($results as $key => $item)
                <tr>
                    <td>{{ $results->total() - ($results->currentPage() - 1) * $results->perPage() - $key }}</td>
                    <td>{{ $item->user->company_name }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->cat1->text }} > {{ $item->cat2->text }}</td>
                    <td>{{ number_format($item->view_cnt) }}</td>
                    <td>{{ number_format($item->review->count()) }}</td>
                    <td>{{ number_format($item->payment0->count()) }}</td>
                    <td>{{ number_format($item->payment1->count()) }}</td>
                    <td>{{ number_format($item->payment->count()) }}</td>
                    <td>{{ number_format($item->payment->sum('amount')) }}</td>
                    <td>{{ $item->created_at }}</td>
                </tr>
                @endforeach

                @if(count($results) == 0)
                <tr class="tabletr">
                    <td colspan="11">검색결과가 없습니다.</td>
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
<style type="text/css">
    .board_search_ex .total_ex {width: auto !important;}
</style>
@push('scripts')
<script type="text/javascript">
    
    $(document).ready(function() {
        $("#data_range").data('daterangepicker').setStartDate('{{$st_date}}');
        $("#data_range").data('daterangepicker').setEndDate('{{$ed_date}}');
    });

    function order(type) {		
		if ($("#sort_type").val() == 0) {
			$("#sort_type").val(type);
		} else {
			if (type == $("#sort_type").val())
				$("#sort_type").val(0);
			else
				$("#sort_type").val(type);
		}

		$("#search_form").submit();
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
