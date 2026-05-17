@extends('admin_layout')
@section('admin_content')
{{-- Preview-only mock page. Production user management is /users and role management is /index-spatie. --}}
@php
$admins = [
    ['id'=>'QTV001','name'=>'Nguyễn Văn An','email'=>'admin@dangduc.com','phone'=>'0901 234 567','role'=>'Super Admin','role_class'=>'red','dept'=>'Ban giám đốc','login'=>'24/05/2025 10:28','status'=>'Hoạt động','status_class'=>'green','new'=>true],
    ['id'=>'QTV002','name'=>'Trần Thị Bích Ngọc','email'=>'bichnhoc@dangduc.com','phone'=>'0912 345 678','role'=>'Admin','role_class'=>'blue','dept'=>'Quản lý','login'=>'24/05/2025 09:48','status'=>'Hoạt động','status_class'=>'green','new'=>false],
    ['id'=>'QTV003','name'=>'Lê Minh Đức','email'=>'minhduc@dangduc.com','phone'=>'0933 456 789','role'=>'Editor','role_class'=>'purple','dept'=>'Nội dung','login'=>'24/05/2025 09:15','status'=>'Hoạt động','status_class'=>'green','new'=>false],
    ['id'=>'QTV004','name'=>'Phạm Hoàng Nam','email'=>'hoangnam@dangduc.com','phone'=>'0909 876 543','role'=>'Admin','role_class'=>'blue','dept'=>'Marketing','login'=>'23/05/2025 18:30','status'=>'Hoạt động','status_class'=>'green','new'=>false],
    ['id'=>'QTV005','name'=>'Vũ Thị Mai','email'=>'vuthimai@dangduc.com','phone'=>'0987 654 321','role'=>'Moderator','role_class'=>'amber','dept'=>'Hỗ trợ KH','login'=>'23/05/2025 16:20','status'=>'Tạm khóa','status_class'=>'red','new'=>false],
    ['id'=>'QTV006','name'=>'Đỗ Hoàng Long','email'=>'hoanglong@dangduc.com','phone'=>'0977 888 999','role'=>'Editor','role_class'=>'purple','dept'=>'Nội dung','login'=>'23/05/2025 15:08','status'=>'Hoạt động','status_class'=>'green','new'=>false],
    ['id'=>'QTV007','name'=>'Nguyễn Thu Hà','email'=>'thuhah@dangduc.com','phone'=>'0966 777 888','role'=>'Viewer','role_class'=>'gray','dept'=>'Kế toán','login'=>'22/05/2025 14:30','status'=>'Chưa kích hoạt','status_class'=>'amber','new'=>false],
    ['id'=>'QTV008','name'=>'Bùi Quang Huy','email'=>'quanghuy@dangduc.com','phone'=>'0911 222 333','role'=>'Moderator','role_class'=>'amber','dept'=>'Hỗ trợ KH','login'=>'21/05/2025 11:00','status'=>'Tạm khóa','status_class'=>'red','new'=>false],
];
$roles = [
    ['l'=>'Super Admin','c'=>'2 (11.1%)','w'=>'11%','color'=>'red'],
    ['l'=>'Admin','c'=>'5 (27.8%)','w'=>'28%','color'=>'blue'],
    ['l'=>'Editor','c'=>'4 (22.2%)','w'=>'22%','color'=>'purple'],
    ['l'=>'Moderator','c'=>'4 (22.2%)','w'=>'22%','color'=>'amber'],
    ['l'=>'Viewer','c'=>'3 (16.7%)','w'=>'17%','color'=>'gray'],
];
@endphp
<div class="adm-page">
    <div class="adm-page-title"><h1><i class="fa fa-user-circle-o" style="color:var(--adm-teal);margin-right:6px"></i>Quản lý quản trị viên</h1><div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › Quản trị viên › Danh sách quản trị viên</div></div>

    <div class="adm-stats">
        <div class="adm-stat"><div class="icon blue"><i class="fa fa-users"></i></div><div><div class="num">18</div><div class="lbl">Tổng quản trị viên</div><div class="trend up"><i class="fa fa-arrow-up"></i>2 mới tháng này</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-check-circle"></i></div><div><div class="num" style="color:var(--adm-green)">12</div><div class="lbl">Đang hoạt động</div><div class="trend">66.7% tổng QTV</div></div></div>
        <div class="adm-stat"><div class="icon teal"><i class="fa fa-id-badge"></i></div><div><div class="num">5</div><div class="lbl">Vai trò hệ thống</div><div style="font-size:10px;color:var(--adm-muted)">Super Admin, Admin, Editor, Mod, Viewer</div></div></div>
        <div class="adm-stat"><div class="icon amber"><i class="fa fa-sign-in"></i></div><div><div class="num">8</div><div class="lbl">Đăng nhập hôm nay</div><div class="trend up"><i class="fa fa-arrow-up"></i>3 so với hôm qua</div></div></div>
        <div class="adm-stat"><div class="icon red"><i class="fa fa-lock"></i></div><div><div class="num">2</div><div class="lbl">Tài khoản bị khóa</div><div style="font-size:10px;color:var(--adm-red)">Cần xem xét</div></div></div>
    </div>

    <div class="adm-layout">
        <div>
            <div class="adm-toolbar">
                <div class="adm-search"><i class="fa fa-search"></i><input type="text" placeholder="Tìm kiếm theo tên, email, SĐT, mã QTV..."></div>
                <button class="adm-btn outline"><i class="fa fa-envelope"></i> Gửi email</button>
                <button class="adm-btn outline"><i class="fa fa-upload"></i> Xuất Excel</button>
                <button class="adm-btn primary"><i class="fa fa-plus"></i> Thêm quản trị viên</button>
            </div>
            <div class="adm-filters">
                <span class="adm-filter-label">Vai trò</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Trạng thái</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Phòng ban</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Thời gian tạo</span><input type="text" class="adm-select" style="width:200px" value="01/05/2025 - 24/05/2025">
            </div>
            <div class="adm-tabs">
                <button class="adm-tab active">Tất cả</button>
                <button class="adm-tab" style="color:var(--adm-green)">● Hoạt động</button>
                <button class="adm-tab" style="color:var(--adm-red)">● Tạm khóa</button>
                <button class="adm-tab" style="color:var(--adm-amber)">● Chưa kích hoạt</button>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead><tr><th><input type="checkbox"></th><th>Mã QTV</th><th>Quản trị viên</th><th>Email</th><th>Số điện thoại</th><th>Vai trò</th><th>Phòng ban</th><th>Đăng nhập cuối</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
                    <tbody>
                    @foreach($admins as $a)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td class="id" style="font-size:11px">{{ $a['id'] }}</td>
                        <td><div class="adm-user-cell"><div class="u-avatar">{{ mb_substr($a['name'],0,1) }}</div><span class="u-name">{{ $a['name'] }}@if($a['new'])<span class="adm-badge green" style="font-size:9px;margin-left:4px">Mới</span>@endif</span></div></td>
                        <td style="font-size:11.5px;color:var(--adm-muted)">{{ $a['email'] }}</td>
                        <td style="font-size:11.5px;color:var(--adm-muted)">{{ $a['phone'] }}</td>
                        <td><span class="adm-badge {{ $a['role_class'] }}">{{ $a['role'] }}</span></td>
                        <td style="font-size:11.5px">{{ $a['dept'] }}</td>
                        <td style="font-size:11px;white-space:nowrap;color:var(--adm-muted)">{{ $a['login'] }}</td>
                        <td><span class="adm-badge {{ $a['status_class'] }}">{{ $a['status'] }}</span></td>
                        <td><div class="adm-actions"><a href="#" title="Xem"><i class="fa fa-eye"></i></a><a href="#" title="Sửa"><i class="fa fa-pencil"></i></a><a href="#" title="Phân quyền"><i class="fa fa-key"></i></a><button title="Thêm"><i class="fa fa-ellipsis-v"></i></button></div></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="adm-pagination">
                    <span class="info">Hiển thị 1 đến 8 của 18 quản trị viên</span>
                    <div class="pages"><a href="#"><i class="fa fa-angle-left"></i></a><span class="active">1</span><a href="#">2</a><a href="#">3</a><a href="#"><i class="fa fa-angle-right"></i></a></div>
                    <select class="adm-select" style="width:auto"><option>10 / trang</option></select>
                </div>
            </div>

            <div class="adm-bottom-grid four-col">
                <div class="adm-bottom-card"><h4>Hoạt động gần đây <a href="#">Xem tất cả →</a></h4>
                    @foreach([['n'=>'Nguyễn Văn An','a'=>'Đăng nhập thành công','d'=>'24/05/2025 10:28'],['n'=>'Trần Thị Bích Ngọc','a'=>'Cập nhật sản phẩm #SP001','d'=>'24/05/2025 10:15'],['n'=>'Lê Minh Đức','a'=>'Duyệt đơn hàng #DD250524','d'=>'24/05/2025 09:58'],['n'=>'Phạm Hoàng Nam','a'=>'Tạo banner mới','d'=>'24/05/2025 09:45'],['n'=>'Đỗ Hoàng Long','a'=>'Đăng bài viết mới','d'=>'23/05/2025 15:08']] as $item)
                    <div class="adm-alert-item"><div class="u-avatar" style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--adm-muted)">{{ mb_substr($item['n'],0,1) }}</div><div class="alert-info"><div class="alert-title">{{ $item['n'] }}</div><div class="alert-desc">{{ $item['a'] }}</div></div><div style="font-size:10px;color:var(--adm-muted);white-space:nowrap">{{ $item['d'] }}</div></div>
                    @endforeach
                </div>
                <div class="adm-bottom-card"><h4>Đăng nhập gần đây <a href="#">Xem tất cả →</a></h4>
                    @foreach([['n'=>'Nguyễn Văn An','a'=>'192.168.1.100 — Chrome/Windows','d'=>'24/05/2025 10:28'],['n'=>'Trần Thị Bích Ngọc','a'=>'192.168.1.101 — Firefox/Mac','d'=>'24/05/2025 09:48'],['n'=>'Lê Minh Đức','a'=>'192.168.1.102 — Chrome/Windows','d'=>'24/05/2025 09:15'],['n'=>'Phạm Hoàng Nam','a'=>'192.168.1.103 — Safari/Mac','d'=>'23/05/2025 18:30'],['n'=>'Vũ Thị Mai','a'=>'192.168.1.104 — Chrome/Windows','d'=>'23/05/2025 16:20']] as $h)
                    <div class="adm-alert-item"><div class="u-avatar" style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--adm-muted)">{{ mb_substr($h['n'],0,1) }}</div><div class="alert-info"><div class="alert-title">{{ $h['n'] }}</div><div class="alert-desc">{{ $h['a'] }}</div></div><div style="font-size:10px;color:var(--adm-muted);white-space:nowrap">{{ $h['d'] }}</div></div>
                    @endforeach
                </div>
                <div class="adm-bottom-card"><h4>Cảnh báo cần xử lý <a href="#">Xem tất cả →</a></h4>
                    <div class="adm-alert-item"><div class="alert-icon red"><i class="fa fa-lock"></i></div><div class="alert-info"><div class="alert-title">2 tài khoản bị khóa</div><div class="alert-desc">Cần xem xét và xử lý</div></div></div>
                    <div class="adm-alert-item"><div class="alert-icon amber"><i class="fa fa-envelope"></i></div><div class="alert-info"><div class="alert-title">1 tài khoản chưa kích hoạt</div><div class="alert-desc">Gửi lại email kích hoạt</div></div></div>
                    <div class="adm-alert-item"><div class="alert-icon amber"><i class="fa fa-key"></i></div><div class="alert-info"><div class="alert-title">3 QTV chưa đổi mật khẩu > 90 ngày</div><div class="alert-desc">Yêu cầu đổi mật khẩu</div></div></div>
                    <div class="adm-alert-item"><div class="alert-icon red"><i class="fa fa-globe"></i></div><div class="alert-info"><div class="alert-title">1 QTV đăng nhập từ IP lạ</div><div class="alert-desc">Kiểm tra bảo mật</div></div></div>
                </div>
                <div class="adm-bottom-card"><h4>Gợi ý tối ưu <a href="#">Xem tất cả →</a></h4>
                    <div class="adm-suggest-item"><div class="s-icon blue"><i class="fa fa-shield"></i></div><div class="s-info"><div class="s-title">Bật xác thực 2 lớp cho tất cả QTV</div><div class="s-desc">Tăng cường bảo mật hệ thống.</div></div></div>
                    <div class="adm-suggest-item"><div class="s-icon teal"><i class="fa fa-key"></i></div><div class="s-info"><div class="s-title">Rà soát phân quyền định kỳ</div><div class="s-desc">Đảm bảo đúng vai trò, đúng quyền.</div></div></div>
                    <div class="adm-suggest-item"><div class="s-icon amber"><i class="fa fa-user-times"></i></div><div class="s-info"><div class="s-title">Xóa tài khoản không hoạt động</div><div class="s-desc">Giảm rủi ro bảo mật.</div></div></div>
                    <div class="adm-suggest-item"><div class="s-icon purple"><i class="fa fa-lock"></i></div><div class="s-info"><div class="s-title">Chính sách mật khẩu mạnh</div><div class="s-desc">Yêu cầu ký tự đặc biệt, độ dài tối thiểu.</div></div></div>
                </div>
            </div>
        </div>

        <aside class="adm-aside">
            <div class="adm-aside-card"><h4>Phân bổ vai trò</h4>
                @foreach($roles as $g)
                <div class="adm-progress-row"><span class="p-label" style="width:80px;text-align:left;font-size:11px">{{ $g['l'] }}</span><div class="adm-progress"><div class="bar {{ $g['color'] }}" style="width:{{ $g['w'] }}"></div></div><span class="p-count" style="font-size:10px">{{ $g['c'] }}</span></div>
                @endforeach
            </div>
            <div class="adm-aside-card"><h4>QTV hoạt động cao</h4>
                <ol class="adm-top-list">
                    @foreach([['n'=>'Nguyễn Văn An','v'=>'4.256'],['n'=>'Trần Thị Bích Ngọc','v'=>'3.891'],['n'=>'Lê Minh Đức','v'=>'2.845'],['n'=>'Phạm Hoàng Nam','v'=>'2.650'],['n'=>'Vũ Thị Mai','v'=>'2.200']] as $i=>$t)
                    <li><span class="rank">{{ $i+1 }}</span><span class="name">{{ $t['n'] }}</span><span class="val">{{ $t['v'] }} thao tác</span></li>
                    @endforeach
                </ol>
            </div>
            <div class="adm-aside-card"><h4>Trạng thái tài khoản</h4>
                <div class="adm-donut-wrap">
                    <div class="adm-donut" style="background:conic-gradient(var(--adm-green) 0% 67%, var(--adm-red) 67% 89%, var(--adm-amber) 89% 100%);width:120px;height:120px"><div class="adm-donut-center" style="width:80px;height:80px;border-radius:50%;background:#fff"><div class="pct" style="font-size:18px">18</div><div class="lbl">Tổng QTV</div></div></div>
                    <ul class="adm-donut-legend">
                        <li><span class="dot" style="background:var(--adm-green)"></span> Hoạt động <span class="pct">12 (66.7%)</span></li>
                        <li><span class="dot" style="background:var(--adm-red)"></span> Tạm khóa <span class="pct">4 (22.2%)</span></li>
                        <li><span class="dot" style="background:var(--adm-amber)"></span> Chưa kích hoạt <span class="pct">2 (11.1%)</span></li>
                    </ul>
                </div>
            </div>
            <div class="adm-aside-card"><h4>Tăng trưởng QTV <select class="adm-select" style="height:24px;font-size:10px;padding:0 20px 0 6px"><option>6 tháng gần đây</option></select></h4>
                <div class="adm-trend-header"><div class="legend"><span><span class="dot" style="background:var(--adm-teal);display:inline-block;width:8px;height:8px;border-radius:2px"></span> Số QTV</span></div></div>
                <div class="adm-trend-chart">@for($i=0;$i<12;$i++)<div class="adm-trend-bar primary" style="height:{{ 40+$i*5 }}%"></div>@endfor</div>
            </div>
        </aside>
    </div>
</div>

<style>.adm-badge.purple{background:rgba(139,92,246,.12);color:#8b5cf6}.adm-badge.gray{background:#f1f5f9;color:#64748b}</style>
@endsection
