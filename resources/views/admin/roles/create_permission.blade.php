@extends('admin_layout')

@section('admin_content')
    @php
        $selectedPermissions = collect($pers_role)->pluck('name')->all();
    @endphp

    <div class="g-admin-page-shell g-admin-permission-create">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Quyền hạn</span>
                <h1>Cập nhật quyền cho {{ $user->name }}</h1>
                <p>Trang này cập nhật quyền cho vai trò hiện tại của tài khoản, không gán quyền trực tiếp riêng lẻ lên người dùng.</p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ $name_roles ?? 'Chưa có vai trò' }}</strong>
                    <span>Vai trò đang áp dụng</span>
                </article>
                <article>
                    <strong>{{ number_format($permission->count()) }}</strong>
                    <span>Quyền có thể chọn</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Danh sách quyền</div>
                    <p>Đánh dấu những quyền cần áp dụng cho tài khoản ở thời điểm hiện tại.</p>
                </div>
            </div>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @unless ($hasRole ?? false)
                    <div class="alert alert-warning">
                        Tài khoản này chưa có vai trò. Hãy gán một vai trò trước khi cập nhật quyền.
                    </div>
                @endunless

                <form action="{{ URL::to('/assign-permission', $user->id) }}" method="post">
                    @csrf

                    <div class="g-admin-check-grid">
                        @foreach ($permission as $per)
                            <label class="g-admin-check-card">
                                <input class="form-check-input" type="checkbox"
                                    name="permission[]"
                                    value="{{ $per->name }}"
                                    id="permission-{{ $per->id }}"
                                    {{ in_array($per->name, $selectedPermissions, true) ? 'checked' : '' }}>
                                <span>{{ $per->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="g-admin-inline-actions" style="margin-top:18px;">
                        <button type="submit" class="btn btn-info" @disabled(! ($hasRole ?? false))>Cập nhật quyền</button>
                        <a href="{{ url('/index-spatie') }}" class="btn btn-default">Quay lại ma trận quyền</a>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
