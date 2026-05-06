@extends('layouts.home-tech')

@section('content')
    @php
        use Illuminate\Support\Str;

        $storefrontLabel = \App\Support\StorefrontLabel::class;
        $defaultPostCover = asset('frontend/images/share_news.png');
        $defaultAuthorAvatar = asset('frontend/images/storefront-account-avatar.svg');
        $defaultProductImage = asset('frontend/images/share_news.png');

        $postCategoryName = optional($post->category)->cate_post_name;
        $postCategorySlug = optional($post->category)->cate_post_slug;
        $postCategoryUrl = $postCategorySlug
            ? url('/danh-muc-bai-viet/' . $postCategorySlug)
            : url('/');

        $postCover = $post->featured_image_path
            ? asset($post->featured_image_path)
            : $defaultPostCover;
        $postPlainText = trim(strip_tags((string) $post->post_content));
        $postWordCount = $postPlainText !== '' ? str_word_count($postPlainText) : 0;
        $postReadTime = max(1, (int) ceil($postWordCount / 220));
        $postViews = number_format((int) $post->post_views, 0, ',', '.');
        $postExcerpt = $post->post_meta_desc
            ?: ($post->post_desc ?: Str::limit($postPlainText, 220));

        $postKeywords = $post->post_meta_keywords;
        $postTags = collect();
        if (! empty($postKeywords)) {
            $postTags = collect(preg_split('/[,;\\|]+/', $postKeywords))
                ->map(fn ($tag) => trim((string) $tag))
                ->filter()
                ->take(6)
                ->values();
        }
        if ($postTags->isEmpty() && $postCategoryName) {
            $postTags = collect([$postCategoryName, 'Công nghệ', 'Hướng dẫn'])->unique()->values();
        }

        $authorName = 'Đăng Đức';
        $authorRole = 'Tác giả';
        $authorBio = 'Biên tập viên công nghệ tại DangDuc. Đam mê chia sẻ kiến thức về phần cứng, laptop và các xu hướng công nghệ mới nhất.';

        $blogCategoryItems = collect($category_post ?? [])
            ->filter(fn ($item) => ! empty($item->cate_post_slug))
            ->values();
        $blogCategoryCounts = $blog_category_counts ?? collect();
        $totalPublishedPosts = $total_published_posts ?? 0;

        $featuredPosts = collect($featured_posts ?? []);
        $suggestedProducts = collect($suggested_products ?? []);
        $relatedPosts = collect($related ?? [])->take(3);

        $shareUrl = urlencode($url_canonical);
    @endphp

    <link href="{{ asset('frontend/css/blog-detail.css') }}" rel="stylesheet">

    <main class="bd-page">
        <div class="bd-container">
            @include('partials.storefront.breadcrumb', [
                'class' => 'bd-breadcrumb',
                'separator' => '›',
                'items' => array_filter([
                    [
                        'label' => 'Bài viết',
                        'url' => url('/bai-viet'),
                    ],
                    $postCategoryName ? [
                        'label' => $postCategoryName,
                        'url' => $postCategoryUrl,
                    ] : null,
                    [
                        'label' => Str::limit($post->post_title, 60),
                        'current' => true,
                    ],
                ]),
            ])

            <div class="bd-layout">
                <article class="bd-article">
                    <header class="bd-article-head">
                        @if ($postCategoryName)
                            <a class="bd-kicker" href="{{ $postCategoryUrl }}">
                                {{ Str::upper($postCategoryName) }}
                            </a>
                        @endif

                        <h1 class="bd-title">{{ $post->post_title }}</h1>

                        <div class="bd-meta">
                            @if ($post->published_at)
                                <span class="bd-meta-item">
                                    <i class="fa fa-calendar-o" aria-hidden="true"></i>
                                    {{ $post->published_at->format('d/m/Y') }}
                                </span>
                            @endif
                            <span class="bd-meta-item">
                                <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                                {{ $authorName }}
                            </span>
                            <span class="bd-meta-item">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                                {{ $postViews }} lượt xem
                            </span>
                            <span class="bd-meta-item">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                {{ $postReadTime }} phút đọc
                            </span>
                        </div>

                        @if ($postExcerpt)
                            <p class="bd-excerpt">{{ $postExcerpt }}</p>
                        @endif
                    </header>

                    <figure class="bd-cover">
                        <img src="{{ $postCover }}" alt="{{ $post->post_title }}">
                    </figure>

                    <div class="bd-content">
                        {!! $post->post_content !!}
                    </div>

                    @if ($postTags->isNotEmpty())
                        <div class="bd-tags">
                            <span class="bd-tags-label">Tags:</span>
                            <ul class="bd-tags-list">
                                @foreach ($postTags as $tag)
                                    <li>
                                        <a href="{{ url('/tag/' . Str::slug($tag)) }}">{{ $tag }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="bd-share">
                        <span class="bd-share-label">Chia sẻ:</span>
                        <ul class="bd-share-list">
                            <li>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="bd-share-btn bd-share-fb" aria-label="Chia sẻ Facebook">
                                    <i class="fa fa-facebook" aria-hidden="true"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.facebook.com/dialog/send?link={{ $shareUrl }}&app_id=0&redirect_uri={{ $shareUrl }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="bd-share-btn bd-share-mes" aria-label="Chia sẻ Messenger">
                                    <i class="fa fa-comments" aria-hidden="true"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://zalo.me/share?url={{ $shareUrl }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="bd-share-btn bd-share-zalo" aria-label="Chia sẻ Zalo">
                                    <i class="fa fa-commenting" aria-hidden="true"></i>
                                </a>
                            </li>
                            <li>
                                <button type="button" class="bd-share-btn bd-share-copy"
                                    data-bd-copy="{{ $url_canonical }}"
                                    aria-label="Sao chép liên kết">
                                    <i class="fa fa-link" aria-hidden="true"></i>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <section class="bd-author">
                        <img src="{{ $defaultAuthorAvatar }}" alt="{{ $authorName }}" class="bd-author-avatar">
                        <div class="bd-author-body">
                            <div class="bd-author-head">
                                <strong class="bd-author-name">{{ $authorName }}</strong>
                                <span class="bd-author-role">{{ $authorRole }}</span>
                            </div>
                            <p class="bd-author-bio">{{ $authorBio }}</p>
                        </div>
                        <a href="{{ url('/danh-muc-bai-viet') }}" class="bd-author-cta">Xem tất cả bài viết</a>
                    </section>

                    @if ($relatedPosts->isNotEmpty())
                        <section class="bd-related">
                            <header class="bd-related-head">
                                <h2>Bài viết liên quan</h2>
                                <a href="{{ url('/danh-muc-bai-viet') }}" class="bd-related-all">
                                    Xem tất cả <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                </a>
                            </header>
                            <div class="bd-related-grid">
                                @foreach ($relatedPosts as $relatedPost)
                                    @php
                                        $relCover = $relatedPost->featured_image_path
                                            ? asset($relatedPost->featured_image_path)
                                            : $defaultPostCover;
                                    @endphp
                                    <a href="{{ url('/bai-viet/' . $relatedPost->post_slug) }}" class="bd-related-card">
                                        <span class="bd-related-cover">
                                            <img src="{{ $relCover }}" alt="{{ $relatedPost->post_title }}">
                                        </span>
                                        <span class="bd-related-body">
                                            <strong class="bd-related-title">{{ $relatedPost->post_title }}</strong>
                                            <span class="bd-related-meta">
                                                @if ($relatedPost->published_at)
                                                    <span>{{ $relatedPost->published_at->format('d/m/Y') }}</span>
                                                    <span class="bd-dot" aria-hidden="true"></span>
                                                @endif
                                                <span>{{ number_format((int) $relatedPost->post_views, 0, ',', '.') }} lượt xem</span>
                                            </span>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <section class="bd-comments">
                        <header class="bd-comments-head">
                            <h2>Bình luận <span class="bd-comments-count">(2)</span></h2>
                            <button type="button" class="bd-comments-cta">Gửi bình luận</button>
                        </header>

                        <ul class="bd-comments-list">
                            <li class="bd-comment">
                                <img src="{{ $defaultAuthorAvatar }}" alt="Minh Anh" class="bd-comment-avatar">
                                <div class="bd-comment-body">
                                    <div class="bd-comment-head">
                                        <strong>Minh Anh</strong>
                                        <span class="bd-comment-time">2 ngày trước</span>
                                    </div>
                                    <p class="bd-comment-text">Bài viết rất hữu ích, mình đang phân vân giữa i5 và Ryzen 5 thì thấy phần bảng so sánh rất dễ hiểu.</p>
                                    <div class="bd-comment-actions">
                                        <button type="button"><i class="fa fa-reply" aria-hidden="true"></i> Trả lời</button>
                                        <button type="button"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> 3</button>
                                    </div>
                                </div>
                                <button type="button" class="bd-comment-emoji" aria-label="Cảm xúc">
                                    <i class="fa fa-smile-o" aria-hidden="true"></i>
                                </button>
                            </li>
                            <li class="bd-comment">
                                <img src="{{ $defaultAuthorAvatar }}" alt="Hoàng Nam" class="bd-comment-avatar">
                                <div class="bd-comment-body">
                                    <div class="bd-comment-head">
                                        <strong>Hoàng Nam</strong>
                                        <span class="bd-comment-time">1 ngày trước</span>
                                    </div>
                                    <p class="bd-comment-text">Ngoài thiết kế đồ họa 2D thì có cần card rời không ạ?</p>
                                    <div class="bd-comment-actions">
                                        <button type="button"><i class="fa fa-reply" aria-hidden="true"></i> Trả lời</button>
                                        <button type="button"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> 1</button>
                                    </div>
                                </div>
                                <button type="button" class="bd-comment-more" aria-label="Tuỳ chọn">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </button>
                            </li>
                        </ul>
                    </section>
                </article>

                <aside class="bd-sidebar">
                    <section class="bd-side-card bd-side-search">
                        <h3>Tìm kiếm bài viết</h3>
                        <form action="{{ url('/tim-kiem') }}" method="GET" class="bd-side-search-form">
                            <input type="text" name="keywords_submit" placeholder="Nhập từ khoá..." aria-label="Từ khoá tìm kiếm">
                            <button type="submit" aria-label="Tìm kiếm">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </button>
                        </form>
                    </section>

                    <section class="bd-side-card bd-side-cats">
                        <h3>Danh mục bài viết</h3>
                        <ul class="bd-side-cat-list">
                            <li>
                                <a href="{{ url('/danh-muc-bai-viet') }}" class="is-active">
                                    <span>Tất cả bài viết</span>
                                    <span class="bd-side-cat-count">({{ $totalPublishedPosts }})</span>
                                </a>
                            </li>
                            @foreach ($blogCategoryItems as $cat)
                                @php
                                    $count = (int) ($blogCategoryCounts[$cat->id ?? $cat->cate_post_id] ?? 0);
                                    $isActive = (int) (optional($post->category)->id) === (int) ($cat->id ?? $cat->cate_post_id);
                                @endphp
                                <li>
                                    <a href="{{ url('/danh-muc-bai-viet/' . $cat->cate_post_slug) }}"
                                        class="{{ $isActive ? 'is-active' : '' }}">
                                        <span>{{ $storefrontLabel::blogCategory($cat->cate_post_name, $cat->cate_post_slug) }}</span>
                                        <span class="bd-side-cat-count">({{ $count }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>

                    @if ($featuredPosts->isNotEmpty())
                        <section class="bd-side-card bd-side-featured">
                            <h3>Bài viết nổi bật</h3>
                            <ul class="bd-side-featured-list">
                                @foreach ($featuredPosts as $featured)
                                    @php
                                        $featCover = $featured->featured_image_path
                                            ? asset($featured->featured_image_path)
                                            : $defaultPostCover;
                                    @endphp
                                    <li>
                                        <a href="{{ url('/bai-viet/' . $featured->post_slug) }}">
                                            <span class="bd-side-feat-cover">
                                                <img src="{{ $featCover }}" alt="{{ $featured->post_title }}">
                                            </span>
                                            <span class="bd-side-feat-body">
                                                <strong>{{ Str::limit($featured->post_title, 56) }}</strong>
                                                <span class="bd-side-feat-date">
                                                    @if ($featured->published_at)
                                                        {{ $featured->published_at->format('d/m/Y') }}
                                                    @endif
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if ($suggestedProducts->isNotEmpty())
                        <section class="bd-side-card bd-side-products">
                            <h3>Sản phẩm gợi ý</h3>
                            <ul class="bd-side-product-list">
                                @foreach ($suggestedProducts as $sugProduct)
                                    @php
                                        $productImage = $sugProduct->product_image
                                            ? asset('uploads/product/' . $sugProduct->product_image)
                                            : $defaultProductImage;
                                        $price = (float) ($sugProduct->final_price ?? $sugProduct->product_price ?? 0);
                                        $originalPrice = (float) ($sugProduct->price ?? 0);
                                        $discount = 0;
                                        if ($originalPrice > 0 && $price > 0 && $price < $originalPrice) {
                                            $discount = (int) round(100 - ($price / $originalPrice * 100));
                                        }
                                    @endphp
                                    <li>
                                        <a href="{{ url('/chi-tiet/' . ($sugProduct->product_slug ?? $sugProduct->slug)) }}">
                                            @if ($discount > 0)
                                                <span class="bd-side-prod-badge">-{{ $discount }}%</span>
                                            @endif
                                            <span class="bd-side-prod-image">
                                                <img src="{{ $productImage }}" alt="{{ $sugProduct->product_name }}">
                                            </span>
                                            <span class="bd-side-prod-body">
                                                <strong>{{ Str::limit($sugProduct->product_name, 36) }}</strong>
                                                @if ($originalPrice > $price && $originalPrice > 0)
                                                    <span class="bd-side-prod-old">{{ number_format($originalPrice, 0, ',', '.') }}đ</span>
                                                @endif
                                                <span class="bd-side-prod-now">{{ number_format($price, 0, ',', '.') }}đ</span>
                                                <span class="bd-side-prod-rating">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star-half-o" aria-hidden="true"></i>
                                                    <span>(128)</span>
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ url('/san-pham') }}" class="bd-side-prod-all">
                                Xem tất cả sản phẩm <i class="fa fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </section>
                    @endif

                    <section class="bd-side-card bd-side-newsletter">
                        <h3>Đăng ký nhận tin</h3>
                        <p>Nhận ngay thông tin khuyến mãi và bài viết mới nhất từ DangDuc.</p>
                        <form action="#" method="POST" class="bd-side-newsletter-form">
                            @csrf
                            <input type="email" name="email" placeholder="Nhập email của bạn" required>
                            <button type="submit" aria-label="Đăng ký">
                                <i class="fa fa-paper-plane" aria-hidden="true"></i>
                            </button>
                        </form>
                        <p class="bd-side-newsletter-note">
                            <i class="fa fa-shield" aria-hidden="true"></i>
                            Chúng tôi cam kết không gửi thư rác và bảo mật thông tin của bạn.
                        </p>
                    </section>
                </aside>
            </div>
        </div>
    </main>

    <script>
        (function () {
            document.querySelectorAll('[data-bd-copy]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var url = btn.getAttribute('data-bd-copy') || window.location.href;
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(url).then(function () {
                            btn.classList.add('is-copied');
                            setTimeout(function () { btn.classList.remove('is-copied'); }, 1600);
                        });
                    } else {
                        var input = document.createElement('input');
                        input.value = url;
                        document.body.appendChild(input);
                        input.select();
                        document.execCommand('copy');
                        document.body.removeChild(input);
                        btn.classList.add('is-copied');
                        setTimeout(function () { btn.classList.remove('is-copied'); }, 1600);
                    }
                });
            });
        })();
    </script>
@endsection
