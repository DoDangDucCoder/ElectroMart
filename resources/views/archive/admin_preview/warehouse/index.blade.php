@extends('admin_layout')
@section('admin_content')
<div class="adm-page">
    <div class="adm-page-title"><h1><i class="fa fa-exchange" style="color:#0d9488;margin-right:6px"></i>Nhập kho / xuất kho</h1><div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › <a href="#">Kho hàng</a> › Nhập kho / xuất kho</div></div>

    <div class="adm-tabs" style="margin-bottom:18px"><button class="adm-tab active" style="padding:8px 20px;font-size:13px">Phiếu nhập kho</button><button class="adm-tab" style="padding:8px 20px;font-size:13px">Phiếu xuất kho</button></div>

    <div class="adm-stats">
        <div class="adm-stat"><div class="icon teal"><i class="fa fa-download"></i></div><div><div class="num">326</div><div class="lbl">Phiếu nhập tháng này</div><div class="trend up"><i class="fa fa-arrow-up"></i>12.5% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon blue"><i class="fa fa-upload"></i></div><div><div class="num">418</div><div class="lbl">Phiếu xuất tháng này</div><div class="trend up"><i class="fa fa-arrow-up"></i>8.9% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-dollar"></i></div><div><div class="num" style="font-size:16px">6.280.000.000đ</div><div class="lbl">Giá trị nhập</div><div class="trend up"><i class="fa fa-arrow-up"></i>15.2% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon amber"><i class="fa fa-money"></i></div><div><div class="num" style="font-size:16px">5.940.000.000đ</div><div class="lbl">Giá trị xuất</div><div class="trend up"><i class="fa fa-arrow-up"></i>10.4% so với tháng trước</div></div></div>
        <div class="adm-stat"><div class="icon purple"><i class="fa fa-clock-o"></i></div><div><div class="num">24</div><div class="lbl">Chờ duyệt</div><div class="lbl" style="color:#dc2626;font-weight:600">Cần xử lý</div></div></div>
    </div>

    <div class="adm-layout">
        <div>
            <div class="adm-toolbar">
                <div class="adm-search"><i class="fa fa-search"></i><input type="text" placeholder="Tìm kiếm mã phiếu, sản phẩm, nhà cung cấp..."></div>
                <button class="adm-btn primary"><i class="fa fa-plus"></i> Tạo phiếu nhập</button>
                <button class="adm-btn outline"><i class="fa fa-plus"></i> Tạo phiếu xuất</button>
                <button class="adm-btn outline"><i class="fa fa-file-excel-o"></i> Nhập Excel</button>
                <button class="adm-btn outline"><i class="fa fa-print"></i> In phiếu</button>
            </div>
            <div class="adm-filters">
                <span class="adm-filter-label">Loại phiếu</span><select class="adm-select"><option>Tất cả</option></select>
                <span class="adm-filter-label">Kho</span><select class="adm-select"><option>Tất cả kho</option></select>
                <span class="adm-filter-label">Trạng thái</span><select class="adm-select"><option>Tất cả trạng thái</option></select>
                <span class="adm-filter-label">Nhà cung cấp / Đối tác</span><select class="adm-select"><option>Tất cả đối tác</option></select>
                <span class="adm-filter-label">Thời gian</span><input type="text" class="adm-select" style="width:100px" placeholder="Từ ngày"><input type="text" class="adm-select" style="width:100px" placeholder="Đến ngày">
            </div>

            <div class="adm-tabs"><button class="adm-tab active">Tất cả</button><button class="adm-tab">Chờ duyệt</button><button class="adm-tab">Hoàn thành</button><button class="adm-tab">Đã hủy</button></div>

            @php
                $receipts = [
                    ['code'=>'NK250524001','type'=>'Nhập kho','type_class'=>'teal','partner'=>'ASUS VN','warehouse'=>'Kho Hà Nội','qty'=>12,'total'=>320000000,'creator'=>'Trần Minh Đức','date'=>'24/05/2025 09:10','status'=>'Hoàn thành','sc'=>'green'],
                    ['code'=>'XK250524002','type'=>'Xuất kho','type_class'=>'blue','partner'=>'Showroom Q1','warehouse'=>'Kho TP.HCM','qty'=>8,'total'=>156000000,'creator'=>'Lê Hoàng Nam','date'=>'24/05/2025 09:35','status'=>'Chờ duyệt','sc'=>'amber'],
                    ['code'=>'NK250523003','type'=>'Nhập kho','type_class'=>'teal','partner'=>'Phong Vũ','warehouse'=>'Kho Đà Nẵng','qty'=>20,'total'=>485000000,'creator'=>'Nguyễn Thu Hà','date'=>'23/05/2025 15:40','status'=>'Hoàn thành','sc'=>'green'],
                    ['code'=>'XK250523004','type'=>'Xuất kho','type_class'=>'blue','partner'=>'Đại lý ABC','warehouse'=>'Kho Hà Nội','qty'=>6,'total'=>98000000,'creator'=>'Vũ Thị Mai','date'=>'23/05/2025 16:20','status'=>'Đang xử lý','sc'=>'amber'],
                    ['code'=>'NK250522005','type'=>'Nhập kho','type_class'=>'teal','partner'=>'MSI VN','warehouse'=>'Kho dự phòng','qty'=>15,'total'=>268000000,'creator'=>'Trần Bích Ngọc','date'=>'22/05/2025 11:05','status'=>'Chờ kiểm','sc'=>'amber'],
                    ['code'=>'XK250522006','type'=>'Xuất kho','type_class'=>'blue','partner'=>'Website Online','warehouse'=>'Kho TP.HCM','qty'=>4,'total'=>72000000,'creator'=>'Phạm Hoàng Nam','date'=>'22/05/2025 14:18','status'=>'Hoàn thành','sc'=>'green'],
                ];
            @endphp

            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead><tr><th><input type="checkbox"></th><th>Mã phiếu</th><th>Loại</th><th>Đối tác</th><th>Kho</th><th>Số lượng SP</th><th>Tổng giá trị</th><th>Người tạo</th><th>Ngày lập</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
                    <tbody>
                    @foreach ($receipts as $r)
                        <tr>
                            <td><input type="checkbox"></td>
                            <td class="id">{{ $r['code'] }}</td>
                            <td><span class="adm-badge {{ $r['type_class'] }}">{{ $r['type'] }}</span></td>
                            <td style="font-size:12px;font-weight:600">{{ $r['partner'] }}</td>
                            <td style="font-size:12px;color:#64748b">{{ $r['warehouse'] }}</td>
                            <td style="text-align:center">{{ $r['qty'] }}</td>
                            <td style="font-weight:700;font-size:12px">{{ number_format($r['total'], 0, ',', '.') }}đ</td>
                            <td style="font-size:12px">{{ $r['creator'] }}</td>
                            <td style="font-size:11px;color:#94a3b8">{{ $r['date'] }}</td>
                            <td><span class="adm-badge {{ $r['sc'] }}">{{ $r['status'] }}</span></td>
                            <td><div class="adm-actions"><a href="#"><i class="fa fa-eye"></i></a><a href="#"><i class="fa fa-pencil"></i></a><button><i class="fa fa-ellipsis-v"></i></button></div></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="adm-pagination">
                    <span class="info">Hiển thị 1 đến 6 của 326 phiếu</span>
                    <div class="pages"><span class="active">1</span><a href="#">2</a><a href="#">3</a><a href="#">4</a><a href="#">5</a><span class="dots">...</span><a href="#">17</a><a href="#"><i class="fa fa-angle-right"></i></a></div>
                    <select class="adm-select" style="width:auto"><option>20 / trang</option></select>
                </div>
            </div>

            {{-- Bottom 5 sections --}}
            <div class="adm-bottom-grid" style="grid-template-columns:repeat(5,1fr)">
                <div class="adm-bottom-card"><h4>Phiếu nhập gần đây <a href="#">Xem tất cả</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        @foreach([['c'=>'NK250524001','f'=>'ASUS VN','v'=>'320.000.000đ','s'=>'Hoàn thành','sc'=>'green'],['c'=>'NK250523003','f'=>'Phong Vũ','v'=>'485.000.000đ','s'=>'Hoàn thành','sc'=>'green'],['c'=>'NK250522005','f'=>'MSI VN','v'=>'268.000.000đ','s'=>'Chờ kiểm','sc'=>'amber'],['c'=>'NK250520007','f'=>'Intel VN','v'=>'198.000.000đ','s'=>'Hoàn thành','sc'=>'green']] as $pn)
                        <li><span class="dot green"></span><div><strong style="color:#0d9488">{{ $pn['c'] }}</strong><br>{{ $pn['f'] }}<br><span style="font-weight:700">{{ $pn['v'] }}</span></div><span class="adm-badge {{ $pn['sc'] }}" style="font-size:8px">{{ $pn['s'] }}</span></li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Phiếu xuất gần đây <a href="#">Xem tất cả</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        @foreach([['c'=>'XK250524002','t'=>'Showroom Q1','v'=>'156.000.000đ','s'=>'Chờ duyệt','sc'=>'amber'],['c'=>'XK250523004','t'=>'Đại lý ABC','v'=>'72.000.000đ','s'=>'Hoàn thành','sc'=>'green'],['c'=>'XK250522003','t'=>'Xuất bán showroom','v'=>'15.890.000đ','s'=>'Hoàn thành','sc'=>'green'],['c'=>'XK250521004','t'=>'Khanh B','v'=>'45.000.000đ','s'=>'Hoàn thành','sc'=>'green']] as $px)
                        <li><span class="dot blue"></span><div><strong style="color:#dc2626">{{ $px['c'] }}</strong><br>{{ $px['t'] }}<br><span style="font-weight:700">{{ $px['v'] }}</span></div><span class="adm-badge {{ $px['sc'] }}" style="font-size:8px">{{ $px['s'] }}</span></li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Lịch sử duyệt phiếu <a href="#">Xem tất cả</a></h4>
                    <ul class="adm-activity" style="font-size:11px">
                        @foreach([['n'=>'Trần Minh Đức','d'=>'24/05/2025 09:10','a'=>'Duyệt nhập NK250524001','sc'=>'green'],['n'=>'Lê Hoàng Nam','d'=>'24/05/2025 09:35','a'=>'Tạo phiếu NK250524002','sc'=>'amber'],['n'=>'Vũ Thị Mai','d'=>'23/05/2025 16:25','a'=>'Duyệt xuất XK250523004','sc'=>'green'],['n'=>'Trần Bích Ngọc','d'=>'22/05/2025 11:10','a'=>'Tạo phiếu NK250522005','sc'=>'amber']] as $h)
                        <li><div style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#64748b;flex-shrink:0">{{ mb_substr($h['n'],0,1) }}</div><div><strong>{{ $h['n'] }}</strong><br><span style="font-size:10px;color:#94a3b8">{{ $h['d'] }}</span><br>{{ $h['a'] }}</div><span class="adm-badge {{ $h['sc'] }}" style="font-size:8px">{{ $h['sc'] === 'green' ? 'Đã duyệt' : 'Chờ duyệt' }}</span></li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Sản phẩm cần nhập thêm <a href="#">Xem tất cả</a></h4>
                    <ul class="adm-alert-list">
                        @foreach([['n'=>'ASUS ROG Strix G16','d'=>'Đã xuất: 25 chiếc'],['n'=>'MSI GP66 Leopard','d'=>'Đã xuất: 18 chiếc'],['n'=>'RAM Corsair 16GB DDR5','d'=>'Đã xuất: 40 sp'],['n'=>'SSD Samsung 990 Pro 1TB','d'=>'Đã xuất: 30 chiếc']] as $sp)
                        <li><div class="img"><img src="{{ asset('uploads/product/no-image.svg') }}" alt=""></div><div class="info"><div class="name">{{ $sp['n'] }}</div><div class="meta">{{ $sp['d'] }}</div></div><span class="adm-badge red" style="font-size:9px">Thiếu</span></li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card"><h4>Chi tiết nhanh</h4>
                    <div style="margin-bottom:10px"><span class="id" style="font-size:14px">NK250524001</span> <span class="adm-badge teal" style="font-size:9px">Nhập kho</span> <a href="#" style="float:right;font-size:10px;color:#0d9488;text-decoration:none">Xem chi tiết ›</a></div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:11px;margin-bottom:10px">
                        <div><span style="color:#94a3b8">Kho</span><br><strong>Kho Hà Nội</strong></div>
                        <div><span style="color:#94a3b8">Đối tác</span><br><strong>ASUS VN</strong></div>
                        <div><span style="color:#94a3b8">Ngày lập</span><br><strong>24/05/2025 09:10</strong></div>
                    </div>
                    <table style="width:100%;font-size:10.5px;border-collapse:collapse">
                        <thead><tr style="border-bottom:1px solid #f1f5f9"><th style="text-align:left;padding:4px;color:#94a3b8">Sản phẩm</th><th style="padding:4px;color:#94a3b8">SL</th><th style="padding:4px;color:#94a3b8">Đơn giá</th><th style="text-align:right;padding:4px;color:#94a3b8">Thành tiền</th></tr></thead>
                        <tbody>
                            <tr><td style="padding:4px">ASUS ROG Strix G16</td><td style="padding:4px;text-align:center">6</td><td style="padding:4px">28.000.000đ</td><td style="padding:4px;text-align:right">168.000.000đ</td></tr>
                            <tr><td style="padding:4px">RAM Corsair 16GB DDR5</td><td style="padding:4px;text-align:center">10</td><td style="padding:4px">1.600.000đ</td><td style="padding:4px;text-align:right">16.000.000đ</td></tr>
                            <tr><td style="padding:4px">SSD Samsung 990 Pro 1TB</td><td style="padding:4px;text-align:center">12</td><td style="padding:4px">22.666.667đ</td><td style="padding:4px;text-align:right">—</td></tr>
                        </tbody>
                    </table>
                    <div style="border-top:1px solid #e5e7eb;margin-top:8px;padding-top:8px;text-align:right;font-size:11px">
                        <div>Tạm tính: <strong>320.000.000đ</strong></div>
                        <div>VAT (10%): <strong>32.000.000đ</strong></div>
                        <div style="font-size:13px;font-weight:800;color:#0d9488;margin-top:4px">Tổng cộng: 352.000.000đ</div>
                    </div>
                    <div style="display:flex;gap:6px;margin-top:10px"><button class="adm-btn primary" style="flex:1;justify-content:center">Duyệt phiếu</button><button class="adm-btn outline" style="flex:1;justify-content:center">Lưu nháp</button></div>
                </div>
            </div>
        </div>

        <aside class="adm-aside">
            <div class="adm-aside-card"><h4>Cảnh báo phiếu <a href="#">Xem tất cả</a></h4>
                <ul class="adm-activity" style="font-size:11px">
                    <li><span class="dot red"></span><div>Chờ duyệt quá 24h <span class="adm-badge red" style="font-size:9px">8</span></div></li>
                    <li><span class="dot amber"></span><div>Chờ kiểm hàng <span class="adm-badge amber" style="font-size:9px">6</span></div></li>
                    <li><span class="dot blue"></span><div>Thiếu chứng từ <span class="adm-badge blue" style="font-size:9px">4</span></div></li>
                    <li><span class="dot amber"></span><div>Sắp hết hạn phiếu <span class="adm-badge amber" style="font-size:9px">3</span></div></li>
                    <li><span class="dot teal"></span><div>Giá trị lớn cần duyệt <span class="adm-badge teal" style="font-size:9px">5</span></div></li>
                </ul>
            </div>
            <div class="adm-aside-card"><h4>Phân bổ nhập / xuất theo kho</h4>
                @foreach([['n'=>'Kho Hà Nội','v'=>'62% (3.286tr)','w'=>'62%','c'=>'teal'],['n'=>'Kho TP.HCM','v'=>'76% (4.130tr)','w'=>'76%','c'=>'blue'],['n'=>'Kho Đà Nẵng','v'=>'44% (2.330tr)','w'=>'44%','c'=>'amber'],['n'=>'Kho dự phòng','v'=>'35% (1.860tr)','w'=>'35%','c'=>'green']] as $wh)
                <div style="margin-bottom:8px"><div style="display:flex;justify-content:space-between;font-size:11.5px;margin-bottom:3px"><span style="font-weight:600">{{ $wh['n'] }}</span><span style="color:#64748b;font-size:10.5px">{{ $wh['v'] }}</span></div><div class="adm-progress"><div class="bar {{ $wh['c'] }}" style="width:{{ $wh['w'] }}"></div></div></div>
                @endforeach
            </div>
            <div class="adm-aside-card"><h4>Top đối tác giao dịch <select class="adm-select" style="height:22px;font-size:10px;padding:0 16px 0 6px"><option>6 tháng gần đây</option></select></h4>
                <ol class="adm-top-list">
                    @foreach([['n'=>'ASUS VN','v'=>'2.150.000.000đ'],['n'=>'Phong Vũ','v'=>'1.840.000.000đ'],['n'=>'MSI VN','v'=>'1.520.000.000đ'],['n'=>'Showroom Q1','v'=>'1.260.000.000đ'],['n'=>'Đại lý ABC','v'=>'980.000.000đ']] as $i => $p)
                    <li><span class="rank">{{ $i+1 }}</span><span class="name">{{ $p['n'] }}</span><span class="val" style="font-size:11px">{{ $p['v'] }}</span></li>
                    @endforeach
                </ol>
            </div>
            <div class="adm-aside-card"><h4>Biến động nhập xuất <select class="adm-select" style="height:22px;font-size:10px;padding:0 16px 0 6px"><option>6 tháng gần đây</option></select></h4>
                <div style="display:flex;gap:16px;margin-bottom:8px">
                    <div><div style="font-size:14px;font-weight:800;color:#16a34a">+1.240</div><div style="font-size:10px;color:#64748b">sản phẩm nhập</div></div>
                    <div><div style="font-size:14px;font-weight:800;color:#dc2626">-980</div><div style="font-size:10px;color:#64748b">sản phẩm xuất</div></div>
                </div>
                <div class="adm-mini-chart">@for($i=0;$i<12;$i++)<div class="adm-mini-bar {{ $i > 8 ? 'active' : '' }}" style="height:{{ rand(15,95) }}%"></div>@endfor</div>
            </div>
        </aside>
    </div>
</div>
@endsection
