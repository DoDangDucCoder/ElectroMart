@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-user-create">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Người dùng</span>
                <h1>Thêm người dùng quản trị</h1>
                <p>
                    Tạo tài khoản admin mới để bàn giao cho nhân sự hoặc phân tách quyền vận hành.
                    Tài khoản mới sẽ được gán vai trò mặc định <strong>{{ config('admin-auth.provisioning.default_role', 'Staff') }}</strong>
                    để có thể đăng nhập an toàn trước khi phân quyền chi tiết.
                </p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>Web</strong>
                    <span>Guard quản trị đang sử dụng</span>
                </article>
                <article>
                    <strong>{{ config('admin-auth.provisioning.default_role', 'Staff') }}</strong>
                    <span>Vai trò mặc định khi tạo mới</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Thông tin tài khoản</div>
                    <p>Tạo tài khoản trước, hệ thống sẽ tự gán vai trò mặc định rồi bạn có thể tinh chỉnh thêm ở màn danh sách người dùng.</p>
                </div>
            </div>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                <form action="{{ URL::to('store-users') }}" method="post" class="g-admin-form-grid">
                    @csrf

                    <div class="form-group">
                        <label for="admin_name">Tên người dùng</label>
                        <input type="text" name="admin_name" class="form-control" id="admin_name" placeholder="Ví dụ: Nguyen Van B">
                    </div>

                    <div class="form-group">
                        <label for="admin_email">Email</label>
                        <input type="text" name="admin_email" class="form-control" id="admin_email" placeholder="quantri@congty.com">
                    </div>

                    <div class="form-group">
                        <label for="admin_phone">Số điện thoại</label>
                        <input type="text" name="admin_phone" class="form-control" id="admin_phone" placeholder="0909xxxxxx">
                    </div>

                    <div class="form-group">
                        <label for="admin_password">Mật khẩu</label>
                        <input type="text" name="admin_password" class="form-control" id="admin_password" placeholder="Mật khẩu tạm thời">
                    </div>

                    <div class="g-admin-inline-actions g-admin-form-span-2">
                        <button type="submit" class="btn btn-info">Thêm người dùng</button>
                        <a href="{{ url('/users') }}" class="btn btn-default">Xem danh sách người dùng</a>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
