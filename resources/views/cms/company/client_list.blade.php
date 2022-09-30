@extends('layouts.cms')

@section('content')

<form action="{{ route('company.client') }}">
<div class="board_search_ex">
	<div class="b_s_ex b_s_ex2">
		<label class="mr-2">
			<span>기간</span>
            <input type="text" value="{{$st_date}} ~ {{$ed_date}}" id="data_range" name="data_range" />
		</label>

		<button type="submit">
			<img src="/assets/images/store/search_on.svg">
		</button>
	</div>

	<div class="total_ex">
		<a href="{{ route('company.export', [
	            'st_date' => $st_date,
	            'ed_date' => $ed_date,
				'type' => 1
	        ]) }}" >다운로드</a>
	</div>
</div>

<div class="board_table_ex">
	<table>
		<thead>
			<tr>
				<th>#</th>
				<th>로그인</th>
				<th>아이디</th>
				<th>회사명</th>
                <th>담당자</th>
				<th>휴대폰번호</th>
                <th>이메일</th>
                <th>사이트수</th>
                <th>이용서비스</th>
                <th>가입일시</th>
			</tr>
		</thead>

		<tbody>
			@foreach($list as $key => $item)
				<tr>
					@if ($write_permission)
						<td class="pointer" onclick="location.href='edit/{{ $item->id }}'">{{ $list->total() - ($list->currentPage() - 1) * $list->perPage() - $key }}</td>
						<td><a href="{{ route('user.admin', ['type' => 1,'id' => $item->id]) }}" target="_blank" class="set3 text-black">join</a></td>
						<td class="pointer" onclick="location.href='edit/{{ $item->id }}'">{{ $item->account }}</td>
						<td class="pointer" onclick="location.href='edit/{{ $item->id }}'">{{ $item->company_name }}</td>
						<td class="pointer" onclick="location.href='edit/{{ $item->id }}'">{{ $item->manager_name }}</td>
						<td class="pointer" onclick="location.href='edit/{{ $item->id }}'">{{ $item->manager_phone }}</td>
						<td class="pointer" onclick="location.href='edit/{{ $item->id }}'">{{ $item->manager_email }}</td>
                        <td class="pointer site_info_td" data-toggle="modal" data-target="#site_info_td" data-item="{{ $item->site }}">{{ $item->site->count() }}</td>
						<td class="pointer" onclick="location.href='edit/{{ $item->id }}'">{{ $item->client_service->count() }}</td>
						<td class="pointer" onclick="location.href='edit/{{ $item->id }}'">{{ $item->created_at }}</td>
					@else
						<td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $list->total() - ($list->currentPage() - 1) * $list->perPage() - $key }}</td>
						<td><a href="javascript:alert('관리자에게 권한 요청해 주세요.')" class="set3 text-black">join</a></td>
						<td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->account }}</td>
						<td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->company_name }}</td>
						<td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->manager_name }}</td>
						<td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->manager_phone }}</td>
						<td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->manager_email }}</td>
                        <td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->site->count() }}</td>
						<td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->client_service->count() }}</td>
						<td class="pointer" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')">{{ $item->created_at }}</td>
					@endif
				</tr>
			@endforeach
		</tbody>
	</table>

	<div class="list_btn_ex">
		{!! $list->appends(Request::except('page'))->render() !!}
	</div>
</div>
</form>

<!-- modal -->
<div class="modal fade" id="site_info_td" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div>
					<label class="form-label fs-6">사이트 정보</label>
					<table>
						<thead>
							<tr>
								<th>사이트명</th>
								<th>URL</th>
								<th>구분</th>
								<th>솔루션</th>
								<th>이용서비스</th>
								<th>스크립트</th>
								<th>사이트 등록일시</th>
							</tr>
						</thead>

						<tbody id="site_body">
						</tbody>
					</table>

					<button data-dismiss="modal">닫기</button>
				</div>
            </div>
        </div>
    </div>
</div> <!-- modal -->

@endsection

@push('scripts')
<script type="text/javascript">

    $(document).ready(function() {
        $("#data_range").data('daterangepicker').setStartDate('{{$st_date}}');
        $("#data_range").data('daterangepicker').setEndDate('{{$ed_date}}');
    });

    $('.site_info_td').on('click', function(){
        $('#site_body').html('');
        let siteData = $(this).data('item');
        let siteTr = '';
        for ( let key of Object.keys(siteData) ) {
            let siteType = '';
            let siteHostname = '';
            let siteScript = '';
            switch(siteData[key].type){
                case 0: siteType = '쇼핑몰'; break;
                case 1: siteType = '홈페이지'; break;
                case 2: siteType = '언론사'; break;
                case 3: siteType = '기타'; break;
                default: break;
            }
            switch(siteData[key].hostname){
                case 0: siteHostname = '독립몰'; break;
                case 1: siteHostname = '카페24'; break;
                case 2: siteHostname = '메이크샵'; break;
                case 3: siteHostname = '고도'; break;
                default: break;
            }
            switch(siteData[key].header){
                case 0: siteScript = 'N'; break;
                case 1: siteScript = 'Y'; break;
                default: break;
            }

            $.ajax({
                url: '/client/site/count',
                method: 'post',
                data:{
                    id: siteData[key].id
                },
                dataType: 'json',
                async: false,
                success: (response) => {
                    if(response.code == 200){
                        serviceCnt = response.cnt;
                    }
                }
            })
            let siteDate = new Date(siteData[key].created_at);
            // console.log(siteDate); //2022-07-07 05:33:59
            siteDate.setHours(siteDate.getHours()+9);
            siteDate = new Date(siteDate).toISOString().slice(0, 19).replace('T', ' ');

            siteTr += '<tr>';
            siteTr += '<td>'+siteData[key].name+'</td>';
            siteTr += '<td>'+siteData[key].url+'</td>';
            siteTr += '<td>'+siteType+'</td>';
            siteTr += '<td>'+siteHostname+'</td>';
            siteTr += '<td>'+serviceCnt+'</td>';
            siteTr += '<td>'+siteScript+'</td>';
            siteTr += '<td>'+siteDate+'</td>';
            siteTr += '</tr>';

        }
        $('#site_body').html(siteTr);
    })
</script>
@endpush
