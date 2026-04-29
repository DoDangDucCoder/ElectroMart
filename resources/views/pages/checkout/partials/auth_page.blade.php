<section id="form" class="shopw-auth-page">
    @if (session()->has('message'))
        <div class="alert alert-success">
            {!! session()->get('message') !!}
        </div>
    @elseif(session()->has('error'))
        <div class="alert alert-danger">
            {!! session()->get('error') !!}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="list-unstyled" style="margin: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
