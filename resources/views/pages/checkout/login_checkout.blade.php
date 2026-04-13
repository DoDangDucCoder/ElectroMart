@extends('layout')

@section('body_class', 'g-login-checkout')

@section('content')
    @php
        $cartCount = $cartItems->sum('product_qty');
        $checkoutUrl = $authRedirectUrl ?? url('/checkout');
        $supportConfig = config('services.storefront_support', []);
        $supportPhoneDisplay = $supportConfig['phone_display'] ?? '1900.0000';
        $supportPhoneHref = $supportConfig['phone_href'] ?? 'tel:19000000';
        $authFlowSteps = [
            ['label' => 'Giỏ hàng', 'state' => 'is-complete'],
            ['label' => 'Đăng nhập', 'state' => 'is-active'],
            ['label' => 'Thanh toán', 'state' => ''],
        ];
        $authSupportItems = [
            [
                'icon' => 'fa-lock',
                'title' => 'Đăng nhập an toàn',
                'text' => config('storefront.copy.login_continue_help', 'Dùng tài khoản cũ để tiếp tục thanh toán, theo dõi lịch sử mua và lưu thông tin nhận hàng.'),
            ],
            [
                'icon' => 'fa-user-plus',
                'title' => 'Tạo tài khoản nhanh',
                'text' => config('storefront.copy.login_register_help', 'Nếu chưa có tài khoản, bạn có thể đăng ký ngay và tiếp tục đơn hàng trong cùng một bước.'),
            ],
            [
                'icon' => 'fa-phone',
                'title' => 'Cần hỗ trợ ngay?',
                'text' => 'Liên hệ ' . $supportPhoneDisplay . ' nếu cần tư vấn trước khi chốt đơn.',
            ],
        ];
        $authQuickLinks = [
            [
                'icon' => 'fa-phone',
                'label' => 'Hotline',
                'value' => $supportPhoneDisplay,
                'url' => $supportPhoneHref,
            ],
            [
                'icon' => 'fa-history',
                'label' => 'Lịch sử mua',
                'value' => 'Xem lại các đơn đã đặt',
                'url' => url('/history'),
            ],
            [
                'icon' => 'fa-lightbulb-o',
                'label' => 'Gợi ý sản phẩm',
                'value' => 'Khám phá thêm sản phẩm phù hợp',
                'url' => url('/goi-y-san-pham'),
            ],
        ];
        $authStatusLabel = $currentCustomer ? 'Đã đăng nhập' : 'Chưa đăng nhập';
        $authHeroTitle = $currentCustomer
            ? 'Bạn đã đăng nhập thành công'
            : (($authRedirectKey ?? 'checkout') === 'history'
                ? 'Đăng nhập để xem đơn hàng của bạn'
                : 'Đăng nhập để tiếp tục thanh toán');
        $authHeroDescription = $currentCustomer
            ? 'Tài khoản của bạn đã sẵn sàng. Bạn có thể tiếp tục sang bước ' . ($authRedirectLabel ?? 'checkout') . ' ngay bây giờ hoặc đổi sang tài khoản khác.'
            : (($authRedirectKey ?? 'checkout') === 'history'
                ? 'Đăng nhập để theo dõi lịch sử mua sắm, trạng thái giao hàng và các đơn đã đặt trước đó.'
                : 'Trang này là bước nối giữa giỏ hàng và thanh toán. Bạn có thể đăng nhập tài khoản hiện có hoặc tạo tài khoản mới để xác nhận đơn hàng.');
        $authCustomerDisplayName = $currentCustomer?->name ?: ($currentCustomer?->email ?: 'Tài khoản của bạn');
        $authCustomerEmail = $currentCustomer?->email;
        $authCustomerPhone = $currentCustomer?->phone;
        $authAvatarInitials = collect(explode(' ', \Illuminate\Support\Str::upper(trim((string) $authCustomerDisplayName))))
            ->filter()
            ->take(2)
            ->map(fn($part) => \Illuminate\Support\Str::substr($part, 0, 1))
            ->implode('');
        $authAvatarInitials = $authAvatarInitials !== '' ? $authAvatarInitials : 'TK';
    @endphp

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

        <div class="shopw-auth-hero">
            <div class="shopw-auth-hero-copy">
                <span class="shopw-auth-kicker">Truy cập tài khoản</span>
                <h1>{{ $authHeroTitle }}</h1>
                <p>{{ $authHeroDescription }}</p>

                <div class="shopw-auth-flow" aria-label="Tiến trình mua hàng">
                    @foreach ($authFlowSteps as $step)
                        <span class="shopw-auth-flow-step {{ $step['state'] }}">{{ $step['label'] }}</span>
                    @endforeach
                </div>
            </div>

            <div class="shopw-auth-hero-stats">
                <div class="shopw-auth-stat">
                    <span>Sản phẩm trong giỏ</span>
                    <strong>{{ number_format($cartCount) }}</strong>
                </div>
                <div class="shopw-auth-stat">
                    <span>Tạm tính</span>
                    <strong>{{ number_format($cartTotals['subtotal'], 0, ',', '.') }} VND</strong>
                </div>
                <div class="shopw-auth-stat">
                    <span>Trạng thái</span>
                    <strong>{{ $authStatusLabel }}</strong>
                </div>
            </div>
        </div>

        <div class="shopw-auth-assurance-grid">
            @foreach ($authSupportItems as $supportItem)
                <div class="shopw-auth-assurance-card">
                    <span class="shopw-auth-assurance-icon">
                        <i class="fa {{ $supportItem['icon'] }}"></i>
                    </span>
                    <div class="shopw-auth-assurance-copy">
                        <strong>{{ $supportItem['title'] }}</strong>
                        <p>{{ $supportItem['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($currentCustomer)
            <div class="shopw-auth-logged-in">
                <div class="shopw-auth-identity">
                    <span class="shopw-auth-identity-avatar">
                        @if (!empty($storefrontCustomerPicture))
                            <img src="{{ $storefrontCustomerPicture }}" alt="{{ $authCustomerDisplayName }}">
                        @else
                            <span>{{ $authAvatarInitials }}</span>
                        @endif
                    </span>
                    <div class="shopw-auth-identity-copy">
                        <span class="shopw-auth-identity-kicker">Tài khoản đã xác thực</span>
                        <strong>{{ $authCustomerDisplayName }}</strong>
                        @if ($authCustomerEmail)
                            <small>{{ $authCustomerEmail }}</small>
                        @endif
                        @if ($authCustomerPhone)
                            <small>{{ $authCustomerPhone }}</small>
                        @endif
                    </div>
                </div>
                <div class="shopw-auth-logged-actions">
                    <a href="{{ $checkoutUrl }}" class="shopw-auth-primary-link">Tiếp tục thanh toán</a>
                    <a href="{{ url('/history') }}" class="shopw-auth-secondary-link">Đơn hàng của tôi</a>
                    <a href="{{ url('/dang-xuat-khach-hang?redirect=' . ($authRedirectKey ?? 'checkout')) }}"
                        class="shopw-auth-secondary-link">Đăng xuất</a>
                </div>
            </div>
        @endif

        @php
            $showCompactAuthSwitcher = (bool) $currentCustomer;
        @endphp

        @if ($showCompactAuthSwitcher)
            <details class="shopw-auth-switcher">
                <summary>Đăng nhập bằng tài khoản khác</summary>
                <div class="shopw-auth-layout">
                    <div class="shopw-auth-card">
                        <div class="shopw-auth-card-head">
                            <h2>Đăng nhập tài khoản</h2>
                            <p>Dùng tài khoản hiện có để tiếp tục đặt hàng và theo dõi lịch sử đơn mua.</p>
                        </div>

                        <form action="{{ url('/login-customer') }}" method="POST" class="shopw-auth-form">
                            {{ csrf_field() }}
                            <input type="hidden" name="redirect_target" value="{{ $authRedirectKey ?? 'checkout' }}">
                            <input type="hidden" name="redirect_to" value="{{ $authRedirectUrl ?? url('/checkout') }}">
                            <label class="shopw-auth-field">
                                <span>Email</span>
                                <input type="email" name="email_account" value="{{ old('email_account') }}"
                                    placeholder="you@example.com" />
                            </label>
                            <label class="shopw-auth-field">
                                <span>Mật khẩu</span>
                                <input type="password" name="password_account" placeholder="Nhập mật khẩu" />
                            </label>

                            <div class="shopw-auth-form-meta">
                                <label class="shopw-auth-check">
                                    <input type="checkbox" class="checkbox">
                                    <span>Ghi nhớ đăng nhập</span>
                                </label>
                                <a href="{{ url('/quen-mat-khau') }}" class="shopw-auth-inline-link">Quên mật khẩu</a>
                            </div>

                            <button type="submit" class="btn btn-default shopw-auth-primary-link is-full">Đăng nhập</button>
                        </form>

                        <div class="shopw-auth-social">
                            <span>Hoặc dùng phương thức khác</span>
                            <a href="{{ url('login-customer-google?' . http_build_query([
                                'redirect' => $authRedirectKey ?? 'checkout',
                                'redirect_to' => $authRedirectUrl ?? url('/checkout'),
                            ])) }}"
                                class="shopw-auth-secondary-link is-full">
                                <i class="fa fa-google"></i>Đăng nhập bằng Google
                            </a>
                        </div>
                    </div>

                    <div class="shopw-auth-card">
                        <div class="shopw-auth-card-head">
                            <h2>Tạo tài khoản mới</h2>
                            <p>Nếu chưa có tài khoản, đăng ký nhanh để tiếp tục thanh toán và lưu lịch sử mua hàng.</p>
                        </div>

                        <form action="{{ url('/add-customer') }}" method="POST" class="shopw-auth-form">
                            {{ csrf_field() }}
                            <input type="hidden" name="redirect_target" value="{{ $authRedirectKey ?? 'checkout' }}">
                            <input type="hidden" name="redirect_to" value="{{ $authRedirectUrl ?? url('/checkout') }}">
                            <label class="shopw-auth-field">
                                <span>Họ và tên</span>
                                <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                    placeholder="Nguyen Van A" />
                            </label>
                            <label class="shopw-auth-field">
                                <span>Email</span>
                                <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                    placeholder="you@example.com" />
                            </label>
                            <label class="shopw-auth-field">
                                <span>Mật khẩu</span>
                                <input type="password" name="customer_password" placeholder="Tối thiểu 6 ký tự" />
                            </label>
                            <label class="shopw-auth-field">
                                <span>Số điện thoại</span>
                                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                                    placeholder="09xxxxxxxx" />
                            </label>

                            <button type="submit" class="btn btn-default shopw-auth-primary-link is-full">Đăng ký tài khoản</button>
                        </form>
                    </div>
                </div>
            </details>
        @else
            <div class="shopw-auth-layout">
                <div class="shopw-auth-card">
                    <div class="shopw-auth-card-head">
                        <h2>Đăng nhập tài khoản</h2>
                        <p>Dùng tài khoản hiện có để tiếp tục đặt hàng và theo dõi lịch sử đơn mua.</p>
                    </div>

                    <form action="{{ url('/login-customer') }}" method="POST" class="shopw-auth-form">
                        {{ csrf_field() }}
                        <input type="hidden" name="redirect_target" value="{{ $authRedirectKey ?? 'checkout' }}">
                        <input type="hidden" name="redirect_to" value="{{ $authRedirectUrl ?? url('/checkout') }}">
                        <label class="shopw-auth-field">
                            <span>Email</span>
                            <input type="email" name="email_account" value="{{ old('email_account') }}"
                                placeholder="you@example.com" />
                        </label>
                        <label class="shopw-auth-field">
                            <span>Mật khẩu</span>
                            <input type="password" name="password_account" placeholder="Nhập mật khẩu" />
                        </label>

                        <div class="shopw-auth-form-meta">
                            <label class="shopw-auth-check">
                                <input type="checkbox" class="checkbox">
                                <span>Ghi nhớ đăng nhập</span>
                            </label>
                            <a href="{{ url('/quen-mat-khau') }}" class="shopw-auth-inline-link">Quên mật khẩu</a>
                        </div>

                        <button type="submit" class="btn btn-default shopw-auth-primary-link is-full">Đăng nhập</button>
                    </form>

                    <div class="shopw-auth-social">
                        <span>Hoặc dùng phương thức khác</span>
                        <a href="{{ url('login-customer-google?' . http_build_query([
                            'redirect' => $authRedirectKey ?? 'checkout',
                            'redirect_to' => $authRedirectUrl ?? url('/checkout'),
                        ])) }}"
                            class="shopw-auth-secondary-link is-full">
                            <i class="fa fa-google"></i>Đăng nhập bằng Google
                        </a>
                    </div>
                </div>

                <div class="shopw-auth-card">
                    <div class="shopw-auth-card-head">
                        <h2>Tạo tài khoản mới</h2>
                        <p>Nếu chưa có tài khoản, đăng ký nhanh để tiếp tục thanh toán và lưu lịch sử mua hàng.</p>
                    </div>

                    <form action="{{ url('/add-customer') }}" method="POST" class="shopw-auth-form">
                        {{ csrf_field() }}
                        <input type="hidden" name="redirect_target" value="{{ $authRedirectKey ?? 'checkout' }}">
                        <input type="hidden" name="redirect_to" value="{{ $authRedirectUrl ?? url('/checkout') }}">
                        <label class="shopw-auth-field">
                            <span>Họ và tên</span>
                            <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                placeholder="Nguyen Van A" />
                        </label>
                        <label class="shopw-auth-field">
                            <span>Email</span>
                            <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                placeholder="you@example.com" />
                        </label>
                        <label class="shopw-auth-field">
                            <span>Mật khẩu</span>
                            <input type="password" name="customer_password" placeholder="Tối thiểu 6 ký tự" />
                        </label>
                        <label class="shopw-auth-field">
                            <span>Số điện thoại</span>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                                placeholder="09xxxxxxxx" />
                        </label>

                        <button type="submit" class="btn btn-default shopw-auth-primary-link is-full">Đăng ký tài khoản</button>
                    </form>
                </div>
            </div>
        @endif

        <div class="shopw-auth-support-strip">
            @foreach ($authQuickLinks as $quickLink)
                <a href="{{ $quickLink['url'] }}" class="shopw-auth-support-item">
                    <span class="shopw-auth-support-icon">
                        <i class="fa {{ $quickLink['icon'] }}"></i>
                    </span>
                    <span class="shopw-auth-support-copy">
                        <strong>{{ $quickLink['label'] }}</strong>
                        <small>{{ $quickLink['value'] }}</small>
                    </span>
                    <span class="shopw-auth-support-arrow">
                        <i class="fa fa-angle-right"></i>
                    </span>
                </a>
            @endforeach
        </div>
    </section>
@endsection

