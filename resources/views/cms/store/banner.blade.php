@extends('layouts.cms')

@section('content')

<div class="tabbable">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link small fw-bold {{ $tab == 1 ? 'active' : '' }}" onclick="location.href='/cms_store/banner?tab=1'">스토어 메인</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link small fw-bold {{ $tab == 2 ? 'active' : '' }}" onclick="location.href='/cms_store/banner?tab=2'">스토어 중간</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link small fw-bold {{ $tab == 3 ? 'active' : '' }}" onclick="location.href='/cms_store/banner?tab=3'">스토어 하단</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link small fw-bold {{ $tab == 4 ? 'active' : '' }}" onclick="location.href='/cms_store/banner?tab=4'">고객사 관리자</button>
        </li>
    </ul>
</div>
<div class="tab-content border shadow-sm p-5 bg-white border-top-0" >
    <div class="tab-pane fade show active" id="tab1" role="tabpanel">
        <div class="d-flex align-items-end justify-content-between">
            <label>드래그 앤 드롭으로 배너 순서를 변경할 수 있습니다. <br /><label class="p-0 col-form-label small">이미지 권장 사이즈 
                @if($tab == 1)
                    @lang('form.img_size.section.main')
                @elseif($tab == 2)
                    @lang('form.img_size.section.mid')
                @elseif($tab == 3)
                    @lang('form.img_size.section.bottom')
                @elseif($tab == 4)
                    @lang('form.img_size.section.agent')
                @endif
                
                pixel</label></label>
            <div>
                <button type="button" class="btn btn-primary btn-new-banner" onclick="regBanner()">신규등록</button>
            </div>
        </div>
        <div class="sortable banner mt-4">
            @foreach($results as $key => $item)
            <div class="item" data-id="{{ $item->id }}" data-sort="{{ $key }}">
                <div class="head">
                    <div>
                        <i class="fa fa-align-justify mr-2" aria-hidden="true"></i> <span>{{ $item->title }}</span>
                    </div>
                    <div>
                        <i class="fa fa-edit" aria-hidden="true" data-id="{{ $item->id }}"></i> <i class="fa fa-times ml-2" aria-hidden="true" data-id="{{ $item->id }}"></i>
                    </div>
                </div>
                <div class="content">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="{{ $item->ko ? Storage::url($item->ko) : '/assets/images/empty2.png' }}" width="100%" />
                        </div>
                        <div class="col-md-6">
                            <div class="row mb-4 " id="lang-wrapper">
                                <div class="col-md-2">
                                    <label class="form-label p-0 m-0 fs-6">사용여부</label>
                                </div>
                                <div class="col-md-10">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" id="use1{{ $key }}" name="use{{ $key }}" type="radio" value="1" data-id="{{$item->id}}" {{ $item->status == 1 ? 'checked' : '' }}  />
                                        <label class="form-check-label" for="use1{{ $key }}">사용</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" id="use2{{ $key }}" name="use{{ $key }}" type="radio" value="2" data-id="{{$item->id}}" {{ $item->status == 2 ? 'checked' : '' }} />
                                        <label class="form-check-label" for="use2{{ $key }}">미사용</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-2">
                                    <label class="form-label p-0 m-0 fs-6" for="url">URL</label>
                                </div>
                                <div class="col-md-10">
                                    <input class="form-control" id="url" name="url" type="text" value="{{ $item->url }}" />
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-2">
                                    <label class="form-label p-0 m-0 fs-6" for="url">노출기간</label>
                                </div>
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <div class="input-group mt-1 mt-md-0 w-100">
                                            <input type="text" id="single_date" class="form-control single_date st_date" value="{{ $item->st_date }}" data-id="{{$item->id}}" />
                                            <span class="input-group-text">~</span>
                                            <input type="text" id="single_date2" class="form-control single_date end_date" value="{{ $item->end_date }}" data-id="{{$item->id}}" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-2">
                                    <label class="form-label p-0 m-0 fs-6" for="url">배너이미지</label>
                                </div>
                                <div class="col-md-10 btn-wrap ">
                                    <button type="button" data-id="{{$item->id}}" data-type="ko" data-path="{{$item->ko}}" class=" line_btn {{ $item->ko ? 'set1' : 'set3' }}">한국어</button>
                                    <button type="button" data-id="{{$item->id}}" data-type="en" data-path="{{$item->en}}" class=" line_btn {{ $item->en ? 'set1' : 'set3' }}">영어</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

    </div>
</div>

<div class="modal fade userqa_modal" id="regModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="board_table_info p-0">
                    <table>
                        <tbody>
                            <tr>
                                <th style="width: 20%;">사용여부</th>
                                <td > 
                                    <label>
                                        <input type="radio" value="1" name="modal_lang" checked /><em>사용</em>
                                    </label>
                                    <label>
                                        <input type="radio" value="2" name="modal_lang" /><em>미사용</em>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th>제목</th>
                                <td><input class="form-control" name="title" type="text" /></td>
                            </tr>
                            <tr>
                                <th>URL</th>
                                <td><input class="form-control" name="url" type="text" /></td>
                            </tr>
                            <tr>
                                <th>기간</th>
                                <td>
                                    <div class="input-group">
                                        <div class="input-group mt-1 mt-md-0 w-100">
                                            <input type="text" id="single_date" class="form-control single_date" style="width: 0%;" />
                                            <span class="input-group-text">~</span>
                                            <input type="text" id="single_date2" class="form-control single_date" style="width: 0%;" />
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="banner_img_wrap">
                                <th>배너이미지 <br /><span class="p-0 col-form-label small">
                                    @if($tab == 1)
                                        @lang('form.img_size.section.main')
                                    @elseif($tab == 2)
                                        @lang('form.img_size.section.mid')
                                    @elseif($tab == 3)
                                        @lang('form.img_size.section.bottom')
                                    @elseif($tab == 4)
                                        @lang('form.img_size.section.agent')
                                    @endif    
                                pixel</span></th>
                                <td>
                                    <div class="position-relative">
                                        <img src="/assets/images/empty2.png" width="100%" class="pointer" />
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </div>
                                    <input type="file" id="chooseFile" style="visibility:hidden;" name="chooseFile" onchange="loadFile(this)" accept="image/*" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div>
                    <div class="modal_btn">
                        <button data-bs-dismiss="modal" class="notice_close">취소</button>
                        <button type="button" onclick="saveBanner()">저장</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

<style type="text/css">
    .banner.sortable .item {
        border: none; background: whtie;
    }
    .banner.sortable .head {
        border: 1px solid #c5c5c5;
        background: #f6f6f6;
        border-radius: 4px;
    }
    .banner.sortable .content {background: white; padding-top: .5rem;}
    .modal table td {text-align: left;}
    .modal table .fa-plus {position: absolute; top: 42%; left: 47%; font-size: 2rem;}
    
</style>
@push('scripts')

<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script type="text/javascript">
    
    let banner_lang = '';
    let banner_id   = '';
    $(document).ready(function() {
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
                $('.sortable .item').each(function (index) {
                    $(this).attr('data-sort', index);
                });
                updateSort();
            }
        });
        // 
        $(document).on('click', '.item .fa-edit', function() {
            let id = $(this).data('id');
            getBannerById(id);
        });
        $(document).on('click', 'img.pointer', function() {
            $('#chooseFile').trigger('click');
        });
        // 
        $(document).on('click', '.btn-wrap button', function() {
            banner_lang = $(this).data('type');
            banner_id   = $(this).data('id');
            let path    = $(this).data('path');
            $('.modal tr').css('display', 'none');
            $('.modal tr.banner_img_wrap').css('display', 'table-row');
            $('.modal img').attr('src', path ? '/storage/' + path : '/assets/images/empty2.png');
            $('#regModal').modal('show');
        });

        // 
        $(document).on('click', 'i.fa-times', function() {
			@if (!$write_permission)
				return alert('관리자에게 권한 요청해 주세요.')
			@endif

            banner_id   = $(this).data('id');
            if(!confirm('배너를 삭제하시겠습니까?')) {
                return;
            }
            $.ajax({
                url: '/cms_store/change_banner',
                method: 'post',
                data: {type: 'delete', banner_id},
                success: (response) => {
                    if(response.code == 200){
                        location.reload();
                    }
                },
                error: (e) => {
                    console.log(e.responseJSON)
                }
            });
        });
        //  change date
        $(document).on('blur', '.sortable .single_date', function() {
            @if (!$write_permission)
                return alert('관리자에게 권한 요청해 주세요.')
            @endif

            let banner_id = $(this).data('id');
            let st_date   = $(this).parent().find('.st_date').val();
            let end_date  = $(this).parent().find('.end_date').val();

            $.ajax({
                url: '/cms_store/change_banner',
                method: 'post',
                data: {type: 'date', banner_id, st_date, end_date},
                success: (response) => {
                    if(response.code == 200){
                    }
                },
                error: (e) => {
                    console.log(e.responseJSON)
                }
            });
        });
        //  change usage
        $(document).on('click', '.sortable .form-check-input', function() {
            @if (!$write_permission)
                return alert('관리자에게 권한 요청해 주세요.')
            @endif

            let banner_id = $(this).data('id');
            let status    = $(this).val();

            $.ajax({
                url: '/cms_store/change_banner',
                method: 'post',
                data: {type: 'status', banner_id, status},
                success: (response) => {
                    if(response.code == 200){
                    }
                },
                error: (e) => {
                    console.log(e.responseJSON)
                }
            });
        });

    });

    var banner_img = null;

    function loadFile(input) {
        banner_img = input.files[0];
        // 
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("img.pointer").attr("src", e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    };

    function regBanner() {
        banner_id = '';
        $('.modal tbody tr').css('display', 'table-row');
        $('.modal input[name="title"], .modal input[name="url"]').val('');
        $('.modal img').attr('src', '/assets/images/empty2.png');
        $('#regModal').modal('show');
    }

    function showModal() {
        $('#regModal').modal('show');
    }

    function getBannerById(id) {
        @if (!$write_permission)
            return alert('관리자에게 권한 요청해 주세요.')
        @endif

        $.ajax({
            url: '/cms_store/get_banner',
            method: 'post',
            data: {id},
            success: (response) => {
                if(response.code == 200){
                    let data    = response.data;
                    banner_id   = id;
                    banner_lang = '';
                    $('.modal tbody tr').css('display', 'table-row');
                    $(`.modal input[value="${data.status}}}"]`).prop(true);
                    $('.modal input[name="title"]').val(data.title);
                    $('.modal input[name="url"]').val(data.url);
                    if(data.st_date) {
                        $(".modal #single_date").data('daterangepicker').setStartDate(data.st_date);
                    }
                    if(data.end_date) {
                        $(".modal #single_date2").data('daterangepicker').setStartDate(data.end_date);
                    }
                    $('.modal img').attr('src', data.ko ? '/storage/' + data.ko : '/assets/images/empty2.png');
                    $('#regModal').modal('show');
                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });
    }

    function saveBanner() {
        @if (!$write_permission)
            return alert('관리자에게 권한 요청해 주세요.');
        @endif

        let status = $(`.modal input[type="radio"]:checked`).val();
        let title  = $('.modal input[name="title"]').val();
        let url    = $('.modal input[name="url"]').val();
        let st_date  = $('.modal #single_date').val();
        let end_date = $('.modal #single_date2').val();
        let banner_type = '{{ $tab }}';

        if((!title || !url || !st_date || !end_date) && !banner_lang) {
            return alert('필수 항목들을 입력해주세요.');
        }

        let request = new FormData();
        request.set('type',       'save');
        request.set('banner_id',  banner_id);
        request.set('status',     status);
        request.set('title',      title);
        request.set('url',        url);
        request.set('st_date',    st_date);
        request.set('end_date',   end_date);
        request.set('banner_img', banner_img);
        request.set('banner_lang', banner_lang);
        request.set('banner_type', banner_type);

        $.ajax({
            url: '/cms_store/change_banner',
            method: 'post',
            data: request,
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

    function updateSort() {
        @if (!$write_permission)
            return alert('관리자에게 권한 요청해 주세요.')
        @endif

        let items = [];
        $('.sortable .item').each(function (index) {
            let id   = $(this).data('id');
            let sort = $(this).attr('data-sort');
            items.push({id, sort});
        });

        $.ajax({
            url: '/cms_store/change_banner',
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

</script>
@endpush
