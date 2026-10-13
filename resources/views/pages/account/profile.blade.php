@extends('layouts.home-tech')
@section('body_class','accp-page')
@section('content')
@php
    $customerName = $customer->name ?? '';
    $customerEmail = $customer->email ?? '';
    $customerPhone = $customer->phone ?? '';
    $customerInitial = strtoupper(mb_substr($customerName, 0, 1, 'UTF-8'));
    $memberSince = $customer->created_at ? $customer->created_at->format('d/m/Y') : '';
    $loyaltyPoints = (float) ($customer->loyalty_points_balance ?? 0);
    $tierName = $customer->loyaltyTier?->name ?? 'Thành viên';
@endphp
<div class="accp-wrap">
    @include('partials.storefront.breadcrumb', ['class' => 'accp-crumb', 'separator' => '›', 'items' => [['label' => 'Tài khoản', 'url' => url('/account')], ['label' => 'Thông tin tài khoản', 'current' => true]]])
    <div class="accp-grid">
        @include('pages.account.partials.sidebar', ['currentSection' => 'overview', 'customer' => $customer])
        <main>
            <div class="accp-page-title"><h1>Thông tin tài khoản</h1></div>

            {{-- Personal info --}}
            <div class="accp-card" style="padding:24px;margin-bottom:16px">
                <h3 style="margin-bottom:20px"><i class="fa fa-user" style="color:#0d9488;margin-right:6px"></i>Thông tin cá nhân</h3>
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
                    <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#1a5c57,#0d9488);color:#fff;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;position:relative;flex-shrink:0">{{ $customerInitial }}
                        <button style="position:absolute;bottom:0;right:0;width:24px;height:24px;border-radius:50%;background:#fff;border:2px solid #e5e7eb;color:#6b7280;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center"><i class="fa fa-camera"></i></button>
                    </div>
                    <div><div style="font-size:16px;font-weight:700;color:#111827">{{ $customerName }}</div><div style="font-size:12px;color:#6b7280;margin-top:2px">Thành viên từ {{ $memberSince }}</div></div>
                </div>
                <form action="{{ route('customer.account.update') }}" method="POST">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                        <div class="accp-field"><label>Họ và tên</label><input type="text" name="name" value="{{ old('name', $customerName) }}"></div>
                        <div class="accp-field"><label>Email @if($customer->email_verified_at)<span class="accp-verified"><i class="fa fa-check-circle"></i> Đã xác minh</span>@endif</label><input type="email" value="{{ $customerEmail }}" readonly style="background:#f8fafc"></div>
                        <div class="accp-field"><label>Số điện thoại @if($customer->phone_verified_at)<span class="accp-verified"><i class="fa fa-check-circle"></i> Đã xác minh</span>@endif</label><input type="text" name="phone" value="{{ old('phone', $customerPhone) }}"></div>
                        <div class="accp-field"><label>Giới tính</label>
                            <div style="display:flex;gap:16px;height:42px;align-items:center">
                                <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:#374151;cursor:pointer"><input type="radio" name="gender" value="male" {{ ($customer->gender ?? '') === 'male' ? 'checked' : '' }}> Nam</label>
                                <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:#374151;cursor:pointer"><input type="radio" name="gender" value="female" {{ ($customer->gender ?? '') === 'female' ? 'checked' : '' }}> Nữ</label>
                                <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:#374151;cursor:pointer"><input type="radio" name="gender" value="other" {{ !in_array($customer->gender ?? '', ['male', 'female']) ? 'checked' : '' }}> Khác</label>
                            </div>
                        </div>
                        <div class="accp-field"><label>Ngày sinh</label><input type="date" name="birthday" value="{{ $customer->birthday ?? '' }}"></div>
                        <div class="accp-field"><label>Hạng thành viên</label>
                            <div style="display:flex;align-items:center;gap:8px;height:42px"><span style="background:linear-gradient(135deg,#d97706,#f59e0b);color:#fff;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700"><i class="fa fa-star"></i> {{ $tierName }}</span><span style="font-size:12px;color:#6b7280">{{ number_format($loyaltyPoints) }} điểm tích lũy</span></div>
                        </div>
                    </div>
                    <button type="submit" class="accp-btn accp-btn--primary" style="margin-top:16px"><i class="fa fa-save"></i> Cập nhật thông tin</button>
                </form>
            </div>

            {{-- Security --}}
            <div class="accp-card" style="padding:24px;margin-bottom:16px">
                <h3 style="margin-bottom:16px"><i class="fa fa-shield" style="color:#0d9488;margin-right:6px"></i>Bảo mật tài khoản</h3>
                <div style="display:flex;flex-direction:column;gap:14px">
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #f3f4f6">
                        <div><div style="font-size:13px;font-weight:600;color:#111827">Mật khẩu</div><div style="font-size:12px;color:#6b7280">••••••••</div></div>
                        <a href="{{ route('customer.account.change-password.view') }}" class="accp-btn accp-btn--outline accp-btn--sm"><i class="fa fa-pencil"></i> Đổi mật khẩu</a>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #f3f4f6">
                        <div><div style="font-size:13px;font-weight:600;color:#111827">Email xác minh</div><div style="font-size:12px;color:{{ $customer->email_verified_at ? '#16a34a' : '#dc2626' }}"><i class="fa {{ $customer->email_verified_at ? 'fa-check-circle' : 'fa-times-circle' }}"></i> {{ $customer->email_verified_at ? 'Đã xác minh' : 'Chưa xác minh' }}</div></div>
                        @if(!$customer->email_verified_at)
                        <a href="#" class="accp-btn accp-btn--outline accp-btn--sm">Xác minh ngay</a>
                        @endif
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0">
                        <div><div style="font-size:13px;font-weight:600;color:#111827">Phiên đăng nhập</div><div style="font-size:12px;color:#6b7280">Đăng xuất tất cả thiết bị khác</div></div>
                        <form method="POST" action="{{ url('/dang-xuat-khach-hang') }}">
                            @csrf
                            <button type="submit" class="accp-btn accp-btn--outline accp-btn--sm" style="color:#dc2626;border-color:#dc2626"><i class="fa fa-sign-out"></i> Đăng xuất</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Notifications --}}
            <div class="accp-card" style="padding:24px">
                <h3 style="margin-bottom:16px"><i class="fa fa-bell" style="color:#0d9488;margin-right:6px"></i>Thông báo & Ưu tiên</h3>
                @foreach([['l'=>'Email thông báo đơn hàng','on'=>true],['l'=>'SMS khuyến mãi','on'=>false],['l'=>'Thông báo sản phẩm yêu thích','on'=>true],['l'=>'Newsletter hàng tuần','on'=>false]] as $n)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;{{ !$loop->last?'border-bottom:1px solid #f3f4f6;':'' }}">
                    <span style="font-size:13px;color:#374151">{{ $n['l'] }}</span>
                    <label style="position:relative;width:44px;height:24px;display:inline-block"><input type="checkbox" {{ $n['on']?'checked':'' }} style="opacity:0;width:0;height:0"><span style="position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:{{ $n['on']?'#0d9488':'#cbd5e1' }};border-radius:12px;transition:.3s"></span></label>
                </div>
                @endforeach
            </div>
        </main>
        <aside class="accp-aside">
            <div class="accp-aside-card">
                <h4><i class="fa fa-star" style="color:#eab308;margin-right:6px"></i>Tài khoản của bạn</h4>
                <div style="text-align:center;padding:12px 0">
                    <div style="background:linear-gradient(135deg,#d97706,#f59e0b);color:#fff;padding:6px 20px;border-radius:20px;font-size:14px;font-weight:700;display:inline-block"><i class="fa fa-star"></i> {{ $tierName }}</div>
                    <div style="font-size:22px;font-weight:800;color:#111827;margin-top:10px">{{ number_format($loyaltyPoints) }}</div>
                    <div style="font-size:12px;color:#6b7280">Điểm tích lũy</div>
                    <div style="font-size:12px;color:#6b7280;margin-top:8px">Thành viên từ {{ $memberSince }}</div>
                </div>
            </div>
            <div class="accp-aside-card accp-contact-card"><h4><i class="fa fa-headphones" style="color:#0d9488;margin-right:6px"></i>Hỗ trợ</h4><div class="hotline">1900 1234</div><div class="hotline-sub">8:00 - 21:00</div><button type="button" class="accp-chat-btn" data-chatbot-launch><i class="fa fa-comment-o"></i> Chat</button></div>
        </aside>
    </div>
</div>
@endsection
