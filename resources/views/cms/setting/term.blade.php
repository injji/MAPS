@extends('layouts.cms')

@section('content')

<script src="{{ config('app.cms_asset_url') }}/libs/smarteditor2/js/HuskyEZCreator.js" charset="utf-8"></script>
<div class="board_table_info main-info mt-0">
	<div class="sortable accordion">
	  	<div class="item">
		  	<div class="head on">
		  		<div>
		  			<span>이용약관</span>
		  		</div>
		  		<div class="pt-1">
		  			<i class="fa fa-chevron-down" aria-hidden="true"></i>
		  		</div>
		  	</div>
			<div class="content term" style="display: block;">
                <textarea class="scrollTextBox" name="content1" id="content1" style="width:calc( 100% - 80px );">{{ $terms[0]->content }}</textarea>
                <script type="text/javascript">
                var oEditors1 = [];
                nhn.husky.EZCreator.createInIFrame({
                    oAppRef: oEditors1,
                    elPlaceHolder: "content1",
                    sSkinURI: "{{ config('app.cms_asset_url') }}/libs/smarteditor2/SmartEditor2Skin_photo.html",
                    fCreator: "createSEditor2"
                });
                </script>
		  	</div>
		</div>

	  	<div class="item">
		  	<div class="head">
		  		<div>
		  			<span>개인정보처리방침</span>
		  		</div>
		  		<div class="pt-1">
		  			<i class="fa fa-chevron-down" aria-hidden="true"></i>
		  		</div>
		  	</div>
			<div class="content" style="display: block;">
                <textarea class="scrollTextBox" name="content2" id="content2" style="width:calc( 100% - 80px );">{{ $terms[1]->content }}</textarea>
                <script type="text/javascript">
                var oEditors2 = [];
                nhn.husky.EZCreator.createInIFrame({
                    oAppRef: oEditors2,
                    elPlaceHolder: "content2",
                    sSkinURI: "{{ config('app.cms_asset_url') }}/libs/smarteditor2/SmartEditor2Skin_photo.html",
                    fCreator: "createSEditor2"
                });
                </script>
		  	</div>
		</div>

	  	<div class="item">
		  	<div class="head">
		  		<div>
		  			<span>마케팅 수신동의</span>
		  		</div>
		  		<div class="pt-1">
		  			<i class="fa fa-chevron-down" aria-hidden="true"></i>
		  		</div>
		  	</div>
			<div class="content" style="display: block;">
                <textarea class="scrollTextBox" name="content3" id="content3" style="width:calc( 100% - 80px );">{{ $terms[2]->content }}</textarea>
                <script type="text/javascript">
                var oEditors3 = [];
                nhn.husky.EZCreator.createInIFrame({
                    oAppRef: oEditors3,
                    elPlaceHolder: "content3",
                    sSkinURI: "{{ config('app.cms_asset_url') }}/libs/smarteditor2/SmartEditor2Skin_photo.html",
                    fCreator: "createSEditor2"
                });
                </script>
		  	</div>
		</div>
	</div>

    <div class="mt-4">
        <div class="col-12 row p-0 m-0" >
            <div class="col-12 text-right">
                @if ($write_permission)
                    <button type="button" onclick="update()" class="btn btn-secondary waves-effect waves-light">저장</button>
				@else
					<button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')" class="btn btn-secondary waves-effect waves-light">저장</button>
				@endif                
            </div>
        </div>
    </div>
</div>

@endsection

<style type="text/css">
	.accordion .item {margin-top: 1rem;}
	.accordion .on .fa {transform: rotate(180deg);}
	..accordion .content {padding-left: .5rem !important;}
</style>
@push('scripts')
<script type="text/javascript">

    $(document).ready(function() {
	    // 
	    $('.accordion .head').on('click', function () {
	        function slideDown(target) {
	            target.addClass('on').next().slideDown();
	        }

	        function slideUp(target) {
	            target.removeClass('on').next().slideUp();
	        };
	        $(this).hasClass('on') ? slideUp($(this)) : slideDown($(this));

	    });
	    setTimeout(() => {
	    	$('.accordion .content').css('display', 'none');
	    	$('.accordion .content.term').css('display', 'block');
	    }, 1000);
	});

    function update() {
        // 에디터의 내용을 textarea에 적용
        oEditors1.getById["content1"].exec("UPDATE_CONTENTS_FIELD", []); 
        oEditors2.getById["content2"].exec("UPDATE_CONTENTS_FIELD", []); 
        oEditors3.getById["content3"].exec("UPDATE_CONTENTS_FIELD", []); 
        var content1 = $("#content1").val();
        var content2 = $("#content2").val();
        var content3 = $("#content3").val();
        
        $.ajax({
            url: '/setting/term_update',
            method: 'post',
            data: {content1, content2, content3},
            success: (response) => {
                if(response.code == 200){
                    toastr.success('저장되었습니다.');
                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });
    }
</script>
@endpush
