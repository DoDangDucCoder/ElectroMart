# BỘ USE CASE PLANTUML MỞ RỘNG BÁM REPO CHO CHƯƠNG 3

File này là bản use case mở rộng theo hướng đầy đủ nhất có thể dựa trên các route và module thực tế đang có trong repo. So với file `thesis_report_usecase_plantuml_revised_guest.md`, bản này có phạm vi rộng hơn:

- vẫn giữ ba tác nhân nghiệp vụ chính là `Khách vãng lai`, `Khách hàng`, `Quản trị viên`
- bổ sung các tác nhân kỹ thuật khi thực sự cần để mô tả đúng hệ thống, gồm `Cổng thanh toán`, `Nhà cung cấp đăng nhập xã hội`, `API client`
- tách thêm các module còn thiếu như `blog`, `contact`, `video shop`, `quên mật khẩu`, `social login`, `admin auth recovery`, `REST API`
- giữ cấu trúc PlantUML để có thể xuất hình trực tiếp cho báo cáo hoặc chọn lọc lại nếu cần rút gọn

Đánh giá nhanh bộ cũ:

- Bộ cũ đủ cho bản báo cáo gọn và dễ bảo vệ.
- Tuy nhiên, nếu xét theo tiêu chí bám repo thật và muốn phủ gần hết các luồng chính, bộ cũ vẫn còn thiếu một số nhánh chức năng quan trọng.
- Vì vậy, nên xem file này là bản use case đầy đủ hơn, còn file cũ là bản tối giản hơn cho trường hợp muốn giảm số lượng hình.

Lưu ý: vẫn giữ quy ước để `Hình 3.5` đến `Hình 3.10` dành cho các sơ đồ kiến trúc, activity, sequence, ERD ở các file khác.

---

## Hình 3.1. Biểu đồ use case tổng quát mở rộng của hệ thống

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Khách vãng lai" as Guest
actor "Khách hàng" as Customer
actor "Quản trị viên" as Admin
actor "Cổng thanh toán" as PaymentGateway
actor "Nhà cung cấp\nđăng nhập xã hội" as OAuthProvider
actor "API client" as ApiClient

rectangle "Hệ thống website thương mại điện tử Laravel 11" {
  usecase "Sử dụng phần công khai:\ntrang chủ, catalog, blog,\nvideo, contact" as UC1
  usecase "Quản lý tài khoản\nvà xác thực khách hàng" as UC2
  usecase "Quản lý giỏ hàng,\ncheckout và đơn hàng" as UC3
  usecase "Tương tác hỗ trợ,\nbình luận, đánh giá, chat" as UC4
  usecase "Quản trị catalog,\nnội dung và vận hành" as UC5
  usecase "Sử dụng REST API\ncông khai và bảo vệ" as UC6
  usecase "Thanh toán qua\ncổng ngoài" as UC7
  usecase "Đăng nhập bằng\nmạng xã hội" as UC8
}

Guest --> UC1
Guest --> UC2

Customer --> UC1
Customer --> UC2
Customer --> UC3
Customer --> UC4

Admin --> UC5
Admin --> UC4

ApiClient --> UC6
PaymentGateway --> UC7
OAuthProvider --> UC8

UC7 ..> UC3 : <<extend>>
UC8 ..> UC2 : <<extend>>
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ tổng quát mở rộng giữ ba tác nhân nghiệp vụ chính nhưng bổ sung thêm ba tác nhân kỹ thuật để mô tả đúng hơn các tích hợp thực tế của hệ thống. Cách biểu diễn này phù hợp khi muốn bám sát repo, đặc biệt ở các luồng thanh toán, đăng nhập xã hội và REST API.

---

## Hình 3.2. Biểu đồ use case phần khách vãng lai mở rộng

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Khách vãng lai" as Guest

rectangle "Phần sử dụng công khai của hệ thống" {
  usecase "Xem trang chủ,\nflash sale, load thêm sản phẩm" as UC1
  usecase "Duyệt danh mục,\nthương hiệu, tag" as UC2
  usecase "Tìm kiếm sản phẩm" as UC3
  usecase "Nhận gợi ý tìm kiếm động" as UC4
  usecase "Xem chi tiết,\nquickview, thông số" as UC5
  usecase "So sánh sản phẩm" as UC6
  usecase "Đọc blog,\nvideo shop, trang liên hệ" as UC7
  usecase "Đổi ngôn ngữ giao diện" as UC8
  usecase "Mở trang đăng ký,\nđăng nhập" as UC9
}

Guest --> UC1
Guest --> UC2
Guest --> UC3
Guest --> UC5
Guest --> UC6
Guest --> UC7
Guest --> UC8
Guest --> UC9

UC4 ..> UC3 : <<extend>>
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ này mở rộng hơn bản cũ vì không chỉ bám các route catalog mà còn tính tới nội dung công khai như blog, video shop, contact page và đổi ngôn ngữ. Nhóm route tiêu biểu gồm `/`, `/tim-kiem`, `/autocomplete-ajax`, `/danh-muc/{slug_category_product}`, `/thuong-hieu/{brandSlug}`, `/tag/{product_tag}`, `/video-shop`, `/lien-he`, `lang/{locale}`.

---

## Hình 3.3. Biểu đồ use case phần khách hàng mở rộng

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Khách hàng" as Customer

rectangle "Phần sử dụng của khách hàng đã xác thực" {
  usecase "Đăng xuất và duy trì\nphiên đăng nhập" as UC1
  usecase "Quản lý giỏ hàng\nvà coupon" as UC2
  usecase "Checkout,\nthanh toán, đặt hàng" as UC3
  usecase "Xem lịch sử,\nchi tiết và hủy đơn" as UC4
  usecase "Gửi bình luận,\nđánh giá sao" as UC5
  usecase "Chat hỗ trợ khách hàng" as UC6
}

Customer --> UC1
Customer --> UC2
Customer --> UC3
Customer --> UC4
Customer --> UC5
Customer --> UC6
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ khách hàng mở rộng gom các thao tác sau xác thực thành các nhóm nghiệp vụ lớn hơn để nhìn hệ thống rõ hơn ở cấp actor. Các use case này tiếp tục được tách nhỏ ở các hình module phía sau.

---

## Hình 3.4. Biểu đồ use case phần quản trị viên mở rộng

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Quản trị viên" as Admin

rectangle "Phần quản trị hệ thống" {
  usecase "Xác thực admin,\nkhôi phục mật khẩu,\nworkspace" as UC1
  usecase "Quản lý catalog,\nthuộc tính, media,\ndocument, comment" as UC2
  usecase "Quản lý nội dung,\nslider, video,\nsite info, mail preview" as UC3
  usecase "Quản lý đơn hàng,\ndelivery, coupon,\nemail marketing" as UC4
  usecase "Quản lý user,\nrole, permission,\nbáo cáo, chat inbox" as UC5
}

Admin --> UC1
Admin --> UC2
Admin --> UC3
Admin --> UC4
Admin --> UC5
@enduml
```

**Mô tả dưới hình:**  
Bản mở rộng của actor quản trị viên phản ánh rõ hơn phần vận hành thật của repo. Khu vực admin không chỉ có CRUD sản phẩm mà còn có xác thực riêng, khôi phục tài khoản, inbox hỗ trợ, mail preview, report và công cụ workspace.

---

## Hình 3.11. Biểu đồ use case module tìm kiếm và khám phá sản phẩm

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Khách vãng lai" as Guest
actor "Khách hàng" as Customer

rectangle "Module tìm kiếm và khám phá sản phẩm" {
  usecase "Tìm kiếm sản phẩm\ntheo từ khóa" as UC1
  usecase "Nhận gợi ý tìm kiếm động" as UC2
  usecase "Xem sản phẩm\ntheo danh mục" as UC3
  usecase "Xem sản phẩm\ntheo thương hiệu" as UC4
  usecase "Xem sản phẩm\ntheo tag" as UC5
  usecase "Xem recommendation,\nflash sale, tab sản phẩm" as UC6
}

Guest --> UC1
Guest --> UC3
Guest --> UC4
Guest --> UC5
Guest --> UC6

Customer --> UC1
Customer --> UC3
Customer --> UC4
Customer --> UC5
Customer --> UC6

UC2 ..> UC1 : <<extend>>
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ này bám các route `/tim-kiem`, `/autocomplete-ajax`, `/goi-y-san-pham`, `/danh-muc/{slug_category_product}`, `/thuong-hieu/{brandSlug}`, `/tag/{product_tag}`, `/product-tabs`. Đây là cụm chức năng quan trọng vì website bán thiết bị công nghệ có hành vi khám phá và so sánh rất cao.

---

## Hình 3.12. Biểu đồ use case module chi tiết sản phẩm, đánh giá và so sánh

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Khách vãng lai" as Guest
actor "Khách hàng" as Customer

rectangle "Module chi tiết sản phẩm, đánh giá và so sánh" {
  usecase "Xem chi tiết sản phẩm" as UC1
  usecase "Xem quickview" as UC2
  usecase "Đọc bình luận,\nđánh giá và thông số" as UC3
  usecase "Xem media,\nvideo, tài liệu" as UC4
  usecase "Gửi bình luận" as UC5
  usecase "Gửi đánh giá sao" as UC6
  usecase "Thêm sản phẩm vào\nso sánh và xem bảng so sánh" as UC7
}

Guest --> UC1
Guest --> UC2
Guest --> UC3
Guest --> UC4
Guest --> UC7

Customer --> UC1
Customer --> UC2
Customer --> UC3
Customer --> UC4
Customer --> UC5
Customer --> UC6
Customer --> UC7
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ này phủ các route `/chi-tiet/{product_slug}`, `/quickview`, `/load-comment`, `/send-comment`, `/insert-rating`, `/so-sanh`, và cả phần hiển thị media/video/tài liệu trên trang sản phẩm. So với bản cũ, phần compare đã được gộp vào chung module khám phá sâu sản phẩm để bám hành vi người dùng hơn.

---

## Hình 3.13. Biểu đồ use case module nội dung công khai: blog, contact, video

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Khách vãng lai" as Guest
actor "Khách hàng" as Customer

rectangle "Module nội dung công khai" {
  usecase "Xem trang liên hệ" as UC1
  usecase "Xem danh mục bài viết" as UC2
  usecase "Đọc bài viết" as UC3
  usecase "Xem video shop" as UC4
  usecase "Theo dõi lượt xem video" as UC5
  usecase "Xem mục yêu thích" as UC6
}

Guest --> UC1
Guest --> UC2
Guest --> UC3
Guest --> UC4
Guest --> UC6

Customer --> UC1
Customer --> UC2
Customer --> UC3
Customer --> UC4
Customer --> UC6

UC5 ..> UC4 : <<include>>
@enduml
```

**Mô tả dưới hình:**  
Đây là nhóm chức năng thường bị bỏ quên nếu chỉ nhìn dự án như website bán hàng thuần. Biểu đồ bám các route `/lien-he`, `/danh-muc-bai-viet/{post_slug}`, `/bai-viet/{post_slug}`, `/video-shop`, `/watch-video`, `/yeu-thich`.

---

## Hình 3.14. Biểu đồ use case module giỏ hàng và coupon

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Khách vãng lai" as Guest
actor "Khách hàng" as Customer

rectangle "Module giỏ hàng và coupon" {
  usecase "Thêm sản phẩm\nvào giỏ hàng" as UC1
  usecase "Cập nhật số lượng" as UC2
  usecase "Xóa sản phẩm\nkhỏi giỏ hàng" as UC3
  usecase "Xem quick cart,\nhover cart, cart menu" as UC4
  usecase "Xem trang giỏ hàng" as UC5
  usecase "Làm trống giỏ hàng" as UC6
  usecase "Áp dụng mã giảm giá" as UC7
  usecase "Hủy mã giảm giá" as UC8
}

Guest --> UC1
Guest --> UC2
Guest --> UC3
Guest --> UC4
Guest --> UC5
Guest --> UC6

Customer --> UC1
Customer --> UC2
Customer --> UC3
Customer --> UC4
Customer --> UC5
Customer --> UC6
Customer --> UC7
Customer --> UC8
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ này bám các route `/add-cart-ajax`, `/save-cart`, `/update-cart`, `/update-cart-quantity`, `/show_quick_cart`, `/hover-cart`, `/show-cart-menu`, `/gio-hang`, `POST/DELETE /del-all-product`, `/check-coupon`, `DELETE /unset-coupon`.

---

## Hình 3.15. Biểu đồ use case module tài khoản khách hàng, khôi phục mật khẩu và đăng nhập xã hội

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Khách vãng lai" as Guest
actor "Khách hàng" as Customer
actor "Nhà cung cấp\nđăng nhập xã hội" as OAuthProvider

rectangle "Module tài khoản khách hàng" {
  usecase "Đăng ký tài khoản" as UC1
  usecase "Đăng nhập hệ thống" as UC2
  usecase "Đăng nhập bằng Facebook" as UC3
  usecase "Đăng nhập bằng Google" as UC4
  usecase "Quên mật khẩu" as UC5
  usecase "Đặt lại mật khẩu" as UC6
  usecase "Đăng xuất" as UC7
  usecase "Điều hướng lại đúng đích\nsau khi đăng nhập" as UC8
}

Guest --> UC1
Guest --> UC2
Guest --> UC5
Guest --> UC6

Customer --> UC7

OAuthProvider --> UC3
OAuthProvider --> UC4

UC3 ..> UC2 : <<extend>>
UC4 ..> UC2 : <<extend>>
UC8 ..> UC2 : <<extend>>
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ này mở rộng rõ hơn phần tài khoản vì repo có đủ đăng ký, đăng nhập, quên mật khẩu, đặt lại mật khẩu, đăng nhập Google/Facebook và cơ chế redirect quay lại đúng đích sau đăng nhập. Các route bám theo gồm `/add-customer`, `/login-customer`, `/dang-nhap`, `/dang-xuat-khach-hang`, `/quen-mat-khau`, `/recover-pass`, `/reset-new-pass`, `/login-facebook-customer`, `/login-customer-google`.

---

## Hình 3.16. Biểu đồ use case module checkout, thanh toán và lịch sử đơn hàng

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Khách hàng" as Customer
actor "Cổng thanh toán" as PaymentGateway

rectangle "Module checkout, thanh toán và lịch sử đơn hàng" {
  usecase "Vào bước checkout" as UC1
  usecase "Tính phí vận chuyển" as UC2
  usecase "Chọn địa chỉ giao hàng" as UC3
  usecase "Xác nhận đơn hàng" as UC4
  usecase "Thanh toán" as UC5
  usecase "Thanh toán qua PayPal" as UC6
  usecase "Thanh toán qua VNPay" as UC7
  usecase "Thanh toán qua MoMo" as UC8
  usecase "Xem lịch sử đơn hàng" as UC9
  usecase "Xem chi tiết đơn hàng" as UC10
  usecase "Hủy đơn hàng" as UC11
}

Customer --> UC1
Customer --> UC2
Customer --> UC3
Customer --> UC4
Customer --> UC5
Customer --> UC9
Customer --> UC10
Customer --> UC11

PaymentGateway --> UC6
PaymentGateway --> UC7
PaymentGateway --> UC8

UC6 ..> UC5 : <<extend>>
UC7 ..> UC5 : <<extend>>
UC8 ..> UC5 : <<extend>>
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ này là phần còn thiếu khá rõ trong bản gọn vì repo có thanh toán qua nhiều cổng ngoài. Các route tiêu biểu gồm `/checkout`, `/payment`, `/calculate-fee`, `/select-delivery-home`, `/confirm-order`, `/process-transaction`, `/vnpay_payment`, `/momo_payment`, `/history`, `/view-history-order/{order_code}`, `/huy-don-hang`.

---

## Hình 3.17. Biểu đồ use case module chat hỗ trợ khách hàng

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Khách vãng lai" as Guest
actor "Khách hàng" as Customer
actor "Quản trị viên" as Admin

rectangle "Module chat hỗ trợ khách hàng" {
  usecase "Mở hội thoại hỗ trợ" as UC1
  usecase "Gửi câu hỏi" as UC2
  usecase "Nhận phản hồi" as UC3
  usecase "Lưu lịch sử hội thoại" as UC4
  usecase "Theo dõi chat inbox" as UC5
  usecase "Phân công, trả lời,\nđổi trạng thái hội thoại" as UC6
}

Guest --> UC1
Guest --> UC2
Guest --> UC3

Customer --> UC1
Customer --> UC2
Customer --> UC3
Customer --> UC4

Admin --> UC5
Admin --> UC6
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ chat hỗ trợ bám cả phần storefront lẫn admin inbox. Các route liên quan gồm `/chatbot/conversation`, `/chatbot/messages`, `/chat-inbox`, `/chat-inbox/conversations/{conversation}`, `/chat-inbox/conversations/{conversation}/reply`, `/assign`, `/status`.

---

## Hình 3.18. Biểu đồ use case module xác thực và workspace quản trị

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Quản trị viên" as Admin

rectangle "Module xác thực và workspace quản trị" {
  usecase "Đăng nhập admin" as UC1
  usecase "Đăng ký admin" as UC2
  usecase "Quên mật khẩu admin" as UC3
  usecase "Đặt lại mật khẩu admin" as UC4
  usecase "Xem dashboard" as UC5
  usecase "Quick search" as UC6
  usecase "Xem và cập nhật profile" as UC7
  usecase "Xem settings" as UC8
  usecase "Đăng xuất" as UC9
}

Admin --> UC1
Admin --> UC2
Admin --> UC3
Admin --> UC4
Admin --> UC5
Admin --> UC6
Admin --> UC7
Admin --> UC8
Admin --> UC9
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ này đầy đủ hơn bản cũ vì đã đưa cả nhánh `register`, `forgot password`, `reset password` của admin vào. Các route bám theo gồm `/admin`, `/admin/register`, `/admin/forgot-password`, `/admin/reset-password/{token}`, `/auth-login`, `/dashboard`, `/admin/quick-search`, `/admin/profile`, `/admin/settings`, `/logout-auth`.

---

## Hình 3.19. Biểu đồ use case module quản lý catalog, sản phẩm, media và comment

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Quản trị viên" as Admin

rectangle "Module quản lý catalog, sản phẩm, media và comment" {
  usecase "Quản lý danh mục sản phẩm" as UC1
  usecase "Quản lý thương hiệu" as UC2
  usecase "Quản lý sản phẩm" as UC3
  usecase "Quản lý thuộc tính\nvà biến thể" as UC4
  usecase "Quản lý gallery ảnh" as UC5
  usecase "Quản lý document" as UC6
  usecase "Kiểm duyệt và phản hồi comment" as UC7
  usecase "Upload ảnh cho editor" as UC8
}

Admin --> UC1
Admin --> UC2
Admin --> UC3
Admin --> UC4
Admin --> UC5
Admin --> UC6
Admin --> UC7
Admin --> UC8
@enduml
```

**Mô tả dưới hình:**  
Đây là một trong các module lớn nhất của admin. Nó phủ các route `catalog/categories`, `catalog/brands`, `/admin/products-management`, `/admin/categories-management`, `/add-product`, `/product-attribute/{product_id}`, `/attribute`, `/add-gallery/{product_id}`, `download_document/{path}/{name}`, `delete_document/{path}`, `/comment`, `/reply-comment`, `/uploads-ckeditor`.

---

## Hình 3.20. Biểu đồ use case module quản lý nội dung, site info và media marketing

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Quản trị viên" as Admin

rectangle "Module quản lý nội dung, site info và media marketing" {
  usecase "Quản lý danh mục bài viết" as UC1
  usecase "Quản lý bài viết" as UC2
  usecase "Quản lý video" as UC3
  usecase "Quản lý slider" as UC4
  usecase "Quản lý thông tin liên hệ,\nicon, đối tác, site info" as UC5
  usecase "Xem mail preview\nvà render mail" as UC6
}

Admin --> UC1
Admin --> UC2
Admin --> UC3
Admin --> UC4
Admin --> UC5
Admin --> UC6
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ này bám các route `/add-category-post`, `/all-category-post`, `/add-post`, `/all-post`, `/video`, `/manage-slider`, `/information`, `/list-nut`, `/list-doitac`, `/save-info`, `/mail-preview`, `/mail-preview/render`.

---

## Hình 3.21. Biểu đồ use case module vận hành đơn hàng, delivery, coupon và email marketing

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Quản trị viên" as Admin

rectangle "Module vận hành đơn hàng, delivery, coupon và email marketing" {
  usecase "Quản lý danh sách đơn hàng" as UC1
  usecase "Xem chi tiết và in đơn" as UC2
  usecase "Cập nhật số lượng,\ntrạng thái đơn hàng" as UC3
  usecase "Quản lý delivery\nvà phí vận chuyển" as UC4
  usecase "Quản lý coupon" as UC5
  usecase "Gửi coupon và\nchiến dịch email" as UC6
}

Admin --> UC1
Admin --> UC2
Admin --> UC3
Admin --> UC4
Admin --> UC5
Admin --> UC6
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ này bám các route `/admin/orders-management`, `/print-order/{checkout_code}`, `/view-order/{order_code}`, `/update-order-qty`, `/update-qty`, `/delivery`, `/select-delivery`, `/insert-delivery`, `/select-feeship`, `/insert-coupon`, `/list-coupon`, `/send-coupon`, `/send-coupon-vip`.

---

## Hình 3.22. Biểu đồ use case module người dùng, phân quyền, báo cáo và chat inbox quản trị

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Quản trị viên" as Admin

rectangle "Module người dùng, phân quyền, báo cáo và chat inbox" {
  usecase "Quản lý users" as UC1
  usecase "Gán role cho user" as UC2
  usecase "Gán permission cho user" as UC3
  usecase "Quản lý vai trò và quyền" as UC4
  usecase "Xem báo cáo lợi nhuận" as UC5
  usecase "Xem báo cáo product discovery" as UC6
  usecase "Theo dõi chat inbox" as UC7
}

Admin --> UC1
Admin --> UC2
Admin --> UC3
Admin --> UC4
Admin --> UC5
Admin --> UC6
Admin --> UC7
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ này gom các route `users`, `assign-roles`, `/create-spatie/{admin_id}`, `/create-permission/{admin_id}`, `/assign-role/{admin_id}`, `/assign-permission/{admin_id}`, `/index-spatie`, `/product-profit`, `/product-discovery-report`, `/chat-inbox`.

---

## Hình 3.23. Biểu đồ use case module Public API

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "API client" as ApiClient

rectangle "Module Public API" {
  usecase "Lấy danh sách,\nchi tiết sản phẩm" as UC1
  usecase "Tìm kiếm, latest,\nfeatured, recommendation" as UC2
  usecase "Lấy category,\nbrand, category tree" as UC3
  usecase "Đọc review,\ncoupon, blog, settings" as UC4
  usecase "Xem health check\nvà tài liệu API" as UC5
  usecase "Đăng ký, đăng nhập,\nrefresh token" as UC6
  usecase "Sử dụng chatbot API" as UC7
}

ApiClient --> UC1
ApiClient --> UC2
ApiClient --> UC3
ApiClient --> UC4
ApiClient --> UC5
ApiClient --> UC6
ApiClient --> UC7
@enduml
```

**Mô tả dưới hình:**  
Đây là phần mà bản use case gọn trước đó chưa biểu diễn. Biểu đồ bám các route `/api/v1/products`, `/latest`, `/featured`, `/search`, `/recommendations`, `/categories`, `/brands`, `/reviews`, `/coupons`, `/blog`, `/settings`, `/health`, `/docs`, `/chatbot`, `/auth/login`, `/auth/register`, `/auth/refresh`.

---

## Hình 3.24. Biểu đồ use case module Protected API cho khách hàng

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "API client đã xác thực" as ApiClient

rectangle "Module Protected API cho khách hàng" {
  usecase "Xem và cập nhật\nthông tin người dùng" as UC1
  usecase "Đăng xuất token" as UC2
  usecase "Quản lý giỏ hàng" as UC3
  usecase "Tạo, xem,\nhủy đơn hàng" as UC4
  usecase "Tạo, sửa,\nxóa đánh giá sản phẩm" as UC5
}

ApiClient --> UC1
ApiClient --> UC2
ApiClient --> UC3
ApiClient --> UC4
ApiClient --> UC5
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ này bám các route bảo vệ bởi `auth:sanctum` như `/api/v1/user`, `/api/v1/auth/logout`, `/api/v1/cart`, `/api/v1/orders`, `/api/v1/reviews`.

---

## Hình 3.25. Biểu đồ use case module Admin API

```plantuml
@startuml
left to right direction
skinparam backgroundColor white
skinparam shadowing false
skinparam packageStyle rectangle
skinparam actorStyle awesome
skinparam usecase {
  BackgroundColor #F8FAFC
  BorderColor #1F2937
  ArrowColor #475569
  FontColor #111827
}
skinparam actor {
  BorderColor #1F2937
  FontColor #111827
}

actor "Admin API client" as AdminApiClient

rectangle "Module Admin API" {
  usecase "Đăng nhập và kiểm tra\ntrạng thái admin API" as UC1
  usecase "Quản lý products,\ncategories, brands" as UC2
  usecase "Quản lý orders\nvà trạng thái đơn" as UC3
  usecase "Xem statistics,\nsales, product discovery" as UC4
  usecase "Chạy tác vụ tối ưu\ncơ sở dữ liệu" as UC5
}

AdminApiClient --> UC1
AdminApiClient --> UC2
AdminApiClient --> UC3
AdminApiClient --> UC4
AdminApiClient --> UC5
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ này bám các route `/api/v1/admin/auth/login`, `/api/v1/admin/auth/me`, `/api/v1/admin/products`, `/api/v1/admin/categories`, `/api/v1/admin/brands`, `/api/v1/admin/orders`, `/api/v1/admin/statistics`, `/optimize-database`, `/add-indexes`, `/slow-queries`.

---

## Kết luận cho bộ use case mở rộng

Nếu xét theo tiêu chí use case “đủ để trình bày gọn trong đồ án”, bộ file cũ đã ở mức dùng được. Tuy nhiên, nếu xét theo tiêu chí “đầy đủ nhất có thể” dựa trên repo hiện tại, bộ cũ chưa bao phủ hết các nhóm chức năng như:

- blog, contact, video shop, đổi ngôn ngữ
- quên mật khẩu, đặt lại mật khẩu, social login
- admin register, admin forgot/reset password
- thanh toán qua cổng ngoài
- public API, protected API, admin API

Vì vậy, file này nên được xem là bản mở rộng bám repo hơn. Khi đưa vào báo cáo Word, anh có thể:

- dùng toàn bộ nếu muốn thể hiện độ phủ use case tối đa
- hoặc chọn các hình trọng tâm như `3.1`, `3.2`, `3.3`, `3.4`, `3.14`, `3.15`, `3.16`, `3.19`, `3.21`, `3.22` nếu cần rút gọn

Trong trường hợp cần cân bằng giữa tính học thuật và độ dài báo cáo, cách tốt nhất là dùng file này làm nguồn chuẩn, sau đó chọn lọc các sơ đồ phù hợp nhất với Chương 3.
