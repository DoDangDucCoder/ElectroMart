@extends('layouts.home-tech')
@section('body_class', 'accp-page')
@section('content')

<div class="accp-wrap">
    @include('partials.storefront.breadcrumb', [
        'class' => 'accp-crumb',
        'separator' => '›',
        'items' => [
            ['label' => 'Tài khoản', 'url' => route('customer.account')],
            ['label' => 'Đổi mật khẩu', 'current' => true],
        ],
    ])

    <div class="accp-page-title">
        <h1>Đổi mật khẩu</h1>
        <p>Cập nhật mật khẩu để bảo vệ tài khoản của bạn</p>
    </div>

    <div class="accp-grid">
        @include('pages.account.partials.sidebar', ['currentSection' => 'password-change'])

        <main>
            <div class="accp-card">
                <h3>Cập nhật mật khẩu</h3>
                <form method="POST" action="{{ route('customer.account.change-password') }}">
                    @csrf
                    <div class="accp-field">
                        <label>Mật khẩu hiện tại</label>
                        <div class="input-wrap">
                            <input type="password" name="current_password" id="curPw" placeholder="Nhập mật khẩu hiện tại" required>
                            <button type="button" class="toggle-pw" onclick="togglePw('curPw',this)"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>

                    <div class="accp-field">
                        <label>Mật khẩu mới</label>
                        <div class="input-wrap">
                            <input type="password" name="new_password" id="newPw2" placeholder="Nhập mật khẩu mới" required>
                            <button type="button" class="toggle-pw" onclick="togglePw('newPw2',this)"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>

                    <div class="accp-pw-meter" id="pwMeter2"><span></span><span></span><span></span><span></span></div>
                    <div class="accp-pw-level"><span>Mức độ bảo mật:</span><span id="pwStrengthLabel2" class="active strong" style="margin-left:auto">Mạnh</span></div>

                    <div class="accp-field">
                        <label>Nhập lại mật khẩu mới</label>
                        <div class="input-wrap">
                            <input type="password" name="new_password_confirmation" id="confirmPw2" required>
                            <button type="button" class="toggle-pw" onclick="togglePw('confirmPw2',this)"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>

                    <div class="accp-pw-reqs">
                        <h5><i class="fa fa-check-circle"></i> Yêu cầu mật khẩu</h5>
                        <ul>
                            <li id="req2-len"><i class="fa fa-circle"></i> Ít nhất 8 ký tự</li>
                            <li id="req2-upper"><i class="fa fa-circle"></i> Có ít nhất 1 chữ hoa (A-Z)</li>
                            <li id="req2-lower"><i class="fa fa-circle"></i> Có ít nhất 1 chữ thường (a-z)</li>
                            <li id="req2-num"><i class="fa fa-circle"></i> Có ít nhất 1 chữ số (0-9)</li>
                            <li id="req2-special"><i class="fa fa-circle"></i> Ít nhất 1 ký tự đặc biệt (!@#$%^&*)</li>
                        </ul>
                    </div>

                    <div class="rspw-checkbox" style="margin-bottom:18px">
                        <input type="checkbox" name="logout_all" id="logoutAll2">
                        <label for="logoutAll2">Đăng xuất khỏi tất các thiết bị sau khi đổi mật khẩu</label>
                    </div>

                    <div style="display:flex;gap:12px">
                        <a href="{{ route('customer.account') }}" class="accp-btn accp-btn--outline">Hủy</a>
                        <button type="submit" class="accp-btn accp-btn--primary"><i class="fa fa-check"></i> Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </main>

        <aside class="accp-aside">
            {{-- Security info --}}
            <div class="accp-aside-card">
                <h4>Bảo mật tài khoản</h4>
                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f3f4f6">
                    <div style="width:36px;height:36px;border-radius:8px;background:#f0fdfa;color:#0d9488;display:flex;align-items:center;justify-content:center"><i class="fa fa-envelope"></i></div>
                    <div style="flex:1"><div style="font-size:12px;font-weight:600;color:#111827">Email</div><div style="font-size:11px;color:#6b7280">{{ $customer->email ?? 'nguyenvanan@gmail.com' }}</div></div>
                    <span class="accp-verified"><i class="fa fa-check-circle"></i> Đã xác minh</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f3f4f6">
                    <div style="width:36px;height:36px;border-radius:8px;background:#f0fdfa;color:#0d9488;display:flex;align-items:center;justify-content:center"><i class="fa fa-phone"></i></div>
                    <div style="flex:1"><div style="font-size:12px;font-weight:600;color:#111827">Số điện thoại</div><div style="font-size:11px;color:#6b7280">0912 345 678</div></div>
                    <span class="accp-verified"><i class="fa fa-check-circle"></i> Đã xác minh</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid #f3f4f6">
                    <div style="width:36px;height:36px;border-radius:8px;background:#f0fdfa;color:#0d9488;display:flex;align-items:center;justify-content:center"><i class="fa fa-shield"></i></div>
                    <div style="flex:1"><div style="font-size:12px;font-weight:600;color:#111827">Xác thực 2 lớp (2FA)</div><div style="font-size:11px;color:#6b7280">Tăng cường bảo mật cho tài khoản của bạn bằng mã xác thực.</div></div>
                    <label style="position:relative;width:42px;height:22px;flex-shrink:0"><input type="checkbox" checked style="opacity:0;width:0;height:0"><span style="position:absolute;inset:0;background:#0d9488;border-radius:11px;cursor:pointer;transition:.3s"></span><span style="position:absolute;top:2px;left:22px;width:18px;height:18px;background:#fff;border-radius:50%;transition:.3s;box-shadow:0 1px 3px rgba(0,0,0,.2)"></span></label>
                </div>
                <div style="padding:6px 0;text-align:right"><a href="#" style="font-size:12px;color:#0d9488;text-decoration:none;font-weight:600">Quản lý 2FA</a></div>
            </div>

            {{-- Recent sessions --}}
            <div class="accp-aside-card">
                <h4>Phiên đăng nhập gần đây</h4>
                @foreach ([['device' => 'Windows 11 • Chrome', 'location' => 'Hà Nội, Việt Nam', 'badge' => 'Thiết bị này', 'time' => 'Vừa xong'], ['device' => 'iPhone 14 • Safari', 'location' => 'Hà Nội, Việt Nam', 'time' => '2 giờ trước'], ['device' => 'MacBook Pro • Chrome', 'location' => 'Đà Nẵng, Việt Nam', 'time' => '1 ngày trước']] as $session)
                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;{{ !$loop->last ? 'border-bottom:1px solid #f3f4f6' : '' }}">
                    <div style="flex:1;min-width:0"><div style="font-size:12px;font-weight:600;color:#111827">{{ $session['device'] }}</div><div style="font-size:11px;color:#9ca3af">{{ $session['location'] }}</div></div>
                    @if (!empty($session['badge']))<span class="accp-badge teal">{{ $session['badge'] }}</span>@endif
                    <span style="font-size:11px;color:#9ca3af;white-space:nowrap">{{ $session['time'] }}</span>
                </div>
                @endforeach
                <div style="text-align:right;padding-top:6px"><a href="#" style="font-size:12px;color:#0d9488;text-decoration:none;font-weight:600">Xem tất cả phiên đăng nhập <i class="fa fa-angle-right"></i></a></div>
            </div>

            {{-- Support --}}
            <div class="accp-aside-card">
                <h4>Bạn cần hỗ trợ?</h4>
                <p style="font-size:12px;color:#6b7280;margin:0 0 10px">Đội ngũ chăm sóc khách hàng luôn sẵn sàng hỗ trợ bạn.</p>
                <div style="padding:4px 0"><span style="font-size:11px;color:#9ca3af">Hotline:</span> <strong style="color:#111827">1900 1234</strong></div>
                <div style="padding:4px 0"><span style="font-size:11px;color:#9ca3af">Email:</span> <strong style="color:#0d9488">support@dangduc.vn</strong></div>
                <button type="button" class="accp-chat-btn-outline" data-chatbot-launch><i class="fa fa-comment-o"></i> Liên hệ hỗ trợ</button>
            </div>
        </aside>
    </div>

    {{-- Support bar --}}
    <div class="accp-support-bar">
        <div class="item"><span class="icon-wrap"><i class="fa fa-question-circle"></i></span><div><strong>Cần hỗ trợ về tài khoản?</strong><span> Đội ngũ chăm sóc khách hàng luôn sẵn sàng hỗ trợ bạn.</span></div></div>
        <div class="item" style="margin-left:auto"><span class="icon-wrap"><i class="fa fa-phone"></i></span><div><strong>Hotline: 1900 1234</strong></div></div>
        <div class="item"><span class="icon-wrap"><i class="fa fa-envelope-o"></i></span><div><strong>Email: support@dangduc.vn</strong></div></div>
        <div class="item"><span class="icon-wrap"><i class="fa fa-comments-o"></i></span><div><strong>Chat với chúng tôi</strong></div></div>
    </div>

    {{-- Articles --}}
    <div class="accp-articles">
        <h3>Bài viết hữu ích</h3>
        <div class="accp-articles-grid">
            <div class="accp-article"><div class="accp-article-img"><span class="icon-placeholder"><i class="fa fa-shield"></i></span></div><div class="accp-article-body"><h4>5 cách bảo mật tài khoản hiệu quả</h4><p>Bảo vệ thông tin và tài sản của bạn trực tuyến.</p><a href="#">Đọc ngay <i class="fa fa-arrow-right"></i></a></div></div>
            <div class="accp-article"><div class="accp-article-img"><span class="icon-placeholder"><i class="fa fa-lock"></i></span></div><div class="accp-article-body"><h4>Tại sao nên sử dụng xác thực 2 lớp?</h4><p>Tăng cường bảo mật với 2FA cho tài khoản của bạn.</p><a href="#">Đọc ngay <i class="fa fa-arrow-right"></i></a></div></div>
            <div class="accp-article"><div class="accp-article-img"><span class="icon-placeholder"><i class="fa fa-key"></i></span></div><div class="accp-article-body"><h4>Cách tạo mật khẩu mạnh và an toàn</h4><p>Hướng dẫn cần tạo mật khẩu khó bị đoán.</p><a href="#">Đọc ngay <i class="fa fa-arrow-right"></i></a></div></div>
            <div class="accp-article"><div class="accp-article-img"><span class="icon-placeholder"><i class="fa fa-exclamation-triangle"></i></span></div><div class="accp-article-body"><h4>Nhận biết và tránh lừa đảo trực tuyến</h4><p>Những dấu hiệu giúp bạn nhận biết các chiêu trò lừa đảo.</p><a href="#">Đọc ngay <i class="fa fa-arrow-right"></i></a></div></div>
        </div>
    </div>
</div>

<script>
function togglePw(id,btn){var i=document.getElementById(id);var ic=btn.querySelector('i');if(i.type==='password'){i.type='text';ic.className='fa fa-eye-slash'}else{i.type='password';ic.className='fa fa-eye'}}
document.getElementById('newPw2').addEventListener('input',function(){
    var v=this.value,score=0,m=document.getElementById('pwMeter2'),lbl=document.getElementById('pwStrengthLabel2');
    if(v.length>=8)score++;if(/[A-Z]/.test(v))score++;if(/[a-z]/.test(v))score++;if(/[0-9]/.test(v))score++;if(/[^A-Za-z0-9]/.test(v))score++;
    m.className='accp-pw-meter '+(score<=1?'weak':score<=2?'medium':score<=3?'strong':'very-strong');
    lbl.className='active '+(score<=1?'weak':score<=3?'medium':'strong');
    lbl.textContent=score<=1?'Yếu':score<=3?'Trung bình':'Mạnh';
    var reqs={len:v.length>=8,upper:/[A-Z]/.test(v),lower:/[a-z]/.test(v),num:/[0-9]/.test(v),special:/[^A-Za-z0-9]/.test(v)};
    Object.keys(reqs).forEach(function(k){var el=document.getElementById('req2-'+k);if(el){el.className=reqs[k]?'met':'';el.querySelector('i').className='fa '+(reqs[k]?'fa-check-circle':'fa-circle')}});
});
</script>
@endsection
