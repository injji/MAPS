@extends('layouts.cms')

@section('content')

<form action="{{ route('store.func') }}">
<div class="board_search_ex">
	<div class="b_s_ex b_s_ex2">
		<input type="text" value="{{$keyword}}" name="keyword" class="ml-0" />

		<button type="submit">
			<img src="/assets/images/store/search_on.svg">
		</button>
	</div>

	<div class="total_ex">
		@if ($write_permission)
			<a href="{{ route('store.func_register') }}" >신규등록</a>
		@else
			<a href="javascript:alert('관리자에게 권한 요청해 주세요.')" >신규등록</a>
		@endif
	</div>
</div>
</form>

<div class="board_table_ex">
	<table class="table_no8">
		<colgroup>
			<col width="56px" />
			<col />
			<col />
			<col/>
			<col />
			<col />
			<col width="185px" />
		</colgroup>

		<thead>
			<tr>
				<th>No</th>
				<th>비중</th>
				<th>항목명</th>
				<th>서비스분류</th>
                <th>노출기간</th>
				<th>진열 상품수</th>
                <th>관리</th>
			</tr>
		</thead>

		<tbody>
			@foreach($results as $key => $item)
			<tr>
				<td>{{$key + 1}}</td>
				<td><a class="set3">{{$item->weight}}</a></td>
				<td class="color00">
					@if ($write_permission)
						<a href="{{ route('store.func_detail') }}?id={{$item->id}}">{{$item->title}}</a>
					@else
						<a href="javascript:alert('관리자에게 권한 요청해 주세요.')">{{$item->title}}</a>
					@endif
				</td>
                <td>{{$item->kinds}}</td>
				<td>{{$item->st_date. ' ~ ' .$item->end_date}}</td>
                <td>{{$item->service_cnt}}</td>
				<td>
					@if ($write_permission)
						<button type="button" onclick="setExpo('{{$item->id}}', {{$item->expo ? 0 : 1}})" class="{{$item->expo == 1 ? 'set1' : 'set3'}}">노출</button>
						<button type="button" onclick="delItem('{{$item->id}}')" class="set2">삭제</button>
					@else
						<button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')" class="{{$item->expo == 1 ? 'set1' : 'set3'}}">노출</button>
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

@endsection

@push('scripts')
<script type="text/javascript">
	
    function setExpo(func_id, expo){

		$.ajax({
			url: '/cms_store/change_func',
			method: 'post',
			data: {type: 'expo', func_id, expo},
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
	
    function delItem(func_id){
    	if(!confirm('삭제하시겠습니까?')) {
    		return ;
    	}
		$.ajax({
			url: '/cms_store/change_func',
			method: 'post',
			data: {type: 'delete', func_id},
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
