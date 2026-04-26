@extends('layout')
@section('content')
    <div class="shopw-account-manager-page container">
        <nav aria-label="breadcrumb" class="shopw-pdp-breadcrumb" style="margin-bottom: 20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Quản lý tài khoản</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="shopw-account-sidebar">
                    <div class="shopw-account-user-card">
                        <div class="shopw-account-user-avatar">
                            <img src="{{ Session::get('storefront_customer_picture', asset('frontend/images/storefront-account-avatar.svg')) }}"
                                alt="Avatar">
                        </div>
                        <div class="shopw-account-user-info">
                            <strong>TÀI KHOẢN</strong>
                            <p>Xin chào {{ $customer->name }}</p>
                        </div>
                    </div>
                    <ul class="shopw-account-nav">
                        <li class="{{ $active_section == 'profile' ? 'active' : '' }}">
                            <a href="{{ route('customer.account', ['section' => 'profile']) }}">
                                <i class="fa fa-user"></i> Thông tin tài khoản
                            </a>
                        </li>
                        <li class="{{ $active_section == 'edit' ? 'active' : '' }}">
                            <a href="{{ route('customer.account', ['section' => 'edit']) }}">
                                <i class="fa fa-edit"></i> Chỉnh sửa thông tin
                            </a>
                        </li>
                        <li class="{{ $active_section == 'review' ? 'active' : '' }}">
                            <a href="{{ route('customer.account', ['section' => 'review']) }}">
                                <i class="fa fa-star"></i> Đánh giá website
                            </a>
                        </li>
                        <li class="{{ $active_section == 'password' ? 'active' : '' }}">
                            <a href="{{ route('customer.account', ['section' => 'password']) }}">
                                <i class="fa fa-key"></i> Đổi mật khẩu
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/dang-xuat-khach-hang') }}" class="text-danger">
                                <i class="fa fa-sign-out"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="shopw-account-content">
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul style="margin-bottom: 0;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($active_section == 'profile')
                        <div class="shopw-account-profile">
                            <div class="shopw-account-profile-header">
                                <div class="shopw-account-profile-avatar">
                                    <img src="{{ Session::get('storefront_customer_picture', asset('frontend/images/storefront-account-avatar.svg')) }}"
                                        alt="Avatar">
                                </div>
                                <div class="shopw-account-profile-meta">
                                    <h3>{{ $customer->name }}</h3>
                                    <p>{{ $customer->email }}</p>
                                    <a href="{{ route('customer.account', ['section' => 'password']) }}" class="btn btn-sm btn-default">Đổi mật khẩu</a>
                                </div>
                            </div>

                            <div class="shopw-account-section-title">
                                <h4>Thông tin tài khoản</h4>
                            </div>

                            <div class="shopw-account-details-list">
                                <div class="shopw-account-detail-row">
                                    <span class="label">Họ và tên</span>
                                    <span class="value">{{ $customer->name }}</span>
                                </div>
                                <div class="shopw-account-detail-row">
                                    <span class="label">Email</span>
                                    <span class="value">{{ $customer->email }}</span>
                                </div>
                                <div class="shopw-account-detail-row">
                                    <span class="label">Số điện thoại</span>
                                    <span class="value">{{ $customer->phone ?? 'Chưa cập nhật' }}</span>
                                </div>
                                <div class="shopw-account-detail-row">
                                    <span class="label">Ngày sinh</span>
                                    <span class="value">Chưa cập nhật</span>
                                </div>
                                <div class="shopw-account-detail-row">
                                    <span class="label">Giới tính</span>
                                    <span class="value">Chưa cập nhật</span>
                                </div>
                            </div>
                        </div>
                    @elseif($active_section == 'edit')
                        <div class="shopw-account-edit">
                            <div class="shopw-account-section-title">
                                <h4>Chỉnh sửa thông tin</h4>
                            </div>
                            <form action="{{ route('customer.account.update') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Họ và tên</label>
                                    <input type="text" name="name" class="form-control" value="{{ $customer->name }}"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>Số điện thoại</label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ $customer->phone }}">
                                </div>
                                <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Lưu thay đổi</button>
                            </form>
                        </div>
                    @elseif($active_section == 'review')
                        <div class="shopw-account-review">
                            <div class="shopw-account-section-title">
                                <h4>Đánh giá website của chúng tôi</h4>
                            </div>
                            <form action="{{ route('customer.account.review') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <textarea name="content" class="form-control" rows="6" placeholder="Cảm nhận của bạn..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Gửi đánh giá</button>
                            </form>
                        </div>
                    @elseif($active_section == 'password')
                        <div class="shopw-account-password">
                            <div class="shopw-account-section-title">
                                <h4>Đổi mật khẩu</h4>
                            </div>
                            <form action="{{ route('customer.account.change-password') }}" method="POST">
                                @csrf
                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label>Mật khẩu hiện tại</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label>Mật khẩu mới</label>
                                    <input type="password" name="new_password" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Xác nhận mật khẩu mới</label>
                                    <input type="password" name="new_password_confirmation" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Đổi mật khẩu</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .shopw-account-manager-page {
            padding-top: 30px;
            padding-bottom: 50px;
            background: #f8fafc;
        }

        .shopw-account-sidebar {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .shopw-account-user-card {
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        .shopw-account-user-avatar img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #3b82f6;
        }

        .shopw-account-user-info strong {
            display: block;
            font-size: 14px;
            color: #1e293b;
        }

        .shopw-account-user-info p {
            margin: 0;
            font-size: 12px;
            color: #64748b;
        }

        .shopw-account-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .shopw-account-nav li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 24px;
            color: #475569;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .shopw-account-nav li.active a {
            background: #f1f5f9;
            color: #3b82f6;
        }

        .shopw-account-nav li a:hover {
            background: #f8fafc;
            color: #3b82f6;
        }

        .shopw-account-nav li a i {
            width: 20px;
            text-align: center;
        }

        .shopw-account-content {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            min-height: 400px;
        }

        .shopw-account-profile-header {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 35px;
        }

        .shopw-account-profile-avatar img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #f1f5f9;
        }

        .shopw-account-profile-meta h3 {
            margin: 0 0 5px;
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
        }

        .shopw-account-profile-meta p {
            margin: 0 0 15px;
            color: #64748b;
        }

        .shopw-account-section-title {
            margin-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 10px;
        }

        .shopw-account-section-title h4 {
            margin: 0;
            font-weight: 700;
            color: #1e293b;
        }

        .shopw-account-details-list {
            display: grid;
            gap: 15px;
        }

        .shopw-account-detail-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #f8fafc;
        }

        .shopw-account-detail-row .label {
            width: 180px;
            color: #64748b;
            font-weight: 600;
        }

        .shopw-account-detail-row .value {
            color: #1e293b;
            font-weight: 700;
        }

        .shopw-account-manager-page .breadcrumb {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 10px 15px;
        }
    </style>
@endsection
