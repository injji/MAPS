@extends('layouts.agent')

@section('content')

<form id="search_form" action="{{ route('agent.inquiry_client') }}">
	<input type="hidden" id="sort_type" name="sort_type" value="{{ $sort_type }}">
    <div class="board_search_ex">
        <div class="b_s_ex">
            <label>
                <span>@lang('inquiry.agent_txt1')</span>
                <input type="text" id="data_range" name="data_range" value="{{$st_date}} ~ {{$ed_date}}">
            </label>
            <input type="text" name="search_info" placeholder="@lang('inquiry.agent_placeholder1')" value="{{ $search_info }}">
            <button type="submit">
                <img src="/assets/images/store/search_on.svg">
            </button>
        </div>

        <div class="total_ex">
            <ul>
                <li><span>@lang('button.total')</span>{{ number_format($total_cnt) }}</li>
                <li><span>@lang('inquiry.agent_txt2')</span>{{ number_format($list->total()) }}</li>
                <li><span>@lang('inquiry.agent_txt3')</span>{{ number_format($no_answer_cnt) }}</li>
            </ul>

            <a href="{{ route('agent.inquiry.client.export', [
				'st_date' => $st_date,
				'ed_date' => $ed_date
			]) }}">@lang('sub.agent-download')</a>
        </div>
    </div>

    <div class="board_table_ex">
        <table class="table_no8">
			<colgroup>
				<col width="56px" />
				<col />
				<col />
				<col width="300px" />
				<col />
				<col />
				<col width="122px" />
			</colgroup>

            <thead>
                <tr>
                    <th>@lang('inquiry.field1')</th>
                    <th>@lang('inquiry.field2')</th>
                    <th>@lang('inquiry.field7')</th>
                    <th onclick="javascript:order(1)" style="cursor: pointer;">
						@lang('inquiry.field3')
						@if($sort_type == 1)
							<img src="/assets/images/store/top_up.png" id="order_img1">
						@else
							<img src="/assets/images/store/top_down_b.png" id="order_img1">
						@endif
					</th>
                    <th onclick="javascript:order(2)" style="cursor: pointer;">
						@lang('inquiry.field4')
						@if($sort_type == 2)
							<img src="/assets/images/store/top_up.png" id="order_img1">
						@else
							<img src="/assets/images/store/top_down_b.png" id="order_img1">
						@endif
					</th>
                    <th>@lang('inquiry.field5')</th>
                    <th onclick="javascript:order(3)" style="cursor: pointer;">
						@lang('inquiry.field6')
						@if($sort_type == 3)
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
                        <td>{{ $item->client_name }}</td>
                        <td>{{ $item->service_name }}</td>
                        <td>{{ $item->type_text }}</td>
						<td class="a_notice_ex" onclick="javascript:detail({{ $item->id }})">{{ mb_strlen($item->title) > 20 ? mb_substr($item->title, 0, 20).'...' : $item->title }}</td>
						@if($item->answer)
							<td id="answer_aq">@lang('inquiry.answer1')<br/>({{ substr($item->answered_at, 0, -9) }})</td>
						@else
							<td id="answer_aq">@lang('inquiry.answer2')</td>
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

<div class="modal fade userqa_modal" id="detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
				<div>
					<div>
						<p>@lang('inquiry.modal_txt1')</p>
						@foreach(config('app.lang_text') as $key => $item)
							<label>
								<input type="radio" name="modal_lang" value="{{ $key }}" disabled><em>{{ $item }}</em>
							</label>
						@endforeach
					</div>
				</div>

				<div>
					<div>
						<p>@lang('inquiry.modal_txt2')</p>
						<input type="text" id="modal_id" value="" disabled>
					</div>

					<div>
						<p>@lang('inquiry.modal_txt3')</p>
						<input type="text"  id="modal_service_name" value="" disabled>
					</div>
				</div>

				<div>
					<div>
						<p>@lang('inquiry.modal_txt4')</p>
						<input type="text" id="modal_type" value="" disabled>
					</div>

					<div>
						<p>@lang('inquiry.modal_txt5')</p>
						<input type="text" id="modal_time" value="" disabled>
					</div>
				</div>

				<div>
					<div>
						<p>@lang('inquiry.modal_txt6')</p>
						<input type="text" id="modal_title" value="" disabled>
					</div>
				</div>

				<div>
					<div>
						<p>@lang('inquiry.modal_txt7')</p>
						<textarea id="modal_content" disabled></textarea>
					</div>
				</div>

                <div>
					<div>
						<p>@lang('inquiry.modal_txt8')</p>
						<a id="web_img" href="javascript:void(0)" download><span id="modal_file1"></span> <span>@lang('inquiry.modal_txt9')</span></a>
					</div>
				</div>

				<div>
					<div>
						<p class="gp">@lang('inquiry.modal_txt10')</p>
						<textarea id="modal_answer" placeholder ="@lang('inquiry.placeholder2')"></textarea>
					</div>
				</div>

				<div>
					<div>
						<p class="gp">@lang('inquiry.modal_txt11')</p>
						<div class="file_button">
							<label for="chooseFile" class="chooseFile">
								<p id="fileName">@lang('inquiry.modal_txt11')</p>
								<span>@lang('service.txt17')</span>
							</label>
						</div>
						<input type="file" id="chooseFile" name="chooseFile" onchange="loadFile(this)">
					</div>
				</div>

				<div>
					<div class="modal_btn">
						<button data-bs-dismiss="modal" class="notice_close">@lang('button.close')</button>
						<button type="button" onclick="addAnswer()">@lang('review.agent_modal_txt1')</button>
					</div>
				</div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
	$(document).ready(function() {
        $("#data_range").data('daterangepicker').setStartDate('{{ $st_date }}');
        $("#data_range").data('daterangepicker').setEndDate('{{ $ed_date }}');
    });

	var answer_file = null;

	function loadFile(input) {
		answer_file = input.files[0];
		var name = document.getElementById('fileName');
		name.textContent = answer_file.name;
	};

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
			url: '/inquiry/info',
			method: 'post',
			data: request,
			contentType: false,
			processData: false,
			success: (response) => {
				if(response.code == 200){
					$('input:radio[name=modal_lang][value='+response.content.lang+']').attr('checked', true);
					$("#modal_id").val(response.content.client_name);
					$("#modal_service_name").val(response.service.name);
					$("#modal_type").val(response.content.type_text);

					if(response.content.created_at)
						$("#modal_time").val(getCorrectDateTime(response.content.created_at));

					$("#modal_title").val(response.content.title);
					$("#modal_content").val(response.content.content);
					$("#modal_answer").html(response.content.answer);

					if(response.content.question_file){
						$("#modal_file1").html(response.content.question_file.split('/', -1).pop());
						$("#web_img").attr('href', "{!! Storage::url('"+response.content.question_file+"') !!}");
					}
					else{
						$("#modal_file1").html('');
						$("#web_img").attr('href', 'javascript:void(0)');
					}

					if(response.content.answer_file)
						$("#fileName").html(response.content.answer_file.split('/', -1).pop());
					else
						$("#fileName").html('');

					$("#detail").modal('show');
				}
			},
			error: (e) => {
				console.log(e.responseJSON)
			}
		});
	}

	function addAnswer() {
		let request = new FormData();
        request.set('id', selId);
        request.set('answer', $("#modal_answer").val());
        request.set('answer_file', answer_file);

        $.ajax({
            url: '/inquiry/add/answer',
			method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                if(response.code == 200){
                    location.href = document.URL;
                }else{
                    alert(response.error);
                }

            },
            error: (e) => {
                console.log(e.responseJSON);
            }
        });
	}
</script>
@endpush
<style>
    .list_number_control {
        top: 77px;
    }
</style>
