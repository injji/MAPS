@extends('layouts.cms')

@section('content')

<form action="{{ route('service.restoration') }}">
	<div class="board_table_ex">
		<table class="table_no8">
			<colgroup>
				<col width="56px" />
				<col />
				<col />
				<col/>
				<col />
				<col width="185px" />
			</colgroup>
			<thead>
				<tr>
					<th>No</th>
					<th>제휴사명</th>
					<th>서비스명</th>
					<th>카테고리</th>
					<th>대표설명</th>
					<th>심사 요청일</th>
					<th>반려 등록일</th>
					<th>반려사유</th>
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
						<td>{{ $item->cat1->text }} > {{ $item->cat2->text }}</td>
						<td>{{ $item->service_info }}</td>
						<td>{{ $item->request_at }}</td>
						<td>{{ $item->reject_at }}</td>
						<td>{{ $item->reject_reason }}</td>
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