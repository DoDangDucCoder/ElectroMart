@extends('admin_layout')
@section('admin_content')
<div class="adm-page">
    <div class="adm-page-title"><h1><i class="fa fa-sliders" style="color:#0d9488;margin-right:6px"></i>Quản lý thuộc tính sản phẩm</h1><div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › <a href="#">Sản phẩm</a> › Quản lý thuộc tính</div></div>

    <div class="adm-stats">
        <div class="adm-stat"><div class="icon teal"><i class="fa fa-list-ul"></i></div><div><div class="num">156</div><div class="lbl">Tổng thuộc tính</div><div class="trend up"><i class="fa fa-arrow-up"></i>9.8% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-th-large"></i></div><div><div class="num">18</div><div class="lbl">Nhóm thuộc tính</div><div class="lbl" style="color:#64748b">Đang chuẩn hoá</div></div></div>
        <div class="adm-stat"><div class="icon blue"><i class="fa fa-tags"></i></div><div><div class="num">1.284</div><div class="lbl">Giá trị thuộc tính</div><div class="trend up"><i class="fa fa-arrow-up"></i>14.2% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon amber"><i class="fa fa-star"></i></div><div><div class="num">24</div><div class="lbl">Thuộc tính nổi bật</div><div class="lbl" style="color:#64748b">Ưu tiên hiển thị</div></div></div>
        <div class="adm-stat"><div class="icon purple"><i class="fa fa-cubes"></i></div><div><div class="num">3.426</div><div class="lbl">Sản phẩm có biến thể</div><div class="trend up"><i class="fa fa-arrow-up"></i>11.6% so với tháng trước</div></div></div>
    </div>

    <div class="adm-layout">
        <div>
            <div class="adm-toolbar">
                <div class="adm-search" style="flex:1"><i class="fa fa-search"></i><input type="text" placeholder="Tìm kiếm thuộc tính (tên, slug, mã...)"></div>
                <button class="adm-btn outline"><i class="fa fa-download"></i> Nhập Excel</button>
                <button class="adm-btn outline"><i class="fa fa-upload"></i> Xuất Excel</button>
                <button class="adm-btn primary"><i class="fa fa-plus"></i> Thêm thuộc tính</button>
            </div>
            <div class="adm-filters">
                <span class="adm-filter-label">Nhóm thuộc tính</span><select class="adm-select"><option>Tất cả nhóm</option></select>
                <span class="adm-filter-label">Loại dữ liệu</span><select class="adm-select"><option>Tất cả loại</option></select>
                <span class="adm-filter-label">Danh mục áp dụng</span><select class="adm-select"><option>Tất cả danh mục</option></select>
                <span class="adm-filter-label">Trạng thái</span><select class="adm-select"><option>Tất cả trạng thái</option></select>
                <span class="adm-filter-label">Thời gian cập nhật</span><select class="adm-select"><option>Tất cả thời gian</option></select>
            </div>
            <div class="adm-tabs"><button class="adm-tab active">Tất cả</button><button class="adm-tab" style="background:#f0fdf4;color:#16a34a;border-color:#dcfce7">Đang hoạt động</button><button class="adm-tab">Có biến thể</button><button class="adm-tab"><i class="fa fa-filter" style="margin-right:4px"></i> Bộ lọc</button><button class="adm-tab">Tạm ẩn</button></div>

            @php
                $attrs = [
                    ['code'=>'AT001','name'=>'Màu sắc','slug'=>'mau-sac','group'=>'Biến thể','gsc'=>'amber','dtype'=>'Swatch màu','cats'=>'Laptop / Phụ kiện','vals'=>12,'display'=>'Swatch','status'=>'Hoạt động','sc'=>'green','updated'=>'24/05/2025 10:28'],
                    ['code'=>'AT002','name'=>'Dung lượng RAM','slug'=>'dung-luong-ram','group'=>'Cấu hình','gsc'=>'blue','dtype'=>'Danh sách chọn','cats'=>'Laptop','vals'=>8,'display'=>'Dropdown','status'=>'Hoạt động','sc'=>'green','updated'=>'24/05/2025 09:48'],
                    ['code'=>'AT003','name'=>'Ổ cứng SSD','slug'=>'o-cung-ssd','group'=>'Cấu hình','gsc'=>'blue','dtype'=>'Danh sách chọn','cats'=>'Laptop / PC','vals'=>10,'display'=>'Button pill','status'=>'Hoạt động','sc'=>'green','updated'=>'24/05/2025 09:21'],
                    ['code'=>'AT004','name'=>'Kích thước màn hình','slug'=>'kich-thuoc-man-hinh','group'=>'Thông số','gsc'=>'green','dtype'=>'Số / text','cats'=>'Laptop / Màn hình','vals'=>9,'display'=>'Text tag','status'=>'Hoạt động','sc'=>'green','updated'=>'24/05/2025 08:57'],
                    ['code'=>'AT005','name'=>'Card đồ hoạ','slug'=>'card-do-hoa','group'=>'Cấu hình','gsc'=>'blue','dtype'=>'Danh sách chọn','cats'=>'Laptop / PC','vals'=>16,'display'=>'Dropdown','status'=>'Nổi bật','sc'=>'amber','updated'=>'23/05/2025 16:12'],
                    ['code'=>'AT006','name'=>'Loại bàn phím','slug'=>'loai-ban-phim','group'=>'Tính năng','gsc'=>'purple','dtype'=>'Danh sách chọn','cats'=>'Laptop','vals'=>6,'display'=>'Text tag','status'=>'Tạm ẩn','sc'=>'gray','updated'=>'23/05/2025 14:55'],
                    ['code'=>'AT007','name'=>'Tần số quét','slug'=>'tan-so-quet','group'=>'Thông số','gsc'=>'green','dtype'=>'Số / text','cats'=>'Màn hình','vals'=>7,'display'=>'Button pill','status'=>'Hoạt động','sc'=>'green','updated'=>'23/05/2025 11:30'],
                    ['code'=>'AT008','name'=>'Kết nối không dây','slug'=>'ket-noi-khong-day','group'=>'Tính năng','gsc'=>'purple','dtype'=>'Checkbox nhiều','cats'=>'Phụ kiện','vals'=>5,'display'=>'Checkbox','status'=>'Hoạt động','sc'=>'green','updated'=>'23/05/2025 10:08'],
                ];
            @endphp

            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead><tr><th><input type="checkbox"></th><th>Mã</th><th>Tên thuộc tính</th><th>Slug</th><th>Nhóm</th><th>Loại dữ liệu</th><th>Danh mục áp dụng</th><th>Số giá trị</th><th>Kiểu hiển thị</th><th>Trạng thái</th><th>Cập nhật</th><th>Thao tác</th></tr></thead>
                    <tbody>
                    @foreach ($attrs as $a)
                        <tr>
                            <td><input type="checkbox"></td>
                            <td class="id">{{ $a['code'] }}</td>
                            <td style="font-weight:700;font-size:12.5px">{{ $a['name'] }}</td>
                            <td style="font-size:11px;color:#64748b">{{ $a['slug'] }}</td>
                            <td><span class="adm-badge {{ $a['gsc'] }}">{{ $a['group'] }}</span></td>
                            <td style="font-size:11.5px">{{ $a['dtype'] }}</td>
                            <td style="font-size:11px;color:#64748b">{{ $a['cats'] }}</td>
                            <td style="text-align:center;font-weight:600">{{ $a['vals'] }}</td>
                            <td style="font-size:11px">{{ $a['display'] }}</td>
                            <td><span class="adm-badge {{ $a['sc'] }}">{{ $a['status'] }}</span></td>
                            <td style="font-size:10.5px;color:#94a3b8;white-space:nowrap">{{ $a['updated'] }}</td>
                            <td><div class="adm-actions"><a href="#"><i class="fa fa-eye"></i></a><a href="#"><i class="fa fa-pencil"></i></a><button><i class="fa fa-ellipsis-v"></i></button></div></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="adm-pagination">
                    <span class="info">Hiển thị 1 đến 8 của 156 thuộc tính</span>
                    <div class="pages">
                        <a class="adm-page-link disabled" href="#"><i class="fa fa-angle-left"></i></a>
                        <a class="adm-page-link active" href="#">1</a>
                        <a class="adm-page-link" href="#">2</a>
                        <a class="adm-page-link" href="#">3</a>
                        <span class="adm-page-ellipsis">...</span>
                        <a class="adm-page-link" href="#">20</a>
                        <a class="adm-page-link" href="#"><i class="fa fa-angle-right"></i></a>
                    </div>
                    <select class="adm-per-page-select" aria-label="Số thuộc tính mỗi trang"><option>20 / trang</option></select>
                </div>
            </div>

            <div class="adm-bottom-grid" style="grid-template-columns:repeat(4,1fr)">
                <div class="adm-bottom-card"><h4>Thuộc tính mới thêm <a href="#">Xem tất cả</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        @foreach([['n'=>'Thunderbolt','d'=>'24/05/2025 10:15'],['n'=>'Bluetooth','d'=>'24/05/2025 09:42'],['n'=>'Wi-Fi 7','d'=>'24/05/2025 08:35'],['n'=>'Màu titan','d'=>'23/05/2025 16:20'],['n'=>'Bề mặt phủ nhám','d'=>'23/05/2025 14:10']] as $na)
                        <li><span class="dot teal"></span><div><strong>{{ $na['n'] }}</strong><div style="font-size:10px;color:#94a3b8">{{ $na['d'] }}</div></div></li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Lịch sử cập nhật <a href="#">Xem tất cả</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        @foreach([['n'=>'Nguyễn Văn An','d'=>'24/05/2025 10:28','a'=>'Cập nhật Màu sắc'],['n'=>'Trần Thị Bích Ngọc','d'=>'24/05/2025 09:48','a'=>'Cập nhật Card đồ hoạ'],['n'=>'Lê Minh Đức','d'=>'24/05/2025 09:21','a'=>'Cập nhật Ổ cứng SSD'],['n'=>'Phạm Hoàng Nam','d'=>'23/05/2025 08:57','a'=>'Cập nhật Tần số quét'],['n'=>'Vũ Thị Mai','d'=>'23/05/2025 15:08','a'=>'Cập nhật Loại bàn phím']] as $h)
                        <li><div style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#64748b;flex-shrink:0">{{ mb_substr($h['n'],0,1) }}</div><div><strong>{{ $h['n'] }}</strong> <span style="font-size:10px;color:#94a3b8">{{ $h['d'] }}</span><br>{{ $h['a'] }}</div></li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Giá trị thuộc tính phổ biến</h4>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px">
                        @foreach(['8GB','16GB','32GB','64GB'] as $v)<span class="adm-badge blue" style="font-size:10px">{{ $v }}</span>@endforeach
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px">
                        @foreach(['512GB','1TB','2TB'] as $v)<span class="adm-badge teal" style="font-size:10px">{{ $v }}</span>@endforeach
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px">
                        @foreach(['RTX 4060','RTX 4070','RTX 4080'] as $v)<span class="adm-badge amber" style="font-size:10px">{{ $v }}</span>@endforeach
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px">
                        @foreach(['144Hz','165Hz','240Hz'] as $v)<span class="adm-badge green" style="font-size:10px">{{ $v }}</span>@endforeach
                    </div>
                </div>
                <div class="adm-bottom-card"><h4>Gợi ý chuẩn hoá <a href="#">Xem tất cả</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        <li><span class="dot teal"></span><div><strong>Hợp nhất "Xám" và "Gray"</strong><br><span style="color:#64748b">Tìm thấy 2 giá trị tương đồng</span></div><button class="adm-btn sm outline" style="font-size:9px">Chuẩn hoá</button></li>
                        <li><span class="dot blue"></span><div><strong>Chuẩn hoá đơn vị GB/TB</strong><br><span style="color:#64748b">Thống nhất cách viết dung lượng</span></div><button class="adm-btn sm outline" style="font-size:9px">Chuẩn hoá</button></li>
                        <li><span class="dot amber"></span><div><strong>Gom nhóm tần số quét</strong><br><span style="color:#64748b">Chuẩn hoá 144Hz, 165Hz, 240Hz</span></div><button class="adm-btn sm outline" style="font-size:9px">Xem chi tiết</button></li>
                        <li><span class="dot green"></span><div><strong>Chuẩn hoá kết nối không dây</strong><br><span style="color:#64748b">Wi-Fi 6 / 6E / 7 vào 1 nhóm</span></div><button class="adm-btn sm outline" style="font-size:9px">Xem chi tiết</button></li>
                    </ul>
                </div>
            </div>
        </div>

        <aside class="adm-aside">
            <div class="adm-aside-card"><h4>Phân loại thuộc tính <a href="#">Xem tất cả</a></h4>
                @foreach([['l'=>'Biến thể','v'=>38,'c'=>'amber'],['l'=>'Cấu hình','v'=>46,'c'=>'blue'],['l'=>'Thông số','v'=>42,'c'=>'green'],['l'=>'Tính năng','v'=>30,'c'=>'purple']] as $cat)
                <div style="margin-bottom:8px"><div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px"><span style="font-weight:600">{{ $cat['l'] }}</span><span style="color:#64748b">{{ $cat['v'] }}</span></div><div class="adm-progress"><div class="bar {{ $cat['c'] }}" style="width:{{ round($cat['v']/46*100) }}%"></div></div></div>
                @endforeach
            </div>
            <div class="adm-aside-card"><h4>Top thuộc tính theo số sản phẩm <a href="#">Xem tất cả</a></h4>
                <ol class="adm-top-list">
                    @foreach([['n'=>'Màu sắc','v'=>'2.846'],['n'=>'Dung lượng RAM','v'=>'2.412'],['n'=>'Ổ cứng SSD','v'=>'2.187'],['n'=>'Card đồ hoạ','v'=>'1.954'],['n'=>'Kích thước màn hình','v'=>'1.736']] as $i => $t)
                    <li><span class="rank">{{ $i+1 }}</span><span style="width:8px;height:8px;border-radius:50%;background:{{ ['#0d9488','#3b82f6','#dc2626','#f59e0b','#16a34a'][$i] }};flex-shrink:0"></span><span class="name">{{ $t['n'] }}</span><span class="val" style="font-size:11px">{{ $t['v'] }}</span></li>
                    @endforeach
                </ol>
            </div>
            <div class="adm-aside-card"><h4>Tình trạng thuộc tính</h4>
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:6px">
                    <div style="width:80px;height:80px;border-radius:50%;background:conic-gradient(#16a34a 0deg 242deg,#f59e0b 242deg 274deg,#3b82f6 274deg 318deg,#94a3b8 318deg 360deg);position:relative;flex-shrink:0"><div style="position:absolute;inset:12px;border-radius:50%;background:#fff"></div></div>
                    <div style="font-size:11px;display:grid;gap:4px">
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#16a34a"></span>Hoạt động <strong>132 (67.3%)</strong></div>
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#f59e0b"></span>Tạm ẩn <strong>18 (9.2%)</strong></div>
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#3b82f6"></span>Nổi bật <strong>24 (12.2%)</strong></div>
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#94a3b8"></span>Chờ duyệt <strong>6 (3.1%)</strong></div>
                    </div>
                </div>
            </div>
            <div class="adm-aside-card"><h4>Tăng trưởng thuộc tính <select class="adm-select" style="height:22px;font-size:10px;padding:0 16px 0 6px"><option>6 tháng gần đây</option></select></h4>
                <div style="font-size:11px;color:#64748b;margin-bottom:6px">—• Số lượng thuộc tính</div>
                <div class="adm-mini-chart" style="height:70px">@for($i=0;$i<12;$i++)<div class="adm-mini-bar {{ $i > 9 ? 'active' : '' }}" style="height:{{ rand(20,100) }}%"></div>@endfor</div>
            </div>
        </aside>
    </div>
</div>
@endsection
