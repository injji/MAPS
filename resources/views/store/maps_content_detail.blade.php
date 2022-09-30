
@extends('layouts.auth')

@section('body')

<div id="maps_content" class="maps_con2">
    @include('layouts.sub_header')
</div>

<div id="maps_content_detail" class="base_wrap">
    <a href="{{ route('store.mapscontent') }}">@lang('sub.maps-list')</a>

    <div class="mcd_tit">
        <h1>{{ $content->title }}</h1>
        <div class="content_about">
            <span>{{ $content->description }}</span>
            <em>{{ $content->created_at->format('Y.m.d') }}</em>
        </div>

    </div>

    {{-- <h2>
        <img src="{{ Storage::url($content->img) }}">
    </h2> --}}

    <p>
        {!! $content->content !!}
    </p>

    <button onclick="moveTop()">@lang('sub.maps-top')</button>
</div>

<script>
    function moveTop(){
        window.scrollTo({top:0, behavior:'smooth'});
    }
</script>


@endsection


