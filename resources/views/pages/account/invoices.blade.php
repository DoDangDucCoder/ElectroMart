@extends('layouts.home-tech')
@section('body_class', 'g-checkout-redesign inv-page')

@php
    $section = 'invoices';
    $customerName = $customer->name ?? 'Khách hàng';
    $customerEmail = $customer->email ?? '';
    $customerInitial = strtoupper(mb_substr($customerName, 0, 1, 'UTF-8'));

    $accountMenu = [
        ['key' => 'overview', 'label' => 'Tổng quan tài khoản', 'icon' => 'fa-th-large', 'href' => route('customer.account')],
        ['key' => 'orders', 'label' => 'Đơn hàng của tôi', 'icon' => 'fa-shopping-cart', 'href' => url('/account/orders')],
        ['key' => 'cancelled', 'label' => 'Đơn hàng đã huỷ', 'icon' => 'fa-times-circle', 'href' => route('customer.account.cancelled-orders')],
        ['key' => 'returns', 'label' => 'Trả hàng / Hoàn tiền', 'icon' => 'fa-undo', 'href' => route('customer.account.returns')],
        ['key' => 'wishlist', 'label' => 'Sản phẩm yêu thích', 'icon' => 'fa-heart-o', 'href' => route('customer.account.wishlist')],
        ['key' => 'recently-viewed', 'label' => 'Sản phẩm đã xem', 'icon' => 'fa-eye', 'href' => route('customer.account.recently-viewed')],
        ['key' => 'addresses', 'label' => 'Địa chỉ của tôi', 'icon' => 'fa-map-marker', 'href' => route('customer.account.addresses')],
        ['key' => 'payment-methods', 'label' => 'Phương thức thanh toán', 'icon' => 'fa-credit-card', 'href' => route('customer.account.payment-methods')],
        ['key' => 'vouchers', 'label' => 'Voucher của tôi', 'icon' => 'fa-ticket', 'href' => route('customer.account.vouchers')],
        ['key' => 'reviews', 'label' => 'Nhận xét của tôi', 'icon' => 'fa-comment-o', 'href' => route('customer.account.reviews')],
        ['key' => 'password', 'label' => 'Đổi mật khẩu', 'icon' => 'fa-cog', 'href' => route('customer.account.change-password.view')],
        ['key' => 'invoices', 'label' => 'Hóa đơn / xuất hóa đơn VAT', 'icon' => 'fa-file-pdf-o', 'href' => url('/account/invoices')],
    ];

    $invoices     = $invoices ?? collect();
    $invoiceStats = $invoiceStats ?? ['total_requests' => 0, 'issued' => 0, 'processing' => 0, 'total_value' => 0];
    $statusMap    = ['issued' => ['Đã xuất', 'green'], 'processing' => ['Đang xử lý', 'amber'], 'pending' => ['Chờ xử lý', 'amber'], 'rejected' => ['Cần bổ sung', 'red']];
@endphp

@section('content')
<style>
body.inv-page{background:#f3f5f7;font-family:'Inter',-apple-system,sans-serif}
.inv-wrap{max-width:1240px;margin:0 auto;padding:0 16px 40px}
.inv-crumb{font-size:13px;color:#6b7280;padding:14px 0;display:flex;align-items:center;gap:8px}
.inv-crumb a{color:#6b7280;text-decoration:none}.inv-crumb a:hover{color:#0d9488}
.inv-crumb .sep{color:#cbd5e1}.inv-crumb .current{color:#111827;font-weight:500}
.inv-grid{display:grid;grid-template-columns:220px 1fr 280px;gap:20px;align-items:flex-start}
@media(max-width:1100px){.inv-grid{grid-template-columns:200px 1fr}}
@media(max-width:768px){.inv-grid{grid-template-columns:1fr}}
/* Sidebar */
.inv-sidebar{background:#fff;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;position:sticky;top:16px}
.inv-user{padding:20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:12px}
.inv-avatar{width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#0f766e);color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;flex-shrink:0}
.inv-user-info{min-width:0}
.inv-user-info strong{font-size:13px;color:#111827;display:block}
.inv-user-info span{font-size:11px;color:#6b7280}
.inv-nav{list-style:none;padding:8px 0;margin:0}
.inv-nav li a{display:flex;align-items:center;gap:10px;padding:8px 18px;font-size:13px;color:#374151;text-decoration:none;transition:all .15s}
.inv-nav li a i{width:18px;text-align:center;color:#9ca3af;font-size:13px}
.inv-nav li a:hover{background:#f0fdfa;color:#0d9488}
.inv-nav li.is-active a{background:#f0fdfa;color:#0d9488;font-weight:600;border-left:3px solid #0d9488}
.inv-nav li.is-active a i{color:#0d9488}
.inv-nav-sep{height:1px;background:#f1f5f9;margin:6px 18px}
.inv-nav-logout a{color:#dc2626!important}.inv-nav-logout a i{color:#dc2626!important}
/* Main */
.inv-main{min-width:0}
.inv-card{background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:22px;margin-bottom:16px;box-shadow:0 1px 2px rgba(0,0,0,.03)}
.inv-page-head{display:flex;align-items:flex-start;gap:16px;margin-bottom:20px}
.inv-page-head-icon{width:48px;height:48px;border-radius:12px;background:#f0fdfa;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
.inv-page-head h2{font-size:20px;font-weight:800;color:#0f172a;margin:0 0 4px}
.inv-page-head p{font-size:13px;color:#6b7280;margin:0}
/* Stats */
.inv-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
@media(max-width:768px){.inv-stats{grid-template-columns:repeat(2,1fr)}}
.inv-stat{border:1px solid #e5e7eb;border-radius:10px;padding:16px;text-align:center;background:#fff}
.inv-stat .icon{width:40px;height:40px;border-radius:10px;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;font-size:18px}
.inv-stat .icon.teal{background:#f0fdfa;color:#0d9488}
.inv-stat .icon.blue{background:#eff6ff;color:#3b82f6}
.inv-stat .icon.amber{background:#fffbeb;color:#d97706}
.inv-stat .icon.red{background:#fef2f2;color:#dc2626}
.inv-stat .num{font-size:24px;font-weight:800;color:#111827;margin-bottom:2px}
.inv-stat .lbl{font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.3px}
/* Table */
.inv-table-wrap{overflow-x:auto}
.inv-table{width:100%;border-collapse:collapse;font-size:12.5px;min-width:700px}
.inv-table thead th{background:#f8fafc;padding:10px 12px;text-align:left;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb;font-size:11px;text-transform:uppercase;letter-spacing:.3px;white-space:nowrap}
.inv-table tbody td{padding:10px 12px;border-bottom:1px solid #f3f4f6;color:#374151;vertical-align:middle}
.inv-table .id-cell{font-size:12px;font-weight:600;color:#111827}.inv-table .id-cell small{display:block;color:#9ca3af;font-weight:400}
.inv-table .prod-cell{display:flex;align-items:center;gap:8px}.inv-table .prod-img{width:36px;height:36px;border-radius:6px;border:1px solid #e5e7eb;overflow:hidden;flex-shrink:0}
.inv-table .prod-img img{width:100%;height:100%;object-fit:cover}
.inv-badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.inv-badge.green{background:#ecfdf5;color:#16a34a}.inv-badge.amber{background:#fffbeb;color:#d97706}.inv-badge.red{background:#fef2f2;color:#dc2626}
.inv-table .actions{display:flex;gap:6px}
.inv-table .act-btn{padding:4px 10px;border-radius:5px;font-size:11px;font-weight:500;text-decoration:none;border:1px solid #e5e7eb;color:#374151;background:#fff;cursor:pointer;white-space:nowrap}
.inv-table .act-btn:hover{border-color:#0d9488;color:#0d9488}
/* Form */
.inv-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
@media(max-width:600px){.inv-form-grid{grid-template-columns:1fr}}
.inv-field label{display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px}
.inv-field input,.inv-field select,.inv-field textarea{width:100%;height:38px;border:1px solid #e5e7eb;border-radius:6px;padding:0 12px;font-size:13px;color:#111827;background:#fff}
.inv-field input:focus,.inv-field select:focus,.inv-field textarea:focus{outline:none;border-color:#0d9488;box-shadow:0 0 0 3px rgba(13,148,136,.1)}
.inv-field textarea{height:auto;padding:8px 12px;min-height:60px;resize:vertical}
.inv-form-actions{display:flex;gap:10px;margin-top:14px}
.inv-btn{height:38px;padding:0 20px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:6px;border:none;transition:all .15s}
.inv-btn--primary{background:#0d9488;color:#fff}.inv-btn--primary:hover{background:#0f766e}
.inv-btn--outline{background:#fff;color:#374151;border:1px solid #d1d5db}.inv-btn--outline:hover{border-color:#0d9488;color:#0d9488}
/* Right aside */
.inv-aside{position:sticky;top:16px}
@media(max-width:1100px){.inv-aside{display:none}}
.inv-aside-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px;margin-bottom:14px}
.inv-aside-card h4{font-size:13px;font-weight:700;color:#111827;margin:0 0 12px}
.inv-aside-tips{list-style:none;padding:0;margin:0}
.inv-aside-tips li{display:flex;align-items:flex-start;gap:10px;padding:8px 0;font-size:12px;color:#374151;line-height:1.45}
.inv-aside-tips li+li{border-top:1px solid #f3f4f6}
.inv-aside-tips li .num{width:24px;height:24px;border-radius:50%;background:#f0fdfa;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0}
.inv-aside-trust{display:flex;flex-direction:column;gap:10px}
.inv-aside-trust-item{display:flex;align-items:center;gap:10px;font-size:12px;color:#374151}
.inv-aside-trust-item i{color:#0d9488;font-size:14px;width:18px;text-align:center}
.inv-aside-trust-item strong{color:#111827}
/* Policy */
.inv-policy{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px 24px;display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-top:20px}
@media(max-width:768px){.inv-policy{grid-template-columns:repeat(2,1fr)}}
.inv-policy-item{display:flex;gap:12px;align-items:flex-start}
.inv-policy-icon{width:40px;height:40px;border-radius:10px;background:#f0fdfa;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.inv-policy-title{font-size:13px;font-weight:700;color:#111827}
.inv-policy-sub{font-size:11.5px;color:#6b7280;line-height:1.4;margin-top:2px}
</style>

<div class="inv-wrap">
    @include('partials.storefront.breadcrumb', [
        'class' => 'inv-crumb',
        'separator' => '›',
        'items' => [
            ['label' => 'Tài khoản', 'url' => route('customer.account')],
            ['label' => 'Hóa đơn / xuất hóa đơn VAT', 'current' => true],
        ],
    ])

    <div class="inv-grid">
        {{-- SIDEBAR --}}
        <aside class="inv-sidebar">
            <div class="inv-user">
                <div class="inv-avatar">{{ $customerInitial }}</div>
                <div class="inv-user-info"><strong>{{ $customerName }}</strong><span>{{ $customerEmail }}</span></div>
            </div>
            <ul class="inv-nav">
                @foreach ($accountMenu as $item)
                    <li class="{{ $section === $item['key'] ? 'is-active' : '' }}"><a href="{{ $item['href'] }}"><i class="fa {{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span></a></li>
                @endforeach
                <li class="inv-nav-sep"></li>
                <li class="inv-nav-logout">
                    <form method="POST" action="{{ url('/dang-xuat-khach-hang') }}">
                        @csrf
                        <button type="submit"><i class="fa fa-sign-out"></i><span>Đăng xuất</span></button>
                    </form>
                </li>
            </ul>
        </aside>

        {{-- MAIN --}}
        <main class="inv-main">
            <div class="inv-page-head">
                <div class="inv-page-head-icon"><i class="fa fa-file-pdf-o"></i></div>
                <div>
                    <h2>Hóa đơn / xuất hóa đơn VAT</h2>
                    <p>Quản lý yêu cầu xuất hóa đơn VAT cho đơn hàng của bạn</p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="inv-stats">
                <div class="inv-stat"><div class="icon teal"><i class="fa fa-file-text-o"></i></div><div class="num">{{ $invoiceStats['total_requests'] }}</div><div class="lbl">Tổng yêu cầu</div></div>
                <div class="inv-stat"><div class="icon blue"><i class="fa fa-check-circle"></i></div><div class="num">{{ $invoiceStats['issued'] }}</div><div class="lbl">Đã xuất hóa đơn</div></div>
                <div class="inv-stat"><div class="icon amber"><i class="fa fa-clock-o"></i></div><div class="num">{{ $invoiceStats['processing'] }}</div><div class="lbl">Đang xử lý</div></div>
                <div class="inv-stat"><div class="icon red"><i class="fa fa-money"></i></div><div class="num">{{ number_format($invoiceStats['total_value'], 0, ',', '.') }}đ</div><div class="lbl">Tổng giá trị VAT</div></div>
            </div>

            {{-- Invoice table --}}
            <div class="inv-card">
                <div class="inv-table-wrap">
                    <table class="inv-table">
                        <thead><tr><th>Mã yêu cầu / Mã đơn hàng</th><th>Ngày yêu cầu</th><th>Thông tin đơn hàng</th><th>Loại hóa đơn</th><th>Giá trị hóa đơn</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
                        <tbody>
                            @forelse ($invoices as $inv)
                            @php
                                $st = $statusMap[$inv->status] ?? ['—', 'amber'];
                                $orderCode = $inv->order?->code ?? '—';
                                $firstItem = $inv->order?->items?->first();
                                $productName = $firstItem?->product?->name ?? $firstItem?->name ?? '—';
                                $productImage = \App\Support\AssetUrl::productVariant($firstItem?->product?->thumbnail ?? $firstItem?->product?->image ?? null, 'thumb');
                                $typeLabel = $inv->type === 'company' ? 'Công ty' : 'Cá nhân';
                            @endphp
                            <tr>
                                <td class="id-cell">{{ $inv->code }}<small>{{ $orderCode }}</small></td>
                                <td>{{ optional($inv->created_at)->format('d/m/Y H:i') }}</td>
                                <td><div class="prod-cell"><div class="prod-img"><img src="{{ $productImage }}" alt=""></div><span>{{ $productName }}</span></div></td>
                                <td>{{ $typeLabel }}</td>
                                <td style="font-weight:600">{{ number_format((float) $inv->value, 0, ',', '.') }}đ</td>
                                <td><span class="inv-badge {{ $st[1] }}">{{ $st[0] }}</span></td>
                                <td><div class="actions"><a href="#" class="act-btn">Xem chi tiết</a>@if($inv->status === 'issued')<a href="#" class="act-btn">Tải PDF</a>@endif</div></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" style="text-align:center;padding:32px;color:#6b7280">
                                    <i class="fa fa-file-pdf-o" style="font-size:24px;color:#cbd5e1;display:block;margin-bottom:6px"></i>
                                    Bạn chưa có yêu cầu xuất hoá đơn VAT nào.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Request form --}}
            <div class="inv-card">
                <h3 style="font-size:15px;font-weight:700;color:#111827;margin:0 0 16px">Yêu cầu xuất hóa đơn mới</h3>
                <form class="inv-form-grid">
                    <div class="inv-field"><label>Mã đơn hàng *</label><input type="text" placeholder="Nhập mã đơn hàng (VD: DD250518234)"></div>
                    <div class="inv-field"><label>Tên công ty *</label><input type="text" placeholder="Nhập tên công ty"></div>
                    <div class="inv-field"><label>Họ và tên người nhận hóa đơn</label><input type="text" placeholder="Nhập họ và tên"></div>
                    <div class="inv-field"><label>Mã số thuế *</label><input type="text" placeholder="Nhập mã số thuế"></div>
                    <div class="inv-field"><label>Email nhận hóa đơn *</label><input type="email" placeholder="Nhập email nhận hóa đơn"></div>
                    <div class="inv-field"><label>Địa chỉ công ty *</label><input type="text" placeholder="Nhập địa chỉ công ty"></div>
                    <div class="inv-field"><label>Số điện thoại *</label><input type="text" placeholder="Nhập số điện thoại"></div>
                    <div class="inv-field"><label>Ghi chú</label><textarea placeholder="Ghi chú thêm (nếu có)" rows="2"></textarea></div>
                </form>
                <div class="inv-form-actions">
                    <button type="button" class="inv-btn inv-btn--outline"><i class="fa fa-refresh"></i> Làm mới</button>
                    <button type="button" class="inv-btn inv-btn--primary"><i class="fa fa-envelope"></i> Gửi yêu cầu</button>
                </div>
            </div>
        </main>

        {{-- RIGHT ASIDE --}}
        <aside class="inv-aside">
            <div class="inv-aside-card">
                <h4><i class="fa fa-phone" style="color:#0d9488;margin-right:6px"></i> Bạn cần hỗ trợ?</h4>
                <div style="padding:10px 0;border-bottom:1px solid #f3f4f6"><div style="font-size:11px;color:#6b7280">Hotline</div><div style="font-size:18px;font-weight:800;color:#0d9488">1900 1234</div></div>
                <div style="padding:10px 0;border-bottom:1px solid #f3f4f6"><div style="font-size:11px;color:#6b7280">Email hỗ trợ</div><div style="font-size:13px;font-weight:600;color:#0d9488">support@dangduc.vn</div></div>
                <div style="padding:10px 0"><div style="font-size:11px;color:#6b7280">Thời gian hỗ trợ</div><div style="font-size:13px;font-weight:600;color:#111827">8:00 - 21:00 (T2 - CN)</div></div>
                <button type="button" data-chatbot-launch style="width:100%;height:38px;background:#0d9488;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;margin-top:8px;display:flex;align-items:center;justify-content:center;gap:6px"><i class="fa fa-comments"></i> Chat ngay</button>
            </div>

            <div class="inv-aside-card">
                <h4>Lưu ý khi xuất hóa đơn VAT</h4>
                <ul class="inv-aside-tips">
                    <li><span class="num">1</span><span>Thời gian xử lý yêu cầu từ 1-3 ngày làm việc.</span></li>
                    <li><span class="num">2</span><span>Thông tin MST và thông tin công ty phải chính xác.</span></li>
                    <li><span class="num">3</span><span>Không thể chỉnh sửa thông tin sau khi hóa đơn đã phát hành.</span></li>
                    <li><span class="num">4</span><span>Hóa đơn điện tử sẽ được gửi qua email của bạn.</span></li>
                </ul>
            </div>

            <div class="inv-aside-card">
                <h4>Mua sắm an tâm</h4>
                <div class="inv-aside-trust">
                    <div class="inv-aside-trust-item"><i class="fa fa-check-circle"></i><strong>100% hàng chính hãng</strong></div>
                    <div class="inv-aside-trust-item"><i class="fa fa-headphones"></i><strong>Hỗ trợ tận tâm</strong></div>
                    <div class="inv-aside-trust-item"><i class="fa fa-shield"></i><strong>Thanh toán an toàn</strong></div>
                </div>
            </div>
        </aside>
    </div>

    <div class="inv-policy">
        <div class="inv-policy-item"><span class="inv-policy-icon"><i class="fa fa-truck"></i></span><div><div class="inv-policy-title">Miễn phí giao hàng</div><div class="inv-policy-sub">Áp dụng cho đơn hàng công nghệ đủ điều kiện</div></div></div>
        <div class="inv-policy-item"><span class="inv-policy-icon"><i class="fa fa-refresh"></i></span><div><div class="inv-policy-title">Đổi trả dễ dàng</div><div class="inv-policy-sub">Hỗ trợ đổi trả trong 7 ngày</div></div></div>
        <div class="inv-policy-item"><span class="inv-policy-icon"><i class="fa fa-shield"></i></span><div><div class="inv-policy-title">Bảo hành chính hãng</div><div class="inv-policy-sub">An tâm sử dụng với bảo hành chính hãng</div></div></div>
        <div class="inv-policy-item"><span class="inv-policy-icon"><i class="fa fa-headphones"></i></span><div><div class="inv-policy-title">Hỗ trợ tư vấn 24/7</div><div class="inv-policy-sub">Tư vấn chọn laptop, điện thoại và phụ kiện phù hợp</div></div></div>
    </div>
</div>
@endsection
