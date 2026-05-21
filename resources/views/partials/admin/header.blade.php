        {{-- Admin top header extracted from admin_layout.blade.php. --}}
        <!--header start-->
        <header class="header fixed-top clearfix">
            @php
                $adminUser = auth('web')->user();
                $adminRawDisplayName = trim((string) ($adminUser?->name ?: 'Quản trị viên'));
                $adminDisplayName = strcasecmp($adminRawDisplayName, 'Super Admin') === 0
                    ? 'Quản trị viên cấp cao'
                    : $adminRawDisplayName;
                $adminAvatarPath = asset('backend/images/admin-avatar-default.svg');
                $adminShellTitle = 'Bảng quản trị';
                $adminInitials = (string) \Illuminate\Support\Str::of($adminDisplayName)
                    ->explode(' ')
                    ->filter()
                    ->take(2)
                    ->map(fn (string $part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
                    ->implode('');
                $adminQuickSearchSuggestions = \App\Support\AdminWorkspace::topSuggestions($adminUser, 8);
            @endphp
            <div class="g-admin-shell-leading">
                <!--logo start-->
                <div class="brand g-admin-shell-brand">
                    <a target="_blank" href="{{ url('/') }}" class="logo g-admin-shell-logo">
                        <span class="g-admin-shell-logo-copy">{{ $adminShellTitle }}</span>
                    </a>
                    <button type="button"
                        class="sidebar-toggle-box g-admin-shell-toggle"
                        data-admin-sidebar-toggle
                        aria-label="Thu gọn thanh điều hướng quản trị"
                        aria-controls="sidebar"
                        aria-expanded="true"
                        data-admin-sidebar-label-expanded="Thu gọn thanh điều hướng quản trị"
                        data-admin-sidebar-label-collapsed="Mở rộng thanh điều hướng quản trị"
                        data-admin-sidebar-label-mobile-open="Đóng menu quản trị"
                        data-admin-sidebar-label-mobile-closed="Mở menu quản trị">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <!--logo end-->

            <div class="top-nav clearfix">
                <!--search & user info start-->
                <ul class="nav pull-right top-menu">
                    <li class="g-admin-top-search">
                        <form action="{{ route('admin.tools.search') }}" method="GET" class="g-admin-top-search-form" autocomplete="off">
                            <label class="sr-only" for="admin-top-search">Tìm kiếm nhanh trong quản trị</label>
                            <input type="search"
                                id="admin-top-search"
                                name="q"
                                list="admin-top-search-suggestions"
                                class="form-control g-admin-top-search-input"
                                value="{{ request()->routeIs('admin.tools.search') ? request('q') : '' }}"
                                placeholder="Tìm màn hình quản trị">
                            <datalist id="admin-top-search-suggestions">
                                @foreach ($adminQuickSearchSuggestions as $destination)
                                    <option value="{{ $destination['title'] }}">{{ $destination['section'] }}</option>
                                @endforeach
                            </datalist>
                            <button type="submit" class="g-admin-top-search-btn" aria-label="Tìm nhanh màn hình quản trị">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </button>
                        </form>
                    </li>
                    <!-- user login dropdown start-->
                    <li class="dropdown g-admin-user-menu">
                        <a data-toggle="dropdown" class="dropdown-toggle g-admin-user-toggle" href="#">
                            <span class="g-admin-user-avatar" aria-hidden="true">
                                <span class="g-admin-user-avatar-fallback">
                                    {{ $adminInitials !== '' ? $adminInitials : 'AD' }}
                                </span>
                                <img alt="" src="{{ $adminAvatarPath }}" width="34" height="34">
                            </span>
                            <span class="username">
                                {{ $adminDisplayName }}
                            </span>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu extended logout">
                            <li><a href="{{ route('admin.profile.show') }}"><i class="fa fa-suitcase"></i>Hồ sơ</a></li>
                            <li><a href="{{ route('admin.settings') }}"><i class="fa fa-cog"></i>Cài đặt</a></li>
                            <li>
                                <form action="{{ url('/logout-auth') }}" method="POST" class="g-admin-user-logout-form">
                                    @csrf
                                    <button type="submit"
                                        class="g-admin-user-logout-button">
                                        <i class="fa fa-sign-out"></i>Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    <!-- user login dropdown end -->

                </ul>
                <!--search & user info end-->
            </div>
        </header>
