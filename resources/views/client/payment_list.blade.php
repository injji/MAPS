@extends('layouts.client')

@section('content')
<div class="p-md-5">
<div class="board_tit board_tit2">
	<h1>@lang('payment.title')</h1>
	<div>
		<a href="javascript:void(0)">@lang('payment.parent_title')</a>
		<span class="material-icons text-muted" style="vertical-align: middle;opacity: .5;">chevron_right</span>
		<a href="{{ route('client.payment_list') }}">@lang('payment.title')</a>
	</div>
</div>

<form id="search_form" action="{{ route('client.payment_list') }}">
	<input type="hidden" id="sort_type" name="sort_type" value="{{ $sort_type }}">
	<div class="board_table_ex">
		<table class="table_no8">
			<colgroup>
				<col width="56px" />
				<col />
				<col />
				<col width="200px" />
				<col />
				<col />
				<col />
                <col />
				<col />
				<col />
				<col width="195px" />
			</colgroup>


			<thead>
				<tr>
					<th>@lang('payment.field1')</th>
                    <th>@lang('payment.field10')</th>
					<th>@lang('payment.field2')</th>
					<th>@lang('payment.field3')</th>
					<th onclick="javascript:order(1)" style="cursor: pointer;">
						@lang('payment.field4')
						@if($sort_type == 1)
							<img src="/assets/images/store/top_up.png" id="order_img1">
						@else
							<img src="/assets/images/store/top_down_b.png" id="order_img1">
						@endif
					</th>
					<th>@lang('payment.field5')</th>
					<th>@lang('payment.field6')</th>
					<th>@lang('payment.field7')</th>
                    <th>@lang('payment.field11')</th>
					<th>@lang('payment.field8')</th>
					<th>@lang('payment.field9')</th>
				</tr>
			</thead>

			<tbody>
				@foreach($list as $key => $item)
                <?php
                    $term = "";
                    if($item->plan){
                        switch($item->plan->term_unit){
                            case 0 : $term = $item->plan->term.' 개월'; break;
                            case 1 : $term = $item->plan->term.' 일'; break;
                            case 2 : $term = $item->plan->term; break;
                            default : break;
                        }
                    }
                ?>
					<tr>
						<td>{{ $list->total() - ($list->currentPage() - 1) * $list->perPage() - $key }}</td>
                        <td>{{ $item->order_no ?? '' }}</td>
						<td>{!! substr($item->created_at, 0, 10).'<br/>'.substr($item->created_at, 11) !!}</td>
						<td>{{ $item->site->name ?? '' }}</td>
						<td>{{ $item->service_name ?? '' }}</td>
						<td>{{ $item->type_text ?? '' }}</td>
						<td>{{ $item->payment_type_text ?? '' }}</td>
						<td>{{ $item->plan->name ?? '' }}</td>
                        {{-- <td>{{ (isset($item->plan->term)) ? $item->plan->term.' 개월' : '' }}</td> --}}
                        <td>{{ $term }}</td>
						<td>{{ '('.$item->currency.') '.number_format($item->amount) }}</td>
						{{-- <td><span>{{ substr(str_replace('-', '.', $item->service_start_at), 0, 10) }}  ~ {{ substr(str_replace('-', '.', $item->service_end_at), 0, 10) }}</span></td> --}}
                        <td><span>{{ $item->service_start_at ? $item->service_start_at->format('Y.m.d'). ' ~ ' : '' }}{{ $item->service_end_at ? $item->service_end_at->format('Y.m.d') : '' }}</span></td>
					</tr>
				@endforeach
			</tbody>
		</table>

		<div class="list_btn_ex">
            @if(!isset($list))
                {!! $list->appends(Request::except('page'))->render() !!}
            @endif
		</div>
	</div>
</form>

</div>
@endsection

@push('scripts')
<script>
    function order(type) {
		if ($("#sort_type").val() == 0) {
			$("#sort_type").val(type);
		} else {
			if (type == $("#sort_type").val())
				$("#sort_type").val(0);
			else
				$("#sort_type").val(type);
		}

		$("#search_form").submit();
	}
</script>
@endpush
