@extends('layouts.home-tech')

@section('content')
    <main class="tech-home-main">
        @include('partials.home.hero')

        @include('partials.home.policies', [
            'policyItems' => $policyItems,
        ])

        @include('partials.home.flash-sale', [
            'products' => $flashSaleProducts,
        ])

        @include('partials.home.categories')

        @include('partials.home.promo-banners')

        <section class="tech-section tech-product-section" id="featured-products">
            <div class="container">
                <div class="tech-section-head">
                    <div>
                        <span class="tech-eyebrow"><i class="fa fa-star-o"></i> SẢN PHẨM NỔI BẬT</span>
                        <h2>Sản phẩm được quan tâm</h2>
                    </div>
                    <a href="{{ url('/san-pham') }}" class="tech-section-link">Xem tất cả <i class="fa fa-angle-right"></i></a>
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

        @include('partials.home.news', [
            'newsItems' => $newsItems,
        ])

        @include('partials.home.partners', [
            'brandItems' => $brandItems,
        ])
    </main>

    @include('partials.home.footer', [
        'paymentItems' => $paymentItems,
    ])
@endsection
