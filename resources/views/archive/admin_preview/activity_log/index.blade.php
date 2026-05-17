@extends('admin_layout')
@section('admin_content')
@php
$logs = [
    ['id'=>'LOG001','time'=>'24/05/2025 10:28','user'=>'Nguyễn Văn An','type'=>'Đăng nhập','type_class'=>'blue','module'=>'Hệ thống','desc'=>'Đăng nhập thành công từ IP 192.168.1.100','ip'=>'192.168.1.100','device'=>'Chrome/Win','level'=>'Thông tin','level_class'=>'blue'],
    ['id'=>'LOG002','time'=>'24/05/2025 10:15','user'=>'Trần Thị Bích Ngọc','type'=>'Cập nhật','type_class'=>'amber','module'=>'Sản phẩm','desc'=>'Cập nhật giá SP #SP001 từ 25.990.000đ → 23.990.000đ','ip'=>'192.168.1.101','device'=>'Firefox/Mac','level'=>'Thông tin','level_class'=>'blue'],
    ['id'=>'LOG003','time'=>'24/05/2025 09:58','user'=>'Lê Minh Đức','type'=>'Xóa','type_class'=>'red','module'=>'Đơn hàng','desc'=>'Xóa đơn hàng #DD250524003 - Lý do: Khách hủy','ip'=>'192.168.1.102','device'=>'Chrome/Win','level'=>'Cảnh báo','level_class'=>'amber'],
    ['id'=>'LOG004','time'=>'24/05/2025 09:45','user'=>'Phạm Hoàng Nam','type'=>'Thêm mới','type_class'=>'green','module'=>'Banner','desc'=>'Tạo banner mới "Flash Sale cuối tuần" #BNR00130','ip'=>'192.168.1.103','device'=>'Safari/Mac','level'=>'Thông tin','level_class'=>'blue'],
    ['id'=>'LOG005','time'=>'23/05/2025 18:30','user'=>'Nguyễn Văn An','type'=>'Cảnh báo','type_class'=>'red','module'=>'Bảo mật','desc'=>'Đăng nhập thất bại 3 lần liên tiếp từ IP 45.33.12.xx','ip'=>'45.33.12.xx','device'=>'Unknown','level'=>'Nguy hiểm','level_class'=>'red'],
    ['id'=>'LOG006','time'=>'23/05/2025 16:20','user'=>'Vũ Thị Mai','type'=>'Cập nhật','type_class'=>'amber','module'=>'Khách hàng','desc'=>'Cập nhật hạng thành viên KH00125 lên VIP','ip'=>'192.168.1.104','device'=>'Chrome/Win','level'=>'Thông tin','level_class'=>'blue'],
    ['id'=>'LOG007','time'=>'23/05/2025 15:08','user'=>'Đỗ Hoàng Long','type'=>'Đăng xuất','type_class'=>'gray','module'=>'Hệ thống','desc'=>'Đăng xuất khỏi hệ thống','ip'=>'192.168.1.105','device'=>'Edge/Win','level'=>'Thông tin','level_class'=>'blue'],
    ['id'=>'LOG008','time'=>'23/05/2025 14:05','user'=>'Trần Thị Bích Ngọc','type'=>'Thêm mới','type_class'=>'green','module'=>'Bài viết','desc'=>'Tạo bài viết "Top 10 laptop gaming 2025"','ip'=>'192.168.1.101','device'=>'Firefox/Mac','level'=>'Thông tin','level_class'=>'blue'],
];
$categories = [
    ['l'=>'Đăng nhập / Đăng xuất','c'=>'3.428 (21.6%)','w'=>'22%','color'=>'teal'],
    ['l'=>'Cập nhật dữ liệu','c'=>'5.126 (32.4%)','w'=>'32%','color'=>'blue'],
    ['l'=>'Thêm mới','c'=>'3.842 (24.3%)','w'=>'24%','color'=>'green'],
    ['l'=>'Xóa','c'=>'1.890 (11.9%)','w'=>'12%','color'=>'red'],
    ['l'=>'Cảnh báo','c'=>'156 (1.0%)','w'=>'1%','color'=>'amber'],
    ['l'=>'Khác','c'=>'1.400 (8.8%)','w'=>'9%','color'=>'gray'],
];
@endphp
<div class="adm-page">
    <div class="adm-page-title"><h1><i class="fa fa-history" style="color:var(--adm-teal);margin-right:6px"></i>Nhật ký hoạt động</h1><div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › Nhật ký hoạt động</div></div>

    <div class="adm-stats">
        <div class="adm-stat"><div class="icon blue"><i class="fa fa-list"></i></div><div><div class="num">15.842</div><div class="lbl">Tổng hoạt động</div><div class="trend up"><i class="fa fa-arrow-up"></i>8.5% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon teal"><i class="fa fa-clock-o"></i></div><div><div class="num" style="color:var(--adm-teal)">128</div><div class="lbl">Hoạt động hôm nay</div><div class="trend up"><i class="fa fa-arrow-up"></i>12.3% so với hôm qua</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-sign-in"></i></div><div><div class="num">342</div><div class="lbl">Đăng nhập</div><div class="trend up"><i class="fa fa-arrow-up"></i>5.1% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon amber"><i class="fa fa-pencil-square-o"></i></div><div><div class="num">8.456</div><div class="lbl">Thay đổi dữ liệu</div><div class="trend up"><i class="fa fa-arrow-up"></i>6.8% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon red"><i class="fa fa-exclamation-triangle"></i></div><div><div class="num">23</div><div class="lbl">Cảnh báo bảo mật</div><div class="trend down"><i class="fa fa-arrow-down"></i>15.2% so với tháng trước</div></div></div>
    </div>

    <div class="adm-layout">
        <div>
            <div class="adm-toolbar">
                <div class="adm-search"><i class="fa fa-search"></i><input type="text" placeholder="Tìm kiếm theo nội dung, IP, quản trị viên..."></div>
                <button class="adm-btn outline"><i class="fa fa-upload"></i> Xuất Excel</button>
                <button class="adm-btn primary" style="background:var(--adm-red)"><i class="fa fa-trash"></i> Xóa log cũ</button>
            </div>
            <div class="adm-filters">
                <span class="adm-filter-label">Loại hoạt động</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Quản trị viên</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Module</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Mức độ</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Ngày tạo</span><input type="text" class="adm-select" style="width:200px" value="01/05/2025 - 24/05/2025">
            </div>
            <div class="adm-tabs">
                <button class="adm-tab active">Tất cả</button>
                <button class="adm-tab" style="color:#3b82f6">● Đăng nhập/Đăng xuất</button>
                <button class="adm-tab" style="color:var(--adm-green)">● Thêm mới</button>
                <button class="adm-tab" style="color:var(--adm-amber)">● Cập nhật</button>
                <button class="adm-tab" style="color:var(--adm-red)">● Xóa</button>
                <button class="adm-tab" style="color:#ef4444">● Cảnh báo</button>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead><tr><th><input type="checkbox"></th><th>Mã log</th><th>Thời gian</th><th>Quản trị viên</th><th>Loại</th><th>Module</th><th>Nội dung</th><th>IP</th><th>Thiết bị</th><th>Mức độ</th><th>Thao tác</th></tr></thead>
                    <tbody>
                    @foreach($logs as $l)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td class="id" style="font-size:11px">{{ $l['id'] }}</td>
                        <td style="font-size:11px;white-space:nowrap;color:var(--adm-muted)">{{ $l['time'] }}</td>
                        <td><div class="adm-user-cell"><div class="u-avatar">{{ mb_substr($l['user'],0,1) }}</div><span class="u-name">{{ $l['user'] }}</span></div></td>
                        <td><span class="adm-badge {{ $l['type_class'] }}">{{ $l['type'] }}</span></td>
                        <td style="font-size:11.5px">{{ $l['module'] }}</td>
                        <td style="max-width:200px;font-size:11.5px;color:var(--adm-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $l['desc'] }}</td>
                        <td style="font-size:11px;color:var(--adm-muted);font-family:monospace">{{ $l['ip'] }}</td>
                        <td style="font-size:11px;color:var(--adm-muted)">{{ $l['device'] }}</td>
                        <td><span class="adm-badge {{ $l['level_class'] }}">{{ $l['level'] }}</span></td>
                        <td><div class="adm-actions"><a href="#" title="Xem"><i class="fa fa-eye"></i></a><button title="Thêm"><i class="fa fa-ellipsis-v"></i></button></div></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="adm-pagination">
                    <span class="info">Hiển thị 1 đến 8 của 15.842 hoạt động</span>
                    <div class="pages"><a href="#"><i class="fa fa-angle-left"></i></a><span class="active">1</span><a href="#">2</a><a href="#">3</a><a href="#">4</a><a href="#">5</a><span class="dots">...</span><a href="#">1.981</a><a href="#"><i class="fa fa-angle-right"></i></a></div>
                    <select class="adm-select" style="width:auto"><option>10 / trang</option></select>
                </div>
            </div>

            <div class="adm-bottom-grid four-col">
                <div class="adm-bottom-card"><h4>Hoạt động gần đây <a href="#">Xem tất cả →</a></h4>
                    @foreach([['n'=>'Nguyễn Văn An','a'=>'Đăng nhập thành công','d'=>'24/05/2025 10:28'],['n'=>'Trần Thị Bích Ngọc','a'=>'Cập nhật giá sản phẩm #SP001','d'=>'24/05/2025 10:15'],['n'=>'Lê Minh Đức','a'=>'Xóa đơn hàng #DD250524003','d'=>'24/05/2025 09:58'],['n'=>'Phạm Hoàng Nam','a'=>'Tạo banner "Flash Sale cuối tuần"','d'=>'24/05/2025 09:45'],['n'=>'Nguyễn Văn An','a'=>'Đăng nhập thất bại 3 lần','d'=>'23/05/2025 18:30']] as $item)
                    <div class="adm-alert-item"><div class="u-avatar" style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--adm-muted)">{{ mb_substr($item['n'],0,1) }}</div><div class="alert-info"><div class="alert-title">{{ $item['n'] }}</div><div class="alert-desc">{{ $item['a'] }}</div></div><div style="font-size:10px;color:var(--adm-muted);white-space:nowrap">{{ $item['d'] }}</div></div>
                    @endforeach
                </div>
                <div class="adm-bottom-card"><h4>Đăng nhập gần đây <a href="#">Xem tất cả →</a></h4>
                    @foreach([['n'=>'Nguyễn Văn An','a'=>'192.168.1.100 — Chrome/Windows','d'=>'24/05/2025 10:28'],['n'=>'Trần Thị Bích Ngọc','a'=>'192.168.1.101 — Firefox/Mac','d'=>'24/05/2025 09:48'],['n'=>'Lê Minh Đức','a'=>'192.168.1.102 — Chrome/Windows','d'=>'24/05/2025 09:15'],['n'=>'Phạm Hoàng Nam','a'=>'192.168.1.103 — Safari/Mac','d'=>'23/05/2025 18:30'],['n'=>'Vũ Thị Mai','a'=>'192.168.1.104 — Chrome/Windows','d'=>'23/05/2025 16:20']] as $h)
                    <div class="adm-alert-item"><div class="u-avatar" style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--adm-muted)">{{ mb_substr($h['n'],0,1) }}</div><div class="alert-info"><div class="alert-title">{{ $h['n'] }}</div><div class="alert-desc">{{ $h['a'] }}</div></div><div style="font-size:10px;color:var(--adm-muted);white-space:nowrap">{{ $h['d'] }}</div></div>
                    @endforeach
                </div>
                <div class="adm-bottom-card"><h4>Cảnh báo cần xử lý <a href="#">Xem tất cả →</a></h4>
                    <div class="adm-alert-item"><div class="alert-icon red"><i class="fa fa-exclamation-circle"></i></div><div class="alert-info"><div class="alert-title">5 lần đăng nhập thất bại liên tiếp</div><div class="alert-desc">Kiểm tra IP 45.33.12.xx ngay lập tức</div></div></div>
                    <div class="adm-alert-item"><div class="alert-icon amber"><i class="fa fa-user-times"></i></div><div class="alert-info"><div class="alert-title">2 tài khoản bị khóa</div><div class="alert-desc">Cần xem xét và xử lý</div></div></div>
                    <div class="adm-alert-item"><div class="alert-icon amber"><i class="fa fa-clock-o"></i></div><div class="alert-info"><div class="alert-title">3 QTV chưa đổi mật khẩu > 90 ngày</div><div class="alert-desc">Yêu cầu đổi mật khẩu ngay</div></div></div>
                    <div class="adm-alert-item"><div class="alert-icon red"><i class="fa fa-globe"></i></div><div class="alert-info"><div class="alert-title">1 QTV đăng nhập từ IP lạ</div><div class="alert-desc">Kiểm tra bảo mật tài khoản</div></div></div>
                </div>
                <div class="adm-bottom-card"><h4>Gợi ý tối ưu <a href="#">Xem tất cả →</a></h4>
                    <div class="adm-suggest-item"><div class="s-icon blue"><i class="fa fa-shield"></i></div><div class="s-info"><div class="s-title">Bật xác thực 2 lớp cho tất cả QTV</div><div class="s-desc">Tăng cường bảo mật hệ thống.</div></div></div>
                    <div class="adm-suggest-item"><div class="s-icon teal"><i class="fa fa-key"></i></div><div class="s-info"><div class="s-title">Rà soát phân quyền định kỳ</div><div class="s-desc">Đảm bảo đúng vai trò, đúng quyền.</div></div></div>
                    <div class="adm-suggest-item"><div class="s-icon amber"><i class="fa fa-user-times"></i></div><div class="s-info"><div class="s-title">Xóa tài khoản không hoạt động > 6 tháng</div><div class="s-desc">Giảm rủi ro bảo mật.</div></div></div>
                    <div class="adm-suggest-item"><div class="s-icon purple"><i class="fa fa-lock"></i></div><div class="s-info"><div class="s-title">Thiết lập chính sách mật khẩu mạnh</div><div class="s-desc">Yêu cầu ký tự đặc biệt, độ dài tối thiểu.</div></div></div>
                </div>
            </div>
        </div>

        <aside class="adm-aside">
            <div class="adm-aside-card"><h4>Phân loại hoạt động</h4>
                @foreach($categories as $g)
                <div class="adm-progress-row"><span class="p-label" style="width:110px;text-align:left;font-size:11px">{{ $g['l'] }}</span><div class="adm-progress"><div class="bar {{ $g['color'] }}" style="width:{{ $g['w'] }}"></div></div><span class="p-count" style="font-size:10px">{{ $g['c'] }}</span></div>
                @endforeach
            </div>
            <div class="adm-aside-card"><h4>Top quản trị viên</h4>
                <ol class="adm-top-list">
                    @foreach([['n'=>'Nguyễn Văn An','v'=>'4.256'],['n'=>'Trần Thị Bích Ngọc','v'=>'3.891'],['n'=>'Lê Minh Đức','v'=>'2.845'],['n'=>'Phạm Hoàng Nam','v'=>'2.650'],['n'=>'Vũ Thị Mai','v'=>'2.200']] as $i=>$t)
                    <li><span class="rank">{{ $i+1 }}</span><span class="name">{{ $t['n'] }}</span><span class="val">{{ $t['v'] }} hoạt động</span></li>
                    @endforeach
                </ol>
            </div>
            <div class="adm-aside-card"><h4>Tình trạng hoạt động</h4>
                <div class="adm-donut-wrap">
                    <div class="adm-donut" style="background:conic-gradient(var(--adm-blue) 0% 90%, var(--adm-amber) 90% 98%, var(--adm-red) 98% 100%);width:120px;height:120px"><div class="adm-donut-center" style="width:80px;height:80px;border-radius:50%;background:#fff"><div class="pct" style="font-size:16px">15.842</div><div class="lbl">Tổng</div></div></div>
                    <ul class="adm-donut-legend">
                        <li><span class="dot" style="background:var(--adm-blue)"></span> Thông tin <span class="pct">14.250 (89.9%)</span></li>
                        <li><span class="dot" style="background:var(--adm-amber)"></span> Cảnh báo <span class="pct">1.250 (7.9%)</span></li>
                        <li><span class="dot" style="background:var(--adm-red)"></span> Nguy hiểm <span class="pct">342 (2.2%)</span></li>
                    </ul>
                </div>
            </div>
            <div class="adm-aside-card"><h4>Xu hướng hoạt động <select class="adm-select" style="height:24px;font-size:10px;padding:0 20px 0 6px"><option>6 tháng gần đây</option></select></h4>
                <div class="adm-trend-header"><div class="legend"><span><span class="dot" style="background:var(--adm-teal);display:inline-block;width:8px;height:8px;border-radius:2px"></span> Số hoạt động</span><span><span class="dot" style="background:#bae6fd;display:inline-block;width:8px;height:8px;border-radius:2px"></span> Cảnh báo</span></div></div>
                <div class="adm-trend-chart">@for($i=0;$i<12;$i++)<div class="adm-trend-bar primary" style="height:{{ rand(30,95) }}%"></div>@endfor</div>
            </div>
        </aside>
    </div>
</div>
@endsection
