@extends('layouts.home-tech')
@section('content')
    @php
        $section = $active_section ?? 'payment-methods';
        $customerName = filled($customer->name ?? null) ? $customer->name : 'Khách hàng';
        $customerEmail = filled($customer->email ?? null) ? $customer->email : 'Chưa cập nhật';
        $customerInitial = strtoupper(mb_substr($customerName, 0, 1, 'UTF-8'));

        $accountMenu = [
            ['key' => 'overview', 'label' => 'Tổng quan tài khoản', 'icon' => 'fa-th-large', 'href' => route('customer.account', ['section' => 'overview'])],
            ['key' => 'orders', 'label' => 'Đơn hàng của tôi', 'icon' => 'fa-shopping-bag', 'href' => route('customer.account', ['section' => 'orders'])],
            ['key' => 'history', 'label' => 'Lịch sử mua hàng', 'icon' => 'fa-history', 'href' => route('customer.account', ['section' => 'history'])],
            ['key' => 'order-detail', 'label' => 'Chi tiết đơn hàng', 'icon' => 'fa-file-text-o', 'href' => route('customer.account', ['section' => 'order-detail'])],
            ['key' => 'tracking', 'label' => 'Theo dõi đơn hàng', 'icon' => 'fa-truck', 'href' => route('customer.account', ['section' => 'tracking'])],
            ['key' => 'wishlist', 'label' => 'Sản phẩm yêu thích', 'icon' => 'fa-heart-o', 'href' => route('customer.account', ['section' => 'wishlist'])],
            ['key' => 'address', 'label' => 'Địa chỉ của tôi', 'icon' => 'fa-map-marker', 'href' => route('customer.account', ['section' => 'address'])],
            ['key' => 'billing', 'label' => 'Thông tin thanh toán', 'icon' => 'fa-credit-card', 'href' => route('customer.account', ['section' => 'billing'])],
            ['key' => 'payment-methods', 'label' => 'Phương thức thanh toán', 'icon' => 'fa-money', 'href' => route('customer.account', ['section' => 'payment-methods'])],
            ['key' => 'review', 'label' => 'Nhận xét của tôi', 'icon' => 'fa-comment-o', 'href' => route('customer.account', ['section' => 'review'])],
            ['key' => 'password', 'label' => 'Cài đặt tài khoản', 'icon' => 'fa-cog', 'href' => route('customer.account', ['section' => 'password'])],
        ];

        $savedMethods = collect($savedMethods ?? []);
        $promoOffers = collect($promoOffers ?? []);
    @endphp

    <div class="pmpage">
        <div class="container">
            @include('partials.storefront.breadcrumb', [
                'class' => 'pmpage-breadcrumb',
                'separator' => '›',
                'items' => [
                    ['label' => 'Tài khoản', 'url' => route('customer.account')],
                    ['label' => 'Phương thức thanh toán', 'current' => true],
                ],
            ])

            @if (session()->has('message'))
                <div class="pmpage-alert is-success">{{ session('message') }}</div>
            @endif
            @if ($errors->any())
                <div class="pmpage-alert is-error">
                    <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="pmpage-grid">
                {{-- ===================== LEFT SIDEBAR ===================== --}}
                <aside class="pmpage-sidebar">
                    <div class="pmpage-user">
                        <div class="pmpage-user-avatar">
                            <span>{{ $customerInitial }}</span>
                        </div>
                        <div class="pmpage-user-info">
                            <strong>{{ $customerName }}</strong>
                            <span>{{ $customerEmail }}</span>
                            <a href="{{ route('customer.account', ['section' => 'profile']) }}">Xem thông tin tài khoản <i class="fa fa-angle-right"></i></a>
                        </div>
                    </div>

                    <ul class="pmpage-nav">
                        @foreach ($accountMenu as $item)
                            <li class="{{ $section === $item['key'] ? 'is-active' : '' }}">
                                <a href="{{ $item['href'] }}">
                                    <i class="fa {{ $item['icon'] }}"></i>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                        <li class="pmpage-nav-sep"></li>
                        <li class="pmpage-nav-logout">
                            <form method="POST" action="{{ url('/dang-xuat-khach-hang') }}">
                                @csrf
                                <button type="submit">
                                <i class="fa fa-sign-out"></i>
                                <span>Đăng xuất</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </aside>

                {{-- ===================== MAIN CONTENT ===================== --}}
                <main class="pmpage-main">
                    @if ($section === 'payment-methods' || !in_array($section, ['profile','edit','review','password']))
                        @include('pages.account.partials.payment-methods', ['savedMethods' => $savedMethods])
                    @elseif ($section === 'profile')
                        <div class="pmpage-card pmpage-card--padded">
                            <div class="pmpage-section-head">
                                <div>
                                    <h2>Thông tin tài khoản</h2>
                                    <p class="pmpage-section-sub">Xem chi tiết tài khoản của bạn.</p>
                                </div>
                            </div>
                            <div class="pmpage-detail-list">
                                <div class="pmpage-detail-row"><span>Họ và tên</span><strong>{{ $customer->name ?? '—' }}</strong></div>
                                <div class="pmpage-detail-row"><span>Email</span><strong>{{ $customer->email ?? '—' }}</strong></div>
                                <div class="pmpage-detail-row"><span>Số điện thoại</span><strong>{{ $customer->phone ?? 'Chưa cập nhật' }}</strong></div>
                            </div>
                        </div>
                    @elseif ($section === 'edit')
                        <div class="pmpage-card pmpage-card--padded">
                            <div class="pmpage-section-head"><h2>Chỉnh sửa thông tin</h2></div>
                            <form action="{{ route('customer.account.update') }}" method="POST" class="pmpage-form">
                                @csrf
                                <label>Họ và tên<input type="text" name="name" value="{{ $customer->name ?? '' }}" required></label>
                                <label>Số điện thoại<input type="text" name="phone" value="{{ $customer->phone ?? '' }}"></label>
                                <button type="submit" class="pmpage-btn pmpage-btn--primary">Lưu thay đổi</button>
                            </form>
                        </div>
                    @elseif ($section === 'review')
                        <div class="pmpage-card pmpage-card--padded">
                            <div class="pmpage-section-head"><h2>Nhận xét của tôi</h2></div>
                            <form action="{{ route('customer.account.review') }}" method="POST" class="pmpage-form">
                                @csrf
                                <textarea name="content" rows="6" placeholder="Cảm nhận của bạn..."></textarea>
                                <button type="submit" class="pmpage-btn pmpage-btn--primary">Gửi đánh giá</button>
                            </form>
                        </div>
                    @elseif ($section === 'password')
                        <div class="pmpage-card pmpage-card--padded">
                            <div class="pmpage-section-head"><h2>Cài đặt tài khoản</h2></div>
                            <form action="{{ route('customer.account.change-password') }}" method="POST" class="pmpage-form">
                                @csrf
                                <label>Mật khẩu hiện tại<input type="password" name="current_password" required></label>
                                <label>Mật khẩu mới<input type="password" name="new_password" required></label>
                                <label>Xác nhận mật khẩu mới<input type="password" name="new_password_confirmation" required></label>
                                <button type="submit" class="pmpage-btn pmpage-btn--primary">Đổi mật khẩu</button>
                            </form>
                        </div>
                    @endif

                    {{-- ===================== PROMO OFFERS ===================== --}}
                    @if ($promoOffers->isNotEmpty())
                        <div class="pmpage-card pmpage-promo">
                            <div class="pmpage-promo-head">Ưu đãi thanh toán</div>
                            <div class="pmpage-promo-grid">
                                @foreach ($promoOffers as $offer)
                                    <div class="pmpage-promo-item">
                                        <div class="pmpage-promo-logo pmpage-promo-logo--{{ $offer['logo'] }}">
                                            @include('pages.account.partials.brand-mark', ['brand' => $offer['logo']])
                                        </div>
                                        <div class="pmpage-promo-body">
                                            <strong>{{ $offer['title'] }}</strong>
                                            @if ($offer['sub'])<span class="pmpage-promo-sub">{{ $offer['sub'] }}</span>@endif
                                            <span class="pmpage-promo-desc">{{ $offer['desc'] }}</span>
                                            @if (!empty($offer['url']))
                                                <a href="{{ $offer['url'] }}" class="pmpage-promo-link">Xem chi tiết <i class="fa fa-angle-right"></i></a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </main>

                {{-- ===================== RIGHT SIDEBAR ===================== --}}
                <aside class="pmpage-aside">
                    <div class="pmpage-aside-card">
                        <div class="pmpage-aside-head">
                            <span class="pmpage-aside-icon pmpage-aside-icon--blue"><i class="fa fa-shield"></i></span>
                            <h4>Thanh toán an toàn</h4>
                        </div>
                        <p>Thông tin thanh toán của bạn được mã hóa và bảo mật tuyệt đối.</p>
                        <div class="pmpage-aside-status is-ok"><i class="fa fa-check-circle"></i> Bảo mật SSL 256-bit</div>
                    </div>

                    <div class="pmpage-aside-card">
                        <div class="pmpage-aside-head">
                            <span class="pmpage-aside-icon pmpage-aside-icon--blue"><i class="fa fa-lock"></i></span>
                            <h4>Xác thực 2 lớp (2FA)</h4>
                        </div>
                        <p>Tính năng xác thực 2 lớp chưa được bật cho tài khoản này.</p>
                        <div class="pmpage-aside-row">
                            <span class="pmpage-aside-status"><i class="fa fa-info-circle"></i> Chưa hỗ trợ</span>
                            <label class="pmpage-switch">
                                <input type="checkbox" disabled>
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="pmpage-aside-card">
                        <div class="pmpage-aside-head">
                            <span class="pmpage-aside-icon pmpage-aside-icon--blue"><i class="fa fa-lightbulb-o"></i></span>
                            <h4>Mẹo bảo mật khi lưu thẻ</h4>
                        </div>
                        <ul class="pmpage-aside-tips">
                            <li><i class="fa fa-check"></i> Chỉ lưu thẻ trên thiết bị cá nhân.</li>
                            <li><i class="fa fa-check"></i> Không chia sẻ thông tin thẻ với người khác.</li>
                            <li><i class="fa fa-check"></i> Bật xác thực 2 lớp để bảo vệ tài khoản.</li>
                        </ul>
                        <a href="{{ url('/chinh-sach-bao-mat') }}" class="pmpage-aside-link">Xem thêm mẹo bảo mật <i class="fa fa-angle-right"></i></a>
                    </div>

                    <div class="pmpage-aside-card">
                        <div class="pmpage-aside-head">
                            <span class="pmpage-aside-icon pmpage-aside-icon--blue"><i class="fa fa-headphones"></i></span>
                            <h4>Liên hệ hỗ trợ</h4>
                        </div>
                        <p>Chúng tôi luôn sẵn sàng hỗ trợ bạn.</p>
                        <ul class="pmpage-aside-contact">
                            <li><i class="fa fa-phone"></i> Hotline: <strong>1900 1234</strong></li>
                            <li><i class="fa fa-envelope-o"></i> Email: <strong>support@dangduc.vn</strong></li>
                            <li><i class="fa fa-clock-o"></i> Thời gian: <strong>8:00 - 21:00 (T2 - CN)</strong></li>
                        </ul>
                        <a href="{{ url('/lien-he') }}" class="pmpage-btn pmpage-btn--outline pmpage-btn--block"><i class="fa fa-comment-o"></i> Liên hệ hỗ trợ</a>
                    </div>

                    <div class="pmpage-aside-card pmpage-aside-card--bank">
                        <div class="pmpage-aside-head">
                            <span class="pmpage-aside-icon pmpage-aside-icon--blue"><i class="fa fa-university"></i></span>
                            <h4>Chuyển khoản</h4>
                        </div>
                        <div class="pmpage-aside-discount">
                            <strong>Giảm 3%</strong>
                            <span>Tối đa 100.000đ</span>
                        </div>
                        <p>Khi chuyển khoản trước</p>
                        <a href="{{ url('/huong-dan-thanh-toan') }}" class="pmpage-aside-link">Xem chi tiết <i class="fa fa-angle-right"></i></a>
                    </div>
                </aside>
            </div>

            {{-- ===================== SUPPORT CONTACT BAR ===================== --}}
            <div class="pmpage-help">
                <div class="pmpage-help-item">
                    <span class="pmpage-help-icon"><i class="fa fa-question-circle"></i></span>
                    <div>
                        <strong>Cần hỗ trợ về thanh toán?</strong>
                        <span>Đội ngũ của chúng tôi luôn sẵn sàng hỗ trợ bạn.</span>
                    </div>
                </div>
                <div class="pmpage-help-item">
                    <span class="pmpage-help-icon"><i class="fa fa-phone"></i></span>
                    <div>
                        <strong>Hotline: 1900 1234</strong>
                        <span>8:00 - 21:00 (T2 - CN)</span>
                    </div>
                </div>
                <div class="pmpage-help-item">
                    <span class="pmpage-help-icon"><i class="fa fa-envelope-o"></i></span>
                    <div>
                        <strong>Email: support@dangduc.vn</strong>
                        <span>Phản hồi trong 15 phút</span>
                    </div>
                </div>
                <div class="pmpage-help-item">
                    <span class="pmpage-help-icon"><i class="fa fa-comments-o"></i></span>
                    <div>
                        <strong>Chat trực tuyến</strong>
                        <span>Trò chuyện ngay với chúng tôi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pages.account.partials.payment-methods-styles')
@endsection
