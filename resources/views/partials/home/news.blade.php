<section class="tech-section tech-news-section" id="tech-news">
    <div class="container">
        <div class="tech-section-head">
            <div>
                <span class="tech-eyebrow"><i class="fa fa-newspaper-o"></i> TIN TỨC MỚI</span>
                <h2>Tin tức &amp; Hướng dẫn</h2>
            </div>
            <a href="{{ url('/bai-viet') }}" class="tech-section-link">Xem tất cả bài viết <i class="fa fa-angle-right"></i></a>
        </div>

        <div class="row tech-news-grid">
            @php $newsFallback = asset('assets/news/anker-735-review-1-year.svg'); @endphp
            @foreach ($newsItems as $news)
                <div class="col-xs-12 col-md-4">
                    <article class="tech-news-card">
                        <a href="{{ $news['url'] }}" class="tech-news-thumb">
                            <img
                                src="{{ !empty($news['thumbnail']) ? $news['thumbnail'] : $newsFallback }}"
                                alt="{{ $news['title'] }}"
                                loading="lazy"
                                decoding="async"
                                onerror="this.onerror=null;this.src='{{ $newsFallback }}';"
                            >
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
