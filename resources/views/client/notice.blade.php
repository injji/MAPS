@extends('layouts.client')

@section('content')
<div class="p-md-5">


	<div class="board_tit">
		<h1>공지사항</h1>
	</div>

	<div class="board_table_ex board_table_ex1">
		<table>
			<thead>
				<tr>
					<th>No</th>
					<th>제목</th>
					<th>등록일자</th>
				</tr>
			</thead>

			<tbody>
				@foreach($list as $key => $item)
					<tr>
						<td>{{ $list->total() - ($list->currentPage() - 1) * $list->perPage() - $key }}</td>
						<td onclick="detail({{ $item }})">{{ $item->title }}</td>
						<td>{!! str_replace('-', '.', $item->created_at) !!}</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		<div class="list_btn_ex">
			{!! $list->appends(Request::except('page'))->render() !!}
		</div>
	</div>

	<div class="modal fade client_notice_detail" id="detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
		aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="m_title"></h5>
					<span id="m_time"></span>
				</div>
				<div class="modal-body">
					{{-- <img src="/assets/images/agent/img740.jpg" id="m_img"> --}}
					<div id="m_content"></div>
					<button data-bs-dismiss="modal" class="notice_close">@lang('button.close')</button>
				</div>

			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
	function detail(obj) {
		$("#m_title").html(obj.title);
		$("#m_time").html(getCorrectDateTime(obj.created_at).replace(/-/g, '.'));

		// if (obj.img != null && obj.img != '') {
		// 	$("#m_img").attr('src', '/storage/'+obj.img);
		// 	$("#m_img").show();
		// } else {
		// 	$("#m_img").hide();
		// }

		$("#m_content").html(obj.content);
		$("#detail").modal('show');

		$.ajax({
			url: '/notice/hits',
			method: 'post',
			data: {id: obj.id},
			success: (response) => {}
		});
	}
</script>
@endpush

<style>
    #m_content {
        text-align: left;
        width: 100%;
        white-space: pre-line;
    }
</style>
