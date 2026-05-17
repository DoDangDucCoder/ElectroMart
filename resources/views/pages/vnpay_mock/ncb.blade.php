<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán Ngân hàng NCB | VNPAY (Test)</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px; }
        .vnpay-container { max-width: 900px; margin: 0 auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; display: flex; }
        .vnpay-left { width: 35%; background: #fafafa; padding: 30px; border-right: 1px solid #eee; }
        .vnpay-right { width: 65%; padding: 40px; position: relative; }
        .vnpay-logo { height: 40px; margin-bottom: 30px; }
        
        .order-info h3 { margin-top: 0; font-size: 18px; color: #333; margin-bottom: 20px; font-weight: normal; }
        .info-row { margin-bottom: 15px; }
        .info-row span { display: block; font-size: 13px; color: #666; margin-bottom: 5px; }
        .info-row strong { display: block; font-size: 15px; color: #333; }
        .info-row .amount { font-size: 20px; color: #005A9C; }
        
        .form-header h2 { margin-top: 0; font-size: 22px; color: #333; font-weight: normal; margin-bottom: 30px; }
        .tabs { border-bottom: 1px solid #005A9C; margin-bottom: 25px; }
        .tab-active { display: inline-block; padding: 10px 20px; border-bottom: 3px solid #005A9C; color: #005A9C; font-weight: bold; margin-bottom: -1px; }
        
        .form-group { margin-bottom: 20px; position: relative; }
        .form-group label { display: block; font-size: 14px; color: #555; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 15px; box-sizing: border-box; outline: none; }
        .form-control:focus { border-color: #005A9C; }
        .ncb-logo { position: absolute; right: 15px; top: 38px; height: 20px; }
        
        .form-actions { display: flex; justify-content: space-between; margin-top: 40px; }
        .btn { padding: 12px 25px; border-radius: 4px; font-size: 16px; cursor: pointer; text-decoration: none; text-align: center; border: none; }
        .btn-cancel { background: #f5f5f5; color: #333; border: 1px solid #ddd; }
        .btn-continue { background: #005A9C; color: #fff; flex: 1; margin-left: 15px; }
        .btn-continue:hover { background: #004a80; }
        
        .terms-link { color: #005A9C; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; }
        
        /* Modal */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal-content { background: #fff; width: 500px; max-width: 90%; border-radius: 8px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-header h3 { margin: 0; font-size: 18px; }
        .close-btn { background: none; border: none; font-size: 20px; cursor: pointer; color: #888; }
        .modal-body { font-size: 14px; color: #444; line-height: 1.6; max-height: 300px; overflow-y: auto; margin-bottom: 20px; }
        .modal-footer { display: flex; justify-content: space-between; gap: 15px; }
        
        @media (max-width: 768px) {
            .vnpay-container { flex-direction: column; }
            .vnpay-left, .vnpay-right { width: 100%; padding: 20px; box-sizing: border-box; }
        }
    </style>
</head>
<body>
    <div style="max-width: 900px; margin: 0 auto; margin-bottom: 15px;">
        <a href="{{ url('/vnpay-mock/select-method') }}?amount={{ request('amount') }}" style="color: #555; text-decoration: none;">&larr; Quay lại</a>
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
                <h2>Thanh toán qua Ngân hàng NCB</h2>
            </div>
            
            <div class="tabs">
                <span class="tab-active">Thẻ nội địa</span>
            </div>
            
            <form action="{{ url('/vnpay-mock/otp') }}" method="GET">
                <input type="hidden" name="amount" value="{{ request('amount') }}">
                
                <div class="form-group">
                    <label>Số thẻ</label>
                    <input type="text" class="form-control" value="9704198526191432198" required>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/Bank_icon_2.svg/512px-Bank_icon_2.svg.png" class="ncb-logo" alt="NCB">
                </div>
                
                <div class="form-group">
                    <label>Tên chủ thẻ</label>
                    <input type="text" class="form-control" value="NGUYEN VAN A" required>
                </div>
                
                <div class="form-group">
                    <label>Ngày phát hành</label>
                    <input type="text" class="form-control" value="07/15" style="width: 200px;" required>
                </div>
                
                <div style="margin-top: 30px;">
                    <span class="terms-link" onclick="openModal()">&#128196; Điều kiện sử dụng dịch vụ</span>
                </div>
                
                <div class="form-actions">
                    <a href="{{ url('/account/orders') }}" class="btn btn-cancel">Hủy thanh toán</a>
                    <button type="submit" class="btn btn-continue">Tiếp tục</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal" id="termsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Điều khoản sử dụng</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p><i>(Người dùng đang thực hiện giao dịch trên môi trường kiểm thử của VNPAY, vui lòng không sử dụng ngoài mục đích kiểm thử và tự chịu trách nhiệm trong trường hợp Nhà cung cấp không giao hàng hóa, dịch vụ khi thanh toán thành công).</i></p>
                <p>Quý khách hàng đang sử dụng Dịch vụ Cổng thanh toán VNPAY được cung cấp bởi Công ty Cổ phần Giải pháp Thanh toán Việt Nam (VNPAY).</p>
                <p>Các dữ liệu cá nhân liên quan tới dịch vụ gồm: thông tin thẻ/tài khoản(*) và các dữ liệu cá nhân khác mà Quý khách cung cấp sẽ được VNPAY chuyển tới Ngân hàng phát hành/ Tổ chức chuyển mạch và Đơn vị bán hàng tương ứng cho mục đích cung cấp dịch vụ Cổng thanh toán VNPAY, bao gồm: Xử lý giao dịch thanh toán và cung ứng hàng hóa, dịch vụ; Quản lý rủi ro; Tra soát khiếu nại; Triển khai các chương trình ưu đãi; Giới thiệu dịch vụ và chăm sóc khách hàng.</p>
                <p>Quý Khách hàng vui lòng đọc các quyền, nghĩa vụ của chủ thể dữ liệu và các nội dung khác liên quan đến bảo vệ dữ liệu cá nhân của Quý Khách hàng tại "Chính sách bảo vệ dữ liệu cá nhân" được nêu chi tiết <a href="#">tại đây</a>.</p>
                <p><i>(*): Thông tin thẻ/tài khoản được quy định là dữ liệu cá nhân nhạy cảm (theo Nghị định số 13/2023/NĐ-CP được Chính phủ ban hành ngày 17/4/2023 về bảo vệ dữ liệu cá nhân.</i></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-cancel" style="flex:1" onclick="closeModal()">Không đồng ý</button>
                <button class="btn btn-continue" style="flex:1" onclick="closeModal()">Đồng ý & Tiếp tục</button>
            </div>
        </div>
    </div>
    
    <script>
        function openModal() { document.getElementById('termsModal').style.display = 'flex'; }
        function closeModal() { document.getElementById('termsModal').style.display = 'none'; }
    </script>
</body>
</html>
