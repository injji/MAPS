@extends('layouts.auth')

@section('title', __('store.store'))

@section('body')

@include('layouts.sub_header')

<!-- CCC 20220524 -->
<div class="service list_service">
    <div class="base_wrap">

		<h1>
            <img src="{{ Storage::url($funcioninf->icon) }}">
            {{ $funcioninf->title }}
        </h1>
        
		<div class="service_wrap list_service_wrap list_service_wrap3">
            @foreach ($services as $service)
                @include('partials.store.service_banner', compact('service'))
            @endforeach
		</div>
    </div>
</div>

@endsection
