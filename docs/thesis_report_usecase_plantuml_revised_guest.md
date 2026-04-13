# BỘ USE CASE PLANTUML ĐÃ CHỈNH LẠI CHO CHƯƠNG 3

Tài liệu này là phiên bản use case PlantUML được chỉnh lại theo hướng phù hợp hơn với báo cáo đồ án. So với bản trước, file này có ba thay đổi chính:

- bổ sung tác nhân `Khách vãng lai`
- bỏ các tác nhân hỗ trợ ngoài hệ thống như cổng thanh toán, dịch vụ đăng nhập xã hội, chatbot hay API client
- tách rõ use case theo từng phần: khách vãng lai, khách hàng và quản trị viên

Vì vậy, nếu anh muốn bộ sơ đồ nhìn “đồ án” hơn, gọn hơn và bám sát phân tích tác nhân chính của hệ thống, nên ưu tiên dùng file này khi xuất hình.

Lưu ý: các số hình từ `3.5` đến `3.10` vẫn được dành cho các biểu đồ kiến trúc, ERD, activity và sequence trong các file khác, nên bộ use case module tiếp tục bắt đầu từ `Hình 3.11`.

---

## Hình 3.1. Biểu đồ use case tổng quát của hệ thống

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

rectangle "Hệ thống website thương mại điện tử Laravel 11" {
  usecase "Xem và tìm kiếm\nsản phẩm" as UC1
  usecase "Xem chi tiết,\nso sánh và đánh giá" as UC2
  usecase "Quản lý tài khoản\nkhách hàng" as UC3
  usecase "Quản lý giỏ hàng,\ncheckout và đơn hàng" as UC4
  usecase "Tra cứu lịch sử,\nchat hỗ trợ" as UC5
  usecase "Quản lý catalog\nvà nội dung" as UC6
  usecase "Quản lý đơn hàng,\ncoupon và delivery" as UC7
  usecase "Quản lý người dùng,\nphân quyền và báo cáo" as UC8
}

Guest --> UC1
Guest --> UC2
Guest --> UC3

Customer --> UC1
Customer --> UC2
Customer --> UC3
Customer --> UC4
Customer --> UC5

Admin --> UC6
Admin --> UC7
Admin --> UC8
Admin --> UC5
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ use case tổng quát đã được chỉnh lại theo hướng chỉ giữ ba tác nhân chính của hệ thống: khách vãng lai, khách hàng và quản trị viên. Cách tách này phản ánh đúng thực tế vận hành của website hơn vì người truy cập chưa đăng nhập vẫn có thể duyệt sản phẩm và tìm kiếm, còn các thao tác nhạy cảm hơn như checkout, xem lịch sử đơn hàng hay quản trị hệ thống được phân cho các tác nhân tương ứng.

---

## Hình 3.2. Biểu đồ use case phần khách vãng lai

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
  usecase "Xem trang chủ,\ndanh mục và thương hiệu" as UC1
  usecase "Tìm kiếm và nhận\ngợi ý tìm kiếm" as UC2
  usecase "Xem chi tiết sản phẩm" as UC3
  usecase "Đọc bình luận,\nđánh giá, thông số" as UC4
  usecase "So sánh sản phẩm" as UC5
  usecase "Mở trang đăng ký,\nđăng nhập" as UC6
}

Guest --> UC1
Guest --> UC2
Guest --> UC3
Guest --> UC4
Guest --> UC5
Guest --> UC6
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ use case phần khách vãng lai nhấn mạnh rằng website không chỉ phục vụ người dùng đã đăng nhập. Một người truy cập chưa có tài khoản vẫn có thể vào trang chủ, duyệt sản phẩm, tìm kiếm, đọc thông tin, so sánh và sau đó mới quyết định chuyển sang bước xác thực. Đây là lý do tác nhân khách vãng lai cần được tách riêng trong báo cáo.

---

## Hình 3.3. Biểu đồ use case phần khách hàng

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
  usecase "Đăng nhập,\nđăng xuất tài khoản" as UC1
  usecase "Thêm, cập nhật,\nxóa giỏ hàng" as UC2
  usecase "Áp dụng coupon" as UC3
  usecase "Checkout và\nxác nhận đơn hàng" as UC4
  usecase "Theo dõi lịch sử\nvà hủy đơn hàng" as UC5
  usecase "Gửi bình luận,\nđánh giá và chat hỗ trợ" as UC6
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
Biểu đồ use case phần khách hàng gom các thao tác cần xác thực, đặc biệt là checkout, lịch sử đơn hàng và các tương tác có tính cá nhân. Cách tách này giúp báo cáo thể hiện rõ ranh giới giữa phần dùng công khai và phần dùng sau đăng nhập.

---

## Hình 3.4. Biểu đồ use case phần quản trị viên

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
  usecase "Đăng nhập admin\nvà vào dashboard" as UC1
  usecase "Quản lý catalog,\nmedia và comment" as UC2
  usecase "Quản lý nội dung,\nvideo, slider" as UC3
  usecase "Quản lý đơn hàng,\ncoupon, delivery" as UC4
  usecase "Quản lý người dùng,\nvai trò, quyền" as UC5
  usecase "Xem báo cáo,\nchat inbox, settings" as UC6
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
Biểu đồ use case phần quản trị viên cho thấy khu vực admin là một phân hệ đầy đủ chứ không chỉ là nơi thêm hoặc sửa sản phẩm. Ngoài catalog, quản trị viên còn phải xử lý nội dung, đơn hàng, phân quyền, báo cáo và các công cụ vận hành nội bộ.

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
  usecase "Xem recommendation\nvà flash sale" as UC6
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
@enduml
```

**Mô tả dưới hình:**  
Module tìm kiếm và khám phá sản phẩm là vùng dùng công khai của website nên cả khách vãng lai và khách hàng đều có thể truy cập. Biểu đồ này bám các route như `/tim-kiem`, `/autocomplete-ajax`, `/goi-y-san-pham`, `/danh-muc/{slug_category_product}`, `/thuong-hieu/{brandSlug}` và `/tag/{product_tag}`.

---

## Hình 3.12. Biểu đồ use case module chi tiết sản phẩm và tương tác

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

rectangle "Module chi tiết sản phẩm và tương tác" {
  usecase "Xem chi tiết sản phẩm" as UC1
  usecase "Xem quickview" as UC2
  usecase "Đọc bình luận\nsản phẩm" as UC3
  usecase "Xem media,\nvideo và thông số" as UC4
  usecase "Gửi bình luận" as UC5
  usecase "Gửi đánh giá sao" as UC6
}

Guest --> UC1
Guest --> UC2
Guest --> UC3
Guest --> UC4

Customer --> UC1
Customer --> UC2
Customer --> UC3
Customer --> UC4
Customer --> UC5
Customer --> UC6
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ này tách rõ hai lớp thao tác: khách vãng lai chỉ đọc và xem thông tin, trong khi khách hàng đã xác thực có thể gửi bình luận và đánh giá. Cách phân tách này bám tốt hơn với các route như `/chi-tiet/{product_slug}`, `/quickview`, `/load-comment`, `/send-comment`, `/insert-rating`.

---

## Hình 3.13. Biểu đồ use case module so sánh sản phẩm

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

rectangle "Module so sánh sản phẩm" {
  usecase "Chọn sản phẩm\ncần so sánh" as UC1
  usecase "Thêm sản phẩm vào\ndanh sách so sánh" as UC2
  usecase "Xem bảng so sánh" as UC3
  usecase "Đối chiếu cấu hình\nvà giá" as UC4
  usecase "Quay lại xem chi tiết\nhoặc mua hàng" as UC5
}

Guest --> UC1
Guest --> UC2
Guest --> UC3
Guest --> UC4
Guest --> UC5

Customer --> UC1
Customer --> UC2
Customer --> UC3
Customer --> UC4
Customer --> UC5
@enduml
```

**Mô tả dưới hình:**  
So sánh sản phẩm là nghiệp vụ phù hợp với cả khách vãng lai lẫn khách hàng vì đây là bước tiền quyết định mua. Biểu đồ bám module và route `/so-sanh` cùng `CompareController`.

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
  usecase "Xóa sản phẩm khỏi giỏ" as UC3
  usecase "Xem quick cart\nvà hover cart" as UC4
  usecase "Xem trang giỏ hàng" as UC5
  usecase "Áp dụng mã giảm giá" as UC6
}

Guest --> UC1
Guest --> UC2
Guest --> UC3
Guest --> UC4
Guest --> UC5

Customer --> UC1
Customer --> UC2
Customer --> UC3
Customer --> UC4
Customer --> UC5
Customer --> UC6
@enduml
```

**Mô tả dưới hình:**  
Biểu đồ này thể hiện rõ rằng giỏ hàng có thể bắt đầu từ khách vãng lai, nhưng các thao tác sâu hơn như coupon thường gắn chặt hơn với bước mua hàng của khách đã xác thực. Các route chính liên quan là `/add-cart-ajax`, `/update-cart-quantity`, `/update-quick-cart`, `/gio-hang`, `/show_quick_cart`, `/hover-cart`, `/check-coupon`.

---

## Hình 3.15. Biểu đồ use case module tài khoản khách hàng

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

rectangle "Module tài khoản khách hàng" {
  usecase "Đăng ký tài khoản" as UC1
  usecase "Đăng nhập hệ thống" as UC2
  usecase "Quên mật khẩu" as UC3
  usecase "Đặt lại mật khẩu" as UC4
  usecase "Đăng xuất" as UC5
  usecase "Xem hồ sơ và\nthông tin tài khoản" as UC6
}

Guest --> UC1
Guest --> UC2
Guest --> UC3
Guest --> UC4

Customer --> UC5
Customer --> UC6
@enduml
```

**Mô tả dưới hình:**  
Module tài khoản là nơi ranh giới giữa khách vãng lai và khách hàng thể hiện rõ nhất. Khách vãng lai khởi tạo đăng ký hoặc đăng nhập, còn khách hàng sau xác thực tiếp tục với hồ sơ và đăng xuất. Biểu đồ này bám các route như `/add-customer`, `/login-customer`, `/dang-nhap`, `/dang-xuat-khach-hang`, `/quen-mat-khau`, `/recover-pass`, `/reset-new-pass`.

---

## Hình 3.16. Biểu đồ use case module checkout, thanh toán và đơn hàng

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

rectangle "Module checkout, thanh toán và đơn hàng" {
  usecase "Vào bước checkout" as UC1
  usecase "Tính phí vận chuyển" as UC2
  usecase "Chọn địa chỉ giao hàng" as UC3
  usecase "Xác nhận đơn hàng" as UC4
  usecase "Thanh toán" as UC5
  usecase "Xem lịch sử đơn hàng" as UC6
  usecase "Hủy đơn hàng" as UC7
}

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
Do hệ thống thực tế yêu cầu đăng nhập trước checkout, module này chỉ còn gắn với tác nhân khách hàng. Các route tiêu biểu là `/checkout`, `/payment`, `/calculate-fee`, `/select-delivery-home`, `/confirm-order`, `/history`, `/huy-don-hang`.

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
  usecase "Phản hồi và xử lý hội thoại" as UC6
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
Trong bản chỉnh này, tác nhân hỗ trợ ngoài hệ thống đã bị loại bỏ; thay vào đó, sơ đồ chỉ giữ khách vãng lai, khách hàng và quản trị viên. Cách biểu diễn này bám hơn với góc nhìn phân tích hệ thống và các route `/chatbot/conversation`, `/chatbot/messages`, `/chat-inbox`, `/chat-inbox/conversations/{conversation}`.

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
  usecase "Xem dashboard" as UC2
  usecase "Quick search" as UC3
  usecase "Xem và cập nhật profile" as UC4
  usecase "Xem settings" as UC5
  usecase "Đăng xuất" as UC6
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
Biểu đồ này giữ nguyên bản chất của module workspace admin nhưng làm gọn actor còn đúng một tác nhân là quản trị viên. Các route tiêu biểu gồm `/admin`, `/auth-login`, `/dashboard`, `/admin/quick-search`, `/admin/profile`, `/admin/settings`, `/logout-auth`.

---

## Hình 3.19. Biểu đồ use case module quản lý catalog

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

rectangle "Module quản lý catalog" {
  usecase "Quản lý danh mục sản phẩm" as UC1
  usecase "Quản lý thương hiệu" as UC2
  usecase "Quản lý sản phẩm" as UC3
  usecase "Quản lý thuộc tính\nvà biến thể" as UC4
  usecase "Quản lý gallery" as UC5
  usecase "Quản lý document" as UC6
  usecase "Kiểm duyệt bình luận" as UC7
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
Module quản lý catalog bám các route `catalog/categories`, `catalog/brands`, `/add-product`, `/all-product`, `/update-product/{product_id}`, `/product-attribute`, `/add-gallery/{product_id}`, `/delete-document`, `/comment`. Đây là nhóm chức năng lớn nhất trong khu vực quản trị.

---

## Hình 3.20. Biểu đồ use case module quản lý nội dung

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

rectangle "Module quản lý nội dung" {
  usecase "Quản lý danh mục bài viết" as UC1
  usecase "Quản lý bài viết" as UC2
  usecase "Quản lý video" as UC3
  usecase "Quản lý slider" as UC4
  usecase "Quản lý thông tin liên hệ\nvà site info" as UC5
  usecase "Xem mail preview" as UC6
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
Biểu đồ use case module quản lý nội dung tiếp tục chỉ dùng một actor lõi là quản trị viên. Nhóm route tiêu biểu gồm `/add-category-post`, `/all-category-post`, `/add-post`, `/all-post`, `/video`, `/manage-slider`, `/information`, `/mail-preview`.

---

## Hình 3.21. Biểu đồ use case module vận hành đơn hàng và khuyến mãi

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

rectangle "Module vận hành đơn hàng và khuyến mãi" {
  usecase "Quản lý danh sách đơn hàng" as UC1
  usecase "Xem chi tiết và in đơn" as UC2
  usecase "Cập nhật số lượng,\ntrạng thái đơn" as UC3
  usecase "Quản lý delivery\nvà feeship" as UC4
  usecase "Quản lý coupon" as UC5
  usecase "Gửi coupon hoặc\nchiến dịch email" as UC6
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
Biểu đồ này mô tả phần vận hành hậu bán hàng trong admin, bám các route `/manage-order`, `/print-order/{checkout_code}`, `/view-order/{order_code}`, `/update-order-qty`, `/delivery`, `/insert-coupon`, `/send-coupon`, `/send-coupon-vip`.

---

## Hình 3.22. Biểu đồ use case module người dùng, phân quyền và báo cáo

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

rectangle "Module người dùng, phân quyền và báo cáo" {
  usecase "Quản lý users" as UC1
  usecase "Gán role cho user" as UC2
  usecase "Gán permission cho user" as UC3
  usecase "Quản lý vai trò\nvà quyền" as UC4
  usecase "Xem báo cáo lợi nhuận" as UC5
  usecase "Xem báo cáo\nproduct discovery" as UC6
  usecase "Xem statistics API\nở góc nhìn quản trị" as UC7
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
Tác nhân `Admin API Client` đã được loại bỏ trong bản chỉnh này để sơ đồ tập trung vào góc nhìn use case của người vận hành hệ thống. Module vẫn bám các route `users`, `/assign-role/{admin_id}`, `/assign-permission/{admin_id}`, `/index-spatie`, `/product-profit`, `/product-discovery-report`, `/api/v1/admin/statistics`.

---

## Kết luận cho bộ use case đã chỉnh lại

Phiên bản use case này phù hợp hơn với phong cách báo cáo đồ án vì nó tập trung vào các tác nhân nghiệp vụ chính thay vì đưa quá nhiều tác nhân kỹ thuật hoặc tác nhân hỗ trợ vào cùng một hình. Việc bổ sung khách vãng lai đồng thời làm cho phần phân tích hệ thống sát với hành vi thực tế của người dùng hơn, đặc biệt ở các luồng tìm kiếm, xem sản phẩm, so sánh và điều hướng sang đăng nhập trước checkout.

Nếu dùng file này thay cho bản use case cũ, Chương 3 sẽ có bộ sơ đồ rõ hơn về mặt phân quyền và dễ giải thích hơn khi bảo vệ.
