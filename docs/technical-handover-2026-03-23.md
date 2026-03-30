# Technical Handover

Ngày: `2026-03-23`

## Phạm vi đã hoàn tất

- Cleanup sâu `storefront/admin surface`
- Dead-code cleanup theo nhiều phase trong [dead-code-audit-2026-03-22.md](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/docs/dead-code-audit-2026-03-22.md)
- Hardening thêm cho `checkout / order / document`
- Hardening thêm cho `mail legacy`

## Thay đổi chính

### Storefront và admin UI

- Storefront live đã được kéo về cùng shell/UX cho các flow chính: home, category, detail, cart, login checkout, checkout, search, history, order detail.
- Admin shell đã được tách partial, dọn JS theo slice và refresh các màn active chính.
- View preview/alias và prototype không còn dùng thật đã được xóa theo audit phase 1-7.

### Hardening mới trong vòng cuối

- [LegacyOrderWorkflowService.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/app/Application/Order/Services/LegacyOrderWorkflowService.php)
  - Tách phần workflow legacy `delete / update line qty / update order status / build admin view data` ra khỏi controller.
  - Gom logic finalize đơn legacy, inventory update và statistics update vào một chỗ để dễ test hơn.
  - Finalize đơn nhiều dòng sản phẩm giờ có validation đủ trước khi transaction commit.
- [LegacyOrderPresentationService.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/app/Application/Order/Services/LegacyOrderPresentationService.php)
  - Tách phần presentation/totals của đơn legacy cho `print_order_convert()` và `view_history_order()` legacy.
  - Gom cách tính `subtotal / discount / shipping / total` về một chỗ để tránh mỗi nhánh tính coupon/phí ship khác nhau.
  - Render HTML in đơn qua [print.blade.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/resources/views/admin/order/print.blade.php) thay vì nối string dài trong controller.
- [LegacyOrderHistoryService.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/app/Application/Order/Services/LegacyOrderHistoryService.php)
  - Tách phần legacy history mapping khỏi controller.
  - `history` list legacy giờ map qua service riêng và lấy đúng payment label từ `shipping_method`.
  - `view_history_order()` legacy dùng chung service này để build detail view thay vì tự map trong controller.
- [LegacyMailWorkflowService.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/app/Application/Shared/Services/LegacyMailWorkflowService.php)
  - Tách phần gửi coupon mail và khôi phục mật khẩu legacy khỏi controller.
  - `send_coupon` / `send_coupon_vip` giờ lấy coupon từ DB làm source of truth.
  - `recover_pass` / `reset_new_pass` giờ có success/failure path rõ hơn và không còn xử lý token/password trực tiếp rải rác trong controller.
- [LegacyCouponCampaignMail.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/app/Mail/LegacyCouponCampaignMail.php)
  - Bọc mail campaign coupon legacy thành `Mailable`.
  - Dùng `CouponCampaignMailData + markdown mail + theme laravel-shop`.
- [LegacyPasswordRecoveryMail.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/app/Mail/LegacyPasswordRecoveryMail.php)
  - Bọc mail quên mật khẩu legacy thành `Mailable`.
  - Dùng `PasswordRecoveryMailData + markdown mail + theme laravel-shop`.
- [config/mail.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/config/mail.php)
  - Mail markdown mặc định đã chuyển sang theme `laravel-shop`.
- [resources/views/mail/legacy](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/resources/views/mail/legacy)
  - Coupon, password recovery và order confirmation đã dùng markdown view chung.
- [resources/views/vendor/mail/html/themes/laravel-shop.css](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/resources/views/vendor/mail/html/themes/laravel-shop.css)
  - Theme mail riêng cho repo, đồng bộ màu và spacing của mail active.
- [resources/views/admin/mail/preview.blade.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/resources/views/admin/mail/preview.blade.php)
  - Tooling preview mail nằm trong admin shell, có iframe xem nhanh và link mở HTML trực tiếp.
- [OrderConfirmationMailData.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/app/Application/Shared/MailData/OrderConfirmationMailData.php)
  - Chuẩn hóa payload mail xác nhận đơn hàng để view render bằng object thay vì `shipping_array/cart_array/code`.
- [CouponCampaignMailData.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/app/Application/Shared/MailData/CouponCampaignMailData.php)
  - Gom toàn bộ copy/discount metadata của mail campaign vào DTO riêng.
- [PasswordRecoveryMailData.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/app/Application/Shared/MailData/PasswordRecoveryMailData.php)
  - Gom reset URL, email và title mail quên mật khẩu vào DTO riêng.
- [CheckoutController.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/app/Http/Controllers/CheckoutController.php)
  - `confirm_order()` giờ yêu cầu session customer hợp lệ và còn active.
  - `login_checkout()` và `checkout()` dùng cùng logic resolve customer session.
- [OrderController.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/app/Http/Controllers/OrderController.php)
  - `huy_don_hang()` chặn guest, chặn stale session, và không cho hủy đơn modern/legacy ở trạng thái hoàn tất hoặc đã hủy.
  - `history()` và `view_history_order()` redirect lại login khi session customer modern đã stale.
  - `view_order()` và `order_code()` trả failure path rõ hơn cho đơn legacy không tồn tại.
  - `update_qty()` validate input và trả `404 JSON` khi không tìm thấy dòng sản phẩm cần cập nhật.
  - `update_order_qty()` giờ validate payload, chặn finalize khi thiếu tồn kho, và không còn mutate dở dang trước khi trả lỗi.
  - `update_order_qty()` chỉ trừ kho/cộng sold/thống kê khi đơn legacy chuyển sang trạng thái hoàn tất lần đầu, tránh double-apply khi gọi lặp lại.
  - `update_order_qty()` cho đơn nhiều sản phẩm giờ aggregate tồn kho/thống kê trong cùng transaction.
  - `print_order()` trả `404` cho mã đơn legacy không tồn tại thay vì cố render PDF lỗi.
  - `history()` và `view_history_order()` legacy không còn tự map data lớn trong controller.
  - Alias `print_order_convert()` đã bị xóa vì không còn consumer.
- [DocumentController.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/app/Http/Controllers/DocumentController.php)
  - `download_document()` và `delete_document()` giờ guard path rõ hơn.
  - Chỉ cho thao tác trên file đang nằm trong danh sách document được quản lý.
  - Path traversal hoặc file không tồn tại không còn được chấp nhận ngầm.
- [MailController.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/app/Http/Controllers/MailController.php)
  - `send_coupon` / `send_coupon_vip` đã chuyển sang `FormRequest + service`.
  - `recover_pass` / `reset_new_pass` đã chuyển sang `FormRequest + service`.
  - `/quen-mat-khau` và `/update-new-pass` có page context an toàn hơn trên DB fresh và không còn dùng `$_GET` trong view.
  - Flow mail legacy không còn dùng `Mail::send(...)` trực tiếp trong controller/service.
  - Có thêm tooling `mail-preview` admin-only để QA mail runtime trực tiếp trong giao diện quản trị.

### Tooling/admin route cleanup gần nhất

- Đã xóa preview route `mail-example`.
- Đã xóa nhóm document utility route không có UI consumer:
  - `upload_file`
  - `upload_image`
  - `upload_video`
  - `create_document`
  - `create_sub_dir`
  - `create_folder`
  - `rename_folder`
  - `delete_folder`
  - `list_document`
- Giữ lại document routes còn dùng thật:
  - `read_data`
  - `download_document`
  - `delete_document`

## Test đã thêm hoặc mở rộng

- [CheckoutFlowTest.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/tests/Feature/CheckoutFlowTest.php)
  - guest/stale session không được confirm order
  - empty cart redirect/failure path
- [OrderHistoryPageTest.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/tests/Feature/OrderHistoryPageTest.php)
  - guest/stale session cancel
  - completed modern order không thể hủy
  - completed legacy order không thể hủy
- [AdminPermissionHardeningTest.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/tests/Feature/AdminPermissionHardeningTest.php)
  - editor bị chặn với document download/delete
- [AdminPanelTest.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/tests/Feature/AdminPanelTest.php)
  - document download/delete success path
  - document missing/traversal failure path
  - admin order legacy missing record failure path
  - admin order legacy insufficient stock failure path không làm thay đổi dữ liệu nửa chừng
  - admin order legacy finalize success path cập nhật kho/thống kê đúng và không double-apply khi finalize lặp lại
  - admin order legacy multi-line success path aggregate tồn kho/thống kê đúng
  - admin order legacy multi-line failure path không mutate nửa chừng nếu một dòng không đủ tồn kho
  - admin print-order `404` khi mã đơn không tồn tại
  - admin printable HTML nhiều sản phẩm tính coupon/phí ship đúng tổng thanh toán
- [OrderHistoryPageTest.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/tests/Feature/OrderHistoryPageTest.php)
  - legacy `view_history_order()` nhiều sản phẩm hiển thị đúng coupon/phí ship/tổng tiền
  - legacy `history()` nhiều sản phẩm với coupon cố định hiển thị đúng tổng tiền và payment method
- [LegacyMailFlowTest.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/tests/Feature/LegacyMailFlowTest.php)
  - render an toàn cho `/quen-mat-khau` và `/update-new-pass`
  - recover password unknown/success path
  - reset password invalid token/success path
  - admin coupon campaign missing coupon/success path
- [LegacyMailTemplateRenderTest.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/tests/Feature/LegacyMailTemplateRenderTest.php)
  - mail template markdown order/coupon vẫn render đúng sau refactor
- [LegacyMailSnapshotTest.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/tests/Feature/LegacyMailSnapshotTest.php)
  - snapshot hash cho coupon thường, coupon VIP, password recovery, order confirmation COD và order confirmation ATM
  - hỗ trợ regenerate fixture qua `UPDATE_MAIL_SNAPSHOTS=1`
- [AdminPanelTest.php](C:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/tests/Feature/AdminPanelTest.php)
  - có coverage cho route `mail-preview` và `mail-preview/render`

## Verify gần nhất

```powershell
php artisan test tests/Feature/LegacyMailFlowTest.php tests/Feature/LegacyMailTemplateRenderTest.php tests/Feature/LegacyMailSnapshotTest.php tests/Feature/AdminPanelTest.php tests/Feature/OrderHistoryPageTest.php tests/Feature/AdminSecurityTest.php tests/Feature/AdminPermissionHardeningTest.php tests/Feature/CheckoutFlowTest.php
```

Kết quả:

- `67 passed / 543 assertions`

## Rủi ro còn lại thật sự

- `Checkout` vẫn là vùng lai giữa session cart storefront và schema order mới; nếu refactor tiếp cần soi kỹ contract JS ở storefront.
- `OrderController` legacy không còn là điểm rủi ro chính cho history/totals/finalize.
- `MailController` đã được harden phần coupon/password recovery và đã chuyển sang `Mailable + typed DTO`.
- Mail legacy active đã chuyển sang markdown mail/theme chung, đã có snapshot hash test, có route preview nội bộ cho admin, và không còn giữ wrapper HTML preview cũ trong `resources/views/pages/...`.
- `DocumentController` hiện đã an toàn hơn, nhưng vẫn là file manager rất mỏng; nếu muốn mở rộng tính năng upload/manage thực sự thì nên tách service/request riêng thay vì nhồi tiếp vào controller.
- Repo vẫn còn một số controller/tooling ít dùng nhưng còn route active; các phần này không còn là dead rõ ràng, nên nếu muốn cắt tiếp phải audit theo usage thật chứ không xóa cơ học.

## Khuyến nghị bước tiếp theo

1. Nếu ưu tiên business: tăng thêm regression cho `recover_pass` nhánh hết hạn token hoặc email duplicate nếu flow này còn consumer thật.
2. Nếu ưu tiên kỹ thuật: markdown/theme mail và snapshot test đã có; bước tiếp theo nếu cần chỉ còn polish copy hoặc tách thêm preview tooling riêng.
3. Nếu ưu tiên cleanup: chỉ tiếp tục với route/tooling có evidence là không còn UI consumer hoặc không còn test/value vận hành.
