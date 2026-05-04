@extends('admin_layout')
@section('admin_content')
<link rel="stylesheet" href="{{ asset('backend/css/admin-dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/admin-pages.css') }}">
@php
$tabs = ['general'=>'Thông tin chung','appearance'=>'Giao diện','seo'=>'SEO & Meta','email'=>'Email & Thông báo','payment'=>'Thanh toán','shipping'=>'Vận chuyển','security'=>'Bảo mật'];
$socials = [
    ['name'=>'Facebook','url'=>'https://facebook.com/dangduc','icon'=>'fa-facebook','on'=>true],
    ['name'=>'Instagram','url'=>'https://instagram.com/dangduc','icon'=>'fa-instagram','on'=>true],
    ['name'=>'YouTube','url'=>'https://youtube.com/@dangduc','icon'=>'fa-youtube-play','on'=>true],
    ['name'=>'TikTok','url'=>'https://tiktok.com/@dangduc','icon'=>'fa-music','on'=>false],
    ['name'=>'Zalo OA','url'=>'4012345678','icon'=>'fa-commenting','on'=>true],
];
@endphp
<div class="adm-page">
    <div class="adm-page-title"><h1><i class="fa fa-cog" style="color:var(--adm-teal);margin-right:6px"></i>Cấu hình website</h1><div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › Cấu hình › Cấu hình website</div></div>

    {{-- Tabs --}}
    <div class="adm-tabs" style="margin-bottom:20px">
        @foreach($tabs as $k=>$v)
        <button class="adm-tab {{ $loop->first?'active':'' }}">{{ $v }}</button>
        @endforeach
    </div>

    <div class="adm-layout">
        <div>
            {{-- Store Information --}}
            <div class="adm-card" style="padding:24px;margin-bottom:20px">
                <h3 style="font-size:15px;font-weight:700;color:var(--adm-dark);margin-bottom:20px"><i class="fa fa-building" style="color:var(--adm-teal);margin-right:6px"></i>Thông tin cửa hàng</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <label class="adm-form-group"><span class="adm-label">Tên website</span><input type="text" class="adm-input" value="DangDuc - Laptop & PC Workstation"></label>
                    <label class="adm-form-group"><span class="adm-label">Slogan</span><input type="text" class="adm-input" value="Nền tảng công nghệ đáng tin cậy"></label>
                    <label class="adm-form-group"><span class="adm-label">Email liên hệ</span><input type="email" class="adm-input" value="contact@dangduc.com"></label>
                    <label class="adm-form-group"><span class="adm-label">Số điện thoại</span><input type="text" class="adm-input" value="1900 1234"></label>
                    <label class="adm-form-group"><span class="adm-label">Hotline hỗ trợ</span><input type="text" class="adm-input" value="0901 234 567"></label>
                    <label class="adm-form-group"><span class="adm-label">Mã số thuế</span><input type="text" class="adm-input" value="0123456789"></label>
                    <label class="adm-form-group" style="grid-column:span 2"><span class="adm-label">Địa chỉ</span><input type="text" class="adm-input" value="123 Nguyễn Trãi, Thanh Xuân, Hà Nội"></label>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px">
                    <div class="adm-form-group">
                        <span class="adm-label">Logo website</span>
                        <div style="border:2px dashed #e2e8f0;border-radius:8px;padding:20px;text-align:center;background:#f8fafc">
                            <div style="width:120px;height:40px;background:#0d9488;border-radius:6px;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px">D dangduc</div>
                            <button class="adm-btn outline" style="font-size:11px"><i class="fa fa-upload"></i> Thay đổi logo</button>
                        </div>
                    </div>
                    <div class="adm-form-group">
                        <span class="adm-label">Favicon</span>
                        <div style="border:2px dashed #e2e8f0;border-radius:8px;padding:20px;text-align:center;background:#f8fafc">
                            <div style="width:32px;height:32px;background:#0d9488;border-radius:6px;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:16px">D</div>
                            <button class="adm-btn outline" style="font-size:11px"><i class="fa fa-upload"></i> Thay đổi favicon</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Social Media --}}
            <div class="adm-card" style="padding:24px;margin-bottom:20px">
                <h3 style="font-size:15px;font-weight:700;color:var(--adm-dark);margin-bottom:20px"><i class="fa fa-share-alt" style="color:var(--adm-teal);margin-right:6px"></i>Mạng xã hội</h3>
                @foreach($socials as $s)
                <div style="display:flex;align-items:center;gap:12px;padding:12px 0;{{ !$loop->last?'border-bottom:1px solid #f1f5f9;':'' }}">
                    <div style="width:36px;height:36px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:16px"><i class="fa {{ $s['icon'] }}"></i></div>
                    <div style="flex:1">
                        <div style="font-size:13px;font-weight:600;color:var(--adm-dark)">{{ $s['name'] }}</div>
                        <input type="text" class="adm-input" value="{{ $s['url'] }}" style="margin-top:4px;height:32px;font-size:12px">
                    </div>
                    <label class="adm-toggle"><input type="checkbox" {{ $s['on']?'checked':'' }}><span></span></label>
                </div>
                @endforeach
            </div>

            {{-- Action buttons --}}
            <div style="display:flex;gap:12px;margin-bottom:24px">
                <button class="adm-btn primary" style="padding:10px 28px"><i class="fa fa-save"></i> Lưu cấu hình</button>
                <button class="adm-btn outline"><i class="fa fa-undo"></i> Khôi phục mặc định</button>
            </div>
        </div>

        <aside class="adm-aside">
            {{-- Website Status --}}
            <div class="adm-aside-card">
                <h4>Trạng thái website</h4>
                <div style="display:flex;flex-direction:column;gap:12px;margin-top:12px">
                    <div style="display:flex;justify-content:space-between;align-items:center"><span style="font-size:12px;color:var(--adm-muted)">Website</span><span style="font-size:12px;color:var(--adm-green);font-weight:600"><i class="fa fa-circle" style="font-size:8px"></i> Đang hoạt động</span></div>
                    <div style="display:flex;justify-content:space-between;align-items:center"><span style="font-size:12px;color:var(--adm-muted)">Chế độ bảo trì</span><span style="font-size:12px;color:var(--adm-red)">Tắt</span></div>
                    <div style="display:flex;justify-content:space-between;align-items:center"><span style="font-size:12px;color:var(--adm-muted)">SSL</span><span style="font-size:12px;color:var(--adm-green);font-weight:600"><i class="fa fa-lock"></i> Đang kích hoạt</span></div>
                    <div style="display:flex;justify-content:space-between;align-items:center"><span style="font-size:12px;color:var(--adm-muted)">Phiên bản</span><span style="font-size:12px;font-weight:600">v2.5.1</span></div>
                </div>
            </div>
            {{-- Backup --}}
            <div class="adm-aside-card">
                <h4>Sao lưu & Khôi phục</h4>
                <div style="margin-top:12px">
                    <div style="font-size:12px;color:var(--adm-muted);margin-bottom:8px">Sao lưu gần nhất: <strong style="color:var(--adm-dark)">24/05/2025 10:28</strong></div>
                    <div style="display:flex;gap:8px">
                        <button class="adm-btn primary" style="flex:1;font-size:11px"><i class="fa fa-download"></i> Sao lưu ngay</button>
                        <button class="adm-btn outline" style="flex:1;font-size:11px"><i class="fa fa-upload"></i> Khôi phục</button>
                    </div>
                </div>
            </div>
            {{-- Cache --}}
            <div class="adm-aside-card">
                <h4>Bộ nhớ đệm</h4>
                <div style="margin-top:12px">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px"><span style="font-size:12px;color:var(--adm-muted)">Trạng thái cache</span><span style="font-size:12px;color:var(--adm-green);font-weight:600">Hoạt động</span></div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px"><span style="font-size:12px;color:var(--adm-muted)">Dung lượng</span><span style="font-size:12px;font-weight:600">128 MB</span></div>
                    <button class="adm-btn outline" style="width:100%;font-size:11px"><i class="fa fa-trash"></i> Xóa cache</button>
                </div>
            </div>
        </aside>
    </div>
</div>
<style>
.adm-form-group{display:flex;flex-direction:column;gap:4px}
.adm-label{font-size:12px;font-weight:600;color:var(--adm-muted)}
.adm-input{height:38px;border:1px solid #e2e8f0;border-radius:6px;padding:0 12px;font-size:13px;color:var(--adm-dark);background:#fff;transition:border .2s}
.adm-input:focus{outline:none;border-color:var(--adm-teal);box-shadow:0 0 0 3px rgba(13,148,136,.1)}
.adm-card{background:#fff;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06)}
.adm-toggle{position:relative;width:40px;height:22px;display:inline-block}
.adm-toggle input{opacity:0;width:0;height:0}
.adm-toggle span{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:#cbd5e1;border-radius:11px;transition:.3s}
.adm-toggle span::before{content:'';position:absolute;height:16px;width:16px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s}
.adm-toggle input:checked+span{background:var(--adm-teal)}
.adm-toggle input:checked+span::before{transform:translateX(18px)}
</style>
@endsection
