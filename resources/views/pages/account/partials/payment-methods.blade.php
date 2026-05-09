{{-- Header + add new --}}
<div class="pmpage-card pmpage-card--padded pmpage-head">
    <div class="pmpage-head-text">
        <h1>Phương thức thanh toán của tôi</h1>
        <p>Quản lý các phương thức thanh toán đã lưu để thanh toán nhanh hơn và an toàn hơn.</p>
    </div>
    <button type="button" class="pmpage-btn pmpage-btn--primary pmpage-btn--lg" disabled title="Tính năng thêm phương thức thanh toán chưa khả dụng">
        <i class="fa fa-plus"></i> Thêm phương thức mới
    </button>
</div>

{{-- Stats --}}
<div class="pmpage-stats">
    @php
        $savedMethods = collect($savedMethods ?? []);
        $linkedMethods = $savedMethods->count();
        $defaultMethods = $savedMethods->where('is_default', true)->count();
        $walletMethods = $savedMethods->whereIn('type', ['momo', 'zalopay', 'wallet'])->count();
        $cardMethods = $savedMethods->whereIn('type', ['visa', 'mastercard', 'vietcombank', 'card', 'bank_account'])->count();
    @endphp
    <div class="pmpage-stat">
        <div class="pmpage-stat-icon pmpage-stat-icon--blue"><i class="fa fa-credit-card"></i></div>
        <div class="pmpage-stat-body">
            <strong>{{ $linkedMethods }}</strong>
            <span class="pmpage-stat-label">Đã liên kết</span>
            <span class="pmpage-stat-sub">Tổng phương thức đã lưu</span>
        </div>
    </div>
    <div class="pmpage-stat">
        <div class="pmpage-stat-icon pmpage-stat-icon--orange"><i class="fa fa-star"></i></div>
        <div class="pmpage-stat-body">
            <strong>{{ $defaultMethods }}</strong>
            <span class="pmpage-stat-label">Phương thức mặc định</span>
            <span class="pmpage-stat-sub">Đang sử dụng</span>
        </div>
    </div>
    <div class="pmpage-stat">
        <div class="pmpage-stat-icon pmpage-stat-icon--cyan"><i class="fa fa-mobile"></i></div>
        <div class="pmpage-stat-body">
            <strong>{{ $walletMethods }}</strong>
            <span class="pmpage-stat-label">Ví điện tử</span>
            <span class="pmpage-stat-sub">Đã liên kết</span>
        </div>
    </div>
    <div class="pmpage-stat">
        <div class="pmpage-stat-icon pmpage-stat-icon--navy"><i class="fa fa-credit-card-alt"></i></div>
        <div class="pmpage-stat-body">
            <strong>{{ $cardMethods }}</strong>
            <span class="pmpage-stat-label">Thẻ ngân hàng</span>
            <span class="pmpage-stat-sub">Đã liên kết</span>
        </div>
    </div>
</div>

{{-- Saved methods --}}
<div class="pmpage-card pmpage-card--padded pmpage-methods">
    <h3 class="pmpage-methods-title">Phương thức thanh toán đã lưu</h3>

    <div class="pmpage-method-list">
        @forelse ($savedMethods as $method)
            <div class="pmpage-method">
                <div class="pmpage-method-logo pmpage-method-logo--{{ $method['logo'] }}">
                    @include('pages.account.partials.brand-mark', ['brand' => $method['logo']])
                </div>
                <div class="pmpage-method-info">
                    <div class="pmpage-method-title-row">
                        <strong>{{ $method['title'] }}</strong>
                        <span class="pmpage-method-mask">{{ $method['masked'] }}</span>
                    </div>
                    @if (!empty($method['holder']) || !empty($method['extra']))
                        <div class="pmpage-method-meta">
                            @if (!empty($method['holder']))<span>{{ $method['holder'] }}</span>@endif
                            @if (!empty($method['extra']))<span>{{ $method['extra'] }}</span>@endif
                        </div>
                    @endif
                </div>
                <div class="pmpage-method-status">
                    @if (!empty($method['is_default']))
                        <span class="pmpage-badge pmpage-badge--default">Mặc định</span>
                    @endif
                    <span class="pmpage-badge pmpage-badge--ok"><i class="fa fa-check-circle"></i> {{ $method['status_label'] }}</span>
                </div>
                <div class="pmpage-method-actions">
                    @if (!empty($method['set_default']))
                        <button type="button" class="pmpage-btn pmpage-btn--ghost">Đặt mặc định</button>
                    @endif
                    <button type="button" class="pmpage-btn pmpage-btn--ghost">Chỉnh sửa</button>
                    <button type="button" class="pmpage-btn pmpage-btn--danger-ghost">Xóa</button>
                </div>
            </div>
        @empty
            @include('partials.storefront.empty-state', [
                'class' => 'shopw-empty-state--compact',
                'icon' => 'fa fa-credit-card',
                'title' => 'Bạn chưa lưu phương thức thanh toán nào',
                'message' => 'Có thể chọn COD hoặc phương thức thanh toán khác tại bước thanh toán.',
            ])
        @endforelse

        {{-- COD row --}}
        <div class="pmpage-method pmpage-method--cod">
            <div class="pmpage-method-logo pmpage-method-logo--cod">
                <i class="fa fa-archive"></i>
            </div>
            <div class="pmpage-method-info">
                <div class="pmpage-method-title-row">
                    <strong>Thanh toán khi nhận hàng (COD)</strong>
                </div>
                <div class="pmpage-method-meta">
                    <span>Thanh toán bằng tiền mặt khi nhận hàng.</span>
                </div>
            </div>
            <div class="pmpage-method-status">
                <span class="pmpage-badge pmpage-badge--info"><i class="fa fa-info-circle"></i> Không cần lưu</span>
            </div>
            <div class="pmpage-method-actions pmpage-method-actions--note">
                <span>Có thể chọn tại bước thanh toán</span>
            </div>
        </div>
    </div>
</div>
