        {{-- Admin sidebar navigation extracted from admin_layout.blade.php. --}}
        <!--sidebar start-->
        <aside>
            <div id="sidebar" class="nav-collapse">
                <div class="leftside-navigation">
                    <ul class="sidebar-menu" id="nav-accordion">
                        <li>
                            <a class="active" href="{{ URL::to('/dashboard') }}">
                                <i class="fa fa-dashboard"></i>
                                <span>Tổng quan</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ URL::to('/information') }}">
                                <i class="fa fa-info-circle"></i>
                                <span>Thông tin website</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ URL::to('/mail-preview') }}">
                                <i class="fa fa-envelope-o"></i>
                                <span>Xem trước email</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ URL::to('/product-discovery-report') }}">
                                <i class="fa fa-line-chart"></i>
                                <span>Tìm kiếm và gợi ý</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ URL::to('/chat-inbox') }}">
                                <i class="fa fa-comments-o"></i>
                                <span>Hộp thư chat</span>
                                @if (($admin_chat_sidebar_stats['unread_total'] ?? 0) > 0)
                                    <span class="g-admin-sidebar-badge" data-admin-chat-unread-badge>
                                        {{ number_format($admin_chat_sidebar_stats['unread_total']) }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-tags"></i>
                                <span>Mã giảm giá</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ URL::to('/insert-coupon') }}">Thêm mã giảm giá</a></li>
                                <li><a href="{{ URL::to('/list-coupon') }}">Liệt kê mã giảm giá</a></li>
                            </ul>
                        </li>

                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-truck"></i>
                                <span>Vận chuyển</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ URL::to('/delivery') }}">Quản lý vận chuyển</a></li>
                            </ul>
                        </li>

                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-list"></i>
                                <span>Danh mục sản phẩm</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ URL::to('/add-category-product') }}">Thêm danh mục sản phẩm</a></li>
                                <li><a href="{{ URL::to('/all-category-product') }}">Liệt kê danh mục sản phẩm</a></li>
                            </ul>
                        </li>

                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-industry"></i>
                                <span>Thương hiệu</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ URL::to('/add-brand-product') }}">Thêm thương hiệu</a></li>
                                <li><a href="{{ URL::to('/all-brand-product') }}">Liệt kê thương hiệu</a></li>
                            </ul>
                        </li>

                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-cogs"></i>
                                <span>Thuộc tính</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ route('attribute.create') }}">Thêm thuộc tính</a></li>
                                <li><a href="{{ route('attribute.index') }}">Liệt kê thuộc tính</a></li>
                            </ul>
                        </li>

                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-edit"></i>
                                <span>Danh mục bài viết</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ URL::to('/add-category-post') }}">Thêm danh mục bài viết</a></li>
                                <li><a href="{{ URL::to('/all-category-post') }}">Liệt kê danh mục bài viết</a></li>
                            </ul>
                        </li>

                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-sliders"></i>
                                <span>Slider</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ URL::to('/manage-slider') }}">Liệt kê slider</a></li>
                                <li><a href="{{ URL::to('/add-slider') }}">Thêm slider</a></li>
                            </ul>
                        </li>

                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-shopping-cart"></i>
                                <span>Đơn hàng</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ URL::to('/manage-order') }}">Quản lý đơn hàng</a></li>
                                <li><a href="{{ URL::to('/product-profit') }}">Lợi nhuận sản phẩm</a></li>
                            </ul>
                        </li>

                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-comments"></i>
                                <span>Bình luận</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ URL::to('/comment') }}">Liệt kê bình luận</a></li>
                            </ul>
                        </li>

                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-cube"></i>
                                <span>Sản phẩm</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ URL::to('/add-product') }}">Thêm sản phẩm</a></li>
                                <li><a href="{{ URL::to('/all-product') }}">Liệt kê sản phẩm</a></li>
                            </ul>
                        </li>

                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-file-text"></i>
                                <span>Bài viết</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ URL::to('/add-post') }}">Thêm bài viết</a></li>
                                <li><a href="{{ URL::to('/all-post') }}">Liệt kê bài viết</a></li>
                            </ul>
                        </li>

                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-video-camera"></i>
                                <span>Video</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ URL::to('video') }}">Thêm video</a></li>
                            </ul>
                        </li>

                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-users"></i>
                                <span>Người dùng</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ URL::to('/add-users') }}">Thêm người dùng</a></li>
                                <li><a href="{{ URL::to('/users') }}">Danh sách người dùng</a></li>
                                <li><a href="{{ URL::to('/index-spatie') }}">Vai trò và quyền</a></li>
                            </ul>
                        </li>

                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-folder-open"></i>
                                <span>Tài liệu</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ URL::to('/read_data') }}">Kho tài liệu</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>
