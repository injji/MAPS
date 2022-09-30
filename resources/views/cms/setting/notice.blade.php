@extends('layouts.cms')

@section('content')

<form action="{{ route('setting.notice') }}">
<div class="board_search_ex">
	<div class="b_s_ex b_s_ex2">
		<input type="text" value="{{$keyword}}" name="keyword" style="margin:0 15px 0 0 " />

		<button type="submit">
			<img src="/assets/images/store/search_on.svg">
		</button>
	</div>

	<div class="total_ex">
		@if ($write_permission)
			<a href="{{ route('setting.notice_register') }}">신규등록</a>
		@else
			<a href="javascript:alert('관리자에게 권한 요청해 주세요.')">신규등록</a>
		@endif
	</div>
</div>
<div class="board_search_ex">
	<div class="table_input_radio">
        <div class="form-check form-check-inline mr-2">
            <input class="form-check-input" id="type1" name="type" type="radio" value="-1" {{ $type == -1 ? 'checked' : '' }} />
            <label class="form-check-label" for="type1">전체</label>
        </div>
        <div class="form-check form-check-inline mr-2">
            <input class="form-check-input" id="type2" name="type" type="radio" value="0" {{ $type == '0' ? 'checked' : '' }} />
            <label class="form-check-label" for="type2">전체공지</label>
        </div>
        <div class="form-check form-check-inline mr-2">
            <input class="form-check-input" id="type3" name="type" type="radio" value="2" {{ $type == '2' ? 'checked' : '' }} />
            <label class="form-check-label" for="type3">제휴사공지</label>
        </div>
        <div class="form-check form-check-inline mr-2">
            <input class="form-check-input" id="type4" name="type" type="radio" value="1" {{ $type == '1' ? 'checked' : '' }} />
            <label class="form-check-label" for="type4">고객사공지</label>
        </div>

	</div>

</div>

<div class="board_table_ex">
	<table class="table_no8">
		<colgroup>
			<col width="56px" />
			<col />
			<col />
			<col/>
			<col width="86px" />
		</colgroup>

		<thead>
			<tr>
				<th>No</th>
				<th>등록일</th>
				<th>구분</th>
				<th>제목</th>
                <th>조회수</th>
			</tr>
		</thead>

		<tbody>
			@foreach($results as $key => $item)
			@if ($write_permission)
				<tr class="pointer" onclick="location.href='{{ route('setting.notice_detail') }}?id={{$item->id}}'">
			@else
				<tr class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">
			@endif
				<td>{{ $results->total() - ($results->currentPage() - 1) * $results->perPage() - $key }}</td>
				<td>{{ $item->created_at }}</td>
				<td>{{ $item->type_text . ($item->popup ? '(팝업)' : '') }}</td>
                <td>{{ $item->title }}</td>
				<td>{{ number_format($item->hits) }}</td>
			</tr>
			@endforeach

			@if(count($results) == 0)
            <tr class="tabletr">
                <td colspan="5">@lang('sub.agent-no_data')</td>
            </tr>
            @endif
		</tbody>
	</table>

	<div class="list_btn_ex">
		{!! $results->appends(Request::except('page'))->render() !!}
	</div>
</div>
</form>

@endsection

<style type="text/css">
	.table_input_radio label { margin-top: 0px !important; }
</style>
@push('scripts')
<script type="text/javascript">



    function addAnswer() {

    }
</script>
@endpush
