@extends('layouts.home-tech')

@section('content')
    <main class="tech-home-main">
        @include('partials.home.hero')

        <section class="tech-section" id="featured-products">
            <div class="container">
                <div class="tech-section-head">
                    <div>
                        <span class="tech-eyebrow">Sản phẩm nổi bật</span>
                        <h2>Sản phẩm công nghệ đang được quan tâm</h2>
                    </div>
                    <a href="{{ url('/san-pham') }}" class="tech-section-link">Xem tất cả</a>
                </div>

                <div class="row tech-product-grid">
                    @foreach ($featuredProducts as $product)
                        <div class="col-xs-12 col-sm-6 col-md-3">
                            @include('partials.home.product-card', [
                                'product' => $product,
                            ])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

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
