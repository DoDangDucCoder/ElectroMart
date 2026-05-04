@extends('admin_layout')
@section('admin_content')
<link rel="stylesheet" href="{{ asset('backend/css/admin-dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/admin-pages.css') }}">
@php
$warranties = [
    ['id'=>'BH20250524-001','customer'=>'Nguyễn Văn An','product'=>'ASUS ROG Strix G16','serial'=>'SN-AS516-8992','reason'=>'Không lên nguồn','priority'=>'Khẩn cấp','priority_class'=>'urgent','assigned'=>'Lê Minh Đức','date'=>'24/05/2025 10:25','status'=>'Đang tiếp nhận','status_class'=>'blue','sla'=>'72%','sla_class'=>'amber'],
    ['id'=>'BH20250524-002','customer'=>'Trần Thị Bích Ngọc','product'=>'Logitech G Pro X Superlight','serial'=>'LN-LG51P-0054','reason'=>'Không nhận tín hiệu chuột','priority'=>'Cao','priority_class'=>'high','assigned'=>'Phạm Hoàng Nam','date'=>'24/05/2025 09:48','status'=>'Đang xử lý','status_class'=>'amber','sla'=>'88%','sla_class'=>'green'],
    ['id'=>'BH20250524-003','customer'=>'Lê Minh Đức','product'=>'Samsung 990 Pro 1TB','serial'=>'SN-990P-7012','reason'=>'Tốc độ chậm bất thường','priority'=>'Cao','priority_class'=>'high','assigned'=>'Vũ Thị Mai','date'=>'23/05/2025 16:30','status'=>'Đang xử lý','status_class'=>'amber','sla'=>'49%','sla_class'=>'red'],
    ['id'=>'BH20250524-004','customer'=>'Phạm Hoàng Nam','product'=>'LG UltraGear 27GP850','serial'=>'SN-LG27-5011','reason'=>'Màn hình xọc ngang','priority'=>'Cao','priority_class'=>'high','assigned'=>'Nguyễn Thu Hà','date'=>'23/05/2025 15:12','status'=>'Đang xử lý','status_class'=>'amber','sla'=>'65%','sla_class'=>'amber'],
    ['id'=>'BH20250524-005','customer'=>'Vũ Thị Mai','product'=>'Corsair Vengeance 16GB','serial'=>'SN-COR16-1188','reason'=>'RAM lỗi boot','priority'=>'Thấp','priority_class'=>'low','assigned'=>'Đỗ Hoàng Long','date'=>'22/05/2025 14:02','status'=>'Hoàn tất','status_class'=>'green','sla'=>'100%','sla_class'=>'green'],
    ['id'=>'BH20250524-006','customer'=>'Đỗ Hoàng Long','product'=>'AKKO 3087B Plus','serial'=>'SN-AK3097-0392','reason'=>'Liệt phím','priority'=>'Trung bình','priority_class'=>'medium','assigned'=>'Nguyễn Văn An','date'=>'22/05/2025 11:30','status'=>'Có đổi trả','status_class'=>'teal','sla'=>'100%','sla_class'=>'green'],
    ['id'=>'BH20250524-007','customer'=>'Nguyễn Thu Hà','product'=>'Razer BlackShark V2','serial'=>'SN-R2S2V2-7200','reason'=>'Mic không hoạt động','priority'=>'Cao','priority_class'=>'high','assigned'=>'Trần Thị Bích Ngọc','date'=>'21/05/2025 10:08','status'=>'Quá hạn','status_class'=>'red','sla'=>'0%','sla_class'=>'red'],
    ['id'=>'BH20250524-008','customer'=>'Bùi Quang Huy','product'=>'MSI GeForce RTX 4070 Ti','serial'=>'SN-MS14510-3117','reason'=>'Màn hình đen khi tải nặng','priority'=>'Cao','priority_class'=>'high','assigned'=>'Phạm Hoàng Nam','date'=>'21/05/2025 17:20','status'=>'Chờ khách xác nhận','status_class'=>'purple','sla'=>'38%','sla_class'=>'red'],
];
@endphp
<div class="adm-page">
    <div class="adm-page-title"><h1><i class="fa fa-shield" style="color:var(--adm-teal);margin-right:6px"></i>Quản lý bảo hành</h1><div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › <a href="#">Khách hàng</a> › Quản lý bảo hành</div></div>

    <div class="adm-stats">
        <div class="adm-stat"><div class="icon blue"><i class="fa fa-file-text"></i></div><div><div class="num">3.286</div><div class="lbl">Tổng phiếu bảo hành</div><div class="trend up"><i class="fa fa-arrow-up"></i>14.8% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon teal"><i class="fa fa-inbox"></i></div><div><div class="num" style="color:var(--adm-teal)">186</div><div class="lbl">Đang tiếp nhận</div><div class="trend up"><i class="fa fa-arrow-up"></i>6.2% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon amber"><i class="fa fa-cogs"></i></div><div><div class="num">428</div><div class="lbl">Đang xử lý</div><div class="trend up"><i class="fa fa-arrow-up"></i>4.9% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">2.514</div><div class="lbl">Đã hoàn tất</div><div class="trend up"><i class="fa fa-arrow-up"></i>16.3% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-clock-o"></i></div><div><div class="num">93,8%</div><div class="lbl">Tỷ lệ đúng SLA</div><div class="trend up"><i class="fa fa-arrow-up"></i>3.1% so với tháng trước</div></div></div>
    </div>

    <div class="adm-layout">
        <div>
            <div class="adm-toolbar">
                <div class="adm-search"><i class="fa fa-search"></i><input type="text" placeholder="Tìm kiếm mã bảo hành, sản phẩm, serial..."></div>
                <button class="adm-btn outline"><i class="fa fa-download"></i> Nhập Excel</button>
                <button class="adm-btn outline"><i class="fa fa-upload"></i> Xuất Excel</button>
                <button class="adm-btn primary"><i class="fa fa-plus"></i> Tạo phiếu bảo hành</button>
            </div>
            <div class="adm-filters">
                <span class="adm-filter-label">Trạng thái</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Loại bảo hành</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Sản phẩm</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Nhân viên phụ trách</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Ngày tạo</span><input type="text" class="adm-select" style="width:200px" value="01/05/2025 - 24/05/2025">
            </div>
            <div class="adm-tabs">
                <button class="adm-tab active">Tất cả</button>
                <button class="adm-tab" style="color:#3b82f6;border-color:#3b82f6">Đang tiếp nhận</button>
                <button class="adm-tab" style="color:var(--adm-amber)">Đang xử lý</button>
                <button class="adm-tab" style="color:var(--adm-teal)">Chờ linh kiện</button>
                <button class="adm-tab" style="color:var(--adm-green)">Hoàn tất</button>
                <button class="adm-tab" style="color:var(--adm-red)">Quá hạn</button>
                <button class="adm-tab" style="color:var(--adm-purple)">Có đổi trả</button>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead><tr><th><input type="checkbox"></th><th>Mã BH</th><th>Khách hàng</th><th>Sản phẩm</th><th>Serial / IMEI</th><th>Lỗi tiếp nhận</th><th>Ưu tiên</th><th>Phụ trách</th><th>Cập nhật</th><th>Trạng thái</th><th>SLA</th><th>Thao tác</th></tr></thead>
                    <tbody>
                    @foreach($warranties as $w)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td class="id" style="font-size:11px">{{ $w['id'] }}</td>
                        <td><div class="adm-user-cell"><div class="u-avatar">{{ mb_substr($w['customer'],0,1) }}</div><span class="u-name">{{ $w['customer'] }}</span></div></td>
                        <td><div class="adm-product-cell"><div class="p-img"><div style="width:100%;height:100%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:9px;color:var(--adm-muted)"><i class="fa fa-image"></i></div></div><span class="p-name">{{ $w['product'] }}</span></div></td>
                        <td style="font-size:11px;color:var(--adm-muted)">{{ $w['serial'] }}</td>
                        <td style="max-width:150px;font-size:11.5px;color:var(--adm-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $w['reason'] }}</td>
                        <td><span class="adm-priority {{ $w['priority_class'] }}">{{ $w['priority'] }}</span></td>
                        <td><div class="adm-user-cell"><div class="u-avatar">{{ mb_substr($w['assigned'],0,1) }}</div><span class="u-name" style="font-size:11px">{{ $w['assigned'] }}</span></div></td>
                        <td style="font-size:11px;white-space:nowrap;color:var(--adm-muted)">{{ $w['date'] }}</td>
                        <td><span class="adm-badge {{ $w['status_class'] }}">{{ $w['status'] }}</span></td>
                        <td><div class="adm-sla-bar"><div class="bar-wrap"><div class="bar-fill {{ $w['sla_class'] }}" style="width:{{ $w['sla'] }}"></div></div><span class="bar-pct">{{ $w['sla'] }}</span></div></td>
                        <td><div class="adm-actions"><a href="#" title="Xem"><i class="fa fa-eye"></i></a><a href="#" title="Sửa"><i class="fa fa-pencil"></i></a><button title="Thêm"><i class="fa fa-ellipsis-v"></i></button></div></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="adm-pagination">
                    <span class="info">Hiển thị 1 đến 8 của 3.286 phiếu bảo hành</span>
                    <div class="pages"><a href="#"><i class="fa fa-angle-left"></i></a><span class="active">1</span><a href="#">2</a><a href="#">3</a><a href="#">4</a><a href="#">5</a><span class="dots">...</span><a href="#">329</a><a href="#"><i class="fa fa-angle-right"></i></a></div>
                    <select class="adm-select" style="width:auto"><option>10 / trang</option></select>
                </div>
            </div>

            <div class="adm-bottom-grid four-col">
                <div class="adm-bottom-card"><h4>Phiếu mới nhất <a href="#">Xem tất cả →</a></h4>
                    @foreach([['n'=>'Nguyễn Văn An','p'=>'ASUS ROG Strix G16 - Không lên nguồn','d'=>'24/05/2025 10:25'],['n'=>'Trần Thị Bích Ngọc','p'=>'Logitech G Pro X Superlight - Click lỗi','d'=>'24/05/2025 09:48'],['n'=>'Lê Minh Đức','p'=>'Samsung 990 Pro 1TB - Tốc độ chậm','d'=>'23/05/2025 16:30'],['n'=>'Phạm Hoàng Nam','p'=>'LG UltraGear 27GP850 - Sọc ngang','d'=>'23/05/2025 15:12'],['n'=>'Vũ Thị Mai','p'=>'Corsair Vengeance 16GB - Lỗi boot','d'=>'23/05/2025 14:02']] as $item)
                    <div class="adm-alert-item"><div class="u-avatar" style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--adm-muted)">{{ mb_substr($item['n'],0,1) }}</div><div class="alert-info"><div class="alert-title">{{ $item['n'] }}</div><div class="alert-desc">{{ $item['p'] }}</div></div><div style="font-size:10px;color:var(--adm-muted);white-space:nowrap">{{ $item['d'] }}</div></div>
                    @endforeach
                </div>
                <div class="adm-bottom-card"><h4>Lịch sử xử lý <a href="#">Xem tất cả →</a></h4>
                    @foreach([['n'=>'Lê Minh Đức','a'=>'đã phân công xử lý BH20250524-004','d'=>'24/05/2025 12:25'],['n'=>'Phạm Hoàng Nam','a'=>'cập nhật trạng thái BH20250524-002','d'=>'24/05/2025 09:48'],['n'=>'Vũ Thị Mai','a'=>'yêu cầu linh kiện thay thế #BH20250534-003','d'=>'23/05/2025 15:30'],['n'=>'Đỗ Hoàng Long','a'=>'đã hoàn tất phiếu BH20250534-005','d'=>'22/05/2025 14:50'],['n'=>'Nguyễn Văn An','a'=>'cập nhật đổi trả BH20250534-006','d'=>'22/05/2025 11:40']] as $h)
                    <div class="adm-alert-item"><div class="u-avatar" style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--adm-muted)">{{ mb_substr($h['n'],0,1) }}</div><div class="alert-info"><div class="alert-title">{{ $h['n'] }}</div><div class="alert-desc">{{ $h['a'] }}</div></div><div style="font-size:10px;color:var(--adm-muted);white-space:nowrap">{{ $h['d'] }}</div></div>
                    @endforeach
                </div>
                <div class="adm-bottom-card"><h4>Cảnh báo cần xử lý <a href="#">Xem tất cả →</a></h4>
                    <div class="adm-alert-item"><div class="alert-icon red"><i class="fa fa-exclamation-circle"></i></div><div class="alert-info"><div class="alert-title">200 phiếu quá hạn SLA</div><div class="alert-desc">Phân bố lại kỹ thuật viên hướng CSKH</div></div></div>
                    <div class="adm-alert-item"><div class="alert-icon amber"><i class="fa fa-cogs"></i></div><div class="alert-info"><div class="alert-title">318 phiếu đang chờ linh kiện</div><div class="alert-desc">Theo dõi thời gian nhập kho</div></div></div>
                    <div class="adm-alert-item"><div class="alert-icon red"><i class="fa fa-clock-o"></i></div><div class="alert-info"><div class="alert-title">42 phiếu khẩn cấp chưa tiếp nhận</div><div class="alert-desc">Ưu tiên xử lý trong 1 giờ</div></div></div>
                    <div class="adm-alert-item"><div class="alert-icon amber"><i class="fa fa-hourglass-half"></i></div><div class="alert-info"><div class="alert-title">18 phiếu chờ khách xác nhận > 24h</div><div class="alert-desc">Gửi nhắc nhở tự động</div></div></div>
                </div>
                <div class="adm-bottom-card"><h4>Gợi ý tối ưu <a href="#">Xem tất cả →</a></h4>
                    <div class="adm-suggest-item"><div class="s-icon blue"><i class="fa fa-users"></i></div><div class="s-info"><div class="s-title">Phân bổ lại kỹ thuật viên</div><div class="s-desc">Một số nhân viên đang quá tải.</div></div></div>
                    <div class="adm-suggest-item"><div class="s-icon teal"><i class="fa fa-file-text"></i></div><div class="s-info"><div class="s-title">Tạo quy trình phân loại mẫu</div><div class="s-desc">Tăng tốc độ cập nhật kiện hàng.</div></div></div>
                    <div class="adm-suggest-item"><div class="s-icon amber"><i class="fa fa-database"></i></div><div class="s-info"><div class="s-title">Đồng bộ kho linh kiện</div><div class="s-desc">Theo dõi linh kiện cho sửa chữa.</div></div></div>
                    <div class="adm-suggest-item"><div class="s-icon purple"><i class="fa fa-link"></i></div><div class="s-info"><div class="s-title">Kết nối CRM & đơn hàng</div><div class="s-desc">Tự động hiện thị lịch sử mua hàng.</div></div></div>
                </div>
            </div>
        </div>

        <aside class="adm-aside">
            <div class="adm-aside-card"><h4>Phân loại bảo hành</h4>
                @foreach([['l'=>'Kỹ thuật','c'=>'1,284 (39.1%)','w'=>'39%','color'=>'teal'],['l'=>'Đổi trả','c'=>'826 (25.1%)','w'=>'25%','color'=>'blue'],['l'=>'Bảo hành hãng','c'=>'632 (19.2%)','w'=>'19%','color'=>'amber'],['l'=>'Linh kiện','c'=>'368 (11.2%)','w'=>'11%','color'=>'green'],['l'=>'Khác','c'=>'176 (5.4%)','w'=>'5%','color'=>'gray']] as $g)
                <div class="adm-progress-row"><span class="p-label" style="width:80px;text-align:left">{{ $g['l'] }}</span><div class="adm-progress"><div class="bar {{ $g['color'] }}" style="width:{{ $g['w'] }}"></div></div><span class="p-count">{{ $g['c'] }}</span></div>
                @endforeach
            </div>
            <div class="adm-aside-card"><h4>Top sản phẩm bảo hành nhiều</h4>
                <ol class="adm-top-list">
                    @foreach([['n'=>'ASUS ROG Strix G16','v'=>'286'],['n'=>'Logitech G Pro X Superlight','v'=>'214'],['n'=>'LG UltraGear 27GP850','v'=>'192'],['n'=>'Samsung 990 Pro 1TB','v'=>'168'],['n'=>'AKKO 3087B Plus','v'=>'129']] as $i => $t)
                    <li><span class="rank">{{ $i+1 }}</span><span class="name">{{ $t['n'] }}</span><span class="val">{{ $t['v'] }}</span></li>
                    @endforeach
                </ol>
            </div>
            <div class="adm-aside-card"><h4>Tình trạng bảo hành</h4>
                <div class="adm-donut-wrap">
                    <div class="adm-donut" style="background:conic-gradient(var(--adm-blue) 0% 6%, var(--adm-amber) 6% 19%, #8b5cf6 19% 29%, var(--adm-green) 29% 95%, var(--adm-red) 95% 100%);width:120px;height:120px"><div class="adm-donut-center" style="width:80px;height:80px;border-radius:50%;background:#fff"><div class="pct" style="font-size:16px">3,286</div><div class="lbl">Tổng phiếu</div></div></div>
                    <ul class="adm-donut-legend">
                        <li><span class="dot" style="background:var(--adm-blue)"></span> Đang tiếp nhận <span class="pct">186 (5.7%)</span></li>
                        <li><span class="dot" style="background:var(--adm-amber)"></span> Đang xử lý <span class="pct">428 (13.0%)</span></li>
                        <li><span class="dot" style="background:#8b5cf6"></span> Chờ linh kiện <span class="pct">318 (9.7%)</span></li>
                        <li><span class="dot" style="background:var(--adm-green)"></span> Hoàn tất <span class="pct">2,154 (65.6%)</span></li>
                        <li><span class="dot" style="background:var(--adm-red)"></span> Quá hạn <span class="pct">200 (6.0%)</span></li>
                    </ul>
                </div>
            </div>
            <div class="adm-aside-card"><h4>Xu hướng bảo hành <select class="adm-select" style="height:24px;font-size:10px;padding:0 20px 0 6px"><option>6 tháng gần đây</option></select></h4>
                <div class="adm-trend-header"><div class="legend"><span><span class="dot" style="background:var(--adm-teal);display:inline-block;width:8px;height:8px;border-radius:2px"></span> Số phiếu</span><span><span class="dot" style="background:#bae6fd;display:inline-block;width:8px;height:8px;border-radius:2px"></span> Tỷ lệ đúng SLA</span></div></div>
                <div class="adm-trend-chart">@for($i=0;$i<12;$i++)<div class="adm-trend-bar primary" style="height:{{ rand(30,95) }}%"></div>@endfor</div>
            </div>
        </aside>
    </div>
</div>
@endsection
