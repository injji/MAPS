@extends('layouts.cms')

@section('content')

<div class="tabbable">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link small fw-bold {{ $tab == 1 ? 'active' : '' }}" onclick="location.href='/category?tab=1'">한국어</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link small fw-bold {{ $tab == 2 ? 'active' : '' }}" onclick="location.href='/category?tab=2'">영어</button>
        </li>
    </ul>
</div>
<div class="tab-content border shadow-sm p-5 bg-white border-top-0" >
    <div class="tab-pane fade show active" id="tab1" role="tabpanel">
		<div class="d-flex align-items-end justify-content-between">
			<label>드래그 앤 드롭으로 배너 순서를 변경할 수 있습니다.</label>
			<div>
				<button type="button" class="btn btn-primary btn-show-all">전체 펼치기</button>
				<button type="button" class="btn btn-primary btn-hide-all">전체 숨기기</button>
			</div>
		</div>
		@if($tab == 1)
		<div class="d-flex mt-3">
			<input type="text" class="border-input main_cat_txt mr-2" placeholder="1차 카테고리를 입력하세요." />
			<button type="button" class="btn btn-primary btn-cat-save main">저장</button>
		</div>
		@endif
		<div class="sortable accordion mt-3">
			@foreach($main_cat as $key => $item)
		  	<div class="item main_item" data-id="{{ $item->id }}" data-content="{{ $item->ko }}" data-sort="{{ $key }}">
			  	<div class="head">
			  		<div>
			  			<i class="fa fa-plus" aria-hidden="true"></i><i class="fa fa-minus d-none" aria-hidden="true"></i> <i class="fa fa-align-justify mx-2" aria-hidden="true"></i> <span class="main">{{ $item->ko }}</span>
			  		</div>
			  		<div>
			  			@if($tab == 2)
			  			<input type="text" data-id="{{ $item->id }}" class="border-input mr-2 {{ $item->expo ? '' : 'd-none' }}" value="{{ $item->en }}" placeholder="언어별 번역본을 입력하세요" />
			  			<button type="button" data-id="{{ $item->id }}" class="btn-expo {{ $item->expo ? 'set1' : 'set3' }}">노출</button>
			  			@elseif($tab == 1)
			  			<i class="fa fa-edit main" aria-hidden="true"></i> <i class="fa fa-times ml-2" aria-hidden="true"></i>
			  			@endif
			  		</div>
			  	</div>
				<div class="content">
					<div class="sortable_sub" data-mainid="{{ $item->id }}">
						@foreach($sub_cat as $idx => $val)
						@if($item->id == $val->parent)
					  	<div class="item" data-mainid="{{ $item->id }}" data-id="{{ $val->id }}" data-content="{{ $val->ko }}" data-sort="{{ $idx }}">
						  	<div class="head">
						  		<div>
						  			<i class="fa fa-align-justify mx-2" aria-hidden="true"></i> <span class="sub">{{ $val->ko }}</span>
						  		</div>
						  		<div>
						  			@if($tab == 2)
						  			<input type="text" data-id="{{ $val->id }}" class="border-input mr-2 {{ $val->expo ? '' : 'd-none' }}" value="{{ $val->en }}" placeholder="언어별 번역본을 입력하세요" />
						  			<button type="button" data-id="{{ $val->id }}" class="btn-expo {{ $val->expo ? 'set1' : 'set3' }}">노출</button>
						  			@elseif($tab == 1)
						  			<i class="fa fa-edit sub" aria-hidden="true"></i> <i class="fa fa-times ml-2" aria-hidden="true"></i>
						  			@endif
						  		</div>
						  	</div>
						</div>
						@endif
						@endforeach
					</div>
					@if($tab == 1)
					<div class="d-flex mt-2">
						<input type="text" class="border-input sub_cat_txt mr-2" placeholder="2차 카테고리를 입력하세요." />
						<button type="button" data-id="{{ $item->id }}" class="btn btn-primary btn-cat-save sub">저장</button>
					</div>
					@endif
			  	</div>
			</div>
			@endforeach
		  
			@if($tab != 1)
			<div class="d-flex justify-content-between mt-4">
				<span> </span>
				<button type="button" class="btn btn-primary" onclick="saveOtherLang()">저장</button>
			</div>
			@endif
		</div>

    </div>
    <div class="tab-pane fade" id="tab2" role="tabpanel">
    	english
    </div>
</div>

@endsection
<style type="text/css">
	.head input {height: 25px;}
	.head div {display: flex; align-items: center;}
</style>
@push('scripts')

<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script type="text/javascript">
	
	let tab 	= '{{$tab}}';
	let cat_id 	= '{{$cat_id}}';
	let category_id = '';
    $(document).ready(function() {
    	// 
    	if(cat_id) {
    		$(`.item[data-id="${cat_id}"] .content`).css('display', 'block');
    	}
	    // 
    	$( ".sortable" ).sortable({
	        start: function(event, ui) {
	            var start_pos = ui.item.index();
	        },
	        change: function(event, ui) {
	            // var start_pos = ui.item.data('sort');
	            // var index = ui.placeholder.index();
	        },
	        update: function(event, ui) {
	            $('.sortable .main_item').each(function (index) {
	            	$(this).attr('data-sort', index);
	            });
	            updateSort();
	        }
	    });
	    // 
    	$( ".sortable_sub" ).sortable({
	        change: function(event, ui) {
	            category_id = ui.item.data('mainid');
	            // var index = ui.placeholder.index();
	        },
	        update: function(event, ui) {
	            $('.sortable_sub .item').each(function (index) {
	            	$(this).attr('data-sort', index);
	            });
	            updateSort('sub');
	        }
	    });
	    // 
	    $(document).on('click', '.accordion .fa-plus, .accordion .fa-minus', function () {
	        function slideDown(target) {
	        	target = target.parent().parent();
	            target.next().slideDown();
	            target.find('.fa-plus').addClass('on').removeClass('d-inline-block').addClass('d-none');
	            target.find('.fa-minus').addClass('on').addClass('d-inline-block').removeClass('d-none');
	        }

	        function slideUp(target) {
	        	// target.removeClass('on');
	        	target = target.parent().parent();
	            target.next().slideUp();
	            target.find('.fa-plus').removeClass('on').addClass('d-inline-block').removeClass('d-none');
	            target.find('.fa-minus').removeClass('on').removeClass('d-inline-block').addClass('d-none');
	        };
	        $(this).hasClass('on') ? slideUp($(this)) : slideDown($(this));

	    });
	    // 
	    $('.btn-show-all').click(function() {
	    	let parent = $(this).parent().parent().parent();
	    	parent.find('.fa-plus').removeClass('d-inline-block').addClass('d-none');
	    	parent.find('.fa-minus').addClass('d-inline-block').removeClass('d-none');
	    	parent.find('.item').find('.content').css('display', 'block');
	    });
	    $('.btn-hide-all').click(function() {
	    	let parent = $(this).parent().parent().parent();
	    	parent.find('.fa-plus').addClass('d-inline-block').removeClass('d-none');
	    	parent.find('.fa-minus').removeClass('d-inline-block').addClass('d-none');
	    	parent.find('.item').find('.content').css('display', 'none');
	    });
	    // 편집하기
	    $(document).on('click', '.fa-edit.main', function() {
	    	let parent  = $(this).parent().parent().parent();
	    	let content = parent.data('content');
	    	category_id = parent.data('id');
	    	$('.main_cat_txt').val(content);
	    });
	    $(document).on('click', '.fa-edit.sub', function() {
	    	let parent  = $(this).parent().parent().parent();
	    	let content = parent.data('content');
	    	category_id = parent.data('id');
	    	parent.parent().next().find('input').val(content);
	    });
	    // reset category_id
	    $(document).on('focusout', 'input', function() {
	    	let content = $(this).val();
	    	if(!content) {
	    		category_id = '';
	    	}
	    });
	    // 카테고리 저장
	    $(document).on('click', '.btn-cat-save', function() {
	    	let content 	= $(this).prev().val();
	    	let main_cat_id = $(this).data('id');
	    	saveCategory(content, main_cat_id);
	    });
	    // delete
	    $(document).on('click', 'i.fa-times', function() {
	    	let cat_id 	= $(this).parent().parent().parent().data('id');
	    	delCategory(cat_id);
	    });
	    // expo
	    $(document).on('click', '.btn-expo', function() {
			@if (!$write_permission)
				return alert('관리자에게 권한 요청해 주세요.')
			@endif
	    	let self 	= $(this);
	    	let cat_id 	= $(this).data('id');
			$.ajax({
				url: '/category/update_category',
				method: 'post',
				data: {type: 'expo', category_id: cat_id, expo: self.hasClass('set1') ? 0 : 1},
				success: (response) => {
					if(response.code == 200){
						if(self.hasClass('set1')) {
							self.removeClass('set1');
							self.addClass('set3');
						}
						else {
							self.removeClass('set3');
							self.addClass('set1');
						}
					}
				},
				error: (e) => {
					console.log(e.responseJSON)
				}
			});
	    });

    });

    function saveCategory(content, cat_id) {	// cat_id == null ? 상위 카테고리 : 하위
		@if (!$write_permission)
			return alert('관리자에게 권한 요청해 주세요.')
		@endif
    	if(!content) {
    		return alert('카테고리를 입력해주세요.');
    	}
		$.ajax({
			url: '/category/update_category',
			method: 'post',
			data: {type: 'content', category_id, cat_id, content},
			success: (response) => {
				if(response.code == 200){
					if(category_id) {	// 편집
						let item = $(`.item[data-id="${category_id}"]`).attr('data-content', content);
						let sub_cls = cat_id ? 'sub' : 'main';
						$(`.item[data-id="${category_id}"]`).find('span.'+sub_cls).text(content);
					}
					else {	// add
						let _href = location.href + '';
						_href = _href.split('?');
						location.href = _href[0] + '?cat_id=' + (cat_id ? cat_id : category_id);
					}
				}
			},
			error: (e) => {
				console.log(e.responseJSON)
			}
		});
    }

    function delCategory(cat_id) {
		@if (!$write_permission)
			return alert('관리자에게 권한 요청해 주세요.')
		@endif
    	if(!confirm('해당 카테고리를 삭제 하시겠습니까?')) {
    		return;
    	}
		$.ajax({
			url: '/category/update_category',
			method: 'post',
			data: {type: 'delete', cat_id},
			success: (response) => {
				if(response.code == 200){
					$(`.item[data-id="${cat_id}"]`).remove();
				}
			},
			error: (e) => {
				console.log(e.responseJSON)
			}
		});
    }

    function updateSort(type='main') {
		@if (!$write_permission)
			return alert('관리자에게 권한 요청해 주세요.')
		@endif

    	let items = [];
    	if(type == 'main') {
    		$('.sortable .main_item').each(function (index) {
            	let id 	 = $(this).data('id');
            	let sort = $(this).data('sort');
    			items.push({id, sort});
            });
    	}
    	else {
	        let parent = $(`.sortable_sub[data-mainid=${category_id}]`);
    		$(parent).find('.item').each(function (index) {
            	let id 	 = $(this).data('id');
            	let sort = $(this).data('sort');
    			items.push({id, sort});
            });
    	}
		$.ajax({
			url: '/category/update_category',
			method: 'post',
			data: {type: 'sort', items},
			success: (response) => {
				if(response.code == 200){
					// location.reload();
				}
			},
			error: (e) => {
				console.log(e.responseJSON)
			}
		});
    }

    // other language
    function saveOtherLang(){
		@if (!$write_permission)
			return alert('관리자에게 권한 요청해 주세요.')
		@endif

    	let items = [];
		$('.head input').each(function (index) {
        	let id 	 = $(this).data('id');
        	let cont = $(this).val();
			items.push({id, cont});
        });

		$.ajax({
			url: '/category/update_category',
			method: 'post',
			data: {type: 'other_lang', items},
			success: (response) => {
				if(response.code == 200){
					// location.reload();
				}
			},
			error: (e) => {
				console.log(e.responseJSON)
			}
		});
    }

</script>
@endpush
