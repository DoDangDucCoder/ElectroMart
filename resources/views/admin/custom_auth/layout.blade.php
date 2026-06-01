<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('backend/css/app.css') }}">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: "Helvetica Neue", Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(32, 150, 243, 0.2), transparent 35%),
                radial-gradient(circle at bottom right, rgba(11, 61, 145, 0.2), transparent 28%),
                linear-gradient(135deg, #0b172a 0%, #12243c 48%, #1c3555 100%);
            color: #10223b;
        }

        .admin-auth-shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 16px;
        }

        .admin-auth-card {
            width: min(100%, 980px);
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(320px, 420px);
            background: rgba(255, 255, 255, 0.96);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(8, 20, 40, 0.32);
        }

        .admin-auth-brand {
            padding: 48px;
            background: linear-gradient(180deg, rgba(12, 32, 61, 0.96), rgba(18, 58, 101, 0.94));
            color: #eef6ff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 28px;
        }

        .admin-auth-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .admin-auth-brand h1 {
            margin: 0 0 14px;
            font-size: clamp(28px, 4vw, 42px);
            line-height: 1.15;
        }

        .admin-auth-brand p {
            margin: 0;
            font-size: 16px;
            line-height: 1.7;
            color: rgba(238, 246, 255, 0.82);
        }

        .admin-auth-note-list {
            display: grid;
            gap: 10px;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .admin-auth-note-list li {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            color: rgba(238, 246, 255, 0.84);
            font-size: 14px;
        }

        .admin-auth-note-list i {
            margin-top: 3px;
            color: #8ec5ff;
        }

        .admin-auth-feature-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .admin-auth-feature-card {
            min-height: 142px;
            display: grid;
            grid-template-rows: 30px auto 1fr;
            align-items: start;
            justify-items: center;
            gap: 8px;
            padding: 18px 14px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .06);
            text-align: center;
        }

        .admin-auth-feature-card i {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #60a5fa;
            font-size: 24px;
            line-height: 1;
        }

        .admin-auth-feature-title {
            min-height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.35;
        }

        .admin-auth-feature-copy {
            color: rgba(255, 255, 255, .56);
            font-size: 10px;
            line-height: 1.45;
        }

        .admin-auth-form {
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 20px;
        }

        .admin-auth-form h2 {
            margin: 0;
            font-size: 30px;
            line-height: 1.2;
            color: #0c203d;
        }

        .admin-auth-form p {
            margin: 0;
            color: #58708f;
            line-height: 1.6;
        }

        .admin-auth-form label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #28415f;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .admin-auth-form .form-control {
            height: 48px;
            border-radius: 14px;
            border: 1px solid #d4dfed;
            box-shadow: none;
            padding: 12px 14px;
        }

        .admin-auth-form textarea.form-control {
            height: auto;
            min-height: 96px;
            resize: vertical;
        }

        .admin-auth-form .form-control:focus {
            border-color: #2f79d9;
            box-shadow: 0 0 0 3px rgba(47, 121, 217, 0.14);
        }

        .admin-auth-form .checkbox {
            margin: 0;
            color: #425874;
        }

        .admin-auth-form .checkbox label {
            margin: 0;
            font-size: 14px;
            font-weight: 500;
            text-transform: none;
            letter-spacing: 0;
            color: #425874;
        }

        .admin-auth-form .btn-primary {
            height: 48px;
            border-radius: 14px;
            border: 0;
            background: linear-gradient(135deg, #1f6feb, #1c8df6);
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .admin-auth-form .btn-default {
            height: 48px;
            border-radius: 14px;
            border: 1px solid #d4dfed;
            background: #fff;
            font-weight: 700;
            color: #28415f;
        }

        .admin-auth-alert {
            margin: 0;
            border-radius: 14px;
            padding: 14px 16px;
        }

        .admin-auth-alert ul {
            margin: 0;
            padding-left: 18px;
        }

        .admin-auth-grid {
            display: grid;
            gap: 16px;
        }

        .admin-auth-grid--double {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .admin-auth-link-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
        }

        .admin-auth-link {
            color: #1f6feb;
            font-weight: 600;
            text-decoration: none;
        }

        .admin-auth-link:hover,
        .admin-auth-link:focus {
            color: #0e4fa8;
            text-decoration: none;
        }

        .admin-auth-meta {
            padding: 14px 16px;
            border-radius: 16px;
            background: #f6f9fd;
            border: 1px solid #e1e9f4;
            color: #425874;
            font-size: 14px;
            line-height: 1.7;
        }

        .admin-auth-meta strong {
            color: #10223b;
        }

        @media (max-width: 900px) {
            .admin-auth-card {
                grid-template-columns: 1fr;
            }

            .admin-auth-brand,
            .admin-auth-form {
                padding: 32px 24px;
            }

            .admin-auth-grid--double {
                grid-template-columns: 1fr;
            }

            .admin-auth-feature-grid {
                grid-template-columns: 1fr;
            }

            .admin-auth-link-row {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        .admin-auth-shell {
            background:
                radial-gradient(circle at 8% 8%, rgba(37, 99, 235, .10), transparent 28%),
                radial-gradient(circle at 92% 84%, rgba(37, 99, 235, .10), transparent 30%),
                linear-gradient(135deg, #f8fbff 0%, #eef5ff 48%, #f9fbff 100%);
            place-items: stretch;
            padding: 14px 40px;
        }

        .admin-auth-card {
            width: min(100%, 1440px);
            min-height: calc(100vh - 28px);
            margin: 0 auto;
            grid-template-columns: minmax(0, 1.25fr) minmax(380px, 560px);
            background: transparent;
            border-radius: 0;
            overflow: visible;
            box-shadow: none;
            gap: 44px;
            align-items: center;
        }

        .admin-auth-brand {
            min-height: 560px;
            padding: 24px 0;
            background: transparent;
            color: #0b1d4d;
            justify-content: center;
            gap: 34px;
            position: relative;
            overflow: hidden;
        }

        .admin-auth-brand::after {
            content: "";
            position: absolute;
            right: -56px;
            top: 78px;
            width: 280px;
            height: 280px;
            border-radius: 999px;
            background: repeating-radial-gradient(circle, rgba(37, 99, 235, .10) 0 1px, transparent 1px 26px);
            opacity: .75;
        }

        .admin-auth-badge {
            background: transparent;
            color: #0b1d4d;
            padding: 0;
            letter-spacing: .08em;
            font-size: 15px;
        }

        .admin-auth-badge i {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            background: linear-gradient(135deg, #2563eb, #60a5fa);
            color: #fff;
            box-shadow: 0 12px 28px rgba(37, 99, 235, .25);
        }

        .admin-auth-brand h1 {
            max-width: 620px;
            margin-top: 24px;
            font-size: clamp(38px, 4.1vw, 56px);
            line-height: 1.08;
            letter-spacing: 0;
            color: #0b1d4d;
        }

        .admin-auth-brand h1 strong {
            display: block;
            color: #2563eb;
        }

        .admin-auth-brand p {
            max-width: 560px;
            margin-top: 16px;
            color: #132657;
            font-size: 17px;
            line-height: 1.5;
        }

        .admin-auth-brand p::before {
            content: "";
            display: block;
            width: 58px;
            height: 4px;
            margin: 0 0 26px;
            border-radius: 999px;
            background: linear-gradient(90deg, #2563eb 0 65%, #bfdbfe 65% 100%);
        }

        .admin-auth-note-list {
            position: relative;
            z-index: 1;
        }

        .admin-login-illustration {
            position: relative;
            min-height: 210px;
            margin-top: 0;
        }

        .admin-login-platform {
            position: absolute;
            left: 0;
            right: 170px;
            bottom: 6px;
            height: 54px;
            border-radius: 50%;
            background: linear-gradient(180deg, #ffffff, #dfeaff);
            box-shadow: inset 0 -18px 30px rgba(37, 99, 235, .06), 0 26px 60px rgba(37, 99, 235, .10);
        }

        .admin-login-laptop {
            position: absolute;
            left: 130px;
            bottom: 38px;
            width: 250px;
            height: 116px;
            border-radius: 18px 18px 8px 8px;
            transform: perspective(520px) rotateX(10deg) rotateZ(-2deg);
            background: linear-gradient(135deg, #9fb4e8, #d6e2ff 52%, #7b91d0);
            box-shadow: 0 28px 48px rgba(30, 64, 175, .20);
        }

        .admin-login-laptop::before {
            content: "\f132";
            font-family: FontAwesome;
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            color: rgba(255, 255, 255, .82);
            font-size: 42px;
        }

        .admin-login-laptop::after {
            content: "";
            position: absolute;
            left: -28px;
            right: -76px;
            bottom: -30px;
            height: 34px;
            border-radius: 7px 7px 20px 20px;
            background: linear-gradient(90deg, #c7d7ff, #edf4ff 55%, #9db4ec);
        }

        .admin-login-cup {
            position: absolute;
            left: 410px;
            bottom: 28px;
            width: 48px;
            height: 54px;
            border-radius: 10px 10px 24px 24px;
            background: linear-gradient(135deg, #1d4ed8, #60a5fa);
            box-shadow: 0 18px 36px rgba(37, 99, 235, .25);
        }

        .admin-login-cup::after {
            content: "";
            position: absolute;
            right: -30px;
            top: 18px;
            width: 38px;
            height: 38px;
            border: 8px solid #2563eb;
            border-left: 0;
            border-radius: 0 26px 26px 0;
        }

        .admin-login-plant {
            position: absolute;
            left: 24px;
            bottom: 34px;
            width: 54px;
            height: 66px;
            border-radius: 0 0 28px 28px;
            background: linear-gradient(180deg, #fff, #dfeaff);
            box-shadow: 0 18px 30px rgba(37, 99, 235, .10);
        }

        .admin-login-plant::before {
            content: "";
            position: absolute;
            left: 8px;
            bottom: 72px;
            width: 92px;
            height: 90px;
            background:
                radial-gradient(ellipse at 20% 70%, #60a5fa 0 18px, transparent 19px),
                radial-gradient(ellipse at 45% 46%, #93c5fd 0 22px, transparent 23px),
                radial-gradient(ellipse at 72% 68%, #3b82f6 0 20px, transparent 21px);
            opacity: .85;
        }

        .admin-login-orbit {
            position: absolute;
            left: 280px;
            top: 0;
            width: 300px;
            height: 210px;
            border: 2px dashed rgba(37, 99, 235, .24);
            border-radius: 50%;
            transform: rotate(-18deg);
        }

        .admin-login-orbit::before,
        .admin-login-orbit::after {
            content: "";
            position: absolute;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #60a5fa;
            box-shadow: 42px 36px 0 #2563eb;
        }

        .admin-login-orbit::before {
            right: 12px;
            top: 100px;
        }

        .admin-login-orbit::after {
            right: 88px;
            bottom: 8px;
        }

        .admin-login-float {
            position: absolute;
            width: 56px;
            height: 56px;
            display: grid;
            place-items: center;
            border-radius: 18px;
            background: rgba(255, 255, 255, .84);
            box-shadow: 0 22px 44px rgba(37, 99, 235, .14);
            color: #2563eb;
            font-size: 23px;
        }

        .admin-login-float.clipboard {
            left: 308px;
            top: 30px;
        }

        .admin-login-float.bag {
            left: 424px;
            top: 72px;
        }

        .admin-login-float.users {
            left: 352px;
            top: 120px;
        }

        .admin-login-float.chart {
            left: 492px;
            top: 148px;
        }

        .admin-auth-feature-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 30px;
            margin-top: 8px;
        }

        .admin-auth-feature-card {
            min-height: auto;
            grid-template-columns: 52px 1fr;
            grid-template-rows: auto auto;
            justify-items: start;
            text-align: left;
            gap: 4px 14px;
            padding: 0;
            background: transparent;
        }

        .admin-auth-feature-card i {
            grid-row: 1 / 3;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #fff;
            color: #2563eb;
            font-size: 24px;
            box-shadow: 0 14px 28px rgba(37, 99, 235, .16);
        }

        .admin-auth-feature-title {
            min-height: 0;
            color: #0b1d4d;
            font-size: 15px;
            justify-content: flex-start;
        }

        .admin-auth-feature-copy {
            color: #283b68;
            font-size: 13px;
        }

        .admin-auth-form {
            width: min(100%, 520px);
            min-height: auto;
            justify-self: end;
            padding: 26px 42px;
            border-radius: 18px;
            background: rgba(255, 255, 255, .92);
            border: 1px solid rgba(226, 232, 240, .86);
            box-shadow: 0 28px 80px rgba(30, 64, 175, .14);
            backdrop-filter: blur(16px);
        }

        .admin-auth-avatar {
            width: 78px;
            height: 78px;
            margin: 0 auto 12px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: linear-gradient(180deg, #dbeafe, #eff6ff);
            color: #2563eb;
            font-size: 42px;
        }

        .admin-auth-form h2 {
            text-align: center;
            color: #0b1d4d;
            font-size: 26px;
        }

        .admin-auth-form p {
            text-align: center;
            color: #283b68;
            line-height: 1.4;
        }

        .admin-auth-form label {
            color: #0b1d4d;
            margin-bottom: 6px;
        }

        .admin-auth-field {
            position: relative;
        }

        .admin-auth-field .form-control {
            height: 46px;
            padding-left: 58px;
            padding-right: 52px;
            border-radius: 11px;
            font-size: 15px;
            color: #0b1d4d;
            background: rgba(255, 255, 255, .96);
        }

        .admin-auth-field-icon {
            position: absolute;
            z-index: 2;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            color: #2563eb;
            font-size: 20px;
            pointer-events: none;
        }

        .admin-auth-password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            font-size: 20px;
        }

        .admin-auth-form .btn-primary {
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #2563eb, #2f79f6);
            box-shadow: 0 16px 30px rgba(37, 99, 235, .22);
            font-size: 18px;
        }

        .admin-auth-form .btn-default {
            height: 48px;
            border-radius: 12px;
            font-size: 18px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .05);
        }

        .admin-auth-separator {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 18px;
            color: #475569;
            font-size: 13px;
            font-weight: 700;
            margin: 4px 0;
        }

        .admin-auth-separator::before,
        .admin-auth-separator::after {
            content: "";
            height: 1px;
            background: #e2e8f0;
        }

        .admin-auth-safe-box {
            display: grid;
            grid-template-columns: 52px 1fr;
            gap: 16px;
            align-items: center;
            padding: 12px 16px;
            border-radius: 12px;
            background: linear-gradient(135deg, #ecfdf5, #f0fdf4);
            color: #14532d;
        }

        .admin-auth-safe-box i {
            color: #22c55e;
            font-size: 38px;
        }

        .admin-auth-safe-box strong {
            display: block;
            color: #15803d;
            font-size: 15px;
            margin-bottom: 4px;
        }

        .admin-auth-safe-box span {
            color: #475569;
            font-size: 12px;
            line-height: 1.45;
        }

        @media (max-width: 1100px) {
            .admin-auth-shell {
                padding: 24px;
            }

            .admin-auth-card {
                grid-template-columns: 1fr;
                gap: 28px;
            }

            .admin-auth-brand,
            .admin-auth-form {
                min-height: auto;
                width: 100%;
                justify-self: stretch;
            }
        }

        @media (max-width: 700px) {
            .admin-auth-shell {
                padding: 16px;
            }

            .admin-auth-brand h1 {
                margin-top: 32px;
                font-size: 42px;
            }

            .admin-auth-brand p {
                font-size: 17px;
            }

            .admin-login-illustration {
                display: none;
            }

            .admin-auth-feature-grid {
                grid-template-columns: 1fr;
            }

            .admin-auth-form {
                padding: 32px 22px;
            }
        }

        @media (min-width: 1101px) and (max-height: 850px) {
            .admin-auth-shell {
                padding-top: 10px;
                padding-bottom: 10px;
            }

            .admin-auth-card {
                min-height: calc(100vh - 20px);
            }

            .admin-auth-brand {
                min-height: 520px;
            }

            .admin-auth-brand h1 {
                margin-top: 18px;
                font-size: clamp(34px, 3.6vw, 50px);
            }

            .admin-auth-brand p {
                margin-top: 12px;
                font-size: 15px;
            }

            .admin-auth-brand p::before {
                margin-bottom: 16px;
            }

            .admin-login-illustration {
                min-height: 180px;
            }

            .admin-auth-feature-grid {
                margin-top: 4px;
            }

            .admin-auth-feature-card i {
                width: 44px;
                height: 44px;
                font-size: 20px;
            }

            .admin-auth-form {
                padding: 20px 38px;
            }

            .admin-auth-avatar {
                width: 66px;
                height: 66px;
                margin-bottom: 8px;
                font-size: 34px;
            }

            .admin-auth-form h2 {
                font-size: 24px;
            }

            .admin-auth-form p {
                font-size: 13px;
            }

            .admin-auth-field .form-control {
                height: 42px;
            }

            .admin-auth-form .btn-primary,
            .admin-auth-form .btn-default {
                height: 44px;
                font-size: 16px;
            }

            .admin-auth-safe-box {
                padding: 10px 14px;
            }
        }
    </style>
</head>

<body>
    <main class="admin-auth-shell">
        <section class="admin-auth-card">
            <div class="admin-auth-brand">
                <div>
                    <span class="admin-auth-badge">
                        <i class="fa fa-shield"></i>
                        @yield('badge', 'Quản trị hệ thống')
                    </span>
                    <h1>@yield('hero_title')</h1>
                    <p>@yield('hero_copy')</p>
                </div>
                @hasSection('hero_notes')
                    <ul class="admin-auth-note-list">
                        @yield('hero_notes')
                    </ul>
                @endif
            </div>

            <div class="admin-auth-form">
                <div>
                    <h2>@yield('form_title')</h2>
                    <p>@yield('form_intro')</p>
                </div>

                @if (session('message'))
                    <div class="alert alert-success admin-auth-alert">{{ session('message') }}</div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success admin-auth-alert">{{ session('status') }}</div>
                @endif

                @if (isset($errors) && $errors->any())
                    <div class="alert alert-danger admin-auth-alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (! empty($adminAuthDatabaseUnavailable))
                    <div class="alert alert-warning admin-auth-alert">
                        {{ $adminAuthDatabaseUnavailableMessage }}
                    </div>
                @endif

                @yield('form_body')
            </div>
        </section>
    </main>
</body>

</html>
