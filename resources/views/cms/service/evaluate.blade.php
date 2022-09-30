@extends('layouts.cms')

@section('content')

<form action="{{ route('service.evaluate') }}">

<div class="board_table_ex">
	<table class="table_no8">
        <colgroup>
            <col width="56px" />
            <col />
            <col />
            <col/>
            <col />
            <col />
            <col width="175px" />
        </colgroup>
		<thead>
			<tr>
				<th>No</th>
				<th>제휴사명</th>
				<th>서비스명</th>
				<th>카테고리</th>
                <th>대표설명</th>
				<th>심사 요청일</th>
                <th>관리</th>
			</tr>
		</thead>

		<tbody>
			@foreach($list as $key => $item)
				<tr>
					<td>{{ $list->total() - ($list->currentPage() - 1) * $list->perPage() - $key }}</td>
                    @if ($write_permission)
                        <td><a href="edit/{{ $item->id }}">{{ $item->user->company_name }}</a></td>
                        <td><a href="edit/{{ $item->id }}">{{ $item->name }}</a></td>
                    @else
                        <td><a href="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->user->company_name }}</a>
                        <td><a href="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->name }}</a></td>
                    @endif
					<td>{{ $item->cat1->text }} > {{ $item->cat2->text }}</td>
					<td>{{ $item->service_info }}</td>
					<td>{{ $item->request_at }}</td>
					<td>
                    @if ($write_permission)
                        <button type="button" onclick="agreeModal({{ $item->id }})" class="set1">승인</button>
                        <button type="button" onclick="rejectModal({{ $item->id }})" class="set3">반려</button>
                    @else
                        <button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')" class="set1">승인</button>
                        <button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')" class="set3">반려</button>
                    @endif
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>

	<div class="list_btn_ex">
		{!! $list->appends(Request::except('page'))->render() !!}
	</div>
</div>
</form>

<div class="modal fade setStyle" id="set1" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitlde"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h1>심사반려</h1>
				<div class="b_s_ex b_s_ex2 my-5">
					<label>
						<select id="reject_reason">
			                <option value="">심사거절사유를 선택해 주세요</option>
                            @foreach(explode(',', App\Models\Cms\QuestionOption::where('type', 0)->first()->content) as $item)
							    <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
			            </select>
					</label>
				</div>

                <div class="btn2">
                    <button data-bs-dismiss="modal" class="notice_close">취소</button>
                    <button class="notice_close" onclick="rejectAgree()">확인</button>
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
                <p>해당 서비스를 승인 하시겠습니까?</p>

                <div class="btn2">
                    <button data-bs-dismiss="modal" class="notice_close">취소</button>
                    <button class="notice_close" onclick="confirmAgree()">확인</button>
                </div>

            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
	var selId = 0;

	function agreeModal(id) {
		selId = id;
		$('#set2').modal('show');
	}

	function confirmAgree() {
		$.ajax({
            url: '/update/service/process',
            method: 'post',
            data: {
                id: selId,
                process: 3
            },
            success: (response) => {
                if(response.code == 200)
                    location.href = document.URL;
            },
            error: (e) => {
                console.log(e.responseJSON);
            }
        });
	}

	function rejectModal(id) {
		selId = id;
		$('#set1').modal('show');
	}

	function rejectAgree() {
		$.ajax({
            url: '/update/service/process',
            method: 'post',
            data: {
                id: selId,
                process: 2,
				reject_reason: $("#reject_reason").val()
            },
            success: (response) => {
                if(response.code == 200)
                    location.href = document.URL;
            },
            error: (e) => {
                console.log(e.responseJSON);
            }
        });
	}
</script>
@endpush
