@extends('layouts.home-tech')
@section('content')

<style>
    .otp-page {
        background: #f5f7f9;
        padding: 26px 0 60px;
    }

    .otp-container {
        max-width: 1180px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .otp-breadcrumb {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 22px;
    }

    .otp-breadcrumb a {
        color: #6b7280;
        text-decoration: none;
    }

    .otp-breadcrumb a:hover {
        color: #0f9aa5;
    }

    .otp-breadcrumb .sep {
        display: inline-flex;
        width: 18px;
        height: 18px;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
    }

    .otp-breadcrumb .current {
        color: #0f9aa5;
        font-weight: 500;
    }

    /* ============= MAIN CARD ============= */
    .otp-card {
        background: #f0fafb;
        border-radius: 22px;
        padding: 40px 44px;
        display: grid;
        grid-template-columns: minmax(0, 1.18fr) minmax(0, 1fr);
        gap: 56px;
        align-items: stretch;
    }

    .otp-form-col {
        display: flex;
        flex-direction: column;
    }

    .otp-form-head {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 14px;
    }

    .otp-form-head__icon {
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(15, 154, 165, 0.14);
        color: #0f9aa5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .otp-form-head h1 {
        font-family: 'Be Vietnam Pro', sans-serif;
        font-size: 26px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        margin: 4px 0 0;
    }

    .otp-form-sub {
        font-size: 14.5px;
        color: #6b7280;
        line-height: 1.65;
        margin: 0 0 24px;
        max-width: 520px;
    }

    .otp-form-sub strong {
        color: #0f172a;
        font-weight: 500;
    }

    .otp-label {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 12px;
    }

    .otp-inputs {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
        margin-bottom: 18px;
    }

    .otp-inputs input {
        width: 100%;
        height: 72px;
        text-align: center;
        font-family: 'Be Vietnam Pro', sans-serif;
        font-size: 30px;
        font-weight: 600;
        color: #0f172a;
        border: 1.5px solid #d6dde4;
        border-radius: 14px;
        background: #ffffff;
        transition: all 0.18s ease;
        caret-color: #0f9aa5;
    }

    .otp-inputs input:focus {
        outline: none;
        border-color: #0f9aa5;
        box-shadow: 0 0 0 4px rgba(15, 154, 165, 0.14);
    }

    .otp-inputs input.is-filled {
        border-color: #0f9aa5;
        color: #0f172a;
    }

    .otp-countdown {
        font-size: 14px;
        color: #6b7280;
        margin: 0 0 14px;
    }

    .otp-countdown strong {
        color: #0f9aa5;
        font-weight: 600;
        margin-left: 4px;
    }

    .otp-resend {
        position: relative;
        margin-bottom: 18px;
    }

    .otp-resend input {
        width: 100%;
        height: 50px;
        padding: 0 130px 0 18px;
        border: 1.5px solid #e1e7ec;
        border-radius: 12px;
        background: #ffffff;
        font-size: 14px;
        color: #6b7280;
    }

    .otp-resend input:focus {
        outline: none;
        border-color: #0f9aa5;
    }

    .otp-resend__btn {
        position: absolute;
        right: 6px;
        top: 6px;
        height: 38px;
        padding: 0 14px;
        border: none;
        border-radius: 8px;
        background: transparent;
        color: #0f9aa5;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .otp-resend__btn:hover {
        background: rgba(15, 154, 165, 0.08);
    }

    .otp-resend__btn i {
        font-size: 13px;
    }

    .otp-trust {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #0f172a;
        margin-bottom: 22px;
        cursor: pointer;
        user-select: none;
    }

    .otp-trust input[type="checkbox"] {
        appearance: none;
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        border: 1.5px solid #cdd5dc;
        border-radius: 5px;
        background: #ffffff;
        position: relative;
        cursor: pointer;
        flex: 0 0 18px;
        transition: all 0.15s ease;
    }

    .otp-trust input[type="checkbox"]:checked {
        background: #0f9aa5;
        border-color: #0f9aa5;
    }

    .otp-trust input[type="checkbox"]:checked::after {
        content: '';
        position: absolute;
        left: 5px;
        top: 1px;
        width: 5px;
        height: 10px;
        border: solid #ffffff;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .otp-trust__info {
        color: #9ca3af;
        font-size: 14px;
        cursor: help;
    }

    .otp-actions {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 18px;
    }

    .otp-btn {
        width: 100%;
        height: 50px;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.18s ease;
        border: 1.5px solid transparent;
    }

    .otp-btn--primary {
        background: #0f9aa5;
        color: #ffffff;
        box-shadow: 0 6px 18px rgba(15, 154, 165, 0.24);
    }

    .otp-btn--primary:hover {
        background: #0c828c;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(15, 154, 165, 0.32);
    }

    .otp-btn--ghost {
        background: #ffffff;
        color: #0f172a;
        border-color: #d6dde4;
    }

    .otp-btn--ghost:hover {
        border-color: #0f9aa5;
        color: #0f9aa5;
        text-decoration: none;
    }

    .otp-change {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        align-self: center;
        color: #0f9aa5;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        padding: 4px 8px;
        border-radius: 8px;
        transition: background 0.15s ease;
    }

    .otp-change:hover {
        background: rgba(15, 154, 165, 0.08);
        color: #0c828c;
        text-decoration: none;
    }

    .otp-change__icon {
        font-size: 16px;
    }

    .otp-change__chev {
        font-size: 12px;
        margin-left: 2px;
    }

    /* ============= RIGHT COLUMN ============= */
    .otp-info-col {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .otp-illustration {
        position: relative;
        height: 220px;
    }

    .otp-illustration svg {
        position: absolute;
        inset: 0;
        margin: auto;
        display: block;
    }

    .otp-info-list {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .otp-info-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .otp-info-item__icon {
        flex: 0 0 44px;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(15, 154, 165, 0.12);
        color: #0f9aa5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .otp-info-item__text strong {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .otp-info-item__text span {
        font-size: 13.5px;
        color: #6b7280;
        line-height: 1.55;
        display: block;
    }

    .otp-support {
        margin-top: auto;
        background: #ffffff;
        border-radius: 16px;
        padding: 20px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        box-shadow: 0 6px 22px rgba(15, 23, 42, 0.06);
    }

    .otp-support__info strong {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .otp-support__row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #4b5563;
        margin-bottom: 4px;
    }

    .otp-support__row i {
        color: #0f9aa5;
        font-size: 13px;
        width: 14px;
    }

    .otp-support__visual {
        flex: 0 0 auto;
        width: 64px;
        height: 64px;
    }

    /* ============= BOTTOM TIP CARDS ============= */
    .otp-tips {
        margin-top: 22px;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
    }

    .otp-tip {
        background: #ffffff;
        border-radius: 16px;
        padding: 22px 24px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
    }

    .otp-tip__visual {
        flex: 0 0 64px;
        width: 64px;
        height: 64px;
    }

    .otp-tip__visual svg {
        width: 100%;
        height: 100%;
        display: block;
    }

    .otp-tip__body strong {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .otp-tip__body p {
        font-size: 13.5px;
        color: #6b7280;
        line-height: 1.55;
        margin: 0 0 8px;
    }

    .otp-tip__link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #0f9aa5;
        font-size: 13.5px;
        font-weight: 500;
        text-decoration: none;
    }

    .otp-tip__link:hover {
        color: #0c828c;
        text-decoration: none;
    }

    /* ============= RESPONSIVE ============= */
    @media (max-width: 992px) {
        .otp-card {
            grid-template-columns: 1fr;
            gap: 36px;
            padding: 32px 28px;
        }

        .otp-tips {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575px) {
        .otp-inputs {
            gap: 8px;
        }

        .otp-inputs input {
            height: 58px;
            font-size: 24px;
            border-radius: 10px;
        }

        .otp-card {
            padding: 24px 20px;
        }

        .otp-form-head h1 {
            font-size: 22px;
        }

        .otp-support {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<section class="otp-page">
    <div class="otp-container">
        <nav class="otp-breadcrumb" aria-label="breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span class="sep"><i class="fa fa-angle-right"></i></span>
            <a href="{{ url('/account') }}">Tài khoản</a>
            <span class="sep"><i class="fa fa-angle-right"></i></span>
            <span class="current">Xác thực OTP</span>
        </nav>

        <div class="otp-card">
            {{-- ============== LEFT: FORM ============== --}}
            <div class="otp-form-col">
                <div class="otp-form-head">
                    <div class="otp-form-head__icon">
                        <i class="fa fa-shield"></i>
                    </div>
                    <h1>Xác thực OTP / mã xác nhận</h1>
                </div>

                <p class="otp-form-sub">
                    Chúng tôi đã gửi mã xác nhận đến email <strong>{{ $email ?? 'nguyenvanan@gmail.com' }}</strong>
                    và số điện thoại <strong>{{ $phoneMasked ?? '*** *** 678' }}</strong>. Vui lòng nhập mã để tiếp tục.
                </p>

                <form action="{{ url('/xac-thuc-otp') }}" method="POST" id="otp-form" autocomplete="off" novalidate>
                    @csrf

                    <p class="otp-label">Nhập mã OTP</p>
                    <div class="otp-inputs" data-otp-inputs>
                        <input type="text" inputmode="numeric" maxlength="1" value="4" data-otp-index="0">
                        <input type="text" inputmode="numeric" maxlength="1" value="8" data-otp-index="1">
                        <input type="text" inputmode="numeric" maxlength="1" value="2" data-otp-index="2">
                        <input type="text" inputmode="numeric" maxlength="1" value="1" data-otp-index="3">
                        <input type="text" inputmode="numeric" maxlength="1" value="5" data-otp-index="4">
                        <input type="text" inputmode="numeric" maxlength="1" value="9" data-otp-index="5">
                    </div>
                    <input type="hidden" name="otp" id="otp-hidden">

                    <p class="otp-countdown">
                        Mã sẽ hết hạn sau:
                        <strong data-otp-timer>01:24</strong>
                    </p>

                    <div class="otp-resend">
                        <input type="text" placeholder="Chưa nhận được mã?" readonly>
                        <button type="button" class="otp-resend__btn" data-otp-resend>
                            Gửi lại mã <i class="fa fa-refresh"></i>
                        </button>
                    </div>

                    <label class="otp-trust">
                        <input type="checkbox" name="trust_device" value="1" checked>
                        <span>Tin cậy thiết bị này trong 30 ngày</span>
                        <i class="fa fa-info-circle otp-trust__info" title="Thiết bị tin cậy sẽ không cần xác thực OTP trong 30 ngày"></i>
                    </label>

                    <div class="otp-actions">
                        <button type="submit" class="otp-btn otp-btn--primary">Xác nhận</button>
                        <a href="{{ url()->previous() }}" class="otp-btn otp-btn--ghost">
                            <i class="fa fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </form>

                <a href="{{ url('/quen-mat-khau') }}" class="otp-change">
                    <i class="fa fa-envelope-o otp-change__icon"></i>
                    <span>Đổi email / số điện thoại nhận mã</span>
                    <i class="fa fa-angle-right otp-change__chev"></i>
                </a>
            </div>

            {{-- ============== RIGHT: INFO ============== --}}
            <aside class="otp-info-col">
                <div class="otp-illustration" aria-hidden="true">
                    <svg width="320" height="220" viewBox="0 0 320 220" fill="none" xmlns="http://www.w3.org/2000/svg">
                        {{-- decorative leaves --}}
                        <g opacity="0.85">
                            <path d="M40 130 q -10 -28 18 -50 q 22 -16 46 -8 q -4 22 -22 38 q -22 18 -42 20 z" fill="#7ed8de" opacity="0.55"/>
                            <path d="M58 110 q 4 16 22 24" stroke="#0f9aa5" stroke-width="1.4" fill="none" opacity="0.55"/>
                            <path d="M280 60 q 14 -8 30 4 q 6 22 -10 36 q -22 -2 -30 -18 q -4 -12 10 -22 z" fill="#7ed8de" opacity="0.55"/>
                        </g>

                        {{-- shield behind phone --}}
                        <g transform="translate(60 24)">
                            <path d="M50 0 L96 14 V62 q0 36 -46 64 q-46 -28 -46 -64 V14 Z" fill="#0f9aa5" opacity="0.18"/>
                            <path d="M50 6 L90 18 V60 q0 30 -40 56 q-40 -26 -40 -56 V18 Z" fill="#0f9aa5"/>
                            <path d="M34 60 l12 12 24 -28" stroke="#ffffff" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        </g>

                        {{-- envelope behind --}}
                        <g transform="translate(180 130)">
                            <rect x="0" y="0" width="80" height="56" rx="8" fill="#ffffff" stroke="#0f9aa5" stroke-width="2"/>
                            <path d="M2 6 L40 32 L78 6" stroke="#0f9aa5" stroke-width="2" fill="none"/>
                        </g>

                        {{-- phone --}}
                        <g transform="translate(160 30)">
                            <rect x="0" y="0" width="120" height="180" rx="20" fill="#ffffff" stroke="#cbe9ec" stroke-width="2"/>
                            <rect x="8" y="10" width="104" height="160" rx="14" fill="#0f9aa5"/>
                            <rect x="44" y="14" width="32" height="6" rx="3" fill="#0c828c"/>
                            <text x="60" y="100" text-anchor="middle" font-family="'Be Vietnam Pro', Arial, sans-serif" font-size="22" font-weight="700" fill="#ffffff" letter-spacing="2">482159</text>
                            {{-- 6 dots --}}
                            <g transform="translate(20 120)">
                                <circle cx="6" cy="6" r="4" fill="#ffffff" opacity="0.85"/>
                                <circle cx="22" cy="6" r="4" fill="#ffffff" opacity="0.85"/>
                                <circle cx="38" cy="6" r="4" fill="#ffffff" opacity="0.85"/>
                                <circle cx="54" cy="6" r="4" fill="#ffffff" opacity="0.85"/>
                                <circle cx="70" cy="6" r="4" fill="#ffffff" opacity="0.85"/>
                                <circle cx="86" cy="6" r="4" fill="#ffffff" opacity="0.55"/>
                            </g>
                            {{-- check badge --}}
                            <g transform="translate(86 142)">
                                <circle cx="12" cy="12" r="14" fill="#ffffff"/>
                                <path d="M6 12 l4 4 8 -8" stroke="#0f9aa5" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                            </g>
                        </g>

                        {{-- floating sparks --}}
                        <circle cx="44" cy="34" r="3" fill="#0f9aa5" opacity="0.45"/>
                        <circle cx="300" cy="190" r="4" fill="#0f9aa5" opacity="0.35"/>
                        <circle cx="300" cy="150" r="2.5" fill="#0f9aa5" opacity="0.5"/>
                    </svg>
                </div>

                <div class="otp-info-list">
                    <div class="otp-info-item">
                        <div class="otp-info-item__icon"><i class="fa fa-shield"></i></div>
                        <div class="otp-info-item__text">
                            <strong>Bảo mật tài khoản</strong>
                            <span>Mã OTP giúp xác minh chính chủ và tăng cường an toàn cho tài khoản của bạn.</span>
                        </div>
                    </div>
                    <div class="otp-info-item">
                        <div class="otp-info-item__icon"><i class="fa fa-headphones"></i></div>
                        <div class="otp-info-item__text">
                            <strong>Hỗ trợ 24/7</strong>
                            <span>Nếu gặp khó khăn, đội ngũ DangDuc luôn sẵn sàng hỗ trợ bạn.</span>
                        </div>
                    </div>
                </div>

                <div class="otp-support">
                    <div class="otp-support__info">
                        <strong>Cần hỗ trợ?</strong>
                        <div class="otp-support__row"><i class="fa fa-phone"></i> 1900 1234</div>
                        <div class="otp-support__row"><i class="fa fa-envelope-o"></i> support@dangduc.vn</div>
                    </div>
                    <div class="otp-support__visual" aria-hidden="true">
                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 36 v-6 a18 18 0 0 1 36 0 v6" stroke="#0f9aa5" stroke-width="3" stroke-linecap="round" fill="none"/>
                            <rect x="10" y="34" width="14" height="18" rx="4" fill="#0f9aa5"/>
                            <rect x="40" y="34" width="14" height="18" rx="4" fill="#0f9aa5"/>
                            <rect x="26" y="50" width="18" height="6" rx="3" fill="#0f9aa5"/>
                            <circle cx="48" cy="56" r="3" fill="#0f9aa5"/>
                        </svg>
                    </div>
                </div>
            </aside>
        </div>

        {{-- ============== BOTTOM TIPS ============== --}}
        <div class="otp-tips">
            <div class="otp-tip">
                <div class="otp-tip__visual" aria-hidden="true">
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="6" y="8" width="52" height="48" rx="10" fill="#e6f5f7"/>
                        <path d="M32 18 L48 24 V36 q0 12 -16 18 q-16 -6 -16 -18 V24 Z" fill="#0f9aa5"/>
                        <rect x="26" y="32" width="12" height="12" rx="2" fill="#ffffff"/>
                        <path d="M28 32 v-4 a4 4 0 0 1 8 0 v4" stroke="#ffffff" stroke-width="2" fill="none"/>
                    </svg>
                </div>
                <div class="otp-tip__body">
                    <strong>Cách bảo vệ tài khoản của bạn</strong>
                    <p>Áp dụng các biện pháp bảo mật để giữ an toàn cho tài khoản và thông tin cá nhân.</p>
                    <a href="#" class="otp-tip__link">Đọc ngay <i class="fa fa-angle-right"></i></a>
                </div>
            </div>

            <div class="otp-tip">
                <div class="otp-tip__visual" aria-hidden="true">
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="6" y="8" width="52" height="48" rx="10" fill="#e6f5f7"/>
                        <rect x="22" y="14" width="20" height="36" rx="4" fill="#ffffff" stroke="#0f9aa5" stroke-width="2"/>
                        <rect x="26" y="20" width="12" height="18" rx="2" fill="#0f9aa5"/>
                        <circle cx="32" cy="44" r="2" fill="#0f9aa5"/>
                        <path d="M44 22 q4 -2 8 0" stroke="#0f9aa5" stroke-width="1.8" fill="none" stroke-linecap="round"/>
                        <path d="M46 28 q3 -1 6 0" stroke="#0f9aa5" stroke-width="1.8" fill="none" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="otp-tip__body">
                    <strong>Vì sao nên bật xác thực 2 lớp?</strong>
                    <p>Tăng cường bảo mật tài khoản với lớp bảo vệ thứ hai.</p>
                    <a href="#" class="otp-tip__link">Đọc ngay <i class="fa fa-angle-right"></i></a>
                </div>
            </div>

            <div class="otp-tip">
                <div class="otp-tip__visual" aria-hidden="true">
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="6" y="8" width="52" height="48" rx="10" fill="#e6f5f7"/>
                        <path d="M14 32 L52 16 L42 50 L32 38 L14 32 Z" fill="#0f9aa5"/>
                        <path d="M32 38 L42 50 L36 40 Z" fill="#0c828c"/>
                        <path d="M32 38 L52 16" stroke="#ffffff" stroke-width="1.4" fill="none"/>
                    </svg>
                </div>
                <div class="otp-tip__body">
                    <strong>Mẹo nhận mã OTP nhanh hơn</strong>
                    <p>Giúp bạn nhận mã nhanh chóng và tránh lỗi thường gặp.</p>
                    <a href="#" class="otp-tip__link">Đọc ngay <i class="fa fa-angle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    (function () {
        const inputs = Array.from(document.querySelectorAll('[data-otp-inputs] input'));
        const hidden = document.getElementById('otp-hidden');
        const timerEl = document.querySelector('[data-otp-timer]');
        const resendBtn = document.querySelector('[data-otp-resend]');

        const syncHidden = () => {
            if (hidden) {
                hidden.value = inputs.map((i) => i.value).join('');
            }
        };

        const markFilled = () => {
            inputs.forEach((i) => i.classList.toggle('is-filled', !!i.value));
        };

        inputs.forEach((input, idx) => {
            input.addEventListener('input', (e) => {
                const v = (e.target.value || '').replace(/\D/g, '').slice(0, 1);
                e.target.value = v;
                if (v && idx < inputs.length - 1) {
                    inputs[idx + 1].focus();
                    inputs[idx + 1].select();
                }
                markFilled();
                syncHidden();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && idx > 0) {
                    inputs[idx - 1].focus();
                    inputs[idx - 1].select();
                } else if (e.key === 'ArrowLeft' && idx > 0) {
                    inputs[idx - 1].focus();
                    inputs[idx - 1].select();
                } else if (e.key === 'ArrowRight' && idx < inputs.length - 1) {
                    inputs[idx + 1].focus();
                    inputs[idx + 1].select();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const data = (e.clipboardData || window.clipboardData).getData('text') || '';
                const digits = data.replace(/\D/g, '').slice(0, inputs.length).split('');
                digits.forEach((d, i) => {
                    if (inputs[i]) inputs[i].value = d;
                });
                const next = Math.min(digits.length, inputs.length - 1);
                if (inputs[next]) inputs[next].focus();
                markFilled();
                syncHidden();
            });

            input.addEventListener('focus', () => input.select());
        });

        markFilled();
        syncHidden();

        // Countdown 1:24 -> 0:00
        let remaining = 84;
        const tick = () => {
            if (!timerEl) return;
            const m = String(Math.floor(remaining / 60)).padStart(2, '0');
            const s = String(remaining % 60).padStart(2, '0');
            timerEl.textContent = `${m}:${s}`;
            if (remaining <= 0) {
                clearInterval(timerId);
                if (resendBtn) resendBtn.disabled = false;
                return;
            }
            remaining -= 1;
        };
        tick();
        const timerId = window.setInterval(tick, 1000);

        if (resendBtn) {
            resendBtn.addEventListener('click', () => {
                remaining = 84;
                inputs.forEach((i) => { i.value = ''; i.classList.remove('is-filled'); });
                if (inputs[0]) inputs[0].focus();
                syncHidden();
            });
        }
    })();
</script>

@endsection
