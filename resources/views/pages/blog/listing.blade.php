@extends('layouts.home-tech')

@section('content')
    @php
        $storefrontLabel = \App\Support\StorefrontLabel::class;
        $blogCategories = collect($blog_categories ?? $category_post ?? []);
        $totalPublished = $total_published ?? $blogCategories->sum(fn($c) => $category_counts[$c->id] ?? 0);
        $sortCurrent = $sort_current ?? 'moi-nhat';
        $searchKeyword = $search_keyword ?? '';
        $featuredPosts = collect($featured_posts ?? []);
        $hotKeywords = $hot_keywords ?? [];

        $sortOptions = [
            'moi-nhat' => 'Mới nhất',
            'cu-nhat' => 'Cũ nhất',
            'xem-nhieu' => 'Xem nhiều',
            'noi-bat' => 'Nổi bật',
        ];

        $kickerLabel = function ($post) use ($selected_category) {
            $name = optional($post->category)->cate_post_name
                ?? optional($selected_category)->cate_post_name
                ?? 'Tin tức';
            return mb_strtoupper($name, 'UTF-8');
        };

        $formatViews = function ($value) {
            $value = (int) $value;
            if ($value >= 1000) {
                return number_format($value, 0, ',', '.');
            }
            return (string) $value;
        };

        $blogFallbackImages = [
            'tin-tuc-cong-nghe' => 'assets/news/anker-735-review-1-year.svg',
            'danh-gia-san-pham' => 'assets/news/anker-735-compare.svg',
            'huong-dan-su-dung' => 'assets/news/macbook-battery-tips.svg',
            'khuyen-mai' => 'assets/news/anker-735-compare.svg',
            'default' => 'assets/news/anker-735-review-1-year.svg',
        ];

        $blogImage = function ($post) use ($blogFallbackImages) {
            $imagePath = trim((string) ($post->featured_image_path ?? ''));

            if ($imagePath !== '' && ! str_starts_with($imagePath, 'http')) {
                $relativePath = ltrim($imagePath, '/');
                if (file_exists(public_path($relativePath))) {
                    return asset($relativePath);
                }
            }

            $categorySlug = optional($post->category)->cate_post_slug ?? 'default';
            return asset($blogFallbackImages[$categorySlug] ?? $blogFallbackImages['default']);
        };
    @endphp

    <div class="dd-blog-archive">
        <div class="dd-blog-shell">
            @include('partials.storefront.breadcrumb', [
                'class' => 'dd-blog-breadcrumb',
                'separatorClass' => 'dd-blog-breadcrumb-sep',
                'currentClass' => 'dd-blog-breadcrumb-current',
                'items' => [
                    [
                        'label' => 'Bài viết',
                        'current' => true,
                    ],
                ],
            ])

            <header class="dd-blog-archive-head">
                <h1>{{ mb_strtoupper($hero_title ?? 'Bài viết & Tin tức', 'UTF-8') }}</h1>
                <p>{{ $hero_desc ?? 'Cập nhật xu hướng công nghệ, hướng dẫn sử dụng và mẹo chọn mua sản phẩm phù hợp.' }}</p>
            </header>

            <div class="dd-blog-archive-grid">
                <main class="dd-blog-archive-main">
                    <div class="dd-blog-toolbar">
                        <div class="dd-blog-tabs" role="tablist">
                            <a href="{{ url('/bai-viet') . ($sortCurrent !== 'moi-nhat' ? '?sort=' . $sortCurrent : '') }}"
                                class="dd-blog-tab {{ ! $selected_category ? 'is-active' : '' }}">
                                Tất cả bài viết
                            </a>
                            @foreach ($blogCategories as $cat)
                                @php
                                    $catQuery = ['category' => $cat->cate_post_slug];
                                    if ($sortCurrent !== 'moi-nhat') {
                                        $catQuery['sort'] = $sortCurrent;
                                    }
                                    $catUrl = url('/bai-viet?' . http_build_query($catQuery));
                                    $isActive = $selected_category && (int) $selected_category->id === (int) $cat->id;
                                @endphp
                                <a href="{{ $catUrl }}" class="dd-blog-tab {{ $isActive ? 'is-active' : '' }}">
                                    {{ $cat->cate_post_name }}
                                </a>
                            @endforeach
                        </div>

                        <form method="GET" class="dd-blog-sort" data-dd-sort-form>
                            @if ($searchKeyword)
                                <input type="hidden" name="search" value="{{ $searchKeyword }}">
                            @endif
                            <div class="dd-blog-sort-control">
                                <select name="sort" data-dd-sort onchange="this.form.submit()">
                                    @foreach ($sortOptions as $value => $label)
                                        <option value="{{ $value }}" {{ $sortCurrent === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fa fa-angle-down"></i>
                            </div>
                        </form>
                    </div>

                    @if ($post_cate->count())
                        @include('pages.blog.partials.article-grid')
                        @include('pages.blog.partials.pagination')
                    @else
                        <div class="dd-blog-empty">
                            <i class="fa fa-file-text-o"></i>
                            <h2>Chưa có bài viết phù hợp</h2>
                            <p>Hãy thử bỏ bộ lọc, đổi từ khóa tìm kiếm hoặc quay lại sau để xem thêm nội dung mới.</p>
                            <a href="{{ url('/bai-viet') }}" class="dd-blog-empty-cta">Quay lại tất cả bài viết</a>
                        </div>
                    @endif
                </main>

                <aside class="dd-blog-sidebar">
                    <section class="dd-blog-side-card dd-blog-side-search">
                        <h3>TÌM KIẾM BÀI VIẾT</h3>
                        <form action="{{ url('/bai-viet') }}" method="GET" class="dd-blog-side-search-form">
                            @if ($sortCurrent !== 'moi-nhat')
                                <input type="hidden" name="sort" value="{{ $sortCurrent }}">
                            @endif
                            <input type="text" name="search" value="{{ $searchKeyword }}" placeholder="Nhập từ khóa...">
                            <button type="submit" aria-label="Tìm kiếm">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>
                    </section>

                    <section class="dd-blog-side-card dd-blog-side-cats">
                        <h3>DANH MỤC BÀI VIẾT</h3>
                        <ul>
                            <li>
                                <a href="{{ url('/bai-viet') }}" class="{{ ! $selected_category ? 'is-active' : '' }}">
                                    <span>Tất cả bài viết</span>
                                    <span class="dd-blog-side-count">({{ $totalPublished }})</span>
                                </a>
                            </li>
                            @foreach ($blogCategories as $cat)
                                @php
                                    $count = (int) ($category_counts[$cat->id] ?? 0);
                                    $isActive = $selected_category && (int) $selected_category->id === (int) $cat->id;
                                @endphp
                                <li>
                                    <a href="{{ url('/bai-viet?' . http_build_query(['category' => $cat->cate_post_slug])) }}"
                                        class="{{ $isActive ? 'is-active' : '' }}">
                                        <span>{{ $cat->cate_post_name }}</span>
                                        <span class="dd-blog-side-count">({{ $count }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>

                    @if ($featuredPosts->isNotEmpty())
                        <section class="dd-blog-side-card dd-blog-side-featured">
                            <h3>BÀI VIẾT NỔI BẬT</h3>
                            <ul>
                                @foreach ($featuredPosts as $featured)
                                    @php
                                        $fUrl = url('/bai-viet/' . $featured->post_slug);
                                        $fImg = $blogImage($featured);
                                    @endphp
                                    <li>
                                        <a href="{{ $fUrl }}" class="dd-blog-side-featured-item">
                                            <span class="dd-blog-side-featured-thumb">
                                                <img src="{{ $fImg }}" alt="{{ $featured->post_title }}" loading="lazy" decoding="async">
                                            </span>
                                            <span class="dd-blog-side-featured-copy">
                                                <strong>{{ \Illuminate\Support\Str::limit($featured->post_title, 60) }}</strong>
                                                @if ($featured->published_at)
                                                    <small>{{ $featured->published_at->format('d/m/Y') }}</small>
                                                @endif
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if (! empty($hotKeywords))
                        <section class="dd-blog-side-card dd-blog-side-tags">
                            <h3>TỪ KHÓA NỔI BẬT</h3>
                            <div class="dd-blog-side-tags-wrap">
                                @foreach ($hotKeywords as $kw)
                                    <a href="{{ $kw['url'] }}" class="dd-blog-tag-pill">{{ $kw['label'] }}</a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @include('partials.blog.newsletter-card', [
                        'cardClass' => 'dd-blog-side-card dd-blog-side-newsletter',
                        'formClass' => 'dd-blog-side-newsletter-form',
                        'noteClass' => 'dd-blog-side-newsletter-note',
                        'onsubmit' => "event.preventDefault(); this.querySelector('input').value=''; alert('Cảm ơn bạn đã đăng ký nhận tin!');",
                    ])
                </aside>
            </div>
        </div>
    </div>
@endsection
