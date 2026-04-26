@extends('layout')

@section('content')
    @php
        $storefrontLabel = \App\Support\StorefrontLabel::class;
        $supportConfig = config('services.storefront_support', []);
        $supportPhoneDisplay = $supportConfig['phone_display'] ?? $supportConfig['contact_phone_display'] ?? '1900.0000';
        $supportPhoneHref = $supportConfig['phone_href'] ?? 'tel:' . ($supportConfig['contact_phone_href'] ?? '19000000');
        $blogCategoryItems = collect($category_post ?? [])->filter(fn($item) => ! empty($item->cate_post_slug))->values();
        $featuredBlogCategory = $blogCategoryItems->firstWhere('cate_post_slug', 'tin-tuc-cong-nghe')
            ?? $blogCategoryItems->firstWhere('cate_post_slug', 'tin-tuc')
            ?? $blogCategoryItems->first();
        $featuredBlogUrl = $featuredBlogCategory
            ? url('/danh-muc-bai-viet/' . $featuredBlogCategory->cate_post_slug)
            : url('/video-shop');
        $featuredBlogLabel = $storefrontLabel::blogCategory(
            $featuredBlogCategory?->cate_post_name,
            $featuredBlogCategory?->cate_post_slug,
            'Tin công nghệ',
        );
        $videoQuickLinks = [
            [
                'icon' => 'fa-newspaper-o',
                'label' => $featuredBlogLabel,
                'value' => config('storefront.copy.video_blog_value', 'Theo dõi thêm các bài đánh giá, tư vấn và hướng dẫn mới'),
                'url' => $featuredBlogUrl,
            ],
            [
                'icon' => 'fa-phone',
                'label' => 'Tư vấn sản phẩm',
                'value' => $supportPhoneDisplay,
                'url' => $supportPhoneHref,
            ],
            [
                'icon' => 'fa-lightbulb-o',
                'label' => config('storefront.labels.recommend_for_you', 'Gợi ý cho bạn'),
                'value' => config('storefront.copy.video_recommend_value', 'Khám phá thêm sản phẩm phù hợp'),
                'url' => url('/goi-y-san-pham'),
            ],
        ];
    @endphp
    <div class="g-video-page">
        <section class="shopw-video-hero">
            <div class="shopw-video-hero-copy">
                <span class="shopw-video-kicker">Video / đánh giá / hướng dẫn</span>
                <h1>Video cửa hàng</h1>
                <p>Xem video demo sản phẩm, clip hướng dẫn sử dụng và các nội dung đánh giá nhanh trước hoặc sau khi mua hàng.</p>
            </div>
            <div class="shopw-video-hero-metrics">
                <strong>{{ method_exists($all_video, 'total') ? $all_video->total() : count($all_video) }}</strong>
                <span>video hiện có</span>
            </div>
        </section>

        <div class="shopw-content-support-strip">
            @foreach ($videoQuickLinks as $quickLink)
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

        @if ($all_video->count())
            <div class="shopw-video-grid">
                @foreach ($all_video as $video)
                    @php
                        $videoId = $video->video_id ?? $video->id;
                        $videoTitle = $video->video_title ?? $video->title;
                        $videoDesc = $video->video_desc ?? $video->description;
                        $thumbnailPath = $video->thumbnail_path ?? null;
                        $legacyImage = $video->video_image ?? null;

                        if ($thumbnailPath && file_exists(public_path($thumbnailPath))) {
                            $thumbnailUrl = \App\Support\AssetUrl::resolve($thumbnailPath, \App\Support\AssetUrl::PRODUCT_FALLBACK);
                        } elseif ($legacyImage && file_exists(public_path('uploads/videos/' . $legacyImage))) {
                            $thumbnailUrl = \App\Support\AssetUrl::resolve('uploads/videos/' . $legacyImage, \App\Support\AssetUrl::PRODUCT_FALLBACK);
                        } else {
                            $thumbnailUrl = \App\Support\AssetUrl::product();
                        }
                    @endphp

                    <article class="shopw-video-card">
                        <div class="shopw-video-card-media">
                            <img src="{{ $thumbnailUrl }}" alt="{{ $videoTitle }}">
                            <button type="button" class="shopw-video-play watch-video" data-toggle="modal"
                                data-target="#modal_video" id="{{ $videoId }}">
                                <i class="fa fa-play"></i>
                            </button>
                        </div>
                        <div class="shopw-video-card-copy">
                            <h2>{{ $videoTitle }}</h2>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags((string) $videoDesc), 140) }}</p>
                            <button type="button" class="shopw-video-cta watch-video" data-toggle="modal"
                                data-target="#modal_video" id="{{ $videoId }}">
                                Xem video
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="shopw-video-pagination">
                {!! $all_video->links() !!}
            </div>
        @else
            <section class="shopw-video-empty">
                <h2>Chưa có video nào</h2>
                <p>Khi có clip review hoặc hướng dẫn mới, danh sách video sẽ xuất hiện tại đây.</p>
            </section>
        @endif

        <div class="modal fade" id="modal_video" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="video_title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div id="video_desc" class="shopw-video-modal-desc"></div>
                        <div id="video_link" class="shopw-video-modal-frame"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="close_video" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

