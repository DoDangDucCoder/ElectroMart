# Nội Dung Thay Thế Cho Báo Cáo Đồ Án

Tài liệu này được viết theo hướng Word-ready để thay trực tiếp vào báo cáo hiện có. Nội dung tập trung thay thế hoặc bổ sung cho Chương 3, Chương 4 và một phần Chương 5 để báo cáo bám sát repo thực tế hơn.

---

# CHƯƠNG 3. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG

## 3.1. Mô tả bài toán

Đề tài tập trung xây dựng một website thương mại điện tử bán thiết bị công nghệ trên nền tảng Laravel 11, phục vụ hai nhóm người dùng chính là khách hàng và quản trị viên. Hệ thống hỗ trợ quá trình tra cứu thông tin, tìm kiếm, lựa chọn sản phẩm, mua sắm trực tuyến, đồng thời cung cấp khu vực quản trị để vận hành dữ liệu sản phẩm, nội dung và đơn hàng.

Điểm đặc thù của dự án là hệ thống không được xây dựng hoàn toàn theo mô hình greenfield ngay từ đầu, mà đang trong trạng thái hiện đại hóa dần từ một nền legacy. Vì vậy, trong quá trình phân tích và thiết kế, cần ghi nhận đồng thời hai lớp kiến trúc dữ liệu: lớp dữ liệu legacy đang phục vụ nhiều luồng storefront và admin hiện tại, và lớp schema chuẩn hóa mới được xây dựng qua migrations để phục vụ quá trình refactor, mở rộng API và chuẩn hóa nghiệp vụ.

Do đó, việc phân tích hệ thống không chỉ dừng ở mức mô hình thương mại điện tử tổng quát, mà cần bám sát thực tế triển khai của mã nguồn, bao gồm các lớp controller legacy, các service nghiệp vụ mới, các route web và API, cũng như quy trình kiểm soát chất lượng đang tồn tại trong repo.

## 3.2. Phân tích yêu cầu hệ thống

### 3.2.1. Yêu cầu chức năng

#### a) Phía khách hàng

Hệ thống cần hỗ trợ các chức năng chính sau:

- xem danh mục sản phẩm
- xem chi tiết sản phẩm
- tìm kiếm sản phẩm theo từ khóa
- gợi ý tìm kiếm động
- xem sản phẩm theo thương hiệu
- xem sản phẩm theo tag
- so sánh sản phẩm
- xem video shop và nội dung liên quan
- bình luận và đánh giá sản phẩm
- thêm sản phẩm vào giỏ hàng
- cập nhật số lượng sản phẩm trong giỏ hàng
- áp dụng mã giảm giá
- đăng ký tài khoản
- đăng nhập, đăng xuất
- đăng nhập xã hội qua Google và Facebook
- quên mật khẩu và đặt lại mật khẩu
- tính phí vận chuyển
- thanh toán và đặt hàng
- theo dõi lịch sử đơn hàng
- hủy đơn hàng trong điều kiện cho phép
- tương tác với chatbot hỗ trợ

#### b) Phía quản trị viên

Hệ thống cần hỗ trợ các chức năng quản trị sau:

- đăng nhập khu vực quản trị
- quản lý danh mục sản phẩm
- quản lý thương hiệu
- quản lý sản phẩm
- quản lý thuộc tính và biến thể sản phẩm
- quản lý gallery và document
- quản lý bài viết
- quản lý video
- quản lý slider
- quản lý coupon
- quản lý đơn hàng
- quản lý delivery và feeship
- quản lý người dùng quản trị
- quản lý vai trò và quyền
- xem báo cáo lợi nhuận sản phẩm
- xem báo cáo khám phá sản phẩm
- quản lý hộp thư chat hỗ trợ
- sử dụng các công cụ quick search, profile, settings

#### c) Phần API

Ngoài hai mặt web là storefront và admin, dự án còn cung cấp hệ thống REST API với ba nhóm chính:

- Public API
- Protected API dùng `auth:sanctum`
- Admin API dùng `auth:sanctum` kết hợp `admin.access:sanctum`

Các API này phục vụ danh sách sản phẩm, category, brand, coupon, blog, settings, health check, docs, cart, orders, reviews, xác thực và thống kê quản trị.

### 3.2.2. Yêu cầu phi chức năng

Hệ thống cần bảo đảm:

- giao diện tương đối thân thiện, dễ sử dụng
- hỗ trợ hiển thị trên desktop và mobile
- phân quyền rõ ràng cho khu vực quản trị
- hạn chế lỗi runtime trong các luồng chính
- có khả năng mở rộng theo service layer và API
- có kiểm tra chất lượng cho các luồng quan trọng

### 3.2.3. Phân tích chi tiết yêu cầu phi chức năng

#### a) Yêu cầu về hiệu năng

Đối với một website thương mại điện tử, hiệu năng là yếu tố có ảnh hưởng trực tiếp đến trải nghiệm người dùng. Hệ thống cần phản hồi nhanh ở các thao tác thường xuyên như mở trang chủ, tải danh mục, xem chi tiết sản phẩm, thêm giỏ hàng, tìm kiếm và cập nhật số lượng trong giỏ hàng. Trong repo hiện tại, yêu cầu này được phản ánh qua việc tách các service tối ưu và theo dõi hiệu năng trong `app/Application/Shared/Services`, cũng như các migration phục vụ index và analytics.

#### b) Yêu cầu về bảo mật

Hệ thống cần bảo đảm an toàn cho cả khách hàng và quản trị viên. Phía storefront cần kiểm soát xác thực khách hàng, giới hạn truy cập checkout, xử lý quên mật khẩu và đăng nhập xã hội một cách an toàn. Phía admin cần có cơ chế phân quyền rõ ràng, giới hạn truy cập theo vai trò và kiểm soát API bằng `auth:sanctum` kết hợp middleware riêng. Đây là lý do báo cáo cần mô tả thêm `spatie/laravel-permission`, `AdminPermissions`, `admin.access` và `admin.access:sanctum`.

#### c) Yêu cầu về khả năng mở rộng

Khả năng mở rộng được thể hiện ở việc dự án không chỉ tổ chức theo MVC cơ bản mà còn có thêm lớp `Application`, `Domain`, `Infrastructure/Persistence`, `Support`, `Http/Requests`. Cấu trúc này cho phép hệ thống dễ mở rộng thêm API, báo cáo, chatbot, marketing hoặc các dịch vụ vận hành mà không làm controller trở nên quá tải.

#### d) Yêu cầu về khả năng bảo trì

Do dự án đang tồn tại đồng thời lớp legacy và lớp chuẩn hóa mới, việc bảo trì là yêu cầu quan trọng. Hệ thống cần có khả năng tiếp tục chạy ổn định trên nền dữ liệu cũ trong khi từng bước chuyển sang kiến trúc mới. Điều này giải thích vì sao repo hiện có song song `app/Models` kiểu legacy và `app/Infrastructure/Persistence/Eloquent` kiểu chuẩn hóa mới.

#### e) Yêu cầu về trải nghiệm người dùng

Hệ thống hướng tới trải nghiệm sử dụng gần với các website thương mại điện tử thực tế, thể hiện qua các thành phần như flash sale, gợi ý tìm kiếm, hero banner, so sánh sản phẩm, chatbot, quick cart, hover cart, lịch sử đơn hàng và các thông báo phản hồi rõ ràng. Đây là nhóm yêu cầu phi chức năng có liên quan trực tiếp đến cảm nhận sử dụng của người dùng cuối.

## 3.3. Tác nhân của hệ thống

Hệ thống có hai tác nhân chính và một nhóm tác nhân hỗ trợ.

### 3.3.1. Khách hàng

Khách hàng là người truy cập website để xem sản phẩm, tìm kiếm, so sánh, đánh giá, thêm giỏ hàng, đăng ký tài khoản, đăng nhập, đặt hàng và theo dõi đơn hàng.

### 3.3.2. Quản trị viên

Quản trị viên là người sử dụng khu vực quản trị để quản lý danh mục, thương hiệu, sản phẩm, bài viết, video, slider, đơn hàng, coupon, tài liệu, người dùng, vai trò và báo cáo.

### 3.3.3. Tác nhân hỗ trợ

Một số tác nhân hỗ trợ gián tiếp bao gồm:

- dịch vụ xác thực xã hội Google/Facebook
- hệ thống thanh toán PayPal, VNPay, MoMo
- dịch vụ email
- chatbot hỗ trợ

## 3.4. Mô tả các sơ đồ UML và mô tả dưới hình

### Hình 3.1. Biểu đồ use case tổng quát của hệ thống

**Mô tả dưới hình:**  
Biểu đồ use case tổng quát mô tả hai nhóm tác nhân chính của hệ thống là khách hàng và quản trị viên. Khách hàng tương tác với các chức năng mua sắm trực tuyến như xem sản phẩm, tìm kiếm, thêm giỏ hàng, đăng nhập và đặt hàng. Quản trị viên tương tác với các chức năng quản lý dữ liệu và vận hành hệ thống như quản lý sản phẩm, nội dung, đơn hàng, người dùng và báo cáo.

### Hình 3.2. Biểu đồ use case phía khách hàng

**Mô tả dưới hình:**  
Biểu đồ use case phía khách hàng thể hiện đầy đủ các nghiệp vụ chính trong storefront, bao gồm xem danh mục sản phẩm, tìm kiếm, xem chi tiết, so sánh, thêm giỏ hàng, áp dụng coupon, đăng ký tài khoản, đăng nhập, thanh toán, xem lịch sử đơn hàng, hủy đơn hàng và sử dụng chatbot hỗ trợ.

### Hình 3.3. Biểu đồ use case phía quản trị viên

**Mô tả dưới hình:**  
Biểu đồ use case phía quản trị viên mô tả các nhóm nghiệp vụ quản trị trong hệ thống, bao gồm quản lý catalog, nội dung, đơn hàng, người dùng, phân quyền, delivery, tài liệu, báo cáo và các công cụ hỗ trợ vận hành.

### Hình 3.4. Sơ đồ hoạt động chức năng tìm kiếm sản phẩm

**Mô tả dưới hình:**  
Sơ đồ hoạt động tìm kiếm sản phẩm thể hiện quá trình người dùng nhập từ khóa vào thanh tìm kiếm, hệ thống xử lý gợi ý qua cơ chế autocomplete, sau đó trả về danh sách sản phẩm hoặc chuyển tới trang tìm kiếm đầy đủ. Luồng này bám sát các endpoint `search`, `autocomplete-ajax` và phần search suggest trên giao diện storefront.

### Hình 3.5. Sơ đồ hoạt động chức năng thêm sản phẩm vào giỏ hàng

**Mô tả dưới hình:**  
Sơ đồ hoạt động thêm giỏ hàng mô tả quá trình khách hàng chọn sản phẩm, hệ thống kiểm tra trạng thái sản phẩm, tồn kho, số lượng yêu cầu và sau đó cập nhật dữ liệu giỏ hàng theo session hoặc dịch vụ tương ứng. Luồng này bám sát các thao tác AJAX như `add-cart-ajax`, `update-cart-quantity`, `update-quick-cart`.

### Hình 3.6. Sơ đồ hoạt động chức năng đăng ký, đăng nhập khách hàng

**Mô tả dưới hình:**  
Sơ đồ hoạt động đăng ký, đăng nhập khách hàng mô tả quá trình xác thực tài khoản từ giao diện checkout/storefront, bao gồm kiểm tra dữ liệu đầu vào, tạo session khách hàng và điều hướng trở lại đúng trang trước đó khi người dùng đang ở trong luồng mua hàng hoặc checkout.

### Hình 3.7. Sơ đồ hoạt động chức năng đặt hàng

**Mô tả dưới hình:**  
Sơ đồ hoạt động đặt hàng mô tả quy trình từ giỏ hàng đến checkout, trong đó khách hàng phải đăng nhập trước khi xác nhận đơn hàng. Hệ thống tiếp nhận thông tin giao hàng, tính phí vận chuyển, chọn phương thức thanh toán và tạo đơn hàng cùng chi tiết đơn hàng.

### Hình 3.8. Sơ đồ hoạt động chức năng quản lý sản phẩm

**Mô tả dưới hình:**  
Sơ đồ hoạt động quản lý sản phẩm mô tả thao tác của quản trị viên khi thêm mới hoặc cập nhật dữ liệu sản phẩm. Hệ thống kiểm tra dữ liệu đầu vào, lưu dữ liệu qua controller và service, đồng thời xử lý các thành phần liên quan như gallery, attribute, document và cache liên quan.

### Hình 3.9. Sơ đồ quan hệ thực thể ERD của hệ thống

**Mô tả dưới hình:**  
ERD của hệ thống phản ánh trạng thái dữ liệu hybrid của dự án. Một mặt, hệ thống vẫn còn các bảng legacy như `tbl_product`, `tbl_customers`, `tbl_order`, `tbl_coupon`, `tbl_category_product`. Mặt khác, lớp migrations mới đã bổ sung schema chuẩn hóa gồm `products`, `product_categories`, `product_variants`, `product_media`, `customers`, `carts`, `cart_items`, `orders`, `order_items`, `payments`, `chat_conversations`, `chat_messages`, `settings` và các bảng analytics, marketing, loyalty. Điều này cho thấy dự án đang trong giai đoạn hiện đại hóa dần từ nền dữ liệu cũ sang kiến trúc mới.

### Hình 3.10. Kiến trúc phân tầng của dự án

**Mô tả dưới hình:**  
Kiến trúc phân tầng của dự án bao gồm tầng giao diện Blade, tầng controller HTTP, tầng request validation, tầng service nghiệp vụ trong `app/Application`, tầng domain và repository trong `app/Domain` và `app/Infrastructure/Persistence`, cùng với tầng dữ liệu legacy và schema chuẩn hóa mới. Đây là điểm quan trọng làm nên tính đặc thù của repo hiện tại.

## 3.5. Đặc tả một số nghiệp vụ chính

### 3.5.1. Nghiệp vụ thêm giỏ hàng bằng AJAX

Khách hàng có thể thêm sản phẩm vào giỏ hàng từ trang danh sách hoặc trang chi tiết sản phẩm. Khi thao tác này xảy ra, controller nhận request qua các endpoint như `add-cart-ajax`, sau đó gọi service nghiệp vụ để kiểm tra sản phẩm, số lượng tồn kho và trạng thái có thể mua. Nếu hợp lệ, dữ liệu giỏ hàng được cập nhật trong session hoặc trong các thành phần dữ liệu liên quan, đồng thời giao diện badge, quick cart và hover cart được đồng bộ lại.

### 3.5.2. Nghiệp vụ đăng nhập rồi quay lại checkout

Khi khách hàng đang trong luồng mua hàng và chuyển tới trang đăng nhập, hệ thống ghi nhớ đích quay lại bằng support class `StorefrontCustomerAuth`. Sau khi đăng nhập thành công, hệ thống không điều hướng ngẫu nhiên mà quay về đúng trang `checkout`, `payment`, `history` hoặc URL nội bộ tương ứng. Đây là một cải tiến quan trọng để đảm bảo trải nghiệm người dùng bám sát luồng mua hàng thực tế.

### 3.5.3. Nghiệp vụ cập nhật sản phẩm ở admin

Quản trị viên vào module sản phẩm, thực hiện thêm mới hoặc chỉnh sửa thông tin. Controller như `ProductController` tiếp nhận request, kiểm tra dữ liệu, lưu sản phẩm, đồng thời xử lý các phần liên quan như gallery, document, attributes và các script hỗ trợ admin. Trong một số luồng mới, service layer như `ProductService` và các lớp repository/domain hỗ trợ chuẩn hóa nghiệp vụ ở tầng sâu hơn.

### 3.5.4. Nghiệp vụ tìm kiếm và gợi ý tìm kiếm

Khi người dùng thao tác với thanh tìm kiếm, hệ thống không chỉ thực hiện truy vấn theo từ khóa mà còn hỗ trợ gợi ý động thông qua endpoint `autocomplete-ajax` và các partial hiển thị kết quả trong storefront. Bên cạnh danh sách sản phẩm phù hợp, giao diện còn có thể hiển thị các nhóm gợi ý như từ khóa nổi bật, danh mục liên quan và lối vào nhanh. Đây là nghiệp vụ quan trọng vì nó tác động trực tiếp đến khả năng khám phá sản phẩm của khách hàng.

### 3.5.5. Nghiệp vụ bình luận và đánh giá sản phẩm

Tại trang chi tiết sản phẩm, người dùng có thể gửi bình luận và đánh giá thông qua các endpoint như `send-comment`, `load-comment`, `insert-rating`. Hệ thống tiếp nhận dữ liệu, kiểm tra đầu vào và lưu thông tin vào các bảng liên quan, sau đó hiển thị lại nội dung trên giao diện. Nghiệp vụ này giúp tăng tính tương tác và hỗ trợ người mua tham khảo trải nghiệm của người dùng trước đó.

### 3.5.6. Nghiệp vụ so sánh sản phẩm

Chức năng so sánh cho phép người dùng lưu một nhóm sản phẩm vào danh sách so sánh, sau đó xem các thông tin cơ bản trên cùng một giao diện. Tuy đây không phải là luồng bắt buộc của mua hàng, nhưng là một chức năng có giá trị thực tế cao đối với nhóm sản phẩm công nghệ, nơi người dùng thường cân nhắc nhiều cấu hình khác nhau trước khi quyết định.

### 3.5.7. Nghiệp vụ quản lý chat hỗ trợ

Storefront hỗ trợ chatbot để tiếp nhận hội thoại từ khách hàng, trong khi admin có module `chat inbox` để theo dõi, phản hồi và phân công xử lý hội thoại. Nghiệp vụ này phản ánh xu hướng hỗ trợ khách hàng trực tuyến hiện đại, đồng thời làm phong phú hơn phạm vi của đề tài so với một website bán hàng thông thường.

### 3.5.8. Nghiệp vụ báo cáo và thống kê

Hệ thống không chỉ quản lý dữ liệu vận hành mà còn hỗ trợ các báo cáo như lợi nhuận sản phẩm, khám phá sản phẩm, thống kê API quản trị và các chỉ số vận hành khác. Các nghiệp vụ này được triển khai thông qua các controller và service chuyên biệt trong nhóm reporting, cho phép hệ thống tiến gần hơn tới mô hình ứng dụng quản trị thực tế.

## 3.6. Phân tích dữ liệu và mô hình ERD

### 3.6.1. Hiện trạng dữ liệu của dự án

Hiện trạng dữ liệu của hệ thống được chia thành hai lớp.

#### a) Lớp dữ liệu legacy

Các model trong `app/Models` vẫn vận hành với nhiều bảng legacy, tiêu biểu:

- `tbl_product`
- `tbl_category_product`
- `tbl_brand_product`
- `tbl_customers`
- `tbl_order`
- `tbl_coupon`
- `tbl_post`
- `tbl_video`

Đây là lớp dữ liệu phục vụ nhiều luồng storefront và admin cũ nhưng vẫn đang hoạt động thật trong hệ thống.

#### b) Lớp dữ liệu chuẩn hóa mới

Trong `database/migrations`, hệ thống đã có một loạt bảng mới phản ánh hướng chuẩn hóa kiến trúc:

- nhóm catalog: `brands`, `product_categories`, `products`, `product_variants`, `product_media`, `attributes`, `attribute_values`
- nhóm checkout và order: `customers`, `carts`, `cart_items`, `customer_addresses`, `shipping_fees`, `orders`, `order_items`, `order_shipping`, `payments`, `payment_transactions`
- nhóm CMS: `blog_categories`, `blog_posts`, `documents`, `videos`, `banners`, `site_links`, `site_contacts`
- nhóm marketing và loyalty: `coupons`, `coupon_redemptions`, `marketing_campaigns`, `campaign_messages`, `loyalty_tiers`, `loyalty_transactions`
- nhóm analytics và support: `analytics_daily`, `analytics_events`, `product_views`, `page_views`, `user_sessions`, `metrics`, `alerts`
- nhóm hỗ trợ người dùng: `product_reviews`, `settings`, `notification_tokens`
- nhóm chatbot: `chat_conversations`, `chat_messages`

### 3.6.2. Các nhóm thực thể chính cần thể hiện trong ERD

#### a) Nhóm catalog

- category / product category
- brand
- product
- product variants
- product media
- attributes, attribute values
- reviews

#### b) Nhóm checkout và order

- customer
- customer address
- cart
- cart item
- shipping fee
- order
- order item
- order shipping
- payment
- payment transaction
- coupon

#### c) Nhóm CMS và media

- blog category
- blog post
- video
- document
- slider/banner
- site contact

#### d) Nhóm chat, setting, permission

- chat conversation
- chat message
- setting
- user
- role
- permission

### 3.6.3. Cách trình bày ERD trong báo cáo

Khi đưa vào báo cáo, nên chú thích rõ:

- sơ đồ thể hiện đồng thời lớp dữ liệu legacy và lớp dữ liệu chuẩn hóa mới
- một số luồng storefront/admin hiện vẫn còn sử dụng model legacy
- lớp migrations mới là nền tảng cho quá trình hiện đại hóa, mở rộng API và chuẩn hóa service layer

### 3.6.4. Ánh xạ giữa lớp legacy và lớp chuẩn hóa mới

Để tăng tính thuyết phục khi trình bày dữ liệu, báo cáo nên bổ sung thêm một đoạn phân tích về sự tương ứng giữa các bảng cũ và các bảng mới. Ví dụ, dữ liệu sản phẩm hiện có thể được nhìn từ hai góc: lớp legacy qua `tbl_product` và lớp chuẩn hóa qua `products`, `product_variants`, `product_media`. Tương tự, dữ liệu khách hàng và đơn hàng cũng đang tồn tại ở cả `tbl_customers`, `tbl_order` và các bảng chuẩn hóa như `customers`, `orders`, `order_items`, `payments`. Việc mô tả sự song song này giúp giảng viên hiểu đúng bản chất dự án và tránh cảm giác rằng ERD chỉ là một mô hình lý tưởng không phản ánh tình trạng mã nguồn thật.

## 3.7. Thiết kế kiến trúc hệ thống

Hệ thống được triển khai theo hướng phân tầng, trong đó vẫn giữ các đặc điểm của MVC nhưng đã được mở rộng thêm theo domain nghiệp vụ.

### 3.7.1. Tầng giao diện

Tầng giao diện nằm trong `resources/views`, chia làm ba nhóm chính:

- `resources/views/pages`: giao diện khách hàng
- `resources/views/admin`: giao diện quản trị
- `resources/views/partials`: các thành phần tái sử dụng cho storefront và admin

### 3.7.2. Tầng điều khiển HTTP

Tầng điều khiển nằm trong `app/Http/Controllers`, chịu trách nhiệm tiếp nhận request, gọi service, trả về view hoặc JSON. Repo hiện có cả controller legacy lẫn controller mới theo module admin và API.

### 3.7.3. Tầng request validation

Các request validation được tổ chức trong `app/Http/Requests`, tách theo admin, catalog, chatbot, storefront cart, storefront checkout, storefront product. Đây là điểm giúp hệ thống giảm logic validation trong controller và tăng tính rõ ràng cho luồng xử lý.

### 3.7.4. Tầng nghiệp vụ

Tầng nghiệp vụ nằm trong `app/Application`, chia theo domain như:

- AI
- Catalog
- Checkout
- Cms
- Customer
- Order
- Reporting
- Shared

Một số service tiêu biểu:

- `ProductService`
- `CartService`
- `CheckoutService`
- `ProductDiscoveryReportService`
- `ProductProfitReportService`
- `LegacyMailWorkflowService`
- `ChatbotService`
- `ChatInboxService`

### 3.7.5. Tầng domain và persistence

Hệ thống có thêm:

- `app/Domain`: DTO, Entities, Repository interfaces
- `app/Infrastructure/Persistence`: Eloquent models mới và repository implementations

Điều này cho thấy dự án đã tiến xa hơn so với MVC cơ bản, hướng tới cách tổ chức có domain và repository.

### 3.7.6. Tầng hỗ trợ và bảo mật

Trong `app/Support`, hệ thống có các lớp:

- `StorefrontCustomerAuth`
- `AdminPermissions`
- `AdminAccess`
- `AdminAuthenticator`
- `AdminWorkspace`
- `StorefrontLabel`

Đây là nhóm lớp hỗ trợ trực tiếp cho xác thực, phân quyền, điều hướng và tổ chức shell của giao diện.

### 3.7.7. Ba mặt triển khai của hệ thống

Khác với nhiều đồ án chỉ dừng ở giao diện web cơ bản, dự án này hiện có ba mặt triển khai tương đối rõ ràng:

- storefront web dành cho khách hàng
- admin web dành cho quản trị viên
- REST API dành cho client và nghiệp vụ mở rộng

Ba mặt triển khai này sử dụng chung một lõi nghiệp vụ nhưng được điều phối qua các route, controller và middleware khác nhau. Đây là một điểm đáng nhấn mạnh trong phần kiến trúc vì nó cho thấy hệ thống có mức độ hoàn thiện cao hơn mặt bằng đồ án thông thường.

## 3.8. Thiết kế giao diện tổng quát

### 3.8.1. Storefront

Giao diện storefront tập trung vào:

- header có thanh tìm kiếm và gợi ý động
- hero/banner
- flash sale
- danh sách sản phẩm
- chi tiết sản phẩm
- giỏ hàng
- checkout
- lịch sử đơn hàng
- chatbot hỗ trợ

### 3.8.2. Admin

Giao diện quản trị được tổ chức thành dashboard, gồm:

- shell quản trị
- sidebar
- topbar
- quick search
- profile
- settings
- các màn hình quản trị theo module

### 3.8.3. Định hướng thiết kế giao diện của dự án

Về mặt trực quan, giao diện storefront được định hướng theo phong cách website bán thiết bị công nghệ hiện đại, nhấn mạnh vào các thành phần như thanh tìm kiếm, khối khuyến mãi, sản phẩm nổi bật, chi tiết sản phẩm, giỏ hàng và checkout. Giao diện admin được định hướng theo mô hình dashboard, ưu tiên rõ trạng thái thao tác, thống nhất điều hướng và dễ quản lý nhiều nhóm dữ liệu khác nhau.

Việc chia nhỏ nhiều thành phần thành partials trong Blade cho thấy giao diện đã được tổ chức theo hướng tái sử dụng và thuận tiện cho việc bảo trì lâu dài.

## 3.9. Kết luận chương

Chương này đã phân tích bài toán và mô tả hệ thống theo đúng hiện trạng repo, trong đó nhấn mạnh đặc thù hybrid giữa dữ liệu legacy và lớp chuẩn hóa mới. Đồng thời, chương cũng trình bày các sơ đồ UML, ERD và kiến trúc phân tầng theo cách bám sát route, actor, service, controller và cấu trúc mã nguồn thực tế của dự án.

---

# CHƯƠNG 4. XÂY DỰNG VÀ TRIỂN KHAI HỆ THỐNG

## 4.1. Môi trường phát triển hệ thống

Hệ thống được xây dựng bằng PHP trên nền tảng Laravel 11, sử dụng MySQL làm hệ quản trị cơ sở dữ liệu. Phần giao diện được triển khai bằng Blade, HTML, CSS, Bootstrap, JavaScript và jQuery. Ngoài các thành phần cốt lõi của Laravel, dự án còn tích hợp các gói và thư viện quan trọng như:

- `laravel/sanctum` cho xác thực API
- `spatie/laravel-permission` cho phân quyền quản trị
- `laravel/socialite` cho đăng nhập xã hội
- `darkaonline/l5-swagger` cho tài liệu API
- `barryvdh/laravel-dompdf` cho hỗ trợ PDF

## 4.2. Cấu trúc mã nguồn của dự án

### 4.2.1. Nhóm thư mục chính

Mã nguồn của dự án được tổ chức theo các nhóm thư mục lớn:

- `app/Application`
- `app/Domain`
- `app/Infrastructure/Persistence`
- `app/Http`
- `app/Support`
- `routes`
- `resources/views`
- `public`
- `database/migrations`

### 4.2.2. Thư mục `app/Application`

Thư mục này là nơi tập trung các service nghiệp vụ theo domain, gồm các module:

- `AI`
- `Catalog`
- `Checkout`
- `Cms`
- `Customer`
- `DataMigration`
- `Marketing`
- `Order`
- `Reporting`
- `Shared`

Một số file tiêu biểu:

- `app/Application/Catalog/Services/ProductService.php`
- `app/Application/Catalog/Services/ProductRecommendationService.php`
- `app/Application/Checkout/Services/CartService.php`
- `app/Application/Checkout/Services/CheckoutService.php`
- `app/Application/Customer/Services/CustomerService.php`
- `app/Application/Reporting/Services/ProductDiscoveryReportService.php`
- `app/Application/Reporting/Services/ProductProfitReportService.php`
- `app/Application/Shared/Services/LegacyMailWorkflowService.php`
- `app/Application/AI/Services/ChatbotService.php`
- `app/Application/AI/Services/ChatInboxService.php`

### 4.2.3. Thư mục `app/Domain`

Thư mục này chứa các thực thể nghiệp vụ, DTO và interface repository. Một số file nổi bật:

- `app/Domain/Catalog/Entities/Product.php`
- `app/Domain/Catalog/Entities/Category.php`
- `app/Domain/Catalog/Entities/Brand.php`
- `app/Domain/Customer/Entities/Customer.php`
- `app/Domain/Order/Entities/Order.php`
- `app/Domain/Shared/ValueObjects/Money.php`
- `app/Domain/Shared/ValueObjects/Slug.php`

### 4.2.4. Thư mục `app/Infrastructure/Persistence`

Đây là lớp persistence mới của hệ thống, gồm Eloquent models mới và repository implementations. Một số file đáng chú ý:

- `app/Infrastructure/Persistence/Eloquent/Catalog/Product.php`
- `app/Infrastructure/Persistence/Eloquent/Catalog/ProductCategory.php`
- `app/Infrastructure/Persistence/Eloquent/Catalog/ProductVariant.php`
- `app/Infrastructure/Persistence/Eloquent/Checkout/Cart.php`
- `app/Infrastructure/Persistence/Eloquent/Order/Order.php`
- `app/Infrastructure/Persistence/Eloquent/Support/ChatConversation.php`
- `app/Infrastructure/Persistence/Repositories/Catalog/EloquentProductRepository.php`

### 4.2.5. Thư mục `app/Http/Requests`

Request validation được tách riêng theo module:

- `Storefront/Cart`
- `Storefront/Checkout`
- `Storefront/Home`
- `Storefront/Product`
- `Admin/Legacy`
- `Catalog`
- `Cms`
- `Api/Admin`
- `Chatbot`

Một số file tiêu biểu:

- `RegisterCustomerRequest.php`
- `LoginCustomerRequest.php`
- `ConfirmOrderRequest.php`
- `AddCartAjaxRequest.php`
- `UpdateCartQuantityRequest.php`
- `StoreVideoRequest.php`
- `StoreCouponRequest.php`

### 4.2.6. Thư mục `app/Support`

Các lớp hỗ trợ trong `app/Support` là điểm quan trọng cần nêu trong báo cáo:

- `StorefrontCustomerAuth.php`
- `AdminPermissions.php`
- `AdminAccess.php`
- `AdminAuthenticator.php`
- `AdminWorkspace.php`
- `StorefrontLabel.php`

### 4.2.7. Vai trò thực tiễn của cách tổ chức mã nguồn

Cấu trúc mã nguồn của dự án cho thấy hệ thống không còn là một ứng dụng CRUD đơn giản đặt toàn bộ xử lý trong controller. Việc tách riêng service nghiệp vụ, request validation, domain entities, repository và support classes giúp hệ thống giảm phụ thuộc vào một lớp duy nhất, đồng thời tăng khả năng bảo trì khi số lượng chức năng ngày càng lớn.

Đặc biệt, việc tồn tại song song controller legacy và lớp service chuẩn hóa mới phản ánh đúng quá trình chuyển đổi của dự án: vừa duy trì khả năng vận hành trên nền dữ liệu cũ, vừa từng bước chuẩn hóa kiến trúc để hướng tới mô hình dễ mở rộng hơn. Đây là một điểm có thể nhấn mạnh khi bảo vệ vì nó thể hiện tư duy tổ chức hệ thống thay vì chỉ tập trung vào giao diện.

## 4.3. Xây dựng phần khách hàng

### 4.3.1. Route storefront

Các route phía khách hàng được khai báo trong `routes/web/frontend.php`. Một số route nổi bật:

- `/`
- `/trang-chu`
- `/tim-kiem`
- `/autocomplete-ajax`
- `/goi-y-san-pham`
- `/danh-muc/{slug_category_product}`
- `/chi-tiet/{product_slug}`
- `/thuong-hieu/{brandSlug}`
- `/tag/{product_tag}`
- `/so-sanh`
- `/gio-hang`
- `/dang-nhap`
- `/checkout`
- `/payment`
- `/history`
- `/video-shop`
- `/chatbot/conversation`
- `/chatbot/messages`

### 4.3.2. Controller storefront

Các controller chính phục vụ storefront gồm:

- `HomeController`
- `CategoryProduct`
- `BrandProduct`
- `ProductController`
- `CompareController`
- `CartController`
- `CheckoutController`
- `OrderController`
- `ChatController`
- `MailController`
- `VideoController`
- `ContactController`

### 4.3.3. Giao diện storefront

Giao diện phía khách hàng nằm chủ yếu tại:

- `resources/views/pages/home.blade.php`
- `resources/views/pages/cart/show_cart.blade.php`
- `resources/views/pages/checkout/login_checkout.blade.php`
- `resources/views/pages/checkout/show_checkout.blade.php`
- `resources/views/pages/history/history.blade.php`
- `resources/views/pages/sanpham/show_details.blade.php`
- `resources/views/partials/storefront/header.blade.php`
- `resources/views/partials/storefront/scripts/interactions.blade.php`
- `resources/views/partials/storefront/scripts/mobile-nav.blade.php`

Các phần như flash sale, search suggest và hero motion được tổ chức qua partials:

- `resources/views/pages/home/partials/flash_sale.blade.php`
- `resources/views/pages/home/partials/autocomplete_results.blade.php`
- `resources/views/partials/storefront/scripts/hero.blade.php`

### 4.3.4. Service layer storefront

Một số service nghiệp vụ quan trọng của storefront gồm:

- `ProductService`
- `ProductRecommendationService`
- `ProductPricingService`
- `CartService`
- `CheckoutService`
- `CouponService`
- `ShippingService`
- `CustomerService`
- `ProductReviewService`

### 4.3.5. Một số điểm nổi bật của storefront theo repo hiện tại

Phần storefront của dự án không chỉ dừng ở các trang danh mục và chi tiết sản phẩm mà đã được mở rộng thêm nhiều thành phần giúp tăng trải nghiệm sử dụng. Có thể kể đến như flash sale trên trang chủ, gợi ý tìm kiếm động trong header, quick cart, hover cart, so sánh sản phẩm, lịch sử đơn hàng, chatbot hỗ trợ và các khối recommendation.

Những chức năng này được triển khai qua sự phối hợp giữa controller, Blade partials, JavaScript và service nghiệp vụ. Điều này cho thấy storefront của dự án có mức độ hoàn thiện tương đối cao và gần với các website thương mại điện tử thực tế hơn so với một mô hình demo cơ bản.

## 4.4. Xây dựng chức năng tài khoản và thanh toán

### 4.4.1. Đăng ký, đăng nhập, đăng xuất khách hàng

Luồng tài khoản khách hàng được tổ chức xoay quanh:

- `CheckoutController`
- `RegisterCustomerRequest`
- `LoginCustomerRequest`
- `StorefrontCustomerAuth`

Support class `StorefrontCustomerAuth` dùng để:

- nhớ `redirect` và `redirect_to`
- chuẩn hóa đích quay lại nội bộ
- lưu thông tin hiển thị tài khoản trên storefront
- xóa session khi đăng xuất

### 4.4.2. Thanh toán và phương thức thanh toán

Phần thanh toán không chỉ dừng ở checkout nội bộ mà còn có các route và controller liên quan đến:

- PayPal qua `PayPalController`
- VNPay qua `CheckoutController`
- MoMo qua `CheckoutController`

Đây là điểm báo cáo cần nêu rõ vì repo đã có route và controller cho các phương thức này.

### 4.4.3. Quên mật khẩu và đăng nhập xã hội

Repo hiện hỗ trợ:

- quên mật khẩu và đặt lại mật khẩu qua `MailController`
- đăng nhập khách hàng bằng Facebook và Google qua `AdminController`

Các route tương ứng cũng đã tồn tại trong `routes/web/frontend.php`.

### 4.4.4. Luồng xác thực và điều hướng sau đăng nhập

Một điểm đáng chú ý của dự án là cơ chế điều hướng sau đăng nhập đã được tổ chức chặt chẽ hơn bằng `StorefrontCustomerAuth`. Thay vì sau khi đăng nhập thành công luôn quay về một trang cố định, hệ thống có thể ghi nhớ đích đến trước đó như `checkout`, `payment`, `history` hoặc một URL nội bộ hợp lệ khác.

Cách tổ chức này giúp luồng mua hàng trở nên liền mạch hơn. Đây cũng là ví dụ rõ ràng cho việc dự án không chỉ xây dựng chức năng ở mức có thể chạy, mà còn có những cải tiến để tăng chất lượng trải nghiệm người dùng.

## 4.5. Xây dựng khu vực quản trị

### 4.5.1. Route và controller quản trị

Các route quản trị nằm trong `routes/web/admin.php` với middleware xác thực và phân quyền. Một số controller trung tâm:

- `AuthController`
- `AdminController`
- `AdminWorkspaceController`
- `ProductController`
- `CategoryProductAdminController`
- `BrandProduct`
- `PostController`
- `CategoryPost`
- `VideoController`
- `SliderController`
- `OrderController`
- `DeliveryController`
- `UserController`
- `UserRolesController`
- `GalleryController`
- `DocumentController`
- `ProductDiscoveryReportController`
- `ProductProfitController`
- `ChatInboxController`

### 4.5.2. Phân quyền quản trị

Khu vực quản trị sử dụng `spatie/laravel-permission` kết hợp với support class `AdminPermissions`. Hệ thống chia quyền theo nhóm:

- products
- content
- orders
- coupons
- users
- roles
- documents
- system

Các middleware như `admin.access` và `admin.permission:*` được áp dụng trực tiếp trên route để kiểm soát quyền truy cập.

### 4.5.3. Giao diện quản trị

Phần view quản trị được tổ chức trong:

- `resources/views/admin`
- `resources/views/partials/admin`

Một số màn hình tiêu biểu:

- dashboard
- quản lý sản phẩm
- quản lý danh mục
- quản lý thương hiệu
- quản lý bài viết
- quản lý video
- quản lý slider
- quản lý người dùng
- quản lý vai trò và quyền
- quản lý đơn hàng
- delivery
- reports
- tools search/profile/settings

### 4.5.4. Đặc điểm của khu vực quản trị trong repo

Khu vực quản trị của dự án mang tính chất hybrid giống toàn bộ hệ thống. Một số màn hình được tổ chức theo hướng mới hơn, có shell riêng, topbar, quick search, profile và settings. Trong khi đó, một số luồng legacy vẫn được duy trì nhưng đã được harden để giảm lỗi runtime và tăng tính rõ ràng cho người vận hành.

Điều này phản ánh quá trình phát triển thực tế của dự án: không phải thay toàn bộ hệ thống cùng một lúc, mà từng bước ổn định hóa, chuẩn hóa và cải thiện trải nghiệm sử dụng trong phạm vi có thể.

## 4.6. Xây dựng API, báo cáo và các dịch vụ dùng chung

### 4.6.1. REST API

Hệ thống API được định nghĩa trong `routes/api.php` với ba nhóm chính.

#### a) Public API

Gồm các endpoint:

- products
- latest
- featured
- search
- recommendations
- categories
- brands
- blog
- coupons
- settings
- health
- docs
- chatbot

#### b) Protected API

Sử dụng `auth:sanctum` cho các chức năng:

- user profile
- logout
- cart
- orders
- reviews

#### c) Admin API

Sử dụng `auth:sanctum` kết hợp `admin.access:sanctum` cho:

- admin auth
- products
- categories
- brands
- orders
- statistics

### 4.6.2. Báo cáo và thống kê

Repo hiện có các thành phần báo cáo thật:

- `ProductDiscoveryReportController`
- `ProductDiscoveryReportService`
- `ProductProfitController`
- `ProductProfitReportService`
- `Api/Admin/StatisticsController`

Đây là các module nên được nhấn mạnh trong Chương 4 để thể hiện hệ thống không chỉ dừng ở CRUD cơ bản.

### 4.6.3. Chatbot, mail và công cụ dùng chung

Một số service và controller hỗ trợ:

- `ChatController`
- `ChatbotService`
- `ChatInboxService`
- `LegacyMailWorkflowService`
- `MailController`

Phần này phản ánh hệ thống có các tính năng hỗ trợ vận hành và tương tác khách hàng khá rõ.

### 4.6.4. Tài liệu API và kiểm soát truy cập

Ngoài việc cung cấp endpoint, hệ thống còn có tài liệu API và các endpoint health check để hỗ trợ kiểm tra trạng thái. Đây là điểm khác biệt đáng kể so với nhiều đồ án chỉ có route web. Việc kết hợp `l5-swagger`, `HealthController`, `ApiDocumentationController` và lớp middleware xác thực cho thấy dự án đã có định hướng mở rộng hoặc tích hợp với client khác trong tương lai.

## 4.7. Console commands và hỗ trợ vận hành

Trong `app/Console/Commands`, repo hiện có các command như:

- `NormalizeAdminAuthCommand`
- `MigrateLegacyDataCommand`
- `ImportLegacyDatabaseCommand`
- `SetupLegacyTablesCommand`
- `OptimizeApplicationCommand`
- `OptimizeDatabaseCommand`
- `OptimizePerformanceCommand`
- `SecurityCommand`
- `UIUXCommand`
- `ProductionDeploymentCommand`

Nhóm command này cho thấy dự án có thêm lớp hỗ trợ migration, chuẩn hóa auth, tối ưu và vận hành, không chỉ thuần một bài tập CRUD cơ bản.

### 4.7.1. Ý nghĩa của nhóm console command

Sự xuất hiện của nhiều command riêng cho migrate legacy data, normalize admin auth, optimize performance, security và deployment cho thấy hệ thống đã được nhìn nhận như một ứng dụng có vòng đời vận hành tương đối đầy đủ. Đây là nội dung rất nên nhấn mạnh trong báo cáo, vì nó phản ánh tư duy phát triển phần mềm theo quy trình chứ không chỉ dừng ở việc hoàn thành giao diện và chức năng.

## 4.8. Tổ chức kiểm tra chất lượng trong mã nguồn


### 4.8.1. Nhóm storefront

- `HomepageTest.php`
- `CartFlowTest.php`
- `CheckoutFlowTest.php`
- `CustomerRegistrationTest.php`
- `ProductDetailPageTest.php`
- `StorefrontLayoutShellTest.php`
- `StorefrontLoginAndSearchPageTest.php`
- `StorefrontComparePageTest.php`
- `OrderHistoryPageTest.php`
- `CategoryPageTest.php`

### 4.8.2. Nhóm admin

- `AdminPanelTest.php`
- `AdminProductCrudTest.php`
- `AdminAuthFlowTest.php`
- `AdminWorkspaceToolsTest.php`
- `AdminPermissionHardeningTest.php`
- `AdminCatalogCacheConsistencyTest.php`
- `AdminApiSecurityTest.php`
- `AdminSecurityTest.php`
- `AdminChatInboxTest.php`
- `AdminOrderApiTest.php`
- `AdminStatisticsApiTest.php`

### 4.8.3. Nhóm API, report, mail, chatbot

- `ProductDiscoveryReportTest.php`
- `ProductProfitReportTest.php`
- `SettingsApiTest.php`
- `HealthAndDocsApiTest.php`
- `BlogApiTest.php`
- `CouponApiTest.php`
- `ProductApiCacheConsistencyTest.php`
- `ProductSearchAndRecommendationTest.php`
- `ChatbotFeatureTest.php`
- `LegacyMailFlowTest.php`
- `LegacyMailSnapshotTest.php`
- `LegacyMailTemplateRenderTest.php`
- `NormalizeAdminAuthCommandTest.php`

### 4.8.4. Giá trị của quy trình kiểm soát chất lượng trong dự án


Từ góc độ đồ án tốt nghiệp, việc xây dựng được một hệ thống test có cấu trúc như hiện tại là một điểm cộng đáng kể, vì nó thể hiện tư duy làm phần mềm có kiểm soát chất lượng thay vì chỉ hoàn thành chức năng bề mặt.

## 4.9. Kết luận chương

Chương này đã trình bày quá trình xây dựng và triển khai hệ thống theo đúng cấu trúc mã nguồn thực tế của repo. Hệ thống không chỉ có storefront và admin mà còn có lớp API, lớp service nghiệp vụ, lớp domain, persistence, các support class, console commands và hệ thống kiểm tra chất lượng. Đây là cơ sở để khẳng định dự án có mức độ hoàn thiện và tổ chức mã nguồn cao hơn một website thương mại điện tử mẫu thông thường.

---

# PHẦN BỔ SUNG CHO CHƯƠNG 5

## 5.x. Bằng chứng kiểm tra chất lượng từ mã nguồn

### 5.x.1. Bằng chứng kiểm tra chất lượng


### 5.x.2. Nhóm kiểm tra chất lượng storefront

Hệ thống có các test cho trang chủ, bố cục storefront, đăng nhập và tìm kiếm, giỏ hàng, checkout, lịch sử đơn hàng, chi tiết sản phẩm và so sánh sản phẩm. Các file tiêu biểu gồm:

- `HomepageTest.php`
- `CartFlowTest.php`
- `CheckoutFlowTest.php`
- `CustomerRegistrationTest.php`
- `ProductDetailPageTest.php`
- `StorefrontLayoutShellTest.php`
- `StorefrontLoginAndSearchPageTest.php`
- `StorefrontComparePageTest.php`

### 5.x.3. Nhóm kiểm tra chất lượng admin

Khu vực quản trị có các test cho phân quyền, đăng nhập, CRUD sản phẩm, API quản trị, công cụ workspace, thống kê và inbox hỗ trợ. Các file tiêu biểu gồm:

- `AdminPanelTest.php`
- `AdminAuthFlowTest.php`
- `AdminProductCrudTest.php`
- `AdminOrderApiTest.php`
- `AdminWorkspaceToolsTest.php`
- `AdminStatisticsApiTest.php`
- `AdminPermissionHardeningTest.php`

### 5.x.4. Nhóm kiểm tra chất lượng API, report và mail

Repo còn có các test cho API hệ thống, báo cáo lợi nhuận, báo cáo khám phá sản phẩm, settings, tài liệu API, chatbot và email legacy. Điều này giúp báo cáo thể hiện đúng rằng dự án đã được kiểm tra chất lượng ở nhiều lớp khác nhau.

### 5.x.5. Lưu ý khi ghi số liệu test

Trong báo cáo chính thức không nên chốt cứng số lượng test pass nếu chưa chạy xác minh ngay trước ngày nộp. Thay vào đó, nên ghi theo hướng: hệ thống có bộ kiểm tra chất lượng theo nhiều nhóm chức năng và các suite đã được sử dụng để kiểm tra regression trong quá trình hoàn thiện dự án.

### 5.x.6. Cách trình bày phần kiểm tra chất lượng trong báo cáo để tăng tính thuyết phục

Để phần kiểm tra chất lượng của đồ án không bị ngắn và mang tính hình thức, nên trình bày theo ba lớp:

- lớp thứ nhất là các bảng kịch bản kiểm tra tiêu biểu cho đăng ký, đăng nhập, giỏ hàng, thanh toán, quản trị sản phẩm

Cách trình bày này giúp người đọc thấy rõ rằng phần kiểm tra chất lượng của đề tài không chỉ được viết ra cho đủ bố cục, mà thực sự có căn cứ từ mã nguồn và quá trình phát triển dự án.

### 5.x.7. Đánh giá mức độ hoàn thiện của hệ thống từ góc độ kiểm tra chất lượng


Đây là điểm có thể nhấn mạnh trong phần nhận xét và đánh giá kết quả, giúp báo cáo có chiều sâu hơn và tránh cảm giác kết luận quá chung chung.

---

# GỢI Ý CẬP NHẬT HÌNH ẢNH CHO BẢN WORD

Các hình giao diện nên được chụp lại từ UI hiện tại của dự án, ưu tiên:

- trang chủ có Flash Sale
- thanh tìm kiếm có gợi ý động
- trang chi tiết sản phẩm
- giỏ hàng
- đăng nhập / checkout
- lịch sử đơn hàng
- dashboard admin
- quản lý sản phẩm
- quản lý đơn hàng
- thống kê hoặc chat inbox admin

Không nên dùng lại hình mockup hoặc giao diện cũ nếu chúng không còn khớp với trạng thái hiện tại của hệ thống.
