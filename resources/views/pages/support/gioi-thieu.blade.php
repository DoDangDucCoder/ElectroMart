@extends('layouts.home-tech')
@section('body_class','policy-page about-page')
@php
$meta_desc=$meta_desc??'Giới thiệu về DangDuc - Đồng hành cùng hiệu năng đỉnh cao, phân phối laptop, PC và linh kiện chính hãng';
$meta_keywords=$meta_keywords??'giới thiệu, về chúng tôi, DangDuc, about us';
$url_canonical=$url_canonical??url('/gioi-thieu');
$meta_title=$meta_title??'Giới thiệu - DangDuc';
@endphp
@push('styles')<link href="{{ asset('frontend/css/policy-page.css') }}" rel="stylesheet">
<style>
.about-hero{display:flex;align-items:center;gap:48px;padding:48px 0;margin-bottom:32px}
.about-hero-copy{flex:1}
.about-hero-copy .label{color:#0f172a;font-size:13px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:8px}
.about-hero-copy h1{color:#0f172a;font-size:32px;font-weight:800;line-height:1.25;margin:0 0 16px}
.about-hero-copy h1 span{color:#0d9488}
.about-hero-copy p{color:#64748b;font-size:14px;line-height:1.65;margin:0 0 20px}
.about-hero-cta{display:inline-flex;align-items:center;gap:8px;background:#0f172a;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;transition:opacity .2s}
.about-hero-cta:hover{opacity:.88;color:#fff}
.about-hero-img{flex:0 0 420px;height:280px;border-radius:16px;background:linear-gradient(135deg,#0f172a,#1e3a5f);display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative}
.about-hero-img i{font-size:80px;color:rgba(13,148,136,.3)}
.about-hero-img::after{content:'';position:absolute;inset:0;background:linear-gradient(135deg,transparent 60%,rgba(13,148,136,.15))}

.about-stats{display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:48px}
.about-stat{text-align:center;padding:20px 12px;border:1px solid #e5e7eb;border-radius:12px;background:#fff}
.about-stat i{font-size:28px;color:#0d9488;display:block;margin-bottom:8px}
.about-stat .num{font-size:28px;font-weight:800;color:#0f172a;margin-bottom:2px}
.about-stat strong{font-size:13px;color:#111827;display:block;margin-bottom:4px}
.about-stat span{font-size:11.5px;color:#64748b;line-height:1.35}

.about-values-title{text-align:center;font-size:20px;font-weight:700;color:#0f172a;margin:0 0 28px;text-transform:uppercase;letter-spacing:.5px}
.about-values{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:48px}
.about-value{padding:28px 20px;border:1px solid #e5e7eb;border-radius:14px;background:#fff;text-align:center}
.about-value i{font-size:36px;color:#0d9488;display:block;margin-bottom:12px}
.about-value strong{font-size:15px;color:#0f172a;display:block;margin-bottom:8px}
.about-value p{font-size:12.5px;color:#64748b;margin:0;line-height:1.5}

@media(max-width:900px){.about-hero{flex-direction:column}.about-hero-img{flex:none;width:100%;height:200px}.about-stats{grid-template-columns:repeat(3,1fr)}.about-values{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){.about-stats{grid-template-columns:repeat(2,1fr)}.about-values{grid-template-columns:1fr}}
</style>
@endpush
@section('content')
<div class="pp-wrap">
    <nav class="pp-breadcrumb"><a href="{{ url('/') }}">Trang chủ</a> <i class="fa fa-angle-right"></i> Giới thiệu</nav>

    {{-- Hero --}}
    <section class="about-hero">
        <div class="about-hero-copy">
            <div class="label">VỀ CHÚNG TÔI</div>
            <h1>DangDuc – Đồng hành cùng<br><span>hiệu năng đỉnh cao</span></h1>
            <p>DangDuc là hệ thống phân phối laptop, PC, workstation và linh kiện chính hãng hàng đầu tại Việt Nam. Chúng tôi cam kết mang đến cho khách hàng những sản phẩm chất lượng – giá tốt – dịch vụ chuyên nghiệp để đáp ứng mọi nhu cầu từ học tập, làm việc đến gaming và sáng tạo.</p>
            <a href="#" class="about-hero-cta">Tìm hiểu thêm về DangDuc <i class="fa fa-arrow-right"></i></a>
        </div>
        <div class="about-hero-img">
            <i class="fa fa-building"></i>
        </div>
    </section>

    {{-- Stats --}}
    <div class="about-stats">
        <div class="about-stat"><i class="fa fa-calendar"></i><div class="num">10+</div><strong>Năm kinh nghiệm</strong><span>Trong lĩnh vực phân phối laptop, PC và linh kiện</span></div>
        <div class="about-stat"><i class="fa fa-map-marker"></i><div class="num">20+</div><strong>Chi nhánh toàn quốc</strong><span>Hệ thống cửa hàng phủ sóng khắp các tỉnh thành</span></div>
        <div class="about-stat"><i class="fa fa-cubes"></i><div class="num">10000+</div><strong>Sản phẩm đa dạng</strong><span>Đáp ứng mọi nhu cầu từ phổ thông đến cao cấp</span></div>
        <div class="about-stat"><i class="fa fa-users"></i><div class="num">50000+</div><strong>Khách hàng tin tưởng</strong><span>Được phục vụ và đồng hành trên khắp cả nước</span></div>
        <div class="about-stat"><i class="fa fa-check-circle"></i><div class="num">100%</div><strong>Sản phẩm chính hãng</strong><span>Cam kết chính hãng – bảo hành rõ ràng – đầy đủ</span></div>
    </div>

    {{-- Core values --}}
    <h2 class="about-values-title">Giá trị cốt lõi của chúng tôi</h2>
    <div class="about-values">
        <div class="about-value">
            <i class="fa fa-shield"></i>
            <strong>Uy tín là nền tảng</strong>
            <p>Chúng tôi luôn đặt chữ tín lên hàng đầu trong mọi hoạt động, cam kết mang đến sản phẩm chính hãng và dịch vụ tốt nhất.</p>
        </div>
        <div class="about-value">
            <i class="fa fa-heart"></i>
            <strong>Khách hàng là trung tâm</strong>
            <p>Mọi quyết định của chúng tôi đều hướng tới việc mang lại trải nghiệm tốt nhất cho khách hàng.</p>
        </div>
        <div class="about-value">
            <i class="fa fa-star"></i>
            <strong>Chất lượng là cam kết</strong>
            <p>Sản phẩm chất lượng, dịch vụ chuyên nghiệp và chế độ hậu mãi vượt trội là những gì chúng tôi cam kết.</p>
        </div>
        <div class="about-value">
            <i class="fa fa-rocket"></i>
            <strong>Không ngừng phát triển</strong>
            <p>Chúng tôi luôn đổi mới, cập nhật công nghệ và nâng cao dịch vụ để bắt kịp xu hướng và phục vụ khách hàng tốt hơn.</p>
        </div>
    </div>
</div>
@endsection
