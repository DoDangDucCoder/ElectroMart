        {{-- Admin top header extracted from admin_layout.blade.php. --}}
        <!--header start-->
        <header class="header fixed-top clearfix">
            <!--logo start-->
            <div class="brand">
                <a target="_blank" href="{{ url('/') }}" class="logo">
                    Quản trị cửa hàng
                </a>
                <div class="sidebar-toggle-box">
                    <div class="fa fa-bars"></div>
                </div>
            </div>
            <!--logo end-->

            <div class="top-nav clearfix">
                <!--search & user info start-->
                <ul class="nav pull-right top-menu">
                    <li>
                        <input type="text" class="form-control search" placeholder="Tìm kiếm nhanh trong quản trị">
                    </li>
                    <!-- user login dropdown start-->
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <img alt="" src="{{ 'backend/images/2.png' }}">
                            <span class="username">

                                {{-- <?php
                                if (Session::get('login_normal')) {
                                    $name = Session::get('admin_name');
                                } else {
                                    $name = Auth::user()->name;
                                }
                                
                                if ($name) {
                                    echo $name;
                                }
                                ?> --}}

                            </span>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu extended logout">
                            <li><a href="#"><i class=" fa fa-suitcase"></i>Hồ sơ</a></li>
                            <li><a href="#"><i class="fa fa-cog"></i>Cài đặt</a></li>
                            <li>
                                <form action="{{ url('/logout-auth') }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit"
                                        style="display:block;width:100%;padding:3px 20px;border:0;background:none;text-align:left;">
                                        <i class="fa fa-key"></i>Đăng xuất
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
