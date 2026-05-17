@extends('layouts.home-tech')

@section('body_class', trim('g-search search-redesign '.$__env->yieldContent('search_listing_body_class', '')))

@push('styles')
<link href="{{ asset('frontend/css/search-redesign.css') }}?v=2.1" rel="stylesheet">
@endpush


@section('content')
    @yield('search_listing_content')
@endsection
