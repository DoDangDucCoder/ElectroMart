@extends('layouts.home-tech')
@section('body_class','accp-page')
@section('content')
<div class="accp-wrap">
    @include('partials.storefront.breadcrumb', ['class' => 'accp-crumb', 'separator' => '›', 'items' => [['label' => 'Tài khoản', 'url' => route('customer.account')], ['label' => 'Địa chỉ của tôi', 'current' => true]]])
    <div class="accp-grid">
        @include('pages.account.partials.sidebar', ['currentSection' => 'address', 'customer' => $customer])

        <main>
            <div class="accp-page-title"><h1>Địa chỉ của tôi</h1><p>Quản lý địa chỉ giao hàng dùng khi thanh toán.</p></div>

            <div class="accp-card">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:14px;flex-wrap:wrap">
                    <h3 style="margin:0">Danh sách địa chỉ ({{ $addresses->count() }})</h3>
                    <button type="button" class="accp-btn accp-btn--primary accp-btn--sm" onclick="document.getElementById('address-form').scrollIntoView({behavior:'smooth'})"><i class="fa fa-plus"></i> Thêm địa chỉ</button>
                </div>

                <div class="accp-address-list">
                    @forelse ($addresses as $addr)
                        @php
                            $line2 = implode(', ', array_filter([$addr->address_line2, $addr->ward?->name, $addr->province?->name, $addr->city?->name]));
                            $label = $addr->label ?: 'Địa chỉ giao hàng';
                        @endphp
                        <div class="accp-address-item {{ $addr->is_default ? 'is-default' : '' }}">
                            <div class="accp-address-radio"><i class="fa {{ $addr->is_default ? 'fa-check-circle' : 'fa-circle-o' }}"></i></div>
                            <div class="accp-address-body">
                                <div class="accp-address-head">
                                    <strong>{{ $addr->recipient_name }}</strong>
                                    @if($addr->recipient_phone)<span>{{ $addr->recipient_phone }}</span>@endif
                                    @if($addr->is_default)<em>Mặc định</em>@endif
                                </div>
                                <div class="accp-address-text">{{ $addr->address_line1 }}@if($line2)<br>{{ $line2 }}@endif</div>
                                <small><i class="fa fa-map-marker"></i> {{ $label }}</small>
                            </div>
                            <div class="accp-address-actions">
                                <button type="button" class="accp-icon-btn" title="Chỉnh sửa"><i class="fa fa-pencil"></i></button>
                                <button type="button" class="accp-icon-btn danger" title="Xóa"><i class="fa fa-trash-o"></i></button>
                            </div>
                        </div>
                    @empty
                        <div style="border:1px dashed #cbd5e1;border-radius:10px;padding:28px;text-align:center;color:#6b7280">
                            <i class="fa fa-map-marker" style="font-size:32px;color:#cbd5e1;display:block;margin-bottom:8px"></i>
                            Bạn chưa có địa chỉ giao hàng nào.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="accp-card" id="address-form">
                <h3>Thêm địa chỉ mới</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div class="accp-field"><label>Họ và tên người nhận</label><input type="text" placeholder="Nhập họ và tên"></div>
                    <div class="accp-field"><label>Số điện thoại</label><input type="tel" placeholder="Nhập số điện thoại"></div>
                    <div class="accp-field"><label>Tỉnh / Thành phố</label><select><option>Chọn tỉnh / thành phố</option></select></div>
                    <div class="accp-field"><label>Quận / Huyện</label><select><option>Chọn quận / huyện</option></select></div>
                    <div class="accp-field"><label>Phường / Xã</label><select><option>Chọn phường / xã</option></select></div>
                    <div class="accp-field"><label>Loại địa chỉ</label><select><option>Nhà riêng</option><option>Công ty</option><option>Khác</option></select></div>
                    <div class="accp-field" style="grid-column:1/-1"><label>Địa chỉ chi tiết</label><input type="text" placeholder="Số nhà, tên đường, tòa nhà..."></div>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:14px">
                    <button type="button" class="accp-btn accp-btn--outline">Hủy</button>
                    <button type="button" class="accp-btn accp-btn--primary">Lưu địa chỉ</button>
                </div>
            </div>
        </main>

        <aside class="accp-aside">
            <div class="accp-aside-card"><h4>Lưu ý</h4><ul class="accp-aside-tips"><li><i class="fa fa-info-circle"></i>Địa chỉ mặc định sẽ được ưu tiên khi đặt hàng.</li><li><i class="fa fa-info-circle"></i>Vui lòng nhập đầy đủ thông tin để giao hàng chính xác.</li></ul></div>
            <div class="accp-aside-card accp-contact-card"><h4>Cần hỗ trợ?</h4><div class="hotline">1900 1234</div><button type="button" class="accp-chat-btn" data-chatbot-launch><i class="fa fa-comment-o"></i> Chat</button></div>
        </aside>
    </div>
</div>
@endsection
