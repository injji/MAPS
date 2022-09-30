@extends('layouts.client')

@section('content')
<div class="p-md-5">
<div class="board_tit board_tit2">
	<h1>@lang('review.title')</h1>
	<div>
		<a href="javascript:void(0)">@lang('review.parent_title')</a>
		<span class="material-icons text-muted" style="vertical-align: middle;opacity: .5;">chevron_right</span>
		<a href="{{ route('client.review') }}">@lang('review.title')</a>
	</div>
</div>

<form id="search_form" action="{{ route('client.review') }}">
	<input type="hidden" id="sort_type" name="sort_type" value="{{ $sort_type }}">
	<div class="board_search_ex">
		<div class="b_s_ex">
			<input type="text" name="search_info" placeholder="@lang('review.placeholder1')" value="{{ $search_info }}" style="margin-left: 0">
			<button type="submit">
				<img src="/assets/images/store/search_on.svg">
			</button>
		</div>
	</div>

	<div class="board_table_ex board_table_ex2">
		<table>
			<colgroup>
				<col width="56px" />
				<col />
				<col width="300px" />
				<col />
				<col   />
				<col width="122px" />
			</colgroup>

			<thead>
				<tr>
					<th>@lang('review.field1')</th>
					<th>@lang('review.field2')</th>
					<th>@lang('review.field3')</th>
					<th>@lang('review.field4')</th>
					<th>@lang('review.field5')</th>					
					<th onclick="javascript:order(1)" style="cursor: pointer;">
						@lang('inquiry.field6') 
						@if($sort_type == 1)
							<img src="/assets/images/store/top_up.png" id="order_img1">
						@else
							<img src="/assets/images/store/top_down_b.png" id="order_img1">
						@endif
					</th>
				</tr>
			</thead>

			<tbody>
				@foreach($list as $key => $item)
					<tr>
						<td>{{ $list->total() - ($list->currentPage() - 1) * $list->perPage() - $key }}</td>
						<td>{!! substr($item->created_at, 0, 10).'<br/>'.substr($item->created_at, 11) !!}</td>
						<td>{{ $item->service->name }}</td>
						<td>{{ $item->rating }}</td>						
						<td class="a_notice_ex" onclick="javascript:detail({{ $item->id }})">{{ mb_strlen($item->content) > 20 ? mb_substr($item->content, 0, 20).'...' : $item->content }}</td>
						@if($item->answer)
							<td id="answer_aq">@lang('review.answer1')<br/>({{ substr($item->answered_at, 0, -9) }})</td>
						@else
							<td id="answer_aq">@lang('review.answer2')</td>
						@endif
					</tr>
				@endforeach

				@if(count($list) == 0)
                <tr class="tabletr">
                    <td colspan="6">검색결과가 없습니다.</td>
                </tr>
                @endif
			</tbody>
		</table>

		<div class="list_btn_ex">
			{!! $list->appends(Request::except('page'))->render() !!}
		</div>
	</div>
</form>

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
</div>
@endsection

@push('scripts')
<script>
	var QAanswer = document.querySelectorAll("#answer_aq")
	
	for (let i = 0; i < QAanswer.length; i++) {
	  if (QAanswer[i].innerHTML != "미답변") {
            QAanswer[i].style.color = "#007c4f";
        } else {
			QAanswer[i].style.color = "#666";
        }
	}
	
	var selId = 0;
	
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
</script>
@endpush
