@extends('layouts.client')

@section('content')
<div class="p-md-5">
<div class="board_tit board_tit2">
	<h1>@lang('refund.title') <span class="information_icon">?</span>
        <div id="refund_information">
            <ul>
                <li><em>@lang('refund.status1')</em>@lang('refund.help_txt1')</li>
                <li><em>@lang('refund.status4')</em>@lang('refund.help_txt2')</li>
                <li><em>@lang('refund.status2')</em>@lang('refund.help_txt3')</li>
                <li><em>@lang('refund.status3')</em>@lang('refund.help_txt4')</li>
                <li><em>@lang('refund.status6')</em>@lang('refund.help_txt5')</li>
            </ul>
        </div>
    </h1>

	<div>
		<a href="javascript:void(0)">@lang('refund.parent_title')</a>
		<span class="material-icons text-muted" style="vertical-align: middle;opacity: .5;">chevron_right</span>
		<a href="{{ route('client.refund') }}">@lang('refund.title')</a>
	</div>
</div>

<form id="search_form" action="{{ route('client.refund') }}">
	<input type="hidden" id="sort_type" name="sort_type" value="{{ $sort_type }}">
    <div class="board_table_ex board_table_ex3">
        <table class="refund_list">
            <colgroup>
				<col width="56px" />
				<col />
				<col />
				<col width="200px" />
				<col />
				<col />
				<col />
				<col />
				<col />
				<col width="142px" />
			</colgroup>


            <thead>
                <tr>
                    <th>@lang('refund.field1')</th>
                    <th>@lang('payment.field10')</th>
                    <th>@lang('refund.field2')</th>
                    <th>@lang('refund.field3')</th>
                    <th onclick="javascript:order(1)" style="cursor: pointer;">
                        @lang('refund.field4')
                        @if($sort_type == 1)
                            <img src="/assets/images/store/top_up.png" id="order_img1">
                        @else
                            <img src="/assets/images/store/top_down_b.png" id="order_img1">
                        @endif
                    </th>
                    <th>@lang('refund.field5')</th>
                    <th>@lang('refund.field6')</th>
                    <th>@lang('refund.field7')</th>
                    <th>@lang('refund.field8')</th>
                    <th onclick="javascript:order(2)" style="cursor: pointer;">
                        @lang('refund.field9')
                        @if($sort_type == 2)
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
                        <td>{{ $item->order_no ?? '' }}</td>
                        <td>{!! substr($item->created_at, 0, 10).'<br/>'.substr($item->created_at, 11) !!}</td>
                        <td>{{ $item->site->name ?? '' }}</td>
                        <td>{{ $item->service_name ?? '' }}</td>
                        <td>{{ $item->payment_type_text ?? '' }}</td>
                        <td>{{ '('.$item->currency.') '.number_format($item->amount) }}</td>
                        <td><span>{{ substr(str_replace('-', '.', $item->service_start_at), 0, 10) }}  ~ {{ substr(str_replace('-', '.', $item->service_end_at), 0, 10) }}</span></td>
                        <td>{!! substr($item->refund_request_at, 0, 10).'<br/>'.substr($item->refund_request_at, 11) !!}</td>
                        @if($item->refund_status == 1)
                            <td onclick="process({{ $item->id }}, {{ $item->refund_status }}, {{ $item->service_id }})">{{ $item->refund_status_text ?? '' }} <span class="warning_icon">!</span></td>
                        @else
                            <td onclick="process({{ $item->id }}, {{ $item->refund_status }}, {{ $item->service_id }})">{{ $item->refund_status_text ?? '' }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="list_btn_ex">
            @if( !isset($list ))
                {!! $list->appends(Request::except('page'))->render() !!}
            @endif
        </div>
    </div>
</form>

<div class="modal fade process_modal" id="process0" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div>
                    <h1>@lang('refund.modal_txt1') - <span class="service_name"></span></h1>
                    <p>@lang('refund.modal_txt5')</p>
                </div>

                <div>
                    <textarea id="refund_reason"></textarea>
                </div>

				<div>
					<div class="modal_btn">
						<button data-bs-dismiss="modal" class="notice_close">@lang('button.close')</button>
						<button type="button" onclick="save0()">@lang('button.save')</button>
					</div>
				</div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade process_modal" id="process1" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div>
                    <h1>@lang('refund.modal_txt1') - <span class="service_name"></span></h1>
                    <p><span class="warning_icon">!</span> @lang('refund.modal_txt4')</p>
                </div>

                <div>
                    <ul>
                        <li><h2>@lang('refund.modal_txt6')</h2> <span class="service_stop_at"></span></li>
                        <li><h2>@lang('refund.modal_txt7')</h2> <span class="refund_amount"></span></li>
                        <li><h2>@lang('refund.modal_txt8')</h2> <span class="refund_fee"></span></li>
                    </ul>
                </div>

				<div>
					<div class=" modal_btn modal_btn2">
						<button data-bs-dismiss="modal" class="notice_close">@lang('button.close')</button>
						<button>@lang('refund.modal_txt3')</button>
                        <button type="button" onclick="inquiryWrite()">@lang('refund.modal_txt2')</button>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade process_modal" id="process2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div>
                    <h1>@lang('refund.modal_txt1') - <span class="service_name"></span></h1>
                    <p>@lang('refund.modal_txt9')</p>
                </div>

                <div>
                    <ul>
                        <li><h2>@lang('refund.modal_txt10')</h2> <span class="refund_complete_at"></span></li>
                        <li><h2>@lang('refund.modal_txt11')</h2> <span class="refund_amount"></span></li>
                    </ul>
                </div>

				<div>
					<div class=" modal_btn modal_btn2">
						<button data-bs-dismiss="modal" class="notice_close">@lang('button.close')</button>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade process_modal" id="process3" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div>
                    <h1>@lang('refund.modal_txt1') - <span class="service_name"></span></h1>
                    <p>@lang('refund.modal_txt12')</p>
                </div>

                <div>
                    <textarea id="refusal_reason"></textarea>
                </div>

				<div>
					<div class=" modal_btn modal_btn2">
						<button data-bs-dismiss="modal" class="notice_close">@lang('button.close')</button>
                        <button type="button" onclick="inquiryWrite()">@lang('refund.modal_txt2')</button>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade process_modal" id="process4" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div>
                    <h1>@lang('refund.modal_txt1') - <span class="service_name"></span></h1>
                    <p>@lang('refund.modal_txt9')</p>
                </div>

                <div>
                    <ul>
                        <li><h2>@lang('refund.modal_txt6')</h2> <span class="service_stop_at"></span></li>
                        <li><h2>@lang('refund.modal_txt7')</h2> <span class="refund_amount"></span></li>
                        <li><h2>@lang('refund.modal_txt8')</h2> <span class="refund_fee"></span></li>
                    </ul>
                </div>

				<div>
					<div class=" modal_btn modal_btn2">
						<button data-bs-dismiss="modal" class="notice_close">@lang('button.close')</button>
                        <button type="button" onclick="inquiryWrite()">@lang('refund.modal_txt2')</button>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade process_modal" id="process5" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div>
                    <h1>@lang('refund.modal_txt1') - <span class="service_name"></span></h1>
                    <p>@lang('refund.modal_txt13')</p>
                </div>

                <div>
                    <ul>
                        <li><h2>@lang('refund.modal_txt14')</h2> <span class="refund_complete_at"></span></li>
                        <li><h2>@lang('refund.modal_txt11')</h2> <span class="refund_amount"></span></li>
                    </ul>
                </div>

				<div>
					<div class=" modal_btn modal_btn2">
						<button data-bs-dismiss="modal" class="notice_close">@lang('button.close')</button>
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
                <h3>@lang('service.txt3')</h3>
                <div>
                    <div>
                        <p>@lang('service.txt12')</p>
                        <div class="select_wrap">
                            <select id="inquiry_type">
                                @foreach(explode(',', App\Models\Cms\QuestionOption::where('type', 1)->first()->content) as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                            <span class="icoArrow"><img src="/assets/images/icons/select_t.png"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <div>
                        <p>@lang('service.txt13')</p>
                        <input type="text" id="inquiry_title" placeholder="@lang('service.placeholder2')">
                    </div>
                </div>

                <div>
                    <div>
                        <p>@lang('service.txt14')</p>
                        <textarea id="inquiry_content" placeholder="@lang('service.placeholder3')"></textarea>
                    </div>
                </div>

                <div>
                    <div>
                        <p>@lang('service.txt15')</p>
                        <div class="file_button">
                            <label for="chooseFile" class="chooseFile">
                                <p id="fileName2">@lang('service.txt16')</p>
                                <span>@lang('service.txt17')</span>
                            </label>
                        </div>
                        <input type="file" name="chooseFile" onchange="loadFile(this)">
                    </div>

                    <div>
                        <p>@lang('service.txt18')</p>
                        <div class="select_wrap">
                            <select id="inquiry_visible">
                                <option value="1">@lang('service.txt19')</option>
                                <option value="0">@lang('service.txt20')</option>
                            </select>
                            <span class="icoArrow"><img src="/assets/images/icons/select_t.png"></span>
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
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function(){
        $('.information_icon').mouseover(function(){
            $('#refund_information').fadeIn();
        });

        $('.information_icon').mouseout(function(){
            $('#refund_information').fadeOut();
        });
    });

    var selId = 0;
    var selServiceId = 0;
    var question_file = null;

    function loadFile(input) {
		question_file = input.files[0];
        var name = document.getElementById('fileName2');
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

    function process(id, status, service_id) {
        selId = id;
        selServiceId = service_id;

        let request = new FormData();
        request.set('id', selId);

        $.ajax({
            url: '/payment/info',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                if(response.code == 200){
                    $(".service_name").html(response.service.name);
                    $("#refund_reason").val(response.content.refund_reason);
                    $("#refusal_reason").val(response.content.refusal_reason);
                    $("#process"+status).modal('show');

                    if(response.content.service_stop_at)
                        $(".service_stop_at").html(response.content.service_stop_at.substr(0, 10));

                    $(".refund_amount").html(numberWithCommas(response.content.refund_amount) + response.content.currency);
                    $(".refund_fee").html(numberWithCommas(response.content.amount * 2 / 100) + response.content.currency);

                    if(response.content.refund_complete_at)
                        // $(".refund_complete_at").html(response.content.refund_complete_at.substr(0, 10));
                        $(".refund_complete_at").html(response.content.refund_complete_at);
                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });
    }

    function save0(){
        $.ajax({
            url: '/update/refund/reason',
            method: 'post',
            data: {
                id: selId,
                refund_reason: $("#refund_reason").val()
            },
            success: (response) => {
                if(response.code == 200)
                    location.href = document.URL;
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });
    }

    function inquiryWrite() {
        question_file = null;
        $("#fileName2").html("{{ __('service.txt16') }}");
        $("#inquiry_type").val(2);
        $("#inquiry_title").val('');
        $("#inquiry_content").val('');
        $("#inquiry_question_file").val('');
        $("#inquiry_visible").val(1);
        $("#inquiry_write").modal('show');
    }

    function addInquiry() {
        let request = new FormData();
        request.set('service_id', selServiceId);
        request.set('type', $("#inquiry_type").val());
        request.set('title', $("#inquiry_title").val());
        request.set('content', $("#inquiry_content").val());
        request.set('question_file', question_file);
        request.set('visible', $("#inquiry_visible").val());

        $.ajax({
            url: '/add/service/inquiry',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                if(response.code == 200) {
                    $("#inquiry_write").modal('hide');
                    toastr.success(response.message);
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
