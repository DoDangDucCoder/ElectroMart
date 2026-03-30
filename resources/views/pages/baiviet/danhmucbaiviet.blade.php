@extends('layout')

@section('content')
    @php
        $storefrontLabel = \App\Support\StorefrontLabel::class;
        $blogQuickLinks = [
            [
                'icon' => 'fa-home',
                'label' => 'Về trang chủ',
                'value' => 'Quay lại trang chủ để tiếp tục xem sản phẩm',
                'url' => url('/'),
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
                'value' => 'Tiếp tục mua sắm với gợi ý phù hợp',
                'url' => url('/goi-y-san-pham'),
            ],
        ];
    @endphp
    <div class="g-blog-list">
        <section class="shopw-blog-list-hero">
            <div class="shopw-blog-list-hero-copy">
                <span class="shopw-blog-kicker">Blog / {{ $storefrontLabel::blogCategory($selected_category->cate_post_name ?? null, $selected_category->cate_post_slug ?? null, $meta_title) }}</span>
                <h1>{{ $storefrontLabel::blogCategory($selected_category->cate_post_name ?? null, $selected_category->cate_post_slug ?? null, $meta_title) }}</h1>
                <p>{{ $selected_category->cate_post_desc ?: 'Tổng hợp bài viết, tin nhanh và hướng dẫn liên quan đến nhóm nội dung này.' }}</p>
            </div>
            <div class="shopw-blog-list-hero-meta">
                <strong>{{ method_exists($post_cate, 'total') ? $post_cate->total() : count($post_cate) }}</strong>
                <span>bài viết đang hiển thị</span>
            </div>
        </section>

        <div class="shopw-content-support-strip">
            @foreach ($blogQuickLinks as $quickLink)
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

        <section class="shopw-blog-share-row">
            <a class="shopw-blog-share-link" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url_canonical) }}"
                target="_blank" rel="noopener noreferrer">
                <i class="fa fa-facebook"></i>
                <span>Chia sẻ Facebook</span>
            </a>
            <a class="shopw-blog-share-link" href="https://zalo.me/share?url={{ urlencode($url_canonical) }}" target="_blank"
                rel="noopener noreferrer">
                <i class="fa fa-commenting"></i>
                <span>Chia sẻ Zalo</span>
            </a>
        </section>

        @if ($post_cate->count())
            <div class="shopw-blog-list-grid">
                @foreach ($post_cate as $post)
                    <article class="shopw-blog-card">
                        <a class="shopw-blog-card-media" href="{{ url('/bai-viet/' . $post->post_slug) }}">
                            <img src="{{ $post->featured_image_path ? asset($post->featured_image_path) : asset('uploads/product/no-image.svg') }}"
                                alt="{{ $post->post_title }}">
                        </a>
                        <div class="shopw-blog-card-copy">
                            <span class="shopw-blog-card-chip">{{ $storefrontLabel::blogCategory($selected_category->cate_post_name ?? null, $selected_category->cate_post_slug ?? null, 'Tin tức') }}</span>
                            <h2>
                                <a href="{{ url('/bai-viet/' . $post->post_slug) }}">{{ $post->post_title }}</a>
                            </h2>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags((string) $post->post_desc), 165) }}</p>
                            <div class="shopw-blog-card-actions">
                                <a href="{{ url('/bai-viet/' . $post->post_slug) }}" class="shopw-blog-card-link">
                                    Đọc bài viết
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="shopw-blog-pagination">
                {{ $post_cate->appends(request()->query())->links() }}
            </div>
        @else
            <section class="shopw-blog-empty">
                <h2>Danh mục này chưa có bài viết</h2>
                <p>Nội dung mới trong danh mục này đang được cập nhật. Vui lòng quay lại sau để xem thêm bài viết.</p>
            </section>
        @endif
    </div>
@endsection

