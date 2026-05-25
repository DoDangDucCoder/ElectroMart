@extends('layouts.home-tech')

@section('body_class', 'g-login-checkout dd-auth-page-body')

@section('content')
<style>
    .dd-auth-shell{
        background:#F4F7FA;
        padding:18px 0 40px;
    }
    .dd-auth-shell .container{max-width:1280px;margin:0 auto;padding:0 16px;}

    .dd-auth-crumbs{
        display:flex;align-items:center;gap:8px;
        font-size:13.5px;color:#6B7280;margin:0 0 18px;
    }
    .dd-auth-crumbs a{color:#6B7280;text-decoration:none;}
    .dd-auth-crumbs a:hover{color:#0FA8AB;}
    .dd-auth-crumbs .sep{color:#9CA3AF;font-size:12px;}
    .dd-auth-crumbs .current{color:#1F2937;}

    .dd-auth-hero{
        text-align:center;margin:0 0 28px;
    }
    .dd-auth-hero h1{
        font-size:30px;letter-spacing:.02em;color:#0F172A;font-weight:700;
        margin:0 0 12px;
    }
    .dd-auth-hero p{
        font-size:15px;color:#64748B;margin:0;
    }

    .dd-auth-flash{margin:0 0 16px;}
    .dd-auth-alert{
        border-radius:12px;padding:12px 16px;font-size:14px;
        border:1px solid transparent;
    }
    .dd-auth-alert--success{background:#E7F5EF;border-color:#BFE6CF;color:#0F5132;}
    .dd-auth-alert--error{background:#FDECEC;border-color:#F5C2C7;color:#842029;}
    .dd-auth-alert ul{margin:0;padding-left:18px;}

    /* main grid */
    .dd-auth-grid{
        display:grid;
        grid-template-columns:minmax(0,1fr) minmax(0,1fr);
        gap:24px;
        align-items:stretch;
    }
    .dd-auth-grid--single{grid-template-columns:minmax(0,640px);justify-content:center;}
    @media (max-width:991px){.dd-auth-grid{grid-template-columns:1fr;}}

    /* card base */
    .dd-auth-card{
        background:#fff;border-radius:18px;padding:32px;
        box-shadow:0 1px 2px rgba(15,23,42,.04);
    }
    .dd-auth-card--form{padding:30px 32px;}
    .dd-auth-card--info{padding:32px;}

    .dd-auth-card-title{
        font-size:18px;letter-spacing:.04em;color:#0F172A;font-weight:700;
        margin:0 0 22px;text-transform:uppercase;
    }

    /* form fields */
    .dd-auth-field{margin:0 0 16px;}
    .dd-auth-field-label{
        display:block;font-size:13.5px;font-weight:500;color:#374151;
        margin:0 0 8px;
    }
    .dd-auth-field-label .req{color:#EF4444;margin-left:2px;}

    .dd-auth-input{
        position:relative;display:flex;align-items:center;
        border:1px solid #E5E7EB;border-radius:10px;background:#fff;
        transition:border-color .15s,box-shadow .15s;
    }
    .dd-auth-input:focus-within{
        border-color:#0FA8AB;box-shadow:0 0 0 3px rgba(15,168,171,.14);
    }
    .dd-auth-input .ipx{
        width:42px;display:flex;align-items:center;justify-content:center;
        color:#9CA3AF;flex-shrink:0;
    }
    .dd-auth-input .ipx i{font-size:15px;}
    .dd-auth-input input{
        flex:1;border:0;outline:0;background:transparent;
        padding:12px 14px 12px 0;font-size:14px;color:#1F2937;
        font-family:inherit;width:100%;
    }
    .dd-auth-input input::placeholder{color:#9CA3AF;}
    .dd-auth-input .isfx{
        width:42px;display:flex;align-items:center;justify-content:center;
        color:#9CA3AF;background:transparent;border:0;cursor:pointer;
    }
    .dd-auth-input .isfx:hover{color:#0FA8AB;}
    .dd-auth-input .isfx i{font-size:14px;}

    .dd-auth-helper{
        display:flex;align-items:center;gap:6px;
        margin:8px 0 0;font-size:12.5px;color:#10B981;
    }
    .dd-auth-helper i{color:#10B981;font-size:13px;}

    /* terms checkbox */
    .dd-auth-terms{
        display:flex;align-items:flex-start;gap:10px;cursor:pointer;
        margin:6px 0 18px;font-size:13.5px;color:#4B5563;line-height:1.5;
    }
    .dd-auth-terms input{
        appearance:none;-webkit-appearance:none;
        width:18px;height:18px;border-radius:4px;border:1.5px solid #D1D5DB;
        background:#fff;cursor:pointer;flex-shrink:0;margin-top:1px;
        position:relative;transition:background .15s,border-color .15s;
    }
    .dd-auth-terms input:checked{background:#0FA8AB;border-color:#0FA8AB;}
    .dd-auth-terms input:checked::after{
        content:"";position:absolute;left:5px;top:1px;width:5px;height:10px;
        border:solid #fff;border-width:0 2px 2px 0;transform:rotate(45deg);
    }
    .dd-auth-terms a{color:#0FA8AB;text-decoration:none;font-weight:500;}
    .dd-auth-terms a:hover{text-decoration:underline;}

    /* meta row (login forgot) */
    .dd-auth-meta{
        display:flex;justify-content:flex-end;align-items:center;
        margin:0 0 14px;
    }
    .dd-auth-meta a{
        font-size:13.5px;color:#0FA8AB;text-decoration:none;font-weight:500;
    }
    .dd-auth-meta a:hover{text-decoration:underline;}

    /* primary submit */
    .dd-auth-submit{
        display:flex;align-items:center;justify-content:center;
        width:100%;padding:13px 18px;border-radius:10px;
        background:#0FA8AB;color:#fff;font-size:15px;font-weight:600;
        border:0;cursor:pointer;transition:background .15s,transform .05s;
    }
    .dd-auth-submit:hover{background:#0d9598;color:#fff;}
    .dd-auth-submit:active{transform:translateY(1px);}

    /* divider */
    .dd-auth-divider{
        position:relative;text-align:center;margin:18px 0 14px;
        font-size:13px;color:#9CA3AF;
    }
    .dd-auth-divider span{background:#fff;padding:0 12px;position:relative;z-index:1;}
    .dd-auth-divider::before{
        content:"";position:absolute;left:0;right:0;top:50%;height:1px;background:#E5E7EB;
    }

    /* socials */
    .dd-auth-socials{
        display:grid;grid-template-columns:repeat(3,1fr);gap:10px;
    }
    .dd-auth-social{
        display:flex;align-items:center;justify-content:center;gap:8px;
        padding:11px 12px;border-radius:10px;background:#fff;
        border:1px solid #E5E7EB;color:#1F2937;font-size:13.5px;font-weight:500;
        cursor:pointer;transition:border-color .15s,background .15s;
        text-decoration:none;
    }
    .dd-auth-social:hover{border-color:#0FA8AB;background:#F9FAFB;color:#1F2937;text-decoration:none;}
    .dd-auth-social svg{flex-shrink:0;}

    .dd-auth-foot{
        text-align:center;font-size:13.5px;color:#6B7280;margin:18px 0 0;
    }
    .dd-auth-foot a{color:#0FA8AB;font-weight:500;text-decoration:none;}
    .dd-auth-foot a:hover{text-decoration:underline;}

    /* info / benefits card (right) */
    .dd-auth-illustration{
        display:block;margin:0 auto 18px;
        width:100%;max-width:280px;height:auto;
    }
    .dd-auth-info-title{
        text-align:center;font-size:16px;letter-spacing:.04em;color:#0F172A;
        font-weight:700;margin:0 0 10px;text-transform:uppercase;
    }
    .dd-auth-info-rule{
        display:block;width:48px;height:3px;background:#0FA8AB;border-radius:2px;
        margin:0 auto 22px;
    }
    .dd-auth-benefits{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:18px;}
    .dd-auth-benefit{display:flex;gap:14px;align-items:flex-start;}
    .dd-auth-benefit-icon{
        width:40px;height:40px;border-radius:50%;background:#EAF8F8;
        display:flex;align-items:center;justify-content:center;
        color:#0FA8AB;flex-shrink:0;
    }
    .dd-auth-benefit-icon i{font-size:16px;}
    .dd-auth-benefit-copy strong{
        display:block;font-size:14.5px;color:#0F172A;font-weight:600;margin-bottom:2px;
    }
    .dd-auth-benefit-copy p{margin:0;font-size:13px;color:#6B7280;line-height:1.5;}

    /* feature strip */
    .dd-auth-feature-strip{
        margin-top:24px;background:#fff;border-radius:18px;padding:22px 28px;
        box-shadow:0 1px 2px rgba(15,23,42,.04);
        display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:24px;
    }
    @media (max-width:991px){.dd-auth-feature-strip{grid-template-columns:repeat(2,1fr);gap:18px;}}
    @media (max-width:575px){.dd-auth-feature-strip{grid-template-columns:1fr;}}

    .dd-auth-feature{display:flex;gap:14px;align-items:center;}
    .dd-auth-feature-icon{
        width:42px;height:42px;border-radius:50%;background:#EAF8F8;
        display:flex;align-items:center;justify-content:center;color:#0FA8AB;flex-shrink:0;
    }
    .dd-auth-feature-icon i{font-size:16px;}
    .dd-auth-feature strong{display:block;font-size:14px;color:#0F172A;font-weight:600;margin-bottom:2px;}
    .dd-auth-feature small{display:block;font-size:12.5px;color:#6B7280;line-height:1.4;}

    /* logged-in state */
    .dd-auth-logged{
        background:#fff;border-radius:18px;padding:32px;
        box-shadow:0 1px 2px rgba(15,23,42,.04);
        display:flex;flex-direction:column;gap:18px;
    }
    .dd-auth-logged-identity{display:flex;align-items:center;gap:16px;}
    .dd-auth-logged-identity img{width:64px;height:64px;border-radius:50%;object-fit:cover;background:#EAF8F8;}
    .dd-auth-logged-identity .copy small{display:block;font-size:12px;color:#6B7280;text-transform:uppercase;letter-spacing:.04em;}
    .dd-auth-logged-identity .copy strong{display:block;font-size:18px;color:#0F172A;margin:2px 0 4px;}
    .dd-auth-logged-identity .copy p{margin:0;font-size:13.5px;color:#6B7280;}
    .dd-auth-logged-actions{display:flex;flex-wrap:wrap;gap:10px;}
    .dd-auth-logged-actions a{padding:10px 16px;border-radius:10px;font-size:14px;text-decoration:none;}
    .dd-auth-logged-actions a.primary{background:#0FA8AB;color:#fff;}
    .dd-auth-logged-actions a.primary:hover{background:#0d9598;}
    .dd-auth-logged-actions a.ghost{background:#EAF8F8;color:#0FA8AB;}
    .dd-auth-logged-actions a.ghost:hover{background:#dceeee;}

    @media (max-width:767px){
        .dd-auth-card,.dd-auth-card--form,.dd-auth-card--info{padding:24px 20px;}
        .dd-auth-hero h1{font-size:24px;}
        .dd-auth-feature-strip{padding:20px;}
    }
</style>

<section class="dd-auth-shell">
    <div class="container">
        @include('partials.storefront.breadcrumb', [
            'class' => 'dd-auth-crumbs',
            'separator' => '›',
            'items' => [
                [
                    'label' => $authPageMode === 'register' ? 'Đăng ký' : 'Đăng nhập',
                    'current' => true,
                ],
            ],
        ])

        <header class="dd-auth-hero">
            <h1>{{ $authPageMode === 'register' ? 'TẠO TÀI KHOẢN MỚI' : 'ĐĂNG NHẬP TÀI KHOẢN' }}</h1>
            <p>
                {{ $authPageMode === 'register'
                    ? 'Đăng ký tài khoản để trải nghiệm mua sắm dễ dàng và nhiều ưu đãi hấp dẫn.'
                    : 'Dùng tài khoản hiện có để tiếp tục đặt hàng và theo dõi lịch sử đơn mua.' }}
            </p>
        </header>

        @if (session()->has('message'))
            <div class="dd-auth-flash"><div class="dd-auth-alert dd-auth-alert--success">{{ session()->get('message') }}</div></div>
        @elseif (session()->has('error'))
            <div class="dd-auth-flash"><div class="dd-auth-alert dd-auth-alert--error">{{ session()->get('error') }}</div></div>
        @endif

        @if ($errors->any())
            <div class="dd-auth-flash">
                <div class="dd-auth-alert dd-auth-alert--error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if ($currentCustomer)
            @php
                $authAvatarUrl = $currentCustomer?->avatar_path
                    ? asset($currentCustomer->avatar_path)
                    : ($storefrontCustomerPicture ?: asset('frontend/images/storefront-account-avatar.svg'));
            @endphp
            <div class="dd-auth-grid dd-auth-grid--single">
                <div class="dd-auth-logged">
                    <div class="dd-auth-logged-identity">
                        <img src="{{ $authAvatarUrl }}" alt="{{ $authCustomerDisplayName }}">
                        <div class="copy">
                            <small>Tài khoản đã xác thực</small>
                            <strong>{{ $authCustomerDisplayName }}</strong>
                            <p>{{ $authLoggedInDescription }}</p>
                            @if ($authCustomerEmail)<p>{{ $authCustomerEmail }}</p>@endif
                            @if ($authCustomerPhone)<p>{{ $authCustomerPhone }}</p>@endif
                        </div>
                    </div>
                    <div class="dd-auth-logged-actions">
                        <a href="{{ $authRedirectUrl }}" class="primary">{{ $authContinueLabel }}</a>
                        <a href="{{ url('/account/orders') }}" class="ghost">Đơn hàng của tôi</a>
                        <form method="POST" action="{{ url('/dang-xuat-khach-hang') }}">
                            @csrf
                            <input type="hidden" name="redirect" value="{{ $authRedirectKey ?? 'login' }}">
                            <button type="submit" class="ghost">Đăng xuất</button>
                        </form>
                    </div>
                </div>
            </div>
        @elseif ($authPageMode === 'register')
            <div class="dd-auth-grid">
                <div class="dd-auth-card dd-auth-card--form">
                    <h2 class="dd-auth-card-title">THÔNG TIN ĐĂNG KÝ</h2>

                    <form action="{{ url('/add-customer') }}" method="POST" id="ddAuthRegisterForm" autocomplete="on" novalidate>
                        @csrf
                        <input type="hidden" name="redirect_target" value="{{ $authRedirectKey ?? 'checkout' }}">
                        <input type="hidden" name="redirect_to" value="{{ $authRedirectUrl ?? url('/checkout') }}">

                        <div class="dd-auth-field">
                            <label class="dd-auth-field-label" for="dd-name">Họ và tên <span class="req">*</span></label>
                            <div class="dd-auth-input">
                                <span class="ipx"><i class="fa fa-user-o"></i></span>
                                <input type="text" id="dd-name" name="customer_name" value="{{ old('customer_name') }}" placeholder="Nhập họ và tên của bạn" required>
                            </div>
                        </div>

                        <div class="dd-auth-field">
                            <label class="dd-auth-field-label" for="dd-email">Email <span class="req">*</span></label>
                            <div class="dd-auth-input">
                                <span class="ipx"><i class="fa fa-envelope-o"></i></span>
                                <input type="email" id="dd-email" name="customer_email" value="{{ old('customer_email') }}" placeholder="Nhập email của bạn" required>
                            </div>
                        </div>

                        <div class="dd-auth-field">
                            <label class="dd-auth-field-label" for="dd-phone">Số điện thoại <span class="req">*</span></label>
                            <div class="dd-auth-input">
                                <span class="ipx"><i class="fa fa-phone"></i></span>
                                <input type="tel" id="dd-phone" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="Nhập số điện thoại" required>
                            </div>
                        </div>

                        <div class="dd-auth-field">
                            <label class="dd-auth-field-label" for="dd-password">Mật khẩu <span class="req">*</span></label>
                            <div class="dd-auth-input">
                                <span class="ipx"><i class="fa fa-lock"></i></span>
                                <input type="password" id="dd-password" name="customer_password" placeholder="Nhập mật khẩu" required data-dd-pwd>
                                <button type="button" class="isfx" data-dd-toggle aria-label="Hiện mật khẩu">
                                    <i class="fa fa-eye-slash"></i>
                                </button>
                            </div>
                            <p class="dd-auth-helper">
                                <i class="fa fa-shield"></i>
                                Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.
                            </p>
                        </div>

                        <div class="dd-auth-field">
                            <label class="dd-auth-field-label" for="dd-password-confirm">Xác nhận mật khẩu <span class="req">*</span></label>
                            <div class="dd-auth-input">
                                <span class="ipx"><i class="fa fa-lock"></i></span>
                                <input type="password" id="dd-password-confirm" name="customer_password_confirmation" placeholder="Nhập lại mật khẩu" required data-dd-pwd>
                                <button type="button" class="isfx" data-dd-toggle aria-label="Hiện mật khẩu">
                                    <i class="fa fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        <label class="dd-auth-terms">
                            <input type="checkbox" name="agree_terms" value="1" required>
                            <span>
                                Tôi đồng ý với
                                <a href="{{ url('/dieu-khoan-su-dung') }}" target="_blank" rel="noopener">Điều khoản sử dụng</a>
                                và
                                <a href="{{ url('/cham-soc-khach-hang/bao-mat') }}" target="_blank" rel="noopener">Chính sách bảo mật</a>
                                của DangDuc.
                            </span>
                        </label>

                        <button type="submit" class="dd-auth-submit">Đăng ký tài khoản</button>

                        <div class="dd-auth-divider"><span>hoặc đăng ký với</span></div>

                        <div class="dd-auth-socials">
                            <button type="button" class="dd-auth-social">
                                <svg width="18" height="18" viewBox="0 0 48 48" aria-hidden="true">
                                    <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3c-1.6 4.7-6 8-11.3 8-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.4 6.1 29.5 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.4-.4-3.5z"/>
                                    <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 16.1 19 13 24 13c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.4 6.1 29.5 4 24 4c-7.5 0-13.9 4.1-17.7 10.7z"/>
                                    <path fill="#4CAF50" d="M24 44c5.3 0 10.2-2 13.9-5.3l-6.4-5.4c-2.1 1.5-4.7 2.4-7.5 2.4-5.3 0-9.7-3.4-11.3-8.1l-6.6 5.1C9.9 39.8 16.4 44 24 44z"/>
                                    <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.7 2-2 3.7-3.6 4.9l6.4 5.4C42 35.6 44 30.2 44 24c0-1.3-.1-2.4-.4-3.5z"/>
                                </svg>
                                <span>Google</span>
                            </button>
                            <button type="button" class="dd-auth-social">
                                <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill="#1877F2" d="M24 12.073C24 5.404 18.627 0 12 0S0 5.404 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.412c0-3.018 1.792-4.685 4.533-4.685 1.313 0 2.686.235 2.686.235v2.967h-1.513c-1.49 0-1.955.93-1.955 1.886v2.265h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
                                </svg>
                                <span>Facebook</span>
                            </button>
                            <button type="button" class="dd-auth-social">
                                <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill="#000" d="M17.564 12.65c-.027-2.789 2.279-4.123 2.382-4.187-1.297-1.897-3.318-2.157-4.038-2.187-1.722-.174-3.358 1.012-4.231 1.012-.872 0-2.219-.987-3.65-.96-1.879.027-3.612 1.092-4.58 2.776-1.951 3.382-.499 8.387 1.402 11.137.93 1.346 2.04 2.857 3.498 2.803 1.404-.057 1.934-.91 3.63-.91 1.696 0 2.176.91 3.66.881 1.51-.027 2.469-1.371 3.394-2.722 1.069-1.561 1.51-3.073 1.537-3.151-.034-.014-2.951-1.131-2.978-4.492zM14.84 4.582c.764-.93 1.281-2.218 1.139-3.504-1.103.045-2.443.736-3.232 1.659-.704.812-1.32 2.115-1.156 3.376 1.231.094 2.485-.62 3.249-1.531z"/>
                                </svg>
                                <span>Apple</span>
                            </button>
                        </div>

                        <p class="dd-auth-foot">
                            Đã có tài khoản? <a href="{{ $authLoginUrl }}">Đăng nhập ngay</a>
                        </p>
                    </form>
                </div>

                <aside class="dd-auth-card dd-auth-card--info">
                    <svg class="dd-auth-illustration" viewBox="0 0 320 240" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <defs>
                            <linearGradient id="ddAuthBg" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#E6FFFB"/>
                                <stop offset="1" stop-color="#CFFAFE"/>
                            </linearGradient>
                        </defs>
                        <circle cx="160" cy="120" r="108" fill="url(#ddAuthBg)"/>
                        <g transform="translate(60,40)">
                            <rect x="14" y="6" width="140" height="170" rx="14" fill="#fff" stroke="#5EEAD4" stroke-width="2"/>
                            <rect x="56" y="0" width="56" height="20" rx="6" fill="#0FA8AB"/>
                            <circle cx="84" cy="62" r="22" fill="#5EEAD4"/>
                            <path d="M76 62 l6 6 l12 -12" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                            <rect x="34" y="100" width="100" height="8" rx="4" fill="#CFFAFE"/>
                            <rect x="34" y="116" width="80" height="8" rx="4" fill="#CFFAFE"/>
                            <rect x="34" y="132" width="92" height="8" rx="4" fill="#CFFAFE"/>
                            <rect x="34" y="150" width="64" height="8" rx="4" fill="#CFFAFE"/>
                        </g>
                        <g transform="translate(206,138)">
                            <path d="M0 14 h52 l-4 42 a6 6 0 0 1 -6 6 h-32 a6 6 0 0 1 -6 -6 z" fill="#0FA8AB"/>
                            <path d="M12 14 v-4 a14 14 0 0 1 28 0 v4" stroke="#0E7490" stroke-width="3" fill="none" stroke-linecap="round"/>
                        </g>
                        <g transform="translate(218,46)">
                            <circle cx="16" cy="16" r="16" fill="#22C55E"/>
                            <path d="M9 16 l5 5 l9 -10" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        </g>
                    </svg>

                    <h3 class="dd-auth-info-title">KHI BẠN ĐĂNG KÝ TÀI KHOẢN</h3>
                    <span class="dd-auth-info-rule" aria-hidden="true"></span>

                    <ul class="dd-auth-benefits">
                        <li class="dd-auth-benefit">
                            <span class="dd-auth-benefit-icon"><i class="fa fa-shopping-cart"></i></span>
                            <div class="dd-auth-benefit-copy">
                                <strong>Mua sắm nhanh chóng</strong>
                                <p>Lưu thông tin và đặt hàng dễ dàng chỉ với vài thao tác.</p>
                            </div>
                        </li>
                        <li class="dd-auth-benefit">
                            <span class="dd-auth-benefit-icon"><i class="fa fa-truck"></i></span>
                            <div class="dd-auth-benefit-copy">
                                <strong>Theo dõi đơn hàng</strong>
                                <p>Kiểm tra trạng thái đơn hàng mọi lúc, mọi nơi.</p>
                            </div>
                        </li>
                        <li class="dd-auth-benefit">
                            <span class="dd-auth-benefit-icon"><i class="fa fa-tag"></i></span>
                            <div class="dd-auth-benefit-copy">
                                <strong>Nhận ưu đãi độc quyền</strong>
                                <p>Nhận thông tin khuyến mãi và voucher riêng dành cho thành viên.</p>
                            </div>
                        </li>
                        <li class="dd-auth-benefit">
                            <span class="dd-auth-benefit-icon"><i class="fa fa-shield"></i></span>
                            <div class="dd-auth-benefit-copy">
                                <strong>Bảo hành dễ dàng</strong>
                                <p>Quản lý sản phẩm, bảo hành và hỗ trợ kỹ thuật nhanh chóng.</p>
                            </div>
                        </li>
                        <li class="dd-auth-benefit">
                            <span class="dd-auth-benefit-icon"><i class="fa fa-gift"></i></span>
                            <div class="dd-auth-benefit-copy">
                                <strong>Tích điểm &amp; đổi quà</strong>
                                <p>Tích điểm khi mua sắm và đổi lấy nhiều phần quà hấp dẫn.</p>
                            </div>
                        </li>
                    </ul>
                </aside>
            </div>
        @else
            <div class="dd-auth-grid dd-auth-grid--single">
                <div class="dd-auth-card dd-auth-card--form">
                    <h2 class="dd-auth-card-title">THÔNG TIN ĐĂNG NHẬP</h2>

                    <form action="{{ url('/login-customer') }}" method="POST" autocomplete="on">
                        @csrf
                        <input type="hidden" name="redirect_target" value="{{ $authRedirectKey ?? 'checkout' }}">
                        <input type="hidden" name="redirect_to" value="{{ $authRedirectUrl ?? url('/checkout') }}">

                        <div class="dd-auth-field">
                            <label class="dd-auth-field-label" for="dd-login-email">Email <span class="req">*</span></label>
                            <div class="dd-auth-input">
                                <span class="ipx"><i class="fa fa-envelope-o"></i></span>
                                <input type="email" id="dd-login-email" name="email_account" value="{{ old('email_account') }}" placeholder="Nhập email của bạn" required autofocus>
                            </div>
                        </div>

                        <div class="dd-auth-field">
                            <label class="dd-auth-field-label" for="dd-login-password">Mật khẩu <span class="req">*</span></label>
                            <div class="dd-auth-input">
                                <span class="ipx"><i class="fa fa-lock"></i></span>
                                <input type="password" id="dd-login-password" name="password_account" placeholder="Nhập mật khẩu" required data-dd-pwd>
                                <button type="button" class="isfx" data-dd-toggle aria-label="Hiện mật khẩu">
                                    <i class="fa fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="dd-auth-meta">
                            <a href="{{ url('/quen-mat-khau') }}">Quên mật khẩu?</a>
                        </div>

                        <button type="submit" class="dd-auth-submit">Đăng nhập</button>

                        <p class="dd-auth-foot">
                            Chưa có tài khoản? <a href="{{ $authRegisterUrl }}">Đăng ký ngay</a>
                        </p>
                    </form>
                </div>
            </div>
        @endif

        <div class="dd-auth-feature-strip">
            <div class="dd-auth-feature">
                <span class="dd-auth-feature-icon"><i class="fa fa-truck"></i></span>
                <div>
                    <strong>Miễn phí giao hàng</strong>
                    <small>Áp dụng cho đơn hàng công nghệ đủ điều kiện</small>
                </div>
            </div>
            <div class="dd-auth-feature">
                <span class="dd-auth-feature-icon"><i class="fa fa-refresh"></i></span>
                <div>
                    <strong>Đổi trả hàng</strong>
                    <small>Hỗ trợ đổi trả trong 30 ngày nếu có lỗi do NSX</small>
                </div>
            </div>
            <div class="dd-auth-feature">
                <span class="dd-auth-feature-icon"><i class="fa fa-shield"></i></span>
                <div>
                    <strong>Bảo hành chính hãng</strong>
                    <small>Cam kết sản phẩm chính hãng 100%</small>
                </div>
            </div>
            <div class="dd-auth-feature">
                <span class="dd-auth-feature-icon"><i class="fa fa-headphones"></i></span>
                <div>
                    <strong>Hỗ trợ tư vấn 24/7</strong>
                    <small>Đội ngũ chuyên viên sẵn sàng hỗ trợ</small>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    (function () {
        document.querySelectorAll('[data-dd-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = btn.parentElement.querySelector('[data-dd-pwd]');
                if (!input) return;
                var icon = btn.querySelector('i');
                var isPwd = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPwd ? 'text' : 'password');
                btn.setAttribute('aria-label', isPwd ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
                if (icon) {
                    icon.classList.toggle('fa-eye', isPwd);
                    icon.classList.toggle('fa-eye-slash', !isPwd);
                }
            });
        });

        var registerForm = document.getElementById('ddAuthRegisterForm');
        if (registerForm) {
            registerForm.addEventListener('submit', function (event) {
                var pwd = document.getElementById('dd-password');
                var confirm = document.getElementById('dd-password-confirm');
                if (pwd && confirm && pwd.value !== confirm.value) {
                    event.preventDefault();
                    alert('Mật khẩu xác nhận không khớp.');
                    confirm.focus();
                    return;
                }
            });
        }
    })();
</script>
@endsection
