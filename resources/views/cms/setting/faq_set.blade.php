@extends('layouts.cms')

@section('content')

<div class="board_search_ex tbtn">

	<div class="total_ex">
		<a data-toggle="modal" data-target="#new_faq" onclick="faqCreate()">신규등록</a>
	</div>
</div>

<div class="board_table_ex">

	<table class="table_no8" id="table-1">
		<colgroup>
			<col width="56px">
			<col>
			<col>
			<col width="86px">
		</colgroup>

		<thead>
			<tr>
				<th>No</th>
				<th>구분</th>
				<th>질문내용</th>
                <th>조회수</th>
			</tr>
		</thead>

		<tbody id="drag_table">

			{{-- <tr class="tabletr">
                <td colspan="4">검색결과가 없습니다</td>
            </tr> --}}
            @foreach ( $faq as $item )
                <tr id="faq{{ $item->id }}" faq_id="{{ $item->id }}" order="{{ $item->order }}">
                    <td><i class="fa fa-align-justify mx-2" aria-hidden="true"></i></td>
                    <td>{{ $item->faq_category }}</td>
                    <td class="newfaq_mo" data-toggle="modal" data-target="#new_faq" onclick="modalSetId({{ $item->id }},`{{ $item->faq_category }}`,`{{ $item->question }}`,`{{ $item->answer }}`)">{{ $item->question }}</td>
                    <td>{{ $item->hits }}</td>
                </tr>
            @endforeach
        </tbody>
	</table>

	<div class="list_btn_ex">

	</div>
</div>
<!-- modal -->
<div class="modal fade" id="new_faq" tabindex="-1" role="dialog" aria-labelledby="basicModal"
aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
	<div class="modal-content">
		<div class="modal-body">
            <input type="hidden" id="modalSetId" value="">
            <?php
                $faq_options = [];
                $options = App\Models\Cms\QuestionOption::where('type', 4)->first();
                if($options){
                    $faq_options_arr = explode(',',$options->content);
                    $selectCol = count($faq_options_arr) + 1;
                    foreach($faq_options_arr as $option){
                        $faq_options[] = [
                            'value' => $option,
                            'text' => $option,
                        ];
                    }
                }

            ?>
            <select class="form-select" name="faq_category" onchange="" id="faq_category">
                <option value="">선택해주세요.</option>
                @foreach($faq_options as $key => $option)
                    <option onclick="" value="{{ $option['value'] }}" {{ ($key == 0) ? 'selected=' : '' }}>{{ $option['text'] }}</option>
                @endforeach
            </select>

            {{-- <select class="form-select top-unset" name="type" onchange="" id="type">
                <option value="">선택해주세요.</option>
                <option onclick="" value="0" selected="">서비스문의</option>
                <option onclick="" value="1">결제문의</option>
                <option onclick="" value="2">기타</option>
            </select> --}}

            <input class="form-control" value="" id="question" name="question" type="text" placeholder="질문을 입력하세요." accept=".">

            <textarea placeholder="답변" id="answer" name="answer"></textarea>
            <ul>
                <li><button onclick="faqDelete()">삭제</button></li>
                <li><button onclick="faqSave()">등록</button></li>
            </ul>
		</div>
	</div>
</div>
</div> <!-- modal -->

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/0.9.1/jquery.tablednd.js" integrity="sha256-d3rtug+Hg1GZPB7Y/yTcRixO/wlI78+2m08tosoRn7A=" crossorigin="anonymous"></script>
<script>

	$(document).ready(function() {
    // Initialise the table
    $("#table-1").tableDnD({
		onDragClass: 'myDrag',
        onDrop: function(table, row) {
            // console.log(table.tBodies[0].rows);
            let faq_id = new Array();
            let faq_order = new Array();

            for(let i=0; i<table.tBodies[0].rows.length; i++){
                faq_id.push(table.tBodies[0].rows[i].getAttribute('faq_id'));
                faq_order.push(table.tBodies[0].rows[i].getAttribute('order'));
            }

            console.log(faq_id);
            console.log(faq_order);

            let request = new FormData();

            request.set('faq_id', faq_id);
            request.set('faq_order', faq_order);

            $.ajax({
                url: '/setting/faq_order',
                method: 'post',
                data: request,
                contentType: false,
                processData: false,
                success: (response) => {
                    console.log('성공');
                    console.log(response);
                    // alert('저장되었습니다.');
                    // location.reload();
                },
                error: (e) => {
                    console.log(e.responseJSON)
                }
            })
        }
	});
    // console.log(ordered_items);

});
    function modalSetId(id,faq_category,question,answer){
        console.log(id);
        console.log(faq_category);
        console.log(question);
        console.log(answer);
        $('#modalSetId').val(id);
        $('#faq_category').val(faq_category);
        // $('#type').val(type).prop("selected",true);
        $('#question').val(question);
        $('#answer').val(answer);
    }

    function faqCreate(){
        $('#faq_category').val("");
        $('#question').val("");
        $('#answer').val("");
    }

    function faqSave(){

        let request = new FormData();
        let id = $('#modalSetId').val();

        request.set('faq_category', $("#faq_category").val());
        // request.set('type', $("#type").val());
        request.set('question', $("#question").val());
        request.set('answer', $("#answer").val());

        let url = '/setting/faq';
        if (id) {
            request.set('id', id);
            url = '/setting/faq_update';
        }

        $.ajax({
            url: url,
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                alert('저장되었습니다.');
                location.reload();
            },
            error: (e) => {
				console.log(e.responseJSON)
			}
        })
    }

    function faqDelete(){

        let request = new FormData();
        let id = $('#modalSetId').val();
        request.set('id', id);

        $.ajax({
            url: '/setting/faq_delete',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                alert('삭제되었습니다.');
                location.reload();
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        })
    }
</script>
@endpush
