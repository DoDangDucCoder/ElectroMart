<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực OTP | VNPAY (Test)</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px; }
        .vnpay-container { max-width: 900px; margin: 0 auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; display: flex; }
        .vnpay-left { width: 35%; background: #fafafa; padding: 30px; border-right: 1px solid #eee; }
        .vnpay-right { width: 65%; padding: 40px 80px; position: relative; text-align: center; display: flex; flex-direction: column; justify-content: center; }
        .vnpay-logo { height: 40px; margin-bottom: 30px; text-align: left; }
        
        .order-info h3 { margin-top: 0; font-size: 18px; color: #333; margin-bottom: 20px; font-weight: normal; }
        .info-row { margin-bottom: 15px; }
        .info-row span { display: block; font-size: 13px; color: #666; margin-bottom: 5px; text-align: left;}
        .info-row strong { display: block; font-size: 15px; color: #333; text-align: left;}
        .info-row .amount { font-size: 20px; color: #005A9C; }
        
        .form-header h2 { margin-top: 0; font-size: 22px; color: #333; font-weight: normal; margin-bottom: 30px; }
        
        .otp-group { position: relative; margin-bottom: 20px; }
        .otp-input { width: 100%; padding: 15px 40px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; box-sizing: border-box; text-align: center; }
        .otp-input:focus { border-color: #005A9C; outline: none; }
        .key-icon { position: absolute; left: 15px; top: 15px; font-size: 18px; color: #666; }
        .ncb-logo { position: absolute; right: 15px; top: 15px; height: 20px; }
        
        .otp-text { font-size: 14px; color: #555; margin-bottom: 30px; line-height: 1.5; text-align: left; }
        
        .btn-submit { width: 100%; padding: 15px; background: #005A9C; color: #fff; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; text-decoration: none; display: block; box-sizing: border-box;}
        .btn-submit:hover { background: #004a80; }
        
        @media (max-width: 768px) {
            .vnpay-container { flex-direction: column; }
            .vnpay-left, .vnpay-right { width: 100%; padding: 20px; box-sizing: border-box; }
        }
    </style>
</head>
<body>
    <div style="max-width: 900px; margin: 0 auto; margin-bottom: 15px;">
        <a href="{{ url('/vnpay-mock/ncb') }}?amount={{ request('amount') }}" style="color: #555; text-decoration: none;">&larr; Quay lại</a>
    </div>
    
    <div class="vnpay-container">
        <!-- Left Sidebar -->
        <div class="vnpay-left">
            <img src="https://vnpay.vn/s1/statics.vnpay.vn/2023/9/06ncktiwd6dc1694418186_logo-vnpay.png" alt="VNPAY" class="vnpay-logo">
            <div class="order-info">
                <h3>Thông tin đơn hàng (Test)</h3>
                
                <div class="info-row">
                    <span>Số tiền thanh toán</span>
                    <strong class="amount">{{ number_format((float)request('amount', 0), 0, ',', '.') }}đ</strong>
                </div>
                
                <div class="info-row">
                    <span>Giá trị đơn hàng</span>
                    <strong>{{ number_format((float)request('amount', 0), 0, ',', '.') }}đ</strong>
                </div>
                
                <div class="info-row">
                    <span>Phí giao dịch</span>
                    <strong>0đ</strong>
                </div>
                
                <div class="info-row">
                    <span>Mã đơn hàng</span>
                    <strong>{{ rand(100, 999) }}</strong>
                </div>
                
                <div class="info-row">
                    <span>Nhà cung cấp</span>
                    <strong>https://vnshop.vn/</strong>
                </div>
            </div>
        </div>
        
        <!-- Right Content -->
        <div class="vnpay-right">
            <div class="form-header">
                <h2>Xác thực OTP</h2>
            </div>
            
            <form action="{{ url('/history') }}" method="GET">
                <div class="otp-group">
                    <span class="key-icon">&#128273;</span>
                    <input type="text" class="otp-input" placeholder="Nhập mã OTP" value="123456" required>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/Bank_icon_2.svg/512px-Bank_icon_2.svg.png" class="ncb-logo" alt="NCB">
                </div>
                
                <p class="otp-text">OTP đã được gửi về số điện thoại đăng ký. Quý khách vui lòng nhập OTP để tiếp tục thực hiện giao dịch.</p>
                
                <button type="submit" class="btn-submit">Thanh toán</button>
            </form>
        </div>
    </div>
</body>
</html>
