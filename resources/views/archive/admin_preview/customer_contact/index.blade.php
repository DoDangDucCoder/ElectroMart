@extends('admin_layout')
@section('admin_content')
@php
$contacts = [
    ['id'=>'LH0006284','customer'=>'Nguyễn Văn An','channel'=>'Email','channel_class'=>'email','subject'=>'Bảo hành','topic'=>'Sản phẩm loa bluetooth không lên nguồn.','priority'=>'Khẩn cấp','priority_class'=>'urgent','assigned'=>'Lê Minh Đức','date'=>'24/05/2025 10:28','status'=>'Đã phản hồi','status_class'=>'green','sla'=>'72%','sla_class'=>'amber'],
    ['id'=>'LH0006283','customer'=>'Trần Thị Bích Ngọc','channel'=>'Hotline','channel_class'=>'hotline','subject'=>'Đổi trả hàng','topic'=>'Muốn đổi sản phẩm do không đúng màu.','priority'=>'Cao','priority_class'=>'high','assigned'=>'Phạm Hoàng Nam','date'=>'24/05/2025 09:52','status'=>'Đang xử lý','status_class'=>'amber','sla'=>'72%','sla_class'=>'amber'],
    ['id'=>'LH0006282','customer'=>'Lê Minh Đức','channel'=>'Facebook','channel_class'=>'facebook','subject'=>'Tư vấn sản phẩm','topic'=>'Tư vấn laptop mỏng nhẹ cho sinh viên.','priority'=>'Trung bình','priority_class'=>'medium','assigned'=>'Vũ Thị Mai','date'=>'23/05/2025 16:40','status'=>'Đã phản hồi','status_class'=>'green','sla'=>'100%','sla_class'=>'green'],
    ['id'=>'LH0006281','customer'=>'Phạm Hoàng Nam','channel'=>'Zalo','channel_class'=>'zalo','subject'=>'Hóa đơn VAT','topic'=>'Cần xuất hóa đơn VAT cho đơn hàng #DD01234.','priority'=>'Thấp','priority_class'=>'low','assigned'=>'Nguyễn Thu Hà','date'=>'23/05/2025 15:08','status'=>'Đã phản hồi','status_class'=>'green','sla'=>'48%','sla_class'=>'red'],
    ['id'=>'LH0006280','customer'=>'Vũ Thị Mai','channel'=>'Form website','channel_class'=>'form','subject'=>'Giao hàng chậm','topic'=>'Đơn hàng #DD5678 giao chậm hơn dự kiến.','priority'=>'Cao','priority_class'=>'high','assigned'=>'Đỗ Hoàng Long','date'=>'22/05/2025 14:50','status'=>'Quá hạn','status_class'=>'red','sla'=>'0%','sla_class'=>'red'],
    ['id'=>'LH0006279','customer'=>'Đỗ Hoàng Long','channel'=>'Email','channel_class'=>'email','subject'=>'Lỗi thanh toán','topic'=>'Thanh toán bị lỗi khi dùng thẻ Visa.','priority'=>'Trung bình','priority_class'=>'medium','assigned'=>'Lê Minh Đức','date'=>'22/05/2025 11:14','status'=>'Đang xử lý','status_class'=>'amber','sla'=>'65%','sla_class'=>'amber'],
    ['id'=>'LH0006278','customer'=>'Nguyễn Thu Hà','channel'=>'Facebook','channel_class'=>'facebook','subject'=>'Khuyến mãi','topic'=>'Hỏi về chương trình khuyến mãi tháng 5.','priority'=>'Thấp','priority_class'=>'low','assigned'=>'Phạm Hoàng Nam','date'=>'21/05/2025 09:42','status'=>'Đã phản hồi','status_class'=>'green','sla'=>'100%','sla_class'=>'green'],
    ['id'=>'LH0006277','customer'=>'Bùi Quang Huy','channel'=>'Hotline','channel_class'=>'hotline','subject'=>'Hướng dẫn sử dụng','topic'=>'Hướng dẫn kết nối tai nghe với điện thoại.','priority'=>'Thấp','priority_class'=>'low','assigned'=>'Vũ Thị Mai','date'=>'20/05/2025 16:22','status'=>'Chờ khách phản hồi','status_class'=>'purple','sla'=>'81%','sla_class'=>'green'],
];
@endphp
<div class="adm-page">
    <div class="adm-page-title"><h1><i class="fa fa-phone" style="color:var(--adm-blue);margin-right:6px"></i>Quản lý liên hệ khách hàng</h1><div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › <a href="#">Khách hàng</a> › Liên hệ khách hàng</div></div>

    <div class="adm-stats">
        <div class="adm-stat"><div class="icon blue"><i class="fa fa-phone"></i></div><div><div class="num">6.284</div><div class="lbl">Tổng liên hệ</div><div class="trend up"><i class="fa fa-arrow-up"></i>15.6% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon teal"><i class="fa fa-clock-o"></i></div><div><div class="num" style="color:var(--adm-teal)">186</div><div class="lbl">Chưa xử lý</div><div class="trend up"><i class="fa fa-arrow-up"></i>8.3% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon amber"><i class="fa fa-cogs"></i></div><div><div class="num">428</div><div class="lbl">Đang xử lý</div><div class="trend up"><i class="fa fa-arrow-up"></i>5.2% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">5.312</div><div class="lbl">Đã phản hồi</div><div class="trend up"><i class="fa fa-arrow-up"></i>17.8% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-clock-o"></i></div><div><div class="num">92,4%</div><div class="lbl">SLA đúng hạn</div><div class="trend up"><i class="fa fa-arrow-up"></i>3.6% so với tháng trước</div></div></div>
    </div>

    <div class="adm-layout">
        <div>
            <div class="adm-toolbar">
                <div class="adm-search"><i class="fa fa-search"></i><input type="text" placeholder="Tìm liên hệ, khách hàng, email, SDT, nội dung..."></div>
                <button class="adm-btn outline"><i class="fa fa-download"></i> Nhập Excel</button>
                <button class="adm-btn outline"><i class="fa fa-upload"></i> Xuất Excel</button>
                <button class="adm-btn primary"><i class="fa fa-plus"></i> Tạo liên hệ</button>
            </div>
            <div class="adm-filters">
                <span class="adm-filter-label">Trạng thái</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Kênh liên hệ</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Loại yêu cầu</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Mức ưu tiên</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Nhân viên phụ trách</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Ngày tạo</span><input type="text" class="adm-select" style="width:200px" value="01/05/2025 - 24/05/2025">
            </div>
            <div class="adm-tabs">
                <button class="adm-tab active">Tất cả</button>
                <button class="adm-tab">Chưa xử lý</button>
                <button class="adm-tab">Đang xử lý</button>
                <button class="adm-tab">Đã phản hồi</button>
                <button class="adm-tab">Email</button>
                <button class="adm-tab">Hotline</button>
                <button class="adm-tab">Facebook</button>
                <button class="adm-tab">Khẩn cấp</button>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead><tr><th><input type="checkbox"></th><th>Mã LH</th><th>Khách hàng</th><th>Kênh</th><th>Chủ đề</th><th>Nội dung ngắn</th><th>Ưu tiên</th><th>Phụ trách</th><th>Cập nhật</th><th>Trạng thái</th><th>SLA</th><th>Thao tác</th></tr></thead>
                    <tbody>
                    @foreach($contacts as $c)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td class="id">{{ $c['id'] }}</td>
                        <td><div class="adm-user-cell"><div class="u-avatar">{{ mb_substr($c['customer'],0,1) }}</div><span class="u-name">{{ $c['customer'] }}</span></div></td>
                        <td><span class="adm-channel {{ $c['channel_class'] }}"><i class="fa fa-{{ $c['channel_class'] === 'email' ? 'envelope' : ($c['channel_class'] === 'hotline' ? 'phone' : ($c['channel_class'] === 'facebook' ? 'facebook' : ($c['channel_class'] === 'zalo' ? 'comment' : 'globe'))) }}"></i> {{ $c['channel'] }}</span></td>
                        <td style="font-size:11.5px;font-weight:600">{{ $c['subject'] }}</td>
                        <td style="max-width:180px;font-size:11.5px;color:var(--adm-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $c['topic'] }}</td>
                        <td><span class="adm-priority {{ $c['priority_class'] }}">{{ $c['priority'] }}</span></td>
                        <td><div class="adm-user-cell"><div class="u-avatar">{{ mb_substr($c['assigned'],0,1) }}</div><span class="u-name" style="font-size:11px">{{ $c['assigned'] }}</span></div></td>
                        <td style="font-size:11px;white-space:nowrap;color:var(--adm-muted)">{{ $c['date'] }}</td>
                        <td><span class="adm-badge {{ $c['status_class'] }}">{{ $c['status'] }}</span></td>
                        <td><div class="adm-sla-bar"><div class="bar-wrap"><div class="bar-fill {{ $c['sla_class'] }}" style="width:{{ $c['sla'] }}"></div></div><span class="bar-pct">{{ $c['sla'] }}</span></div></td>
                        <td><div class="adm-actions"><a href="#" title="Xem"><i class="fa fa-eye"></i></a><a href="#" title="Phản hồi"><i class="fa fa-reply"></i></a><button title="Thêm"><i class="fa fa-ellipsis-v"></i></button></div></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="adm-pagination">
                    <span class="info">Hiển thị 1 đến 8 của 6.284 liên hệ</span>
                    <div class="pages"><a href="#"><i class="fa fa-angle-left"></i></a><span class="active">1</span><a href="#">2</a><a href="#">3</a><a href="#">4</a><a href="#">5</a><span class="dots">...</span><a href="#">628</a><a href="#"><i class="fa fa-angle-right"></i></a></div>
                    <select class="adm-select" style="width:auto"><option>10 / trang</option></select>
                </div>
            </div>

            <div class="adm-bottom-grid four-col">
                <div class="adm-bottom-card"><h4>Liên hệ mới nhất <a href="#">Xem tất cả →</a></h4>
                    @foreach([['n'=>'Nguyễn Văn An','ch'=>'Email','desc'=>'Bảo hành - Loa không lên nguồn','d'=>'24/05/2025 10:28'],['n'=>'Trần Thị Bích Ngọc','ch'=>'Hotline','desc'=>'Đổi trả - Không đúng màu','d'=>'24/05/2025 09:52'],['n'=>'Lê Minh Đức','ch'=>'Facebook','desc'=>'Tư vấn laptop sinh viên','d'=>'23/05/2025 16:40'],['n'=>'Phạm Hoàng Nam','ch'=>'Zalo','desc'=>'Hóa đơn VAT cho đơn DD1234','d'=>'23/05/2025 15:08'],['n'=>'Vũ Thị Mai','ch'=>'Form','desc'=>'Giao hàng chậm #DD5678','d'=>'22/05/2025 14:50']] as $item)
                    <div class="adm-alert-item"><div class="u-avatar" style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--adm-muted)">{{ mb_substr($item['n'],0,1) }}</div><div class="alert-info"><div class="alert-title">{{ $item['n'] }} <span style="font-weight:400;color:var(--adm-muted);font-size:10px">{{ $item['ch'] }}</span></div><div class="alert-desc">{{ $item['desc'] }}</div></div><div style="font-size:10px;color:var(--adm-muted);white-space:nowrap">{{ $item['d'] }}</div></div>
                    @endforeach
                </div>
                <div class="adm-bottom-card"><h4>Lịch sử xử lý <a href="#">Xem tất cả →</a></h4>
                    @foreach([['n'=>'Lê Minh Đức','a'=>'đã phản hồi khách và bàn giao sản phẩm.','d'=>'24/05/2025 10:28'],['n'=>'Phạm Hoàng Nam','a'=>'chuyển yêu cầu cho Nguyễn Thu Hà xử lý.','d'=>'24/05/2025 09:48'],['n'=>'Vũ Thị Mai','a'=>'cập nhật trạng thái sang Đang xử lý.','d'=>'23/05/2025 14:05'],['n'=>'Nguyễn Thu Hà','a'=>'yêu cầu khách bổ sung thông tin...','d'=>'23/05/2025 15:30'],['n'=>'Đỗ Hoàng Long','a'=>'đánh giá liên hệ từ form website.','d'=>'22/05/2025 14:47']] as $h)
                    <div class="adm-alert-item"><div class="u-avatar" style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--adm-muted)">{{ mb_substr($h['n'],0,1) }}</div><div class="alert-info"><div class="alert-title">{{ $h['n'] }}</div><div class="alert-desc">{{ $h['a'] }}</div></div><div style="font-size:10px;color:var(--adm-muted);white-space:nowrap">{{ $h['d'] }}</div></div>
                    @endforeach
                </div>
                <div class="adm-bottom-card"><h4>Cảnh báo cần xử lý <a href="#">Xem tất cả →</a></h4>
                    <div class="adm-alert-item"><div class="alert-icon red"><i class="fa fa-exclamation-circle"></i></div><div class="alert-info"><div class="alert-title">358 liên hệ quá hạn SLA</div><div class="alert-desc">Cần xử lý ngay để giảm mất khách.</div></div></div>
                    <div class="adm-alert-item"><div class="alert-icon amber"><i class="fa fa-clock-o"></i></div><div class="alert-info"><div class="alert-title">186 liên hệ chưa được xử lý</div><div class="alert-desc">Thời gian chờ trung bình: 12 - 45m.</div></div></div>
                    <div class="adm-alert-item"><div class="alert-icon amber"><i class="fa fa-hourglass-half"></i></div><div class="alert-info"><div class="alert-title">42 liên hệ khẩn cấp chưa phản hồi</div><div class="alert-desc">Ưu tiên xử lý trong 1 giờ.</div></div></div>
                    <div class="adm-alert-item"><div class="alert-icon blue"><i class="fa fa-clock-o"></i></div><div class="alert-info"><div class="alert-title">15 khách hàng chờ xác nhận > 24h</div><div class="alert-desc">Gửi nhắc nhở tự động.</div></div></div>
                </div>
                <div class="adm-bottom-card"><h4>Gợi ý tối ưu <a href="#">Xem tất cả →</a></h4>
                    <div class="adm-suggest-item"><div class="s-icon blue"><i class="fa fa-users"></i></div><div class="s-info"><div class="s-title">Phân bổ lý nhân sự</div><div class="s-desc">Tăng mức xuất xử lý 18%.</div></div></div>
                    <div class="adm-suggest-item"><div class="s-icon teal"><i class="fa fa-ticket"></i></div><div class="s-info"><div class="s-title">Ưu tiên xử lý ticket khẩn cấp</div><div class="s-desc">Giảm 90% ticket quá hạn.</div></div></div>
                    <div class="adm-suggest-item"><div class="s-icon amber"><i class="fa fa-file-text"></i></div><div class="s-info"><div class="s-title">Tạo phản hồi mẫu</div><div class="s-desc">Tiết kiệm 30% thời gian trả lời.</div></div></div>
                    <div class="adm-suggest-item"><div class="s-icon purple"><i class="fa fa-link"></i></div><div class="s-info"><div class="s-title">Đồng bộ CRM</div><div class="s-desc">Tự động lấy thông tin từ hệ thống.</div></div></div>
                </div>
            </div>
        </div>

        <aside class="adm-aside">
            <div class="adm-aside-card"><h4>Phân loại liên hệ</h4>
                @foreach([['l'=>'Bảo hành','c'=>'2,286 (36.4%)','w'=>'36%','color'=>'teal'],['l'=>'Đơn hàng','c'=>'1,834 (29.0%)','w'=>'29%','color'=>'blue'],['l'=>'Thanh toán','c'=>'1,036 (16.3%)','w'=>'16%','color'=>'amber'],['l'=>'Tư vấn sản phẩm','c'=>'742 (11.8%)','w'=>'12%','color'=>'green'],['l'=>'Khiếu nại','c'=>'256 (4.1%)','w'=>'4%','color'=>'red'],['l'=>'Khác','c'=>'150 (2.4%)','w'=>'2%','color'=>'gray']] as $g)
                <div class="adm-progress-row"><span class="p-label" style="width:90px;text-align:left">{{ $g['l'] }}</span><div class="adm-progress"><div class="bar {{ $g['color'] }}" style="width:{{ $g['w'] }}"></div></div><span class="p-count">{{ $g['c'] }}</span></div>
                @endforeach
            </div>
            <div class="adm-aside-card"><h4>Top kênh liên hệ</h4>
                <ol class="adm-top-list">
                    @foreach([['n'=>'Email','v'=>'2,512 (40.0%)'],['n'=>'Hotline','v'=>'1,968 (31.3%)'],['n'=>'Facebook','v'=>'1,082 (17.2%)'],['n'=>'Zalo','v'=>'532 (8.5%)'],['n'=>'Form website','v'=>'190 (3.0%)']] as $i => $t)
                    <li><span class="rank">{{ $i+1 }}</span><span class="name">{{ $t['n'] }}</span><span class="val">{{ $t['v'] }}</span></li>
                    @endforeach
                </ol>
            </div>
            <div class="adm-aside-card"><h4>Tình trạng liên hệ</h4>
                <div class="adm-donut-wrap">
                    <div class="adm-donut" style="background:conic-gradient(var(--adm-blue) 0% 3%, var(--adm-amber) 3% 10%, var(--adm-green) 10% 94%, var(--adm-red) 94% 100%);width:120px;height:120px"><div class="adm-donut-center" style="width:80px;height:80px;border-radius:50%;background:#fff"><div class="pct" style="font-size:16px">84.6%</div><div class="lbl">Đã phản hồi</div></div></div>
                    <ul class="adm-donut-legend">
                        <li><span class="dot" style="background:var(--adm-blue)"></span> Chưa xử lý <span class="pct">186 (3.0%)</span></li>
                        <li><span class="dot" style="background:var(--adm-amber)"></span> Đang xử lý <span class="pct">428 (6.8%)</span></li>
                        <li><span class="dot" style="background:var(--adm-green)"></span> Đã phản hồi <span class="pct">5,312 (84.6%)</span></li>
                        <li><span class="dot" style="background:var(--adm-red)"></span> Quá hạn <span class="pct">358 (5.6%)</span></li>
                    </ul>
                </div>
            </div>
            <div class="adm-aside-card"><h4>Xu hướng liên hệ <select class="adm-select" style="height:24px;font-size:10px;padding:0 20px 0 6px"><option>6 tháng gần đây</option></select></h4>
                <div class="adm-trend-header"><div class="legend"><span><span class="dot" style="background:var(--adm-teal);display:inline-block;width:8px;height:8px;border-radius:2px"></span> Số liên hệ</span><span><span class="dot" style="background:#bae6fd;display:inline-block;width:8px;height:8px;border-radius:2px"></span> Tỷ lệ hoàn thành SLA</span></div></div>
                <div class="adm-trend-chart">@for($i=0;$i<12;$i++)<div class="adm-trend-bar primary" style="height:{{ rand(25,100) }}%"></div>@endfor</div>
            </div>
        </aside>
    </div>
</div>
@endsection
