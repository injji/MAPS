@extends('layouts.cms')

@section('content')

<form id="search_form" action="{{ route('stat.agent') }}">
    <input type="hidden" id="sort_type" name="sort_type" value="{{ $sort_type }}">
    <div class="board_search_ex">
        <div class="b_s_ex b_s_ex2">
            <label class="mr-2">
                <span>기간</span>
                <input type="text" value="{{$st_date}} ~ {{$ed_date}}" id="data_range" name="data_range" />
            </label>

            <button type="submit">
                <img src="/assets/images/store/search_on.svg">
            </button>
        </div>

        <div class="total_ex">
            <a href="{{ route('stat_export', [
                    'st_date' => $st_date,
                    'ed_date' => $ed_date,
                    'type'    => 'agent',
                ]) }}" >다운로드</a>
        </div>
    </div>

    <div class="board_table_ex">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>제휴사명</th>
                    <th class="pointer" onclick="javascript:order(1)">서비스 
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
                    <th>가입일시</th>
                </tr>
            </thead>

            <tbody>
                @foreach($results as $key => $item)
                <tr>
                    <td>{{ $results->total() - ($results->currentPage() - 1) * $results->perPage() - $key }}</td>
                    <td>{{ $item->company_name }}</td>
                    <td>{{ number_format($item->service->count()) }}</td>
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
                    <td colspan="9">검색결과가 없습니다.</td>
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
</script>
@endpush
