@extends('layouts.cms')

@section('content')
<div class="dashboard_wrap">
    <div class="shot_information">
        <div>
            <div>
                <h3>이번달 제휴사 가입수</h3>

                <h1>{{ number_format($cur_month_cnt1) }}</h1>

                <ul>
                    <li><em>today</em>{{ number_format($today_cnt1) }}</li>
                    <li><em>total</em>{{ number_format($total_cnt1) }}</li>
                </ul>
            </div>
        </div>

        <div>
            <div>
                <h3>이번달 고객사 가입수</h3>

                <h1>{{ number_format($cur_month_cnt2) }}</h1>

                <ul>
                    <li><em>today</em>{{ number_format($today_cnt2) }}</li>
                    <li><em>total</em>{{ number_format($total_cnt2) }}</li>
                </ul>
            </div>
        </div>

        <div>
            <div>
                <h3>이번달 심사요청 서비스</h3>

                <h1>{{ number_format($cur_month_cnt3) }}</h1>

                <ul>
                    <li><em>today</em>{{ number_format($today_cnt3) }}</li>
                    <li><em>total</em>{{ number_format($total_cnt3) }}</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="shot_information mt-3">
        <div>
            <div>
                <h3>이번달 서비스 신청수</h3>

                <h1>{{ number_format($cur_month_cnt4) }}</h1>

                <ul>
                    <li><em>today</em>{{ number_format($today_cnt4) }}</li>
                    <li><em>total</em>{{ number_format($total_cnt4) }}</li>
                </ul>
            </div>
        </div>

        <div>
            <div>
                <h3>이번달 서비스 진행수</h3>

                <h1>{{ number_format($cur_month_cnt5) }}</h1>

                <ul>
                    <li><em>today</em>{{ number_format($today_cnt5) }}</li>
                    <li><em>total</em>{{ number_format($total_cnt5) }}</li>
                </ul>
            </div>
        </div>

        <div>
            <div>
                <h3>이번달 매출액</h3>

                <h1>{{ number_format($cur_month_cnt6) }}</h1>

                <ul>
                    <li><em>today</em>{{ number_format($today_cnt6) }}</li>
                    <li><em>total</em>{{ number_format($total_cnt6) }}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="bell">
        <div class="notice">
            <h2>
                서비스 심사 목록
                <span><a href="{{ route('service.evaluate') }}">더보기</a></span>
            </h2>

            <div class="board_table_ex board_table_ex_cms">

                <table>
                    <colgroup>
                        <col width="105px" />
                        <col />
                        <col />
                        <col width="188px" />
                    </colgroup>

                    <thead>
                        <tr>
                            <th>제휴사명</th>
                            <th>서비스명</th>
                            <th>카테고리</th>
                            <th>심사 요청일</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($service_list as $key => $item)                            
                            @if ($write_permission3)
                            <tr class="tabletr pointer @if($item->process == 1) active @endif" onclick="location.href='service/edit/{{ $item->id }}'">
                            @else
                            <tr class="tabletr pointer @if($item->process == 1) active @endif" onclick="alert('관리자에게 권한 요청해 주세요.')">
                            @endif                            
                                <td>{{ $item->user->company_name }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->cat1->text }} > {{ $item->cat2->text }}</td>
                                <td>{{ $item->request_at }}</td>
                            </tr>
                        @endforeach                        
                    </tbody>
                </table>
            </div>

           
        </div>

        <div class="notice">
            <h2>
                제휴사문의
                <span><a href="{{ route('company.agent_question') }}">더보기</a></span>
            </h2>

            <div class="board_table_ex board_table_ex_cms">
                <table>
                    <colgroup>
                        <col width="105px" />
                        <col />
                        <col width="188px" />
                    </colgroup>

                    <thead>
                        <tr>
                            <th>제휴사</th>
                            <th>문의유형</th>
                            <th>등록일</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($agent_inquiry_list as $key => $item)
                            @if($item->answered_at)
                            <tr class="tabletr pointer" onclick="detail({{ $item->id }})">
                            @else
                            <tr class="tabletr pointer active" onclick="detail({{ $item->id }})">
                            @endif
                                <td>{{ $item->user->company_name }}</td>
                                <td>{{ $item->type_text }}</td>
                                <td>{{ $item->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>

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
									<div id="web_img" onclick="detailImg1()"><span id="modal_file1"></span> <span>클릭 시 확인 가능</span></div>
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
								<td colspan="3" id="modal_file2"></td>
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
						@if ($write_permission8)
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
    var answer_file = null;
	var selId = 0;
	var file1 = '';

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
						if(response.content.answer_file)
							$("#modal_file2").html(response.content.answer_file.split('/', -1).pop());

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
						file1 = response.content.question_file;
					}						
					else{
						$("#modal_file1").html('');
						file1 = '';
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
                if(response.code == 200)
                    location.href = document.URL;
            },
            error: (e) => {
                console.log(e.responseJSON);
            }
        });
    }

    function detailImg1(){
		if(file1 != ''){
			$("#img").attr('src', "{!! Storage::url('"+file1+"') !!}");
        	$("#web_pop").modal('show');
		}
    }
</script>
@endpush

<style>
    #agent_nav {
        display: none !important;
    }
    .modal table td {text-align: left;}
</style>