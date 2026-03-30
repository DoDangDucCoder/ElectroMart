@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-role-index">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Vai trò & quyền</span>
                <h1>Liệt kê vai trò và quyền user</h1>
                <p>
                    Theo dõi vai trò hiện tại của từng tài khoản và các quyền đi kèm.
                </p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($user->count()) }}</strong>
                    <span>Tài khoản đang hiển thị</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Ma trận phân quyền</div>
                    <p>Kiểm tra vai trò và quyền đang áp dụng cho từng tài khoản.</p>
                </div>
            </div>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                @if ($user->isEmpty())
                    <div class="g-admin-empty-state">Chưa có user nào để hiển thị ma trận phân quyền.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light g-admin-data-table">
                            <thead>
                                <tr>
                                    <th>Tên user</th>
                                    <th>Email</th>
                                    <th>Vai trò</th>
                                    <th>Quyền thừa hưởng</th>
                                    <th>Quản lý</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user as $u)
                                    @php
                                        $rolePermissions = $u->getPermissionsViaRoles();
                                    @endphp
                                    <tr>
                                        <td>{{ $u->name }}</td>
                                        <td>{{ $u->email }}</td>
                                        <td>
                                            <div class="g-admin-chip-grid">
                                                @forelse ($u->roles as $roleItem)
                                                    <span class="g-admin-status-chip is-info">{{ $roleItem->name }}</span>
                                                @empty
                                                    <span class="g-admin-status-chip">Chưa có role</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td>
                                            <div class="g-admin-chip-grid">
                                                @forelse ($rolePermissions as $per)
                                                    <span class="g-admin-status-chip">{{ $per->name }}</span>
                                                @empty
                                                    <span class="text-muted">Chưa có quyền qua role</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td>
                                            <div class="g-admin-action-stack">
                                                <a href="{{ url('create-spatie/' . $u->id) }}" class="btn btn-primary btn-xs">Cấp vai trò</a>
                                                <a href="{{ url('create-permission/' . $u->id) }}" class="btn btn-danger btn-xs">Cấp quyền</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
