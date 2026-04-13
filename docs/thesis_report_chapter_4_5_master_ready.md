# BẢN GHÉP COPY-PASTE READY CHO CHƯƠNG 4 VÀ CHƯƠNG 5

File này là bản ghép đã chốt cho `Chương 4` và `Chương 5` của báo cáo. Nội dung được tổng hợp từ các file markdown đã chuẩn bị trong repo theo phương án đã rà soát, để anh có thể copy trực tiếp sang Word và chỉnh nhẹ lại câu chữ, caption, bảng biểu nếu cần.

Lưu ý khi dùng file này:

- Chương 4 được giữ gần như nguyên vẹn từ phần đã viết bám repo.
- Chương 5 được tổ chức lại thành một chương hoàn chỉnh theo đúng khung phổ biến của báo cáo đồ án.
- Không chốt cứng số lượng test pass nếu anh chưa chạy lại ngay trước ngày nộp.
- Sau khi dán vào Word, anh vẫn cần cập nhật mục lục, danh mục bảng và thêm ảnh chụp giao diện thật.

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
- `tests/Feature`

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

## 4.8. Tổ chức kiểm thử trong mã nguồn

Hệ thống test được tổ chức chủ yếu trong `tests/Feature`. Đây là một trong những điểm mạnh của repo hiện tại.

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

### 4.8.4. Giá trị của hệ thống kiểm thử trong dự án

Việc tổ chức số lượng lớn feature test cho storefront, admin, API, report, mail và chatbot giúp hệ thống hạn chế lỗi quay trở lại sau các lần chỉnh sửa giao diện hoặc nghiệp vụ. Đối với một dự án đang trong trạng thái hybrid giữa legacy và lớp chuẩn hóa mới, kiểm thử tự động càng có vai trò quan trọng vì chỉ cần một thay đổi nhỏ cũng có thể ảnh hưởng đến nhiều module khác nhau.

Từ góc độ đồ án tốt nghiệp, việc xây dựng được một hệ thống test có cấu trúc như hiện tại là một điểm cộng đáng kể, vì nó thể hiện tư duy làm phần mềm có kiểm soát chất lượng thay vì chỉ hoàn thành chức năng bề mặt.

## 4.9. Kết luận chương

Chương này đã trình bày quá trình xây dựng và triển khai hệ thống theo đúng cấu trúc mã nguồn thực tế của repo. Hệ thống không chỉ có storefront và admin mà còn có lớp API, lớp service nghiệp vụ, lớp domain, persistence, các support class, console commands và hệ thống kiểm thử tự động. Đây là cơ sở để khẳng định dự án có mức độ hoàn thiện và tổ chức mã nguồn cao hơn một website thương mại điện tử mẫu thông thường.

---

# CHƯƠNG 5. KIỂM THỬ, ĐÁNH GIÁ VÀ HƯỚNG PHÁT TRIỂN

## 5.1. Mục tiêu kiểm thử

Mục tiêu của kiểm thử trong đề tài là xác minh rằng các luồng nghiệp vụ chính của hệ thống hoạt động đúng, dữ liệu được xử lý nhất quán và những thay đổi mới không làm hỏng các chức năng đang tồn tại. Đối với một website thương mại điện tử bán thiết bị công nghệ, kiểm thử không chỉ nhằm phát hiện lỗi kỹ thuật mà còn nhằm bảo vệ những luồng liên quan trực tiếp tới trải nghiệm người dùng và nghiệp vụ vận hành như tìm kiếm, giỏ hàng, checkout, quản trị sản phẩm, đơn hàng và phân quyền.

Với đặc thù hybrid của repo, mục tiêu kiểm thử còn mang ý nghĩa sâu hơn: tạo ra một lớp an toàn trong quá trình hiện đại hóa dần từ cấu trúc legacy sang lớp service, domain và persistence mới. Nếu không có kiểm thử, bất kỳ thay đổi nhỏ nào ở route, controller, support class hay validation cũng có thể gây regression trên nhiều màn hình khác nhau.

## 5.2. Phương pháp kiểm thử

Trong phạm vi đồ án, hệ thống được kiểm thử theo nhiều lớp kết hợp:

- kiểm thử thủ công đối với giao diện và các thao tác người dùng dễ quan sát
- feature test để kiểm tra luồng request -> response trong Laravel
- regression test để hạn chế lỗi cũ quay trở lại
- kiểm thử theo nhóm module như storefront, admin, API, report, mail và chatbot

Về mặt thực tiễn, feature test là hình thức có giá trị lớn nhất trong repo hiện tại vì nó bám khá sát cách hệ thống vận hành thực tế. Khi một request được gửi tới route, đi qua middleware, validation, controller và trả response, feature test cho phép kiểm tra tương đối đầy đủ luồng đó trong cùng một bài test.

## 5.3. Xây dựng kịch bản kiểm thử

Các kịch bản kiểm thử nên được tổ chức theo các luồng có rủi ro nghiệp vụ cao và tần suất sử dụng lớn. Đối với phần khách hàng, có thể tập trung vào các kịch bản như:

- mở trang chủ và tìm kiếm sản phẩm
- xem chi tiết sản phẩm
- thêm sản phẩm vào giỏ hàng
- cập nhật giỏ hàng
- đăng ký hoặc đăng nhập tài khoản
- chuyển từ đăng nhập sang checkout
- xác nhận đơn hàng
- xem lịch sử đơn hàng

Đối với phần quản trị, nên tập trung vào:

- đăng nhập khu vực admin
- truy cập dashboard
- thêm mới hoặc cập nhật sản phẩm
- kiểm tra phân quyền của người dùng quản trị
- xem báo cáo thống kê
- quản lý đơn hàng

Đối với API, nên tập trung vào:

- public API cho sản phẩm, danh mục, thương hiệu
- protected API cho cart, orders, reviews
- admin API cho statistics, products và orders

Trong báo cáo Word, phần này nên được thể hiện bằng một bảng test case tiêu biểu thay vì chỉ mô tả bằng chữ. Ví dụ, mỗi dòng có thể gồm: mã test, chức năng, dữ liệu đầu vào, kết quả mong đợi và trạng thái.

## 5.4. Kết quả kiểm thử

Kết quả kiểm thử nên được trình bày theo hướng thận trọng và trung thực. Thay vì chốt cứng một con số pass nếu chưa chạy lại ngay trước ngày nộp, báo cáo nên nêu rằng hệ thống đã được kiểm thử trên nhiều nhóm chức năng và có bộ feature test tương đối rộng trong repo.

Tính đến thời điểm rà soát tài liệu, thư mục `tests` của dự án có `46` file test. Đây là một chỉ dấu quan trọng cho thấy kiểm thử không được làm theo kiểu hình thức mà đã được tổ chức thành nhiều nhóm chức năng khác nhau. Các nhóm test này hỗ trợ tốt cho việc phát hiện regression trong quá trình chỉnh sửa liên tục của dự án.

## 5.5. Bằng chứng kiểm thử từ mã nguồn

Ngoài các kịch bản kiểm thử thủ công, dự án còn có hệ thống kiểm thử tự động được tổ chức trong thư mục `tests/Feature`. Các file test được chia theo nhóm storefront, admin, API, report, mail và chatbot. Điều này cho thấy hệ thống không chỉ được xây dựng ở mức chức năng mà còn được quan tâm đến chất lượng phần mềm và khả năng regression sau mỗi lần chỉnh sửa.

### 5.5.1. Nhóm kiểm thử storefront

Hệ thống có các test cho trang chủ, bố cục storefront, đăng nhập và tìm kiếm, giỏ hàng, checkout, lịch sử đơn hàng, chi tiết sản phẩm và so sánh sản phẩm. Các file tiêu biểu gồm:

- `HomepageTest.php`
- `CartFlowTest.php`
- `CheckoutFlowTest.php`
- `CustomerRegistrationTest.php`
- `ProductDetailPageTest.php`
- `StorefrontLayoutShellTest.php`
- `StorefrontLoginAndSearchPageTest.php`
- `StorefrontComparePageTest.php`

### 5.5.2. Nhóm kiểm thử admin

Khu vực quản trị có các test cho phân quyền, đăng nhập, CRUD sản phẩm, API quản trị, công cụ workspace, thống kê và inbox hỗ trợ. Các file tiêu biểu gồm:

- `AdminPanelTest.php`
- `AdminAuthFlowTest.php`
- `AdminProductCrudTest.php`
- `AdminOrderApiTest.php`
- `AdminWorkspaceToolsTest.php`
- `AdminStatisticsApiTest.php`
- `AdminPermissionHardeningTest.php`

### 5.5.3. Nhóm kiểm thử API, report, mail và chatbot

Repo còn có các test cho API hệ thống, báo cáo lợi nhuận, báo cáo khám phá sản phẩm, settings, tài liệu API, chatbot và email legacy. Điều này giúp báo cáo thể hiện đúng rằng dự án đã được kiểm thử ở nhiều lớp khác nhau.

### 5.5.4. Cách trình bày bằng chứng kiểm thử trong báo cáo

Để phần kiểm thử không bị ngắn và mang tính hình thức, nên trình bày theo ba lớp:

- lớp thứ nhất là bảng test case tiêu biểu cho đăng ký, đăng nhập, giỏ hàng, thanh toán, quản trị sản phẩm
- lớp thứ hai là bằng chứng mã nguồn, thể hiện qua danh sách các file test trong `tests/Feature`
- lớp thứ ba là hình ảnh minh họa như ảnh cửa sổ terminal chạy `php artisan test`, ảnh danh sách test file trong IDE hoặc ảnh một số màn hình liên quan tới luồng đã kiểm thử

Cách trình bày này giúp người đọc thấy rõ rằng phần kiểm thử của đề tài không chỉ được viết ra cho đủ bố cục, mà thực sự có căn cứ từ mã nguồn và quá trình phát triển dự án.

## 5.6. Đánh giá kết quả đạt được

Xét trên phạm vi một đồ án tốt nghiệp đại học, hệ thống hiện tại đã đạt được một số kết quả đáng kể.

Thứ nhất, dự án không chỉ xây dựng được storefront cho khách hàng mà còn có khu vực admin với mức độ vận hành tương đối rõ. Các chức năng quản trị không dừng ở CRUD sản phẩm mà còn mở rộng sang nội dung, đơn hàng, delivery, coupon, người dùng, vai trò, quyền hạn, báo cáo và inbox hỗ trợ.

Thứ hai, repo cho thấy hệ thống không còn được tổ chức theo MVC cơ bản một cách đơn giản. Việc tồn tại song song `app/Application`, `app/Domain`, `app/Infrastructure/Persistence`, `app/Http/Requests` và `app/Support` cho thấy dự án đã đi theo hướng phân tầng rõ hơn, giúp tăng khả năng bảo trì và mở rộng.

Thứ ba, hệ thống đã có những tính năng làm tăng giá trị thực tiễn như gợi ý tìm kiếm, so sánh sản phẩm, flash sale, chatbot, báo cáo product discovery, báo cáo lợi nhuận và REST API. Điều này giúp đề tài gần hơn với một ứng dụng thực tế so với nhiều mô hình đồ án bán hàng chỉ có giỏ hàng và đơn hàng cơ bản.

Thứ tư, sự hiện diện của số lượng lớn feature test trong repo là một chỉ dấu quan trọng về chất lượng. Đây là điểm nổi bật giúp dự án có chiều sâu kỹ thuật hơn và có cơ sở để bảo vệ trước các câu hỏi về độ ổn định khi thay đổi mã nguồn.

## 5.7. Hạn chế của hệ thống

Bên cạnh các kết quả đạt được, hệ thống vẫn còn một số hạn chế cần thẳng thắn nêu trong báo cáo.

Thứ nhất, dự án vẫn đang ở trạng thái hybrid giữa dữ liệu legacy và lớp chuẩn hóa mới. Cách tiếp cận này an toàn cho quá trình chuyển đổi nhưng cũng làm tăng độ phức tạp trong bảo trì, vì có những luồng phải hiểu đồng thời cả model cũ và lớp persistence mới.

Thứ hai, một số tính năng tích hợp ngoài như thanh toán, đăng nhập xã hội, mail workflow hay chatbot có thể mới dừng ở mức phù hợp với phạm vi đồ án, chưa phải mức production-ready cho môi trường triển khai thực tế quy mô lớn. Đây là hạn chế bình thường đối với một đồ án đại học nhưng nên được nêu trung thực.

Thứ ba, giao diện và trải nghiệm người dùng dù đã khá đầy đủ vẫn còn có thể tiếp tục tối ưu, đặc biệt ở các khu vực mobile, tốc độ tải trang, tối ưu media và tính nhất quán giữa các màn hình legacy với các màn hình đã được chuẩn hóa hơn.

Thứ tư, do hệ thống có phạm vi chức năng rộng, việc đồng bộ tuyệt đối tất cả các module về cùng một phong cách kiến trúc vẫn chưa hoàn tất. Đây là điểm có thể xem vừa là hạn chế, vừa là hướng phát triển tiếp theo của đề tài.

## 5.8. Hướng phát triển trong tương lai

Từ hiện trạng của dự án, có thể xác định một số hướng phát triển hợp lý trong tương lai.

Thứ nhất, tiếp tục chuẩn hóa sâu hơn lớp dữ liệu và luồng nghiệp vụ để giảm dần phụ thuộc vào các thành phần legacy. Đây là hướng phát triển quan trọng nhất nếu muốn hệ thống dễ bảo trì và mở rộng lâu dài.

Thứ hai, mở rộng khả năng tích hợp thực tế cho các luồng thanh toán, vận chuyển và thông báo. Nếu được triển khai ở quy mô cao hơn, các phần như VNPay, MoMo, email workflow và delivery có thể được hoàn thiện thêm theo chuẩn môi trường thật.

Thứ ba, phát triển sâu hơn phần recommendation, product discovery và analytics. Đây là nhóm chức năng có giá trị lớn đối với website bán thiết bị công nghệ vì nó tác động trực tiếp đến hành vi khám phá sản phẩm của người dùng.

Thứ tư, nâng cao trải nghiệm giao diện trên thiết bị di động, tối ưu hiệu năng hình ảnh, tối ưu tìm kiếm và đồng bộ tốt hơn giữa storefront web với API nếu sau này phát triển thêm mobile app hoặc PWA.

Thứ năm, tiếp tục mở rộng hệ thống kiểm thử theo hướng tăng độ phủ cho các luồng phức tạp hơn như payment callback, permission edge case, migration command và các nghiệp vụ liên quan đến data normalization.

## 5.9. Kết luận chương

Chương này đã trình bày quá trình kiểm thử, đánh giá kết quả và định hướng phát triển của hệ thống theo đúng bối cảnh thực tế của repo. Có thể thấy dự án không chỉ đạt được các chức năng cốt lõi của một website thương mại điện tử mà còn thể hiện rõ tư duy tổ chức mã nguồn, mở rộng dịch vụ, xây dựng API, kiểm thử tự động và chuẩn hóa dần kiến trúc.

Mặc dù vẫn còn những hạn chế nhất định do phạm vi đồ án và đặc thù hybrid của hệ thống, đề tài đã đạt được mức độ hoàn thiện tương đối tốt và có nền tảng đủ rõ để tiếp tục phát triển theo hướng thực tiễn hơn trong tương lai.

---

# GỢI Ý HÌNH ẢNH NÊN CHÈN THÊM CHO CHƯƠNG 4 VÀ CHƯƠNG 5

Các hình giao diện nên được chụp lại từ UI hiện tại của dự án, ưu tiên:

- trang chủ có flash sale
- thanh tìm kiếm có gợi ý động
- trang chi tiết sản phẩm
- giỏ hàng
- đăng nhập và checkout
- lịch sử đơn hàng
- dashboard admin
- quản lý sản phẩm
- quản lý đơn hàng
- thống kê hoặc chat inbox admin

Ngoài ra, với Chương 5 nên có thêm:

- ảnh danh sách file test trong thư mục `tests/Feature`
- ảnh terminal khi chạy `php artisan test`
- ảnh một hoặc hai màn hình liên quan tới các luồng đã kiểm thử

Không nên dùng lại hình mockup hoặc giao diện cũ nếu chúng không còn khớp với trạng thái hiện tại của hệ thống.
