<div class="dd-blog-list">
    @foreach ($post_cate as $post)
        @php
            $postUrl = url('/bai-viet/' . $post->post_slug);
            $imgSrc = $blogImage($post);
            $excerpt = \Illuminate\Support\Str::limit(strip_tags((string) $post->post_desc), 140);
        @endphp
        <article class="dd-blog-card">
            <a class="dd-blog-card-media" href="{{ $postUrl }}">
                <img src="{{ $imgSrc }}" alt="{{ $post->post_title }}" loading="lazy" decoding="async">
            </a>
            <div class="dd-blog-card-body">
                <span class="dd-blog-card-kicker">{{ $kickerLabel($post) }}</span>
                <h2 class="dd-blog-card-title">
                    <a href="{{ $postUrl }}">{{ $post->post_title }}</a>
                </h2>
                <p class="dd-blog-card-excerpt">{{ $excerpt }}</p>
                <div class="dd-blog-card-foot">
                    <div class="dd-blog-card-meta">
                        @if ($post->published_at)
                            <span><i class="fa fa-calendar-o"></i> {{ $post->published_at->format('d/m/Y') }}</span>
                        @endif
                        <span><i class="fa fa-eye"></i> {{ $formatViews($post->post_views) }} lượt xem</span>
                    </div>
                    <a class="dd-blog-card-link" href="{{ $postUrl }}">
                        Xem chi tiết <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </article>
    @endforeach
</div>
