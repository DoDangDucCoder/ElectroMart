<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Đăng nhập quản trị</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('backend/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/font-awesome.css') }}">
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
            grid-template-columns: minmax(0, 1.1fr) minmax(320px, 420px);
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

        .admin-auth-form .btn {
            height: 48px;
            border-radius: 14px;
            border: 0;
            background: linear-gradient(135deg, #1f6feb, #1c8df6);
            font-weight: 700;
            letter-spacing: 0.02em;
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

        .admin-auth-alert {
            margin: 0;
            border-radius: 14px;
            padding: 14px 16px;
        }

        .admin-auth-alert ul {
            margin: 0;
            padding-left: 18px;
        }

        @media (max-width: 900px) {
            .admin-auth-card {
                grid-template-columns: 1fr;
            }

            .admin-auth-brand,
            .admin-auth-form {
                padding: 32px 24px;
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
                        Quản trị hệ thống
                    </span>
                    <h1>Đăng nhập khu vực quản trị</h1>
                    <p>Truy cập bảng điều khiển để quản lý sản phẩm, nội dung, đơn hàng và cấu hình vận hành của website.</p>
                </div>
                <ul class="admin-auth-note-list">
                    <li><i class="fa fa-check-circle"></i><span>Chỉ tài khoản có quyền admin mới có thể tiếp tục.</span></li>
                    <li><i class="fa fa-check-circle"></i><span>Phiên đăng nhập được bảo vệ bằng CSRF token và kiểm tra phân quyền.</span></li>
                    <li><i class="fa fa-check-circle"></i><span>Nếu không đăng nhập được, hãy kiểm tra email hoặc quyền truy cập được cấp.</span></li>
                </ul>
            </div>

            <div class="admin-auth-form">
                <div>
                    <h2>Xin chào</h2>
                    <p>Đăng nhập bằng email quản trị để tiếp tục vào trang điều hành.</p>
                </div>

                @if (session('message'))
                    <div class="alert alert-success admin-auth-alert">{{ session('message') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger admin-auth-alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ url('/auth-login') }}" method="post" novalidate>
                    @csrf

                    <div class="form-group">
                        <label for="email">Email quản trị</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ old('email') }}" placeholder="admin@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Nhập mật khẩu" required>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember" value="1">
                                Ghi nhớ đăng nhập trên thiết bị này
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Đăng nhập quản trị</button>
                </form>

                <p>
                    <a href="{{ url('/') }}" class="admin-auth-link">Quay lại trang chủ website</a>
                </p>
            </div>
        </section>
    </main>
</body>

</html>
