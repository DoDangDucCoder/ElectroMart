<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng thanh toán VNPAY (Test)</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px; }
        .vnpay-container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; }
        .vnpay-header { padding: 20px; text-align: center; border-bottom: 1px solid #eee; background: #fafafa; }
        .vnpay-header h2 { margin: 10px 0 0; font-size: 20px; font-weight: normal; color: #333; }
        .vnpay-logo { height: 40px; }
        .vnpay-body { padding: 20px; }
        .payment-option { display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; border: 1px solid #ddd; margin-bottom: 15px; border-radius: 8px; cursor: pointer; text-decoration: none; color: #333; transition: 0.2s; }
        .payment-option:hover { border-color: #005A9C; box-shadow: 0 0 5px rgba(0,90,156,0.2); }
        .payment-option span { font-size: 16px; }
        .payment-option-icons { display: flex; gap: 5px; }
        .payment-option-icons img { height: 24px; border-radius: 2px; }
        .vnpay-footer { text-align: center; padding: 20px; font-size: 13px; color: #888; background: #fafafa; border-top: 1px solid #eee; }
        .back-btn { font-size: 14px; text-decoration: none; color: #555; display: inline-block; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto;">
        <a href="{{ url('/history') }}" class="back-btn">&larr; Quay lại</a>
    </div>
    <div class="vnpay-container">
        <div class="vnpay-header">
            <img src="https://vnpay.vn/s1/statics.vnpay.vn/2023/9/06ncktiwd6dc1694418186_logo-vnpay.png" alt="VNPAY" class="vnpay-logo">
            <h2>Chọn phương thức thanh toán (Test)</h2>
        </div>
        <div class="vnpay-body">
            <div class="payment-option" style="opacity: 0.5; cursor: not-allowed;">
                <span>App Ngân hàng và Ví điện tử (VNPAY-QR)</span>
                <div class="payment-option-icons">QR</div>
            </div>
            <a href="{{ url('/vnpay-mock/ncb') }}?amount={{ request('amount') }}" class="payment-option">
                <span>Thẻ nội địa và tài khoản ngân hàng</span>
                <div class="payment-option-icons">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/Bank_icon_2.svg/512px-Bank_icon_2.svg.png" alt="Bank">
                </div>
            </a>
            <div class="payment-option" style="opacity: 0.5; cursor: not-allowed;">
                <span>Thẻ thanh toán quốc tế</span>
                <div class="payment-option-icons">VISA / MC</div>
            </div>
            <div class="payment-option" style="opacity: 0.5; cursor: not-allowed;">
                <span>App VNPAY</span>
                <div class="payment-option-icons">VNPAY</div>
            </div>
        </div>
        <div class="vnpay-footer">
            Phát triển bởi VNPAY © 2026
        </div>
    </div>
</body>
</html>
