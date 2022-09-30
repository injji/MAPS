@extends('layouts.cms')

@section('content')

<div class="tabbable">
    <ul class="nav nav-tabs" role="tablist">
		@foreach($menu_list as $key => $item)
			<li class="nav-item" role="presentation">
				@if($key == 0)
					<button id="tab{{ $item->id }}" class="nav-link small fw-bold active" type="button" onclick="tab({{ $item->id }})">{{ $item->name }}</button>
				@else
					<button id="tab{{ $item->id }}" class="nav-link small fw-bold" type="button" onclick="tab({{ $item->id }})">{{ $item->name }}</button>
				@endif
			</li>
		@endforeach
    </ul>
</div>
<div class="tab-content border shadow-sm p-5 bg-white border-top-0">
    <div class="tab-pane fade show active" id="tab1" role="tabpanel">
        <div class="board_search_ex mt-0">
		    <div class="b_s_ex b_s_ex2">
		        <label class="mr-2">
		            <span>읽기권한</span>
		            <input type="text" class="form-control" id="read" />
		        </label>
		        <label class="mr-2">
		            <span>쓰기권한</span>
		            <input type="text" class="form-control" id="write" />
		        </label>

		        <button type="button" onclick="saveMenu()">저장</button>
		    </div>
		</div>
    </div>
</div>

<form action="{{ route('setting.admin') }}">
	<div class="board_table_ex">
		<table>
			<thead>
				<tr>
					<th>이름</th>
					<th>Level</th>
					<th>등록일</th>
					<th>관리</th>
				</tr>
			</thead>

			<tbody>
				@foreach($admin_list as $key => $item)
				<tr>
					<td>{{ $item->name }}</td>
					<td>{{ $item->level }}</td>
					<td>{{ $item->created_at }}</td>
					<td>
						@if($item->use == 0)
							<button type="button" class="set1" onclick="modal1({{ $item->id }})">승인</button>
						@else
							<button type="button" class="set2" onclick="modal2({{ $item->id }})">차단</button>
							<button type="button" class="set1" onclick="showModal({{ $item->id }}, {{ $item->level }})">권한</button>
						@endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>

		<div class="list_btn_ex">
			{!! $admin_list->appends(Request::except('page'))->render() !!}
		</div>
	</div>
</form>

<div class="modal fade userqa_modal" id="showMenu" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
				<div class="d-block">
					<label class="mr-2">
						<span>Level</span>
						<input type="text" value="" id="level" class="form-control ml-3" />
					</label>
				</div>
                <div class="board_table_info p-0 mt-0">
					<table>
						<thead>
							<tr>
								<th>Menu</th>
								<th>Level</th>
							</tr>
						</thead>

						<tbody class="sortable">
							@foreach($menu_list as $key => $item)
								<tr >
									<td>{{ $item->name }}</td>
									<td>
										<input type="text" class="form-control" id="menu{{ $item->id }}" />
									</td>
								</tr>
							@endforeach							
						</tbody>
					</table>
                </div>

                <div>
                    <div class="modal_btn">
                        <button data-bs-dismiss="modal" class="notice_close">취소</button>
                        <button type="button" onclick="saveScope()">저장</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade service_stop" id="modal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">         
				<p>계정을 승인 하시겠습니까?</p>
				<ul>
					<li><button type="button" data-bs-dismiss="modal" aria-label="Close">닫기</button></li>
					<li>
						<button type="button" onclick="updateUse(1)">승인</button>
					</li>
				</ul>
            </div>
        </div>
    </div>
</div>

<div class="modal fade service_stop" id="modal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">                
				<p>계정을 차단 하시겠습니까?</p>
				<ul>
					<li><button type="button" data-bs-dismiss="modal" aria-label="Close">닫기</button></li>
					<li>
						<button type="button" onclick="updateUse(0)">차단</button>
					</li>
				</ul>
            </div>
        </div>
    </div>
</div>
@endsection

<style type="text/css">
	.board_search_ex .b_s_ex span {
	    min-width: 75px !important;
	}
	.tabbable .nav-tabs {		
		flex-wrap: wrap !important;
		width: 100% !important;
	}
	.nav-tabs .nav-item {
		margin-top: 5px;
	}
</style>
@push('scripts')
<script type="text/javascript">
	var selMenuId = 1;
	var selAdminId = 0;
	tab(selMenuId);
	
	function tab(id) {
		selMenuId = id;
		$(".nav-link.small").removeClass('active');
		$("#tab"+id).addClass('active');
		
		$.ajax({
            url : "/setting/get/menu",
            type : 'post',
            data : {id: id},
            success : (response) => {
                if(response.code == 200){
                    $("#read").val(response.content.read);
					$("#write").val(response.content.write);
                }
            }
        });
	}

	function modal1(id) {
		selAdminId = id;
        $('#modal1').modal('show');
    }

	function modal2(id) {
		selAdminId = id;
        $('#modal2').modal('show');
    }

	function updateUse(use) {
		$.ajax({
            url : "/setting/update/admin/use",
            type : 'post',
            data : {
				id: selAdminId, 
				use: use
			},
            success : (response) => {
                if(response.code == 200){
                    location.href = document.URL;
                }
            }
        });
	}

	function saveMenu() {
		$.ajax({
            url : "/setting/update/menu",
            type : 'post',
            data : {
				id: selMenuId,
				read: $("#read").val(),
				write: $("#write").val()
			},
            success : (response) => {
                if(response.code == 200){
                    toastr.success('저장되었습니다.');
                }
            }
        });
	}

    function showModal(id, level) {
		selAdminId = id;
		
		$.ajax({
            url : "/setting/get/menu/permission/list",
            type : 'post',
            data : {id: id},
            success : (response) => {
                if(response.code == 200){
					for(let i = 0; i < response.list.length; i++) {
						$("#menu"+response.list[i].menu_id).val(response.list[i].level);
					}

					$("#level").val(level);
                    $('#showMenu').modal('show');
                }
            }
        });
    }

	function saveScope() {
		$('#showMenu').modal('hide');

		var menu_list = @json($menu_list);
		var menu_id_arr = [];
		var menu_level_arr = [];

		for(let i = 0; i < menu_list.length; i++) {
			var tmp_level = $("#menu"+menu_list[i].id).val();			
			menu_id_arr.push(menu_list[i].id);
			menu_level_arr.push(tmp_level);			
		}

		$.ajax({
            url : "/setting/save/menu/permission",
            type : 'post',
            data : {
				user_id: selAdminId,
				level: $("#level").val(),
				menu_id_list: menu_id_arr.length > 0 ? menu_id_arr.join() : '',
				menu_level_list: menu_level_arr.length > 0 ? menu_level_arr.join() : ''
			},
            success : (response) => {
                if(response.code == 200){
					toastr.success('저장되었습니다.');
					location.href = document.URL;
                }
            },
			error : (err) => {
				console.log(err);
			}
        });
	}
</script>
@endpush
