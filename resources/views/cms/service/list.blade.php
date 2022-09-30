@extends('layouts.cms')

@section('content')

<form action="{{ route('service.list') }}">
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

			<button type="submit">
				<img src="/assets/images/store/search_on.svg">
			</button>
		</div>

		<div class="total_ex">
			<a href="{{ route('service.export', [
					'st_date' => $st_date,
					'ed_date' => $ed_date,
					'agent_id' => $agent_id,
					'service_id' => $service_id,
					'category_id' => $category_id
				]) }}" >다운로드</a>
		</div>
	</div>

	<div class="board_table_ex">
		<table class="table_no8">
			<colgroup>
				<col width="56px" />
				<col />
				<col />
				<col/>
				<col />
				<col />
				<col/>
				<col />
				<col />
				<col width="185px" />
			</colgroup>
			<thead>
				<tr>
					<th>No</th>
					<th>제휴사명</th>
					<th>서비스명</th>
					<th>카테고리</th>
					<th>진행상태</th>
					<th>노출여부</th>
					<th>인앱결제</th>
					<th>조회수</th>
					<th>누적신청수</th>
					<th>등록일</th>
				</tr>
			</thead>

			<tbody>
				@foreach($list as $key => $item)
					@if ($write_permission)
					<tr class="pointer" onclick="location.href='edit/{{ $item->id }}'">
					@else
					<tr class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">
					@endif
						<td>{{ $list->total() - ($list->currentPage() - 1) * $list->perPage() - $key }}</td>
						<td>{{ $item->user->company_name }}</td>
						<td>{{ $item->name }}</td>
						<td>{{ $item->cat1->text }} > {{ $item->cat2->text ?? '' }}</td>
						<td>{{ $item->process_text }}</td>
						<td>{{ $item->visible == 1 ? 'True' : 'False'}}</td>
						<td>{{ $item->in_app_payment == 1 ? 'True' : 'False'}}</td>
						<td>{{ number_format($item->view_cnt) }}</td>
						<td>{{ number_format($item->request_cnt) }}</td>
						<td>{{ $item->created_at }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		<div class="list_btn_ex">
			{!! $list->appends(Request::except('page'))->render() !!}
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
