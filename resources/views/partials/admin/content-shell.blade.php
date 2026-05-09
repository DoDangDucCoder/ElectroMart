        {{-- Admin content shell and footer extracted from admin_layout.blade.php. --}}
        <!--sidebar end-->
        <!--main content start-->
        <section id="main-content">
            <section class="wrapper">
                @php
                    $adminCreateLocked = (bool) config('admin_features.create_locked', false);
                    $adminCreateLockedNotice = (string) config('admin_features.create_locked_notice', 'Chức năng thêm mới đang tạm khóa để ổn định dữ liệu. Vui lòng chỉ xem tham khảo.');
                    $adminCreateLockedPaths = (array) config('admin_features.create_form_paths', []);
                    $adminCurrentPath = trim(request()->path(), '/');
                    $adminCreateFormLocked = $adminCreateLocked && \Illuminate\Support\Str::is($adminCreateLockedPaths, $adminCurrentPath);
                @endphp
                @if ($adminCreateFormLocked)
                    <div class="alert alert-warning" data-admin-create-lock-notice>
                        {{ $adminCreateLockedNotice }}
                    </div>
                @endif
                @yield('admin_content')
            </section>
            <!-- footer -->
            <div class="footer">
                <div class="wthree-copyright">
                    <p>&copy; <span id="year"></span> {{ config('services.storefront_brand.name', 'DangDuc') }}. Quản trị hệ thống bán hàng.</p>
                </div>
            </div>

            <script>
                document.getElementById('year').textContent = new Date().getFullYear();
            </script>
            <!-- / footer -->
        </section>
        <!--main content end-->
