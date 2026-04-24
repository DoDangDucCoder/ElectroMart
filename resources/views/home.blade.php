@extends('layouts.home-tech')

@section('content')
    <main class="tech-home-main">
        @include('partials.home.hero')

        @include('partials.home.featured-products', [
            'products' => $featuredProducts,
        ])

        @include('partials.home.flash-sale', [
            'products' => $flashSaleProducts,
        ])

        @include('partials.home.news', [
            'newsItems' => $newsItems,
        ])

        @include('partials.home.brands-policies', [
            'brandItems' => $brandItems,
            'policyItems' => $policyItems,
        ])
    </main>

    @include('partials.home.footer', [
        'paymentItems' => $paymentItems,
    ])
@endsection
