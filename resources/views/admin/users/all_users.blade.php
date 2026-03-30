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
                    <p>Vai trò được hiển thị trực tiếp và có thể đồng bộ nhanh theo từng tài khoản.</p>
                </div>
                <a href="{{ url('/add-users') }}" class="btn btn-primary btn-sm">Thêm người dùng</a>
            </div>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
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

                                                <div class="g-admin-role-option-list">
                                                    @foreach ($roles as $role)
                                                        <label class="g-admin-check-card">
                                                            <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                                                {{ $user->roles->contains('name', $role->name) ? 'checked' : '' }}>
                                                            <span>{{ ucfirst($role->name) }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>

                                                @if (\App\Support\AdminAccess::canAccess($user))
                                                    <button type="submit" class="btn btn-sm btn-default">Cập nhật vai trò</button>
                                                @endif
                                            </form>
                                        </td>
                                        <td>
                                            <div class="g-admin-action-stack">
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
                <div class="row">
                    <div class="col-sm-5 text-center">
                        <small class="text-muted inline m-t-sm m-b-sm">
                            Hiển thị {{ $admin->count() }} / {{ $admin->total() }} người dùng
                        </small>
                    </div>
                    <div class="col-sm-7 text-right text-center-xs">
                        {{ $admin->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
