@extends('admin_layout')
@section('admin_content')
<link rel="stylesheet" href="{{ asset('backend/css/admin-dashboard.css') }}">
<div class="adm-page">
    <div class="adm-page-title"><h1><i class="fa fa-newspaper-o" style="color:#0d9488;margin-right:6px"></i>Quản lý bài viết</h1><div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › <a href="#">Bài viết</a> › Quản lý bài viết</div></div>

    <div class="adm-stats">
        <div class="adm-stat"><div class="icon teal"><i class="fa fa-file-text"></i></div><div><div class="num">368</div><div class="lbl">Tổng bài viết</div><div class="trend up"><i class="fa fa-arrow-up"></i> 12.6% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-check-circle"></i></div><div><div class="num" style="color:#16a34a">246</div><div class="lbl">Đã xuất bản</div><div class="trend up"><i class="fa fa-arrow-up"></i> 9.8% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon blue"><i class="fa fa-pencil-square-o"></i></div><div><div class="num" style="color:#3b82f6">54</div><div class="lbl">Bài viết nháp</div><div class="trend up"><i class="fa fa-arrow-up"></i> 4.2% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon amber"><i class="fa fa-star"></i></div><div><div class="num" style="color:#d97706">32</div><div class="lbl">Bài viết nổi bật</div><div class="trend up"><i class="fa fa-arrow-up"></i> 18.3% so với tháng trước</div></div></div>
    </div>

    <div class="adm-layout">
        <div>
            <div class="adm-toolbar">
                <div class="adm-search" style="flex:1"><i class="fa fa-search"></i><input type="text" placeholder="Tìm kiếm bài viết (tiêu đề, slug, từ khóa...)"></div>
                <button class="adm-btn outline"><i class="fa fa-download"></i> Nhập Excel</button>
                <button class="adm-btn outline"><i class="fa fa-upload"></i> Xuất Excel</button>
                <button class="adm-btn primary"><i class="fa fa-plus"></i> Thêm bài viết</button>
            </div>
            <div class="adm-filters">
                <span class="adm-filter-label">Trạng thái</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Danh mục</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Tác giả</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Loại nội dung</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Ngày cập nhật</span><select class="adm-select"><option>Tất cả</option></select>
            </div>
            <div class="adm-tabs">
                <button class="adm-tab active">Tất cả</button>
                <button class="adm-tab" style="background:#f0fdf4;color:#16a34a;border-color:#dcfce7">Đã xuất bản</button>
                <button class="adm-tab">Nháp</button>
                <button class="adm-tab" style="background:#fff7ed;color:#ea580c;border-color:#fed7aa">Nổi bật</button>
                <button class="adm-tab" style="background:#eff6ff;color:#3b82f6;border-color:#bfdbfe">Tin tức</button>
                <button class="adm-tab">Hướng dẫn</button>
                <button class="adm-tab">Chính sách</button>
            </div>

            @php
            $posts = [
                ['id'=>'BV0001','img'=>'📰','title'=>'Cách chọn laptop phù hợp cho sinh viên 2025','cat'=>'Tin tức','author'=>'Nguyễn Văn An','type'=>'Blog','date'=>'24/05/2025 10:15','views'=>'12.840','comments'=>'36','featured'=>true,'status'=>'Đã xuất bản','sc'=>'green','updated'=>'24/05/2025 10:20'],
                ['id'=>'BV0002','img'=>'📖','title'=>'Hướng dẫn kiểm tra bảo hành sản phẩm DangDuc','cat'=>'Hướng dẫn','author'=>'Trần Thị Bích Ngọc','type'=>'Blog','date'=>'23/05/2025 09:48','views'=>'8.915','comments'=>'24','featured'=>true,'status'=>'Đã xuất bản','sc'=>'green','updated'=>'23/05/2025 09:55'],
                ['id'=>'BV0003','img'=>'🖥️','title'=>'Top 5 màn hình gaming đáng mua tháng 5','cat'=>'Tin tức','author'=>'Lê Minh Đức','type'=>'Blog','date'=>'22/05/2025 14:30','views'=>'15.672','comments'=>'52','featured'=>true,'status'=>'Đã xuất bản','sc'=>'green','updated'=>'22/05/2025 14:35'],
                ['id'=>'BV0004','img'=>'📋','title'=>'Chính sách đổi trả và bảo hành mới nhất','cat'=>'Chính sách','author'=>'Phạm Hoàng Nam','type'=>'Policy','date'=>'21/05/2025 14:20','views'=>'6.341','comments'=>'18','featured'=>false,'status'=>'Chờ duyệt','sc'=>'amber','updated'=>'21/05/2025 14:25'],
                ['id'=>'BV0005','img'=>'❓','title'=>'FAQ: Các câu hỏi thường gặp khi đặt hàng','cat'=>'FAQ','author'=>'Vũ Thị Mai','type'=>'FAQ','date'=>'20/05/2025 11:05','views'=>'9.273','comments'=>'41','featured'=>true,'status'=>'Đã xuất bản','sc'=>'green','updated'=>'20/05/2025 11:10'],
                ['id'=>'BV0006','img'=>'💳','title'=>'Hướng dẫn thanh toán và xuất hóa đơn VAT','cat'=>'Hướng dẫn','author'=>'Nguyễn Văn An','type'=>'Blog','date'=>'19/05/2025 09:22','views'=>'5.842','comments'=>'21','featured'=>false,'status'=>'Đã xuất bản','sc'=>'green','updated'=>'19/05/2025 09:22'],
                ['id'=>'BV0007','img'=>'🏢','title'=>'Mẹo tối ưu góc làm việc cho dân văn phòng','cat'=>'Tin tức','author'=>'Trần Thị Bích Ngọc','type'=>'Blog','date'=>'18/05/2025 15:40','views'=>'4.693','comments'=>'16','featured'=>false,'status'=>'Nháp','sc'=>'blue','updated'=>'18/05/2025 15:45'],
                ['id'=>'BV0008','img'=>'🏷️','title'=>'Khuyến mãi cuối tuần: săn deal linh kiện','cat'=>'Khuyến mãi','author'=>'Lê Minh Đức','type'=>'News','date'=>'17/05/2025 08:30','views'=>'7.624','comments'=>'32','featured'=>false,'status'=>'Tạm ẩn','sc'=>'gray','updated'=>'17/05/2025 08:35'],
            ];
            @endphp

            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead><tr><th><input type="checkbox"></th><th>Mã</th><th>Ảnh bìa</th><th>Tiêu đề bài viết</th><th>Danh mục</th><th>Tác giả</th><th>Loại</th><th>Ngày đăng</th><th>Lượt xem</th><th>Bình luận</th><th>Nổi bật</th><th>Trạng thái</th><th>Cập nhật</th><th>Thao tác</th></tr></thead>
                    <tbody>
                    @foreach($posts as $p)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td class="id">{{ $p['id'] }}</td>
                        <td style="font-size:24px;text-align:center">{{ $p['img'] }}</td>
                        <td style="font-weight:600;font-size:12px;max-width:200px">{{ $p['title'] }}</td>
                        <td style="font-size:11.5px">{{ $p['cat'] }}</td>
                        <td style="font-size:11.5px">{{ $p['author'] }}</td>
                        <td><span class="adm-badge blue" style="font-size:10px">{{ $p['type'] }}</span></td>
                        <td style="font-size:10.5px;color:#64748b;white-space:nowrap">{{ $p['date'] }}</td>
                        <td style="font-size:12px">{{ $p['views'] }}</td>
                        <td style="font-size:12px">{{ $p['comments'] }}</td>
                        <td style="text-align:center">@if($p['featured'])<span style="color:#d97706"><i class="fa fa-star"></i></span>@else<span style="color:#cbd5e1"><i class="fa fa-star-o"></i></span>@endif</td>
                        <td><span class="adm-badge {{ $p['sc'] }}">{{ $p['status'] }}</span></td>
                        <td style="font-size:10.5px;color:#94a3b8;white-space:nowrap">{{ $p['updated'] }}</td>
                        <td><div class="adm-actions"><a href="#"><i class="fa fa-eye"></i></a><a href="#"><i class="fa fa-pencil"></i></a><button><i class="fa fa-ellipsis-v"></i></button></div></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="adm-pagination">
                    <span class="info">Hiển thị 1 đến 8 của 368 bài viết</span>
                    <div class="pages"><a href="#"><i class="fa fa-angle-left"></i></a><span class="active">1</span><a href="#">2</a><a href="#">3</a><a href="#">4</a><a href="#">5</a><span class="dots">...</span><a href="#">46</a><a href="#"><i class="fa fa-angle-right"></i></a></div>
                    <select class="adm-select" style="width:auto"><option>20 / trang</option></select>
                </div>
            </div>

            <div class="adm-bottom-grid" style="grid-template-columns:repeat(4,1fr)">
                <div class="adm-bottom-card"><h4>Bài viết mới tạo <a href="#">Xem tất cả →</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        @foreach([['c'=>'Mẹo setup hệ thống dual monitor','d'=>'24/05/2025 10:35'],['c'=>'Bảo vệ đồ điện khi làm việc online','d'=>'24/05/2025 09:22'],['c'=>'So sánh SSD Gen 4 và Gen 5','d'=>'23/05/2025 08:15'],['c'=>'Những lỗi laptop thường gặp','d'=>'23/05/2025 16:25'],['c'=>'Cách vệ sinh laptop đúng cách','d'=>'23/05/2025 15:20']] as $nv)
                        <li><span class="adm-badge red" style="font-size:9px;width:30px;justify-content:center">Mới</span><div><strong>{{ $nv['c'] }}</strong><div style="font-size:10px;color:#94a3b8">{{ $nv['d'] }}</div></div></li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Lịch sử cập nhật <a href="#">Xem tất cả →</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        @foreach([['n'=>'Nguyễn Văn An','d'=>'24/05/2025 10:20','a'=>'Cập nhật bài viết BV0001'],['n'=>'Trần Thị Bích Ngọc','d'=>'23/05/2025 09:55','a'=>'Sửa nội dung bài BV0002'],['n'=>'Lê Minh Đức','d'=>'22/05/2025 14:35','a'=>'Cập nhật ảnh bìa bài BV0003'],['n'=>'Phạm Hoàng Nam','d'=>'21/05/2025 16:45','a'=>'Đổi trạng thái bài BV0004'],['n'=>'Vũ Thị Mai','d'=>'20/05/2025 11:10','a'=>'Sửa meta description bài BV0005']] as $h)
                        <li><div style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#64748b;flex-shrink:0">{{ mb_substr($h['n'],0,1) }}</div><div><strong>{{ $h['n'] }}</strong><div style="font-size:10px;color:#94a3b8">{{ $h['d'] }}</div>{{ $h['a'] }}</div></li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Cảnh báo cần xử lý <a href="#">Xem tất cả →</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        <li><span class="dot red"></span><div><strong style="color:#dc2626">12 bài viết chờ duyệt</strong><br><span style="color:#64748b">Vui lòng kiểm tra và phê duyệt</span></div></li>
                        <li><span class="dot amber"></span><div><strong style="color:#d97706">8 bài viết thiếu ảnh bìa</strong><br><span style="color:#64748b">Cần bổ sung ảnh để tối ưu hiển thị</span></div></li>
                        <li><span class="dot blue"></span><div><strong style="color:#3b82f6">5 bài viết thiếu mô tả SEO</strong><br><span style="color:#64748b">Ảnh hưởng đến thứ hạng tìm kiếm</span></div></li>
                        <li><span class="dot amber"></span><div><strong style="color:#d97706">3 bài viết slug trùng lặp</strong><br><span style="color:#64748b">Cần chỉnh sửa để tránh trùng nội dung</span></div></li>
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Gợi ý tối ưu <a href="#">Xem tất cả →</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        <li><span class="dot teal"></span><div><strong>Tối ưu tiêu đề bài SEO</strong><br><span style="color:#64748b">Thêm từ khóa mục tiêu</span></div></li>
                        <li><span class="dot teal"></span><div><strong>Thêm internal link</strong><br><span style="color:#64748b">Liên kết đến bài viết liên quan</span></div></li>
                        <li><span class="dot blue"></span><div><strong>Cập nhật ảnh chất lượng</strong><br><span style="color:#64748b">Ảnh rõ nét, đúng kích thước</span></div></li>
                        <li><span class="dot blue"></span><div><strong>Đánh giá bài viết nổi bật</strong><br><span style="color:#64748b">Tăng khả năng hiển thị</span></div></li>
                    </ul>
                </div>
            </div>
        </div>

        <aside class="adm-aside">
            <div class="adm-aside-card"><h4>Tổng lượt xem <a href="#">Xem tất cả</a></h4>
                <div style="font-size:24px;font-weight:800;color:#111827;margin-bottom:4px">1.286.000</div>
                <div class="trend up" style="font-size:11px"><i class="fa fa-arrow-up"></i> 15.6% so với tháng trước</div>
            </div>
            <div class="adm-aside-card"><h4>Phân loại bài viết</h4>
                @foreach([['l'=>'Tin tức','v'=>128],['l'=>'Hướng dẫn','v'=>96],['l'=>'Khuyến mãi','v'=>56],['l'=>'Chính sách','v'=>56],['l'=>'FAQ','v'=>32]] as $cat)
                <div style="margin-bottom:8px"><div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px"><span style="font-weight:600">{{ $cat['l'] }}</span><span style="color:#64748b">{{ $cat['v'] }}</span></div><div class="adm-progress"><div class="bar teal" style="width:{{ round($cat['v']/128*100) }}%"></div></div></div>
                @endforeach
            </div>
            <div class="adm-aside-card"><h4>Top bài viết lượt xem cao <a href="#">Xem tất cả</a></h4>
                <ol class="adm-top-list">
                    @foreach([['n'=>'Top 5 màn hình gaming đáng mua...','v'=>'15.672'],['n'=>'Cách chọn laptop phù hợp cho...','v'=>'12.840'],['n'=>'FAQ: Các câu hỏi thường gặp...','v'=>'9.273'],['n'=>'Hướng dẫn kiểm tra bảo hành...','v'=>'8.915'],['n'=>'Khuyến mãi cuối tuần: săn deal...','v'=>'7.624']] as $i=>$t)
                    <li><span class="rank">{{ $i+1 }}</span><span style="width:8px;height:8px;border-radius:50%;background:var(--adm-teal);flex-shrink:0"></span><span class="name">{{ $t['n'] }}</span><span class="val" style="font-size:10px">{{ $t['v'] }}</span></li>
                    @endforeach
                </ol>
            </div>
            <div class="adm-aside-card"><h4>Tình trạng bài viết</h4>
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:10px">
                    <div style="width:80px;height:80px;border-radius:50%;background:conic-gradient(#16a34a 0deg 240deg,#3b82f6 240deg 293deg,#f59e0b 293deg 328deg,#94a3b8 328deg 360deg);position:relative;flex-shrink:0"><div style="position:absolute;inset:12px;border-radius:50%;background:#fff"></div></div>
                    <div style="font-size:11px;display:grid;gap:4px">
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#16a34a"></span>Đã xuất bản <strong>246 (66.8%)</strong></div>
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#3b82f6"></span>Nháp <strong>54 (14.7%)</strong></div>
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#f59e0b"></span>Chờ duyệt <strong>36 (9.8%)</strong></div>
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#94a3b8"></span>Tạm ẩn <strong>32 (8.7%)</strong></div>
                    </div>
                </div>
            </div>
            <div class="adm-aside-card"><h4>Hiệu suất nội dung <select class="adm-select" style="height:22px;font-size:10px;padding:0 16px 0 6px"><option>4 tháng gần đây</option></select></h4>
                <div class="adm-mini-chart" style="height:60px">@for($i=0;$i<12;$i++)<div class="adm-mini-bar {{ $i > 9 ? 'active' : '' }}" style="height:{{ rand(20,100) }}%"></div>@endfor</div>
            </div>
        </aside>
    </div>
</div>
@endsection
