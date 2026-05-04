@php $dd_active = 'admin/shipping'; @endphp
@extends('layouts.dangduc_admin')

@section('page_title', 'Quản lý vận chuyển')
@section('header_title', 'Quản lý vận chuyển')
@section('header_search', 'Tìm kiếm mã vận đơn, đơn hàng, khách hàng, hãng vận chuyển...')

@section('content')
<div class="adm-page">
    <div class="adm-page-title" style="margin-bottom:16px">
        <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › <a href="#">Đơn hàng</a> › Quản lý vận chuyển</div>
    </div>

    {{-- =========== KPI cards =========== --}}
    <div class="adm-stats">
        @php
            $stats = [
                ['lbl'=>'Tổng vận đơn','num'=>'12.846','trend'=>'+13.2% so với tháng trước','tc'=>'up','ic'=>'fa-archive','icc'=>'blue','color'=>'#3b82f6','spark'=>[28,32,30,36,34,40,36,42,40,46,44,52]],
                ['lbl'=>'Đang lấy hàng','num'=>'186','trend'=>'+7.4% so với tháng trước','tc'=>'up','ic'=>'fa-truck','icc'=>'orange','color'=>'#f97316','spark'=>[20,24,22,28,26,32,28,34,30,36,32,38]],
                ['lbl'=>'Đang giao','num'=>'2.428','trend'=>'+9.1% so với tháng trước','tc'=>'up','ic'=>'fa-truck','icc'=>'amber','color'=>'#f59e0b','spark'=>[26,30,28,34,32,38,36,42,40,46,44,50]],
                ['lbl'=>'Giao thành công','num'=>'9.812','trend'=>'+15.6% so với tháng trước','tc'=>'up','ic'=>'fa-check-circle','icc'=>'green','color'=>'#16a34a','spark'=>[24,28,30,32,36,38,42,44,48,50,54,58]],
                ['lbl'=>'Tỷ lệ đúng SLA','num'=>'95,2%','trend'=>'+2.8% so với tháng trước','tc'=>'up','ic'=>'fa-shield','icc'=>'purple','color'=>'#8b5cf6','spark'=>[60,64,62,68,70,72,68,74,76,78,76,82]],
            ];
            $sparkline = function($data, $color) {
                $w = 200; $h = 34; $max = max($data); $min = min($data); $range = $max - $min ?: 1;
                $step = $w / (count($data) - 1);
                $points = [];
                foreach ($data as $i => $v) { $x = round($i * $step, 1); $y = round($h - (($v - $min) / $range) * ($h - 4) - 2, 1); $points[] = "$x,$y"; }
                $line = implode(' ', $points);
                $area = "0,$h " . $line . " {$w},{$h}";
                $rgb = sscanf($color, "#%02x%02x%02x");
                $rgba = "rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},0.18)";
                return '<svg viewBox="0 0 '.$w.' '.$h.'" preserveAspectRatio="none" class="spark"><polygon points="'.$area.'" fill="'.$rgba.'"/><polyline points="'.$line.'" fill="none" stroke="'.$color.'" stroke-width="1.6"/></svg>';
            };
        @endphp
        @foreach ($stats as $s)
        <div class="adm-stat">
            <div class="stat-row">
                <div>
                    <div class="lbl-top">{{ $s['lbl'] }}</div>
                    <div class="num-xl">{{ $s['num'] }}</div>
                    <div class="trend {{ $s['tc'] }}"><i class="fa fa-arrow-up"></i> {{ $s['trend'] }}</div>
                </div>
                <div class="icon-sm {{ $s['icc'] }}"><i class="fa {{ $s['ic'] }}"></i></div>
            </div>
            {!! $sparkline($s['spark'], $s['color']) !!}
        </div>
        @endforeach
    </div>

    {{-- =========== Main + Aside =========== --}}
    <div class="adm-layout">
        <div>
            {{-- Toolbar --}}
            <div class="adm-toolbar">
                <div class="adm-search"><i class="fa fa-search"></i><input type="text" placeholder="Tìm kiếm mã vận đơn, đơn hàng, khách hàng, hãng vận chuyển..."></div>
                <button class="adm-btn outline"><i class="fa fa-download"></i> Nhập Excel</button>
                <button class="adm-btn outline"><i class="fa fa-upload"></i> Xuất Excel <i class="fa fa-caret-down" style="margin-left:4px"></i></button>
                <button class="adm-btn primary"><i class="fa fa-plus"></i> Tạo vận đơn</button>
            </div>

            {{-- Filter row --}}
            <div class="adm-filters" style="display:grid;grid-template-columns:repeat(6,1fr);gap:10px">
                <div><div class="adm-filter-label" style="display:block;margin-bottom:4px">Trạng thái</div><select class="adm-select" style="width:100%"><option>Tất cả</option></select></div>
                <div><div class="adm-filter-label" style="display:block;margin-bottom:4px">Hãng vận chuyển</div><select class="adm-select" style="width:100%"><option>Tất cả</option></select></div>
                <div><div class="adm-filter-label" style="display:block;margin-bottom:4px">Kho xuất</div><select class="adm-select" style="width:100%"><option>Tất cả</option></select></div>
                <div><div class="adm-filter-label" style="display:block;margin-bottom:4px">Phương thức giao</div><select class="adm-select" style="width:100%"><option>Tất cả</option></select></div>
                <div><div class="adm-filter-label" style="display:block;margin-bottom:4px">Nhân viên phụ trách</div><select class="adm-select" style="width:100%"><option>Tất cả</option></select></div>
                <div><div class="adm-filter-label" style="display:block;margin-bottom:4px">Ngày tạo</div><div style="position:relative"><input type="text" class="adm-select" style="width:100%;padding-left:12px;padding-right:30px" value="01/05/2025 - 24/05/2025"><i class="fa fa-calendar" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:12px"></i></div></div>
            </div>

            {{-- Status tabs --}}
            <div class="adm-tabs">
                <button class="adm-tab active">Tất cả</button>
                <button class="adm-tab warn"><i class="fa fa-circle"></i>Chờ lấy hàng</button>
                <button class="adm-tab warn"><i class="fa fa-truck"></i>Đang giao</button>
                <button class="adm-tab ok"><i class="fa fa-check-circle"></i>Giao thành công</button>
                <button class="adm-tab err"><i class="fa fa-times-circle"></i>Giao thất bại</button>
                <button class="adm-tab purple"><i class="fa fa-undo"></i>Hoàn hàng</button>
                <button class="adm-tab info"><i class="fa fa-money"></i>COD</button>
            </div>

            @php
                $rows = [
                    ['code'=>'VC250524-0001','c'=>'Nguyễn Văn An','cc'=>'c1','o'=>'#DH25051234','car'=>'Giao Hàng Nhanh','ci'=>'ghn','cl'=>'GHN','svc'=>'Tiết kiệm','rt'=>'Hà Nội nội thành','fee'=>'23.000đ','cod'=>'385.000đ','t'=>['24/05/2025','10:25'],'st'=>'Chờ lấy hàng','sk'=>'amber','sla'=>'100%','slk'=>'green'],
                    ['code'=>'VC250524-0002','c'=>'Trần Thị Bích Ngọc','cc'=>'c2','o'=>'#DH25051235','car'=>'GHTK','ci'=>'ghtk','cl'=>'GHTK','svc'=>'Tiêu chuẩn','rt'=>'Hà Nội → Bắc Ninh','fee'=>'28.000đ','cod'=>'652.000đ','t'=>['24/05/2025','09:48'],'st'=>'Đang giao','sk'=>'amber','sla'=>'78%','slk'=>'amber'],
                    ['code'=>'VC250524-0003','c'=>'Lê Minh Đức','cc'=>'c4','o'=>'#DH25051236','car'=>'Viettel Post','ci'=>'vtp','cl'=>'VTP','svc'=>'Nhanh','rt'=>'TP.HCM nội thành','fee'=>'20.000đ','cod'=>'499.000đ','t'=>['24/05/2025','16:30'],'st'=>'Giao thành công','sk'=>'green','sla'=>'100%','slk'=>'green'],
                    ['code'=>'VC250524-0004','c'=>'Phạm Hoàng Nam','cc'=>'c5','o'=>'#DH25051237','car'=>'J&T Express','ci'=>'jt','cl'=>'J&T','svc'=>'Tiết kiệm','rt'=>'TP.HCM → Bình Dương','fee'=>'25.000đ','cod'=>'315.000đ','t'=>['25/05/2025','15:12'],'st'=>'Giao thất bại','sk'=>'red','sla'=>'0%','slk'=>'red'],
                    ['code'=>'VC250524-0005','c'=>'Vũ Thị Mai','cc'=>'c6','o'=>'#DH25051238','car'=>'Ninja Van','ci'=>'nv','cl'=>'NV','svc'=>'Tiêu chuẩn','rt'=>'Đà Nẵng → Quảng Nam','fee'=>'30.000đ','cod'=>'718.000đ','t'=>['24/05/2025','06:57'],'st'=>'Hoàn hàng','sk'=>'amber','sla'=>'46%','slk'=>'amber'],
                    ['code'=>'VC250524-0006','c'=>'Đỗ Hoàng Long','cc'=>'c7','o'=>'#DH25051239','car'=>'Giao Hàng Nhanh','ci'=>'ghn','cl'=>'GHN','svc'=>'Tiết kiệm','rt'=>'Hà Nội → Hải Phòng','fee'=>'27.000đ','cod'=>'0đ','t'=>['24/05/2025','14:02'],'st'=>'Đang giao','sk'=>'amber','sla'=>'82%','slk'=>'amber'],
                    ['code'=>'VC250524-0007','c'=>'Nguyễn Thu Hà','cc'=>'c8','o'=>'#DH25051230','car'=>'GHTK','ci'=>'ghtk','cl'=>'GHTK','svc'=>'Nhanh','rt'=>'Cần Thơ nội thành','fee'=>'18.000đ','cod'=>'240.000đ','t'=>['23/05/2025','11:36'],'st'=>'Chờ đối soát COD','sk'=>'info','sla'=>'100%','slk'=>'green'],
                    ['code'=>'VC250524-0008','c'=>'Bùi Quang Huy','cc'=>'c3','o'=>'#DH25051241','car'=>'Viettel Post','ci'=>'vtp','cl'=>'VTP','svc'=>'Tiêu chuẩn','rt'=>'Huế → Đà Nẵng','fee'=>'24.000đ','cod'=>'560.000đ','t'=>['23/05/2025','11:30'],'st'=>'Giao thành công','sk'=>'green','sla'=>'95%','slk'=>'green'],
                ];
            @endphp

            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead><tr>
                        <th style="width:30px"><input type="checkbox"></th>
                        <th>Mã VC</th>
                        <th>Khách hàng</th>
                        <th>Đơn hàng</th>
                        <th>Hãng VC</th>
                        <th>Gói / Dịch vụ</th>
                        <th>Tuyến giao</th>
                        <th>Phí ship</th>
                        <th>Thu hộ</th>
                        <th>Cập nhật</th>
                        <th>Trạng thái</th>
                        <th>SLA</th>
                        <th>Thao tác</th>
                    </tr></thead>
                    <tbody>
                    @foreach ($rows as $r)
                        <tr>
                            <td><input type="checkbox"></td>
                            <td class="id">{{ $r['code'] }}</td>
                            <td><div class="adm-cust"><div class="av {{ $r['cc'] }}">{{ mb_substr($r['c'],0,1) }}</div><div class="nm">{{ $r['c'] }}</div></div></td>
                            <td><a href="#" class="dd-link">{{ $r['o'] }}</a></td>
                            <td><span class="dd-carrier"><span class="lg {{ $r['ci'] }}">{{ $r['cl'] }}</span>{{ $r['car'] }}</span></td>
                            <td style="font-size:12px">{{ $r['svc'] }}</td>
                            <td style="font-size:11.5px;color:#475569">{{ $r['rt'] }}</td>
                            <td style="font-size:12px">{{ $r['fee'] }}</td>
                            <td style="font-size:12px;font-weight:600">{{ $r['cod'] }}</td>
                            <td style="font-size:11px;color:#64748b">{{ $r['t'][0] }}<br>{{ $r['t'][1] }}</td>
                            <td><span class="adm-badge {{ $r['sk'] }}">{{ $r['st'] }}</span></td>
                            <td style="font-weight:700;font-size:12px;color:{{ $r['slk']==='green'?'#16a34a':($r['slk']==='amber'?'#d97706':'#dc2626') }}">{{ $r['sla'] }}</td>
                            <td><div class="adm-actions"><a href="#"><i class="fa fa-eye"></i></a><a href="#"><i class="fa fa-pencil"></i></a><button><i class="fa fa-ellipsis-v"></i></button></div></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="adm-pagination">
                    <span class="info">Hiển thị 1 đến 8 của 12.846 vận đơn</span>
                    <div class="pages"><a href="#"><i class="fa fa-angle-left"></i></a><span class="active">1</span><a href="#">2</a><a href="#">3</a><a href="#">4</a><a href="#">5</a><span class="dots">...</span><a href="#">1.285</a><a href="#"><i class="fa fa-angle-right"></i></a></div>
                    <select class="adm-select" style="width:auto"><option>10 / trang</option></select>
                </div>
            </div>

            {{-- Bottom 4 cards --}}
            <div class="adm-bottom-grid cols-4">
                <div class="adm-bottom-card">
                    <h4>Vận đơn mới nhất <a href="#">Xem tất cả →</a></h4>
                    <ul class="dd-latest">
                        @foreach([
                            ['n'=>'VC250524-0008','c'=>'c1','rt'=>'Viettel Post · Huế → Đà Nẵng','t'=>['23/05/2025','11:30']],
                            ['n'=>'VC250524-0007','c'=>'c2','rt'=>'GHTK · Cần Thơ nội thành','t'=>['23/05/2025','11:28']],
                            ['n'=>'VC250524-0006','c'=>'c4','rt'=>'GHN · Hà Nội → Hải Phòng','t'=>['23/05/2025','11:15']],
                            ['n'=>'VC250524-0005','c'=>'c5','rt'=>'Ninja Van · Đà Nẵng → Quảng Nam','t'=>['23/05/2025','11:02']],
                            ['n'=>'VC250524-0004','c'=>'c3','rt'=>'J&T Express · TP.HCM → Bình Dương','t'=>['23/05/2025','10:45']],
                        ] as $u)
                        <li>
                            <div class="av {{ $u['c'] }}">{{ mb_substr($u['n'],3,1) }}</div>
                            <div class="body"><strong style="color:#0ea5e9">{{ $u['n'] }}</strong><small>{{ $u['rt'] }}</small></div>
                            <div class="ts">{{ $u['t'][0] }}<br>{{ $u['t'][1] }}</div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card">
                    <h4>Lịch sử xử lý <a href="#">Xem tất cả →</a></h4>
                    <ul class="dd-timeline">
                        @foreach([
                            ['cls'=>'green','n'=>'Đơn vận VC250524-0003','meta'=>'giao thành công','sub'=>'Nhân viên Lê Minh Đức','t'=>'24/05/2025 16:40'],
                            ['cls'=>'red','n'=>'Đã giao lần 2 thất bại','meta'=>'· Khách vắng','sub'=>'Nhân viên Phạm Hoàng Nam','t'=>'24/05/2025 14:22'],
                            ['cls'=>'blue','n'=>'Cập nhật trạng thái','meta'=>'Đang giao','sub'=>'Nhân viên Vũ Thị Mai','t'=>'24/05/2025 08:57'],
                            ['cls'=>'green','n'=>'Lấy hàng thành công tại kho HN01','meta'=>'','sub'=>'Nhân viên Nguyễn Văn An','t'=>'24/05/2025 08:15'],
                            ['cls'=>'amber','n'=>'Tạo vận đơn mới VC250524-0001','meta'=>'','sub'=>'Nhân viên Trần Thị Bích Ngọc','t'=>'24/05/2025 07:55'],
                        ] as $h)
                        <li class="{{ $h['cls'] }}"><span class="pt"></span><strong>{{ $h['n'] }}</strong><span class="meta">{{ $h['meta'] }}</span><span class="ts">{{ $h['sub'] }} · {{ $h['t'] }}</span></li>
                        @endforeach
                    </ul>
                </div>
                <div class="adm-bottom-card">
                    <h4>Cảnh báo cần xử lý <a href="#">Xem tất cả →</a></h4>
                    <ul class="dd-tips">
                        <li><div class="ic err"><i class="fa fa-exclamation-circle"></i></div><div class="body"><strong style="color:#dc2626">28 vận đơn thất bại cần xử lý</strong><small>Liên hệ khách hoặc giao lại</small></div></li>
                        <li><div class="ic warn"><i class="fa fa-clock-o"></i></div><div class="body"><strong style="color:#d97706">36 vận đơn quá hạn lấy hàng</strong><small>Quá 24h chưa được lấy</small></div></li>
                        <li><div class="ic info"><i class="fa fa-money"></i></div><div class="body"><strong style="color:#2563eb">42 COD chưa đối soát</strong><small>Tổng tiền thu hộ 18.960.000đ</small></div></li>
                        <li><div class="ic purple"><i class="fa fa-truck"></i></div><div class="body"><strong style="color:#7c3aed">16 vận đơn giao chậm SLA</strong><small>Tỷ lệ đúng SLA dưới 90%</small></div></li>
                        <li><div class="ic warn"><i class="fa fa-undo"></i></div><div class="body"><strong style="color:#d97706">12 đơn hoàn hàng chờ xử lý</strong><small>Chưa nhập về kho hoàn</small></div></li>
                    </ul>
                </div>
                <div class="adm-bottom-card">
                    <h4>Gợi ý tối ưu <a href="#">Xem tất cả →</a></h4>
                    <ul class="dd-tips">
                        <li><div class="ic warn"><i class="fa fa-phone"></i></div><div class="body"><strong>Ưu tiên gọi xác nhận</strong><small>Giảm 15% giao thất bại</small></div></li>
                        <li><div class="ic info"><i class="fa fa-route"></i></div><div class="body"><strong>Gom tuyến giao</strong><small>Tiết kiệm 8-12% chi phí ship</small></div></li>
                        <li><div class="ic ok"><i class="fa fa-money"></i></div><div class="body"><strong>Đồng bộ COD</strong><small>Rút ngắn 20% thời gian đối soát</small></div></li>
                        <li><div class="ic err"><i class="fa fa-bell"></i></div><div class="body"><strong>Cảnh báo giao chậm</strong><small>Thiết lập ngưỡng &amp; thông báo sớm</small></div></li>
                        <li><div class="ic purple"><i class="fa fa-cog"></i></div><div class="body"><strong>Tối ưu chọn hãng VC</strong><small>Gợi ý hãng phù hợp theo tuyến</small></div></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- =========== Right aside =========== --}}
        <aside class="adm-aside">
            <div class="adm-aside-card">
                <h4>Phân hạng vận chuyển <a href="#">Xem tất cả</a></h4>
                @foreach([
                    ['l'=>'Giao Hàng Nhanh','c'=>'5.248','p'=>'40,8%','w'=>41,'cl'=>'teal'],
                    ['l'=>'GHTK','c'=>'3.142','p'=>'24,4%','w'=>24,'cl'=>'green'],
                    ['l'=>'Viettel Post','c'=>'2.156','p'=>'16,8%','w'=>17,'cl'=>'red'],
                    ['l'=>'J&T Express','c'=>'1.468','p'=>'11,4%','w'=>11,'cl'=>'amber'],
                    ['l'=>'Ninja Van','c'=>'612','p'=>'4,8%','w'=>5,'cl'=>'blue'],
                    ['l'=>'Khác','c'=>'220','p'=>'1,8%','w'=>2,'cl'=>'gray'],
                ] as $g)
                <div style="margin-bottom:10px">
                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;font-size:12px">
                        <span style="font-weight:500;color:#475569">{{ $g['l'] }}</span>
                        <span style="margin-left:auto;color:#0f172a;font-weight:700;font-size:11.5px">{{ $g['c'] }} ({{ $g['p'] }})</span>
                    </div>
                    <div class="adm-progress"><div class="bar {{ $g['cl'] }}" style="width:{{ $g['w'] }}%"></div></div>
                </div>
                @endforeach
            </div>

            <div class="adm-aside-card">
                <h4>Top tuyến giao nhiều <a href="#">Xem tất cả</a></h4>
                <ol class="adm-top-list">
                    @foreach([
                        ['n'=>'Hà Nội nội thành','v'=>'2.986 (23,2%)'],
                        ['n'=>'TP.HCM nội thành','v'=>'2.214 (17,2%)'],
                        ['n'=>'Hà Nội → Bắc Ninh','v'=>'1.742 (13,5%)'],
                        ['n'=>'TP.HCM → Bình Dương','v'=>'1.368 (10,6%)'],
                        ['n'=>'Liên tỉnh miền Trung','v'=>'1.102 (8,6%)'],
                    ] as $i => $t)
                    <li><span class="rank">{{ $i+1 }}</span><span class="name">{{ $t['n'] }}</span><span class="val" style="font-size:11px;color:#64748b;font-weight:500">{{ $t['v'] }}</span></li>
                    @endforeach
                </ol>
            </div>

            <div class="adm-aside-card">
                <h4>Tình trạng vận chuyển</h4>
                @php
                    $segs = [
                        ['v'=>1.4,'c'=>'#f97316','l'=>'Chờ lấy hàng','n'=>'186 (1,4%)'],
                        ['v'=>18.9,'c'=>'#f59e0b','l'=>'Đang giao','n'=>'2.428 (18,9%)'],
                        ['v'=>76.4,'c'=>'#16a34a','l'=>'Giao thành công','n'=>'9.812 (76,4%)'],
                        ['v'=>1.6,'c'=>'#dc2626','l'=>'Giao thất bại','n'=>'208 (1,6%)'],
                        ['v'=>1.7,'c'=>'#8b5cf6','l'=>'Hoàn hàng','n'=>'212 (1,7%)'],
                    ];
                    $r = 38; $cir = 2 * 3.14159 * $r; $offset = 0;
                @endphp
                <div class="dd-donut">
                    <svg viewBox="0 0 100 100" width="110" height="110">
                        <circle cx="50" cy="50" r="{{ $r }}" fill="none" stroke="#f1f5f9" stroke-width="14"/>
                        @foreach ($segs as $sg)
                            @php $len = $cir * $sg['v'] / 100; $dash = $len.' '.($cir-$len); @endphp
                            <circle cx="50" cy="50" r="{{ $r }}" fill="none" stroke="{{ $sg['c'] }}" stroke-width="14" stroke-dasharray="{{ $dash }}" stroke-dashoffset="{{ -$offset }}" transform="rotate(-90 50 50)"/>
                            @php $offset += $len; @endphp
                        @endforeach
                    </svg>
                    <div class="legend">
                        <ul>
                        @foreach ($segs as $sg)
                            <li><span class="sw" style="background:{{ $sg['c'] }}"></span>{{ $sg['l'] }}<span class="v">{{ $sg['n'] }}</span></li>
                        @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="adm-aside-card">
                <h4>Xu hướng vận chuyển <select class="adm-select adm-select-mini"><option>6 tháng gần đây</option></select></h4>
                <div class="dd-trend">
                    <div class="dd-trend-head">
                        <div class="legend"><span><span class="sw"></span>Số vận đơn</span><span><span class="sw g"></span>Tỷ lệ đúng SLA</span></div>
                    </div>
                    @php
                        $months = ['12/2024','01/2025','02/2025','03/2025','04/2025','05/2025'];
                        $a = [3000, 3800, 4400, 5200, 6400, 7800];
                        $b = [70, 76, 82, 86, 90, 94];
                        $w = 280; $h = 130; $stepX = $w/(count($months)-1);
                        $maxA = max($a); $maxB = 100;
                        $pa = []; $pb = [];
                        foreach ($a as $i => $v) { $x = round($i*$stepX,1); $y = round($h - ($v/$maxA)*($h-30) - 10,1); $pa[] = "$x,$y"; }
                        foreach ($b as $i => $v) { $x = round($i*$stepX,1); $y = round($h - ($v/$maxB)*($h-30) - 10,1); $pb[] = "$x,$y"; }
                    @endphp
                    <svg viewBox="0 0 {{ $w }} {{ $h }}" preserveAspectRatio="none" class="dd-trend-svg">
                        @for ($i = 1; $i < 5; $i++)
                            @php $gy = round($h * $i / 5,1) @endphp
                            <line x1="0" y1="{{ $gy }}" x2="{{ $w }}" y2="{{ $gy }}" stroke="#f1f5f9" stroke-width="1"/>
                        @endfor
                        <polyline points="{{ implode(' ', $pa) }}" fill="none" stroke="#3b82f6" stroke-width="2"/>
                        <polyline points="{{ implode(' ', $pb) }}" fill="none" stroke="#16a34a" stroke-width="2"/>
                        @foreach ($pa as $pt) @php [$x,$y] = explode(',', $pt) @endphp <circle cx="{{ $x }}" cy="{{ $y }}" r="3" fill="#3b82f6"/> @endforeach
                        @foreach ($pb as $pt) @php [$x,$y] = explode(',', $pt) @endphp <circle cx="{{ $x }}" cy="{{ $y }}" r="3" fill="#16a34a"/> @endforeach
                    </svg>
                    <div style="display:flex;justify-content:space-between;font-size:9.5px;color:#94a3b8;margin-top:4px">
                        @foreach ($months as $m) <span>{{ $m }}</span> @endforeach
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
