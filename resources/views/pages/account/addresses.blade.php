@extends('layouts.home-tech')

@section('body_class', 'minimal-layout acc-portal-body')

@push('styles')
<style>
    :root {
        --acc-bg: #f5f7fb;
        --acc-surface: #ffffff;
        --acc-border: #e6eaf2;
        --acc-border-soft: #eef1f7;
        --acc-text: #0f172a;
        --acc-text-2: #475569;
        --acc-text-3: #94a3b8;
        --acc-primary: #2563eb;
        --acc-primary-soft: #eef4ff;
        --acc-primary-strong: #1d4ed8;
        --acc-danger: #ef4444;
        --acc-warning: #f59e0b;
        --acc-radius: 14px;
        --acc-radius-sm: 10px;
        --acc-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 6px 18px rgba(15, 23, 42, .04);
    }

    body.acc-portal-body {
        background: var(--acc-bg);
        font-family: 'Inter', system-ui, -apple-system, "Segoe UI", sans-serif;
        color: var(--acc-text);
        margin: 0;
        padding: 0;
    }

    body.acc-portal-body * { box-sizing: border-box; }

    .acc-portal a { text-decoration: none; color: inherit; }
    .acc-portal button { font-family: inherit; cursor: pointer; }

    /* ---------- Top bar ---------- */
    .acc-topbar {
        background: #fff;
        border-bottom: 1px solid var(--acc-border);
        position: sticky;
        top: 0;
        z-index: 30;
    }

    .acc-topbar-inner {
        display: flex;
        align-items: center;
        gap: 24px;
        padding: 14px 28px;
        max-width: 1440px;
        margin: 0 auto;
    }

    .acc-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
        min-width: 220px;
    }

    .acc-brand-logo {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ff8a00, #ff5a1f);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 16px;
        box-shadow: 0 4px 12px rgba(255, 90, 31, .35);
    }

    .acc-brand-name {
        font-weight: 800;
        font-size: 20px;
        color: #0f172a;
        letter-spacing: -.2px;
    }

    .acc-menu-btn {
        width: 40px;
        height: 40px;
        border: 1px solid var(--acc-border);
        background: #fff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #475569;
        font-size: 16px;
    }

    .acc-search {
        flex: 1;
        position: relative;
        max-width: 720px;
    }

    .acc-search input {
        width: 100%;
        height: 44px;
        border: 1px solid var(--acc-border);
        background: #f8fafc;
        border-radius: 12px;
        padding: 0 48px 0 18px;
        font-size: 14px;
        color: var(--acc-text);
        outline: none;
        transition: .15s;
    }

    .acc-search input::placeholder { color: var(--acc-text-3); }
    .acc-search input:focus { border-color: var(--acc-primary); background: #fff; box-shadow: 0 0 0 4px rgba(37, 99, 235, .1); }

    .acc-search-btn {
        position: absolute;
        right: 6px;
        top: 6px;
        width: 32px;
        height: 32px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: var(--acc-text-2);
    }

    .acc-actions {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-shrink: 0;
    }

    .acc-icon-btn {
        position: relative;
        width: 42px;
        height: 42px;
        border: 1px solid var(--acc-border);
        background: #fff;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #475569;
        font-size: 16px;
    }

    .acc-icon-btn:hover { color: var(--acc-primary); border-color: #c7d6f5; }

    .acc-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        min-width: 20px;
        height: 20px;
        padding: 0 5px;
        border-radius: 999px;
        background: var(--acc-danger);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
    }

    .acc-user {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 4px 14px 4px 6px;
        border-radius: 999px;
        border: 1px solid var(--acc-border);
        background: #fff;
        cursor: pointer;
    }

    .acc-user:hover { border-color: #c7d6f5; }

    .acc-user-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #cbd5f5, #94a3b8);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
    }

    .acc-user-avatar img { width: 100%; height: 100%; object-fit: cover; }

    .acc-user-meta { line-height: 1.2; }
    .acc-user-name { font-size: 14px; font-weight: 700; color: var(--acc-text); }
    .acc-user-sub { font-size: 12px; color: var(--acc-text-3); }
    .acc-user-caret { color: var(--acc-text-3); font-size: 11px; margin-left: 4px; }

    /* ---------- Shell ---------- */
    .acc-shell {
        max-width: 1440px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 24px;
        padding: 24px 28px 60px;
        align-items: start;
    }

    /* ---------- Sidebar ---------- */
    .acc-sidebar {
        position: sticky;
        top: 84px;
        background: #fff;
        border: 1px solid var(--acc-border);
        border-radius: var(--acc-radius);
        box-shadow: var(--acc-shadow);
        overflow: hidden;
    }

    .acc-nav { list-style: none; padding: 10px 0; margin: 0; }

    .acc-nav-item a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 18px;
        font-size: 14px;
        color: #475569;
        font-weight: 500;
        border-left: 3px solid transparent;
        transition: .15s;
    }

    .acc-nav-item a:hover {
        background: #f8fafc;
        color: var(--acc-primary);
    }

    .acc-nav-item.active a {
        background: var(--acc-primary-soft);
        color: var(--acc-primary);
        font-weight: 700;
        border-left-color: var(--acc-primary);
    }

    .acc-nav-item.danger a { color: var(--acc-danger); }
    .acc-nav-item.danger a:hover { background: #fef2f2; color: var(--acc-danger); }

    .acc-nav-icon {
        width: 22px;
        text-align: center;
        font-size: 15px;
        color: inherit;
    }

    .acc-nav-divider {
        height: 1px;
        background: var(--acc-border-soft);
        margin: 8px 14px;
    }

    .acc-nav-badge {
        margin-left: auto;
        background: #fee2e2;
        color: var(--acc-danger);
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 999px;
    }

    .acc-nav-item.active .acc-nav-badge {
        background: rgba(239, 68, 68, .1);
    }

    /* ---- Promo card under sidebar ---- */
    .acc-promo {
        margin-top: 18px;
        background: linear-gradient(155deg, #1d4ed8 0%, #2563eb 50%, #1e3a8a 100%);
        color: #fff;
        border-radius: var(--acc-radius);
        padding: 20px 18px 0;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 28px rgba(29, 78, 216, .25);
    }

    .acc-promo::before {
        content: "";
        position: absolute;
        top: -40px;
        right: -40px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .08);
    }

    .acc-promo-title { font-size: 15px; font-weight: 600; line-height: 1.3; }
    .acc-promo-title strong { display: block; font-size: 22px; font-weight: 800; margin-top: 2px; }
    .acc-promo-text { font-size: 12px; color: rgba(255, 255, 255, .85); margin-top: 8px; line-height: 1.45; }

    .acc-promo-cta {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 14px;
        padding: 7px 14px;
        background: #fff;
        color: var(--acc-primary);
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .acc-promo-truck {
        display: block;
        margin: 14px auto -4px;
        width: 140px;
        max-width: 100%;
    }

    /* ---------- Main ---------- */
    .acc-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--acc-text-3);
        margin-bottom: 14px;
    }

    .acc-breadcrumb a { color: var(--acc-text-3); }
    .acc-breadcrumb a:hover { color: var(--acc-primary); }
    .acc-breadcrumb .sep { color: #cbd5e1; }
    .acc-breadcrumb .current { color: var(--acc-text); font-weight: 600; }

    .acc-page-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 22px;
    }

    .acc-page-title {
        font-size: 26px;
        font-weight: 800;
        color: var(--acc-text);
        margin: 0;
        letter-spacing: -.3px;
    }

    .acc-page-sub {
        font-size: 14px;
        color: var(--acc-text-2);
        margin-top: 6px;
    }

    .acc-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        border: 1px solid transparent;
        background: #fff;
        color: var(--acc-text);
        transition: .15s;
        white-space: nowrap;
    }

    .acc-btn-primary { background: var(--acc-primary); color: #fff; }
    .acc-btn-primary:hover { background: var(--acc-primary-strong); }
    .acc-btn-ghost { background: #fff; border-color: var(--acc-border); color: var(--acc-text-2); }
    .acc-btn-ghost:hover { border-color: var(--acc-primary); color: var(--acc-primary); }

    .acc-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 20px;
        align-items: start;
    }

    .acc-card {
        background: #fff;
        border: 1px solid var(--acc-border);
        border-radius: var(--acc-radius);
        box-shadow: var(--acc-shadow);
    }

    .acc-card-head {
        padding: 18px 22px 14px;
    }

    .acc-card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--acc-text);
        margin: 0;
    }

    /* ---- Address list cards ---- */
    .acc-addr-list {
        padding: 0 22px 18px;
        display: grid;
        gap: 14px;
    }

    .acc-addr {
        display: grid;
        grid-template-columns: 28px 1fr auto;
        gap: 16px;
        align-items: flex-start;
        padding: 18px 18px 16px;
        border: 1.5px solid var(--acc-border);
        border-radius: 12px;
        background: #fff;
        transition: .15s;
        position: relative;
    }

    .acc-addr.is-selected {
        border-color: var(--acc-primary);
        background: linear-gradient(180deg, #f7faff 0%, #ffffff 100%);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .08);
    }

    .acc-addr:hover { border-color: #c7d6f5; }

    .acc-radio {
        appearance: none;
        -webkit-appearance: none;
        width: 22px;
        height: 22px;
        border: 2px solid #cbd5e1;
        border-radius: 50%;
        margin: 2px 0 0;
        position: relative;
        cursor: pointer;
        transition: .15s;
        background: #fff;
        flex-shrink: 0;
    }

    .acc-radio:checked {
        border-color: var(--acc-primary);
        background: var(--acc-primary);
    }

    .acc-radio:checked::after {
        content: "";
        position: absolute;
        inset: 4px;
        border-radius: 50%;
        background: #fff;
    }

    .acc-addr-body { min-width: 0; }

    .acc-addr-row1 {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 4px;
    }

    .acc-default-tag {
        display: inline-flex;
        align-items: center;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .8px;
        padding: 4px 10px;
        border-radius: 4px;
        color: #be123c;
        background:
            repeating-linear-gradient(135deg,
                #ffe4e6 0 6px,
                #fecdd3 6px 10px);
        border: 1px dashed #fda4af;
        text-transform: uppercase;
    }

    .acc-addr-name { font-size: 15px; font-weight: 700; color: var(--acc-text); }
    .acc-addr-phone { font-size: 14px; color: var(--acc-text-2); padding-left: 12px; border-left: 1px solid var(--acc-border); margin-left: 4px; }

    .acc-addr-text {
        font-size: 14px;
        color: var(--acc-text-2);
        line-height: 1.55;
        margin: 6px 0 12px;
    }

    .acc-addr-tags { display: flex; gap: 8px; flex-wrap: wrap; }

    .acc-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .acc-tag i { font-size: 11px; }
    .acc-tag.tag-default { background: #eff6ff; color: var(--acc-primary); border-color: #bfdbfe; }

    .acc-addr-actions { display: flex; gap: 8px; }

    .acc-addr-action {
        width: 38px;
        height: 38px;
        border: 1px solid var(--acc-border);
        border-radius: 10px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--acc-text-2);
        transition: .15s;
    }

    .acc-addr-action:hover { color: var(--acc-primary); border-color: var(--acc-primary); }
    .acc-addr-action.danger:hover { color: var(--acc-danger); border-color: var(--acc-danger); }

    /* ---- Right info column ---- */
    .acc-info {
        background: #fff;
        border: 1px solid var(--acc-border);
        border-radius: var(--acc-radius);
        padding: 20px 20px 18px;
        box-shadow: var(--acc-shadow);
    }

    .acc-info + .acc-info { margin-top: 16px; }

    .acc-info-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }

    .acc-info-head .ic {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: var(--acc-primary-soft);
        color: var(--acc-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .acc-info-head h4 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: var(--acc-text);
    }

    .acc-info-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        gap: 12px;
    }

    .acc-info-list li {
        display: flex;
        gap: 10px;
        font-size: 13px;
        color: var(--acc-text-2);
        line-height: 1.55;
    }

    .acc-info-list li .dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: var(--acc-primary-soft);
        color: var(--acc-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .acc-support-text { font-size: 13px; color: var(--acc-text-2); line-height: 1.55; margin-bottom: 14px; }

    .acc-support-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        border-radius: 10px;
        background: var(--acc-primary-soft);
        color: var(--acc-primary);
        font-size: 13px;
        font-weight: 600;
        border: 1px solid #dbe7ff;
    }

    /* ---- Form ---- */
    .acc-form {
        background: #fff;
        border: 1px solid var(--acc-border);
        border-radius: var(--acc-radius);
        box-shadow: var(--acc-shadow);
        margin-top: 22px;
        padding: 22px 24px 24px;
    }

    .acc-form-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--acc-text);
        margin: 0 0 18px;
    }

    .acc-form-grid {
        display: grid;
        gap: 16px 18px;
        grid-template-columns: 1fr 1fr;
    }

    .acc-form-grid.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
    .acc-form-row.full { grid-column: 1 / -1; }

    .acc-field { display: flex; flex-direction: column; gap: 6px; }

    .acc-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--acc-text);
    }

    .acc-label .req { color: var(--acc-danger); }

    .acc-input,
    .acc-select {
        height: 44px;
        border: 1px solid var(--acc-border);
        background: #fff;
        border-radius: 10px;
        padding: 0 14px;
        font-size: 14px;
        color: var(--acc-text);
        outline: none;
        font-family: inherit;
        transition: .15s;
    }

    .acc-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 40px;
    }

    .acc-input::placeholder { color: var(--acc-text-3); }
    .acc-input:focus,
    .acc-select:focus {
        border-color: var(--acc-primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .1);
    }

    /* ---- Type chips ---- */
    .acc-type-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }

    .acc-type {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 48px;
        border: 1.5px solid var(--acc-border);
        border-radius: 10px;
        background: #fff;
        font-size: 14px;
        font-weight: 600;
        color: var(--acc-text-2);
        cursor: pointer;
        transition: .15s;
    }

    .acc-type:hover { border-color: #c7d6f5; color: var(--acc-primary); }
    .acc-type.active {
        border-color: var(--acc-primary);
        background: var(--acc-primary-soft);
        color: var(--acc-primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .08);
    }

    /* ---- Toggle ---- */
    .acc-toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 0 0;
        border-top: 1px solid var(--acc-border-soft);
        margin-top: 18px;
    }

    .acc-toggle-info { display: flex; align-items: flex-start; gap: 10px; }

    .acc-toggle-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: #f1f5f9;
        color: var(--acc-text-2);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .acc-toggle-info h5 { margin: 0; font-size: 14px; font-weight: 700; color: var(--acc-text); }
    .acc-toggle-info p { margin: 2px 0 0; font-size: 12px; color: var(--acc-text-3); }

    .acc-switch {
        position: relative;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }

    .acc-switch input { opacity: 0; width: 0; height: 0; }

    .acc-switch-slider {
        position: absolute;
        inset: 0;
        background: #cbd5e1;
        border-radius: 999px;
        transition: .2s;
        cursor: pointer;
    }

    .acc-switch-slider::before {
        content: "";
        position: absolute;
        top: 3px;
        left: 3px;
        width: 18px;
        height: 18px;
        background: #fff;
        border-radius: 50%;
        transition: .2s;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .15);
    }

    .acc-switch input:checked + .acc-switch-slider { background: var(--acc-primary); }
    .acc-switch input:checked + .acc-switch-slider::before { transform: translateX(20px); }

    .acc-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 22px;
        margin-top: 22px;
        border-top: 1px solid var(--acc-border-soft);
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 1100px) {
        .acc-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 900px) {
        .acc-shell { grid-template-columns: 1fr; padding: 16px; }
        .acc-sidebar { position: static; }
        .acc-form-grid.cols-3,
        .acc-form-grid { grid-template-columns: 1fr; }
        .acc-type-row { grid-template-columns: 1fr 1fr; }
        .acc-topbar-inner { padding: 12px 16px; flex-wrap: wrap; }
        .acc-search { order: 5; flex-basis: 100%; }
        .acc-user-meta { display: none; }
    }

    @media (max-width: 520px) {
        .acc-page-head { flex-direction: column; align-items: stretch; }
        .acc-addr { grid-template-columns: 24px 1fr; }
        .acc-addr-actions { grid-column: 2; justify-content: flex-end; }
    }
</style>
@endpush

@section('content')
<div class="acc-portal">
    {{-- ============== TOP BAR ============== --}}
    <header class="acc-topbar">
        <div class="acc-topbar-inner">
            <a href="{{ url('/') }}" class="acc-brand">
                <span class="acc-brand-logo"><i class="fa fa-shopping-bag"></i></span>
                <span class="acc-brand-name">dangduc</span>
            </a>

            <button class="acc-menu-btn" type="button" aria-label="Mở menu">
                <i class="fa fa-bars"></i>
            </button>

            <form class="acc-search" role="search">
                <input type="text" placeholder="Tìm kiếm sản phẩm, đơn hàng...">
                <button class="acc-search-btn" type="submit" aria-label="Tìm kiếm"><i class="fa fa-search"></i></button>
            </form>

            <div class="acc-actions">
                <button class="acc-icon-btn" type="button" aria-label="Thông báo">
                    <i class="fa fa-bell-o"></i>
                    <span class="acc-badge">3</span>
                </button>
                <a href="{{ url('/cart') }}" class="acc-icon-btn" aria-label="Giỏ hàng">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="acc-badge">2</span>
                </a>
                <button class="acc-user" type="button">
                    <span class="acc-user-avatar">
                        @php $picture = Session::get('storefront_customer_picture'); @endphp
                        @if ($picture)
                            <img src="{{ $picture }}" alt="Avatar">
                        @else
                            {{ mb_substr($customer->name ?? 'U', 0, 1) }}
                        @endif
                    </span>
                    <span class="acc-user-meta">
                        <span class="acc-user-name">{{ $customer->name ?? 'Khách hàng' }}</span>
                        <span class="acc-user-sub">Tài khoản của tôi</span>
                    </span>
                    <i class="fa fa-chevron-down acc-user-caret"></i>
                </button>
            </div>
        </div>
    </header>

    {{-- ============== SHELL ============== --}}
    <div class="acc-shell">
        {{-- ----- Sidebar ----- --}}
        <aside>
            <nav class="acc-sidebar">
                <ul class="acc-nav">
                    <li class="acc-nav-item"><a href="#"><i class="acc-nav-icon fa fa-th-large"></i>Tổng quan</a></li>
                    <li class="acc-nav-item">
                        <a href="#"><i class="acc-nav-icon fa fa-shopping-bag"></i>Đơn hàng <span class="acc-nav-badge">12</span></a>
                    </li>
                    <li class="acc-nav-item"><a href="#"><i class="acc-nav-icon fa fa-truck"></i>Theo dõi đơn hàng</a></li>
                    <li class="acc-nav-item"><a href="#"><i class="acc-nav-icon fa fa-heart-o"></i>Sản phẩm yêu thích</a></li>
                    <li class="acc-nav-item"><a href="#"><i class="acc-nav-icon fa fa-history"></i>Sản phẩm đã xem</a></li>
                    <li class="acc-nav-item"><a href="#"><i class="acc-nav-icon fa fa-ticket"></i>Kho voucher</a></li>

                    <li class="acc-nav-divider" aria-hidden="true"></li>

                    <li class="acc-nav-item"><a href="{{ route('customer.account', ['section' => 'profile']) }}"><i class="acc-nav-icon fa fa-user-o"></i>Thông tin tài khoản</a></li>
                    <li class="acc-nav-item active"><a href="{{ route('customer.account.addresses') }}"><i class="acc-nav-icon fa fa-map-marker"></i>Địa chỉ giao hàng</a></li>
                    <li class="acc-nav-item"><a href="#"><i class="acc-nav-icon fa fa-credit-card"></i>Phương thức thanh toán</a></li>
                    <li class="acc-nav-item"><a href="{{ route('customer.account', ['section' => 'password']) }}"><i class="acc-nav-icon fa fa-shield"></i>Bảo mật tài khoản</a></li>
                    <li class="acc-nav-item"><a href="#"><i class="acc-nav-icon fa fa-bell-o"></i>Thông báo của tôi</a></li>

                    <li class="acc-nav-divider" aria-hidden="true"></li>

                    <li class="acc-nav-item"><a href="#"><i class="acc-nav-icon fa fa-question-circle-o"></i>Trung tâm hỗ trợ</a></li>
                    <li class="acc-nav-item"><a href="{{ route('customer.account', ['section' => 'review']) }}"><i class="acc-nav-icon fa fa-star-o"></i>Đánh giá của tôi</a></li>
                    <li class="acc-nav-item danger">
                        <form method="POST" action="{{ url('/dang-xuat-khach-hang') }}">
                            @csrf
                            <button type="submit"><i class="acc-nav-icon fa fa-sign-out"></i>Đăng xuất</button>
                        </form>
                    </li>
                </ul>
            </nav>

            {{-- Promo card --}}
            <div class="acc-promo">
                <div class="acc-promo-title">Giao hàng nhanh<strong>trong 2h</strong></div>
                <p class="acc-promo-text">Áp dụng cho nội thành<br>Hà Nội &amp; TP. Hồ Chí Minh</p>
                <a href="#" class="acc-promo-cta">Tìm hiểu thêm</a>
                <svg class="acc-promo-truck" viewBox="0 0 200 110" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <ellipse cx="100" cy="98" rx="80" ry="6" fill="rgba(0,0,0,.18)"/>
                    <rect x="20" y="40" width="90" height="50" rx="6" fill="#ffffff"/>
                    <rect x="20" y="40" width="90" height="14" rx="6" fill="#dbeafe"/>
                    <rect x="110" y="55" width="50" height="35" rx="6" fill="#bfdbfe"/>
                    <rect x="118" y="62" width="20" height="14" rx="2" fill="#ffffff"/>
                    <circle cx="50" cy="92" r="10" fill="#1e293b"/>
                    <circle cx="50" cy="92" r="4" fill="#cbd5e1"/>
                    <circle cx="135" cy="92" r="10" fill="#1e293b"/>
                    <circle cx="135" cy="92" r="4" fill="#cbd5e1"/>
                    <path d="M30 50 h20 v8 h-20 z" fill="#fed7aa"/>
                    <path d="M58 50 h20 v8 h-20 z" fill="#fecaca"/>
                </svg>
            </div>
        </aside>

        {{-- ----- Main ----- --}}
        <main>
            @include('partials.storefront.breadcrumb', [
                'class' => 'acc-breadcrumb',
                'separator' => '›',
                'items' => [
                    ['label' => 'Tài khoản', 'url' => route('customer.account')],
                    ['label' => 'Địa chỉ giao hàng', 'current' => true],
                ],
            ])

            <div class="acc-page-head">
                <div>
                    <h1 class="acc-page-title">Địa chỉ giao hàng</h1>
                    <p class="acc-page-sub">Quản lý và chọn địa chỉ giao hàng cho đơn hàng của bạn</p>
                </div>
                <button type="button" class="acc-btn acc-btn-primary" onclick="document.getElementById('acc-form').scrollIntoView({behavior:'smooth'})">
                    <i class="fa fa-plus"></i>Thêm địa chỉ mới
                </button>
            </div>

            <div class="acc-grid">
                {{-- Left: address list --}}
                <section class="acc-card">
                    <header class="acc-card-head">
                        <h3 class="acc-card-title">Danh sách địa chỉ ({{ $addresses->count() }})</h3>
                    </header>
                    <div class="acc-addr-list">
                        @forelse ($addresses as $addr)
                            @php
                                $typeKey = strtolower((string) ($addr->label ?? ''));
                                $typeMeta = [
                                    'nhà riêng' => ['icon' => 'fa-home', 'label' => 'Nhà riêng'],
                                    'home'      => ['icon' => 'fa-home', 'label' => 'Nhà riêng'],
                                    'công ty'   => ['icon' => 'fa-building-o', 'label' => 'Công ty'],
                                    'company'   => ['icon' => 'fa-building-o', 'label' => 'Công ty'],
                                    'nhà người thân' => ['icon' => 'fa-users', 'label' => 'Nhà người thân'],
                                    'family'    => ['icon' => 'fa-users', 'label' => 'Nhà người thân'],
                                ][$typeKey] ?? ['icon' => 'fa-map-marker', 'label' => $addr->label ?: 'Khác'];

                                $line2Parts = array_filter([
                                    $addr->address_line2,
                                    optional($addr->ward)->name,
                                    optional($addr->province)->name,
                                    optional($addr->city)->name,
                                ]);
                                $line2 = implode(', ', $line2Parts);
                            @endphp
                            <label class="acc-addr {{ $addr->is_default ? 'is-selected' : '' }}">
                                <input type="radio" name="default_addr" class="acc-radio" {{ $addr->is_default ? 'checked' : '' }}>
                                <div class="acc-addr-body">
                                    <div class="acc-addr-row1">
                                        @if ($addr->is_default)
                                            <span class="acc-default-tag">Mặc định</span>
                                        @endif
                                        <span class="acc-addr-name">{{ $addr->recipient_name }}</span>
                                        <span class="acc-addr-phone">{{ $addr->recipient_phone }}</span>
                                    </div>
                                    <p class="acc-addr-text">
                                        {{ $addr->address_line1 }}@if ($line2)<br>{{ $line2 }}@endif
                                    </p>
                                    <div class="acc-addr-tags">
                                        <span class="acc-tag"><i class="fa {{ $typeMeta['icon'] }}"></i>{{ $typeMeta['label'] }}</span>
                                        @if ($addr->is_default)
                                            <span class="acc-tag tag-default"><i class="fa fa-check-circle"></i>Mặc định</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="acc-addr-actions">
                                    <button type="button" class="acc-addr-action" aria-label="Chỉnh sửa"><i class="fa fa-pencil"></i></button>
                                    <button type="button" class="acc-addr-action danger" aria-label="Xoá"><i class="fa fa-trash-o"></i></button>
                                </div>
                            </label>
                        @empty
                            <div style="padding:24px;text-align:center;color:#6b7280;border:1px dashed #cbd5e1;border-radius:10px">
                                <i class="fa fa-map-marker" style="font-size:24px;color:#cbd5e1;display:block;margin-bottom:6px"></i>
                                <div style="font-size:13px;font-weight:600;color:#374151">Bạn chưa có địa chỉ nào</div>
                                <div style="font-size:12px;margin-top:2px">Thêm địa chỉ để thanh toán nhanh hơn ở các đơn sau.</div>
                            </div>
                        @endforelse
                    </div>
                </section>

                {{-- Right: info panels --}}
                <aside>
                    <section class="acc-info">
                        <div class="acc-info-head">
                            <span class="ic"><i class="fa fa-info"></i></span>
                            <h4>Lưu ý</h4>
                        </div>
                        <ul class="acc-info-list">
                            <li><span class="dot"><i class="fa fa-circle"></i></span><span>Bạn có thể thêm tối đa 10 địa chỉ giao hàng.</span></li>
                            <li><span class="dot"><i class="fa fa-circle"></i></span><span>Địa chỉ mặc định sẽ được ưu tiên khi đặt hàng.</span></li>
                            <li><span class="dot"><i class="fa fa-circle"></i></span><span>Vui lòng cập nhật thông tin đầy đủ và chính xác để đảm bảo giao hàng thành công.</span></li>
                        </ul>
                    </section>

                    <section class="acc-info">
                        <div class="acc-info-head">
                            <span class="ic"><i class="fa fa-headphones"></i></span>
                            <h4>Cần hỗ trợ?</h4>
                        </div>
                        <p class="acc-support-text">Liên hệ với chúng tôi nếu bạn cần hỗ trợ thêm thông tin.</p>
                        <a href="{{ url('/lien-he') }}" class="acc-support-btn"><i class="fa fa-comments-o"></i>Liên hệ hỗ trợ</a>
                    </section>
                </aside>
            </div>

            {{-- ----- Form ----- --}}
            <section class="acc-form" id="acc-form">
                <h3 class="acc-form-title">Thêm địa chỉ mới</h3>

                <div class="acc-form-grid">
                    <div class="acc-field">
                        <label class="acc-label" for="recipient">Họ và tên người nhận <span class="req">*</span></label>
                        <input id="recipient" class="acc-input" type="text" placeholder="Nhập họ và tên">
                    </div>
                    <div class="acc-field">
                        <label class="acc-label" for="phone">Số điện thoại <span class="req">*</span></label>
                        <input id="phone" class="acc-input" type="tel" placeholder="Nhập số điện thoại">
                    </div>
                </div>

                <div class="acc-form-grid cols-3" style="margin-top: 16px;">
                    <div class="acc-field">
                        <label class="acc-label" for="prov">Tỉnh / Thành phố <span class="req">*</span></label>
                        <select id="prov" class="acc-select"><option value="">Chọn tỉnh / thành phố</option></select>
                    </div>
                    <div class="acc-field">
                        <label class="acc-label" for="dist">Quận / Huyện <span class="req">*</span></label>
                        <select id="dist" class="acc-select"><option value="">Chọn quận / huyện</option></select>
                    </div>
                    <div class="acc-field">
                        <label class="acc-label" for="ward">Phường / Xã <span class="req">*</span></label>
                        <select id="ward" class="acc-select"><option value="">Chọn phường / xã</option></select>
                    </div>
                </div>

                <div class="acc-field" style="margin-top: 16px;">
                    <label class="acc-label" for="line">Địa chỉ chi tiết <span class="req">*</span></label>
                    <input id="line" class="acc-input" type="text" placeholder="Số nhà, tên đường, toà nhà, căn hộ...">
                </div>

                <div class="acc-field" style="margin-top: 18px;">
                    <span class="acc-label">Loại địa chỉ</span>
                    <div class="acc-type-row" data-acc-type-row>
                        <button type="button" class="acc-type active" data-type="home"><i class="fa fa-home"></i>Nhà riêng</button>
                        <button type="button" class="acc-type" data-type="company"><i class="fa fa-building-o"></i>Công ty</button>
                        <button type="button" class="acc-type" data-type="family"><i class="fa fa-users"></i>Nhà người thân</button>
                        <button type="button" class="acc-type" data-type="other"><i class="fa fa-ellipsis-h"></i>Khác</button>
                    </div>
                </div>

                <div class="acc-toggle-row">
                    <div class="acc-toggle-info">
                        <span class="acc-toggle-icon"><i class="fa fa-refresh"></i></span>
                        <div>
                            <h5>Đặt làm địa chỉ mặc định</h5>
                            <p>Địa chỉ mặc định sẽ được ưu tiên khi đặt hàng</p>
                        </div>
                    </div>
                    <label class="acc-switch">
                        <input type="checkbox">
                        <span class="acc-switch-slider"></span>
                    </label>
                </div>

                <div class="acc-form-actions">
                    <button type="button" class="acc-btn acc-btn-ghost">Huỷ</button>
                    <button type="submit" class="acc-btn acc-btn-primary">Lưu địa chỉ</button>
                </div>
            </section>
        </main>
    </div>
</div>

<script>
    (function () {
        document.querySelectorAll('[data-acc-type-row]').forEach(function (row) {
            row.addEventListener('click', function (e) {
                var btn = e.target.closest('.acc-type');
                if (!btn) return;
                row.querySelectorAll('.acc-type').forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');
            });
        });

        document.querySelectorAll('.acc-addr .acc-radio').forEach(function (radio) {
            radio.addEventListener('change', function () {
                document.querySelectorAll('.acc-addr').forEach(function (card) { card.classList.remove('is-selected'); });
                if (radio.checked) radio.closest('.acc-addr').classList.add('is-selected');
            });
        });
    })();
</script>
@endsection
