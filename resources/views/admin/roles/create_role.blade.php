@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-role-create">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Vai trò</span>
                <h1>Cấp vai trò cho {{ $user->name }}</h1>
                <p>Chọn vai trò phù hợp cho tài khoản này.</p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ $user_roles?->name ?? 'Chưa có' }}</strong>
                    <span>Vai trò hiện tại</span>
                </article>
                <article>
                    <strong>{{ number_format($roles->count()) }}</strong>
                    <span>Vai trò có thể chọn</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Danh sách vai trò</div>
                    <p>Mỗi tài khoản nên có vai trò phù hợp với phạm vi công việc được giao.</p>
                </div>
            </div>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                <form action="{{ URL::to('/assign-role', $user->id) }}" method="post">
                    @csrf

                    <div class="g-admin-radio-grid">
                        @foreach ($roles as $role)
                            <label class="g-admin-choice-card">
                                <input type="radio" name="role[]" value="{{ $role->name }}"
                                    {{ optional($user_roles)->name === $role->name ? 'checked' : '' }}>
                                <span>{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="g-admin-inline-actions" style="margin-top:18px;">
                        <button type="submit" class="btn btn-info">Cập nhật vai trò</button>
                        <a href="{{ url('/index-spatie') }}" class="btn btn-default">Quay lại ma trận quyền</a>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
