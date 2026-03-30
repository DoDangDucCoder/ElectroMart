@extends('layout')

@section('content')
    @php
        $storefrontLabel = \App\Support\StorefrontLabel::class;
        $blogDetailQuickLinks = [
            [
                'icon' => 'fa-list',
                'label' => 'Danh mục bài viết',
                'value' => 'Quay lại danh mục bài viết hiện tại',
                'url' => optional($post->category)->cate_post_slug
                    ? url('/danh-muc-bai-viet/' . optional($post->category)->cate_post_slug)
                    : url('/'),
            ],
            [
                'icon' => 'fa-youtube-play',
                'label' => 'Video cửa hàng',
                'value' => 'Xem thêm clip review và hướng dẫn',
                'url' => url('/video-shop'),
            ],
            [
                'icon' => 'fa-lightbulb-o',
                'label' => 'Gợi ý sản phẩm',
                'value' => 'Khám phá thêm các sản phẩm phù hợp',
                'url' => url('/goi-y-san-pham'),
            ],
        ];
    @endphp
    <div class="g-blog-detail">
        <section class="shopw-blog-detail-hero">
            <div class="shopw-blog-detail-copy">
                <span class="shopw-blog-kicker">
                    {{ $storefrontLabel::blogCategory(optional($post->category)->cate_post_name, optional($post->category)->cate_post_slug, 'Tin tức') }}
                </span>
                <h1>{{ $meta_title }}</h1>
                <div class="shopw-blog-detail-meta">
                    @if ($post->published_at)
                        <span><i class="fa fa-calendar-o"></i> {{ $post->published_at->format('d/m/Y') }}</span>
                    @endif
                    <span><i class="fa fa-eye"></i> {{ $post->post_views }} lượt xem</span>
                </div>
                <p>{{ $post->post_meta_desc ?: \Illuminate\Support\Str::limit(strip_tags((string) $post->post_desc), 180) }}</p>
            </div>
            <div class="shopw-blog-detail-share">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url_canonical) }}" target="_blank"
                    rel="noopener noreferrer">
                    <i class="fa fa-facebook"></i>
                    <span>Facebook</span>
                </a>
                <a href="https://zalo.me/share?url={{ urlencode($url_canonical) }}" target="_blank"
                    rel="noopener noreferrer">
                    <i class="fa fa-commenting"></i>
                    <span>Zalo</span>
                </a>
            </div>
        </section>

        <div class="shopw-content-support-strip">
            @foreach ($blogDetailQuickLinks as $quickLink)
                <a href="{{ $quickLink['url'] }}" class="shopw-content-support-item">
                    <span class="shopw-content-support-icon">
                        <i class="fa {{ $quickLink['icon'] }}"></i>
                    </span>
                    <span class="shopw-content-support-copy">
                        <strong>{{ $quickLink['label'] }}</strong>
                        <small>{{ $quickLink['value'] }}</small>
                    </span>
                    <span class="shopw-content-support-arrow">
                        <i class="fa fa-angle-right"></i>
                    </span>
                </a>
            @endforeach
        </div>

        <div class="shopw-blog-detail-layout">
            <article class="shopw-blog-detail-article">
                @if ($post->featured_image_path)
                    <div class="shopw-blog-detail-cover">
                        <img src="{{ asset($post->featured_image_path) }}" alt="{{ $post->post_title }}">
                    </div>
                @endif

                <div class="shopw-blog-detail-content">
                    {!! $post->post_content !!}
                </div>
            </article>

            <aside class="shopw-blog-detail-sidebar">
                <section class="shopw-blog-detail-sidecard">
                    <h2>Bài viết liên quan</h2>
                    <ul class="shopw-blog-related-list">
                        @forelse ($related as $post_relate)
                            <li>
                                <a href="{{ url('/bai-viet/' . $post_relate->post_slug) }}">
                                    <strong>{{ $post_relate->post_title }}</strong>
                                    <span>{{ \Illuminate\Support\Str::limit(strip_tags((string) $post_relate->post_desc), 90) }}</span>
                                </a>
                            </li>
                        @empty
                            <li class="is-empty">Chưa có bài viết liên quan.</li>
                        @endforelse
                    </ul>
                </section>
            </aside>
        </div>
    </div>
@endsection

