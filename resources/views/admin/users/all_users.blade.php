@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-user-list">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Người dùng</span>
                <h1>Danh sách người dùng quản trị</h1>
                <p>
                    Quản lý tài khoản admin, chỉnh vai trò nhanh và xóa người dùng khi không còn sử dụng.
                    Luồng `assign-roles` và `delete-user-roles` vẫn giữ nguyên.
                </p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($admin->total()) }}</strong>
                    <span>Tổng người dùng</span>
                </article>
                <article>
                    <strong>{{ number_format($roles->count()) }}</strong>
                    <span>Vai trò có thể gán</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Danh sách tài khoản quản trị</div>
                    <p>Vai trò được hiển thị trực tiếp và đồng bộ theo một vai trò chính cho từng tài khoản.</p>
                </div>
                <a href="{{ url('/add-users') }}" class="btn btn-primary btn-sm">Thêm người dùng</a>
            </div>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($admin->isEmpty())
                    <div class="g-admin-empty-state">Chưa có người dùng nào để quản lý.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light g-admin-data-table">
                            <thead>
                                <tr>
                                    <th>Tài khoản</th>
                                    <th>Liên hệ</th>
                                    <th>Vai trò hiện tại</th>
                                    <th>Đồng bộ vai trò</th>
                                    <th>Quản lý</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($admin as $user)
                                    <tr>
                                        <td>
                                            <div class="g-admin-order-line-name">{{ $user->name }}</div>
                                            <small class="text-muted">ID: {{ $user->id }}</small>
                                        </td>
                                        <td>
                                            <div>{{ $user->email }}</div>
                                            <small class="text-muted">{{ $user->phone ?? 'Chưa có số điện thoại' }}</small>
                                        </td>
                                        <td>
                                            <div class="g-admin-chip-grid">
                                                @forelse ($user->roles as $role)
                                                    <span class="g-admin-status-chip is-info">{{ $role->name }}</span>
                                                @empty
                                                    <span class="g-admin-status-chip">Chưa có vai trò</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td>
                                            <form action="{{ url('/assign-roles') }}" method="POST" class="g-admin-role-sync-form">
                                                @csrf
                                                <input type="hidden" name="admin_email" value="{{ $user->email }}">
                                                <input type="hidden" name="admin_id" value="{{ $user->id }}">

                                                <div class="form-group" style="min-width: 220px;">
                                                    <select name="roles[]" class="form-control input-sm">
                                                        <option value="">-- Chưa gán vai trò --</option>
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->name }}"
                                                                {{ $user->roles->contains('name', $role->name) ? 'selected' : '' }}>
                                                                {{ ucfirst($role->name) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <button type="submit" class="btn btn-sm btn-default">Cập nhật vai trò</button>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="g-admin-action-stack g-admin-user-actions">
                                                <a href="{{ url('/create-spatie/' . $user->id) }}" class="btn btn-xs btn-primary">Vai trò</a>
                                                <a href="{{ url('/create-permission/' . $user->id) }}" class="btn btn-xs btn-info">Quyền</a>
                                                <form action="{{ url('/delete-user-roles/' . $user->id) }}" method="POST"
                                                    onsubmit="return confirm('Bạn có chắc là muốn xóa người dùng này không?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-danger">Xóa người dùng</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="panel-footer">
                @include('admin.partials.pagination', ['paginator' => $admin, 'label' => 'người dùng'])
            </div>
        </section>
    </div>
@endsection
