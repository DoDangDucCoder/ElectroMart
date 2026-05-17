@extends('layouts.home-tech')
@section('body_class', 'accp-page')
@section('content')
<style>
.pm-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px}
.pm-header h2{font-size:22px;font-weight:800;color:#0f172a;margin:0}
.pm-header p{font-size:13px;color:#6b7280;margin:2px 0 0}
.pm-add-btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:#0d9488;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s;text-decoration:none}
.pm-add-btn:hover{background:#0f766e;color:#fff;text-decoration:none}
.pm-add-btn.is-disabled{background:#e5e7eb;color:#6b7280;cursor:not-allowed;pointer-events:none}
.pm-add-btn i{font-size:14px}
/* Method list */
.pm-list{margin-bottom:16px}
.pm-item{display:flex;align-items:center;gap:14px;padding:16px 18px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;margin-bottom:10px;transition:box-shadow .15s}
.pm-item:hover{box-shadow:0 2px 8px rgba(0,0,0,.04)}
.pm-logo{width:48px;height:32px;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:10px;font-weight:800;color:#fff;overflow:hidden}
.pm-logo.visa{background:#1a1f71}.pm-logo.mc{background:#eb001b}
.pm-logo.momo{background:#ae2070}.pm-logo.zalo{background:#0068ff}
.pm-logo.vcb{background:#006a4e}.pm-logo.cod{background:#f3f4f6;color:#374151;border:1px solid #e5e7eb}
.pm-info{flex:1;min-width:0}
.pm-info .name{font-size:14px;font-weight:700;color:#111827;margin-bottom:1px}
.pm-info .detail{font-size:12px;color:#6b7280}
.pm-badges{display:flex;gap:6px;flex-shrink:0}
.pm-actions{display:flex;gap:6px;flex-shrink:0;flex-wrap:wrap}
.pm-action-btn{padding:5px 14px;border-radius:6px;font-size:11.5px;font-weight:600;cursor:pointer;border:1.5px solid #d1d5db;background:#fff;color:#374151;transition:all .15s;white-space:nowrap}
.pm-action-btn:hover{border-color:#0d9488;color:#0d9488}
.pm-action-btn.default{border-color:#0d9488;color:#0d9488;background:#f0fdfa}
.pm-action-btn.delete{color:#dc2626;border-color:#fca5a5}.pm-action-btn.delete:hover{background:#fef2f2}
.pm-note{display:flex;align-items:center;gap:8px;font-size:11.5px;color:#6b7280;padding:8px 0}
.pm-note i{color:#9ca3af}
/* Offers */
.pm-offers{margin-top:20px}
.pm-offers h4{font-size:15px;font-weight:700;color:#111827;margin:0 0 12px}
.pm-offer-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:10px}
@media(max-width:900px){.pm-offer-grid{grid-template-columns:repeat(2,1fr)}}
.pm-offer{border:1px solid #e5e7eb;border-radius:8px;padding:12px;background:#fff;text-align:center}
.pm-offer .logo{height:24px;display:flex;align-items:center;justify-content:center;margin-bottom:6px;font-size:12px;font-weight:800;color:#6b7280}
.pm-offer .discount{font-size:13px;font-weight:700;color:#111827;margin-bottom:2px}
.pm-offer .desc{font-size:10.5px;color:#6b7280;line-height:1.35;margin-bottom:4px}
.pm-offer .link{font-size:11px;color:#0d9488;text-decoration:none;font-weight:600}
/* Toggle switch */
.pm-toggle{position:relative;width:42px;height:22px;flex-shrink:0;display:inline-block}
.pm-toggle input{opacity:0;width:0;height:0}
.pm-toggle .slider{position:absolute;inset:0;background:#d1d5db;border-radius:11px;cursor:pointer;transition:.3s}
.pm-toggle input:checked+.slider{background:#0d9488}
.pm-toggle .slider:before{content:'';position:absolute;top:2px;left:2px;width:18px;height:18px;background:#fff;border-radius:50%;transition:.3s;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.pm-toggle input:checked+.slider:before{left:22px}
</style>

@php
    $methods = $methods ?? collect();
    $stats   = $stats ?? ['linked' => 0, 'default' => 0, 'wallet' => 0, 'card' => 0];

    $providerLogoClass = function ($method) {
        $provider = strtolower((string) $method->provider);
        if (in_array($provider, ['visa'])) return 'visa';
        if (in_array($provider, ['mastercard'])) return 'mc';
        if (in_array($provider, ['momo'])) return 'momo';
        if (in_array($provider, ['zalopay', 'zalo'])) return 'zalo';
        if (in_array($provider, ['vietcombank', 'vcb'])) return 'vcb';
        if ($method->type === 'cod') return 'cod';
        return 'cod';
    };
    $providerLogoText = function ($method) {
        $provider = strtolower((string) $method->provider);
        $map = [
            'visa' => 'VISA',
            'mastercard' => 'MC',
            'momo' => 'MoMo',
            'zalopay' => 'Zalo',
            'vietcombank' => 'VCB',
            'techcombank' => 'TCB',
            'bidv' => 'BIDV',
            'mbbank' => 'MB',
        ];
        return $map[$provider] ?? ($method->type === 'cod' ? 'COD' : strtoupper(substr($provider, 0, 3)));
    };
@endphp

<div class="accp-wrap">
    @include('partials.storefront.breadcrumb', [
        'class' => 'accp-crumb',
        'separator' => '›',
        'items' => [
            ['label' => 'Tài khoản', 'url' => route('customer.account')],
            ['label' => 'Phương thức thanh toán', 'current' => true],
        ],
    ])

    <div class="accp-grid">
        @include('pages.account.partials.sidebar', ['currentSection' => 'payment-methods'])

        <main>
            <div class="pm-header">
                <div><h2>Phương thức thanh toán của tôi</h2><p>Quản lý các phương thức thanh toán đã lưu để thanh toán nhanh hơn và an toàn hơn.</p></div>
                <span class="pm-add-btn is-disabled" aria-disabled="true" title="Tính năng thêm phương thức thanh toán chưa khả dụng"><i class="fa fa-plus"></i> Thêm phương thức mới</span>
            </div>

            <div class="accp-stats">
                <div class="accp-stat"><div class="accp-stat-icon teal"><i class="fa fa-link"></i></div><div><div class="num">{{ $stats['linked'] }}</div><div class="lbl">Đã liên kết</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">{{ $stats['default'] }}</div><div class="lbl">Phương thức mặc định<br><small style="color:#9ca3af;font-weight:400">Đang sử dụng</small></div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon blue"><i class="fa fa-mobile"></i></div><div><div class="num">{{ $stats['wallet'] }}</div><div class="lbl">Ví điện tử<br><small style="color:#9ca3af;font-weight:400">Đã liên kết</small></div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon purple"><i class="fa fa-credit-card"></i></div><div><div class="num">{{ $stats['card'] }}</div><div class="lbl">Thẻ ngân hàng<br><small style="color:#9ca3af;font-weight:400">Đã liên kết</small></div></div></div>
            </div>

            <h4 style="font-size:14px;font-weight:700;color:#111827;margin:0 0 12px">Phương thức thanh toán đã lưu</h4>
            <div class="pm-list">
                @forelse ($methods as $m)
                <div class="pm-item">
                    <div class="pm-logo {{ $providerLogoClass($m) }}">{{ $providerLogoText($m) }}</div>
                    <div class="pm-info">
                        <div class="name">{{ $m->providerLabel() }} {{ $m->maskedNumber() }}</div>
                        <div class="detail">
                            {{ $m->holder_name ? $m->holder_name : '' }}
                            @if ($m->expiryLabel()) {{ $m->holder_name ? ' · ' : '' }}Hết hạn {{ $m->expiryLabel() }}@endif
                        </div>
                    </div>
                    <div class="pm-badges">
                        @if ($m->is_default)<span class="accp-badge teal">Mặc định</span>@endif
                        @if ($m->is_verified)<span class="accp-badge green">Đã xác minh</span>@endif
                    </div>
                    <div class="pm-actions">
                        @if (!$m->is_default && $m->type !== 'cod')<button class="pm-action-btn">Đặt mặc định</button>@endif
                        <button class="pm-action-btn">Chỉnh sửa</button>
                        @if ($m->type !== 'cod')<button class="pm-action-btn delete">Xóa</button>@endif
                    </div>
                </div>
                @empty
                <div style="background:#fff;border:1px dashed #cbd5e1;border-radius:10px;padding:24px;text-align:center;color:#6b7280">
                    <i class="fa fa-credit-card" style="font-size:24px;color:#cbd5e1;display:block;margin-bottom:6px"></i>
                    <div style="font-size:13px;font-weight:600;color:#374151">Bạn chưa lưu phương thức thanh toán nào</div>
                    <div style="font-size:12px;margin-top:2px">Thêm thẻ hoặc ví điện tử để thanh toán nhanh hơn ở các đơn sau.</div>
                </div>
                @endforelse
                <div class="pm-note" style="margin-top:4px"><i class="fa fa-info-circle"></i><span>Thanh toán khi nhận hàng (COD)</span><span style="margin-left:auto;color:#9ca3af">Không cần lưu</span><span>Có thể chọn tại bước thanh toán</span></div>
            </div>

            {{-- Ưu đãi thanh toán: chờ CMS quản lý banner thật --}}
        </main>

        <aside class="accp-aside">
            <div class="accp-aside-card">
                <h4>Thanh toán an toàn</h4>
                <ul class="accp-aside-tips">
                    <li><i class="fa fa-lock"></i><div>Thông tin thanh toán của bạn được mã hóa và bảo mật tuyệt đối.</div></li>
                    <li><i class="fa fa-shield"></i><div>Bảo mật SSL 256-bit</div></li>
                </ul>
            </div>
            <div class="accp-aside-card">
                <h4>Xác thực 2 lớp (2FA)</h4>
                <p style="font-size:12px;color:#6b7280;margin:0 0 8px">Tăng cường bảo mật cho tài khoản của bạn bằng mã xác thực.</p>
                <div style="display:flex;align-items:center;justify-content:space-between">
                    <span style="font-size:12px;font-weight:600;color:#6b7280">Chưa hỗ trợ</span>
                    <label class="pm-toggle"><input type="checkbox" disabled><span class="slider"></span></label>
                </div>
            </div>
            <div class="accp-aside-card">
                <h4>Mẹo bảo mật khi lưu thẻ</h4>
                <ul class="accp-aside-tips">
                    <li><i class="fa fa-check"></i>Chỉ lưu thẻ trên thiết bị cá nhân.</li>
                    <li><i class="fa fa-check"></i>Không chia sẻ thông tin thẻ với người khác.</li>
                    <li><i class="fa fa-check"></i>Bật xác thực 2 lớp để bảo vệ tài khoản.</li>
                </ul>
                <a href="{{ url('/cham-soc-khach-hang/bao-mat') }}" style="font-size:12px;color:#0d9488;text-decoration:none;font-weight:600">Xem thêm mẹo bảo mật →</a>
            </div>
            <div class="accp-aside-card accp-contact-card">
                <h4>Liên hệ hỗ trợ</h4>
                <p style="font-size:12px;color:#6b7280;margin:0 0 8px">Chúng tôi luôn sẵn sàng hỗ trợ bạn.</p>
                <div style="padding:4px 0"><i class="fa fa-phone" style="color:#0d9488;margin-right:6px"></i><strong style="font-size:13px">Hotline: 1900 1234</strong></div>
                <div style="padding:4px 0"><i class="fa fa-envelope" style="color:#0d9488;margin-right:6px"></i><span style="font-size:13px">Email: support@dangduc.vn</span></div>
                <div style="padding:4px 0"><i class="fa fa-clock-o" style="color:#9ca3af;margin-right:6px"></i><span style="font-size:12px;color:#9ca3af">Thời gian: 8:00 - 21:00 (T2 - CN)</span></div>
                <a href="{{ url('/lien-he') }}" class="accp-chat-btn-outline" style="margin-top:8px;text-decoration:none"><i class="fa fa-comments"></i> Liên hệ hỗ trợ</a>
            </div>
        </aside>
    </div>

    <div class="accp-support-bar">
        <div class="item"><span class="icon-wrap"><i class="fa fa-question-circle"></i></span><div><strong>Cần hỗ trợ về thanh toán?</strong><span> Đội ngũ của chúng tôi luôn sẵn sàng hỗ trợ bạn.</span></div></div>
        <div class="item" style="margin-left:auto"><span class="icon-wrap"><i class="fa fa-phone"></i></span><div><strong>Hotline: 1900 1234</strong><br><small style="color:#9ca3af">8:00 - 21:00 (T2 - CN)</small></div></div>
        <div class="item"><span class="icon-wrap"><i class="fa fa-envelope-o"></i></span><div><strong>Email: support@dangduc.vn</strong><br><small style="color:#9ca3af">Phản hồi trong 15 phút</small></div></div>
        <div class="item"><span class="icon-wrap"><i class="fa fa-comments-o"></i></span><div><strong>Chat trực tuyến</strong><br><small style="color:#9ca3af">Trò chuyện ngay với chúng tôi</small></div></div>
    </div>
</div>
@endsection
