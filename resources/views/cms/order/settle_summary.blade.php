@extends('layouts.cms')

@section('content')

<form action="{{ route('order.settle_summary') }}">
<div class="board_search_ex">
    <div class="b_s_ex b_s_ex2">
        <label class="mr-2">
            <select name="agent_id">
                <option value="">제휴사선택</option>
                @foreach($agent_list as $key => $item)
                    <option value="{{ $item->id }}" {{ $agent_id == $item->id ? 'selected' : ''}}>{{ $item->company_name }}</option>
                @endforeach
            </select>
        </label>
        <label >
            <select name="st_date">
                <?php for ($i=1; $i <= date('m'); $i++) {
                    $j = $i < 10 ? '0'.$i : $i;
                    $date = date("Y").'-'.$j;
                    $selected = $st_date == $date ? 'selected' : '';
                    echo '<option value="'.$date.'" '.$selected.'>'.$date.'</option>';
                }
                ?>
            </select>
        </label>
        <input type="text" value="{{$keyword}}" name="keyword" placeholder="@lang('sub.agent-site')" />

        <button type="submit">
            <img src="/assets/images/store/search_on.svg">
        </button>
    </div>

    <div class="total_ex">

        <a href="{{ route('cms_order_export', [
                'st_date' => $st_date,
                'keyword' => $keyword,
                'type'    => 'settle_summary',
            ]) }}" >다운로드</a>
    </div>
</div>

<div class="board_table_ex">
    <?php
        $req_cnt_total = 0; $krw_total = 0; $usd_total = 0; $total_sum = 0;
        foreach ($results as $key => $value) {
            $req_cnt_total += $value->req_cnt;
            if($value->currency=='$') {
                $usd_total += $value->amount;
            }
            if($value->currency=='￦') {
                $krw_total += $value->amount;
            }
            $total_sum += $value->total_sum;
        }
    ?>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>상세내역</th>
                <th>정산상태</th>
                <th>기준</th>
                <th>제휴사</th>
                <th>신청수</th>
                <th>결제금액</th>
                <th>정산금액</th>
                <th>수수료</th>
            </tr>
            <tr>
                <th colspan="5" class="text-right">합계</th>
                <th>{{ number_format($req_cnt_total) }}</th>
                <th>{{ number_format($total_sum) }}</th>
                <th>{{ number_format($total_sum * 0.85) }}</th>
                <th>{{ number_format($total_sum - ($total_sum * 0.85)) }}</th>
            </tr>
        </thead>

        <tbody>
            @foreach($results as $key => $item)
            <tr >
                <td>{{ $results->total() - ($results->currentPage() - 1) * $results->perPage() - $key }}</td>
                <td><a href="settle_detail/?agent_id={{ $item->agent_id }}" target="_blank" class="set3 text-black">상세내역</a></td>
                <td>
                @if ($write_permission)
                    @if($item->settle_status == 1)
                        <button type="button" onclick="showStatus('{{ $item->agent_id }}', '{{$item->settle_status}}')" class="set3">대기</button>
                    @elseif($item->settle_status == 2)
                        <button type="button" onclick="showStatus('{{ $item->agent_id }}', '{{$item->settle_status}}', '{{$item->settle_reason}}')" title="{{$item->settle_reason}}" class="set2">불가</button>
                    @else
                        <button type="button" onclick="showStatus('{{ $item->agent_id }}', '{{$item->settle_status}}')" class="set1">완료</button>
                    @endif
                @else
                    @if($item->settle_status == 1)
                        <button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')" class="set3">대기</button>
                    @elseif($item->settle_status == 2)
                        <button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')" class="set2">불가</button>
                    @else
                        <button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')" class="set1">완료</button>
                    @endif
                @endif
                </td>
                <td>{{ substr($item->created_at, 0, 7) }}</td>
                <td>{{ $item->service->user->company_name }}</td>
                <td>{{ number_format($item->req_cnt) }}</td>
                <td>{{ number_format($item->total_sum) }}</td>
                <td>{{ number_format($item->total_sum * 0.85) }}</td>
                <td>{{ number_format($item->total_sum - ($item->total_sum * 0.85)) }}</td>
            </tr>
            @endforeach

            @if(count($results) == 0)
            <tr class="tabletr">
                <td colspan="8">@lang('sub.agent-no_data')</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="list_btn_ex">
        {!! $results->appends(Request::except('page'))->render() !!}
    </div>

</div>
</form>

<div class="modal fade process_modal" id="sattle_status" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">


            <div class="modal-body">

                <div class="tabletr">
                    <select id="settle_status">
                        <option value="0" selected="">정산상태를 선택해 주세요</option>
                        <option value="1">대기</option>
                        <option value="2">불가</option>
                        <option value="3">완료</option>
                    </select>
                </div>
                <div class="mb-3 settle_reason d-none">
                    <input type="text" class="form-control" id="settle_reason" placeholder="불가사유를 입력해 주세요" />
                </div>
                <div>
                    <div class=" modal_btn modal_btn2">
                        <button data-bs-dismiss="modal" class="notice_close">취소</button>
                        <button onclick="changeStatus()">확인</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">

    $(document).ready(function() {
        $(document).on('change', '#settle_status', function() {
            let val = $(this).val();
            if(val == 2) {
                $('.modal .settle_reason').removeClass('d-none');
                $('#settle_reason').val('');
            }
            else {
                $('.modal .settle_reason').addClass('d-none');
            }
        })
    });

    let st_date  = '{{ $st_date }}';
    let agent_id = '';
    function showStatus(agentid, settle_status, settle_reason) {
        agent_id = agentid;
        if(settle_status == 2) {
            $('.modal .settle_reason').removeClass('d-none');
            $('#settle_reason').val(settle_reason);
        }
        $('#settle_status').val(settle_status);
        $('#sattle_status').modal('show');
    }

    function changeStatus() {
        let settle_status  = $('#settle_status').val();
        let settle_reason  = $('#settle_reason').val();

        if(!settle_status) {
            return alert('정산상태를 선택해 주세요');
        }
        if(settle_status == 2 && !settle_reason) {
            return alert('불가사유를 입력해 주세요');
        }
        let data = {
            agent_id, st_date, settle_status, settle_reason
        }
        $.ajax({
            url: '/order/change_sattle',
            method: 'post',
            data: data,
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
