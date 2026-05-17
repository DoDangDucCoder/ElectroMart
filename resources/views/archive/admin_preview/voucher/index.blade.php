@extends('admin_layout')
@section('admin_content')
<div class="adm-page">
    <div class="adm-page-title"><h1><i class="fa fa-tags" style="color:#0d9488;margin-right:6px"></i>Quản lý mã giảm giá / voucher</h1><div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › <a href="#">Khuyến mãi</a> › Quản lý voucher</div></div>

    <div class="adm-stats">
        <div class="adm-stat"><div class="icon teal"><i class="fa fa-ticket"></i></div><div><div class="num">186</div><div class="lbl">Tổng voucher</div><div class="trend up"><i class="fa fa-arrow-up"></i>12.4% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-check-circle"></i></div><div><div class="num" style="color:#16a34a">142</div><div class="lbl">Voucher đang hoạt động</div><div class="trend up"><i class="fa fa-arrow-up"></i>8.9% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon red"><i class="fa fa-clock-o"></i></div><div><div class="num" style="color:#dc2626">24</div><div class="lbl">Sắp hết hạn</div><div class="lbl" style="color:#dc2626;font-weight:600">Cần xử lý sớm</div></div></div>
        <div class="adm-stat"><div class="icon blue"><i class="fa fa-bar-chart"></i></div><div><div class="num">18.426</div><div class="lbl">Lượt sử dụng</div><div class="trend up"><i class="fa fa-arrow-up"></i>15.6% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon purple"><i class="fa fa-money"></i></div><div><div class="num" style="font-size:15px">2.860.000.000đ</div><div class="lbl">Ngân sách giảm giá</div><div class="trend up"><i class="fa fa-arrow-up"></i>10.8% so với tháng trước</div></div></div>
    </div>

    <div class="adm-layout">
        <div>
            <div class="adm-toolbar">
                <div class="adm-search" style="flex:1"><i class="fa fa-search"></i><input type="text" placeholder="Tìm kiếm voucher (mã, tên, đối tượng, kênh...)"></div>
                <button class="adm-btn outline"><i class="fa fa-download"></i> Nhập Excel</button>
                <button class="adm-btn outline"><i class="fa fa-upload"></i> Xuất Excel</button>
                <button class="adm-btn primary"><i class="fa fa-plus"></i> Tạo voucher</button>
            </div>
            <div class="adm-filters">
                <span class="adm-filter-label">Trạng thái</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Loại voucher</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Kênh áp dụng</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Đối tượng KH</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Thời gian áp dụng</span><select class="adm-select"><option>Tất cả</option></select>
            </div>
            <div class="adm-tabs"><button class="adm-tab active">Tất cả</button><button class="adm-tab" style="background:#f0fdf4;color:#16a34a;border-color:#dcfce7">Đang hoạt động</button><button class="adm-tab">Sắp hết hạn</button><button class="adm-tab" style="background:#fef2f2;color:#dc2626;border-color:#fecaca">Flash sale</button><button class="adm-tab">Khách mới</button><button class="adm-tab" style="background:#f5f3ff;color:#7c3aed;border-color:#e9d5ff">VIP</button></div>

            @php
                $vouchers = [
                    ['code'=>'DDMAY30','name'=>'Sinh nhật tháng 5','type'=>'% giảm','val'=>'30% giá đa 50.000đ','cond'=>'','channel'=>'Website/App','time'=>'01/05/2025 – 31/05/2025','used'=>'1.286 / 2.000','status'=>'Hoạt động','sc'=>'green','updated'=>'24/05/2025 10:28'],
                    ['code'=>'FREESHIP50','name'=>'Miễn phí vận chuyển','type'=>'Freeship','val'=>'Tối đa 50.000đ','cond'=>'Đơn từ 500.000đ','channel'=>'Toàn kênh','time'=>'01/05/2025 – 15/06/2025','used'=>'3.842 / 5.000','status'=>'Hoạt động','sc'=>'green','updated'=>'24/05/2025 09:52'],
                    ['code'=>'NEWUSER10','name'=>'Chào khách hàng mới','type'=>'% giảm','val'=>'10% giá đa 150.000đ','cond'=>'','channel'=>'Website/App','time'=>'10/05/2025 – 30/06/2025','used'=>'2.145 / 3.000','status'=>'Hoạt động','sc'=>'green','updated'=>'24/05/2025 09:30'],
                    ['code'=>'VIP2025','name'=>'Ưu đãi khách VIP','type'=>'Số tiền','val'=>'500.000đ','cond'=>'Đơn từ 8.000.000đ','channel'=>'Website','time'=>'01/05/2025 – 31/12/2025','used'=>'428 / 1.000','status'=>'Nổi bật','sc'=>'amber','updated'=>'23/05/2025 16:40'],
                    ['code'=>'FLASHPC','name'=>'Flash sale linh kiện','type'=>'% giảm','val'=>'15% tối đa 300.000đ','cond'=>'Áp dụng danh mục PC','channel'=>'App','time'=>'24/05/2025 – 27/05/2025','used'=>'690 / 800','status'=>'Sắp hết hạn','sc'=>'red','updated'=>'23/05/2025 15:08'],
                    ['code'=>'GIAM500K','name'=>'Giảm ngay 500K','type'=>'Số tiền','val'=>'500.000đ','cond'=>'Đơn từ 12.000.000đ','channel'=>'Website','time'=>'01/05/2025 – 15/05/2025','used'=>'932 / 1.000','status'=>'Hết hạn','sc'=>'gray','updated'=>'22/05/2025 14:50'],
                    ['code'=>'MEMBER8','name'=>'Thành viên thân thiết','type'=>'% giảm','val'=>'8% tối đa 250.000đ','cond'=>'Thành viên bạc/vàng','channel'=>'Website/App','time'=>'01/05/2025 – 31/07/2025','used'=>'1.120 / 2.500','status'=>'Hoạt động','sc'=>'green','updated'=>'22/05/2025 11:14'],
                    ['code'=>'CODBONUS','name'=>'Thanh toán COD','type'=>'Số tiền','val'=>'100.000đ','cond'=>'Đơn từ 2.000.000đ','channel'=>'Website','time'=>'20/05/2025 – 20/06/2025','used'=>'210 / 600','status'=>'Tạm ẩn','sc'=>'gray','updated'=>'22/05/2025 09:42'],
                ];
            @endphp

            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead><tr><th><input type="checkbox"></th><th>Mã voucher</th><th>Tên chương trình</th><th>Loại giảm</th><th>Giá trị</th><th>Điều kiện áp dụng</th><th>Kênh</th><th>Thời gian</th><th>Đã dùng / Giới hạn</th><th>Trạng thái</th><th>Cập nhật</th><th>Thao tác</th></tr></thead>
                    <tbody>
                    @foreach ($vouchers as $v)
                        <tr>
                            <td><input type="checkbox"></td>
                            <td class="id">{{ $v['code'] }}</td>
                            <td style="font-weight:600;font-size:12px">{{ $v['name'] }}</td>
                            <td style="font-size:11.5px">{{ $v['type'] }}</td>
                            <td style="font-size:12px;font-weight:600">{{ $v['val'] }}</td>
                            <td style="font-size:11px;color:#64748b">{{ $v['cond'] ?: '—' }}</td>
                            <td style="font-size:11.5px">{{ $v['channel'] }}</td>
                            <td style="font-size:10.5px;color:#64748b;white-space:nowrap">{{ $v['time'] }}</td>
                            <td style="font-size:12px">{{ $v['used'] }}</td>
                            <td><span class="adm-badge {{ $v['sc'] }}">{{ $v['status'] }}</span></td>
                            <td style="font-size:10.5px;color:#94a3b8;white-space:nowrap">{{ $v['updated'] }}</td>
                            <td><div class="adm-actions"><a href="#"><i class="fa fa-eye"></i></a><a href="#"><i class="fa fa-pencil"></i></a><button><i class="fa fa-ellipsis-v"></i></button></div></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="adm-pagination">
                    <span class="info">Hiển thị 1 đến 8 của 186 voucher</span>
                    <div class="pages"><a href="#"><i class="fa fa-angle-left"></i></a><span class="active">1</span><a href="#">2</a><a href="#">3</a><a href="#">4</a><a href="#">5</a><span class="dots">...</span><a href="#">24</a><a href="#"><i class="fa fa-angle-right"></i></a></div>
                    <select class="adm-select" style="width:auto"><option>20 / trang</option></select>
                </div>
            </div>

            <div class="adm-bottom-grid" style="grid-template-columns:repeat(4,1fr)">
                <div class="adm-bottom-card"><h4>Voucher mới tạo <a href="#">Xem tất cả →</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        @foreach([['c'=>'SUMMER25','d'=>'24/05/2025 10:15','color'=>'red'],['c'=>'GAMING100K','d'=>'24/05/2025 09:48','color'=>'red'],['c'=>'BACKTOSCHOOL','d'=>'24/05/2025 09:20','color'=>'red'],['c'=>'OFFICE2025','d'=>'23/05/2025 16:33','color'=>'blue'],['c'=>'STUDENT20','d'=>'23/05/2025 15:05','color'=>'blue']] as $nv)
                        <li><span class="adm-badge {{ $nv['color'] }}" style="font-size:9px;width:30px;justify-content:center">Mới</span><div><strong>{{ $nv['c'] }}</strong><div style="font-size:10px;color:#94a3b8">{{ $nv['d'] }}</div></div></li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Lịch sử cập nhật <a href="#">Xem tất cả →</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        @foreach([['n'=>'Nguyễn Văn An','d'=>'24/05/2025 10:28','a'=>'Cập nhật điều kiện voucher DDMAY30'],['n'=>'Trần Thị Bích Ngọc','d'=>'24/05/2025 09:52','a'=>'Sửa giá trị voucher FREESHIP50'],['n'=>'Lê Minh Đức','d'=>'24/05/2025 09:20','a'=>'Gia hạn thời gian NEWUSER10'],['n'=>'Phạm Hoàng Nam','d'=>'23/05/2025 16:40','a'=>'Tạo mới VIP2025'],['n'=>'Vũ Thị Mai','d'=>'23/05/2025 15:08','a'=>'Cập nhật giới hạn FLASHPC']] as $h)
                        <li><div style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#64748b;flex-shrink:0">{{ mb_substr($h['n'],0,1) }}</div><div><strong>{{ $h['n'] }}</strong><div style="font-size:10px;color:#94a3b8">{{ $h['d'] }}</div>{{ $h['a'] }}</div></li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Cảnh báo cần xử lý <a href="#">Xem tất cả →</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        <li><span class="dot red"></span><div><strong style="color:#dc2626">24 voucher sắp hết hạn</strong><br><span style="color:#64748b">Cần gia hạn hoặc dừng chương trình</span></div></li>
                        <li><span class="dot amber"></span><div><strong style="color:#d97706">8 voucher vượt ngân sách</strong><br><span style="color:#64748b">Tổng vượt 126.500.000đ</span></div></li>
                        <li><span class="dot blue"></span><div><strong style="color:#3b82f6">12 voucher gần chạm giới hạn</strong><br><span style="color:#64748b">Số lượng sử dụng ≥ 90%</span></div></li>
                        <li><span class="dot amber"></span><div><strong style="color:#d97706">15 voucher chưa gắn banner</strong><br><span style="color:#64748b">Hiển thị chưa tối ưu hiệu quả</span></div></li>
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Gợi ý tối ưu <a href="#">Xem tất cả →</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        <li><span class="dot teal"></span><div><strong>Tăng giới hạn FREESHIP50</strong><br><span style="color:#64748b">Hiệu suất tối ưu, nên gia hạn</span></div></li>
                        <li><span class="dot teal"></span><div><strong>Gia hạn NEWUSER10</strong><br><span style="color:#64748b">Tỷ lệ chuyển đổi cao, nên mở rộng</span></div></li>
                        <li><span class="dot blue"></span><div><strong>Đặt VIP2025 lên banner</strong><br><span style="color:#64748b">Tăng hiệu lực chương trình VIP</span></div></li>
                        <li><span class="dot amber"></span><div><strong>Tối ưu điều kiện GIAM500K</strong><br><span style="color:#64748b">Đã hết hạn, cần xem lại điều kiện</span></div></li>
                    </ul>
                </div>
            </div>
        </div>

        <aside class="adm-aside">
            <div class="adm-aside-card"><h4>Phân loại voucher <a href="#">Xem tất cả</a></h4>
                @foreach([['l'=>'Phần trăm giảm','v'=>68,'c'=>'teal'],['l'=>'Số tiền','v'=>52,'c'=>'blue'],['l'=>'Freeship','v'=>26,'c'=>'green'],['l'=>'Combo / Quà tặng','v'=>40,'c'=>'amber']] as $cat)
                <div style="margin-bottom:8px"><div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px"><span style="font-weight:600">{{ $cat['l'] }}</span><span style="color:#64748b">{{ $cat['v'] }}</span></div><div class="adm-progress"><div class="bar {{ $cat['c'] }}" style="width:{{ round($cat['v']/68*100) }}%"></div></div></div>
                @endforeach
            </div>
            <div class="adm-aside-card"><h4>Top voucher sử dụng nhiều <a href="#">Xem tất cả</a></h4>
                <ol class="adm-top-list">
                    @foreach([['n'=>'FREESHIP50','v'=>'3.842 lượt','c'=>'green'],['n'=>'NEWUSER10','v'=>'2.145 lượt','c'=>'blue'],['n'=>'DDMAY30','v'=>'1.286 lượt','c'=>'amber'],['n'=>'MEMBER8','v'=>'1.120 lượt','c'=>'purple'],['n'=>'GIAM500K','v'=>'932 lượt','c'=>'gray']] as $i => $t)
                    <li><span class="rank">{{ $i+1 }}</span><span style="width:8px;height:8px;border-radius:50%;background:var(--adm-{{ $t['c'] }});flex-shrink:0"></span><span class="name">{{ $t['n'] }}</span><span class="val" style="font-size:11px">{{ $t['v'] }}</span></li>
                    @endforeach
                </ol>
            </div>
            <div class="adm-aside-card"><h4>Tình trạng voucher</h4>
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:10px">
                    <div style="width:80px;height:80px;border-radius:50%;background:conic-gradient(#16a34a 0deg 210deg,#f59e0b 210deg 255deg,#dc2626 255deg 300deg,#94a3b8 300deg 360deg);position:relative;flex-shrink:0"><div style="position:absolute;inset:12px;border-radius:50%;background:#fff"></div></div>
                    <div style="font-size:11px;display:grid;gap:4px">
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#16a34a"></span>Hoạt động <strong>142</strong></div>
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#f59e0b"></span>Tạm ẩn <strong>12</strong></div>
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#dc2626"></span>Sắp hết hạn <strong>24</strong></div>
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#94a3b8"></span>Hết hạn <strong>8</strong></div>
                    </div>
                </div>
            </div>
            <div class="adm-aside-card"><h4>Tăng trưởng lượt dùng <select class="adm-select" style="height:22px;font-size:10px;padding:0 16px 0 6px"><option>6 tháng gần đây</option></select></h4>
                <div style="font-size:20px;font-weight:800;color:#111827">20K</div>
                <div class="adm-mini-chart" style="height:60px">@for($i=0;$i<12;$i++)<div class="adm-mini-bar {{ $i > 9 ? 'active' : '' }}" style="height:{{ rand(20,100) }}%"></div>@endfor</div>
            </div>
        </aside>
    </div>
</div>
@endsection
