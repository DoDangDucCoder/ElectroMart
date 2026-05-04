@extends('admin_layout')
@section('admin_content')
<link rel="stylesheet" href="{{ asset('backend/css/admin-dashboard.css') }}">
<div class="adm-page">
    <div class="adm-page-title"><h1><i class="fa fa-image" style="color:#0d9488;margin-right:6px"></i>Quản lý banner</h1><div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › <a href="#">Khuyến mãi</a> › Quản lý banner</div></div>

    <div class="adm-stats">
        <div class="adm-stat"><div class="icon teal"><i class="fa fa-image"></i></div><div><div class="num">128</div><div class="lbl">Tổng banner</div><div class="trend up"><i class="fa fa-arrow-up"></i> 12.6% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-play-circle"></i></div><div><div class="num" style="color:#16a34a">96</div><div class="lbl">Banner đang hoạt động</div><div class="trend up"><i class="fa fa-arrow-up"></i> 8.4% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon red"><i class="fa fa-clock-o"></i></div><div><div class="num" style="color:#dc2626">14</div><div class="lbl">Banner sắp hết hạn</div><div class="trend up"><i class="fa fa-arrow-up"></i> 16.7% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon blue"><i class="fa fa-eye"></i></div><div><div class="num">2.840.000</div><div class="lbl">Tổng lượt hiển thị</div><div class="trend up"><i class="fa fa-arrow-up"></i> 15.3% so với tháng trước</div></div></div>
    </div>

    <div class="adm-layout">
        <div>
            <div class="adm-toolbar">
                <div class="adm-search" style="flex:1"><i class="fa fa-search"></i><input type="text" placeholder="Tìm kiếm banner (tên, vị trí, chiến dịch...)"></div>
                <button class="adm-btn outline"><i class="fa fa-download"></i> Nhập Excel</button>
                <button class="adm-btn outline"><i class="fa fa-upload"></i> Xuất Excel</button>
                <button class="adm-btn primary"><i class="fa fa-plus"></i> Tạo banner</button>
            </div>
            <div class="adm-filters">
                <span class="adm-filter-label">Vị trí hiển thị</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Loại banner</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Trạng thái</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Kênh áp dụng</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Thời gian hiển thị</span><select class="adm-select"><option>Tất cả</option></select>
            </div>
            <div class="adm-tabs">
                <button class="adm-tab active">Tất cả</button>
                <button class="adm-tab" style="background:#f0fdf4;color:#16a34a;border-color:#dcfce7">Đang hoạt động</button>
                <button class="adm-tab">Sắp hết hạn</button>
                <button class="adm-tab" style="background:#fef2f2;color:#dc2626;border-color:#fecaca">Popup</button>
                <button class="adm-tab">Trang chủ</button>
                <button class="adm-tab" style="background:#fff7ed;color:#ea580c;border-color:#fed7aa">Flash sale</button>
            </div>

            @php
            $banners = [
                ['id'=>'BNR00128','img'=>'🏖️','name'=>'Sale hè rực rỡ','pos'=>'Trang chủ','type'=>'Banner','channel'=>'Web, App','time'=>'01/05/2025 – 31/05/2025','views'=>'568.324','ctr'=>'5.2%','status'=>'Hoạt động','sc'=>'green','date'=>'24/05/2025 10:28'],
                ['id'=>'BNR00127','img'=>'🚚','name'=>'Banner freeship toàn quốc','pos'=>'Trang chủ','type'=>'Banner','channel'=>'Web, App','time'=>'01/05/2025 – 31/05/2025','views'=>'452.187','ctr'=>'4.6%','status'=>'Hoạt động','sc'=>'green','date'=>'24/05/2025 09:48'],
                ['id'=>'BNR00126','img'=>'⚡','name'=>'Flash sale cuối tuần','pos'=>'Trang chủ','type'=>'Slider','channel'=>'Web, App','time'=>'23/05/2025 – 25/05/2025','views'=>'236.891','ctr'=>'6.1%','status'=>'Hoạt động','sc'=>'green','date'=>'23/05/2025 16:12'],
                ['id'=>'BNR00125','img'=>'🎮','name'=>'Ra mắt laptop gaming','pos'=>'Danh mục','type'=>'Banner','channel'=>'Web','time'=>'15/05/2025 – 15/06/2025','views'=>'312.450','ctr'=>'3.7%','status'=>'Hoạt động','sc'=>'green','date'=>'22/05/2025 14:55'],
                ['id'=>'BNR00124','img'=>'🎁','name'=>'Voucher khách hàng mới','pos'=>'Popup','type'=>'Popup','channel'=>'Web, App','time'=>'10/05/2025 – 30/05/2025','views'=>'198.734','ctr'=>'7.3%','status'=>'Sắp hết hạn','sc'=>'amber','date'=>'22/05/2025 11:30'],
                ['id'=>'BNR00123','img'=>'👑','name'=>'Ưu đãi thành viên VIP','pos'=>'Popup','type'=>'Popup','channel'=>'Web, App','time'=>'20/04/2025 – 20/05/2025','views'=>'145.278','ctr'=>'6.0%','status'=>'Sắp hết hạn','sc'=>'amber','date'=>'21/05/2025 10:08'],
                ['id'=>'BNR00122','img'=>'🆕','name'=>'Bộ sưu tập mới 2025','pos'=>'Landing page','type'=>'Banner','channel'=>'Web','time'=>'01/04/2025 – 30/04/2025','views'=>'98.316','ctr'=>'2.1%','status'=>'Hết hạn','sc'=>'gray','date'=>'30/04/2025 17:20'],
                ['id'=>'BNR00121','img'=>'🏷️','name'=>'Black Friday siêu giảm giá','pos'=>'Popup','type'=>'Popup','channel'=>'Web, App','time'=>'15/11/2024 – 30/11/2024','views'=>'72.640','ctr'=>'8.6%','status'=>'Tạm ẩn','sc'=>'gray','date'=>'21/11/2024 09:15'],
            ];
            @endphp

            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead><tr><th><input type="checkbox"></th><th>Mã banner</th><th>Ảnh banner</th><th>Tên chiến dịch</th><th>Vị trí</th><th>Loại</th><th>Kênh</th><th>Thời gian hiển thị</th><th>Lượt hiển thị</th><th>CTR</th><th>Trạng thái</th><th>Cập nhật</th><th>Thao tác</th></tr></thead>
                    <tbody>
                    @foreach($banners as $b)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td class="id">{{ $b['id'] }}</td>
                        <td style="font-size:24px;text-align:center">{{ $b['img'] }}</td>
                        <td style="font-weight:600;font-size:12px">{{ $b['name'] }}</td>
                        <td style="font-size:11.5px">{{ $b['pos'] }}</td>
                        <td><span class="adm-badge blue" style="font-size:10px">{{ $b['type'] }}</span></td>
                        <td style="font-size:11px;color:#64748b">{{ $b['channel'] }}</td>
                        <td style="font-size:10.5px;color:#64748b;white-space:nowrap">{{ $b['time'] }}</td>
                        <td style="font-size:12px;font-weight:600">{{ $b['views'] }}</td>
                        <td style="font-size:12px">{{ $b['ctr'] }}</td>
                        <td><span class="adm-badge {{ $b['sc'] }}">{{ $b['status'] }}</span></td>
                        <td style="font-size:10.5px;color:#94a3b8;white-space:nowrap">{{ $b['date'] }}</td>
                        <td><div class="adm-actions"><a href="#"><i class="fa fa-eye"></i></a><a href="#"><i class="fa fa-pencil"></i></a><button><i class="fa fa-ellipsis-v"></i></button></div></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="adm-pagination">
                    <span class="info">Hiển thị 1 đến 8 của 128 banner</span>
                    <div class="pages"><a href="#"><i class="fa fa-angle-left"></i></a><span class="active">1</span><a href="#">2</a><a href="#">3</a><a href="#">4</a><a href="#">5</a><span class="dots">...</span><a href="#">16</a><a href="#"><i class="fa fa-angle-right"></i></a></div>
                    <select class="adm-select" style="width:auto"><option>20 / trang</option></select>
                </div>
            </div>

            <div class="adm-bottom-grid" style="grid-template-columns:repeat(4,1fr)">
                <div class="adm-bottom-card"><h4>Banner mới tạo <a href="#">Xem tất cả →</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        @foreach([['c'=>'Sale hè rực rỡ','d'=>'24/05/2025 10:28'],['c'=>'Banner freeship','d'=>'24/05/2025 09:48'],['c'=>'Flash sale cuối tuần','d'=>'23/05/2025 16:12'],['c'=>'Voucher KH mới','d'=>'22/05/2025 11:30'],['c'=>'Ưu đãi VIP','d'=>'21/05/2025 10:08']] as $nv)
                        <li><span class="adm-badge red" style="font-size:9px;width:30px;justify-content:center">Mới</span><div><strong>{{ $nv['c'] }}</strong><div style="font-size:10px;color:#94a3b8">{{ $nv['d'] }}</div></div></li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Lịch sử cập nhật <a href="#">Xem tất cả →</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        @foreach([['n'=>'Nguyễn Văn An','d'=>'24/05/2025 10:28','a'=>'Cập nhật banner BNR00128'],['n'=>'Trần Thị Bích Ngọc','d'=>'24/05/2025 09:48','a'=>'Sửa thời gian banner BNR00127'],['n'=>'Phạm Hoàng Nam','d'=>'23/05/2025 16:12','a'=>'Tạo banner Flash sale'],['n'=>'Lê Minh Đức','d'=>'22/05/2025 14:55','a'=>'Sửa trạng thái banner BNR00125'],['n'=>'Vũ Thị Mai','d'=>'21/05/2025 11:30','a'=>'Cập nhật banner BNR00124']] as $h)
                        <li><div style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#64748b;flex-shrink:0">{{ mb_substr($h['n'],0,1) }}</div><div><strong>{{ $h['n'] }}</strong><div style="font-size:10px;color:#94a3b8">{{ $h['d'] }}</div>{{ $h['a'] }}</div></li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Cảnh báo cần xử lý <a href="#">Xem tất cả →</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        <li><span class="dot red"></span><div><strong style="color:#dc2626">14 banner sắp hết hạn</strong><br><span style="color:#64748b">Cần gia hạn hoặc dừng hiển thị</span></div></li>
                        <li><span class="dot amber"></span><div><strong style="color:#d97706">12 banner đang tạm ẩn</strong><br><span style="color:#64748b">Xem xét lại trạng thái hiển thị</span></div></li>
                        <li><span class="dot blue"></span><div><strong style="color:#3b82f6">6 banner chưa có alt text</strong><br><span style="color:#64748b">Ảnh banner cần tối ưu SEO</span></div></li>
                        <li><span class="dot amber"></span><div><strong style="color:#d97706">5 banner có CTR thấp (&lt;1%)</strong><br><span style="color:#64748b">Cần tối ưu lại nội dung</span></div></li>
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Gợi ý tối ưu <a href="#">Xem tất cả →</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        <li><span class="dot teal"></span><div><strong>Tối ưu banner Trang chủ</strong><br><span style="color:#64748b">CTR nhận trang trên 30%</span></div></li>
                        <li><span class="dot teal"></span><div><strong>Thử nghiệm A/B banner</strong><br><span style="color:#64748b">Tăng hiệu quả hiển thị</span></div></li>
                        <li><span class="dot blue"></span><div><strong>Cà nhân hóa theo nhóm KH</strong><br><span style="color:#64748b">Hiển thị banner phù hợp</span></div></li>
                        <li><span class="dot blue"></span><div><strong>Tối ưu kích thước ảnh</strong><br><span style="color:#64748b">Giảm tải tải trong trang</span></div></li>
                    </ul>
                </div>
            </div>
        </div>

        <aside class="adm-aside">
            <div class="adm-aside-card"><h4>Phân loại banner <a href="#">Xem tất cả</a></h4>
                @foreach([['l'=>'Trang chủ','v'=>48,'p'=>'37.5%'],['l'=>'Danh mục','v'=>32,'p'=>'25.0%'],['l'=>'Popup','v'=>24,'p'=>'18.8%'],['l'=>'Blog','v'=>12,'p'=>'9.4%'],['l'=>'Landing page','v'=>12,'p'=>'9.4%']] as $cat)
                <div style="margin-bottom:8px"><div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px"><span style="font-weight:600">{{ $cat['l'] }}</span><span style="color:#64748b">{{ $cat['v'] }} ({{ $cat['p'] }})</span></div><div class="adm-progress"><div class="bar teal" style="width:{{ round($cat['v']/48*100) }}%"></div></div></div>
                @endforeach
            </div>
            <div class="adm-aside-card"><h4>Top banner hiệu quả <a href="#">Xem tất cả</a></h4>
                <ol class="adm-top-list">
                    @foreach([['n'=>'Sale hè rực rỡ','v'=>'5.2% / 568.324'],['n'=>'Flash sale cuối tuần','v'=>'6.1% / 236.891'],['n'=>'Voucher KH mới','v'=>'7.3% / 198.734'],['n'=>'Banner freeship','v'=>'4.6% / 452.187'],['n'=>'Ra mắt laptop gaming','v'=>'3.7% / 312.450']] as $i=>$t)
                    <li><span class="rank">{{ $i+1 }}</span><span style="width:8px;height:8px;border-radius:50%;background:var(--adm-teal);flex-shrink:0"></span><span class="name">{{ $t['n'] }}</span><span class="val" style="font-size:10px">{{ $t['v'] }}</span></li>
                    @endforeach
                </ol>
            </div>
            <div class="adm-aside-card"><h4>Tình trạng banner</h4>
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:10px">
                    <div style="width:80px;height:80px;border-radius:50%;background:conic-gradient(#16a34a 0deg 270deg,#f59e0b 270deg 307deg,#dc2626 307deg 324deg,#94a3b8 324deg 360deg);position:relative;flex-shrink:0"><div style="position:absolute;inset:12px;border-radius:50%;background:#fff"></div></div>
                    <div style="font-size:11px;display:grid;gap:4px">
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#16a34a"></span>Hoạt động <strong>96 (75.0)</strong></div>
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#f59e0b"></span>Sắp hết hạn <strong>14 (10.9%)</strong></div>
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#dc2626"></span>Tạm ẩn <strong>6 (4.7%)</strong></div>
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#94a3b8"></span>Hết hạn <strong>12 (9.4%)</strong></div>
                    </div>
                </div>
            </div>
            <div class="adm-aside-card"><h4>Hiệu suất banner <select class="adm-select" style="height:22px;font-size:10px;padding:0 16px 0 6px"><option>6 tháng gần đây</option></select></h4>
                <div style="font-size:20px;font-weight:800;color:#111827">3M</div>
                <div class="adm-mini-chart" style="height:60px">@for($i=0;$i<12;$i++)<div class="adm-mini-bar {{ $i > 9 ? 'active' : '' }}" style="height:{{ rand(20,100) }}%"></div>@endfor</div>
            </div>
        </aside>
    </div>
</div>
@endsection
