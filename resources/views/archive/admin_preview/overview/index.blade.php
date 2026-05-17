@extends('admin_layout')
@section('admin_content')
<div class="adm-page">
    <div class="adm-page-title">
        <h1><i class="fa fa-tachometer" style="color:var(--adm-teal);margin-right:8px"></i>Tổng quan Admin</h1>
        <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › Tổng quan</div>
    </div>

    {{-- ===== 5 STAT CARDS ===== --}}
    <div class="adm-stats">
        <div class="adm-stat">
            <div class="icon blue"><i class="fa fa-money"></i></div>
            <div>
                <div class="num">1,256,890,000đ</div>
                <div class="lbl">Doanh thu tháng này</div>
                <div class="trend up"><i class="fa fa-arrow-up"></i> 12.5% so với tháng trước</div>
            </div>
        </div>
        <div class="adm-stat">
            <div class="icon teal"><i class="fa fa-shopping-cart"></i></div>
            <div>
                <div class="num">1,847</div>
                <div class="lbl">Đơn hàng</div>
                <div class="trend up"><i class="fa fa-arrow-up"></i> 8.3% so với tháng trước</div>
            </div>
        </div>
        <div class="adm-stat">
            <div class="icon green"><i class="fa fa-users"></i></div>
            <div>
                <div class="num">12,456</div>
                <div class="lbl">Khách hàng</div>
                <div class="trend up"><i class="fa fa-arrow-up"></i> 5.1% so với tháng trước</div>
            </div>
        </div>
        <div class="adm-stat">
            <div class="icon amber"><i class="fa fa-cube"></i></div>
            <div>
                <div class="num">3,245</div>
                <div class="lbl">Sản phẩm</div>
                <div style="font-size:10px;color:var(--adm-muted)">2,890 đang hoạt động</div>
            </div>
        </div>
        <div class="adm-stat">
            <div class="icon red"><i class="fa fa-star"></i></div>
            <div>
                <div class="num">4.8/5</div>
                <div class="lbl">Đánh giá TB</div>
                <div class="trend up"><i class="fa fa-arrow-up"></i> 0.2 so với tháng trước</div>
            </div>
        </div>
    </div>

    {{-- ===== MAIN LAYOUT ===== --}}
    <div class="adm-layout">
        <div>
            {{-- Revenue Chart --}}
            <div class="adm-card" style="padding:24px;margin-bottom:20px">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                    <div>
                        <h3 style="font-size:16px;font-weight:700;color:var(--adm-dark);margin:0 0 4px">Biểu đồ doanh thu</h3>
                        <span style="font-size:12px;color:var(--adm-muted)">Tổng doanh thu 12 tháng gần nhất</span>
                    </div>
                    <div style="display:flex;gap:6px">
                        <button class="adm-tab active" style="padding:6px 14px;font-size:11px">7 ngày</button>
                        <button class="adm-tab" style="padding:6px 14px;font-size:11px">30 ngày</button>
                        <button class="adm-tab" style="padding:6px 14px;font-size:11px">12 tháng</button>
                    </div>
                </div>
                <div style="display:flex;gap:4px;margin-bottom:8px">
                    @foreach(['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'] as $m)
                    <div style="flex:1;text-align:center;font-size:10px;color:var(--adm-muted)">{{ $m }}</div>
                    @endforeach
                </div>
                <div style="height:220px;background:#f8fafc;border-radius:10px;display:flex;align-items:end;padding:16px 10px 0;gap:6px">
                    @foreach([65,45,78,55,89,72,95,60,85,70,92,88] as $h)
                    <div style="flex:1;background:linear-gradient(180deg,var(--adm-teal) 0%,rgba(13,148,136,.35) 100%);height:{{ $h }}%;border-radius:6px 6px 0 0;min-width:0;transition:all .4s ease;position:relative;cursor:pointer" onmouseenter="this.style.opacity='0.8'" onmouseleave="this.style.opacity='1'">
                        <div style="position:absolute;top:-18px;left:50%;transform:translateX(-50%);font-size:9px;color:var(--adm-muted);white-space:nowrap;opacity:0;transition:opacity .2s" class="bar-label">{{ number_format($h * 14230000, 0, ',', '.') }}đ</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Recent Orders --}}
            <div class="adm-card" style="padding:0;margin-bottom:20px">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 24px 16px">
                    <div>
                        <h3 style="font-size:15px;font-weight:700;color:var(--adm-dark);margin:0 0 2px">Đơn hàng gần đây</h3>
                        <span style="font-size:11px;color:var(--adm-muted)">5 đơn mới nhất</span>
                    </div>
                    <a href="{{ url('/admin/orders-management') }}" style="font-size:12px;color:var(--adm-teal);text-decoration:none;font-weight:600;display:flex;align-items:center;gap:4px">Xem tất cả <i class="fa fa-angle-right"></i></a>
                </div>
                <table class="adm-table" style="margin:0">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Sản phẩm</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach([
                        ['c'=>'DD250524001','n'=>'Nguyễn Văn An','p'=>'ASUS TUF Gaming A15','t'=>'26.990.000đ','s'=>'Đang giao','sc'=>'blue'],
                        ['c'=>'DD250524002','n'=>'Trần Thị Bích Ngọc','p'=>'Dell XPS 13 Plus','t'=>'34.990.000đ','s'=>'Chờ xác nhận','sc'=>'amber'],
                        ['c'=>'DD250523003','n'=>'Lê Minh Đức','p'=>'MacBook Air M2','t'=>'28.990.000đ','s'=>'Hoàn thành','sc'=>'green'],
                        ['c'=>'DD250522004','n'=>'Phạm Hoàng Nam','p'=>'HP Spectre x360','t'=>'29.790.000đ','s'=>'Hoàn thành','sc'=>'green'],
                        ['c'=>'DD250521005','n'=>'Vũ Thị Mai','p'=>'Lenovo ThinkPad X1','t'=>'39.990.000đ','s'=>'Đã hủy','sc'=>'red'],
                    ] as $o)
                    <tr>
                        <td class="id" style="font-size:11px;font-weight:600;color:var(--adm-teal)">{{ $o['c'] }}</td>
                        <td>
                            <div class="adm-user-cell">
                                <div class="u-avatar">{{ mb_substr($o['n'],0,1) }}</div>
                                <span class="u-name">{{ $o['n'] }}</span>
                            </div>
                        </td>
                        <td style="font-size:12px">{{ $o['p'] }}</td>
                        <td style="font-size:12px;font-weight:700;color:var(--adm-dark)">{{ $o['t'] }}</td>
                        <td><span class="adm-badge {{ $o['sc'] }}">{{ $o['s'] }}</span></td>
                        <td>
                            <div class="adm-actions">
                                <a href="#" title="Xem chi tiết"><i class="fa fa-eye"></i></a>
                                <a href="#" title="In hóa đơn"><i class="fa fa-print"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Bottom Grid: 4 insight cards --}}
            <div class="adm-bottom-grid four-col">
                {{-- Hoạt động gần đây --}}
                <div class="adm-bottom-card">
                    <h4><i class="fa fa-clock-o" style="color:var(--adm-teal);margin-right:4px"></i> Hoạt động gần đây</h4>
                    @foreach([
                        ['a'=>'Đơn hàng #DD250524001 đã tạo','d'=>'5 phút trước','i'=>'fa-shopping-cart','c'=>'teal'],
                        ['a'=>'SP "ASUS TUF Gaming" cập nhật giá','d'=>'15 phút trước','i'=>'fa-tag','c'=>'blue'],
                        ['a'=>'Khách hàng mới: Vũ Thị Mai','d'=>'1 giờ trước','i'=>'fa-user-plus','c'=>'green'],
                        ['a'=>'Banner "Flash Sale" đã kích hoạt','d'=>'2 giờ trước','i'=>'fa-image','c'=>'amber'],
                    ] as $a)
                    <div class="adm-alert-item">
                        <div class="alert-icon {{ $a['c'] }}"><i class="fa {{ $a['i'] }}" style="font-size:10px"></i></div>
                        <div class="alert-info">
                            <div class="alert-title">{{ $a['a'] }}</div>
                        </div>
                        <div style="font-size:10px;color:var(--adm-muted);white-space:nowrap">{{ $a['d'] }}</div>
                    </div>
                    @endforeach
                </div>

                {{-- Sản phẩm bán chạy --}}
                <div class="adm-bottom-card">
                    <h4><i class="fa fa-fire" style="color:#ef4444;margin-right:4px"></i> Sản phẩm bán chạy</h4>
                    <ol class="adm-top-list">
                    @foreach([
                        ['n'=>'ASUS TUF Gaming A15','v'=>'156 đơn'],
                        ['n'=>'Dell XPS 13 Plus','v'=>'128 đơn'],
                        ['n'=>'MacBook Air M2','v'=>'112 đơn'],
                        ['n'=>'HP Spectre x360','v'=>'98 đơn'],
                        ['n'=>'Lenovo ThinkPad X1','v'=>'87 đơn'],
                    ] as $i=>$t)
                    <li>
                        <span class="rank">{{ $i+1 }}</span>
                        <span class="name">{{ $t['n'] }}</span>
                        <span class="val">{{ $t['v'] }}</span>
                    </li>
                    @endforeach
                    </ol>
                </div>

                {{-- Cảnh báo hệ thống --}}
                <div class="adm-bottom-card">
                    <h4><i class="fa fa-exclamation-circle" style="color:var(--adm-red);margin-right:4px"></i> Cảnh báo hệ thống</h4>
                    <div class="adm-alert-item">
                        <div class="alert-icon red"><i class="fa fa-exclamation-triangle"></i></div>
                        <div class="alert-info">
                            <div class="alert-title">12 sản phẩm sắp hết hàng</div>
                            <div class="alert-desc">Cần nhập thêm hàng</div>
                        </div>
                    </div>
                    <div class="adm-alert-item">
                        <div class="alert-icon amber"><i class="fa fa-clock-o"></i></div>
                        <div class="alert-info">
                            <div class="alert-title">5 đơn chờ xác nhận > 24h</div>
                            <div class="alert-desc">Kiểm tra ngay</div>
                        </div>
                    </div>
                    <div class="adm-alert-item">
                        <div class="alert-icon amber"><i class="fa fa-star-half-o"></i></div>
                        <div class="alert-info">
                            <div class="alert-title">3 đánh giá 1 sao mới</div>
                            <div class="alert-desc">Cần phản hồi</div>
                        </div>
                    </div>
                </div>

                {{-- Gợi ý tối ưu --}}
                <div class="adm-bottom-card">
                    <h4><i class="fa fa-lightbulb-o" style="color:var(--adm-amber);margin-right:4px"></i> Gợi ý tối ưu</h4>
                    <div class="adm-suggest-item">
                        <div class="s-icon blue"><i class="fa fa-bolt"></i></div>
                        <div class="s-info">
                            <div class="s-title">Tạo Flash Sale cho 5 SP tồn kho lâu</div>
                            <div class="s-desc">Giải phóng hàng tồn, tăng doanh thu.</div>
                        </div>
                    </div>
                    <div class="adm-suggest-item">
                        <div class="s-icon teal"><i class="fa fa-envelope"></i></div>
                        <div class="s-info">
                            <div class="s-title">Gửi email cho 1.200 KH inactive</div>
                            <div class="s-desc">Kích hoạt lại khách hàng cũ.</div>
                        </div>
                    </div>
                    <div class="adm-suggest-item">
                        <div class="s-icon purple"><i class="fa fa-image"></i></div>
                        <div class="s-info">
                            <div class="s-title">Cập nhật banner trang chủ</div>
                            <div class="s-desc">Banner hiện tại đã hết hạn.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== RIGHT SIDEBAR ===== --}}
        <aside class="adm-aside">
            {{-- Order Status Donut --}}
            <div class="adm-aside-card">
                <h4>Tình trạng đơn hàng</h4>
                <div class="adm-donut-wrap">
                    <div class="adm-donut" style="background:conic-gradient(var(--adm-green) 0% 60%, var(--adm-blue) 60% 78%, var(--adm-amber) 78% 90%, var(--adm-red) 90% 100%);width:130px;height:130px">
                        <div class="adm-donut-center" style="width:86px;height:86px;border-radius:50%;background:#fff">
                            <div class="pct" style="font-size:18px;font-weight:800">1,847</div>
                            <div class="lbl">Tổng đơn</div>
                        </div>
                    </div>
                    <ul class="adm-donut-legend">
                        <li><span class="dot" style="background:var(--adm-green)"></span> Hoàn thành <span class="pct">1.108 (60%)</span></li>
                        <li><span class="dot" style="background:var(--adm-blue)"></span> Đang giao <span class="pct">332 (18%)</span></li>
                        <li><span class="dot" style="background:var(--adm-amber)"></span> Chờ xử lý <span class="pct">222 (12%)</span></li>
                        <li><span class="dot" style="background:var(--adm-red)"></span> Đã hủy <span class="pct">185 (10%)</span></li>
                    </ul>
                </div>
            </div>

            {{-- Top Categories --}}
            <div class="adm-aside-card">
                <h4>Top danh mục</h4>
                @foreach([
                    ['l'=>'Laptop','c'=>'45.2%','w'=>'45%','color'=>'teal'],
                    ['l'=>'PC','c'=>'22.8%','w'=>'23%','color'=>'blue'],
                    ['l'=>'Phụ kiện','c'=>'18.5%','w'=>'19%','color'=>'green'],
                    ['l'=>'Màn hình','c'=>'13.5%','w'=>'14%','color'=>'amber'],
                ] as $g)
                <div class="adm-progress-row">
                    <span class="p-label" style="width:60px;text-align:left;font-size:11px">{{ $g['l'] }}</span>
                    <div class="adm-progress"><div class="bar {{ $g['color'] }}" style="width:{{ $g['w'] }}"></div></div>
                    <span class="p-count" style="font-size:10px">{{ $g['c'] }}</span>
                </div>
                @endforeach
            </div>

            {{-- Weekly Revenue --}}
            <div class="adm-aside-card">
                <h4>Doanh thu tuần
                    <select class="adm-select" style="height:24px;font-size:10px;padding:0 20px 0 6px;float:right">
                        <option>Tuần này</option>
                        <option>Tuần trước</option>
                    </select>
                </h4>
                <div style="display:flex;gap:3px;margin-bottom:6px">
                    @foreach(['T2','T3','T4','T5','T6','T7','CN'] as $d)
                    <div style="flex:1;text-align:center;font-size:9px;color:var(--adm-muted)">{{ $d }}</div>
                    @endforeach
                </div>
                <div class="adm-trend-chart">
                    @foreach([72,58,85,65,92,78,45] as $h)
                    <div class="adm-trend-bar primary" style="height:{{ $h }}%"></div>
                    @endforeach
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="adm-aside-card">
                <h4>Thao tác nhanh</h4>
                <div style="display:flex;flex-direction:column;gap:8px">
                    <a href="{{ url('/add-product') }}" class="adm-btn primary" style="width:100%;justify-content:center;font-size:12px"><i class="fa fa-plus"></i> Thêm sản phẩm</a>
                    <a href="{{ url('/admin/orders-management') }}" class="adm-btn outline" style="width:100%;justify-content:center;font-size:12px"><i class="fa fa-list"></i> Xem đơn hàng</a>
                    <a href="#" class="adm-btn outline" style="width:100%;justify-content:center;font-size:12px"><i class="fa fa-download"></i> Xuất báo cáo</a>
                </div>
            </div>
        </aside>
    </div>
</div>
<style>
.adm-badge.purple{background:rgba(139,92,246,.12);color:#8b5cf6}
.adm-card:hover{box-shadow:0 4px 12px rgba(0,0,0,.06)}
</style>
@endsection
