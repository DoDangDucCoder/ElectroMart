# Phần Mở Rộng Thêm Cho Báo Cáo Đồ Án

Tài liệu này được tạo riêng để kéo dài báo cáo theo hướng học thuật hơn, nhưng vẫn bám sát repo hiện tại của dự án. Nội dung bên dưới không thay thế hoàn toàn file [thesis_report_revised_sections.md](c:/Users/Admin/Desktop/shop/Laravel11_Shop_Website_Full/docs/thesis_report_revised_sections.md), mà đóng vai trò là phần mở rộng để anh chèn thêm vào Word khi cần tăng dung lượng, tăng chiều sâu phân tích và tăng độ thuyết phục khi bảo vệ.

Các phần được viết theo nguyên tắc:

- không thêm chữ vô nghĩa
- không mô tả dự án như một hệ thống lý tưởng không có trong repo
- ưu tiên bám sát route, controller, service, support class, migration và test suite đang tồn tại
- giữ giọng văn học thuật kiểu đồ án CNTT Việt Nam

---

# PHẦN MỞ RỘNG CHO CHƯƠNG 3

## 3.x. Đặc thù của bài toán trong bối cảnh dự án hybrid

Một điểm rất đáng chú ý của đề tài này là bài toán xây dựng hệ thống không diễn ra trên một nền mã nguồn “sạch” hoàn toàn ngay từ đầu, mà diễn ra trong bối cảnh hệ thống đang được chỉnh lý, chuẩn hóa và mở rộng dần từ một nền legacy. Điều này làm cho bài toán của đồ án trở nên thực tế hơn, đồng thời cũng phức tạp hơn so với nhiều đề tài chỉ xây dựng một website thương mại điện tử mới hoàn toàn.

Trong các hệ thống thương mại điện tử thông thường, người thiết kế có thể xây dựng đồng bộ cơ sở dữ liệu, route, giao diện và dịch vụ theo một chuẩn thống nhất ngay từ đầu. Tuy nhiên, trong dự án này, nhiều luồng storefront và admin cũ vẫn đang hoạt động trên các model legacy như `Product`, `Customer`, `Order`, `CategoryProductModel`, trong khi một lớp schema mới hơn lại đã được xây dựng qua migrations và hệ thống `Infrastructure/Persistence`. Điều đó khiến việc phân tích hệ thống cần nhìn theo hai chiều:

- chiều thứ nhất là khả năng vận hành hiện tại của hệ thống trên nền dữ liệu và controller legacy
- chiều thứ hai là định hướng chuẩn hóa kiến trúc sang service layer, domain layer, repository layer và API

Việc mô tả rõ đặc thù này trong báo cáo sẽ giúp đề tài có chiều sâu thực tiễn hơn. Nó cho thấy sinh viên không chỉ thực hiện một bài toán mô phỏng, mà thực sự đang làm việc với một hệ thống có vòng đời phát triển, có nợ kỹ thuật, có yêu cầu bảo trì và có quá trình hiện đại hóa từng phần.

## 3.x. Các ràng buộc thực tế của đề tài

Ngoài các yêu cầu chức năng và phi chức năng đã nêu, hệ thống còn chịu tác động bởi nhiều ràng buộc thực tế.

### 3.x.1. Ràng buộc về dữ liệu

Do dự án tồn tại song song lớp legacy và lớp schema chuẩn hóa mới, việc thiết kế dữ liệu không thể chỉ dựa trên lý thuyết chuẩn hóa hoàn toàn. Trong thực tế, nhiều luồng nghiệp vụ vẫn còn dựa vào các bảng cũ như `tbl_product`, `tbl_order`, `tbl_customers`, nên các thay đổi về dữ liệu cần được xem xét cẩn thận để tránh làm gián đoạn các chức năng đang vận hành.

### 3.x.2. Ràng buộc về tương thích giao diện

Nhiều phần storefront và admin đã được cải tiến nhưng vẫn phải giữ khả năng tương thích với các màn cũ, các partial cũ và các luồng request cũ. Vì vậy, việc thiết kế giao diện không chỉ hướng tới sự hiện đại, mà còn phải bảo đảm không phá vỡ những thành phần đã tồn tại trước đó.

### 3.x.3. Ràng buộc về phạm vi đồ án

Mặc dù hệ thống có nhiều thành phần khá rộng như API, chatbot, báo cáo, document, delivery, statistics, nhưng trong phạm vi đồ án, không phải mọi module đều có thể được trình bày sâu như nhau. Vì vậy, cần ưu tiên trình bày kỹ các phần có ảnh hưởng trực tiếp đến giá trị kỹ thuật của đề tài, như kiến trúc hybrid, luồng mua hàng, quản trị, API và kiểm thử.

### 3.x.4. Ràng buộc về khả năng triển khai thực tế

Một số phần của hệ thống đã có route, controller hoặc service nhưng ở mức độ tích hợp thực tế có thể vẫn đang được hoàn thiện dần. Do đó, báo cáo nên mô tả hệ thống theo hướng “đã xây dựng, đã kiểm thử, có khả năng mở rộng”, thay vì khẳng định toàn bộ hệ thống đã sẵn sàng ở mức production đầy đủ.

## 3.x. Phân tích mối quan hệ giữa các tác nhân

Trong nhiều đồ án, phần tác nhân thường chỉ dừng ở việc liệt kê khách hàng và quản trị viên. Tuy nhiên, với dự án này, việc phân tích mối quan hệ giữa các tác nhân với từng phân hệ sẽ giúp báo cáo có chiều sâu hơn.

### 3.x.1. Quan hệ giữa khách hàng và storefront

Khách hàng là tác nhân sử dụng storefront nhiều nhất. Tất cả các thao tác như tìm kiếm, xem sản phẩm, thêm giỏ hàng, đăng ký, đăng nhập, thanh toán, xem lịch sử đơn hàng và trò chuyện với chatbot đều diễn ra trên storefront. Storefront vì vậy là mặt hiển thị nghiệp vụ rõ nhất của hệ thống.

### 3.x.2. Quan hệ giữa quản trị viên và admin workspace

Quản trị viên làm việc trong admin shell và các màn quản lý theo module. Không giống storefront, admin là môi trường thao tác dữ liệu, điều phối nội dung và giám sát vận hành. Điều này làm cho khu vực quản trị có yêu cầu rất cao về phân quyền, thông báo trạng thái, khả năng tìm kiếm nhanh và độ ổn định của các thao tác CRUD.

### 3.x.3. Quan hệ giữa các tác nhân và API

Mặc dù người dùng cuối không trực tiếp thao tác với API ở mức hiển thị, nhưng API đóng vai trò như một giao diện dịch vụ để phục vụ client, kiểm thử, tích hợp hoặc mở rộng trong tương lai. Do đó, API có thể được xem là “lớp giao tiếp kỹ thuật” giữa hệ thống với các tác nhân hoặc hệ thống bên ngoài.

### 3.x.4. Quan hệ giữa hệ thống và các tác nhân hỗ trợ

Các dịch vụ như Google/Facebook login, PayPal, VNPay, MoMo, email và chatbot không phải là tác nhân trung tâm, nhưng lại ảnh hưởng mạnh đến các luồng xác thực, thanh toán và hỗ trợ khách hàng. Việc đưa nhóm tác nhân này vào phân tích giúp báo cáo phản ánh đúng thực tế là hệ thống không hoạt động hoàn toàn khép kín mà có sự liên kết với các dịch vụ ngoài.

## 3.x. Phân tích sâu hơn về nghiệp vụ mua hàng

### 3.x.1. Giai đoạn khám phá sản phẩm

Trước khi thêm sản phẩm vào giỏ hàng, khách hàng thường đi qua một giai đoạn khám phá thông tin. Giai đoạn này bao gồm tìm kiếm theo từ khóa, xem sản phẩm theo danh mục, theo thương hiệu, theo tag, xem video giới thiệu, theo dõi khuyến mãi flash sale, và thậm chí sử dụng chức năng so sánh sản phẩm.

Đối với mặt hàng công nghệ, đây là giai đoạn rất quan trọng, vì quyết định mua hàng thường không chỉ dựa trên giá mà còn dựa trên cấu hình, thương hiệu, nội dung truyền thông, đánh giá và trải nghiệm hỗ trợ. Chính vì vậy, các thành phần như thanh tìm kiếm, recommendation, comparison, review và chatbot đều có vai trò hỗ trợ quyết định mua.

### 3.x.2. Giai đoạn hình thành đơn hàng

Sau khi người dùng quyết định chọn sản phẩm, hệ thống chuyển sang giai đoạn hình thành đơn hàng thông qua giỏ hàng. Trong giai đoạn này, các nghiệp vụ chính bao gồm thêm sản phẩm, cập nhật số lượng, loại bỏ sản phẩm không còn nhu cầu, áp dụng coupon và tính toán phí vận chuyển.

Nếu người dùng chưa đăng nhập, hệ thống sẽ yêu cầu xác thực trước khi cho phép đi tiếp sang checkout. Cách tổ chức này giúp hệ thống quản lý đơn hàng gắn với tài khoản khách hàng rõ ràng hơn và đồng thời làm cho luồng theo dõi lịch sử đơn hàng có tính nhất quán.

### 3.x.3. Giai đoạn xác nhận và hậu kiểm đơn hàng

Sau khi đơn hàng được tạo, hệ thống tiếp tục hỗ trợ khách hàng theo dõi lịch sử mua sắm, xem chi tiết đơn hàng và hủy đơn trong các điều kiện cho phép. Điều này làm cho vòng đời nghiệp vụ không chỉ dừng ở thao tác đặt hàng, mà kéo dài sang cả giai đoạn hậu kiểm sau mua.

## 3.x. Phân tích sâu hơn về nghiệp vụ quản trị

### 3.x.1. Quản trị catalog

Catalog là lõi của hệ thống thương mại điện tử. Quản trị viên cần có khả năng tổ chức danh mục, thương hiệu, sản phẩm, thuộc tính, biến thể và gallery theo cách dễ mở rộng. Trong repo hiện tại, phần này được phản ánh qua nhiều controller khác nhau như `ProductController`, `AttributeController`, `ProductAttributeController`, `GalleryController`, `Admin/Catalog/CategoryController`, `Admin/Catalog/BrandController`.

Điều này cho thấy nghiệp vụ catalog không còn là một màn hình đơn lẻ, mà đã tách thành nhiều lớp và nhiều nhóm dữ liệu khác nhau.

### 3.x.2. Quản trị nội dung

Ngoài sản phẩm, hệ thống còn có nội dung truyền thông như bài viết, video, slider, site info, tài liệu và các phần media liên quan. Quản trị nội dung giúp website không chỉ đóng vai trò bán hàng mà còn là kênh truyền thông và chăm sóc khách hàng.

### 3.x.3. Quản trị vận hành

Quản trị vận hành bao gồm các tác vụ như theo dõi đơn hàng, delivery, reports, chat inbox, users, roles, quick search, profile và settings. Đây là nhóm nghiệp vụ cho thấy khu vực quản trị của dự án đang được phát triển theo hướng của một hệ thống vận hành nội bộ thực sự, thay vì chỉ dừng ở CRUD catalog.

## 3.x. Phân tích sâu hơn về dữ liệu và chuyển đổi kiến trúc

### 3.x.1. Lý do tồn tại song song hai lớp dữ liệu

Trong thực tế, nhiều hệ thống phần mềm không thể chuyển đổi toàn bộ cơ sở dữ liệu và mã nguồn sang một kiến trúc mới trong một lần. Dự án này cũng nằm trong bối cảnh đó. Lớp dữ liệu legacy tồn tại vì nó đang phục vụ các chức năng cũ nhưng vẫn có giá trị vận hành. Lớp schema mới được tạo ra để chuẩn hóa cấu trúc, làm nền cho API, testing, analytics và các domain/service mới.

### 3.x.2. Lợi ích của cách tiếp cận hybrid

Cách tiếp cận hybrid giúp hệ thống:

- giảm rủi ro khi thay đổi lớn
- duy trì khả năng chạy của các luồng cũ
- cho phép phát triển các thành phần mới theo chuẩn tốt hơn
- hỗ trợ refactor từng phần thay vì phải viết lại toàn bộ

### 3.x.3. Hạn chế của cách tiếp cận hybrid

Bên cạnh lợi ích, cách tiếp cận này cũng tạo ra một số thách thức:

- tài liệu phân tích dễ bị lệch nếu chỉ nhìn vào một lớp dữ liệu
- codebase có thể trông phức tạp hơn do tồn tại song song nhiều mô hình
- việc kiểm thử cần kỹ hơn để tránh đụng chéo giữa luồng cũ và luồng mới
- việc thuyết minh trong đồ án cần rõ ràng để giảng viên hiểu đúng bối cảnh

## 3.x. Gợi ý phần có thể bổ sung vào bảng biểu

Để làm báo cáo dày hơn mà vẫn có giá trị, nên bổ sung một số bảng sau:

### Bảng 3.x. So sánh lớp dữ liệu legacy và lớp dữ liệu chuẩn hóa

| Nhóm nghiệp vụ | Legacy | Chuẩn hóa mới |
|---|---|---|
| Sản phẩm | `tbl_product` | `products`, `product_variants`, `product_media` |
| Khách hàng | `tbl_customers` | `customers`, `customer_addresses` |
| Đơn hàng | `tbl_order` | `orders`, `order_items`, `payments` |
| Danh mục | `tbl_category_product` | `product_categories` |
| Coupon | `tbl_coupon` | `coupons`, `coupon_redemptions` |

### Bảng 3.x. Nhóm tác nhân và phân hệ tương ứng

| Tác nhân | Phân hệ chính | Chức năng nổi bật |
|---|---|---|
| Khách hàng | Storefront | Tìm kiếm, giỏ hàng, checkout, lịch sử đơn hàng |
| Quản trị viên | Admin | Catalog, content, orders, reports, users |
| Dịch vụ ngoài | Integration | Social login, payment gateway, email |

---

# PHẦN MỞ RỘNG CHO CHƯƠNG 4

## 4.x. Phân tích sâu hơn về môi trường và phụ thuộc kỹ thuật

### 4.x.1. Ý nghĩa của Laravel 11 trong dự án

Việc lựa chọn Laravel 11 mang lại nhiều lợi ích cho dự án. Framework này giúp hệ thống có routing rõ ràng, middleware linh hoạt, request validation, ORM Eloquent, Blade template, event, notification và testing framework tích hợp sẵn. Với một dự án đang cần vừa duy trì luồng cũ vừa chuẩn hóa dần kiến trúc, Laravel 11 đóng vai trò như nền tảng giúp tổ chức mã nguồn mạch lạc hơn.

### 4.x.2. Ý nghĩa của Sanctum

`laravel/sanctum` cho phép hệ thống triển khai xác thực API theo hướng an toàn hơn cho các endpoint protected và admin. Đây là điểm cho thấy dự án đã có định hướng hỗ trợ API thực tế, thay vì chỉ cung cấp route web thuần.

### 4.x.3. Ý nghĩa của Spatie Permission

`spatie/laravel-permission` là thành phần quan trọng trong việc quản lý vai trò và quyền ở khu vực quản trị. Việc repo dùng gói này cho thấy hệ thống admin không chỉ có một tầng đăng nhập đơn giản, mà đã phân chia quyền khá rõ theo nhóm nghiệp vụ.

## 4.x. Phân tích sâu hơn về tổ chức mã nguồn

### 4.x.1. Lý do không để toàn bộ logic trong controller

Nếu toàn bộ xử lý nghiệp vụ được đặt trong controller, mã nguồn sẽ nhanh chóng trở nên khó bảo trì khi số lượng chức năng tăng lên. Dự án hiện tại đã đi theo hướng tốt hơn bằng cách tách logic sang service, request validation, support classes, repository và domain objects.

Ví dụ, các nghiệp vụ về giỏ hàng, checkout, recommendation, reporting hay chatbot không nên bị dồn vào một controller đơn lẻ. Việc tách lớp như hiện tại giúp mỗi nhóm chức năng có phạm vi trách nhiệm rõ hơn.

### 4.x.2. Vai trò của request validation trong repo

`app/Http/Requests` là một trong những thư mục giúp hệ thống sạch hơn rõ rệt. Khi các request như `RegisterCustomerRequest`, `LoginCustomerRequest`, `ConfirmOrderRequest`, `AddCartAjaxRequest`, `StoreVideoRequest` được tách riêng, controller không còn phải ôm quá nhiều điều kiện kiểm tra đầu vào. Điều này làm cho luồng mã dễ đọc, dễ kiểm thử và ít lỗi hơn.

### 4.x.3. Vai trò của support classes

Các support classes như `StorefrontCustomerAuth`, `AdminPermissions`, `AdminAccess`, `AdminAuthenticator`, `AdminWorkspace`, `StorefrontLabel` là các lớp nhỏ nhưng có giá trị kiến trúc lớn. Chúng giúp gom các quy tắc lặp đi lặp lại ra khỏi controller và view, làm cho toàn bộ hệ thống nhất quán hơn.

## 4.x. Phân tích sâu hơn về storefront

### 4.x.1. Vai trò của header storefront

Header storefront trong dự án không chỉ là một thành phần giao diện tĩnh. Đây là nơi tập trung nhiều điểm chạm quan trọng với người dùng như:

- điều hướng chính
- thanh tìm kiếm
- gợi ý tìm kiếm
- quick cart
- hover cart
- trạng thái đăng nhập
- liên kết tới checkout và lịch sử đơn hàng

Do đó, phần header có ảnh hưởng rất lớn đến trải nghiệm người dùng và thường là nơi tích hợp nhiều logic giao diện, JavaScript và session state.

### 4.x.2. Flash Sale và recommendation

Flash Sale là một khối nội dung mang tính thương mại rõ rệt, giúp tăng mức độ thực tế của storefront. Bên cạnh đó, recommendation và các khối gợi ý sản phẩm cũng giúp website không chỉ hiển thị danh sách hàng hóa đơn thuần mà còn mang tính định hướng mua hàng.

### 4.x.3. So sánh sản phẩm và đánh giá sản phẩm

So sánh sản phẩm và đánh giá là hai chức năng đặc biệt phù hợp với lĩnh vực bán thiết bị công nghệ. Các chức năng này làm tăng giá trị cho storefront vì hỗ trợ khách hàng ra quyết định mua hàng dựa trên nhiều nguồn thông tin hơn, thay vì chỉ nhìn giá bán.

## 4.x. Phân tích sâu hơn về checkout và order

### 4.x.1. Vai trò của CartService và CheckoutService

Hai service này là trung tâm của luồng mua hàng. `CartService` phụ trách các thao tác với giỏ hàng như thêm, cập nhật, xóa, tính toán dữ liệu hiển thị. `CheckoutService` phụ trách bước xử lý sâu hơn ở giai đoạn xác nhận đơn hàng, phối hợp với các thành phần liên quan như customer, shipping, coupon và order workflow.

### 4.x.2. Tính nhất quán của luồng xác thực khách hàng

Việc đưa `StorefrontCustomerAuth` vào repo là một bước quan trọng trong việc chuẩn hóa luồng xác thực khách hàng. Nó giúp giải quyết bài toán điều hướng sau đăng nhập, quản lý session hiển thị, và giữ cho các bước từ giỏ hàng đến thanh toán diễn ra liền mạch hơn.

### 4.x.3. Liên kết giữa order history và checkout

Một hệ thống thương mại điện tử hoàn chỉnh không chỉ xử lý đặt hàng, mà còn phải hỗ trợ khách hàng theo dõi đơn đã tạo. Repo hiện đã có các route và view cho lịch sử đơn hàng, chi tiết đơn hàng và hủy đơn. Đây là phần nên được nhấn mạnh trong báo cáo vì nó cho thấy vòng đời nghiệp vụ sau mua đã được quan tâm.

## 4.x. Phân tích sâu hơn về admin

### 4.x.1. Admin shell và workspace tools

Admin không chỉ có các màn CRUD riêng lẻ mà còn có các công cụ hỗ trợ vận hành như quick search, profile, settings. Các thành phần này cho thấy repo đang hướng tới một shell quản trị có tính thống nhất, thay vì một tập hợp các trang rời rạc.

### 4.x.2. Admin reports

Các báo cáo như `ProductDiscoveryReport` và `ProductProfitReport` là minh chứng cho việc hệ thống không chỉ dừng ở thao tác quản trị dữ liệu, mà còn hỗ trợ phân tích hiệu quả vận hành và hành vi tiếp cận sản phẩm.

### 4.x.3. Admin chat inbox

Module chat inbox là một điểm mở rộng có giá trị thực tế cao. Nó kết nối trực tiếp giữa phần storefront chatbot và khu vực vận hành phía quản trị, thể hiện rằng hệ thống đã bắt đầu hình thành các vòng tương tác hai chiều giữa khách hàng và đội ngũ hỗ trợ.

## 4.x. Phân tích sâu hơn về API

### 4.x.1. API như một mặt triển khai độc lập

Trong nhiều đồ án, API chỉ được thêm như phần phụ. Tuy nhiên, trong dự án này, API đã có cấu trúc tương đối rõ ràng, chia nhóm public, protected và admin, có tài liệu API và health check. Do đó, API có thể được xem như một mặt triển khai độc lập, không chỉ là công cụ phụ trợ cho web.

### 4.x.2. Lợi ích của API versioning

Việc nhóm endpoint dưới prefix `v1` là một lựa chọn tốt vì cho phép hệ thống mở rộng về sau mà không phá vỡ các client cũ. Đây là chi tiết nhỏ nhưng có giá trị kiến trúc lớn, nên có thể được đưa vào báo cáo như một minh chứng cho tư duy thiết kế có tính mở rộng.

### 4.x.3. API documentation và health check

Sự có mặt của API docs và health check cho thấy hệ thống đã quan tâm đến khả năng quan sát, kiểm tra và tích hợp. Đây là nội dung rất đáng viết thêm vì giúp báo cáo bớt cảm giác chỉ xoay quanh giao diện.

## 4.x. Phân tích sâu hơn về console commands

### 4.x.1. Nhóm command phục vụ chuyển đổi dữ liệu

Các command như `MigrateLegacyDataCommand`, `ImportLegacyDatabaseCommand`, `SetupLegacyTablesCommand` cho thấy repo có định hướng xử lý dữ liệu cũ một cách có quy trình. Điều này là minh chứng trực tiếp cho bản chất hybrid của dự án.

### 4.x.2. Nhóm command phục vụ chuẩn hóa và vận hành

Các command như `NormalizeAdminAuthCommand`, `OptimizeApplicationCommand`, `OptimizePerformanceCommand`, `SecurityCommand`, `ProductionDeploymentCommand`, `UIUXCommand` phản ánh nỗ lực mở rộng dự án theo hướng vận hành, tối ưu và kiểm soát chất lượng.

### 4.x.3. Giá trị của console commands trong đồ án

Đối với một đồ án tốt nghiệp, việc có thêm lớp console commands là một điểm cộng rõ ràng vì nó cho thấy hệ thống không chỉ phục vụ thao tác qua giao diện mà còn có các công cụ kỹ thuật nội bộ để hỗ trợ triển khai, chuẩn hóa và tối ưu hệ thống.

## 4.x. Phân tích sâu hơn về kiểm thử

### 4.x.1. Tại sao feature test quan trọng trong repo này

Dự án hiện có rất nhiều feature test vì đây là loại kiểm thử phù hợp nhất với một hệ thống web nhiều luồng nghiệp vụ như storefront, admin và API. Feature test giúp xác minh toàn bộ luồng từ request, middleware, controller, service tới response.

### 4.x.2. Vai trò của regression test trong hệ thống hybrid

Trong một hệ thống hybrid, thay đổi ở một vùng code có thể làm ảnh hưởng tới nhiều vùng khác mà người phát triển không dự đoán hết. Vì vậy, regression test có vai trò đặc biệt quan trọng. Đây là lý do báo cáo nên dành thêm dung lượng cho phần test, thay vì chỉ liệt kê vài test case mẫu.

### 4.x.3. Kiểm thử như một minh chứng cho độ trưởng thành của dự án

Một điểm mà anh có thể nhấn mạnh khi bảo vệ là: việc dự án có nhiều nhóm feature test không chỉ giúp bắt lỗi, mà còn phản ánh mức độ trưởng thành của mã nguồn. Nói cách khác, chất lượng của dự án không chỉ được thể hiện qua số lượng chức năng, mà còn qua khả năng kiểm soát chất lượng trong quá trình phát triển.

---

# PHẦN MỞ RỘNG CHO CHƯƠNG 5 VÀ PHỤ LỤC

## 5.x. Có thể viết thêm gì ở Chương 5

Nếu muốn kéo dài Chương 5 mà vẫn hợp lý, anh có thể viết thêm các mục sau.

### 5.x.1. Đánh giá ưu điểm của hệ thống

Phần này có thể viết sâu hơn về:

- cấu trúc mã nguồn rõ ràng
- có sự phân tách nghiệp vụ tốt hơn MVC cơ bản
- có API, chatbot, reports, commands
- có kiểm thử ở nhiều nhóm chức năng
- storefront và admin đều đã được đầu tư

### 5.x.2. Đánh giá hạn chế của hệ thống

Phần hạn chế nên nói thật và đúng:

- hệ thống còn đang hybrid giữa dữ liệu cũ và kiến trúc mới
- chưa tích hợp đầy đủ mọi dịch vụ production thực tế
- một số luồng legacy vẫn cần tiếp tục chuẩn hóa
- UI/UX dù đã cải thiện nhưng còn khả năng tối ưu thêm

### 5.x.3. Hướng phát triển trong tương lai

Có thể viết thành các hướng riêng:

- chuẩn hóa dần toàn bộ legacy sang schema mới
- mở rộng API cho mobile app hoặc PWA
- cá nhân hóa recommendation tốt hơn
- tích hợp sâu hơn thanh toán và vận chuyển thực tế
- mở rộng dashboard analytics và marketing automation
- nâng cấp chatbot theo hướng thông minh hơn

## Phụ lục A. Gợi ý bảng route để đưa vào báo cáo

### Bảng phụ lục A.1. Một số route storefront tiêu biểu

| Route | Controller | Chức năng |
|---|---|---|
| `/` | `HomeController@index` | Trang chủ |
| `/tim-kiem` | `HomeController@search` | Tìm kiếm sản phẩm |
| `/autocomplete-ajax` | `HomeController@autocomplete_ajax` | Gợi ý tìm kiếm |
| `/gio-hang` | `CartController@gio_hang` | Giỏ hàng |
| `/checkout` | `CheckoutController@checkout` | Thanh toán |
| `/history` | `OrderController@history` | Lịch sử đơn hàng |

### Bảng phụ lục A.2. Một số route admin tiêu biểu

| Route | Controller | Chức năng |
|---|---|---|
| `/dashboard` | `AdminController@show_dashboard` | Dashboard admin |
| `/all-product` | `ProductController@all_product` | Quản lý sản phẩm |
| `/manage-order` | `OrderController@manage_order` | Quản lý đơn hàng |
| `/product-profit` | `ProductProfitController@index` | Báo cáo lợi nhuận |
| `/product-discovery-report` | `ProductDiscoveryReportController@index` | Báo cáo khám phá sản phẩm |

## Phụ lục B. Gợi ý bảng test suite để đưa vào báo cáo

### Bảng phụ lục B.1. Nhóm test storefront

| File test | Nhóm chức năng |
|---|---|
| `HomepageTest.php` | Trang chủ |
| `CartFlowTest.php` | Giỏ hàng |
| `CheckoutFlowTest.php` | Checkout |
| `CustomerRegistrationTest.php` | Đăng ký khách hàng |
| `StorefrontLoginAndSearchPageTest.php` | Đăng nhập và tìm kiếm |
| `StorefrontLayoutShellTest.php` | Bố cục storefront |

### Bảng phụ lục B.2. Nhóm test admin

| File test | Nhóm chức năng |
|---|---|
| `AdminPanelTest.php` | Dashboard admin |
| `AdminProductCrudTest.php` | CRUD sản phẩm |
| `AdminAuthFlowTest.php` | Đăng nhập admin |
| `AdminWorkspaceToolsTest.php` | Quick search, profile, settings |
| `AdminOrderApiTest.php` | API đơn hàng quản trị |
| `AdminStatisticsApiTest.php` | API thống kê |

## Phụ lục C. Gợi ý thêm phần mô tả hình ảnh

Nếu muốn báo cáo dày thêm mà vẫn hợp lý, mỗi hình giao diện có thể kèm một đoạn mô tả 4-6 dòng, ví dụ:

- hình trang chủ: mô tả bố cục, flash sale, hero, search, recommendation
- hình chi tiết sản phẩm: mô tả giá, tồn kho, add to cart, review, compare
- hình giỏ hàng: mô tả session cart, coupon, quick actions
- hình checkout: mô tả xác thực khách hàng, phí vận chuyển, thanh toán
- hình admin dashboard: mô tả shell, sidebar, topbar, quick tools

Việc viết mô tả dưới hình đầy đủ sẽ giúp tăng độ dày của báo cáo một cách tự nhiên, không bị cảm giác thêm chữ vô nghĩa.

---

# KẾT LUẬN CHO PHẦN MỞ RỘNG

Nếu anh muốn báo cáo dài thêm mà vẫn giữ được chất lượng, nên kéo dài theo 4 hướng:

- phân tích sâu hơn bối cảnh hybrid legacy + modernized
- mô tả kỹ hơn kiến trúc mã nguồn theo repo thật
- viết đầy đủ hơn về kiểm thử, API, reports, commands
- thêm phụ lục bảng route, bảng test suite, bảng mapping dữ liệu và mô tả dưới hình

Đây là các phần tăng được số trang nhưng vẫn làm báo cáo thuyết phục hơn khi bảo vệ.
