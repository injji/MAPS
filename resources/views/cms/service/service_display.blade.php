@extends('layouts.cms')

@section('content')

<script src="{{ config('app.cms_asset_url') }}/libs/smarteditor2/js/HuskyEZCreator.js" charset="utf-8"></script>



    <input type="hidden" name="id" value="">
    <div class="tabbable">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link small fw-bold active" data-bs-toggle="tab" data-bs-target="#best_tab" type="button" role="tab">베스트</button>
                {{-- @lang('form.title.basic_info') --}}
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link small fw-bold" data-bs-toggle="tab" data-bs-target="#new_tab" type="button" role="tab">새로운 서비스</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link small fw-bold" data-bs-toggle="tab" data-bs-target="#satisfy200_tab" type="button" role="tab">만족 200%</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link small fw-bold" data-bs-toggle="tab" data-bs-target="#martech_tab" type="button" role="tab">필수 마테크</button>
            </li>
        </ul>
    </div>
    <div class="tab-content border shadow-sm p-md-10 p-3 pt-5 pb-0 bg-white border-top-0 service_best" style="margin-bottom:60px;min-height:calc(100vh - 300px)">
        <div class="tab-pane fade show active" id="best_tab" role="tabpanel" aria-labelledby="best_tab">
           <h3>기본 진열 기준</h3>
           <p>- 전월(월~일)간 서비스 신청 수 기준 상위 4개 서비스</p>


            <div class="board_search_ex ">
                <p>서비스 진열 등록시 관리자 권한 우선 노출 서비스가 적용됩니다.</p>
                <div class="total_ex">
                    {{-- <a href="javascript:;" data-toggle="modal" data-target="#new_faq">서비스 선택</a> --}}
                    <button type="button" onclick="showModal('best')" class="btn btn-secondary">서비스 선택</button>
                </div>
            </div>

            <div class="board_table_ex board_table_ex8">

                <table class="table_no8">
                    <colgroup>
                        <col width="66px">
                        <col>
                        <col>
                        <col>
                        <col width="86px">
                    </colgroup>

                    <thead>
                        <tr>
                            <th>노출</th>
                            <th>제휴사명</th>
                            <th>서비스명</th>
                            <th>카테고리</th>
                            <th>관리</th>
                        </tr>
                    </thead>

                    <input type="hidden" id="best_service" name="service" value="{{ $best_id }}">
                    <tbody id="best" class="sortable best_service">
                        @foreach($best as $item)
                            <tr data-id="{{ $item->id }}">
                                <td><i class="fa fa-align-justify mx-2" aria-hidden="true"></i></td>
                                <td>{{ $item->user->company_name }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->cat1->ko }} > {{ $item->cat2->ko }}</td>
                                <td><button type="button" data-id="{{ $item->id }}">삭제</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

            <div class="save_btn_2">
                <ol>
                    <li><button type="button" onclick="cancel()">취소</button></li>
                    <li><button type="button" onclick="saveSortDisplay('best')">저장</button></li>
                </ol>
            </div>
        </div>


        {{--  --}}
        <div class="tab-pane fade" id="new_tab" role="tabpanel" aria-labelledby="new_tab">
            <h3>기본 진열 기준</h3>
            <p>- 1개월 기준 최신등록순</p>


            <div class="board_search_ex ">
                <p>서비스 진열 등록시 관리자 권한 우선 노출 서비스가 적용됩니다.</p>
                <div class="total_ex">
                    {{-- <a href="javascript:;" data-toggle="modal" data-target="#new_faq">서비스 선택</a> --}}
                    <button type="button" onclick="showModal('new')" class="btn btn-secondary">서비스 선택</button>
                </div>
            </div>

            <div class="board_table_ex board_table_ex8">

                <table class="table_no8">
                    <colgroup>
                        <col width="66px">
                        <col>
                        <col>
                        <col>
                        <col width="86px">
                    </colgroup>

                    <thead>
                        <tr>
                            <th>노출</th>
                            <th>제휴사명</th>
                            <th>서비스명</th>
                            <th>카테고리</th>
                            <th>관리</th>
                        </tr>
                    </thead>

                    <input type="hidden" id="new_service" name="service" value="{{ $new_id }}">
                    <tbody id="new" class="sortable new_service">
                        @foreach($new as $item)
                            <tr data-id="{{ $item->id }}">
                                <td><i class="fa fa-align-justify mx-2" aria-hidden="true"></i></td>
                                <td>{{ $item->user->company_name }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->cat1->ko }} > {{ $item->cat2->ko }}</td>
                                <td><button type="button" data-id="{{ $item->id }}">삭제</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

            <div class="save_btn_2">
                <ol>
                    <li><button type="button" onclick="cancel()">취소</button></li>
                    <li><button type="button" onclick="saveSortDisplay('new')">저장</button></li>
                </ol>
            </div>
        </div>



        {{--  --}}
        <div class="tab-pane fade" id="satisfy200_tab" role="tabpanel" aria-labelledby="satisfy200_tab">
            <h3>기본 진열 기준</h3>
            <p>- 1순위: 최근 30일 리뷰등록 많은순</p>
            <p>- 2순위: 리뷰 많은순</p>


            <div class="board_search_ex ">
                <p>서비스 진열 등록시 관리자 권한 우선 노출 서비스가 적용됩니다.</p>
                <div class="total_ex">
                    {{-- <a href="javascript:;" data-toggle="modal" data-target="#new_faq">서비스 선택</a> --}}
                    <button type="button" onclick="showModal('satisfy200')" class="btn btn-secondary">서비스 선택</button>
                </div>
            </div>

            <div class="board_table_ex board_table_ex8">

                <table class="table_no8">
                    <colgroup>
                        <col width="66px">
                        <col>
                        <col>
                        <col>
                        <col width="86px">
                    </colgroup>

                    <thead>
                        <tr>
                            <th>노출</th>
                            <th>제휴사명</th>
                            <th>서비스명</th>
                            <th>카테고리</th>
                            <th>관리</th>
                        </tr>
                    </thead>

                    <input type="hidden" id="satisfy200_service" name="service" value="{{ $satisfy200_id }}">
                    <tbody id="satisfy200" class="sortable satisfy200_service">
                        @foreach($satisfy200 as $item)
                            <tr data-id="{{ $item->id }}">
                                <td><i class="fa fa-align-justify mx-2" aria-hidden="true"></i></td>
                                <td>{{ $item->user->company_name }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->cat1->ko }} > {{ $item->cat2->ko }}</td>
                                <td><button type="button" data-id="{{ $item->id }}">삭제</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

            <div class="save_btn_2">
                <ol>
                    <li><button type="button" onclick="cancel()">취소</button></li>
                    <li><button type="button" onclick="saveSortDisplay('satisfy200')">저장</button></li>
                </ol>
            </div>
        </div>


        {{--  --}}
        <div class="tab-pane fade" id="martech_tab" role="tabpanel" aria-labelledby="martech_tab">
            <h3>기본 진열 기준</h3>
            <p>- 전월(월~일)간 서비스 신청 수 기준 상위 4개 서비스</p>


            <div class="board_search_ex ">
                <p>서비스 진열 등록시 관리자 권한 우선 노출 서비스가 적용됩니다.</p>
                <div class="total_ex">
                    {{-- <a href="javascript:;" data-toggle="modal" data-target="#new_faq">서비스 선택</a> --}}
                    <button type="button" onclick="showModal('martech')" class="btn btn-secondary">서비스 선택</button>
                </div>
            </div>

            <div class="board_table_ex board_table_ex8">

                <table class="table_no8">
                    <colgroup>
                        <col width="66px">
                        <col>
                        <col>
                        <col>
                        <col width="86px">
                    </colgroup>

                    <thead>
                        <tr>
                            <th>노출</th>
                            <th>제휴사명</th>
                            <th>서비스명</th>
                            <th>카테고리</th>
                            <th>관리</th>
                        </tr>
                    </thead>

                    <input type="hidden" id="martech_service" name="service" value="{{ $martech_id }}">
                    <tbody id="martech" class="sortable martech_service">
                        @foreach($martech as $item)
                            <tr data-id="{{ $item->id }}">
                                <td><i class="fa fa-align-justify mx-2" aria-hidden="true"></i></td>
                                <td>{{ $item->user->company_name }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->cat1->ko }} > {{ $item->cat2->ko }}</td>
                                <td><button type="button" data-id="{{ $item->id }}">삭제</button></td>
                            </tr>
                        @endforeach
                        {{-- <tr>
                            <td><i class="fa fa-align-justify mx-2" aria-hidden="true"></i></td>
                            <td>이노티브</td>
                            <td><a href="">무료 앱제작 - 바이앱스</a></td>
                            <td>대카테 > 소카테</td>
                            <td><button>삭제</button></td>
                        </tr>

                        <tr>
                            <td><i class="fa fa-align-justify mx-2" aria-hidden="true"></i></td>
                            <td>이노티브2</td>
                            <td><a href="">무료 앱제작 - 바이앱스</a></td>
                            <td>대카테 > 소카테</td>
                            <td><button>삭제</button></td>
                        </tr> --}}
                    </tbody>
                </table>

            </div>

            <div class="save_btn_2">
                <ol>
                    <li><button type="button" onclick="cancel()">취소</button></li>
                    <li><button type="button" onclick="saveSortDisplay('martech')">저장</button></li>
                </ol>
            </div>
        </div>

        <div class="modal fade userqa_modal" id="showCategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <input type="hidden" id="tab_gubun" value="">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="d-block">
                            <label class="mr-2">
                                <select id="agent_id">
                                    <option value="">제휴사선택</option>
                                    @foreach($agent_list as $key => $item)
                                        <option value="{{ $item->id }}">{{ $item->company_name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="mr-2">
                                <select id="service_id">
                                    <option value="">서비스선택</option>
                                    @foreach($service_list as $key => $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="mr-2">
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


    </div>



@endsection
<style type="text/css">
	.main-info td {text-align: left;}
	.main-info td td {text-align: center;}
	.userqa_modal .modal-body select {border: 1px solid #e7e0e0 !important; padding-right: 1rem !important;}
</style>
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/0.9.1/jquery.tablednd.js" integrity="sha256-d3rtug+Hg1GZPB7Y/yTcRixO/wlI78+2m08tosoRn7A=" crossorigin="anonymous"></script>
<script>
	$(document).ready(function() {
        // Initialise the table
        // $("#table-1").tableDnD({
        //     onDragClass: 'myDrag'
        // });
        $('.sortable').sortable();

        $(document).on('click', '.sortable button', function() {	// delete service
        	let service_id = $(this).data('id');
            console.log(service_id);
            let tab = $(this).parent().parent().parent().attr('id');
            console.log(tab);
        	let service  = $('#'+tab+'_service').val();
        	service = service.split(',').filter(id => id != service_id);
            $('#'+tab+'_service').val(service);
        	$(this).parent().parent().remove();
        });
        //
        $(document).on('change', '.modal select', function() {
            let tab = $('#tab_gubun').val();
        	showModal(tab);
        });
        $(document).on('click', '.modal td button', function() {
            console.log('등록');
        	let service_id = $(this).data('id');
        	let selected = $(this).data('selected');
            let tab = $('#tab_gubun').val();
        	let service  = $('#'+tab+'_service').val();
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
        	$('#'+tab+'_service').val(service);
        });

    });

    function saveModalService() {
        let tab = $('#tab_gubun').val();
    	let service = $('#'+tab+'_service').val();
    	$('#showCategory').modal('hide');
    	// $('.sortable').html('');
        $('.'+tab+'_service').html('');

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
									<td><button type="button" data-id="${item.id}" class="set2">삭제</button></td>
								</tr>`;
                    }

                    html = data.length > 0 ? html : '<tr><td colspan="">내역이 없습니다1.</td></tr>';

                    // $('.sortable').html(html);
                    $('.'+tab+'_service').html(html);
                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });

    }

    function showModal(tab) {
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

                    let service = $('#'+tab+'_service').val();
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

                    $('#tab_gubun').val(tab);

                    $('.modal .service_list').html(html);
                    $('#showCategory').modal('show');

                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });

    }

    function saveSortDisplay(tab) {

        let service = [];

        $('.'+tab+'_service tr').each(function (index) {
            let id   = $(this).data('id');
            service.push(id);
        });
        $('#'+tab+'_service').val(service);

        let request = new FormData();

        request.set('service',  service.join());
        request.set('tab',  tab);

        // alert(service.join());
        // return;

    	$.ajax({
            url: '/service/sort_display',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                if(response.code == 200){
                    console.log(response);
                    alert('저장 되었습니다.');
                    location.href = '/service/display';
                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });
    }

</script>
@endpush
