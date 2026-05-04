@extends('layouts.home-tech')
@section('body_class', 'accp-page')
@section('content')
<link rel="stylesheet" href="{{ asset('frontend/css/account-shared.css') }}">
<style>
.rspw-grid{display:grid;grid-template-columns:1fr 380px;gap:24px;max-width:1000px;margin:0 auto;align-items:flex-start}
@media(max-width:900px){.rspw-grid{grid-template-columns:1fr}}
.rspw-card{background:#fff;border-radius:14px;border:1px solid #e5e7eb;padding:32px 36px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
.rspw-card h2{font-size:22px;font-weight:800;color:#0f172a;margin:0 0 6px}
.rspw-card>p{font-size:13.5px;color:#6b7280;margin:0 0 24px;line-height:1.5}
.rspw-actions{display:flex;gap:12px;margin-top:20px;align-items:center;flex-wrap:wrap}
.rspw-actions .primary{flex:1;max-width:260px;height:44px;background:#0d9488;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .2s}
.rspw-actions .primary:hover{background:#0f766e}
.rspw-actions .back{display:flex;align-items:center;gap:6px;color:#6b7280;text-decoration:none;font-size:13px;font-weight:500}
.rspw-actions .back:hover{color:#0d9488}
.rspw-checkbox{display:flex;align-items:center;gap:8px;font-size:13px;color:#374151;margin-top:14px}
.rspw-checkbox input{width:16px;height:16px;accent-color:#0d9488}
.rspw-checkbox .info{color:#9ca3af;cursor:help;font-size:12px}
/* Right sidebar */
.rspw-aside .aside-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;margin-bottom:16px}
.rspw-aside .aside-icon{width:80px;height:80px;margin:0 auto 16px;display:block}
.rspw-aside .aside-item{display:flex;gap:14px;padding:12px 0;align-items:flex-start}
.rspw-aside .aside-item+.aside-item{border-top:1px solid #f3f4f6}
.rspw-aside .aside-item-icon{width:40px;height:40px;border-radius:10px;background:#f0fdfa;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.rspw-aside .aside-item h5{font-size:13px;font-weight:700;color:#111827;margin:0 0 2px}
.rspw-aside .aside-item p{font-size:12px;color:#6b7280;margin:0;line-height:1.4}
.rspw-aside .contact-row{display:flex;align-items:center;gap:8px;padding:6px 0;font-size:13px}
.rspw-aside .contact-row i{color:#0d9488;width:16px;text-align:center}
.rspw-aside .contact-row a,.rspw-aside .contact-row strong{color:#0d9488;text-decoration:none;font-weight:600}
</style>

<div class="accp-wrap">
    <div class="accp-crumb">
        <a href="{{ url('/') }}">Trang chủ</a><span class="sep">›</span>
        <a href="{{ route('customer.account') }}">Tài khoản</a><span class="sep">›</span>
        <span class="current">Đặt lại mật khẩu</span>
    </div>

    <div class="rspw-grid">
        {{-- LEFT: Form --}}
        <div class="rspw-card">
            <h2>Đặt lại mật khẩu mới</h2>
            <p>Tạo mật khẩu mới cho tài khoản của bạn để tiếp tục mua sắm an toàn.</p>

            <form method="POST" action="{{ url('/account/reset-password') }}" id="resetPwForm">
                @csrf
                <div class="accp-field">
                    <label>Mật khẩu mới</label>
                    <div class="input-wrap">
                        <input type="password" name="new_password" id="newPw" placeholder="Nhập mật khẩu mới" required>
                        <button type="button" class="toggle-pw" onclick="togglePw('newPw',this)"><i class="fa fa-eye"></i></button>
                    </div>
                </div>

                <div class="accp-pw-meter" id="pwMeter"><span></span><span></span><span></span><span></span></div>
                <div class="accp-pw-level">
                    <span id="pwLblWeak">Yếu</span>
                    <span id="pwLblMed">Trung bình</span>
                    <span id="pwLblStrong">Mạnh</span>
                </div>

                <div class="accp-field">
                    <label>Nhập lại mật khẩu mới</label>
                    <div class="input-wrap">
                        <input type="password" name="new_password_confirmation" id="confirmPw" placeholder="Nhập lại mật khẩu mới" required>
                        <button type="button" class="toggle-pw" onclick="togglePw('confirmPw',this)"><i class="fa fa-eye"></i></button>
                    </div>
                </div>

                <div class="accp-pw-reqs">
                    <h5><i class="fa fa-check-circle"></i> Yêu cầu mật khẩu</h5>
                    <ul>
                        <li id="req-len"><i class="fa fa-circle"></i> Ít nhất 8 ký tự</li>
                        <li id="req-upper"><i class="fa fa-circle"></i> Ít nhất 1 chữ cái viết hoa</li>
                        <li id="req-lower"><i class="fa fa-circle"></i> Ít nhất 1 chữ cái viết thường</li>
                        <li id="req-num"><i class="fa fa-circle"></i> Ít nhất 1 chữ số (0-9)</li>
                        <li id="req-special"><i class="fa fa-circle"></i> Ít nhất 1 ký tự đặc biệt (!@#$%^&*)</li>
                    </ul>
                </div>

                <div class="rspw-checkbox">
                    <input type="checkbox" name="logout_all" id="logoutAll">
                    <label for="logoutAll">Đăng xuất khỏi tất cả thiết bị khác</label>
                    <span class="info" title="Tất cả phiên đăng nhập trên các thiết bị khác sẽ bị đăng xuất">ⓘ</span>
                </div>

                <div class="rspw-actions">
                    <button type="submit" class="primary"><i class="fa fa-lock"></i> Lưu mật khẩu mới</button>
                    <a href="{{ url('/dang-nhap') }}" class="back"><i class="fa fa-arrow-left"></i> Quay lại đăng nhập</a>
                </div>
            </form>
        </div>

        {{-- RIGHT: Security tips --}}
        <aside class="rspw-aside">
            <div class="aside-card">
                <svg class="aside-icon" viewBox="0 0 80 80" fill="none"><rect width="80" height="80" rx="40" fill="#f0fdfa"/><path d="M40 20c-8 0-14 6-14 14v6h-2a4 4 0 00-4 4v16a4 4 0 004 4h32a4 4 0 004-4V44a4 4 0 00-4-4h-2v-6c0-8-6-14-14-14zm-8 14c0-4.4 3.6-8 8-8s8 3.6 8 8v6H32v-6zm10 18a2 2 0 11-4 0v-4a2 2 0 114 0v4z" fill="#0d9488"/></svg>
                <div class="aside-item">
                    <div class="aside-item-icon"><i class="fa fa-shield"></i></div>
                    <div><h5>Bảo mật tài khoản</h5><p>Mật khẩu mới giúp bảo vệ tài khoản và thông tin cá nhân của bạn.</p></div>
                </div>
                <div class="aside-item">
                    <div class="aside-item-icon"><i class="fa fa-envelope"></i></div>
                    <div><h5>Email đã xác minh</h5><p>Liên kết đặt lại đã được gửi đến email của bạn và đã được xác minh.</p></div>
                </div>
                <div class="aside-item">
                    <div class="aside-item-icon"><i class="fa fa-headphones"></i></div>
                    <div><h5>Hỗ trợ 24/7</h5><p>Đội ngũ hỗ trợ luôn sẵn sàng giúp bạn mọi lúc, mọi nơi.</p></div>
                </div>
            </div>

            <div class="aside-card">
                <h4 style="font-size:14px;font-weight:700;color:#111827;margin:0 0 10px">Cần hỗ trợ?</h4>
                <div class="contact-row"><i class="fa fa-phone"></i><strong>1900 1234</strong></div>
                <div class="contact-row"><i class="fa fa-envelope"></i><a href="mailto:support@dangduc.vn">support@dangduc.vn</a></div>
            </div>
        </aside>
    </div>

    {{-- Articles --}}
    <div class="accp-articles">
        <h3>Xem thêm bài viết hữu ích</h3>
        <div class="accp-articles-grid">
            <div class="accp-article"><div class="accp-article-img"><span class="icon-placeholder"><i class="fa fa-key"></i></span></div><div class="accp-article-body"><h4>Cách tạo mật khẩu mạnh</h4><p>Hướng dẫn chi tiết giúp bạn tạo mật khẩu an toàn và khó bị đoán.</p><a href="#">Đọc ngay <i class="fa fa-arrow-right"></i></a></div></div>
            <div class="accp-article"><div class="accp-article-img"><span class="icon-placeholder"><i class="fa fa-lock"></i></span></div><div class="accp-article-body"><h4>Vì sao nên bật xác thực 2 lớp?</h4><p>Tăng cường bảo mật tài khoản với lớp bảo vệ thứ hai.</p><a href="#">Đọc ngay <i class="fa fa-arrow-right"></i></a></div></div>
            <div class="accp-article"><div class="accp-article-img"><span class="icon-placeholder"><i class="fa fa-shield"></i></span></div><div class="accp-article-body"><h4>Mẹo bảo vệ tài khoản</h4><p>Những thói quen đơn giản giúp bạn giữ tài khoản luôn an toàn.</p><a href="#">Đọc ngay <i class="fa fa-arrow-right"></i></a></div></div>
        </div>
    </div>
</div>

<script>
function togglePw(id,btn){var i=document.getElementById(id);var ic=btn.querySelector('i');if(i.type==='password'){i.type='text';ic.className='fa fa-eye-slash'}else{i.type='password';ic.className='fa fa-eye'}}
document.getElementById('newPw').addEventListener('input',function(){
    var v=this.value,score=0,m=document.getElementById('pwMeter');
    if(v.length>=8)score++;if(/[A-Z]/.test(v))score++;if(/[a-z]/.test(v))score++;if(/[0-9]/.test(v))score++;if(/[^A-Za-z0-9]/.test(v))score++;
    m.className='accp-pw-meter '+(score<=1?'weak':score<=2?'medium':score<=3?'strong':'very-strong');
    var labels=['pwLblWeak','pwLblMed','pwLblStrong'];
    labels.forEach(function(l){document.getElementById(l).className=''});
    if(score<=1)document.getElementById('pwLblWeak').className='active weak';
    else if(score<=3)document.getElementById('pwLblMed').className='active medium';
    else document.getElementById('pwLblStrong').className='active strong';
    // Requirements
    var reqs={len:v.length>=8,upper:/[A-Z]/.test(v),lower:/[a-z]/.test(v),num:/[0-9]/.test(v),special:/[^A-Za-z0-9]/.test(v)};
    Object.keys(reqs).forEach(function(k){var el=document.getElementById('req-'+k);if(el){el.className=reqs[k]?'met':'';el.querySelector('i').className='fa '+(reqs[k]?'fa-check-circle':'fa-circle')}});
});
</script>
@endsection
