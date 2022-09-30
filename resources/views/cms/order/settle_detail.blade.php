@extends('layouts.cms')

@section('content')

<form action="{{ route('order.settle_detail') }}">
<div class="board_search_ex">
	<div class="b_s_ex b_s_ex2">
        <label>
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
        <label class="mr-2">
            <select name="agent_id" onchange="changeAgent(this.value)">
                <option value="">제휴사선택</option>
                @foreach($agent_list as $key => $item)
                    <option value="{{ $item->id }}" {{ $agent_id == $item->id ? 'selected' : ''}}>{{ $item->company_name }}</option>
                @endforeach
            </select>
        </label>
        <label class="mr-2">
            <select name="service_id">
                <option value="">서비스선택</option>
                @foreach($service_list as $key => $item)
                    <option value="{{ $item->id }}" {{ $service_id == $item->id ? 'selected' : ''}}>{{ $item->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="mr-2">
            <select name="category_id">
                <option value="">카테고리선택</option>
                @foreach($category_list as $key => $item)
                    <option value="{{ $item->id }}" {{ $category_id == $item->id ? 'selected' : ''}}>{{ $item->text }}</option>
                @endforeach
            </select>
        </label>

		<button type="submit">
			<img src="/assets/images/store/search_on.svg">
		</button>
	</div>

	<div class="total_ex">

        <a href="{{ route('cms_order_export', [
                'st_date' => $st_date,
                'keyword' => $keyword,
                'agent_id'    => $agent_id,
                'service_id'  => $service_id,
                'category_id' => $category_id,
                'type'        => 'settle_detail'
            ]) }}" >다운로드</a>
	</div>
</div>

<div class="board_table_ex">
    <?php
        $krw_total = 0; $usd_total = 0; $total_sum = 0;
        foreach ($results as $key => $value) {
            if($value->currency=='$') {
                $usd_total += $value->amount;
            }
            if($value->currency=='￦') {
                $krw_total += $value->amount;
            }
            $total_sum += $value->amount;
        }
    ?>
	<table>
		<thead>
			<tr>
				<th>No</th>
                <th>주문번호</th>
				<th>결제일</th>
				<th>제휴사</th>
				<th>서비스명</th>
                <th>사이트명</th>
				<th>구분</th>
                <th>결제수단</th>
				<th>이용기간</th>
                <th>결제금액</th>
                <th>정산금액</th>
			</tr>
            <tr>
                <th colspan="9" class="text-right">합계</th>
                <th>{{ number_format($total_sum) }}</th>
                <th>{{ number_format($total_sum * 0.85) }}</th>
            </tr>
		</thead>

            @foreach($results as $key => $item)
            <tr >
                <td>{{ $results->total() - ($results->currentPage() - 1) * $results->perPage() - $key }}</td>
                <td>{{ $item->order_no }}</td>
                <td>{!! substr($item->created_at, 0, 10) !!}</td>
                <td>{{ $item->service->user->company_name }}</td>
                <td>{{ $item->service->name }}</td>
                <td>{{ $item->site->name }}</td>
                <td>{{ $item->service->in_app_payment == 1 ? '인앱' : '자체' }}</td>
                <td>{{ $item->payment_type_text ?? '' }}</td>
                <td><span>{{ $item->service_start_at ? $item->service_start_at->format('Y.m.d'). ' ~ ' : '' }}{{ $item->service_end_at ? $item->service_end_at->format('Y.m.d') : '' }}</span></td>
                <td>{{ number_format($item->amount) }}</td>
                <td>{{ number_format($item->amount * 0.85) }}</td>
            </tr>
            @endforeach

            @if(count($results) == 0)
            <tr class="tabletr">
                <td colspan="11">@lang('sub.agent-no_data')</td>
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

@push('scripts')
<script type="text/javascript">
    function changeAgent(id) {
		var list = @json($total_service_list);

		if (id != "")
			list = list.filter(obj => obj.agent_id == id);

		var html = '<option value="">서비스선택</option>';

		for(var i = 0; i < list.length; i++) {
			html += '<option value="'+list[i].id+'">'+list[i].name+'</option>';
		}

		$("select[name='service_id']").html(html);
	}
</script>
@endpush
