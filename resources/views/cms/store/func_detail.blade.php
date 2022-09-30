@extends('layouts.cms')

@section('content')

<div class="board_table_info main-info mt-0">
    <div>
    	<input type="hidden" name="func_id" value="{{$result->id}}" />
    	<input type="hidden" name="type" value="save" />
    	<input type="hidden" id="kind" name="kind" value="{{$result->kind}}" />
		<table>
			<tbody>
				<tr>
					<th>비중</th>
	                <td colspan="3"><input type="text" name="weight" value="{{$result->weight}}" style="width: 20%;" /></td>
				</tr>
				<tr>
					<th>항목명</th>
	                <td colspan="3"><input type="text" name="title" value="{{$result->title}}" /></td>
				</tr>
				<tr>
					<th>서비스분류</th>
	                <td colspan="3">
	                	<?php 
	                	$categorys = explode(',', $result->kind);
	                	foreach($category_list as $key => $item) {
	                		$checked = '';
	                		foreach($categorys as $k => $val) {
	                			if($val == $item->id) {
	                				$checked = 'checked'; break;
	                			}
	                		}
	                	?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" id="category{{$key}}" name="category" type="checkbox" value="{{$item->id}}" {{$checked}} />
                            <label class="form-check-label" for="category{{$key}}">{{$item->ko}}</label>
                        </div>
                    	<?php } ?>
	                </td>
				</tr>
				<tr>
					<th>노출기간</th>
	                <td colspan="3">
                        <div class="input-group">
                            <div class="input-group mt-1 mt-md-0 w-100">
                                <input type="text" id="single_date" name="st_date" class="form-control single_date" value="{{$result->st_date}}" style="width: 0%;" />
                                <span class="input-group-text">~</span>
                                <input type="text" id="single_date2" name="end_date" class="form-control single_date" value="{{$result->end_date}}" style="width: 0%;" />
                            </div>
                        </div>
	                </td>
				</tr>
				<tr>
					<th style="width: 20%;">아이콘<br>@lang('form.img_size.section.icon')</th>
	                <td style="width: 20%;">
	                	<img class="pointer icon-img" src="{{ $result->icon ? Storage::url($result->icon) : '/assets/images/empty2.png' }}" width="100%" />
	                	<input type="file" id="icon" style="visibility:hidden;" name="icon" onchange="loadFile(this, 'icon-img')" accept="image/*" />
	                </td>
					<th style="width: 20%;">배너<br>@lang('form.img_size.section.banner')</th>
	                <td style="width: 40%;">
	                	<img class="pointer thumb-img" src="{{ $result->thumb ? Storage::url($result->thumb) : '/assets/images/empty2.png' }}" width="100%" />
	                	<input type="file" id="thumb" style="visibility:hidden;" name="thumb" onchange="loadFile(this, 'thumb-img')" accept="image/*" />
	                </td>
				</tr>
				<tr>
					<th><button type="button" onclick="showModal()" class="btn btn-secondary">서비스선택</button></th>
	                <td colspan="3">
	                	<input type="hidden" id="service" name="service" value="{{$result->service}}" />
						<table>
							<thead>
								<tr>
									<th>노출순서</th>
									<th>제휴사명</th>
									<th>서비스명</th>
									<th>카테고리</th>
					                <th>인앱결제</th>
									<th>조회수</th>
					                <th>누적신청수</th>
					                <th>관리</th>
								</tr>
							</thead>

							<tbody class="sortable">
								@foreach($service_lists as $key => $item)
								<tr data-id="{{ $item->id }}">
									<td><i class="fa fa-align-justify" aria-hidden="true"></i></td>
									<td>{{ $item->user->company_name }}</td>
									<td>{{ $item->name }}</td>
					                <td>{{ $item->cat1->text }} > {{ $item->cat2->text }}</td>
									<td>{{ $item->in_app_payment ? 'True' : 'False' }}</td>
					                <td>{{ number_format($item->view_cnt) }}</td>
					                <td>{{ number_format($item->request_cnt) }}</td>
									<td><button type="button" data-id="{{ $item->id }}" class="set2">삭제</button></td>
								</tr>
								@endforeach
							</tbody>
						</table>
	                </td>
				</tr>
			</tbody>
		</table>
    </div>

    <div class="mt-4">
        <div class="col-12 row p-0 m-0" >
            <div class="col-12 text-right">
                <button type="button" onclick="history.go(-1)" class="btn btn-light waves-effect waves-light line_btn">취소</button>
                <button type="button" onclick="saveMainFuncKind()" class="btn btn-secondary waves-effect waves-light bg_btn">저장</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade userqa_modal func_modal" id="showCategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
				<div class="d-block">
					<label class="mr-2 sel_option">
						<svg width="10" height="7" viewBox="0 0 7 4" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M1 1L3.5 3.5L6 1" stroke="#666666" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
							
						<select id="agent_id">
							
							<option value="">제휴사선택 
							</option>
							@foreach($agent_list as $key => $item)
								<option value="{{ $item->id }}">{{ $item->company_name }}</option>
							@endforeach
						</select>
					</label>
					<label class="mr-2 sel_option">
						<svg width="10" height="7" viewBox="0 0 7 4" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M1 1L3.5 3.5L6 1" stroke="#666666" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						<select id="service_id">
							<option value="">서비스선택</option>
							@foreach($service_list as $key => $item)
								<option value="{{ $item->id }}">{{ $item->name }}</option>
							@endforeach
						</select>
					</label>
					<label class="mr-2 sel_option">
						<svg width="10" height="7" viewBox="0 0 7 4" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M1 1L3.5 3.5L6 1" stroke="#666666" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
						<select id="category_id">
							<option value="">카테고리선택</option>
							@foreach($category_list as $key => $item)
								<option value="{{ $item->id }}">{{ $item->text }}</option>
							@endforeach
						</select>
					</label>
				</div>
                <div class="board_table_info p-0 mt-0">
					<table>
						<thead>
							<tr>
								<th>등록</th>
								<th>제휴사명</th>
								<th>서비스명</th>
								<th>카테고리</th>
				                <th>인앱결제</th>
								<th>조회수</th>
				                <th>누적신청수</th>
							</tr>
						</thead>

						<tbody class="service_list">
							<tr><td rowspan="7">검색결과가 없습니다.</td></tr>
						</tbody>
					</table>
                </div>

                <div>
                    <div class="modal_btn">
                        <button data-bs-dismiss="modal" class="notice_close">취소</button>
                        <button type="button" onclick="saveModalService()">저장</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
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
        // 
        $( ".sortable" ).sortable();
        // 
        $('img.pointer').click(function() {
        	$(this).next().trigger('click');
        });
        $(document).on('click', '.sortable button', function() {	// delete service
        	let service_id = $(this).data('id');
        	let service  = $('#service').val();
        	service = service.split(',').filter(id => id != service_id);
        	$('#service').val(service);
        	$(this).parent().parent().remove();
        });
        // 
        $(document).on('change', '.modal select', function() {
        	showModal();
        });
        $(document).on('click', '.modal td button', function() {
        	let service_id = $(this).data('id');
        	let selected = $(this).data('selected');
        	let service  = $('#service').val();
        	service = service.split(',');

        	if(!selected) {		// 등록 안되어 있음
        		$(this).data('selected', 1);
        		$(this).removeClass('set1');
        		$(this).addClass('set3');
        		// 
        		service.push(service_id);
        	}
        	else {
        		$(this).data('selected', 0);
        		$(this).removeClass('set3');
        		$(this).addClass('set1');
        		// 
        		service = service.filter(id => id != service_id);
        	}

        	service = service.filter(id => id != '');
        	$('#service').val(service);
        });
    });

    var func_id 	= '{{$result->id}}';
    var icon_img    = null;
    var thumb_img   = null;

    function loadFile(input, type) {
    	if(type == 'icon-img') {
    		icon_img = input.files[0];
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

    // 
    function saveMainFuncKind() {
        let kind 	= [];
        let service = [];
        $('td input[name="category"]').each(function (index) {
        	if($(this).is(':checked')) {
	            let id   = $(this).val();
	            kind.push(id);
        	}
        });
        $('.sortable tr').each(function (index) {
            let id   = $(this).data('id');
            service.push(id);
        });
        // 
        $('#kind').val(kind);
        $('#service').val(service);
        // 
        let weight = $('input[name="weight"]').val();
        let title  = $('input[name="title"]').val();
        let st_date  = $('input[name="st_date"]').val();
        let end_date = $('input[name="end_date"]').val();
        // let icon   = $('input[name="icon"]').val();
        // let thumb  = $('input[name="thumb"]').val();
        if((weight < 0) || !title || !st_date || !end_date ) {
        	return alert('필수항목들을 입력해주세요.');
        }

        // 
        let request = new FormData();
        request.set('type',     'save');
        request.set('weight',   weight);
        request.set('func_id',  func_id);
        request.set('title',    title);
        request.set('st_date',  st_date);
        request.set('end_date', end_date);
        request.set('icon',  	icon_img);
        request.set('thumb', 	thumb_img);
        request.set('kind',  	kind.join());
        request.set('service',  service.join());
    	// 
    	saveFuncKind(request);
    }

    function saveModalService() {
    	let service = $('#service').val();
    	$('#showCategory').modal('hide');
    	$('.sortable').html('');

        $.ajax({
            url: '/cms_store/get_service',
            method: 'post',
            data: {type: 'service', service},
            success: (response) => {
                if(response.code == 200){
                    let data = response.data;
                    let html = '';

                    for(let i in data) {
                    	let item = data[i];

                    	html += `
								<tr data-id="${item.id}">
									<td><i class="fa fa-align-justify" aria-hidden="true"></i></td>
									<td>${item.company_name}</td>
									<td>${item.name}</td>
									<td>${item.cat1 ?? ''} > ${item.cat2 ?? ''}</td>
									<td>${item.in_app_payment ? 'True' : 'False'}</td>
									<td>${item.view_cnt}</td>
									<td>${item.request_cnt}</td>
									<td><button type="button" data-id="${item.id}" class="set2">삭제</button></td>
								</tr>`;
                    }

                    html = data.length > 0 ? html : '<tr><td colspan="">내역이 없습니다.</td></tr>';

                    $('.sortable').html(html);
                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });
        
    }

    function showModal() {
    	let agent_id 	= $('#agent_id').val();
    	let service_id  = $('#service_id').val();
    	let category_id = $('#category_id').val();

        $.ajax({
            url: '/cms_store/get_service',
            method: 'post',
            data: {agent_id, service_id, category_id},
            success: (response) => {
                if(response.code == 200){
                    let data = response.data;
                    let html = '';

                    let service = $('#service').val();
                    service = service.split(',');
                    for(let i in data) {
                    	let item = data[i];
                    	let selected = service.filter(id => id == item.id);

                    	html += `<tr >
									<td><button type="button" data-id="${item.id}" data-selected="${selected.length}" class="${selected.length ? 'set3' : 'set1'}">등록</button></td>
									<td>${item.company_name}</td>
									<td>${item.name}</td>
									<td>${item.cat1 ?? ''} > ${item.cat2 ?? ''}</td>
									<td>${item.in_app_payment ? 'True' : 'False'}</td>
									<td>${item.view_cnt}</td>
									<td>${item.request_cnt}</td>
								</tr>`;
                    }

                    html = data.length > 0 ? html : '<tr><td colspan="7">검색결과가 없습니다.</td></tr>';

                    $('.modal .service_list').html(html);
                    $('#showCategory').modal('show');
                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });
        
    }

    function saveFuncKind(data) {

        $.ajax({
            url: '/cms_store/change_func',
            method: 'post',
            data,
            contentType: false,
            processData: false,
            success: (response) => {
                if(response.code == 200){
                    location.reload();
                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });
        
    }

</script>
@endpush
