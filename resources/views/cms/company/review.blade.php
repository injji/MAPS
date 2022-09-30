@extends('layouts.cms')

@section('content')

<form action="{{ route('company.review') }}">
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

			<button type="submit">
				<img src="/assets/images/store/search_on.svg">
			</button>
		</div>

		<div class="total_ex">
			<a href="{{ route('company.export', [
					'st_date' => $st_date,
					'ed_date' => $ed_date,
					'agent_id' => $agent_id,
					'service_id' => $service_id,
					'type' => 5
				]) }}" >다운로드</a>
		</div>
	</div>

	<div class="board_table_ex">
		<table>
			<thead>
				<tr>
					<th style="width: 40px">#</th>
					<th style="width: 110px">등록일</th>
					<th style="width: 120px">제휴사</th>
					<th>서비스명</th>
					<th style="width: 60px">평점</th>
					<th>내용</th>
					<th style="width: 120px">고객사</th>
					<th style="width: 135px">관리</th>
				</tr>
			</thead>

			<tbody>
				@foreach($list as $key => $item)
					<tr>
						<td>{{ $list->total() - ($list->currentPage() - 1) * $list->perPage() - $key }}</td>
						<td>{{ $item->created_at }}</td>
						<td>{{ $item->service->user->company_name }}</td>
						<td>{{ $item->service->name }}</td>
						<td>{{ $item->rating }}</td>						
						<td class="a_notice_ex" onclick="javascript:detail({{ $item->id }})">{{ mb_strlen($item->content) > 20 ? mb_substr($item->content, 0, 20).'...' : $item->content }}</td>
						<td>{{ $item->author->company_name }}</td>
						@if ($write_permission)
							<td>
								@if ($item->visible == 1)
									<button type="button" onclick="visible({{ $item->id }}, 0)" class="set1">노출</button>
								@else
									<button type="button" onclick="visible({{ $item->id }}, 1)" class="set3">노출</button>
								@endif
								<button type="button" onclick="del({{ $item->id }})" class="set2">삭제</button>
							</td>
						@else
							<td>
								@if ($item->visible == 1)
									<button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')" class="set1">노출</button>
								@else
									<button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')" class="set3">노출</button>
								@endif
								<button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')" class="set2">삭제</button>
							</td>
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

<div class="modal fade setStyle" id="set2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitlde"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p>해당 리뷰를 삭제 하시겠습니까?</p>

                <div class="btn2">
                    <button data-bs-dismiss="modal" class="notice_close">취소</button>
                    <button class="notice_close" onclick="confirmDel()">삭제</button>
                </div>
                
            </div>

        </div>
    </div>
</div>

<div class="modal fade userqa_modal" id="detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
				<div>
					<div>
						<p>@lang('review.modal_txt1')</p>
						@foreach(config('app.lang_text') as $key => $item)						
							<label>
								<input type="radio" name="modal_lang" value="{{ $key }}" disabled><em>{{ $item }}</em>
							</label>
						@endforeach
					</div>
				</div>

				<div>
					<div>
						<p>@lang('review.modal_txt2')</p>
						<input type="text" id="modal_id" value="" disabled>
					</div>

					<div>						
						<p>@lang('review.modal_txt3')</p>
						<input type="text"  id="modal_service_name" value="" disabled>
					</div>
				</div>

                <div>
					<div>
						<p>@lang('review.modal_txt4')</p>
						<input type="text" id="modal_time" value="" disabled>
					</div>

					<div>						
						<p>@lang('review.modal_txt5')</p>
						<input type="text" id="modal_rating" value="" disabled>
					</div>
				</div>

				<div>
					<div>						
						<p>@lang('review.modal_txt6')</p>
						<textarea id="modal_content" disabled></textarea>
					</div>
				</div>
				
				<div>
					<div>
						<p class="gp">@lang('review.modal_txt7')</p>						
						<textarea id="modal_answer" placeholder ="@lang('review.placeholder2')" disabled></textarea>
					</div>
				</div>
				
				<div>
					<div class="modal_btn">
						<button data-bs-dismiss="modal" class="notice_close">@lang('button.close')</button>
					</div>
				</div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	
    $(document).ready(function() {
        $("#data_range").data('daterangepicker').setStartDate('{{$st_date}}');
        $("#data_range").data('daterangepicker').setEndDate('{{$ed_date}}');
    });

	var selId = 0;

    function visible(id, val) {
    	$.ajax({
            url: '/update/review/visible',
            method: 'post',
            data: {
                id: id,
                visible: val
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

	function del(id) {
		selId = id;
		$("#set2").modal('show');
	}

	function confirmDel() {
		$.ajax({
            url: '/del/review',
            method: 'post',
            data: {
                id: selId
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

	function detail(id) {
		selId = id;

		let request = new FormData();        
		request.set('id', selId);

		$.ajax({
			url: '/review/info',
			method: 'post',
			data: request,
			contentType: false,
			processData: false,
			success: (response) => {
				if(response.code == 200){
					$('input:radio[name=modal_lang][value='+response.content.lang+']').attr('checked', true);
					$("#modal_id").val(response.content.id);
					$("#modal_service_name").val(response.service.name);
					
					if(response.content.created_at)
						$("#modal_time").val(getCorrectDateTime(response.content.created_at));

					$("#modal_rating").val(response.content.rating);
					$("#modal_content").val(response.content.content);
					$("#modal_answer").html(response.content.answer);
					
					$("#detail").modal('show');
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
