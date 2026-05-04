@extends('layouts.home-tech')
@section('content')

<style>
    .reset-page {
        background: #f5f7f9;
        padding: 24px 0 56px;
        font-family: 'Be Vietnam Pro', 'Inter', sans-serif;
    }

    .reset-container {
        max-width: 1240px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .reset-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13.5px;
        color: #6b7280;
        margin-bottom: 18px;
    }

    .reset-breadcrumb a {
        color: #6b7280;
        text-decoration: none;
        transition: color 0.18s ease;
    }

    .reset-breadcrumb a:hover {
        color: #0f9aa5;
    }

    .reset-breadcrumb .sep {
        color: #cbd5e1;
        font-size: 11px;
    }

    .reset-breadcrumb .current {
        color: #0f9aa5;
        font-weight: 500;
    }

    .reset-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(0, 1fr);
        gap: 24px;
        background: #ffffff;
        border-radius: 22px;
        padding: 36px 36px 36px;
        box-shadow: 0 12px 40px rgba(15, 23, 42, 0.04);
    }

    .reset-card__title {
        font-size: 26px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 8px;
        line-height: 1.25;
    }

    .reset-card__sub {
        font-size: 14.5px;
        color: #64748b;
        margin: 0 0 26px;
        line-height: 1.55;
    }

    .reset-alert {
        margin-bottom: 18px;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 14px;
    }

    .reset-alert--success {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .reset-alert--error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .reset-field {
        margin-bottom: 18px;
    }

    .reset-field__label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .reset-input {
        position: relative;
    }

    .reset-input input {
        width: 100%;
        height: 52px;
        padding: 0 50px 0 18px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        font-size: 14.5px;
        color: #0f172a;
        transition: all 0.18s ease;
        font-family: inherit;
    }

    .reset-input input::placeholder {
        color: #9ca3af;
    }

    .reset-input input:focus {
        outline: none;
        border-color: #0f9aa5;
        box-shadow: 0 0 0 4px rgba(15, 154, 165, 0.12);
    }

    .reset-input__toggle {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 32px;
        height: 32px;
        border: none;
        background: transparent;
        color: #94a3b8;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.18s ease;
    }

    .reset-input__toggle:hover {
        color: #0f9aa5;
        background: #f1f5f9;
    }

    .reset-input__toggle i {
        font-size: 15px;
    }

    .reset-strength {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin: 12px 2px 4px;
    }

    .reset-strength__col {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .reset-strength__bar {
        height: 4px;
        border-radius: 999px;
        background: #e2e8f0;
        transition: background 0.2s ease;
    }

    .reset-strength.is-weak .reset-strength__col:nth-child(1) .reset-strength__bar {
        background: #ef4444;
    }

    .reset-strength.is-medium .reset-strength__col:nth-child(1) .reset-strength__bar,
    .reset-strength.is-medium .reset-strength__col:nth-child(2) .reset-strength__bar {
        background: #f59e0b;
    }

    .reset-strength.is-strong .reset-strength__col .reset-strength__bar {
        background: #10b981;
    }

    .reset-strength__label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        color: #94a3b8;
        font-weight: 500;
    }

    .reset-strength__label::before {
        content: '';
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    .reset-strength__col:nth-child(1) .reset-strength__label { color: #ef4444; }
    .reset-strength__col:nth-child(2) .reset-strength__label { color: #f59e0b; }
    .reset-strength__col:nth-child(3) .reset-strength__label { color: #10b981; }

    .reset-error {
        margin: 8px 2px 0;
        font-size: 13px;
        color: #ef4444;
    }

    .reset-requirements {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px 18px;
        margin: 8px 0 20px;
    }

    .reset-requirements__title {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14.5px;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 12px;
    }

    .reset-requirements__title i {
        color: #10b981;
        font-size: 14px;
    }

    .reset-requirements__list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px 18px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .reset-requirements__list li {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13.5px;
        color: #475569;
        transition: color 0.18s ease;
    }

    .reset-requirements__list li i {
        color: #10b981;
        font-size: 13px;
        transition: all 0.18s ease;
    }

    .reset-requirements__list li.is-pending {
        color: #94a3b8;
    }

    .reset-requirements__list li.is-pending i {
        color: #cbd5e1;
    }

    .reset-checkbox {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin: 4px 0 22px;
        cursor: pointer;
        user-select: none;
    }

    .reset-checkbox input {
        width: 16px;
        height: 16px;
        accent-color: #0f9aa5;
        cursor: pointer;
    }

    .reset-checkbox span {
        font-size: 13.5px;
        color: #334155;
    }

    .reset-checkbox .info-icon {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 1.5px solid #cbd5e1;
        color: #94a3b8;
        font-size: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-style: normal;
        font-weight: 600;
        font-family: serif;
    }

    .reset-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .reset-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        height: 52px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #0f9aa5 0%, #0c828c 100%);
        color: #ffffff;
        font-size: 14.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.18s ease;
        box-shadow: 0 6px 18px rgba(15, 154, 165, 0.28);
        font-family: inherit;
    }

    .reset-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(15, 154, 165, 0.34);
    }

    .reset-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        height: 52px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        color: #334155;
        font-size: 14.5px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.18s ease;
    }

    .reset-back:hover {
        border-color: #0f9aa5;
        color: #0f9aa5;
        text-decoration: none;
    }

    /* ===================== Right side ===================== */
    .reset-side {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .reset-hero {
        position: relative;
        height: 220px;
        border-radius: 18px;
        background: linear-gradient(160deg, #ecfafb 0%, #e6f6f7 60%, #ddf1f3 100%);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .reset-hero svg {
        width: 240px;
        height: auto;
    }

    .reset-features {
        display: flex;
        flex-direction: column;
        gap: 16px;
        padding: 4px 6px;
    }

    .reset-feature {
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .reset-feature__icon {
        flex: 0 0 38px;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #ecfafb;
        color: #0f9aa5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }

    .reset-feature__text strong {
        display: block;
        font-size: 14.5px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .reset-feature__text span {
        font-size: 13px;
        color: #64748b;
        line-height: 1.55;
    }

    .reset-support {
        position: relative;
        background: linear-gradient(135deg, #ecfafb 0%, #e6f6f7 100%);
        border-radius: 16px;
        padding: 20px 22px;
        overflow: hidden;
    }

    .reset-support__title {
        font-size: 14.5px;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 12px;
    }

    .reset-support__row {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13.5px;
        color: #334155;
        margin-bottom: 8px;
    }

    .reset-support__row:last-child {
        margin-bottom: 0;
    }

    .reset-support__row i {
        color: #0f9aa5;
        font-size: 13px;
        width: 16px;
        text-align: center;
    }

    .reset-support__deco {
        position: absolute;
        right: 18px;
        bottom: 14px;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #ffffff;
        color: #0f9aa5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        box-shadow: 0 8px 20px rgba(15, 154, 165, 0.18);
    }

    /* ===================== Articles ===================== */
    .reset-articles {
        margin-top: 28px;
    }

    .reset-articles__title {
        text-align: center;
        font-size: 17px;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 18px;
    }

    .reset-articles__grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
    }

    .reset-article {
        display: flex;
        gap: 16px;
        align-items: center;
        background: #ffffff;
        border-radius: 16px;
        padding: 18px 20px;
        text-decoration: none;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
        transition: all 0.2s ease;
    }

    .reset-article:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        text-decoration: none;
    }

    .reset-article__icon {
        flex: 0 0 64px;
        width: 64px;
        height: 64px;
        border-radius: 14px;
        background: linear-gradient(160deg, #ecfafb 0%, #e6f6f7 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .reset-article__icon svg {
        width: 38px;
        height: auto;
    }

    .reset-article__body {
        flex: 1;
        min-width: 0;
    }

    .reset-article__body h3 {
        font-size: 14.5px;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 4px;
        line-height: 1.35;
    }

    .reset-article__body p {
        font-size: 12.5px;
        color: #64748b;
        margin: 0 0 6px;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .reset-article__more {
        font-size: 12.5px;
        color: #0f9aa5;
        font-weight: 500;
    }

    @media (max-width: 992px) {
        .reset-grid {
            grid-template-columns: 1fr;
            padding: 28px 22px;
        }

        .reset-articles__grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575px) {
        .reset-requirements__list {
            grid-template-columns: 1fr;
        }

        .reset-actions {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="reset-page">
    <div class="reset-container">
        <nav class="reset-breadcrumb" aria-label="breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span class="sep"><i class="fa fa-angle-right"></i></span>
            <a href="{{ url('/tai-khoan') }}">Tài khoản</a>
            <span class="sep"><i class="fa fa-angle-right"></i></span>
            <span class="current">Đặt lại mật khẩu</span>
        </nav>

        <div class="reset-grid">
            <div class="reset-card">
                @if(session()->has('message'))
                    <div class="reset-alert reset-alert--success">{!! session()->get('message') !!}</div>
                @elseif(session()->has('error'))
                    <div class="reset-alert reset-alert--error">{!! session()->get('error') !!}</div>
                @endif

                <h1 class="reset-card__title">Đặt lại mật khẩu mới</h1>
                <p class="reset-card__sub">Tạo mật khẩu mới cho tài khoản của bạn để tiếp tục mua sắm an toàn.</p>

                <form action="{{ url('/reset-new-pass') }}" method="POST" id="resetForm" novalidate>
                    @csrf
                    <input type="hidden" name="email" value="{{ $resetPasswordEmail }}">
                    <input type="hidden" name="token" value="{{ $resetPasswordToken }}">

                    <div class="reset-field">
                        <label class="reset-field__label" for="password_account">Mật khẩu mới</label>
                        <div class="reset-input">
                            <input type="password" id="password_account" name="password_account"
                                   placeholder="Nhập mật khẩu mới" autocomplete="new-password" data-pw="primary">
                            <button type="button" class="reset-input__toggle" data-toggle="#password_account" aria-label="Hiện mật khẩu">
                                <i class="fa fa-eye-slash"></i>
                            </button>
                        </div>
                        <div class="reset-strength" id="resetStrength" aria-hidden="true">
                            <div class="reset-strength__col">
                                <span class="reset-strength__bar"></span>
                                <span class="reset-strength__label">Yếu</span>
                            </div>
                            <div class="reset-strength__col">
                                <span class="reset-strength__bar"></span>
                                <span class="reset-strength__label">Trung bình</span>
                            </div>
                            <div class="reset-strength__col">
                                <span class="reset-strength__bar"></span>
                                <span class="reset-strength__label">Mạnh</span>
                            </div>
                        </div>
                        @error('password_account')
                            <p class="reset-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="reset-field">
                        <label class="reset-field__label" for="password_account_confirmation">Nhập lại mật khẩu mới</label>
                        <div class="reset-input">
                            <input type="password" id="password_account_confirmation" name="password_account_confirmation"
                                   placeholder="Nhập lại mật khẩu mới" autocomplete="new-password" data-pw="confirm">
                            <button type="button" class="reset-input__toggle" data-toggle="#password_account_confirmation" aria-label="Hiện mật khẩu">
                                <i class="fa fa-eye-slash"></i>
                            </button>
                        </div>
                        @error('password_account_confirmation')
                            <p class="reset-error">{{ $message }}</p>
                        @enderror
                        <p class="reset-error" id="confirmError" hidden>Mật khẩu nhập lại không khớp.</p>
                    </div>

                    <div class="reset-requirements">
                        <p class="reset-requirements__title"><i class="fa fa-check-circle"></i> Yêu cầu mật khẩu</p>
                        <ul class="reset-requirements__list" id="resetRules">
                            <li data-rule="length"><i class="fa fa-check-circle"></i> Ít nhất 8 ký tự</li>
                            <li data-rule="digit"><i class="fa fa-check-circle"></i> Ít nhất 1 chữ số (0-9)</li>
                            <li data-rule="upper"><i class="fa fa-check-circle"></i> Ít nhất 1 chữ cái viết hoa</li>
                            <li data-rule="special"><i class="fa fa-check-circle"></i> Ít nhất 1 ký tự đặc biệt (!@#$%^&amp;*)</li>
                            <li data-rule="lower"><i class="fa fa-check-circle"></i> Ít nhất 1 chữ cái viết thường</li>
                        </ul>
                    </div>

                    <label class="reset-checkbox">
                        <input type="checkbox" name="logout_all_devices" value="1">
                        <span>Đăng xuất khỏi tất cả thiết bị khác</span>
                        <i class="info-icon">i</i>
                    </label>

                    <div class="reset-actions">
                        <button type="submit" class="reset-submit">
                            <i class="fa fa-lock"></i>
                            <span>Lưu mật khẩu mới</span>
                        </button>
                        <a href="{{ url('/dang-nhap') }}" class="reset-back">
                            <i class="fa fa-arrow-left"></i>
                            <span>Quay lại đăng nhập</span>
                        </a>
                    </div>
                </form>
            </div>

            <aside class="reset-side">
                <div class="reset-hero">
                    <svg viewBox="0 0 240 200" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <defs>
                            <linearGradient id="shieldGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#1ab2bd"/>
                                <stop offset="100%" stop-color="#0f9aa5"/>
                            </linearGradient>
                            <linearGradient id="bodyGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#0f9aa5"/>
                                <stop offset="100%" stop-color="#0c828c"/>
                            </linearGradient>
                        </defs>

                        <!-- decorative dots -->
                        <circle cx="36" cy="40" r="3" fill="#0f9aa5" opacity="0.35"/>
                        <circle cx="208" cy="60" r="4" fill="#0f9aa5" opacity="0.45"/>
                        <circle cx="40" cy="160" r="3" fill="#0f9aa5" opacity="0.4"/>
                        <circle cx="200" cy="158" r="3.5" fill="#0f9aa5" opacity="0.35"/>
                        <path d="M22 100 L30 100" stroke="#0f9aa5" stroke-width="2" stroke-linecap="round" opacity="0.4"/>
                        <path d="M210 110 L218 110" stroke="#0f9aa5" stroke-width="2" stroke-linecap="round" opacity="0.4"/>
                        <path d="M214 110 L218 106 M214 110 L218 114" stroke="#0f9aa5" stroke-width="2" stroke-linecap="round" opacity="0.4" fill="none"/>

                        <!-- shield -->
                        <path d="M120 26 L172 46 V104 C172 138 150 162 120 174 C90 162 68 138 68 104 V46 Z"
                              fill="url(#shieldGrad)"/>
                        <path d="M120 32 L166 50 V104 C166 134 146 156 120 166 C94 156 74 134 74 104 V50 Z"
                              fill="#0c828c" opacity="0.18"/>

                        <!-- inner panel with password dots -->
                        <rect x="92" y="86" width="56" height="22" rx="6" fill="#ffffff"/>
                        <circle cx="100" cy="97" r="2.4" fill="#0f9aa5"/>
                        <circle cx="108" cy="97" r="2.4" fill="#0f9aa5"/>
                        <circle cx="116" cy="97" r="2.4" fill="#0f9aa5"/>
                        <circle cx="124" cy="97" r="2.4" fill="#0f9aa5"/>
                        <circle cx="132" cy="97" r="2.4" fill="#0f9aa5"/>
                        <circle cx="140" cy="97" r="2.4" fill="#0f9aa5"/>

                        <!-- padlock above panel -->
                        <path d="M104 86 V72 a16 16 0 0 1 32 0 V86" stroke="#ffffff" stroke-width="5" fill="none" stroke-linecap="round"/>

                        <!-- check badge -->
                        <circle cx="172" cy="138" r="14" fill="#10b981"/>
                        <path d="M166 138 L171 143 L179 134" stroke="#ffffff" stroke-width="2.6" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <div class="reset-features">
                    <div class="reset-feature">
                        <div class="reset-feature__icon"><i class="fa fa-shield"></i></div>
                        <div class="reset-feature__text">
                            <strong>Bảo mật tài khoản</strong>
                            <span>Mật khẩu mới giúp bảo vệ tài khoản và thông tin cá nhân của bạn.</span>
                        </div>
                    </div>
                    <div class="reset-feature">
                        <div class="reset-feature__icon"><i class="fa fa-envelope-o"></i></div>
                        <div class="reset-feature__text">
                            <strong>Email đã xác minh</strong>
                            <span>Liên kết đặt lại đã được gửi đến email của bạn và đã được xác minh.</span>
                        </div>
                    </div>
                    <div class="reset-feature">
                        <div class="reset-feature__icon"><i class="fa fa-headphones"></i></div>
                        <div class="reset-feature__text">
                            <strong>Hỗ trợ 24/7</strong>
                            <span>Đội ngũ hỗ trợ luôn sẵn sàng giúp bạn mọi lúc, mọi nơi.</span>
                        </div>
                    </div>
                </div>

                <div class="reset-support">
                    <p class="reset-support__title">Cần hỗ trợ?</p>
                    <div class="reset-support__row"><i class="fa fa-phone"></i> 1900 1234</div>
                    <div class="reset-support__row"><i class="fa fa-envelope-o"></i> support@dangduc.vn</div>
                    <div class="reset-support__deco"><i class="fa fa-headphones"></i></div>
                </div>
            </aside>
        </div>

        <div class="reset-articles">
            <p class="reset-articles__title">Xem thêm bài viết hữu ích</p>
            <div class="reset-articles__grid">
                <a href="{{ url('/bai-viet') }}" class="reset-article">
                    <div class="reset-article__icon">
                        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <rect x="18" y="28" width="28" height="22" rx="4" fill="#0f9aa5"/>
                            <path d="M24 28 V20 a8 8 0 0 1 16 0 V28" stroke="#0f9aa5" stroke-width="3.5" fill="none" stroke-linecap="round"/>
                            <circle cx="32" cy="38" r="3" fill="#ffffff"/>
                            <rect x="30.5" y="40" width="3" height="6" rx="1" fill="#ffffff"/>
                            <circle cx="50" cy="20" r="2" fill="#0f9aa5" opacity="0.5"/>
                            <circle cx="14" cy="50" r="2" fill="#0f9aa5" opacity="0.5"/>
                        </svg>
                    </div>
                    <div class="reset-article__body">
                        <h3>Cách tạo mật khẩu mạnh</h3>
                        <p>Hướng dẫn chi tiết giúp bạn tạo mật khẩu an toàn và khó bị đoán.</p>
                        <span class="reset-article__more">Đọc ngay <i class="fa fa-arrow-right"></i></span>
                    </div>
                </a>

                <a href="{{ url('/bai-viet') }}" class="reset-article">
                    <div class="reset-article__icon">
                        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <rect x="22" y="14" width="20" height="36" rx="4" fill="#0f9aa5"/>
                            <rect x="25" y="18" width="14" height="24" rx="2" fill="#ffffff"/>
                            <circle cx="32" cy="46" r="1.6" fill="#ffffff"/>
                            <path d="M28 26 L31 29 L37 23" stroke="#0f9aa5" stroke-width="2.4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="48" cy="20" r="2" fill="#0f9aa5" opacity="0.5"/>
                            <circle cx="14" cy="42" r="2" fill="#0f9aa5" opacity="0.5"/>
                        </svg>
                    </div>
                    <div class="reset-article__body">
                        <h3>Vì sao nên bật xác thực 2 lớp?</h3>
                        <p>Tăng cường bảo mật tài khoản với lớp bảo vệ thứ hai.</p>
                        <span class="reset-article__more">Đọc ngay <i class="fa fa-arrow-right"></i></span>
                    </div>
                </a>

                <a href="{{ url('/bai-viet') }}" class="reset-article">
                    <div class="reset-article__icon">
                        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M32 12 L52 20 V36 C52 46 44 54 32 58 C20 54 12 46 12 36 V20 Z" fill="#0f9aa5"/>
                            <path d="M24 34 L30 40 L42 28" stroke="#ffffff" stroke-width="3.4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="50" cy="14" r="2" fill="#0f9aa5" opacity="0.5"/>
                            <circle cx="14" cy="46" r="2" fill="#0f9aa5" opacity="0.5"/>
                        </svg>
                    </div>
                    <div class="reset-article__body">
                        <h3>Mẹo bảo vệ tài khoản</h3>
                        <p>Những thói quen đơn giản giúp giữ tài khoản luôn an toàn.</p>
                        <span class="reset-article__more">Đọc ngay <i class="fa fa-arrow-right"></i></span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<script>
    (function () {
        // Eye toggle
        document.querySelectorAll('.reset-input__toggle').forEach((btn) => {
            btn.addEventListener('click', () => {
                const target = document.querySelector(btn.getAttribute('data-toggle'));
                if (!target) return;

                const isHidden = target.type === 'password';
                target.type = isHidden ? 'text' : 'password';

                const icon = btn.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye', isHidden);
                    icon.classList.toggle('fa-eye-slash', !isHidden);
                }
            });
        });

        const primary = document.getElementById('password_account');
        const confirm = document.getElementById('password_account_confirmation');
        const strength = document.getElementById('resetStrength');
        const rules = document.getElementById('resetRules');
        const confirmError = document.getElementById('confirmError');
        const form = document.getElementById('resetForm');

        const ruleChecks = {
            length: (v) => v.length >= 8,
            digit: (v) => /\d/.test(v),
            upper: (v) => /[A-Z]/.test(v),
            special: (v) => /[!@#$%^&*()_+\-={}\[\]:;"'<>,.?/\\|~`]/.test(v),
            lower: (v) => /[a-z]/.test(v),
        };

        function evaluatePassword(value) {
            let passed = 0;

            rules.querySelectorAll('li').forEach((li) => {
                const key = li.getAttribute('data-rule');
                const ok = ruleChecks[key] ? ruleChecks[key](value) : false;
                li.classList.toggle('is-pending', !ok);
                if (ok) passed += 1;
            });

            strength.classList.remove('is-weak', 'is-medium', 'is-strong');

            if (!value) {
                strength.classList.add('is-weak');
                return;
            }

            if (passed <= 2) {
                strength.classList.add('is-weak');
            } else if (passed <= 4) {
                strength.classList.add('is-medium');
            } else {
                strength.classList.add('is-strong');
            }
        }

        function checkConfirmation() {
            if (!confirm.value) {
                confirmError.hidden = true;
                return true;
            }

            const matches = confirm.value === primary.value;
            confirmError.hidden = matches;
            return matches;
        }

        if (primary) {
            evaluatePassword('');
            primary.addEventListener('input', () => {
                evaluatePassword(primary.value);
                if (confirm.value) checkConfirmation();
            });
        }

        if (confirm) {
            confirm.addEventListener('input', checkConfirmation);
        }

        if (form) {
            form.addEventListener('submit', (event) => {
                if (!checkConfirmation()) {
                    event.preventDefault();
                    confirm.focus();
                }
            });
        }
    })();
</script>

@endsection
