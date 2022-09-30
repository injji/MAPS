@extends('layouts.default')

@section('body')
    @include('partials.front.header')
    @yield('content')
    {{-- @include('partials.front.footer') --}}
    @include('layouts.footer') 
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/front.css') }}">
<link rel="stylesheet" href="{{ asset('css/store/store.css') }}">
@endpush
