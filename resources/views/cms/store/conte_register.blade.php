@extends('layouts.cms')

@section('content')
<script src="{{ config('app.cms_asset_url') }}/libs/smarteditor2/js/HuskyEZCreator.js" charset="utf-8"></script>
<div class="board_table_info main-info mt-0">
    <div id="conte_res">
        <div class="cr_input">
            <input type="hidden" id="content_id" name="id" value="{{ $content->id ?? '' }}" />
            <table>
                <tbody>
                    <tr>
                        <th>
                            제목<br>
                            (최대 40자)
                        </th>
                        <td colspan="3"><input type="text" id="title" name="title" value="{{ $content->title ?? '' }}" placeholder="컨텐츠 제목 입력"  maxlength="40"/></td>
                    </tr>
                    <tr>
                        <th>
                            설명<br>
                            (최대 50자)
                        </th>
                        <td colspan="3"><input type="text" id="description" name="description" value="{{ $content->description ?? '' }}" placeholder="설명 입력" maxlength="50"/></td>
                    </tr>

                    <tr>
                        <th style="width: 20%;">이미지<br>1000 * 564</th>
                        <td style="width: 30%;">
                        <div class="ban_img3">
                            <?php
                                $img_src = '/assets/images/empty2.png';
                                if($content){
                                    if($content->img){
                                        $img_src = Storage::url($content->img);
                                    }
                                }
                            ?>
                            <input type="hidden" id="preview_image" value="{{ $img_src }}"/>
                            <img class="pointer icon-img" src="{{ $img_src }}" width="233" height="132" />
                            <input type="file" id="icon" style="visibility:hidden;" name="icon" onchange="loadFile(this, 'icon-img')" accept="image/*"  style="display:none"/>
                        </div>

                        </td>
                        <th style="width: 20%;">배너여부</th>
                        <td style="width: 30%;" class="banynn">
                            <?php
                                $banner = 0;
                                if($content){
                                    if($content->banner == 1){
                                        $banner = 1;
                                    }
                                }
                            ?>
                            <div>
                                <label><input type="radio" name="banyn" value="1" {{ ($banner == 1) ? 'checked' : '' }}>Y</label>
                                <label><input type="radio" name="banyn" value="0" {{ ($banner == 0) ? 'checked' : '' }}>N</label>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <th>내용</th>
                        <td colspan="3">
                        <textarea class="scrollTextBox" name="content" id="content" style="width:100%;">{{ $content->content ?? '' }}</textarea>
                        <script type="text/javascript">
                        var oEditors = [];
                        nhn.husky.EZCreator.createInIFrame({
                            htParams : {
                            aAdditionalFontList: [
                                ["Noto Sans KR", "노토산스 고딕"],
                            ],
                            },
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


            <div class=" row p-0 m-0" >

            <div class=" btn_miri">
                <button type="button" class="btn" style="background:#007c4f; color:#fff" onclick="preview()"> 미리보기</button>
                <div>
                    <button type="button" onclick="history.go(-1)" class="btn btn-light waves-effect waves-light">취소</button>
                    <button type="button" onclick="saveMapsContent()" class="btn btn-secondary waves-effect waves-light">저장</button>
                </div>

            </div>
        </div>
        </div>

        <div class="miriview">
            <iframe id="preview" src="" width="100%" height="100%"></iframe>
        </div>
    </div>


</div>
</form>

@endsection

<style type="text/css">
	.main-info td {text-align: left;}
	.main-info td td {text-align: center;}
	.userqa_modal .modal-body select {border: 1px solid #e7e0e0 !important; padding-right: 1rem !important;}
</style>
@push('scripts')
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script type="text/javascript">

    $(document).ready(function() {
        $('img.pointer').click(function() {
        	$(this).next().trigger('click');
        });
    });

    var func_id 	= '';
    var icon_img    = null;
    var thumb_img   = null;

    function loadFile(input, type) {
    	if(type == 'icon-img') {
    		thumb_img = input.files[0];
    	}
    	else {
    		thumb_img = input.files[0];
    	}

        //
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $("img."+type).attr("src", e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    function preview(){

        let request = new FormData();

        // 에디터의 내용을 textarea에 적용
        oEditors.getById["content"].exec("UPDATE_CONTENTS_FIELD", []);
        let title = $("#title").val();
        let description = $("#description").val();
        let content = $("#content").val();
        let preview_img = $('#preview_image').val();

        request.set('title' , $('#title').val());
        request.set('description' , $('#description').val());
        request.set('content', content);
        request.set('thumb_img', thumb_img);
        request.set('preview_img', preview_img);

        $.ajax({
            url: '/cms_store/preview',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                console.log(response);
                if(response.code == 200){
                    $('#preview').attr('src',`{{ route('store.preview') }}`)
                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });

    }

    function saveMapsContent() {

        let request = new FormData();
        let id = $('#content_id').val();
        let title = $("#title").val();
        let description = $("#description").val();
        // 에디터의 내용을 textarea에 적용
        oEditors.getById["content"].exec("UPDATE_CONTENTS_FIELD", []);
        let content = $("#content").val();
        let banner = $('input[name=banyn]:checked').val();

        if(!title){
            alert('제목을 입력해주세요.');
            return;
        }
        if(!description){
            alert('설명을 입력해주세요.');
            return;
        }
        if(!id && !thumb_img){
            alert('이미지를 등록해주세요.');
            return;
        }
        if( content == ""  || content == null || content == '&nbsp;' || content == '<p>&nbsp;</p>')  {
             alert("내용을 입력하세요.");
             oEditors.getById["content"].exec("FOCUS"); //포커싱
             return;
        }

        request.set('id' , $('#content_id').val());
        request.set('title' , $('#title').val());
        request.set('description' , $('#description').val());
        request.set('banner' , banner);
        request.set('content', content);
        request.set('thumb_img', thumb_img);

        $.ajax({
            url: '/cms_store/conte_store',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                console.log(response);
                if(response.code == 200){
                    alert('저장되었습니다.');
                    location.href = '/cms_store/conte';
                }else if(response.code == 400){
                    alert(response.error);
                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });

    }

</script>
@endpush
