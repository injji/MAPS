@extends('layouts.agent')

@section('content')

<form id="search_form" action="{{ route('agent.inquiry_agent') }}">
	<input type="hidden" id="sort_type" name="sort_type" value="{{ $sort_type }}">
    <div class="board_search_ex">
        <div class="b_s_ex">
            <label>
                <span>@lang('inquiry.agent_txt1')</span>
                <input type="text" id="data_range" name="data_range" value="{{$st_date}} ~ {{$ed_date}}">
            </label>
            <input type="text" name="search_info" placeholder="@lang('inquiry.agent_placeholder2')" value="{{ $search_info }}">
            <button type="submit">
                <img src="/assets/images/store/search_on.svg">
            </button>
        </div>

        <div class="total_ex">
            <ul>
                <li><span>TOTAL</span>{{ number_format($total_cnt) }}</li>
                <li><span>@lang('inquiry.agent_txt2')</span>{{ number_format($list->total()) }}</li>
                <li><span>@lang('inquiry.agent_txt3')</span>{{ number_format($no_answer_cnt) }}</li>
            </ul>

            <a href="javascript:inquiryWrite()">@lang('button.inquiry_add')</a>
        </div>
    </div>

    <div class="board_table_ex board_table_ex3">
        <table class="table_no8">
			<colgroup>
				<col width="56px" />
				<col />
				<col />
				<col />
				<col width="110px" />
			</colgroup>

            <thead>
                <tr>
                    <th>@lang('inquiry.field1')</th>
					<th>@lang('inquiry.field2')</th>
                    <th onclick="javascript:order(1)" style="cursor: pointer;">
						@lang('inquiry.field4')
						@if($sort_type == 1)
							<img src="/assets/images/store/top_up.png" id="order_img1">
						@else
							<img src="/assets/images/store/top_down_b.png" id="order_img1">
						@endif
					</th>
                    <th>@lang('inquiry.field5')</th>
                    <th onclick="javascript:order(2)" style="cursor: pointer;">
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
                        <td>{!! $item->created_at !!}</td>
                        <td>{{ $item->type_text }}</td>
                        <td class="a_notice_ex" onclick="javascript:detail({{ $item->id }})">{{ mb_strlen($item->title) > 20 ? mb_substr($item->title, 0, 20).'...' : $item->title }}</td>
						@if($item->answered_at)
							<td id="answer_aq">@lang('inquiry.answer1')<br/>({{ substr($item->answered_at, 0, -9) }})</td>
						@else
							<td id="answer_aq">@lang('inquiry.answer2')</td>
						@endif
                    </tr>
                @endforeach

				@if(count($list) == 0)
                <tr class="tabletr">
                    <td colspan="5">@lang('messages.search_no')</td>
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
						<p>@lang('inquiry.modal_txt15')</p>
						<input type="text" id="e_modal_type" disabled>
					</div>
					<div>
						<p>@lang('inquiry.modal_txt5')</p>
						<input type="text" id="e_modal_time" disabled>
					</div>
				</div>

				<div>
					<div>
						<p>@lang('inquiry.modal_txt6')</p>
						<input type="text" id="e_modal_title" placeholder="@lang('inquiry.agent_placeholder3')" disabled>
					</div>
				</div>

				<div>
					<div>
						<p>@lang('inquiry.modal_txt7')</p>
						<textarea id="e_modal_content" placeholder="@lang('inquiry.agent_placeholder4')" disabled></textarea>
					</div>
				</div>

                <div>
					<div>
						<p>@lang('inquiry.modal_txt8')</p>
						<a id="web_img" class="web_img1" href="javascript:void(0)" download><span id="e_modal_file1"></span> <span>@lang('inquiry.modal_txt9')</span></a>
					</div>
				</div>

				<div>
					<div>
						<p class="gp">@lang('inquiry.modal_txt10')</p>
						<textarea id="e_modal_answer" placeholder ="@lang('inquiry.placeholder2')" disabled></textarea>
					</div>
				</div>

				<div>
					<div>
						<p class="gp">@lang('inquiry.modal_txt11')</p>
						<a id="web_img" class="web_img2" href="javascript:void(0)" download><span id="e_modal_file2"></span> <span>@lang('inquiry.modal_txt9')</span></a>
					</div>
				</div>

				<div>
					<div class="modal_btn">
						<button type="button" data-bs-dismiss="modal" class="notice_close">@lang('button.close')</button>
					</div>
				</div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade userqa_modal" id="inquiry_write" tabindex="-1" role="dialog"  aria-labelledby="exampleModalCenterTitlde"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
				<h3>@lang('inquiry.modal_txt12')</h3>

				<div>
                    <div>
                        <p>@lang('inquiry.field4')</p>
                        <div class="select_wrap">
                            <select id="inquiry_type">
								@foreach(explode(',', App\Models\Cms\QuestionOption::where('type', 2)->first()->content) as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                            <span class="icoArrow"><img src="/assets/images/icons/select_t.png"></span>
                        </div>
                    </div>
                </div>

				<div>
                    <div>
                        <p>@lang('inquiry.modal_txt13')</p>
                        <input type="text" id="inquiry_title" placeholder="@lang('inquiry.agent_placeholder3')">
                    </div>
                </div>

                <div>
                    <div>
                        <p>@lang('inquiry.modal_txt14')</p>
                        <textarea id="inquiry_content" placeholder="@lang('inquiry.agent_placeholder4')"></textarea>
                    </div>
                </div>

				<div>
                    <div>
                        <p>@lang('service.txt15')</p>
                        <div class="file_button">
                            <label for="chooseFile" class="chooseFile">
                                <p id="fileName">@lang('service.txt16')</p>
                                <span>@lang('service.txt17')</span>
                            </label>
                        </div>
                        <input type="file" name="chooseFile" onchange="loadFile(this)">
                    </div>
                </div>

				<div>
					<div>
						<p>@lang('service.txt23')</p>
						<div class="managercall_box">
							<input type="hidden" name="contact_phone">
							<input type="text" value="010" maxlength="3" id="phoneInputA" onkeypress="return event.keyCode === 8 || event.charCode >= 48 && event.charCode <= 57">
							<em>-</em>
							<input type="text" value="" maxlength="4" id="phoneInputB" onkeypress="return event.keyCode === 8 || event.charCode >= 48 && event.charCode <= 57">
							<em>-</em>
							<input type="text" value="" maxlength="4" id="phoneInputC" onkeypress="return event.keyCode === 8 || event.charCode >= 48 && event.charCode <= 57">
						</div>
					</div>
				</div>

				<div>
					<div class="modal_btn">
						<button data-bs-dismiss="modal" class="notice_close">@lang('button.close')</button>
						<button onclick="addInquiry()">@lang('button.register')</button>
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

	var QAanswer = document.querySelectorAll("#answer_aq");

	for (let i = 0; i < QAanswer.length; i++) {
	  if (QAanswer[i].innerHTML != "미답변") {
            QAanswer[i].style.color = "#007c4f";
        }else{
			QAanswer[i].style.color = "#666";
        }
	}

	var question_file = null;
	var selId = 0;

	function loadFile(input) {
		question_file = input.files[0];
        var name = document.getElementById('fileName');
		name.textContent = question_file.name;
	};

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
			url: '/agent/inquiry/info',
			method: 'post',
			data: request,
			contentType: false,
			processData: false,
			success: (response) => {
				if(response.code == 200){
					$("#e_modal_type").val(response.content.type_text);

					if(response.content.created_at)
						$("#e_modal_time").val(getCorrectDateTime(response.content.created_at));

					$("#e_modal_title").val(response.content.title);
					$("#e_modal_content").val(response.content.content);
					$("#e_modal_answer").html(response.content.answer);

					if(response.content.question_file){
						$("#e_modal_file1").html(response.content.question_file.split('/', -1).pop());
						$(".web_img1").attr('href', "{!! Storage::url('"+response.content.question_file+"') !!}");
					}
					else{
						$("#e_modal_file1").html('');
						$(".web_img1").attr('href', 'javascript:void(0)');
					}

					if(response.content.answer_file){
						$("#e_modal_file2").html(response.content.answer_file.split('/', -1).pop());
						$(".web_img2").attr('href', "{!! Storage::url('"+response.content.answer_file+"') !!}");
					}
					else{
						$("#e_modal_file2").html('');
						$(".web_img2").attr('href', 'javascript:void(0)');
					}

					$("#detail").modal('show');
				}
			},
			error: (e) => {
				console.log(e.responseJSON);
			}
		});
	}

	function inquiryWrite() {
        question_file = null;
        $("#fileName").html("{{ __('service.txt16') }}");
        $("#inquiry_type").val(0);
        $("#inquiry_title").val('');
        $("#inquiry_content").val('');
        $("#inquiry_write").modal('show');
    }

	function addInquiry() {
        let request = new FormData();
        request.set('type', $("#inquiry_type").val());
        request.set('title', $("#inquiry_title").val());
        request.set('content', $("#inquiry_content").val());
        request.set('question_file', question_file);

        let contact_phone = "";
        let phoneA = $('#phoneInputA').val();
        let phoneB = $('#phoneInputB').val();
        let phoneC = $('#phoneInputc').val();
        if( phoneB && phoneC ){
            contact_phone = phoneA + '-' + phoneB + '-' + phoneC;
        }
        request.set('contact_phone', contact_phone);

        $.ajax({
            url: '/add/agent/inquiry',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                if(response.code == 200) {
                    toastr.success(response.message);
					location.href = document.URL;
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
	.userqa_modal input[type='file'] {
        display: block !important;
        margin-top: -50px;
        height: 50px;
        width: 100%;
        opacity: 0;
        cursor: pointer;
    }
</style>
