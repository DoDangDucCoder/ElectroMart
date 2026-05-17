@php
    $homeUrl = url('/');
    $shopUrl = url('/san-pham');
    $contactUrl = url('/lien-he');
    $cartUrl = url('/gio-hang');
    $blogUrl = url('/bai-viet');

    $suggestedProducts = [
        [
            'brand' => 'Apple',
            'name' => 'MacBook Air M2 256GB',
            'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=600&q=80',
            'old_price' => 28990000,
            'price' => 26990000,
            'rating' => 3.5,
            'reviews' => 128,
        ],
        [
            'brand' => 'ASUS',
            'name' => 'TUF Gaming RTX 4060 8GB',
            'image' => 'https://images.unsplash.com/photo-1591488320449-011701bb6704?auto=format&fit=crop&w=600&q=80',
            'old_price' => 10990000,
            'price' => 9990000,
            'rating' => 4,
            'reviews' => 86,
        ],
        [
            'brand' => 'LG',
            'name' => 'UltraGear 27GP850 27" 2K 165Hz',
            'image' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=600&q=80',
            'old_price' => 9990000,
            'price' => 8590000,
            'rating' => 3.5,
            'reviews' => 64,
        ],
        [
            'brand' => 'AKKO',
            'name' => 'Bàn phím cơ AKKO 3068B Plus',
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=600&q=80',
            'old_price' => 1990000,
            'price' => 1590000,
            'rating' => 2.5,
            'reviews' => 131,
        ],
        [
            'brand' => 'Apple',
            'name' => 'AirPods Pro 2 (USB-C)',
            'image' => 'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?auto=format&fit=crop&w=600&q=80',
            'old_price' => 5490000,
            'price' => 4260000,
            'rating' => 3.5,
            'reviews' => 242,
        ],
    ];

    $visibleSuggestedProducts = collect($suggestedProducts)
        ->take(count($suggestedProducts) >= 8 ? 8 : min(4, count($suggestedProducts)));

    $shortcuts = [
        ['label' => 'Laptop', 'icon' => '💻'],
        ['label' => 'PC - Linh kiện', 'icon' => '🖥️'],
        ['label' => 'Màn hình', 'icon' => '🖱️'],
        ['label' => 'Bàn phím', 'icon' => '⌨️'],
        ['label' => 'Chuột', 'icon' => '🖱️'],
        ['label' => 'Tai nghe', 'icon' => '🎧'],
    ];

    $formatPrice = static fn ($amount) => number_format((float) $amount, 0, ',', '.') . 'đ';

    $renderStars = static function (float $rating) {
        $full = (int) floor($rating);
        $hasHalf = ($rating - $full) >= 0.5;
        $html = '';
        for ($i = 0; $i < $full; $i++) {
            $html .= '<i class="fa fa-star"></i>';
        }
        if ($hasHalf) {
            $html .= '<i class="fa fa-star-half-o"></i>';
            $full++;
        }
        for ($i = $full; $i < 5; $i++) {
            $html .= '<i class="fa fa-star-o"></i>';
        }
        return $html;
    };
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>404 — Không tìm thấy trang | DangDuc</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        :root {
            --primary: #14b8a6;
            --primary-dark: #0f766e;
            --primary-soft: #e6f7f6;
            --primary-tint: #f1faf9;
            --bg: #f5f9fa;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --line-soft: #eef2f5;
            --danger: #ef4444;
            --footer: #1e2a37;
            --footer-text: #cfd6df;
            --footer-muted: #8b95a3;
            --radius-lg: 22px;
            --radius-md: 14px;
            --radius-sm: 10px;
            --shadow-sm: 0 4px 14px rgba(15, 23, 42, 0.05);
            --shadow-md: 0 10px 30px rgba(15, 23, 42, 0.07);
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: 'Be Vietnam Pro', system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }
        a { color: inherit; text-decoration: none; }
        button { font-family: inherit; cursor: pointer; border: 0; background: none; }
        img { max-width: 100%; display: block; }

        .container {
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ===== Top bar ===== */
        .topbar {
            background: linear-gradient(90deg, #0aa1a1 0%, #14b8a6 50%, #0d9488 100%);
            color: #ffffff;
            font-size: 13px;
            padding: 9px 0;
        }
        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .topbar-left { display: flex; align-items: center; gap: 8px; opacity: 0.95; }
        .topbar-right { display: flex; align-items: center; gap: 28px; flex-wrap: wrap; }
        .topbar-right span { display: inline-flex; align-items: center; gap: 8px; opacity: 0.95; }

        /* ===== Header ===== */
        .header {
            background: #ffffff;
            padding: 18px 0;
            border-bottom: 1px solid var(--line-soft);
        }
        .header-inner {
            display: grid;
            grid-template-columns: 40px auto 1fr auto auto auto;
            align-items: center;
            gap: 22px;
        }
        .header-burger {
            width: 40px; height: 40px;
            border-radius: 10px;
            background: var(--primary-tint);
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            color: var(--primary);
            font-size: 22px;
        }
        .logo-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: var(--primary-tint);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 18px;
        }
        .search {
            position: relative;
            background: #f1f5f7;
            border-radius: 999px;
            padding: 12px 56px 12px 22px;
            display: flex;
            align-items: center;
        }
        .search input {
            flex: 1;
            border: 0;
            background: transparent;
            font-size: 14px;
            outline: none;
            color: var(--text);
        }
        .search input::placeholder { color: #94a3b8; }
        .search-btn {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .header-action {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--text);
        }
        .header-action .ico {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: var(--primary-tint);
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .header-action small { display: block; color: var(--muted); font-size: 12px; }
        .header-action strong { display: block; font-size: 14px; font-weight: 600; }
        .cart-action { position: relative; }
        .cart-badge {
            position: absolute;
            top: -4px;
            right: -6px;
            background: var(--danger);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }

        /* ===== Main nav ===== */
        .mainnav {
            background: #ffffff;
            border-top: 1px solid var(--line-soft);
            border-bottom: 1px solid var(--line-soft);
        }
        .mainnav-inner {
            display: flex;
            align-items: center;
            gap: 36px;
            padding: 14px 0;
        }
        .nav-cats {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--text);
            font-weight: 600;
        }
        .nav-cats .bars {
            width: 28px; height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }
        .nav-cats .caret { color: var(--primary); margin-left: 2px; }
        .nav-links {
            display: inline-flex;
            align-items: center;
            gap: 36px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .nav-links a {
            font-size: 15px;
            font-weight: 500;
            color: var(--text);
        }
        .nav-links a:hover { color: var(--primary); }

        /* ===== Breadcrumb ===== */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 14px;
            padding: 18px 0 14px;
        }
        .breadcrumb a:hover { color: var(--primary); }
        .breadcrumb .sep { color: #cbd5e1; }
        .breadcrumb .current { color: var(--text); font-weight: 500; }

        /* ===== Hero 404 ===== */
        .hero404 {
            background: var(--card);
            border-radius: var(--radius-lg);
            padding: 50px 56px;
            box-shadow: var(--shadow-sm);
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.05fr);
            align-items: center;
            gap: 32px;
        }
        .hero404-num {
            font-size: 168px;
            font-weight: 800;
            line-height: 1;
            color: var(--primary);
            letter-spacing: -4px;
            margin: 0;
        }
        .hero404-title {
            font-size: 32px;
            font-weight: 700;
            margin: 18px 0 12px;
            line-height: 1.25;
            color: var(--text);
        }
        .hero404-title .accent { color: var(--primary); }
        .hero404-desc {
            color: var(--muted);
            font-size: 15px;
            margin: 0 0 26px;
            max-width: 460px;
        }
        .hero404-actions {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 13px 22px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }
        .btn-primary {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 8px 18px rgba(20, 184, 166, 0.28);
        }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-outline {
            background: #fff;
            color: var(--text);
            border: 1px solid var(--line);
        }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }
        .btn-link {
            color: var(--primary);
            font-weight: 600;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 6px;
        }
        .btn-link:hover { color: var(--primary-dark); }

        .hero404-art {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 320px;
        }
        .hero404-art svg { width: 100%; max-width: 540px; height: auto; }

        /* ===== Search card ===== */
        .find-card {
            background: var(--card);
            border-radius: var(--radius-lg);
            padding: 22px;
            margin-top: 22px;
            box-shadow: var(--shadow-sm);
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 1.4fr);
            gap: 22px;
            align-items: stretch;
        }
        .find-search {
            display: grid;
            grid-template-columns: 56px 1fr auto;
            align-items: center;
            background: var(--primary-tint);
            border-radius: var(--radius-md);
            padding: 14px 14px 14px 18px;
            gap: 12px;
        }
        .find-search-icon {
            width: 56px; height: 56px;
            border-radius: 14px;
            background: var(--primary);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .find-search-text label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 4px; }
        .find-search-text input {
            border: 0;
            background: transparent;
            font-size: 14px;
            color: var(--text);
            width: 100%;
            outline: none;
            padding: 0;
        }
        .find-search-text input::placeholder { color: #94a3b8; }
        .find-search button {
            background: var(--primary);
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            border-radius: 12px;
            padding: 12px 22px;
            white-space: nowrap;
        }
        .find-search button:hover { background: var(--primary-dark); }

        .find-chips {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 10px;
        }
        .chip {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12.5px;
            font-weight: 500;
            color: var(--text);
            transition: border-color 0.15s ease, color 0.15s ease, transform 0.15s ease;
            text-align: center;
            min-height: 76px;
        }
        .chip:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-1px);
        }
        .chip-ico {
            width: 36px; height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--muted);
        }
        .chip:hover .chip-ico { color: var(--primary); }
        .chip-more .chip-ico {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2px;
            width: 20px;
            height: 20px;
        }
        .chip-more .chip-ico span {
            width: 8px; height: 8px; border-radius: 2px; background: var(--muted);
        }
        .chip-more:hover .chip-ico span { background: var(--primary); }

        /* ===== Recommendations ===== */
        .reco {
            margin-top: 36px;
        }
        .reco-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }
        .reco-head h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
        }
        .reco-head a {
            color: var(--primary);
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .reco-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }
        .product {
            background: #fff;
            border-radius: var(--radius-md);
            padding: 14px;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-sm);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .product:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .product-media {
            background: #f8fafc;
            border-radius: 12px;
            aspect-ratio: 1 / 1;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 12px;
        }
        .product-media img {
            width: 100%; height: 100%;
            object-fit: cover;
        }
        .product-brand {
            font-size: 12px;
            color: var(--muted);
            font-weight: 600;
            margin-bottom: 4px;
        }
        .product-name {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text);
            line-height: 1.4;
            margin: 0 0 8px;
            min-height: 38px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .product-old {
            font-size: 12px;
            color: var(--muted);
            text-decoration: line-through;
        }
        .product-price {
            font-size: 16px;
            font-weight: 700;
            color: var(--danger);
            margin-top: 2px;
        }
        .product-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
        }
        .product-rate {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            color: var(--muted);
        }
        .product-rate i { color: #f5b50a; font-size: 12px; }
        .product-rate i.fa-star-o { color: #cbd5e1; }
        .product-rate span { color: var(--muted); margin-left: 2px; }
        .product-cart {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }
        .product-cart:hover { background: var(--primary-dark); }

        /* ===== Support row ===== */
        .support {
            background: var(--card);
            border-radius: var(--radius-lg);
            padding: 22px 26px;
            margin: 36px 0 28px;
            box-shadow: var(--shadow-sm);
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) repeat(3, minmax(0, 1fr));
            gap: 22px;
            align-items: center;
        }
        .support-lead {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .support-lead-ico {
            width: 56px; height: 56px;
            border-radius: 16px;
            background: var(--primary-soft);
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        .support-lead h3 { margin: 0 0 2px; font-size: 16px; font-weight: 700; }
        .support-lead p { margin: 0; font-size: 13px; color: var(--muted); }
        .support-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 14px;
            background: #f8fafc;
        }
        .support-card-ico {
            width: 42px; height: 42px;
            border-radius: 12px;
            background: var(--primary-soft);
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }
        .support-card strong { display: block; font-size: 14px; font-weight: 700; color: var(--text); }
        .support-card span { display: block; font-size: 12px; color: var(--muted); margin-top: 2px; }

        /* ===== Footer ===== */
        .footer {
            background: var(--footer);
            color: var(--footer-text);
            padding: 50px 0 22px;
            margin-top: 24px;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 1.4fr repeat(4, 1fr);
            gap: 36px;
        }
        .footer-brand .logo { color: #fff; }
        .footer-brand .logo-icon { background: rgba(255,255,255,0.08); color: #fff; }
        .footer-brand p {
            margin: 18px 0 18px;
            font-size: 13.5px;
            color: var(--footer-muted);
            line-height: 1.7;
            max-width: 320px;
        }
        .footer-socials { display: flex; gap: 10px; }
        .footer-socials a {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            transition: background 0.15s ease;
        }
        .footer-socials a:hover { background: var(--primary); }
        .footer h4 {
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin: 0 0 18px;
        }
        .footer ul { list-style: none; margin: 0; padding: 0; display: grid; gap: 10px; }
        .footer ul a {
            font-size: 13.5px;
            color: var(--footer-muted);
            transition: color 0.15s ease;
        }
        .footer ul a:hover { color: #fff; }
        .footer-contact li { display: flex; gap: 10px; align-items: flex-start; font-size: 13.5px; color: var(--footer-muted); }
        .footer-contact li i { color: var(--primary); margin-top: 3px; }
        .footer-bot {
            border-top: 1px solid rgba(255,255,255,0.08);
            margin-top: 38px;
            padding-top: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--footer-muted);
            font-size: 13px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .footer-pays { display: inline-flex; align-items: center; gap: 22px; flex-wrap: wrap; }
        .footer-pays span {
            font-weight: 700;
            color: rgba(255,255,255,0.85);
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        .pay-visa { color: #1a1f71; background: #fff; padding: 4px 10px; border-radius: 6px; font-style: italic; }
        .pay-mc { color: #ff5f00; background: #fff; padding: 4px 10px; border-radius: 6px; }
        .pay-momo { color: #a50064; background: #fff; padding: 4px 10px; border-radius: 6px; }
        .pay-zalo { color: #0068ff; background: #fff; padding: 4px 10px; border-radius: 6px; }
        .pay-napas { color: #c8102e; background: #fff; padding: 4px 10px; border-radius: 6px; }

        .moct-badge {
            position: absolute;
            right: 22px;
            top: -28px;
            background: #1d4ed8;
            color: #fff;
            border-radius: 50%;
            width: 86px;
            height: 86px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1.2;
            padding: 6px;
        }

        .chat-fab {
            position: fixed;
            right: 22px;
            bottom: 22px;
            width: 52px; height: 52px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 12px 28px rgba(20, 184, 166, 0.45);
            z-index: 80;
        }

        /* ===== Responsive ===== */
        @media (max-width: 1100px) {
            .hero404 { grid-template-columns: 1fr; padding: 40px 30px; }
            .hero404-num { font-size: 120px; }
            .find-card { grid-template-columns: 1fr; }
            .find-chips { grid-template-columns: repeat(4, 1fr); }
            .reco-grid { grid-template-columns: repeat(2, 1fr); }
            .support { grid-template-columns: 1fr 1fr; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .header-inner { grid-template-columns: 40px auto 1fr auto; }
            .header-action.head-loc, .header-action.head-acc { display: none; }
        }
        @media (max-width: 720px) {
            .topbar-right { gap: 14px; font-size: 12px; }
            .hero404-num { font-size: 96px; }
            .hero404-title { font-size: 24px; }
            .find-chips { grid-template-columns: repeat(3, 1fr); }
            .reco-grid { grid-template-columns: repeat(2, 1fr); }
            .support { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
            .nav-links { display: none; }
            .header-inner { grid-template-columns: 40px 1fr auto; }
            .header-inner .search { display: none; }
        }
    </style>
</head>
<body>

    {{-- Top bar --}}
    <div class="topbar">
        <div class="container topbar-inner">
            <div class="topbar-left">
                <i class="fa fa-tag" aria-hidden="true"></i>
                <span>Ưu đãi chọn lọc mỗi ngày</span>
            </div>
            <div class="topbar-right">
                <span><i class="fa fa-truck" aria-hidden="true"></i> Giao hàng nhanh nội thành</span>
                <span><i class="fa fa-shield" aria-hidden="true"></i> Bảo hành chính hãng</span>
                <span><i class="fa fa-headphones" aria-hidden="true"></i> Hỗ trợ 24/7</span>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <header class="header">
        <div class="container header-inner">
            <button type="button" class="header-burger" aria-label="Mở danh mục">
                <i class="fa fa-bars"></i>
            </button>

            <a href="{{ $homeUrl }}" class="logo">
                <span class="logo-icon"><i class="fa fa-shopping-bag"></i></span>
                dangduc
            </a>

            <form class="search" action="{{ url('/san-pham') }}" method="GET" role="search">
                <input type="search" name="keywords_submit" placeholder="Bạn cần gì hôm nay?" aria-label="Tìm kiếm sản phẩm">
                <button type="submit" class="search-btn" aria-label="Tìm kiếm">
                    <i class="fa fa-search"></i>
                </button>
            </form>

            <div class="header-action head-loc">
                <span class="ico"><i class="fa fa-map-marker"></i></span>
                <div>
                    <small>Giao hàng</small>
                    <strong>Hà Nội</strong>
                </div>
            </div>

            <div class="header-action head-acc">
                <span class="ico"><i class="fa fa-user-o"></i></span>
                <div>
                    <small>Tài khoản</small>
                    <strong>Nguyễn Văn An</strong>
                </div>
            </div>

            <a href="{{ $cartUrl }}" class="header-action cart-action">
                <span class="ico" style="position: relative;">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="cart-badge">3</span>
                </span>
                <div>
                    <strong>Giỏ hàng</strong>
                </div>
            </a>
        </div>
    </header>

    {{-- Main nav --}}
    <nav class="mainnav">
        <div class="container mainnav-inner">
            <a href="#" class="nav-cats">
                <span class="bars"><i class="fa fa-bars"></i></span>
                Danh mục sản phẩm
                <i class="fa fa-angle-down caret"></i>
            </a>
            <ul class="nav-links">
                <li><a href="{{ $homeUrl }}">Trang chủ</a></li>
                <li><a href="{{ $shopUrl }}">Sản phẩm</a></li>
                <li><a href="{{ $blogUrl }}">Bài viết</a></li>
                <li><a href="{{ $cartUrl }}">Giỏ hàng</a></li>
                <li><a href="{{ $contactUrl }}">Liên hệ</a></li>
            </ul>
        </div>
    </nav>

    {{-- Page --}}
    <main>
        <div class="container">
            <nav class="breadcrumb" aria-label="breadcrumb">
                <a href="{{ $homeUrl }}">Trang chủ</a>
                <span class="sep"><i class="fa fa-angle-right"></i></span>
                <span class="current">Lỗi 404</span>
            </nav>

            {{-- Hero 404 --}}
            <section class="hero404">
                <div>
                    <p class="hero404-num">404</p>
                    <h1 class="hero404-title">
                        <span class="accent">Oops!</span> Không tìm thấy trang bạn yêu cầu
                    </h1>
                    <p class="hero404-desc">
                        Trang bạn đang tìm kiếm có thể đã được di chuyển,<br>
                        xóa hoặc chưa bao giờ tồn tại.
                    </p>
                    <div class="hero404-actions">
                        <a href="{{ $homeUrl }}" class="btn btn-primary">
                            <i class="fa fa-home"></i> Về trang chủ
                        </a>
                        <a href="{{ $shopUrl }}" class="btn btn-outline">
                            <i class="fa fa-shopping-cart"></i> Tiếp tục mua sắm
                        </a>
                        <a href="javascript:history.back()" class="btn-link">
                            <i class="fa fa-angle-left"></i> Quay lại trang trước
                        </a>
                    </div>
                </div>

                <div class="hero404-art" aria-hidden="true">
                    {{-- Robot + laptop SVG --}}
                    <svg viewBox="0 0 540 360" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="skyGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#f0fbfa"/>
                                <stop offset="100%" stop-color="#ffffff"/>
                            </linearGradient>
                            <linearGradient id="botGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#ffffff"/>
                                <stop offset="100%" stop-color="#e7f6f5"/>
                            </linearGradient>
                            <linearGradient id="lapGrad" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#e2e8f0"/>
                                <stop offset="100%" stop-color="#cbd5e1"/>
                            </linearGradient>
                        </defs>

                        {{-- City silhouette --}}
                        <rect x="0" y="0" width="540" height="360" fill="url(#skyGrad)" opacity="0"/>
                        <g opacity="0.18" fill="#14b8a6">
                            <rect x="20" y="180" width="36" height="120" rx="3"/>
                            <rect x="64" y="200" width="28" height="100" rx="3"/>
                            <rect x="100" y="160" width="44" height="140" rx="3"/>
                            <rect x="152" y="190" width="22" height="110" rx="3"/>
                            <rect x="184" y="150" width="34" height="150" rx="3"/>
                            <rect x="226" y="200" width="24" height="100" rx="3"/>
                            <polygon points="200,150 218,130 218,150"/>

                            <rect x="380" y="170" width="34" height="130" rx="3"/>
                            <rect x="420" y="190" width="26" height="110" rx="3"/>
                            <rect x="452" y="160" width="40" height="140" rx="3"/>
                            <rect x="500" y="200" width="28" height="100" rx="3"/>
                            <polygon points="466,160 482,140 482,160"/>
                        </g>

                        {{-- Window dots --}}
                        <g fill="#0d9488" opacity="0.35">
                            <rect x="106" y="170" width="6" height="6"/>
                            <rect x="120" y="170" width="6" height="6"/>
                            <rect x="106" y="186" width="6" height="6"/>
                            <rect x="190" y="160" width="6" height="6"/>
                            <rect x="204" y="160" width="6" height="6"/>
                            <rect x="386" y="180" width="6" height="6"/>
                            <rect x="400" y="180" width="6" height="6"/>
                            <rect x="460" y="170" width="6" height="6"/>
                            <rect x="474" y="170" width="6" height="6"/>
                        </g>

                        {{-- Decorative dashed circle behind robot --}}
                        <circle cx="190" cy="180" r="120" fill="none" stroke="#cbe9e7" stroke-width="2" stroke-dasharray="4 6" opacity="0.7"/>

                        {{-- Question bubble --}}
                        <g transform="translate(300, 60)">
                            <circle cx="0" cy="0" r="22" fill="#ffffff" stroke="#cbd5e1" stroke-width="2"/>
                            <text x="0" y="6" text-anchor="middle" font-family="Be Vietnam Pro, sans-serif" font-size="22" font-weight="700" fill="#94a3b8">?</text>
                            <circle cx="-22" cy="22" r="4" fill="#ffffff" stroke="#cbd5e1" stroke-width="2"/>
                            <circle cx="-30" cy="32" r="2.5" fill="#ffffff" stroke="#cbd5e1" stroke-width="1.5"/>
                        </g>

                        {{-- Cross marks decoration --}}
                        <g stroke="#cbd5e1" stroke-width="2" stroke-linecap="round" opacity="0.7">
                            <line x1="260" y1="50" x2="270" y2="60"/>
                            <line x1="270" y1="50" x2="260" y2="60"/>
                            <line x1="80" y1="80" x2="88" y2="88"/>
                            <line x1="88" y1="80" x2="80" y2="88"/>
                        </g>

                        {{-- Tiny plant left --}}
                        <g transform="translate(110, 305)">
                            <path d="M0 0 Q -8 -16 -14 -8 Q -10 -2 0 0 Z" fill="#14b8a6" opacity="0.85"/>
                            <path d="M0 0 Q 6 -22 14 -16 Q 12 -6 0 0 Z" fill="#0d9488" opacity="0.9"/>
                            <path d="M0 0 Q -2 -28 6 -28 Q 8 -10 0 0 Z" fill="#14b8a6"/>
                        </g>

                        {{-- Tiny plant right --}}
                        <g transform="translate(440, 308)">
                            <path d="M0 0 Q -10 -18 -16 -10 Q -12 -2 0 0 Z" fill="#14b8a6"/>
                            <path d="M0 0 Q 8 -24 16 -18 Q 14 -8 0 0 Z" fill="#0d9488"/>
                        </g>

                        {{-- Robot body --}}
                        <g transform="translate(150, 110)">
                            {{-- Antenna --}}
                            <line x1="55" y1="-10" x2="55" y2="-30" stroke="#0d9488" stroke-width="3" stroke-linecap="round"/>
                            <circle cx="55" cy="-34" r="6" fill="#14b8a6"/>
                            <circle cx="55" cy="-34" r="2" fill="#fff"/>

                            {{-- Head --}}
                            <rect x="10" y="-10" width="100" height="86" rx="22" fill="url(#botGrad)" stroke="#0d9488" stroke-width="2.5"/>
                            {{-- Face screen --}}
                            <rect x="22" y="6" width="76" height="50" rx="14" fill="#0f172a"/>
                            {{-- Eyes (winking — left closed, right open) --}}
                            <path d="M36 30 q6 -8 12 0" stroke="#14b8a6" stroke-width="3" fill="none" stroke-linecap="round"/>
                            <circle cx="76" cy="30" r="4.5" fill="#14b8a6"/>
                            <circle cx="76" cy="30" r="1.6" fill="#fff"/>
                            {{-- Mouth --}}
                            <path d="M52 44 q8 6 16 0" stroke="#14b8a6" stroke-width="3" fill="none" stroke-linecap="round"/>
                            {{-- Side ears --}}
                            <rect x="2" y="14" width="10" height="34" rx="5" fill="#0d9488"/>
                            <rect x="108" y="14" width="10" height="34" rx="5" fill="#0d9488"/>

                            {{-- Neck --}}
                            <rect x="50" y="76" width="20" height="10" fill="#cbd5e1"/>
                            {{-- Body --}}
                            <rect x="6" y="86" width="108" height="92" rx="20" fill="url(#botGrad)" stroke="#0d9488" stroke-width="2.5"/>
                            {{-- Body badge (shopping bag) --}}
                            <rect x="44" y="118" width="32" height="34" rx="6" fill="#14b8a6"/>
                            <path d="M52 118 v-6 q0 -8 8 -8 q8 0 8 8 v6" stroke="#fff" stroke-width="2.5" fill="none" stroke-linecap="round"/>

                            {{-- Left arm (raised, holding magnifier) --}}
                            <path d="M114 100 q26 -10 50 8" stroke="#0d9488" stroke-width="9" fill="none" stroke-linecap="round"/>
                            {{-- Right arm (down) --}}
                            <path d="M6 110 q-12 18 -10 38" stroke="#0d9488" stroke-width="9" fill="none" stroke-linecap="round"/>

                            {{-- Legs --}}
                            <rect x="20" y="178" width="22" height="20" rx="6" fill="#0d9488"/>
                            <rect x="78" y="178" width="22" height="20" rx="6" fill="#0d9488"/>
                        </g>

                        {{-- Magnifying glass --}}
                        <g transform="translate(316, 196)">
                            <circle cx="0" cy="0" r="32" fill="rgba(20, 184, 166, 0.12)" stroke="#0d9488" stroke-width="5"/>
                            <circle cx="0" cy="0" r="32" fill="none" stroke="#fff" stroke-width="2"/>
                            <line x1="22" y1="22" x2="48" y2="48" stroke="#0d9488" stroke-width="9" stroke-linecap="round"/>
                            {{-- Glass shine --}}
                            <path d="M-18 -10 q4 -18 22 -18" stroke="#fff" stroke-width="3" fill="none" stroke-linecap="round" opacity="0.8"/>
                        </g>

                        {{-- Sad laptop --}}
                        <g transform="translate(360, 168)">
                            {{-- Laptop screen --}}
                            <rect x="0" y="0" width="140" height="92" rx="10" fill="url(#lapGrad)" stroke="#94a3b8" stroke-width="2.5"/>
                            <rect x="8" y="8" width="124" height="76" rx="6" fill="#f1f5f9"/>
                            {{-- Pixelated cracks --}}
                            <g fill="#cbd5e1">
                                <rect x="14" y="14" width="6" height="6"/>
                                <rect x="22" y="20" width="6" height="6"/>
                                <rect x="120" y="14" width="6" height="6"/>
                                <rect x="112" y="22" width="6" height="6"/>
                                <rect x="14" y="68" width="6" height="6"/>
                                <rect x="120" y="68" width="6" height="6"/>
                            </g>
                            {{-- Sad face --}}
                            <circle cx="52" cy="42" r="3.4" fill="#475569"/>
                            <circle cx="88" cy="42" r="3.4" fill="#475569"/>
                            <path d="M52 62 q18 -14 36 0" stroke="#475569" stroke-width="3.2" fill="none" stroke-linecap="round"/>
                            {{-- Laptop base --}}
                            <path d="M-10 92 h160 l-10 14 h-140 z" fill="#cbd5e1" stroke="#94a3b8" stroke-width="2"/>
                            <rect x="56" y="98" width="28" height="4" rx="2" fill="#94a3b8"/>
                        </g>

                        {{-- Floor shadow --}}
                        <ellipse cx="270" cy="318" rx="220" ry="10" fill="#0f766e" opacity="0.05"/>
                    </svg>
                </div>
            </section>

            {{-- Search + chips --}}
            <section class="find-card">
                <form class="find-search" action="{{ url('/san-pham') }}" method="GET">
                    <span class="find-search-icon"><i class="fa fa-search"></i></span>
                    <div class="find-search-text">
                        <label for="find404-keywords">Tìm sản phẩm bạn cần...</label>
                        <input id="find404-keywords" type="search" name="keywords_submit" placeholder="Nhập tên sản phẩm, thương hiệu hoặc từ khóa...">
                    </div>
                    <button type="submit">Tìm kiếm</button>
                </form>

                <div class="find-chips">
                    @foreach ($shortcuts as $sc)
                        <a href="{{ $shopUrl }}" class="chip">
                            <span class="chip-ico">
                                @switch($sc['label'])
                                    @case('Laptop') <i class="fa fa-laptop"></i> @break
                                    @case('PC - Linh kiện') <i class="fa fa-desktop"></i> @break
                                    @case('Màn hình') <i class="fa fa-tv"></i> @break
                                    @case('Bàn phím') <i class="fa fa-keyboard-o"></i> @break
                                    @case('Chuột') <i class="fa fa-mouse-pointer"></i> @break
                                    @case('Tai nghe') <i class="fa fa-headphones"></i> @break
                                @endswitch
                            </span>
                            {{ $sc['label'] }}
                        </a>
                    @endforeach
                    <a href="{{ $shopUrl }}" class="chip chip-more">
                        <span class="chip-ico">
                            <span></span><span></span><span></span><span></span>
                        </span>
                        Xem thêm
                    </a>
                </div>
            </section>

            {{-- Recommendations --}}
            <section class="reco">
                <div class="reco-head">
                    <h2>Sản phẩm gợi ý cho bạn</h2>
                    <a href="{{ $shopUrl }}">Xem tất cả <i class="fa fa-angle-right"></i></a>
                </div>
                <div class="reco-grid">
                    @foreach ($visibleSuggestedProducts as $p)
                        <article class="product">
                            <div class="product-media">
                                <img src="{{ $p['image'] }}" alt="{{ $p['name'] }}" loading="lazy">
                            </div>
                            <div class="product-brand">{{ $p['brand'] }}</div>
                            <h3 class="product-name">{{ $p['name'] }}</h3>
                            <div class="product-old">{{ $formatPrice($p['old_price']) }}</div>
                            <div class="product-price">{{ $formatPrice($p['price']) }}</div>
                            <div class="product-foot">
                                <div class="product-rate">
                                    {!! $renderStars((float) $p['rating']) !!}
                                    <span>({{ $p['reviews'] }})</span>
                                </div>
                                <button type="button" class="product-cart" aria-label="Thêm vào giỏ">
                                    <i class="fa fa-shopping-cart"></i>
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- Support row --}}
            <section class="support">
                <div class="support-lead">
                    <span class="support-lead-ico"><i class="fa fa-headphones"></i></span>
                    <div>
                        <h3>Bạn cần hỗ trợ?</h3>
                        <p>Đội ngũ của chúng tôi luôn sẵn sàng giúp bạn.</p>
                    </div>
                </div>
                <a href="tel:19001234" class="support-card">
                    <span class="support-card-ico"><i class="fa fa-phone"></i></span>
                    <div>
                        <strong>Hotline: 1900 1234</strong>
                        <span>(8:00 – 21:00 mỗi ngày)</span>
                    </div>
                </a>
                <a href="mailto:support@dangduc.vn" class="support-card">
                    <span class="support-card-ico"><i class="fa fa-envelope-o"></i></span>
                    <div>
                        <strong>Email: support@dangduc.vn</strong>
                        <span>Phản hồi trong 30 phút</span>
                    </div>
                </a>
                <a href="{{ $contactUrl }}" class="support-card">
                    <span class="support-card-ico"><i class="fa fa-comments-o"></i></span>
                    <div>
                        <strong>Chat với chúng tôi</strong>
                        <span>Hỗ trợ nhanh chóng</span>
                    </div>
                </a>
            </section>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="{{ $homeUrl }}" class="logo">
                        <span class="logo-icon"><i class="fa fa-shopping-bag"></i></span>
                        dangduc
                    </a>
                    <p>Chuyên cung cấp laptop, linh kiện PC và phụ kiện công nghệ chính hãng. Cam kết giá tốt, bảo hành uy tín và hỗ trợ tận tâm.</p>
                    <div class="footer-socials">
                        <a href="#" aria-label="Facebook"><i class="fa fa-facebook"></i></a>
                        <a href="#" aria-label="Youtube"><i class="fa fa-youtube-play"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fa fa-instagram"></i></a>
                        <a href="#" aria-label="Tiktok"><i class="fa fa-music"></i></a>
                    </div>
                </div>

                <div>
                    <h4>Về chúng tôi</h4>
                    <ul>
                        <li><a href="#">Giới thiệu</a></li>
                        <li><a href="#">Tin tức</a></li>
                        <li><a href="#">Tuyển dụng</a></li>
                        <li><a href="{{ $contactUrl }}">Liên hệ</a></li>
                    </ul>
                </div>

                <div>
                    <h4>Chính sách</h4>
                    <ul>
                        <li><a href="#">Chính sách bảo hành</a></li>
                        <li><a href="#">Chính sách đổi trả</a></li>
                        <li><a href="#">Chính sách thanh toán</a></li>
                        <li><a href="#">Chính sách vận chuyển</a></li>
                        <li><a href="#">Chính sách bảo mật</a></li>
                    </ul>
                </div>

                <div>
                    <h4>Hỗ trợ khách hàng</h4>
                    <ul>
                        <li><a href="#">Hướng dẫn mua hàng</a></li>
                        <li><a href="#">Hướng dẫn thanh toán</a></li>
                        <li><a href="#">Kiểm tra đơn hàng</a></li>
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                    </ul>
                </div>

                <div style="position: relative;">
                    <h4>Liên hệ</h4>
                    <ul class="footer-contact">
                        <li><i class="fa fa-phone"></i> Hotline: 1900 1234</li>
                        <li><i class="fa fa-envelope-o"></i> support@dangduc.vn</li>
                        <li><i class="fa fa-map-marker"></i> Địa chỉ: 123 Nguyễn Văn Cừ,<br>Long Biên, Hà Nội</li>
                    </ul>
                    <div class="moct-badge" title="Đã thông báo Bộ Công Thương">
                        Đã thông báo<br>Bộ Công Thương
                    </div>
                </div>
            </div>

            <div class="footer-bot">
                <span>&copy; {{ date('Y') }} DangDuc. All rights reserved.</span>
                <div class="footer-pays">
                    <span class="pay-visa">VISA</span>
                    <span class="pay-mc">mastercard</span>
                    <span class="pay-momo">momo</span>
                    <span class="pay-zalo">ZaloPay</span>
                    <span class="pay-napas">napas 247</span>
                </div>
            </div>
        </div>
    </footer>

    <a href="{{ $contactUrl }}" class="chat-fab" aria-label="Chat hỗ trợ">
        <i class="fa fa-comment"></i>
    </a>
</body>
</html>
