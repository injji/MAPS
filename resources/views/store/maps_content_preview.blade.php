
@extends('layouts.auth')

@section('body')

<div id="maps_content" class="maps_con2">
    @include('layouts.sub_header')
</div>

<div id="maps_content_detail" class="base_wrap">
    <a href="{{ route('store.mapscontent') }}">@lang('sub.maps-list')</a>

    <div class="mcd_tit">
        <h1 id="title">{{ (Session::has('preview_title')) ? Session::has('preview_title') : '....1212?' }}</h1>
        <div class="content_about">
            <span id="description">dsdsd</span>
            <em id="today"></em>
        </div>

    </div>

    <h2>
        <img src="" id="img">
    </h2>

    <p id="content">

    </p>

    <button onclick="moveTop()">@lang('sub.maps-top')</button>
</div>

<script>
    function moveTop(){
        window.scrollTo({top:0, behavior:'smooth'});
    }

    $(document).ready(function (){

    })
</script>


@endsection


