@extends('layouts.client')

@section('content')

<div class="myservice">
	<div class="myservice_tit">
		<h2>@lang('client.client_siteset')</h2>
		<button onclick="createSite()">@lang('client.client_app_btn')</button>
	</div>

	<table>
		<tr>
			<th>@lang('client.client_sitename')</th>
			<th>@lang('client.client_siteurl')</th>
			<th>@lang('client.client_sitekind')</th>
			<th>@lang('client.client_sitsol')</th>
			<th>@lang('client.client_siteset_btn')</th>
		</tr>
		@foreach($list as $key => $item)
			<tr>
				<td>{{ $item->name }}</td>
				<td>{{ $item->url }}</td>
				<td>{{ $item->type_text }}</td>
				<td>{{ $item->type == 0 ? $item->host_text : ''}}</td>
				<td>
					<ul>
						<li><button type="button" onclick="editSite({{ $item }})">@lang('client.client_siteset_btn_fix')</button></li>
						{{-- <li><button type="button" onclick="del({{ $item->id }}, {{ $item->service->count() }})">@lang('client.client_siteset_btn_del')</button></li> --}}
                        <li><button type="button" onclick="del({{ $item->id }}, {{ $stop_cnt[$key] }})">@lang('client.client_siteset_btn_del')</button></li>
					</ul>
				</td>
			</tr>
		@endforeach
	</table>
</div>


<div class="cencel_popup">
	<h2>@lang('client.client_popup1')</h2>
	<p>@lang('client.client_popup2')</p>
	<button id="cencel_popup_ok">@lang('client.client_siteset_btn_ok')</button>
</div>

@endsection


@push('scripts')
<script>
	function del(id, service_cnt) {
		if (service_cnt > 0) {
			$('.cencel_popup').show(200);
		} else {
			let request = new FormData();

			request.set('id', id);

			let data = {};
			request.forEach((value, key) => (data[key] = value));

			$.ajax({
				url: '/site/delete',
				type : 'post',
				data : data,
				success : (response) => {
					if (response.code == 200) {
						toastr.success(response.message);
						location.href = document.URL;
					}
				}
			});
		}
	}

	$('#cencel_popup_ok').click(function(){
		$('.cencel_popup').hide(200);
	})
</script>
@endpush
