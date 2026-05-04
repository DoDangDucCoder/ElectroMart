@extends('admin_layout')
@section('admin_content')
<link rel="stylesheet" href="{{ asset('backend/css/admin-dashboard.css') }}">
<div class="adm-page">
    <div class="adm-page-title"><h1><i class="fa fa-diamond" style="color:#0d9488;margin-right:6px"></i>Quản lý thương hiệu</h1><div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › <a href="#">Sản phẩm</a> › Quản lý thương hiệu</div></div>

    <div class="adm-stats">
        <div class="adm-stat"><div class="icon teal"><i class="fa fa-building"></i></div><div><div class="num">248</div><div class="lbl">Tổng thương hiệu</div><div class="trend up"><i class="fa fa-arrow-up"></i>12.5% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-check-circle"></i></div><div><div class="num" style="color:#16a34a">226</div><div class="lbl">Thương hiệu đang hoạt động</div><div class="trend up"><i class="fa fa-arrow-up"></i>8.3% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon amber"><i class="fa fa-star"></i></div><div><div class="num">32</div><div class="lbl">Thương hiệu nổi bật</div><div class="lbl" style="color:#64748b">Được ưu tiên hiển thị</div></div></div>
        <div class="adm-stat"><div class="icon blue"><i class="fa fa-cubes"></i></div><div><div class="num">8.426</div><div class="lbl">Tổng sản phẩm theo thương hiệu</div><div class="trend up"><i class="fa fa-arrow-up"></i>6.8% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon purple"><i class="fa fa-money"></i></div><div><div class="num" style="font-size:14px">18.290.000.000đ</div><div class="lbl">Doanh thu từ brand top</div><div class="trend up"><i class="fa fa-arrow-up"></i>14.1% so với tháng trước</div></div></div>
    </div>

    <div class="adm-layout">
        <div>
            <div class="adm-toolbar">
                <div class="adm-search" style="flex:1"><i class="fa fa-search"></i><input type="text" placeholder="Tìm kiếm thương hiệu (tên, slug, website...)"></div>
                <button class="adm-btn outline"><i class="fa fa-download"></i> Nhập Excel</button>
                <button class="adm-btn outline"><i class="fa fa-upload"></i> Xuất Excel</button>
                <button class="adm-btn primary"><i class="fa fa-plus"></i> Thêm thương hiệu</button>
            </div>
            <div class="adm-filters">
                <span class="adm-filter-label">Trạng thái</span><select class="adm-select"><option>Tất cả trạng thái</option></select>
                <span class="adm-filter-label">Quốc gia</span><select class="adm-select"><option>Tất cả quốc gia</option></select>
                <span class="adm-filter-label">Danh mục</span><select class="adm-select"><option>Tất cả danh mục</option></select>
                <span class="adm-filter-label">Nổi bật</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Thời gian cập nhật</span><select class="adm-select"><option>Tất cả thời gian</option></select>
            </div>
            <div class="adm-tabs"><button class="adm-tab active">Tất cả</button><button class="adm-tab" style="background:#f0fdf4;color:#16a34a;border-color:#dcfce7">Đang hoạt động</button><button class="adm-tab">Nổi bật</button><button class="adm-tab">Tạm ẩn</button></div>

            @php
                $brands = [
                    ['code'=>'BR001','logo'=>'A','bgc'=>'#1a1a2e','name'=>'ASUS','slug'=>'asus','country'=>'Đài Loan','cats'=>'Laptop / Linh kiện','count'=>286,'featured'=>'Có','status'=>'Hoạt động','sc'=>'green','updated'=>'24/05/2025 10:20'],
                    ['code'=>'BR002','logo'=>'M','bgc'=>'#dc2626','name'=>'MSI','slug'=>'msi','country'=>'Đài Loan','cats'=>'Laptop / Gaming','count'=>174,'featured'=>'Có','status'=>'Hoạt động','sc'=>'green','updated'=>'24/05/2025 09:48'],
                    ['code'=>'BR003','logo'=>'LG','bgc'=>'#a3a3a3','name'=>'LG','slug'=>'lg','country'=>'Hàn Quốc','cats'=>'Màn hình / Phụ kiện','count'=>132,'featured'=>'Không','status'=>'Hoạt động','sc'=>'green','updated'=>'24/05/2025 09:21'],
                    ['code'=>'BR004','logo'=>'L','bgc'=>'#16a34a','name'=>'Logitech','slug'=>'logitech','country'=>'Thụy Sĩ','cats'=>'Phụ kiện','count'=>96,'featured'=>'Có','status'=>'Hoạt động','sc'=>'green','updated'=>'24/05/2025 08:57'],
                    ['code'=>'BR005','logo'=>'C','bgc'=>'#1a1a2e','name'=>'Corsair','slug'=>'corsair','country'=>'Mỹ','cats'=>'Linh kiện','count'=>84,'featured'=>'Không','status'=>'Hoạt động','sc'=>'green','updated'=>'23/05/2025 16:12'],
                    ['code'=>'BR006','logo'=>'DE','bgc'=>'#3b82f6','name'=>'Dell','slug'=>'dell','country'=>'Mỹ','cats'=>'Laptop / Monitor','count'=>158,'featured'=>'Không','status'=>'Tạm ẩn','sc'=>'amber','updated'=>'23/05/2025 14:55'],
                    ['code'=>'BR007','logo'=>'AK','bgc'=>'#f59e0b','name'=>'AKKO','slug'=>'akko','country'=>'Trung Quốc','cats'=>'Phụ kiện','count'=>43,'featured'=>'Không','status'=>'Hoạt động','sc'=>'green','updated'=>'23/05/2025 11:30'],
                    ['code'=>'BR008','logo'=>'K','bgc'=>'#dc2626','name'=>'Kingston','slug'=>'kingston','country'=>'Mỹ','cats'=>'Linh kiện','count'=>119,'featured'=>'Có','status'=>'Hoạt động','sc'=>'green','updated'=>'23/05/2025 10:08'],
                ];
            @endphp

            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead><tr><th><input type="checkbox"></th><th>Mã</th><th>Logo</th><th>Thương hiệu</th><th>Slug</th><th>Quốc gia</th><th>Danh mục</th><th>Số SP</th><th>Nổi bật</th><th>Trạng thái</th><th>Cập nhật</th><th>Thao tác</th></tr></thead>
                    <tbody>
                    @foreach ($brands as $b)
                        <tr>
                            <td><input type="checkbox"></td>
                            <td class="id">{{ $b['code'] }}</td>
                            <td><div style="width:36px;height:36px;border-radius:8px;background:{{ $b['bgc'] }};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:11px">{{ $b['logo'] }}</div></td>
                            <td style="font-weight:700;font-size:13px">{{ $b['name'] }}</td>
                            <td style="font-size:11px;color:#64748b">{{ $b['slug'] }}</td>
                            <td style="font-size:12px">{{ $b['country'] }}</td>
                            <td style="font-size:11.5px;color:#64748b">{{ $b['cats'] }}</td>
                            <td style="text-align:center;font-weight:600">{{ $b['count'] }}</td>
                            <td style="text-align:center">@if($b['featured']==='Có')<span class="adm-badge green" style="font-size:9px">Có</span>@else<span style="color:#94a3b8;font-size:11px">Không</span>@endif</td>
                            <td><span class="adm-badge {{ $b['sc'] }}">{{ $b['status'] }}</span></td>
                            <td style="font-size:10.5px;color:#94a3b8;white-space:nowrap">{{ $b['updated'] }}</td>
                            <td><div class="adm-actions"><a href="#"><i class="fa fa-eye"></i></a><a href="#"><i class="fa fa-pencil"></i></a><button><i class="fa fa-ellipsis-v"></i></button></div></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="adm-pagination">
                    <span class="info">Hiển thị 1 đến 8 của 248 thương hiệu</span>
                    <div class="pages"><a href="#"><i class="fa fa-angle-left"></i></a><span class="active">1</span><a href="#">2</a><a href="#">3</a><a href="#">4</a><a href="#">5</a><span class="dots">...</span><a href="#">31</a><a href="#"><i class="fa fa-angle-right"></i></a></div>
                    <select class="adm-select" style="width:auto"><option>20 / trang</option></select>
                </div>
            </div>

            <div class="adm-bottom-grid" style="grid-template-columns:repeat(4,1fr)">
                <div class="adm-bottom-card"><h4>Thương hiệu mới thêm <a href="#">Xem tất cả</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        @foreach([['n'=>'Thunderobot','d'=>'24/05/2025 10:15'],['n'=>'Pixio','d'=>'24/05/2025 09:42'],['n'=>'DeepCool','d'=>'24/05/2025 08:35'],['n'=>'HyperX','d'=>'23/05/2025 16:20'],['n'=>'Razer','d'=>'23/05/2025 14:10']] as $nb)
                        <li><div style="width:28px;height:28px;border-radius:6px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#64748b;flex-shrink:0">{{ mb_substr($nb['n'],0,1) }}</div><div><strong>{{ $nb['n'] }}</strong><div style="font-size:10px;color:#94a3b8">{{ $nb['d'] }}</div></div></li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Lịch sử cập nhật <a href="#">Xem tất cả</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        @foreach([['n'=>'Nguyễn Văn An','d'=>'24/05/2025 10:20','a'=>'Cập nhật ASUS'],['n'=>'Trần Thị Bích Ngọc','d'=>'24/05/2025 09:48','a'=>'Cập nhật MSI'],['n'=>'Lê Minh Đức','d'=>'24/05/2025 09:21','a'=>'Cập nhật LG'],['n'=>'Phạm Hoàng Nam','d'=>'23/05/2025 14:55','a'=>'Cập nhật Dell'],['n'=>'Vũ Thị Mai','d'=>'23/05/2025 10:08','a'=>'Cập nhật Kingston']] as $h)
                        <li><div style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#64748b;flex-shrink:0">{{ mb_substr($h['n'],0,1) }}</div><div><strong>{{ $h['n'] }}</strong> <span style="font-size:10px;color:#94a3b8">{{ $h['d'] }}</span><br>{{ $h['a'] }}</div></li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Brand cần bổ sung banner / mô tả</h4>
                    <ul class="adm-activity" style="font-size:11px">
                        @foreach([['n'=>'AKKO','tag'=>'Thiếu banner','sc'=>'red'],['n'=>'Pixio','tag'=>'Thiếu banner','sc'=>'red'],['n'=>'DeepCool','tag'=>'Thiếu SEO','sc'=>'amber'],['n'=>'Thunderobot','tag'=>'Thiếu mô tả','sc'=>'amber']] as $miss)
                        <li><div style="width:28px;height:28px;border-radius:6px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#64748b;flex-shrink:0">{{ mb_substr($miss['n'],0,1) }}</div><div><strong>{{ $miss['n'] }}</strong></div><span class="adm-badge {{ $miss['sc'] }}" style="font-size:9px">{{ $miss['tag'] }}</span></li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Gợi ý thương hiệu nổi bật</h4>
                    <ul class="adm-activity" style="font-size:11px">
                        @foreach([['n'=>'Razer','tag'=>'Đề cử nổi bật'],['n'=>'HyperX','tag'=>'Đề cử nổi bật'],['n'=>'MSI','tag'=>'Đề cử nổi bật']] as $rec)
                        <li><div style="width:28px;height:28px;border-radius:6px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#64748b;flex-shrink:0">{{ mb_substr($rec['n'],0,1) }}</div><div><strong>{{ $rec['n'] }}</strong></div><span class="adm-badge blue" style="font-size:9px">{{ $rec['tag'] }}</span></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <aside class="adm-aside">
            <div class="adm-aside-card"><h4>Phân loại thương hiệu <a href="#">Xem tất cả</a></h4>
                @foreach([['l'=>'Laptop','v'=>'82 thương hiệu','pct'=>'33.1%','w'=>'33%','c'=>'teal'],['l'=>'Linh kiện','v'=>'74 thương hiệu','pct'=>'29.8%','w'=>'30%','c'=>'blue'],['l'=>'Phụ kiện','v'=>'56 thương hiệu','pct'=>'22.6%','w'=>'23%','c'=>'amber'],['l'=>'Màn hình','v'=>'36 thương hiệu','pct'=>'14.5%','w'=>'15%','c'=>'green']] as $cat)
                <div style="margin-bottom:8px"><div style="display:flex;justify-content:space-between;font-size:11.5px;margin-bottom:3px"><span style="font-weight:600">{{ $cat['l'] }}</span><span style="color:#64748b;font-size:10.5px">{{ $cat['v'] }} — {{ $cat['pct'] }}</span></div><div class="adm-progress"><div class="bar {{ $cat['c'] }}" style="width:{{ $cat['w'] }}"></div></div></div>
                @endforeach
            </div>
            <div class="adm-aside-card"><h4>Top thương hiệu theo số sản phẩm <a href="#">Xem tất cả</a></h4>
                <ol class="adm-top-list">
                    @foreach([['n'=>'ASUS','v'=>286],['n'=>'MSI','v'=>174],['n'=>'Dell','v'=>158],['n'=>'LG','v'=>132],['n'=>'Kingston','v'=>119]] as $i => $t)
                    <li><span class="rank">{{ $i+1 }}</span><div style="width:24px;height:24px;border-radius:6px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:700;color:#64748b;flex-shrink:0">{{ mb_substr($t['n'],0,2) }}</div><span class="name">{{ $t['n'] }}</span><span class="val">{{ $t['v'] }}</span></li>
                    @endforeach
                </ol>
            </div>
            <div class="adm-aside-card"><h4>Tình trạng thương hiệu</h4>
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:6px">
                    <div style="width:80px;height:80px;border-radius:50%;background:conic-gradient(#16a34a 0deg 328deg,#f59e0b 328deg 348deg,#3b82f6 348deg 360deg);position:relative;flex-shrink:0"><div style="position:absolute;inset:12px;border-radius:50%;background:#fff"></div></div>
                    <div style="font-size:11px;display:grid;gap:4px">
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#16a34a"></span>Hoạt động <strong>226 (91.1%)</strong></div>
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#f59e0b"></span>Tạm ẩn <strong>14 (5.6%)</strong></div>
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#3b82f6"></span>Nổi bật <strong>32 (12.9%)</strong></div>
                        <div style="display:flex;align-items:center;gap:6px"><span style="width:8px;height:8px;border-radius:50%;background:#94a3b8"></span>Chờ duyệt <strong>8 (3.2%)</strong></div>
                    </div>
                </div>
            </div>
            <div class="adm-aside-card"><h4>Tăng trưởng thương hiệu <select class="adm-select" style="height:22px;font-size:10px;padding:0 16px 0 6px"><option>6 tháng gần đây</option></select></h4>
                <div style="font-size:11px;color:#64748b;margin-bottom:6px">—• Số lượng thương hiệu</div>
                <div class="adm-mini-chart" style="height:70px">@for($i=0;$i<12;$i++)<div class="adm-mini-bar {{ $i > 9 ? 'active' : '' }}" style="height:{{ rand(25,100) }}%"></div>@endfor</div>
            </div>
        </aside>
    </div>
</div>
@endsection
