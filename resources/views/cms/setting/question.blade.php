@extends('layouts.cms')

@section('content')

<form action="{{ route('setting.question') }}">
<div class="board_search_ex">
	<div class="b_s_ex b_s_ex2">
		<label>
			<span>기간</span>
            <input type="text" value="{{$st_date}} ~ {{$ed_date}}" id="data_range" name="data_range" />
		</label>
		<input type="text" value="" name="keyword" />

		<button type="submit">
			<img src="/assets/images/store/search_on.svg">
		</button>
	</div>

	<div class="total_ex">

		<a href="{{ route('agent.order_export', [
	            'st_date' => $st_date,
	            'ed_date' => $ed_date,
	            'type'    => 'payment_list',
	        ]) }}" >다운로드</a>
	</div>
</div>

<div class="board_table_ex">
	<table>
		<thead>
			<tr>
				<th>#</th>
				<th>등록일</th>
				<th>구분</th>
				<th>담당자</th>
                <th>연락처</th>
				<th>문의내용</th>
                <th>확인여부</th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td>1</td>
				<td>2022-02-05 10:10:30</td>
				<td>광고주</td>
                <td>홍길동</td>
				<td>010-1111-2222</td>
                <td>제휴 하고자 합니다. 연락주세요</td>
				<td>
					<button type="button" onclick="" class="set1">처리</button>
				</td>
			</tr>
		</tbody>
	</table>

	<div class="list_btn_ex">
		<ul>
			<li><img src="/assets/images/agent/prev_two.svg"></li>
			<li><img src="/assets/images/agent/prev_one.svg"></li>
			<li>1</li>
			<li><img src="/assets/images/agent/next_one.svg"></li>
			<li><img src="/assets/images/agent/next_two.svg"></li>
		</ul>
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

    function addAnswer() {
    	
    }
</script>
@endpush
