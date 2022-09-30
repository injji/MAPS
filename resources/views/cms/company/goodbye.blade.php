@extends('layouts.cms')

@section('content')

<form action="{{ route('company.goodbye') }}">
    <div class="board_search_ex">
        <div class="b_s_ex" >
            <label>
                <span>@lang('sub.agent-period')</span>
                <input type="text" value="{{$st_date}} ~ {{$ed_date}}" id="data_range" name="data_range" />
            </label>

            <input type="text" value="{{$keyword}}" name="keyword" placeholder="아이디/회사명" />
            <button type="submit">
                <img src="/assets/images/store/search_on.svg">
            </button>
        </div>

        <div class="total_ex">
            <ul>
                <li><span>TOTAL</span>{{ number_format($total_cnt) }}</li>
			    <li><span>@lang('sub.agent-search_result')</span>{{ number_format($list->total()) }}</li>
            </ul>

            {{-- <a href="" >@lang('sub.agent-download')</a> --}}
            <a href="{{ route('company.goodbye_export', [
                'type' => 'drop',
	            'st_date' => $st_date,
	            'ed_date' => $ed_date,
	            'keyword' => $keyword,
	        ]) }}" >@lang('sub.agent-download')</a>
        </div>
    </div>

    <div class="board_table_ex goodbye_table">
        <table>
            <colgroup>
                <col width="56px" />
                <col  />
                <col />
                <col />
                <col width="300px" />
                <col />
                <col />
                <col />
                <col />
                <col width="105px" />
            </colgroup>

            <thead>
                <tr>
                    <th></th>
                    <th>No</th>
                    <th>요청일</th>
                    <th>구분</th>
                    <th>아이디</th>
                    <th>회사명</th>
                    <th>사유</th>
                    <th>서비스</th>
                    <th>완료일</th>
                    <th>상태</th>
                </tr>
            </thead>

            <tbody>

                @foreach($list as $key => $item)
                    <?php

                        switch($item->status){
                            case 1: $color = 'colorred'; $status = '보류'; break;
                            case 2: $color = 'colorgray'; $status = '완료'; break;
                            default: $color = 'colorgreen'; $status = '접수'; break;
                        }

                        if($item->user_type == '고객사'){
                            $service_count = $item->client_service->count();
                        }else{
                            $service_count = $item->agent_service->count();
                        }
                    ?>
                    <tr class="tabletr">
                        <td class="table_input_radio">
                            <label><input type="checkbox" name="service_select_input" data-id="" /><em></em></label>
                        </td>
                        <td>{{ $list->total() - ($list->currentPage() - 1) * $list->perPage() - $key }}</td>
                        <td>{{ substr($item->created_at,0,10) }}</td>
                        <td>{{ $item->user_type }}</td>
                        <td>{{ $item->account }}</td>
                        <td>{{ $item->company_name }}</td>
                        <td>{{ $item->reason }}</td>
                        <td>{{ $service_count }}</td>
                        <td>{{ ($item->status == 2) ? substr($item->dropped_at,0,10) : '' }}</td>
                        <td><button class="{{ $color }}" type="button" data-toggle="modal" data-target="#goodbye_status" data-id="{{ $item->id }}" onclick="drop_setId({{ $item->id }},{{ $item->status }},'{{ $item->admin_reason }}')">{{ $status }}</button></td>
                    </tr>
                @endforeach


                {{-- <tr class="tabletr">
                    <td class="table_input_radio">
                        <label><input type="checkbox" name="service_select_input" data-id="" /><em></em></label>
                    </td>
                    <td>2</td>
                    <td>2022-06-14</td>
                    <td>고객사</td>
                    <td>as156</td>
                    <td>슈즈몰</td>
                    <td>이용하고자 하는 서비스 부족</td>
                    <td>2</td>
                    <td></td>
                    <td><button  class="colorgray" type="button" data-toggle="modal" data-target="#goodbye_status">완료</button></td>
                </tr>

                <tr class="tabletr">
                    <td class="table_input_radio">
                        <label><input type="checkbox" name="service_select_input" data-id="" /><em></em></label>
                    </td>
                    <td>1</td>
                    <td>2022-06-14</td>
                    <td>고객사</td>
                    <td>as156</td>
                    <td>슈즈몰</td>
                    <td>이용하고자 하는 서비스 부족</td>
                    <td>2</td>
                    <td>2022-07-08</td>
                    <td><button  class="colorred" type="button" data-toggle="modal" data-target="#goodbye_status">보류</button></td>
                </tr> --}}

            </tbody>
        </table>

        <div class="list_btn_ex">
			{!! $list->appends(Request::except('page'))->render() !!}
		</div>
    </div>
</form>

<!-- modal -->
<div class="modal fade goodbye_user" id="goodbye_status" tabindex="-1" role="dialog" aria-labelledby="basicModal"
aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
	<div class="modal-content">
		<div class="modal-body">
            {{-- <h5>정말로 회원탈퇴 요청 하시겠습니까?</h5>
            <p>탈퇴 요청 시 진행 중인 서비스 체크 후 탈퇴 처리 진행되기 때문에 수일 경과될 수 있습니다.</p> --}}
			<select class="form-select goodbye_select" name="status" onchange="" id="status">
				<option value="">상태선택</option>
				<option onclick="" value="0" selected="">접수</option>
				<option onclick="" value="1">보류</option>
				<option onclick="" value="2">완료</option>
			</select>
            <input type="hidden" id="drop_id" value="">
			<input class="form-control" value="" id="reason" name="reason" type="text" placeholder="보류 사유를 입력하세요." accept=".">

			<ul>
				<li><button type="button" data-dismiss="modal">닫기</button></li>
				<li><button type="button" onclick="goodbye_status()">확인</button></li>
			</ul>


		</div>
	</div>
</div>
</div> <!-- modal -->


@endsection

@push('scripts')
<script type="text/javascript">
	$(document).ready(function() {
        $('.goodbye_select').change(function() {
            var result = $('.goodbye_select option:selected').val();
            if (result == '1') {
            $('#reason').show();
            } else {
            $('#reason').hide();
            }
        });

        $("#data_range").data('daterangepicker').setStartDate('{{ $st_date }}');
        $("#data_range").data('daterangepicker').setEndDate('{{ $ed_date }}');
    });

    function drop_setId(id,status,reason){
        $('#drop_id').val(id);
        $('.goodbye_select').val(status);
        if(status == 1){
            $('#reason').show();
            $('#reason').val(reason);
        }
    }

    function goodbye_status(){
        let request = new FormData();
        let status = $('#status').val();
        let reason = $('#reason').val();
        let id = $('#drop_id').val();
        request.set('status', status);
        request.set('reason', reason);
        request.set('id', id);

        $.ajax({
            url: '/company/goodbye_status',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: ( response ) => {
                console.log(response);
                alert('저장 되었습니다.');
                location.reload();
            }
        })

    }

</script>
@endpush
