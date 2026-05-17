@extends('admin_layout')
@section('admin_content')
@php
$requests = [
    ['id'=>'SR20250524-0001','customer'=>'Nguyễn Văn An','channel'=>'Email','channel_class'=>'email','category'=>'Bảo hành','topic'=>'Laptop mất nguồn, không lên...','detail'=>'Máy tự tắt khi cắm sạc, mất...','priority'=>'Khẩn cấp','priority_class'=>'urgent','assigned'=>'Lê Minh Đức','date'=>'24/05/2025 10:23','status'=>'Chưa tiếp nhận','status_class'=>'gray','sla'=>'0%','sla_class'=>'red'],
    ['id'=>'SR20250524-0002','customer'=>'Trần Thị Bích Ngọc','channel'=>'Hotline','channel_class'=>'hotline','category'=>'Kỹ thuật','topic'=>'Máy chạy chậm, màu sắc...','detail'=>'Máy laptop bị nóng và chạy...','priority'=>'Cao','priority_class'=>'high','assigned'=>'Phạm Hoàng Nam','date'=>'24/05/2025 09:15','status'=>'Đang xử lý','status_class'=>'amber','sla'=>'72%','sla_class'=>'amber'],
    ['id'=>'SR20250524-0003','customer'=>'Lê Minh Đức','channel'=>'Facebook','channel_class'=>'facebook','category'=>'Đơn hàng','topic'=>'Chưa nhận được đơn hàng...','detail'=>'Đã đặt hàng 3 ngày nhưng...','priority'=>'Cao','priority_class'=>'high','assigned'=>'Vũ Thị Mai','date'=>'23/05/2025 08:42','status'=>'Chờ khách phản hồi','status_class'=>'purple','sla'=>'48%','sla_class'=>'red'],
    ['id'=>'SR20250524-0004','customer'=>'Phạm Hoàng Nam','channel'=>'Zalo','channel_class'=>'zalo','category'=>'Thanh toán','topic'=>'Không thanh toán được...','detail'=>'Thẻ bị từ chối khi thanh toán...','priority'=>'Trung bình','priority_class'=>'medium','assigned'=>'Nguyễn Thu Hà','date'=>'23/05/2025 16:20','status'=>'Đang xử lý','status_class'=>'amber','sla'=>'65%','sla_class'=>'amber'],
    ['id'=>'SR20250524-0005','customer'=>'Vũ Thị Mai','channel'=>'Form website','channel_class'=>'form','category'=>'Tài khoản','topic'=>'Quên mật khẩu tài khoản...','detail'=>'Không được email đặt lại...','priority'=>'Thấp','priority_class'=>'low','assigned'=>'Đỗ Hoàng Long','date'=>'22/05/2025 15:08','status'=>'Hoàn tất','status_class'=>'green','sla'=>'100%','sla_class'=>'green'],
    ['id'=>'SR20250524-0006','customer'=>'Đỗ Hoàng Long','channel'=>'Live chat','channel_class'=>'livechat','category'=>'Đổi trả','topic'=>'Muốn đổi sản phẩm...','detail'=>'Sản phẩm không đúng mô tả...','priority'=>'Trung bình','priority_class'=>'medium','assigned'=>'Nguyễn Văn An','date'=>'22/05/2025 11:43','status'=>'Hoàn tất','status_class'=>'green','sla'=>'100%','sla_class'=>'green'],
    ['id'=>'SR20250524-0007','customer'=>'Nguyễn Thu Hà','channel'=>'Email','channel_class'=>'email','category'=>'Kỹ thuật','topic'=>'Màn hình bị nhấp nháy...','detail'=>'Xuất hiện sọc ngang khi...','priority'=>'Cao','priority_class'=>'high','assigned'=>'Trần Thị Bích Ngọc','date'=>'21/05/2025 10:08','status'=>'Quá hạn','status_class'=>'red','sla'=>'0%','sla_class'=>'red'],
    ['id'=>'SR20250524-0008','customer'=>'Bùi Quang Huy','channel'=>'Hotline','channel_class'=>'hotline','category'=>'Đơn hàng','topic'=>'Hỏi về thời gian giao hàng...','detail'=>'Đơn hàng có được giao vào...','priority'=>'Thấp','priority_class'=>'low','assigned'=>'Phạm Hoàng Nam','date'=>'21/05/2025 14:05','status'=>'Chờ khách phản hồi','status_class'=>'purple','sla'=>'38%','sla_class'=>'red'],
];
@endphp
<div class="adm-page">
    <div class="adm-page-title"><h1><i class="fa fa-life-ring" style="color:var(--adm-teal);margin-right:6px"></i>Quản lý yêu cầu hỗ trợ</h1><div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › <a href="#">Khách hàng</a> › Yêu cầu hỗ trợ</div></div>

    <div class="adm-stats">
        <div class="adm-stat"><div class="icon blue"><i class="fa fa-life-ring"></i></div><div><div class="num">4.286</div><div class="lbl">Tổng yêu cầu</div><div class="trend up"><i class="fa fa-arrow-up"></i>15.3% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon teal"><i class="fa fa-inbox"></i></div><div><div class="num" style="color:var(--adm-teal)">142</div><div class="lbl">Chưa tiếp nhận</div><div class="trend up"><i class="fa fa-arrow-up"></i>8.6% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon amber"><i class="fa fa-cogs"></i></div><div><div class="num">386</div><div class="lbl">Đang xử lý</div><div class="trend up"><i class="fa fa-arrow-up"></i>5.3% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">3.512</div><div class="lbl">Đã hoàn tất</div><div class="trend up"><i class="fa fa-arrow-up"></i>17.8% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-clock-o"></i></div><div><div class="num">94,1%</div><div class="lbl">SLA đúng hạn</div><div class="trend up"><i class="fa fa-arrow-up"></i>3.2% so với tháng trước</div></div></div>
    </div>

    <div class="adm-layout">
        <div>
            <div class="adm-toolbar">
                <div class="adm-search"><i class="fa fa-search"></i><input type="text" placeholder="Tìm kiếm yêu cầu, khách hàng, email, SDT, nội dung..."></div>
                <button class="adm-btn outline"><i class="fa fa-download"></i> Nhập Excel</button>
                <button class="adm-btn outline"><i class="fa fa-upload"></i> Xuất Excel</button>
                <button class="adm-btn primary"><i class="fa fa-plus"></i> Tạo yêu cầu</button>
            </div>
            <div class="adm-filters">
                <span class="adm-filter-label">Trạng thái</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Mức ưu tiên</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Danh mục hỗ trợ</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Kênh gửi</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Nhân viên phụ trách</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Ngày tạo</span><input type="text" class="adm-select" style="width:200px" value="01/05/2025 - 24/05/2025">
            </div>
            <div class="adm-tabs">
                <button class="adm-tab active">Tất cả</button>
                <button class="adm-tab">Chưa tiếp nhận</button>
                <button class="adm-tab">Đang xử lý</button>
                <button class="adm-tab">Chờ khách phản hồi</button>
                <button class="adm-tab">Hoàn tất</button>
                <button class="adm-tab">Khẩn cấp</button>
                <button class="adm-tab">Có file đính kèm</button>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead><tr><th><input type="checkbox"></th><th>Mã YC</th><th>Khách hàng</th><th>Kênh</th><th>Danh mục</th><th>Tiêu đề / nội dung ngắn</th><th>Ưu tiên</th><th>Phụ trách</th><th>Cập nhật</th><th>Trạng thái</th><th>SLA</th><th>Thao tác</th></tr></thead>
                    <tbody>
                    @foreach($requests as $r)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td class="id" style="font-size:11px">{{ $r['id'] }}</td>
                        <td><div class="adm-user-cell"><div class="u-avatar">{{ mb_substr($r['customer'],0,1) }}</div><span class="u-name">{{ $r['customer'] }}</span></div></td>
                        <td><span class="adm-channel {{ $r['channel_class'] }}"><i class="fa fa-{{ $r['channel_class'] === 'email' ? 'envelope' : ($r['channel_class'] === 'hotline' ? 'phone' : ($r['channel_class'] === 'facebook' ? 'facebook' : ($r['channel_class'] === 'livechat' ? 'comments' : ($r['channel_class'] === 'zalo' ? 'comment' : 'globe')))) }}"></i> {{ $r['channel'] }}</span></td>
                        <td style="font-size:11.5px;font-weight:600">{{ $r['category'] }}</td>
                        <td style="max-width:200px;font-size:11.5px;color:var(--adm-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $r['topic'] }}</td>
                        <td><span class="adm-priority {{ $r['priority_class'] }}">{{ $r['priority'] }}</span></td>
                        <td><div class="adm-user-cell"><div class="u-avatar">{{ mb_substr($r['assigned'],0,1) }}</div><span class="u-name" style="font-size:11px">{{ $r['assigned'] }}</span></div></td>
                        <td style="font-size:11px;white-space:nowrap;color:var(--adm-muted)">{{ $r['date'] }}</td>
                        <td><span class="adm-badge {{ $r['status_class'] }}">{{ $r['status'] }}</span></td>
                        <td><div class="adm-sla-bar"><div class="bar-wrap"><div class="bar-fill {{ $r['sla_class'] }}" style="width:{{ $r['sla'] }}"></div></div><span class="bar-pct">{{ $r['sla'] }}</span></div></td>
                        <td><div class="adm-actions"><a href="#" title="Xem"><i class="fa fa-eye"></i></a><a href="#" title="Phản hồi"><i class="fa fa-reply"></i></a><button title="Thêm"><i class="fa fa-ellipsis-v"></i></button></div></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="adm-pagination">
                    <span class="info">Hiển thị 1 đến 8 của 4.286 yêu cầu</span>
                    <div class="pages"><a href="#"><i class="fa fa-angle-left"></i></a><span class="active">1</span><a href="#">2</a><a href="#">3</a><a href="#">4</a><a href="#">5</a><span class="dots">...</span><a href="#">429</a><a href="#"><i class="fa fa-angle-right"></i></a></div>
                    <select class="adm-select" style="width:auto"><option>10 / trang</option></select>
                </div>
            </div>

            <div class="adm-bottom-grid four-col">
                <div class="adm-bottom-card"><h4>Yêu cầu mới nhất <a href="#">Xem tất cả →</a></h4>
                    @foreach([['n'=>'Bùi Quang Huy','ch'=>'Live chat','desc'=>'Hỏi về thời gian giao hàng nhanh','d'=>'24/05/2025 10:18'],['n'=>'Nguyễn Văn An','ch'=>'Email','desc'=>'Laptop tự tắt khi cắm sạc','d'=>'24/05/2025 10:12'],['n'=>'Trần Thị Bích Ngọc','ch'=>'Hotline','desc'=>'Máy chạy chậm, quạt tốn ồn','d'=>'24/05/2025 09:58'],['n'=>'Lê Minh Đức','ch'=>'Facebook','desc'=>'Chưa nhận được đơn hàng','d'=>'24/05/2025 09:23'],['n'=>'Phạm Hoàng Nam','ch'=>'Zalo','desc'=>'Không thanh toán online','d'=>'24/05/2025 09:10']] as $item)
                    <div class="adm-alert-item"><div class="u-avatar" style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--adm-muted)">{{ mb_substr($item['n'],0,1) }}</div><div class="alert-info"><div class="alert-title">{{ $item['n'] }} <span style="font-weight:400;color:var(--adm-muted);font-size:10px">{{ $item['ch'] }}</span></div><div class="alert-desc">{{ $item['desc'] }}</div></div><div style="font-size:10px;color:var(--adm-muted);white-space:nowrap">{{ $item['d'] }}</div></div>
                    @endforeach
                </div>
                <div class="adm-bottom-card"><h4>Lịch sử xử lý <a href="#">Xem tất cả →</a></h4>
                    @foreach([['n'=>'Lê Minh Đức','a'=>'đã chuyển yêu cầu cho Phạm Hoàng Nam','d'=>'24/05/2025 10:28'],['n'=>'Nguyễn Thu Hà','a'=>'đã phản hồi khách yêu cầu SR20250524-0006','d'=>'24/05/2025 10:05'],['n'=>'Phạm Hoàng Nam','a'=>'cập nhật trạng thái SR20250524-0003 thành "Đang xử lý"','d'=>'24/05/2025 09:18'],['n'=>'Vũ Thị Mai','a'=>'đánh giá yêu cầu hỗ trợ cho Nguyễn Thu Hà...','d'=>'24/05/2025 08:50'],['n'=>'Đỗ Hoàng Long','a'=>'đã đánh giá yêu cầu SR20250524-0005 là "Hoàn tất"','d'=>'23/05/2025 16:15']] as $h)
                    <div class="adm-alert-item"><div class="u-avatar" style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--adm-muted)">{{ mb_substr($h['n'],0,1) }}</div><div class="alert-info"><div class="alert-title">{{ $h['n'] }}</div><div class="alert-desc">{{ $h['a'] }}</div></div><div style="font-size:10px;color:var(--adm-muted);white-space:nowrap">{{ $h['d'] }}</div></div>
                    @endforeach
                </div>
                <div class="adm-bottom-card"><h4>Cảnh báo cần xử lý <a href="#">Xem tất cả →</a></h4>
                    <div class="adm-alert-item"><div class="alert-icon red"><i class="fa fa-exclamation-circle"></i></div><div class="alert-info"><div class="alert-title">160 yêu cầu quá hạn SLA</div><div class="alert-desc">Cần xử lý ngay để tránh mất trải nghiệm</div></div></div>
                    <div class="adm-alert-item"><div class="alert-icon amber"><i class="fa fa-clock-o"></i></div><div class="alert-info"><div class="alert-title">210 yêu cầu xử lý trong thời gian chậm</div><div class="alert-desc">Ưu tiên xử lý trong thời gian sớm nhất</div></div></div>
                    <div class="adm-alert-item"><div class="alert-icon amber"><i class="fa fa-user"></i></div><div class="alert-info"><div class="alert-title">941 yêu cầu đang chờ phản hồi khách</div><div class="alert-desc">Nhắc nhở khách hàng tích cực hơn</div></div></div>
                    <div class="adm-alert-item"><div class="alert-icon blue"><i class="fa fa-hourglass-half"></i></div><div class="alert-info"><div class="alert-title">142 yêu cầu chưa được phân công</div><div class="alert-desc">Phân bổ nhân sự để tiếp nhận bổ hạn</div></div></div>
                </div>
                <div class="adm-bottom-card"><h4>Gợi ý tối ưu <a href="#">Xem tất cả →</a></h4>
                    <div class="adm-suggest-item"><div class="s-icon blue"><i class="fa fa-users"></i></div><div class="s-info"><div class="s-title">Phân bổ lại nhân sự</div><div class="s-desc">Giải phóng nhân viên đang quá tải.</div></div></div>
                    <div class="adm-suggest-item"><div class="s-icon teal"><i class="fa fa-file-text"></i></div><div class="s-info"><div class="s-title">Tạo phản hồi mẫu</div><div class="s-desc">Giảm thời gian phản hồi và đóng họp.</div></div></div>
                    <div class="adm-suggest-item"><div class="s-icon amber"><i class="fa fa-ticket"></i></div><div class="s-info"><div class="s-title">Ưu tiên ticket khẩn cấp</div><div class="s-desc">Tập trung xử lý yêu cầu cấp thiết.</div></div></div>
                    <div class="adm-suggest-item"><div class="s-icon purple"><i class="fa fa-link"></i></div><div class="s-info"><div class="s-title">Đồng bộ với CRM</div><div class="s-desc">Cập nhật đồ lòng khách hàng tự động.</div></div></div>
                </div>
            </div>
        </div>

        <aside class="adm-aside">
            <div class="adm-aside-card"><h4>Phân loại yêu cầu</h4>
                @foreach([['l'=>'Kỹ thuật','c'=>'1,652 (38.5%)','w'=>'39%','color'=>'teal'],['l'=>'Bảo hành','c'=>'1,126 (26.3%)','w'=>'26%','color'=>'blue'],['l'=>'Đơn hàng','c'=>'832 (19.4%)','w'=>'19%','color'=>'amber'],['l'=>'Thanh toán','c'=>'362 (8.4%)','w'=>'8%','color'=>'green'],['l'=>'Tài khoản','c'=>'202 (4.7%)','w'=>'5%','color'=>'purple'],['l'=>'Khác','c'=>'112 (2.6%)','w'=>'3%','color'=>'gray']] as $g)
                <div class="adm-progress-row"><span class="p-label" style="width:68px;text-align:left">{{ $g['l'] }}</span><div class="adm-progress"><div class="bar {{ $g['color'] }}" style="width:{{ $g['w'] }}"></div></div><span class="p-count">{{ $g['c'] }}</span></div>
                @endforeach
            </div>
            <div class="adm-aside-card"><h4>Top kênh gửi</h4>
                <ol class="adm-top-list">
                    @foreach([['n'=>'Email','v'=>'1,428 (33.3%)'],['n'=>'Hotline','v'=>'1,216 (28.4%)'],['n'=>'Live chat','v'=>'859 (20.0%)'],['n'=>'Facebook','v'=>'512 (11.9%)'],['n'=>'Zalo','v'=>'271 (6.3%)']] as $i => $t)
                    <li><span class="rank">{{ $i+1 }}</span><span class="name">{{ $t['n'] }}</span><span class="val">{{ $t['v'] }}</span></li>
                    @endforeach
                </ol>
            </div>
            <div class="adm-aside-card"><h4>Tình trạng yêu cầu</h4>
                <div class="adm-donut-wrap">
                    <div class="adm-donut" style="background:conic-gradient(var(--adm-blue) 0% 3%, var(--adm-amber) 3% 12%, #8b5cf6 12% 34%, var(--adm-green) 34% 96%, var(--adm-red) 96% 100%);width:120px;height:120px"><div class="adm-donut-center" style="width:80px;height:80px;border-radius:50%;background:#fff"><div class="pct" style="font-size:16px">81.8%</div><div class="lbl">Hoàn tất</div></div></div>
                    <ul class="adm-donut-legend">
                        <li><span class="dot" style="background:var(--adm-blue)"></span> Chưa tiếp nhận <span class="pct">142 (3.3%)</span></li>
                        <li><span class="dot" style="background:var(--adm-amber)"></span> Đang xử lý <span class="pct">386 (9.0%)</span></li>
                        <li><span class="dot" style="background:#8b5cf6"></span> Chờ khách phản hồi <span class="pct">948 (22.1%)</span></li>
                        <li><span class="dot" style="background:var(--adm-green)"></span> Hoàn tất <span class="pct">2,650 (61.8%)</span></li>
                        <li><span class="dot" style="background:var(--adm-red)"></span> Quá hạn <span class="pct">160 (3.7%)</span></li>
                    </ul>
                </div>
            </div>
            <div class="adm-aside-card"><h4>Xu hướng hỗ trợ <select class="adm-select" style="height:24px;font-size:10px;padding:0 20px 0 6px"><option>6 tháng gần đây</option></select></h4>
                <div class="adm-trend-header"><div class="legend"><span><span class="dot" style="background:var(--adm-teal);display:inline-block;width:8px;height:8px;border-radius:2px"></span> Số yêu cầu</span><span><span class="dot" style="background:#bae6fd;display:inline-block;width:8px;height:8px;border-radius:2px"></span> Tỷ lệ đúng SLA</span></div></div>
                <div class="adm-trend-chart">@for($i=0;$i<12;$i++)<div class="adm-trend-bar primary" style="height:{{ rand(25,100) }}%"></div>@endfor</div>
            </div>
        </aside>
    </div>
</div>
@endsection
