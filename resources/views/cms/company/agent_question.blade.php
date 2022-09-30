@extends('layouts.cms')

@section('content')

<form action="{{ route('company.agent_question') }}">
<div class="board_search_ex">
	<div class="b_s_ex b_s_ex2">
		<label class="mr-2">
			<span>기간</span>
            <input type="text" value="{{$st_date}} ~ {{$ed_date}}" id="data_range" name="data_range" />
		</label>
		<label class="mr-2">
			<select name="agent_id">
				<option value="">제휴사선택</option>
				@foreach($agent_list as $key => $item)
					<option value="{{ $item->id }}" {{ $agent_id == $item->id ? 'selected' : ''}}>{{ $item->company_name }}</option>
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
				'type' => 4
			]) }}" >다운로드</a>
	</div>
</div>

<div class="board_table_ex">
	<table>
		<thead>
			<tr>
				<th>#</th>
				<th>등록일</th>
				<th>제휴사</th>
                <th>문의유형</th>
				<th>제목</th>
                <th>답변여부</th>
			</tr>
		</thead>

		<tbody>
			@foreach($list as $key => $item)
				<tr class="pointer" onclick="detail({{ $item->id }})">
					<td>{{ $list->total() - ($list->currentPage() - 1) * $list->perPage() - $key }}</td>
					<td>{{ $item->created_at }}</td>
					<td>{{ $item->user->company_name }}</td>
					<td>{{ $item->type_text }}</td>
					<td>{{ mb_strlen($item->title) > 20 ? mb_substr($item->title, 0, 20).'...' : $item->title }}</td>
					<td id="answer_aq">{{ $item->answered_at != null ? '답변완료' : '미답변' }}</td>
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
				<div class="board_table_info p-0">
					<table>
						<tbody>
							<tr>
								<th>제휴사명</th>
								<td colspan="3" id="modal_agent_name"></td>
							</tr>
							<tr>
								<th>문의일시</th>
								<td id="modal_time"></td>
								<th>문의유형</th>
								<td id="modal_type"></td>
							</tr>
							<tr>
								<th>제목</th>
								<td colspan="3" id="modal_title"></td>
							</tr>
							<tr>
								<th>내용</th>
								<td colspan="3" id="modal_content" style="white-space: pre-line;"></td>
							</tr>
							<tr>
								<th>첨부파일</th>
								<td colspan="3">
									<a id="web_img" href="javascript:void(0)" download><span id="modal_file1"></span> <span>클릭 시 확인 가능</span></a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="board_table_info p-0" id="answer1">
					<table>
						<tbody>
							<tr>
								<th>답변내용</th>
								<td colspan="3">
									<textarea id="modal_answer1" placeholder="답변을 등록하세요."></textarea>
								</td>
							</tr>
							<tr>
								<th>첨부파일</th>
								<td colspan="3">
									<div class="file_button">
										<label for="chooseFile" class="chooseFile">
											<p id="fileName">첨부파일</p>
										</label>
									</div>
									<input type="file" id="chooseFile" name="chooseFile" onchange="loadFile(this)">
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="board_table_info p-0" id="answer2">
					<table>
						<tbody>
							<tr>
								<th>답변내용</th>
								<td colspan="3" id="modal_answer2"></td>
							</tr>
							<tr>
								<th>첨부파일</th>
								<td colspan="3"><a href="javascript:void(0)" id="modal_file2" download></a></td>
							</tr>
							<tr>
								<th>답변일시</th>
								<td colspan="3" id="modal_answer_time"></td>
							</tr>
						</tbody>
					</table>
				</div>

				<div>
					<div id="modal_btn1" class="modal_btn">
						<button data-bs-dismiss="modal" class="notice_close">취소</button>
						@if ($write_permission)
							<button type="button" onclick="addAnswer()">저장</button>
						@else
							<button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">저장</button>
						@endif
					</div>
					<div id="modal_btn2" class="modal_btn">
						<button data-bs-dismiss="modal" class="notice_close">닫기</button>
					</div>
				</div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade web_pop_0" id="web_pop" tabindex="-1" role="dialog"  aria-labelledby="exampleModalCenterTitlde"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
				<img id="img" src="" style="width: 100%;">
				<button data-bs-dismiss="modal" class="notice_close" style="margin-top: 10px;">@lang('button.close')</button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
	var QAanswer = document.querySelectorAll("#answer_aq")

	for (let i = 0; i < QAanswer.length; i++) {
	  if (QAanswer[i].innerHTML != "미답변") {
            QAanswer[i].style.color = "#007c4f";
        } else {
			QAanswer[i].style.color = "#666";
        }
	}

    $(document).ready(function() {
        $("#data_range").data('daterangepicker').setStartDate('{{$st_date}}');
        $("#data_range").data('daterangepicker').setEndDate('{{$ed_date}}');
    });

    var answer_file = null;
	var selId = 0;

	function loadFile(input) {
		answer_file = input.files[0];
		var name = document.getElementById('fileName');
		name.textContent = answer_file.name;
	};

	function detail(id) {
		selId = id;

		$.ajax({
			url: '/agent/inquiry/info',
			method: 'post',
			data: {
				id: id
			},
			success: (response) => {
				if(response.code == 200){
					$("#modal_agent_name").html(response.user.company_name);

					if(response.content.created_at)
						$("#modal_time").html(getCorrectDateTime(response.content.created_at));

					$("#modal_type").html(response.content.type_text);
					$("#modal_title").html(response.content.title);
					$("#modal_content").html(response.content.content);

					if (response.content.answered_at) {
						if(response.content.answer_file){
							$("#modal_file2").html(response.content.answer_file.split('/', -1).pop());
							$("#modal_file2").attr('href', "{!! Storage::url('"+response.content.answer_file+"') !!}");
						}
						else{
							$("#modal_file2").html('');
							$("#modal_file2").attr('href', 'javascript:void(0)');
						}

						$("#modal_answer2").html(response.content.answer);
						$("#modal_answer_time").html(getCorrectDateTime(response.content.answered_at));

						$("#answer1").hide();
						$("#answer2").show();
						$("#modal_btn1").hide();
						$("#modal_btn2").show();
					} else {
						$("#answer2").hide();
						$("#answer1").show();
						$("#modal_btn2").hide();
						$("#modal_btn1").show();
					}

					if(response.content.question_file){
						$("#modal_file1").html(response.content.question_file.split('/', -1).pop());
						$("#web_img").attr('href', "{!! Storage::url('"+response.content.question_file+"') !!}");
					}
					else{
						$("#modal_file1").html('');
						$("#web_img").attr('href', 'javascript:void(0)');
					}

					$('#detail').modal('show');
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
        request.set('answer', $("#modal_answer1").val());
        request.set('answer_file', answer_file);

        $.ajax({
            url: '/agent/inquiry/add/answer',
			method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                console.log(response);
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

<style type="text/css">
	.modal table td {text-align: left;}
</style>
