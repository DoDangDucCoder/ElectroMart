<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Đăng ký quản trị</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('backend/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/font-awesome.css') }}">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #0f1f34, #173454 55%, #224b73);
            color: #11233b;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .admin-auth-disabled {
            width: min(100%, 560px);
            background: rgba(255, 255, 255, 0.97);
            border-radius: 24px;
            padding: 36px 32px;
            text-align: center;
            box-shadow: 0 24px 60px rgba(5, 18, 34, 0.32);
        }

        .admin-auth-disabled .icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 18px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 28px;
            color: #0f5ed7;
            background: rgba(15, 94, 215, 0.12);
        }

        .admin-auth-disabled h1 {
            margin: 0 0 12px;
            font-size: 30px;
            line-height: 1.2;
        }

        .admin-auth-disabled p {
            margin: 0 0 12px;
            color: #5f748f;
            line-height: 1.7;
        }

        .admin-auth-disabled a {
            color: #0f5ed7;
            font-weight: 700;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <section class="admin-auth-disabled">
        <div class="icon">
            <i class="fa fa-lock"></i>
        </div>
        <h1>Đăng ký quản trị đã bị vô hiệu hóa</h1>
        <p>Tài khoản quản trị mới không thể tự đăng ký công khai trên hệ thống.</p>
        <p>Nếu cần cấp quyền truy cập, hãy liên hệ quản trị viên hệ thống hiện tại.</p>
        <p><a href="{{ url('/admin') }}">Quay lại màn hình đăng nhập quản trị</a></p>
    </section>
</body>

</html>
