@extends('layouts.default')

@section('body')
    <div id="wrapper">
        <header id="topnav">
            @include('partials.cms_header')
            @include('partials.cms_navigation', [
                'menus' => config('menu')
            ])
        </header>
    </div>
    
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cms.css') }}">
@endpush
