<section id="form" class="shopw-auth-page">
    @include('pages.checkout.partials.auth_messages')

    @if ($currentCustomer)
        @include('pages.checkout.partials.auth_logged_in_state')
    @else
        <div class="shopw-auth-layout shopw-auth-layout--single">
            <div class="shopw-auth-card">
                <div class="shopw-auth-card-head">
                    <h2>{{ $authPageHeading }}</h2>
                    <p>{{ $authPageDescription }}</p>
                </div>

                @if ($authPageMode === 'register')
                    @include('pages.checkout.partials.auth_register_form')
                @else
                    @include('pages.checkout.partials.auth_login_form')
                @endif
            </div>
        </div>
    @endif
</section>
