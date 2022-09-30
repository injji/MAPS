@extends('layouts.auth')

@section('body')

@include('layouts.sub_header')

<div class="base_wrap termscss">
    {!! $terms['content'] !!}
</div>

@endsection


