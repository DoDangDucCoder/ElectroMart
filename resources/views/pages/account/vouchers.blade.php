@extends('layouts.home-tech')
@section('body_class', 'accp-page')
@section('content')
<style>
.vc-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px}
@media(max-width:768px){.vc-grid{grid-template-columns:1fr}}
.vc-card{border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;display:flex;transition:box-shadow .15s;background:#fff}
.vc-card:hover{box-shadow:0 2px 8px rgba(0,0,0,.05)}
.vc-left{width:100px;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:14px;flex-shrink:0;position:relative}
.vc-left.teal{background:linear-gradient(135deg,#0d9488,#14b8a6)}.vc-left.amber{background:linear-gradient(135deg,#d97706,#f59e0b)}
.vc-left.red{background:linear-gradient(135deg,#dc2626,#ef4444)}.vc-left.blue{background:linear-gradient(135deg,#3b82f6,#60a5fa)}
.vc-left.gray{background:#e5e7eb}
.vc-left .discount-text{font-size:11px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.5px}
.vc-left .discount-val{font-size:26px;font-weight:900;color:#fff;line-height:1}
.vc-left .discount-unit{font-size:12px;font-weight:600;color:rgba(255,255,255,.85)}
.vc-left.gray .discount-text,.vc-left.gray .discount-val,.vc-left.gray .discount-unit{color:#9ca3af}
.vc-right{flex:1;padding:14px 16px;display:flex;flex-direction:column;min-width:0}
.vc-right .name{font-size:13.5px;font-weight:700;color:#111827;margin-bottom:4px;display:flex;align-items:center;gap:6px}
.vc-right .code-row{display:flex;align-items:center;gap:6px;margin-bottom:4px}
.vc-right .code{font-size:12px;font-weight:700;color:#0d9488;background:#f0fdfa;padding:2px 8px;border-radius:4px;letter-spacing:.5px}
.vc-right .copy-btn{background:none;border:none;color:#9ca3af;cursor:pointer;font-size:12px;padding:2px}
.vc-right .copy-btn:hover{color:#0d9488}
.vc-right .meta{font-size:11px;color:#6b7280;line-height:1.5}
.vc-right .meta i{color:#9ca3af;margin-right:4px}
.vc-right .bottom{display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:8px}
.vc-right .hsd{font-size:11px;color:#9ca3af}
.vc-apply-btn{padding:5px 14px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;border:none;transition:all .15s}
.vc-apply-btn.active{background:#0d9488;color:#fff}.vc-apply-btn.active:hover{background:#0f766e}
.vc-apply-btn.used{background:#f3f4f6;color:#9ca3af;cursor:default}
.vc-apply-btn.expired{background:#fef2f2;color:#dc2626;cursor:default}
.vc-apply-btn.copy{background:#fff;color:#374151;border:1px solid #d1d5db}.vc-apply-btn.copy:hover{border-color:#0d9488;color:#0d9488}
/* Personalized offers */
.vc-offers{margin-top:16px}
.vc-offers h4{font-size:14px;font-weight:700;color:#111827;margin:0 0 12px;display:flex;align-items:center;justify-content:space-between}
.vc-offers h4 a{font-size:12px;color:#0d9488;text-decoration:none;font-weight:500}
.vc-offer-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}
@media(max-width:900px){.vc-offer-grid{grid-template-columns:repeat(2,1fr)}}
.vc-offer{border:1px solid #e5e7eb;border-radius:10px;padding:14px;background:#fff}
.vc-offer .top{display:flex;align-items:center;gap:8px;margin-bottom:6px}
.vc-offer .top .discount{font-size:13px;font-weight:800;color:#dc2626}
.vc-offer .top .via{font-size:11px;color:#6b7280}
.vc-offer .brand-logo{width:50px;height:24px;object-fit:contain}
.vc-offer .desc{font-size:11px;color:#6b7280;margin-bottom:4px;line-height:1.4}
.vc-offer .hsd{font-size:10px;color:#d97706;font-weight:600}
</style>

@php
    $availableVouchers = $availableVouchers ?? collect();
    $usedRedemptions   = $usedRedemptions ?? collect();
    $expiredVouchers   = $expiredVouchers ?? collect();
    $stats             = $stats ?? ['total' => 0, 'active' => 0, 'expiring_soon' => 0, 'used' => 0];

    $voucherDiscount = function ($coupon) {
        return match ($coupon->type) {
            'percent'  => rtrim(rtrim(number_format((float) $coupon->value, 2, '.', ''), '0'), '.') . '%',
            'shipping' => 'FREE',
            default    => number_format((float) $coupon->value / 1000, 0) . 'K',
        };
    };
    $voucherColor = function ($coupon) {
        return match ($coupon->type) {
            'percent'  => 'teal',
            'shipping' => 'teal',
            default    => 'blue',
        };
    };
@endphp

<div class="accp-wrap">
    @include('partials.storefront.breadcrumb', ['class' => 'accp-crumb', 'separator' => '›', 'items' => [['label' => 'Tài khoản', 'url' => route('customer.account')], ['label' => 'Kho voucher / mã giảm giá của tôi', 'current' => true]]])

    <div class="accp-grid">
        @include('pages.account.partials.sidebar', ['currentSection' => 'vouchers'])

        <main>
            <div style="margin-bottom:20px"><h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0 0 4px">Kho voucher / mã giảm giá của tôi</h2><p style="font-size:13px;color:#6b7280;margin:0">Quản lý và sử dụng các voucher, mã giảm giá đã lưu của bạn.</p></div>

            <div class="accp-stats">
                <div class="accp-stat"><div class="accp-stat-icon teal"><i class="fa fa-ticket"></i></div><div><div class="num">{{ $stats['total'] }}</div><div class="lbl">Tổng voucher</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">{{ $stats['active'] }}</div><div class="lbl">Có thể sử dụng</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon amber"><i class="fa fa-clock-o"></i></div><div><div class="num">{{ $stats['expiring_soon'] }}</div><div class="lbl">Sắp hết hạn</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon blue"><i class="fa fa-history"></i></div><div><div class="num">{{ $stats['used'] }}</div><div class="lbl">Đã sử dụng</div></div></div>
            </div>

            <div class="accp-toolbar">
                <div class="accp-search"><i class="fa fa-search"></i><input type="text" placeholder="Tìm kiếm mã hoặc tên voucher"></div>
                <select class="accp-select"><option>Danh mục áp dụng</option></select>
                <select class="accp-select"><option>Mới nhất</option></select>
            </div>

            <div class="accp-tabs">
                <button class="accp-tab active">Tất cả</button>
                <button class="accp-tab">Có thể dùng</button>
                <button class="accp-tab">Sắp hết hạn</button>
                <button class="accp-tab">Đã dùng</button>
                <button class="accp-tab">Đã hết hạn</button>
            </div>

            @if ($availableVouchers->isEmpty() && $usedRedemptions->isEmpty() && $expiredVouchers->isEmpty())
                <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:32px;text-align:center;color:#6b7280">
                    <i class="fa fa-ticket" style="font-size:32px;color:#cbd5e1;margin-bottom:8px"></i>
                    <div style="font-size:14px;font-weight:600;color:#374151">Bạn chưa có voucher nào</div>
                    <div style="font-size:12px;margin-top:4px">Các mã giảm giá khả dụng sẽ hiển thị tại đây.</div>
                </div>
            @else
            <div class="vc-grid">
                @foreach ($availableVouchers as $coupon)
                <div class="vc-card">
                    <div class="vc-left {{ $voucherColor($coupon) }}">
                        <span class="discount-text">GIẢM</span>
                        <span class="discount-val">{{ $voucherDiscount($coupon) }}</span>
                    </div>
                    <div class="vc-right">
                        <div class="name">{{ $coupon->name }} <span class="accp-badge green">Có thể dùng</span></div>
                        <div class="code-row">
                            <span style="font-size:11px;color:#9ca3af">Mã:</span>
                            <span class="code">{{ $coupon->code }}</span>
                            <button class="copy-btn" onclick="copyVoucher('{{ $coupon->code }}',this)" title="Sao chép mã"><i class="fa fa-clone"></i></button>
                        </div>
                        <div class="meta">
                            @if ($coupon->min_order_total)Đơn tối thiểu: {{ number_format((float) $coupon->min_order_total, 0, ',', '.') }}đ@else Áp dụng mọi đơn @endif
                        </div>
                        <div class="bottom">
                            <span class="hsd">HSD: {{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y') : 'Không giới hạn' }}</span>
                            <button class="vc-apply-btn copy" onclick="copyVoucher('{{ $coupon->code }}',this)">Sao chép mã</button>
                        </div>
                    </div>
                </div>
                @endforeach

                @foreach ($usedRedemptions as $redemption)
                @php $coupon = $redemption->coupon; @endphp
                @if ($coupon)
                <div class="vc-card">
                    <div class="vc-left gray">
                        <span class="discount-text">GIẢM</span>
                        <span class="discount-val">{{ $voucherDiscount($coupon) }}</span>
                    </div>
                    <div class="vc-right">
                        <div class="name">{{ $coupon->name }} <span class="accp-badge gray">Đã sử dụng</span></div>
                        <div class="code-row">
                            <span style="font-size:11px;color:#9ca3af">Mã:</span>
                            <span class="code">{{ $coupon->code }}</span>
                        </div>
                        <div class="meta">
                            Tiết kiệm: {{ number_format((float) $redemption->discount_amount, 0, ',', '.') }}đ
                            @if ($redemption->order) · Đơn #{{ $redemption->order->code }} @endif
                        </div>
                        <div class="bottom">
                            <span class="hsd">Đã dùng ngày {{ optional($redemption->created_at)->format('d/m/Y') }}</span>
                            <span class="vc-apply-btn used">Xem chi tiết</span>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach

                @foreach ($expiredVouchers as $coupon)
                <div class="vc-card">
                    <div class="vc-left gray">
                        <span class="discount-text">GIẢM</span>
                        <span class="discount-val">{{ $voucherDiscount($coupon) }}</span>
                    </div>
                    <div class="vc-right">
                        <div class="name">{{ $coupon->name }} <span class="accp-badge red">Hết hạn</span></div>
                        <div class="code-row">
                            <span style="font-size:11px;color:#9ca3af">Mã:</span>
                            <span class="code">{{ $coupon->code }}</span>
                        </div>
                        <div class="meta">
                            @if ($coupon->min_order_total)Đơn tối thiểu: {{ number_format((float) $coupon->min_order_total, 0, ',', '.') }}đ @endif
                        </div>
                        <div class="bottom">
                            <span class="hsd">HSD: {{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y') : '—' }}</span>
                            <span class="vc-apply-btn expired">Đã hết hạn</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Personalized offers: ẩn cho đến khi có CMS/segment thật để chạy --}}
        </main>

        <aside class="accp-aside">
            <div class="accp-aside-card accp-contact-card">
                <div style="padding:4px 0"><span style="font-size:11px;color:#9ca3af;display:block">Hotline</span><span class="hotline">1900 1234</span><div class="hotline-sub">(8:00 - 21:00 mỗi ngày)</div></div>
                <div style="padding:8px 0;border-top:1px solid #f3f4f6"><span style="font-size:11px;color:#9ca3af;display:block">Email</span><a class="email" href="mailto:support@dangduc.vn">support@dangduc.vn</a></div>
                <div style="padding:8px 0;border-top:1px solid #f3f4f6"><span style="font-size:11px;color:#9ca3af;display:block">Chat trực tuyến</span><span style="font-size:12px;color:#6b7280">Hỗ trợ nhanh chóng 24/7</span></div>
                <button type="button" class="accp-chat-btn" data-chatbot-launch><i class="fa fa-comments"></i> Chat ngay</button>
            </div>
            <div class="accp-aside-card">
                <h4>Mẹo sử dụng voucher</h4>
                <ul class="accp-aside-tips">
                    <li><i class="fa fa-check"></i>Kiểm tra điều kiện áp dụng. Đọc kỹ đơn tối thiểu và sản phẩm áp dụng.</li>
                    <li><i class="fa fa-check"></i>Chú ý thời hạn sử dụng. Voucher hết hạn sẽ không thể sử dụng.</li>
                    <li><i class="fa fa-check"></i>Áp dụng đúng danh mục. Một số voucher chỉ áp dụng theo danh mục.</li>
                    <li><i class="fa fa-check"></i>Không cộng dồn voucher. Mỗi đơn hàng chỉ áp dụng 1 voucher.</li>
                </ul>
            </div>
            <div class="accp-aside-card">
                <h4>Mua sắm an tâm</h4>
                <div class="accp-aside-trust">
                    <div class="accp-aside-trust-item"><i class="fa fa-check-circle"></i><span>100% sản phẩm chính hãng</span></div>
                    <div class="accp-aside-trust-item"><i class="fa fa-shield"></i><span>Bảo hành toàn diện</span></div>
                    <div class="accp-aside-trust-item"><i class="fa fa-lock"></i><span>Thanh toán an toàn</span></div>
                    <div class="accp-aside-trust-item"><i class="fa fa-refresh"></i><span>Đổi trả dễ dàng</span></div>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
function copyVoucher(code,btn){navigator.clipboard.writeText(code).then(function(){var orig=btn.innerHTML;btn.innerHTML='<i class="fa fa-check"></i> Đã sao chép';btn.style.color='#16a34a';setTimeout(function(){btn.innerHTML=orig;btn.style.color=''},2000)});}
</script>
@endsection
