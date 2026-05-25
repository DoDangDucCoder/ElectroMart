@extends('layouts.home-tech')
@section('content')

<style>
    .forgot-page {
        background: #f5f7f9;
        padding: 28px 0 60px;
    }

    .forgot-container {
        max-width: 1180px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .forgot-breadcrumb {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 22px;
    }

    .forgot-breadcrumb a {
        color: #6b7280;
        text-decoration: none;
    }

    .forgot-breadcrumb a:hover {
        color: #0f9aa5;
    }

    .forgot-breadcrumb .sep {
        display: inline-flex;
        width: 18px;
        height: 18px;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
    }

    .forgot-breadcrumb .current {
        color: #111827;
        font-weight: 500;
    }

    .forgot-card {
        display: grid;
        grid-template-columns: 1fr 1fr;
        background: #ffffff;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(15, 23, 42, 0.06);
    }

    .forgot-card__visual {
        position: relative;
        background: linear-gradient(160deg, #ecfafb 0%, #e6f6f7 60%, #ddf1f3 100%);
        padding: 56px 52px 48px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 640px;
    }

    .forgot-illustration {
        position: relative;
        height: 280px;
        margin-bottom: 24px;
    }

    .forgot-illustration .blob {
        position: absolute;
        inset: 0;
        margin: auto;
        width: 280px;
        height: 280px;
        background: radial-gradient(circle at 50% 50%, rgba(15, 154, 165, 0.08) 0%, rgba(15, 154, 165, 0) 70%);
        border-radius: 50%;
    }

    .forgot-illustration svg {
        position: relative;
        display: block;
        margin: 0 auto;
    }

    .forgot-headline h2 {
        font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
        font-size: 30px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 4px;
        line-height: 1.25;
    }

    .forgot-headline h2 .accent {
        color: #0f9aa5;
        display: block;
    }

    .forgot-headline p {
        font-size: 15px;
        color: #6b7280;
        line-height: 1.6;
        margin: 16px 0 28px;
        max-width: 360px;
    }

    .forgot-features {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .forgot-feature {
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .forgot-feature__icon {
        flex: 0 0 44px;
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: #ffffff;
        color: #0f9aa5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        box-shadow: 0 6px 14px rgba(15, 154, 165, 0.12);
    }

    .forgot-feature__text strong {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 2px;
    }

    .forgot-feature__text span {
        font-size: 13.5px;
        color: #6b7280;
    }

    .forgot-card__form {
        padding: 64px 60px 56px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .forgot-form-title {
        text-align: center;
        font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
        font-size: 24px;
        font-weight: 700;
        color: #111827;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        margin: 0 0 14px;
    }

    .forgot-form-sub {
        text-align: center;
        font-size: 14.5px;
        color: #6b7280;
        line-height: 1.6;
        margin: 0 auto 28px;
        max-width: 380px;
    }

    .forgot-method {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 26px;
    }

    .forgot-method__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 13px 12px;
        border-radius: 12px;
        background: #ffffff;
        border: 1.5px solid #e5e7eb;
        color: #4b5563;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .forgot-method__btn i {
        font-size: 15px;
        color: #9ca3af;
    }

    .forgot-method__btn.is-active {
        border-color: #0f9aa5;
        color: #0f9aa5;
        background: #f0fafb;
        box-shadow: 0 4px 12px rgba(15, 154, 165, 0.12);
    }

    .forgot-method__btn.is-active i {
        color: #0f9aa5;
    }

    .forgot-field {
        margin-bottom: 18px;
    }

    .forgot-field label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 8px;
    }

    .forgot-field label .req {
        color: #ef4444;
        margin-left: 2px;
    }

    .forgot-input {
        position: relative;
    }

    .forgot-input i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 15px;
    }

    .forgot-input input {
        width: 100%;
        height: 52px;
        padding: 0 18px 0 48px;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        font-size: 14.5px;
        color: #111827;
        transition: all 0.2s ease;
    }

    .forgot-input input::placeholder {
        color: #9ca3af;
    }

    .forgot-input input:focus {
        outline: none;
        border-color: #0f9aa5;
        box-shadow: 0 0 0 4px rgba(15, 154, 165, 0.12);
    }

    .forgot-error {
        margin-top: 8px;
        font-size: 13px;
        color: #ef4444;
    }

    .forgot-submit {
        width: 100%;
        height: 54px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #0f9aa5 0%, #0c828c 100%);
        color: #ffffff;
        font-size: 15px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 8px 22px rgba(15, 154, 165, 0.28);
    }

    .forgot-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 28px rgba(15, 154, 165, 0.34);
    }

    .forgot-submit__divider {
        width: 1px;
        height: 22px;
        background: rgba(255, 255, 255, 0.35);
        margin-left: auto;
    }

    .forgot-submit i {
        margin-left: 0;
    }

    .forgot-divider {
        display: flex;
        align-items: center;
        margin: 24px 0 18px;
        color: #9ca3af;
        font-size: 13px;
    }

    .forgot-divider::before,
    .forgot-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e5e7eb;
    }

    .forgot-divider::before {
        margin-right: 14px;
    }

    .forgot-divider::after {
        margin-left: 14px;
    }

    .forgot-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        height: 52px;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        color: #111827;
        font-size: 14.5px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .forgot-back:hover {
        border-color: #0f9aa5;
        color: #0f9aa5;
        text-decoration: none;
    }

    .forgot-back i {
        font-size: 13px;
    }

    .forgot-trust {
        margin-top: 22px;
        background: #ffffff;
        border-radius: 18px;
        padding: 22px 28px;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 22px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
    }

    .forgot-trust__item {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .forgot-trust__icon {
        flex: 0 0 44px;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #ecfafb;
        color: #0f9aa5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .forgot-trust__text strong {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 2px;
    }

    .forgot-trust__text span {
        font-size: 12.5px;
        color: #6b7280;
        line-height: 1.5;
        display: block;
    }

    .forgot-alert {
        margin-bottom: 18px;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 14px;
    }

    .forgot-alert--success {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .forgot-alert--error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    @media (max-width: 992px) {
        .forgot-card {
            grid-template-columns: 1fr;
        }

        .forgot-card__visual {
            min-height: auto;
            padding: 40px 32px;
        }

        .forgot-card__form {
            padding: 40px 32px;
        }

        .forgot-trust {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 575px) {
        .forgot-method {
            grid-template-columns: 1fr;
        }

        .forgot-trust {
            grid-template-columns: 1fr;
        }

        .forgot-card__form,
        .forgot-card__visual {
            padding: 32px 22px;
        }
    }
</style>

<section class="forgot-page">
    <div class="forgot-container">
        @include('partials.storefront.breadcrumb', [
            'class' => 'forgot-breadcrumb',
            'separator' => '›',
            'items' => [
                [
                    'label' => 'Quên mật khẩu',
                    'current' => true,
                ],
            ],
        ])

        <div class="forgot-card">
            <aside class="forgot-card__visual">
                <div>
                    <div class="forgot-illustration">
                        <span class="blob"></span>
                        <svg width="320" height="280" viewBox="0 0 320 280" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <!-- dotted flight path -->
                            <path d="M40 70 C 90 30, 150 110, 210 70" stroke="#0f9aa5" stroke-width="2" stroke-dasharray="3 6" stroke-linecap="round" fill="none" opacity="0.6"/>

                            <!-- paper plane -->
                            <g transform="translate(28 38) rotate(-10)">
                                <path d="M0 18 L46 0 L34 22 L18 18 L14 32 L8 24 Z" fill="#0f9aa5"/>
                                <path d="M18 18 L34 22 L14 32 Z" fill="#0c828c"/>
                            </g>

                            <!-- padlock body -->
                            <rect x="100" y="120" width="120" height="110" rx="14" fill="#0f9aa5"/>
                            <rect x="100" y="120" width="120" height="14" rx="14" fill="#0c828c" opacity="0.5"/>

                            <!-- shackle -->
                            <path d="M122 120 V92 a38 38 0 0 1 76 0 V120" stroke="#0f9aa5" stroke-width="14" fill="none" stroke-linecap="round"/>
                            <path d="M122 120 V92 a38 38 0 0 1 76 0 V120" stroke="#0c828c" stroke-width="3" fill="none" stroke-linecap="round" opacity="0.4"/>

                            <!-- keyhole -->
                            <circle cx="160" cy="170" r="14" fill="#ffffff"/>
                            <rect x="155" y="178" width="10" height="22" rx="3" fill="#ffffff"/>

                            <!-- chat bubble with stars -->
                            <g transform="translate(200 60)">
                                <path d="M0 16 a16 16 0 0 1 16 -16 h64 a16 16 0 0 1 16 16 v28 a16 16 0 0 1 -16 16 h-46 l-14 14 v-14 h-4 a16 16 0 0 1 -16 -16 z" fill="#ffffff" stroke="#0f9aa5" stroke-width="2"/>
                                <text x="14" y="40" font-family="Arial" font-size="20" font-weight="700" fill="#0f9aa5" letter-spacing="3">****</text>
                            </g>

                            <!-- floating dots -->
                            <circle cx="60" cy="200" r="4" fill="#0f9aa5" opacity="0.5"/>
                            <circle cx="240" cy="220" r="6" fill="#0f9aa5" opacity="0.35"/>
                            <circle cx="280" cy="150" r="3" fill="#0f9aa5" opacity="0.45"/>
                        </svg>
                    </div>

                    <div class="forgot-headline">
                        <h2>Quên mật khẩu? <span class="accent">Đừng lo!</span></h2>
                        <p>Chúng tôi sẽ hỗ trợ bạn đặt lại mật khẩu một cách nhanh chóng và bảo mật.</p>
                    </div>
                </div>

                <div class="forgot-features">
                    <div class="forgot-feature">
                        <div class="forgot-feature__icon"><i class="fa fa-envelope-o"></i></div>
                        <div class="forgot-feature__text">
                            <strong>Bảo mật tuyệt đối</strong>
                            <span>Thông tin của bạn được bảo vệ an toàn.</span>
                        </div>
                    </div>
                    <div class="forgot-feature">
                        <div class="forgot-feature__icon"><i class="fa fa-bolt"></i></div>
                        <div class="forgot-feature__text">
                            <strong>Nhanh chóng</strong>
                            <span>Chỉ vài bước để lấy lại mật khẩu.</span>
                        </div>
                    </div>
                    <div class="forgot-feature">
                        <div class="forgot-feature__icon"><i class="fa fa-shield"></i></div>
                        <div class="forgot-feature__text">
                            <strong>Hỗ trợ 24/7</strong>
                            <span>Đội ngũ của chúng tôi luôn sẵn sàng.</span>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="forgot-card__form">
                @if(session()->has('message'))
                    <div class="forgot-alert forgot-alert--success">{{ session()->get('message') }}</div>
                @elseif(session()->has('error'))
                    <div class="forgot-alert forgot-alert--error">{{ session()->get('error') }}</div>
                @endif

                <h1 class="forgot-form-title">Đặt lại mật khẩu</h1>
                <p class="forgot-form-sub">Nhập địa chỉ email hoặc số điện thoại của bạn. Chúng tôi sẽ gửi hướng dẫn đặt lại mật khẩu.</p>

                <div class="forgot-method" role="tablist">
                    <button type="button" class="forgot-method__btn is-active" data-method="email" aria-pressed="true">
                        <i class="fa fa-envelope-o"></i> Qua email
                    </button>
                    <button type="button" class="forgot-method__btn" data-method="phone" aria-pressed="false">
                        <i class="fa fa-phone"></i> Qua số điện thoại
                    </button>
                </div>

                <form action="{{ url('/recover-pass') }}" method="POST" id="forgot-form" novalidate>
                    @csrf
                    <div class="forgot-field" data-field="email">
                        <label for="email_account">Email <span class="req">*</span></label>
                        <div class="forgot-input">
                            <i class="fa fa-envelope-o"></i>
                            <input type="email" id="email_account" name="email_account" value="{{ old('email_account') }}" placeholder="Nhập email đã đăng ký" autocomplete="email">
                        </div>
                        @error('email_account')
                            <p class="forgot-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="forgot-field" data-field="phone" hidden>
                        <label for="phone_account">Số điện thoại <span class="req">*</span></label>
                        <div class="forgot-input">
                            <i class="fa fa-phone"></i>
                            <input type="tel" id="phone_account" name="phone_account" value="{{ old('phone_account') }}" placeholder="Nhập số điện thoại đã đăng ký" autocomplete="tel">
                        </div>
                        @error('phone_account')
                            <p class="forgot-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="forgot-submit">
                        <span>Gửi yêu cầu đặt lại mật khẩu</span>
                        <span class="forgot-submit__divider"></span>
                        <i class="fa fa-envelope"></i>
                    </button>
                </form>

                <div class="forgot-divider">hoặc</div>

                <a href="{{ url('/dang-nhap') }}" class="forgot-back">
                    <i class="fa fa-arrow-left"></i> Quay lại đăng nhập
                </a>
            </div>
        </div>

        <div class="forgot-trust">
            <div class="forgot-trust__item">
                <div class="forgot-trust__icon"><i class="fa fa-truck"></i></div>
                <div class="forgot-trust__text">
                    <strong>Miễn phí giao hàng</strong>
                    <span>Áp dụng cho đơn hàng công nghệ đủ điều kiện</span>
                </div>
            </div>
            <div class="forgot-trust__item">
                <div class="forgot-trust__icon"><i class="fa fa-refresh"></i></div>
                <div class="forgot-trust__text">
                    <strong>Đổi trả hàng</strong>
                    <span>Hỗ trợ đổi trả trong 30 ngày nếu có lỗi do NSX</span>
                </div>
            </div>
            <div class="forgot-trust__item">
                <div class="forgot-trust__icon"><i class="fa fa-shield"></i></div>
                <div class="forgot-trust__text">
                    <strong>Bảo hành chính hãng</strong>
                    <span>Cam kết sản phẩm chính hãng 100%</span>
                </div>
            </div>
            <div class="forgot-trust__item">
                <div class="forgot-trust__icon"><i class="fa fa-headphones"></i></div>
                <div class="forgot-trust__text">
                    <strong>Hỗ trợ tư vấn 24/7</strong>
                    <span>Đội ngũ chuyên viên sẵn sàng hỗ trợ</span>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    (function () {
        const buttons = document.querySelectorAll('.forgot-method__btn');
        const fields = document.querySelectorAll('.forgot-field');

        buttons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const method = btn.getAttribute('data-method');

                buttons.forEach((b) => {
                    const active = b === btn;
                    b.classList.toggle('is-active', active);
                    b.setAttribute('aria-pressed', active ? 'true' : 'false');
                });

                fields.forEach((field) => {
                    const match = field.getAttribute('data-field') === method;
                    field.hidden = !match;
                    const input = field.querySelector('input');
                    if (input) {
                        input.disabled = !match;
                    }
                });
            });
        });
    })();
</script>

@endsection
