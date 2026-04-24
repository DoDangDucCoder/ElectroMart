<section class="tech-section" id="tech-news">
    <div class="container">
        <div class="tech-section-head">
            <div>
                <span class="tech-eyebrow">Tin tức nổi bật</span>
                <h2>Cập nhật công nghệ mới nhất</h2>
            </div>
            <a href="{{ url('/video-shop') }}" class="tech-section-link">Xem thêm</a>
        </div>

        <div class="row tech-news-grid">
            @foreach ($newsItems as $news)
                <div class="col-xs-12 col-md-4">
                    <article class="tech-news-card">
                        <a href="{{ $news['url'] }}" class="tech-news-thumb">
                            @if (!empty($news['thumbnail']))
                                <img src="{{ $news['thumbnail'] }}" alt="{{ $news['title'] }}">
                            @endif
                        </a>
                        <div class="tech-news-body">
                            <time datetime="{{ optional($news['published_at'])->toDateString() }}">
                                {{ optional($news['published_at'])->format('d/m/Y') }}
                            </time>
                            <h3><a href="{{ $news['url'] }}">{{ $news['title'] }}</a></h3>
                            <p>{{ $news['summary'] }}</p>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
