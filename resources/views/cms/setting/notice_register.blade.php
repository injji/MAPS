@extends('layouts.cms')

@section('content')

<script src="{{ config('app.cms_asset_url') }}/libs/smarteditor2/js/HuskyEZCreator.js" charset="utf-8"></script>
<form id="form">
<div class="board_table_info main-info mt-0 notice_res">
    <div>
		<table>
			<tbody>
				<tr>
					<th>구분</th>
	                <td>
						<select class="form-control d-inline-block" id="type" name="type" style="width: 40%;">
			                <option value="0">전체공지</option>
                            <option value="2">제휴사공지</option>
                            <option value="1">고객사공지</option>
			            </select>
                        <div class="form-check form-check-inline ml-3">
                            <input class="form-check-input" id="popup" name="popup" type="checkbox" value="1" />
                            <label class="form-check-label" for="popup">팝업적용</label>
                        </div>
	                </td>
				</tr>
                <tr>
                    <th>팝업이미지 <br /><span class="p-0 col-form-label small">500*500 pixel</span></th>
                    <td>
                        <div class="position-relative" style="width:20rem">
                            <img src="/assets/images/empty2.png" width="100%" class="pointer" />
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </div>
                    </td>
                </tr>
				<tr>
					<th>제목</th>
	                <td><input type="text" name="title" id="title" value="" /></td>
				</tr>
				<tr>
					<th>
                        내용<br>
                        _self(이동)<br>
                        _blank(새 창 이동)
                    </th>
	                <td>
                        <textarea class="scrollTextBox" name="content" id="content" style="width:calc( 100% - 80px );"></textarea>
                        <script type="text/javascript">
                        var oEditors = [];
                        nhn.husky.EZCreator.createInIFrame({
                            oAppRef: oEditors,
                            elPlaceHolder: "content",
                            sSkinURI: "{{ config('app.cms_asset_url') }}/libs/smarteditor2/SmartEditor2Skin_photo.html",
                            fCreator: "createSEditor2"
                        });
                        </script>
	                </td>
				</tr>
			</tbody>
		</table>
    </div>

    <div class="mt-4">
        <div class="col-12 row p-0 m-0" >
            <div class="col-12 text-right">
                <button type="button" onclick="history.go(-1)" class="btn btn-light waves-effect waves-light">취소</button>
                <button type="button" onclick="saveNotice()" class="btn btn-secondary waves-effect waves-light">저장</button>
            </div>
        </div>
    </div>
</div>
</form>
<input type="file" id="chooseFile" style="visibility:hidden;" name="chooseFile" onchange="loadFile(this)" accept="image/*" />

@endsection

<style type="text/css">
	.main-info td {text-align: left;}
    table .fa-plus {position: absolute; top: 42%; left: 47%; font-size: 2rem;}
</style>
@push('scripts')
<script type="text/javascript">

    $(document).ready(function() {
        $(document).on('click', 'img.pointer', function() {
            $('#chooseFile').trigger('click');
        });
    });

    var thumb_img = null;
    function loadFile(input) {
        thumb_img = input.files[0];
        //
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("img.pointer").attr("src", e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    };

    function saveNotice() {
        var type  = $("#type").val();
        var title = $("#title").val();
        var popup = $("#popup:checked").val();
        // 에디터의 내용을 textarea에 적용
        oEditors.getById["content"].exec("UPDATE_CONTENTS_FIELD", []);
        var content = $("#content").val();

        if(title == "") {
            alert("제목을 입력해주세요.");
            return;
        }
        if(content == "") {
            alert("내용을 입력해주세요.");
            return;
        }

        let request = new FormData();
        request.set('popup',  popup ? 1 : 0);
        request.set('type',   type);
        request.set('title',  title);
        request.set('content', content);
        request.set('thumb_img', thumb_img);

        $.ajax({
            url: '/setting/notice_update',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                if(response.code == 200){
                    location.href  = 'notice';
                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });
    }
</script>
@endpush
