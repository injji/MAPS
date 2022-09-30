@extends('layouts.cms')

@section('content')

<form action="{{ route('company.agent') }}">
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
			<a href="{{ route('company.export', [
					'st_date' => $st_date,
					'ed_date' => $ed_date,
					'type' => 2
				]) }}" >다운로드</a>
		</div>
	</div>

	<div class="board_table_ex">
		<table>
			<thead>
				<tr>
					<th>#</th>
					<th>로그인</th>
					<th>아이디</th>
					<th>회사명</th>
					<th>담당자</th>
					<th>휴대폰번호</th>
					<th>이메일</th>
					<th>서비스</th>
					<th>가입일시</th>
				</tr>
			</thead>

			<tbody>
				@foreach($list as $key => $item)
					<tr>						
						@if ($write_permission)
							<td class="pointer" onclick="location.href='edit/{{ $item->id }}'">{{ $list->total() - ($list->currentPage() - 1) * $list->perPage() - $key }}</td>
							<td><a href="{{ route('user.admin', ['type' => 2,'id' => $item->id]) }}" target="_blank" class="set3 text-black">join</a></td>
							<td class="pointer" onclick="location.href='edit/{{ $item->id }}'">{{ $item->account }}</td>
							<td class="pointer" onclick="location.href='edit/{{ $item->id }}'">{{ $item->company_name }}</td>
							<td class="pointer" onclick="location.href='edit/{{ $item->id }}'">{{ $item->manager_name }}</td>
							<td class="pointer" onclick="location.href='edit/{{ $item->id }}'">{{ $item->manager_phone }}</td>					
							<td class="pointer" onclick="location.href='edit/{{ $item->id }}'">{{ $item->manager_email }}</td>	
							<td class="pointer" onclick="location.href='edit/{{ $item->id }}'">{{ $item->service->count() }}</td>
							<td class="pointer" onclick="location.href='edit/{{ $item->id }}'">{{ $item->created_at }}</td>
						@else
							<td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $list->total() - ($list->currentPage() - 1) * $list->perPage() - $key }}</td>
							<td><a href="javascript:alert('관리자에게 권한 요청해 주세요.')" class="set3 text-black">join</a></td>
							<td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->account }}</td>
							<td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->company_name }}</td>
							<td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->manager_name }}</td>
							<td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->manager_phone }}</td>					
							<td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->manager_email }}</td>	
							<td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->service->count() }}</td>
							<td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->created_at }}</td>
						@endif
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
</script>
@endpush
