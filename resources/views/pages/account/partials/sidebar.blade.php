{{-- Shared account sidebar partial --}}
@php
    $currentSection = $currentSection ?? '';
    $customerName = filled($customer->name ?? null) ? $customer->name : 'Khách hàng';
    $customerEmail = filled($customer->email ?? null) ? $customer->email : 'Chưa cập nhật';
    $customerInitial = strtoupper(mb_substr($customerName, 0, 1, 'UTF-8'));

    $sidebarMenu = [
        ['key' => 'overview', 'label' => 'Tổng quan tài khoản', 'icon' => 'fa-th-large'],
        ['key' => 'orders', 'label' => 'Đơn hàng của tôi', 'icon' => 'fa-shopping-cart'],
        ['key' => 'history', 'label' => 'Lịch sử mua hàng', 'icon' => 'fa-history'],
        ['key' => 'cancelled-orders', 'label' => 'Đơn hàng đã hủy', 'icon' => 'fa-times-circle-o'],
        ['key' => 'returns', 'label' => 'Trả hàng / Hoàn tiền', 'icon' => 'fa-undo'],
        ['key' => 'wishlist', 'label' => 'Sản phẩm yêu thích', 'icon' => 'fa-heart-o'],
        ['key' => 'recently-viewed', 'label' => 'Sản phẩm đã xem', 'icon' => 'fa-eye'],
        ['key' => 'address', 'label' => 'Địa chỉ của tôi', 'icon' => 'fa-map-marker'],
        ['key' => 'billing', 'label' => 'Thông tin thanh toán', 'icon' => 'fa-credit-card'],
        ['key' => 'payment-methods', 'label' => 'Phương thức thanh toán', 'icon' => 'fa-money'],
        ['key' => 'reviews', 'label' => 'Nhận xét của tôi', 'icon' => 'fa-comment-o'],
        ['key' => 'vouchers', 'label' => 'Kho voucher / mã giảm giá của tôi', 'icon' => 'fa-ticket'],
    ];

    $sectionRouteMap = [
        'overview' => url('/account'),
        'orders' => url('/account/orders'),
        'history' => url('/account/purchase-history'),
        'tracking' => url('/account/orders'),
        'wishlist' => url('/account/wishlist'),
        'profile' => url('/account/profile'),
        'address' => url('/account/addresses'),
        'billing' => url('/account/invoices'),
        'cancelled-orders' => url('/account/cancelled-orders'),
        'returns' => url('/account/returns'),
        'recently-viewed' => url('/account/recently-viewed'),
        'payment-methods' => url('/account/payment-methods'),
        'reviews' => url('/account/reviews'),
        'vouchers' => url('/account/vouchers'),
    ];
@endphp

<aside class="accp-sidebar">
    <div class="accp-user">
        <div class="accp-avatar">{{ $customerInitial }}</div>
        <div class="accp-user-info">
            <strong>{{ $customerName }}</strong>
            <span>{{ $customerEmail }}</span>
            <a href="{{ route('customer.account.profile') }}">Xem thông tin tài khoản <i class="fa fa-angle-right"></i></a>
        </div>
    </div>
    <ul class="accp-nav">
        @foreach ($sidebarMenu as $item)
            @php
                $href = $sectionRouteMap[$item['key']] ?? route('customer.account', ['section' => $item['key']]);
                $isActive = $currentSection === $item['key'];
            @endphp
            <li class="{{ $isActive ? 'is-active' : '' }}">
                <a href="{{ $href }}"><i class="fa {{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span></a>
            </li>
        @endforeach
        <li class="accp-nav-sep"></li>
        <li class="accp-nav-logout">
            <form method="POST" action="{{ url('/dang-xuat-khach-hang') }}">
                @csrf
                <button type="submit"><i class="fa fa-sign-out"></i><span>Đăng xuất</span></button>
            </form>
        </li>
    </ul>
</aside>
