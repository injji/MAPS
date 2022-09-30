@extends('layouts.cms')

@section('content')

<form action="{{ route('store.conte') }}">
<div class="board_search_ex">
	<div class="b_s_ex b_s_ex2">
		<label class="mr-2">
			<span>기간</span>
            <input type="text" value="{{$st_date}} ~ {{$ed_date}}" id="data_range" name="data_range" />
		</label>
		<label class="mr-2">
			<select name="banner">
				<option value="" {{ ($banner == '') ? 'selected' : '' }}>배너여부</option>
                <option value="1" {{ ($banner == '1') ? 'selected' : '' }}>Y</option>
                <option value="0" {{ ($banner == '0') ? 'selected' : '' }}>N</option>

			</select>
		</label>

        <input type="text" placeholder="제목을 입력해주세요." name="keyword" value="{{ $keyword }}" class="ml-0" />

		<button type="submit">
			<img src="/assets/images/store/search_on.svg">
		</button>
	</div>

	<div class="total_ex">
        @if ($write_permission)
			<a href="{{ route('store.conte_register') }}" >신규등록</a>
		@else
			<a href="javascript:alert('관리자에게 권한 요청해 주세요.')" >신규등록</a>
		@endif
	</div>
</div>


</form>

<div class="board_table_ex board_table_ex5">
	<table class="table_no8">
		<colgroup>
			<col width="65px" />
			<col />
			<col />
			<col/>
			<col />
			<col />
			<col width="185px" />
		</colgroup>

		<thead>
			<tr>
				<th>노출</th>
				<th>이미지</th>
				<th>제목</th>
				<th>배너여부</th>
                <th>조회수</th>
				<th>등록일</th>
                <th>관리</th>
			</tr>
		</thead>

		<tbody class="sortable conte_list">

			{{-- <tr>
				<td><i class="fa fa-align-justify mx-2" aria-hidden="true"></i></td>
				<td><div style="height: 50px"><img src="/storage/store/pHtFHsa4KMq58ngz4oKPnwgNrzvCVutcsGYXvuB9.png" style="height: 100%; width: auto" ></div></td>
				<td>
					고객을 이해하기위한 데이터 분석과 액션플랜
				</td>
                <td>Y</td>
				<td>50</td>
                <td>2022-08-01</td>
				<td>
					@if ($write_permission)
						<button type="button" onclick="" class="set1">수정</button>
						<button type="button" data-toggle="modal" data-target="#delmodal" class="set2">삭제</button>
					@else
						<button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')" class="set3">수정</button>
						<button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')" class="set2">삭제</button>
					@endif
				</td>
			</tr> --}}
            @foreach($results as $key => $item)
                <tr data-id={{$item->id}}>
                    <td><i class="fa fa-align-justify mx-2" aria-hidden="true"></i></td>
                    <td><div style="height: 50px"><img src="{{ Storage::url($item->img) }}" style="height: 100%; width: auto" ></div></td>
                    <td>
                        {{ $item->title }}
                    </td>
                    <?php
                        $banner = 'N';
                        if($item->banner == 1){
                            $banner = 'Y';
                        }
                    ?>
                    <td>{{ $banner }}</td>
                    <td>{{ $item->hits }}</td>
                    <td>{{ substr($item->created_at,0,10) }}</td>
                    <td>
                        @if ($write_permission)
                            <button type="button" onclick="conte_detail({{ $item->id }})" class="set1">수정</button>
                            <button type="button" data-toggle="modal" data-target="#delmodal" class="set2" onclick="conte_del_set({{ $item->id }})">삭제</button>
                        @else
                            <button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')" class="set3">수정</button>
                            <button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')" class="set2">삭제</button>
                        @endif
                    </td>
                </tr>
            @endforeach


			@if(count($results) == 0)
            <tr class="tabletr">
                <td colspan="7">@lang('sub.agent-no_data')</td>
            </tr>
            @endif
		</tbody>
	</table>
    
</div>
<div class="btn_right">
    <button type="button" onclick="conte_order()" class="btn btn-secondary mdc-ripple-upgraded ">순서저장</button>
</div>



<div class="modal fade no_site" id="delmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body ">
                <p>삭제 처리 하시겠습니까?</p>
                <ul class="contact_buttons3">
                    <li>
                        <button type="button" class="btn" data-dismiss="modal">취소</button>
                    </li>
                    <li>
                        <input type="hidden" id="del_id" value="">
                        <button type="button" onclick="conte_delete();">
                            <a style="color:white" >
                                삭제
                            </a>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $('.sortable').sortable();

// $(document).on('click', '.sortable button', function() {	// delete service
//     let service_id = $(this).data('id');
//     console.log(service_id);
//     let tab = $(this).parent().parent().parent().attr('id');
//     console.log(tab);
//     let service  = $('#'+tab+'_service').val();
//     service = service.split(',').filter(id => id != service_id);
//     $('#'+tab+'_service').val(service);
//     $(this).parent().parent().remove();
// });

    $(document).ready(function() {
        $("#data_range").data('daterangepicker').setStartDate('{{$st_date}}');
        $("#data_range").data('daterangepicker').setEndDate('{{$ed_date}}');
    });

    function conte_detail(id){
        location.href="/cms_store/conte_register?id="+id;
    }

    function conte_del_set(id){
        $('#del_id').val(id);
    }

    function conte_delete(){
        let id = $('#del_id').val();
        let request = new FormData();
        request.set('id', id);

        $.ajax({
            url: '/cms_store/conte_delete',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                // alert('삭제되었습니다.');
                $('#del_id').val('');
                location.reload();
            }
        })
    }

    function conte_order(){
        let conte_arr = [];
        $('.conte_list tr').each(function (index){
            let id = $(this).data('id');
            conte_arr.push(id);
        });

        let request = new FormData();
        request.set('conte_arr',  conte_arr.join());

        $.ajax({
            url: '/cms_store/conte_sort',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                alert('순서가 저장되었습니다.');
                location.reload();
            }
        })
    }
</script>
@endpush
