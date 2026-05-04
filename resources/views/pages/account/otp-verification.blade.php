@extends('layouts.home-tech')
@section('body_class', 'accp-page')
@section('content')
<link rel="stylesheet" href="{{ asset('frontend/css/account-shared.css') }}">
<style>
.otp-grid{display:grid;grid-template-columns:1fr 380px;gap:24px;max-width:1000px;margin:0 auto;align-items:flex-start}
@media(max-width:900px){.otp-grid{grid-template-columns:1fr}}
.otp-card{background:#fff;border-radius:14px;border:1px solid #e5e7eb;padding:32px 36px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
.otp-card .icon-header{display:flex;align-items:center;gap:10px;margin-bottom:16px}
.otp-card .icon-header .icon{width:36px;height:36px;border-radius:50%;background:#f0fdfa;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:18px}
.otp-card h2{font-size:22px;font-weight:800;color:#0f172a;margin:0}
.otp-card .desc{font-size:13px;color:#6b7280;line-height:1.6;margin:0 0 24px}
.otp-card .desc b{color:#111827}
.otp-label{font-size:14px;font-weight:700;color:#111827;margin-bottom:14px}
/* OTP inputs */
.otp-inputs{display:flex;gap:10px;margin-bottom:16px;justify-content:flex-start}
.otp-inputs input{width:52px;height:60px;border:2px solid #e5e7eb;border-radius:10px;text-align:center;font-size:26px;font-weight:800;color:#111827;transition:border-color .15s;outline:none;background:#fff}
.otp-inputs input:focus{border-color:#0d9488;box-shadow:0 0 0 3px rgba(13,148,136,.1)}
.otp-inputs input.filled{border-color:#0d9488}
.otp-timer{font-size:14px;color:#374151;margin-bottom:20px;display:flex;align-items:center;gap:6px}
.otp-timer .time{font-size:24px;font-weight:800;color:#0d9488}
.otp-resend{display:flex;align-items:center;gap:8px;padding:10px 0;margin-bottom:16px}
.otp-resend input{flex:1;height:42px;border:1px solid #e5e7eb;border-radius:8px;padding:0 14px;font-size:13px;color:#374151;outline:none}
.otp-resend input::placeholder{color:#9ca3af}
.otp-resend a{display:flex;align-items:center;gap:6px;color:#0d9488;text-decoration:none;font-size:13px;font-weight:600;white-space:nowrap}
.otp-resend a:hover{color:#0f766e}
.otp-trust{display:flex;align-items:center;gap:8px;font-size:13px;color:#374151;margin-bottom:20px}
.otp-trust input{width:16px;height:16px;accent-color:#0d9488}
.otp-trust .info{color:#9ca3af;cursor:help;font-size:12px}
.otp-submit{width:100%;height:46px;background:#0d9488;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer;transition:background .15s;margin-bottom:12px}
.otp-submit:hover{background:#0f766e}
.otp-back{width:100%;height:42px;background:#fff;color:#374151;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s;margin-bottom:16px;display:flex;align-items:center;justify-content:center;gap:6px;text-decoration:none}
.otp-back:hover{border-color:#0d9488;color:#0d9488}
.otp-change-method{text-align:center;margin-top:4px}
.otp-change-method a{font-size:13px;color:#6b7280;text-decoration:none;display:flex;align-items:center;gap:6px;justify-content:center}
.otp-change-method a:hover{color:#0d9488}
/* Aside */
.otp-aside .aside-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;margin-bottom:16px}
.otp-aside .illustration{width:200px;height:160px;margin:0 auto 16px;display:block;background:#f0fdfa;border-radius:12px;display:flex;align-items:center;justify-content:center}
.otp-aside .illustration .otp-visual{position:relative;width:140px;height:120px}
.otp-aside .illustration .phone{width:60px;height:100px;border:2px solid #0d9488;border-radius:8px;background:#fff;position:absolute;left:10px;top:10px;display:flex;align-items:center;justify-content:center}
.otp-aside .illustration .otp-badge{position:absolute;top:0;right:0;background:#0d9488;color:#fff;padding:6px 12px;border-radius:8px;font-size:14px;font-weight:800;letter-spacing:2px}
.otp-aside .illustration .shield{position:absolute;bottom:0;right:20px;width:28px;height:28px;background:#0d9488;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px}
.otp-aside .aside-item{display:flex;gap:12px;padding:10px 0;align-items:flex-start}
.otp-aside .aside-item+.aside-item{border-top:1px solid #f3f4f6}
.otp-aside .aside-item .ic{width:36px;height:36px;border-radius:8px;background:#f0fdfa;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.otp-aside .aside-item h5{font-size:13px;font-weight:700;color:#111827;margin:0 0 2px}
.otp-aside .aside-item p{font-size:12px;color:#6b7280;margin:0;line-height:1.4}
.otp-aside .contact-box{background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px;padding:14px}
.otp-aside .contact-box h5{font-size:13px;font-weight:700;color:#111827;margin:0 0 8px}
.otp-aside .contact-box .row{display:flex;align-items:center;gap:8px;padding:3px 0;font-size:13px}
.otp-aside .contact-box .row i{color:#0d9488;width:14px}
.otp-aside .contact-box .row a,.otp-aside .contact-box .row strong{color:#0d9488;text-decoration:none;font-weight:600}
/* Articles */
.otp-articles{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:28px}
@media(max-width:768px){.otp-articles{grid-template-columns:1fr}}
.otp-article{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:20px;display:flex;gap:14px;align-items:flex-start;transition:box-shadow .15s}
.otp-article:hover{box-shadow:0 2px 8px rgba(0,0,0,.06)}
.otp-article .art-icon{width:54px;height:54px;border-radius:10px;background:#f0fdfa;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
.otp-article .art-body h4{font-size:13px;font-weight:700;color:#111827;margin:0 0 4px;line-height:1.4}
.otp-article .art-body p{font-size:11.5px;color:#6b7280;margin:0 0 6px;line-height:1.45}
.otp-article .art-body a{font-size:12px;color:#0d9488;text-decoration:none;font-weight:600}
</style>

<div class="accp-wrap">
    <div class="accp-crumb"><a href="{{ url('/') }}">Trang chủ</a><span class="sep">›</span><a href="{{ route('customer.account') }}">Tài khoản</a><span class="sep">›</span><span class="current">Xác thực OTP</span></div>

    <div class="otp-grid">
        {{-- Left: OTP Form --}}
        <div class="otp-card">
            <div class="icon-header">
                <div class="icon"><i class="fa fa-shield"></i></div>
                <h2>Xác thực OTP / mã xác nhận</h2>
            </div>
            <p class="desc">Chúng tôi đã gửi mã xác nhận đến email <b>nguyenvanan@gmail.com</b> và số điện thoại <b>*** *** 678</b>. Vui lòng nhập mã để tiếp tục.</p>

            <div class="otp-label">Nhập mã OTP</div>
            <form id="otpForm" method="POST" action="#">
                @csrf
                <div class="otp-inputs">
                    <input type="text" maxlength="1" class="otp-input filled" value="4" data-index="0" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-input filled" value="8" data-index="1" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-input filled" value="2" data-index="2" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-input" value="1" data-index="3" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-input" value="5" data-index="4" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-input" value="9" data-index="5" inputmode="numeric">
                </div>

                <div class="otp-timer">Mã sẽ hết hạn sau: <span class="time" id="otpCountdown">01:24</span></div>

                <div class="otp-resend">
                    <input type="text" placeholder="Chưa nhận được mã?" disabled>
                    <a href="#" id="resendBtn"><i class="fa fa-refresh"></i> Gửi lại mã</a>
                </div>

                <div class="otp-trust">
                    <input type="checkbox" name="trust_device" id="trustDevice" checked>
                    <label for="trustDevice">Tin cậy thiết bị này trong 30 ngày</label>
                    <span class="info" title="Bạn sẽ không cần nhập OTP trong 30 ngày tới trên thiết bị này">ⓘ</span>
                </div>

                <button type="submit" class="otp-submit">Xác nhận</button>
                <a href="{{ url('/dang-nhap') }}" class="otp-back"><i class="fa fa-arrow-left"></i> Quay lại</a>

                <div class="otp-change-method">
                    <a href="#"><i class="fa fa-exchange"></i> Đổi email / số điện thoại nhận mã →</a>
                </div>
            </form>
        </div>

        {{-- Right: Security info --}}
        <aside class="otp-aside">
            <div class="aside-card">
                <div class="illustration">
                    <div class="otp-visual">
                        <div class="phone"><i class="fa fa-mobile" style="font-size:28px;color:#0d9488"></i></div>
                        <div class="otp-badge">482159</div>
                        <div class="shield"><i class="fa fa-check"></i></div>
                    </div>
                </div>
                <div class="aside-item">
                    <div class="ic"><i class="fa fa-check-circle"></i></div>
                    <div><h5>Bảo mật tài khoản</h5><p>Mã OTP giúp xác minh chính chủ và tăng cường an toàn cho tài khoản của bạn.</p></div>
                </div>
                <div class="aside-item">
                    <div class="ic"><i class="fa fa-headphones"></i></div>
                    <div><h5>Hỗ trợ 24/7</h5><p>Nếu gặp khó khăn, đội ngũ DangDuc luôn sẵn sàng hỗ trợ bạn.</p></div>
                </div>
            </div>
            <div class="contact-box">
                <h5>Cần hỗ trợ?</h5>
                <div class="row"><i class="fa fa-phone"></i><strong>1900 1234</strong></div>
                <div class="row"><i class="fa fa-envelope"></i><a href="mailto:support@dangduc.vn">support@dangduc.vn</a></div>
            </div>
        </aside>
    </div>

    {{-- Articles --}}
    <div class="otp-articles">
        <div class="otp-article">
            <div class="art-icon"><i class="fa fa-shield"></i></div>
            <div class="art-body"><h4>Cách bảo vệ tài khoản của bạn</h4><p>Áp dụng các biện pháp bảo mật để giữ an toàn cho tài khoản và thông tin cá nhân.</p><a href="#">Đọc ngay →</a></div>
        </div>
        <div class="otp-article">
            <div class="art-icon"><i class="fa fa-lock"></i></div>
            <div class="art-body"><h4>Vì sao nên bật xác thực 2 lớp?</h4><p>Tăng cường bảo mật tài khoản với lớp bảo vệ thứ hai.</p><a href="#">Đọc ngay →</a></div>
        </div>
        <div class="otp-article">
            <div class="art-icon"><i class="fa fa-mobile"></i></div>
            <div class="art-body"><h4>Mẹo nhận mã OTP nhanh hơn</h4><p>Giúp bạn nhận mã OTP nhanh chóng và tránh lỗi thường gặp.</p><a href="#">Đọc ngay →</a></div>
        </div>
    </div>
</div>

<script>
// OTP input auto-focus
document.querySelectorAll('.otp-input').forEach(function(inp,i,all){
    inp.addEventListener('input',function(){
        if(this.value.length===1&&all[i+1])all[i+1].focus();
        this.classList.toggle('filled',this.value.length>0);
    });
    inp.addEventListener('keydown',function(e){
        if(e.key==='Backspace'&&!this.value&&all[i-1]){all[i-1].focus();all[i-1].value='';all[i-1].classList.remove('filled');}
    });
});
// Countdown
(function(){
    var sec=84,el=document.getElementById('otpCountdown');
    if(!el)return;
    var timer=setInterval(function(){
        sec--;if(sec<0){clearInterval(timer);el.textContent='00:00';return;}
        var m=Math.floor(sec/60),s=sec%60;
        el.textContent=(m<10?'0'+m:m)+':'+(s<10?'0'+s:s);
    },1000);
})();
</script>
@endsection
