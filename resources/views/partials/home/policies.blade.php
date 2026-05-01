<section class="tech-section tech-policy-section">
    <div class="container">
        <div class="row tech-policy-grid">
            @foreach ($policyItems as $policy)
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <article class="tech-policy-card">
                        <span class="tech-policy-icon">
                            <i class="fa {{ $policy['icon'] }}"></i>
                        </span>
                        <div class="tech-policy-body">
                            <h3>{{ $policy['title'] }}</h3>
                            <p>{{ $policy['description'] }}</p>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
