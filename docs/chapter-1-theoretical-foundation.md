# CHƯƠNG 1. CƠ SỞ LÝ THUYẾT

## 1.1. Mở đầu chương

Chương này trình bày các cơ sở lý thuyết làm nền tảng cho việc xây dựng website bán hàng bằng Laravel. Theo định hướng của mục `Graduation Thesis Report Guide` trong `DOCUMENTATION_TEMPLATE.md`, Chương 1 tập trung vào các khái niệm cơ bản liên quan đến đề tài, lý thuyết nền tảng trong phát triển hệ thống, công nghệ và ngôn ngữ lập trình sử dụng, framework và thư viện liên quan, cùng một số tiêu chuẩn và quy trình phát triển phần mềm áp dụng trong phạm vi đồ án.

Đề tài thuộc nhóm đề tài lập trình, vì vậy nội dung chương không đi sâu vào mô tả mã nguồn triển khai cụ thể mà trình bày những kiến thức nền cần thiết để hiểu các chương sau. Các nội dung chính bao gồm website thương mại điện tử, mô hình MVC, framework `Laravel 11`, `Blade` template engine, cơ sở dữ liệu quan hệ và `MySQL`, `Eloquent ORM`, routing, controller, middleware, session, authentication, cart, checkout, frontend với `HTML`, `CSS`, `JavaScript`, `Vite`, kiểm tra chất lượng phần mềm trong Laravel và bảo mật cơ bản cho website bán hàng.


## 1.2. Các khái niệm cơ bản về website thương mại điện tử

### 1.2.1. Thương mại điện tử

Thương mại điện tử là hình thức thực hiện một phần hoặc toàn bộ hoạt động mua bán hàng hóa, dịch vụ thông qua phương tiện điện tử, phổ biến nhất là Internet. Trong môi trường thương mại điện tử, các hoạt động như giới thiệu sản phẩm, tìm kiếm thông tin, đặt hàng, thanh toán, chăm sóc khách hàng và quản lý dữ liệu có thể được số hóa để giảm thao tác thủ công và mở rộng phạm vi tiếp cận khách hàng.

Đối với doanh nghiệp bán lẻ, thương mại điện tử tạo ra một kênh bán hàng trực tuyến hoạt động liên tục, không bị giới hạn bởi vị trí địa lý và thời gian mở cửa như cửa hàng truyền thống. Đối với khách hàng, website thương mại điện tử giúp quá trình mua sắm thuận tiện hơn vì người dùng có thể xem thông tin sản phẩm, so sánh giá, đọc đánh giá, thêm sản phẩm vào giỏ hàng và đặt mua ngay trên trình duyệt.

Trong phạm vi đề tài này, thương mại điện tử được tiếp cận theo hướng xây dựng một website bán hàng, có giao diện khách hàng để duyệt và mua sản phẩm, đồng thời có khu vực quản trị để quản lý dữ liệu sản phẩm, danh mục, thương hiệu, đơn hàng, nội dung và người dùng nội bộ.

### 1.2.2. Website bán hàng

Website bán hàng là một hệ thống phần mềm hoạt động trên nền web, hỗ trợ doanh nghiệp giới thiệu sản phẩm, tiếp nhận đơn hàng và quản lý các nghiệp vụ bán hàng trực tuyến. Một website bán hàng thông thường gồm hai phần chính: phần hiển thị cho khách hàng và phần quản trị cho người vận hành.

Phần hiển thị cho khách hàng thường bao gồm trang chủ, danh sách sản phẩm, chi tiết sản phẩm, tìm kiếm, giỏ hàng, checkout, đăng nhập, đăng ký, lịch sử đơn hàng và các trang nội dung hỗ trợ. Phần quản trị thường bao gồm quản lý sản phẩm, danh mục, thương hiệu, đơn hàng, khách hàng, mã giảm giá, bài viết, banner, phân quyền và báo cáo.

Đối với website bán hàng, dữ liệu có mối quan hệ chặt chẽ với nhau. Một sản phẩm có thể thuộc danh mục và thương hiệu; một đơn hàng có nhiều chi tiết đơn hàng; một khách hàng có thể có nhiều đơn hàng; một mã giảm giá có thể được áp dụng trong quá trình checkout. Vì vậy, thiết kế hệ thống cần bảo đảm tính nhất quán dữ liệu, khả năng mở rộng và an toàn trong các luồng giao dịch.

### 1.2.3. Mô hình B2C trong website bán hàng

Mô hình B2C, viết tắt của Business to Customer, là mô hình doanh nghiệp bán hàng trực tiếp cho khách hàng cuối. Đây là mô hình phù hợp với đề tài vì hệ thống hướng tới việc cho phép khách hàng truy cập website, xem sản phẩm, thêm vào giỏ hàng, đặt hàng và theo dõi đơn hàng.

Trong mô hình B2C, website không chỉ đóng vai trò là nơi trưng bày sản phẩm mà còn là điểm tiếp xúc chính giữa doanh nghiệp và khách hàng. Chất lượng giao diện, tốc độ phản hồi, độ chính xác của thông tin sản phẩm, tính thuận tiện của giỏ hàng và checkout đều ảnh hưởng trực tiếp tới trải nghiệm mua sắm.

Về phía quản trị, mô hình B2C yêu cầu hệ thống hỗ trợ cập nhật sản phẩm, quản lý tồn tại dữ liệu đơn hàng, xử lý trạng thái giao hàng, quản lý tài khoản khách hàng và bảo vệ dữ liệu cá nhân. Đây là cơ sở để các chương sau phân tích chi tiết hơn các chức năng storefront và admin.

### 1.2.4. Các thành phần nghiệp vụ cơ bản

Một website bán hàng thường bao gồm các thành phần nghiệp vụ chính sau:

- Catalog: tập hợp dữ liệu sản phẩm, danh mục, thương hiệu, hình ảnh, biến thể và thuộc tính sản phẩm.
- Search: chức năng tìm kiếm sản phẩm theo từ khóa, danh mục hoặc các tiêu chí liên quan.
- Product detail: trang chi tiết sản phẩm, cung cấp thông tin mô tả, giá, hình ảnh, đánh giá và các sản phẩm liên quan.
- Cart: giỏ hàng, nơi lưu các sản phẩm khách hàng có ý định mua trước khi đặt hàng.
- Checkout: luồng xác nhận thông tin giao hàng, phí vận chuyển, mã giảm giá, phương thức thanh toán và tạo đơn hàng.
- Order management: quản lý đơn hàng, trạng thái xử lý, chi tiết sản phẩm trong đơn và thông tin giao nhận.
- Customer account: quản lý tài khoản khách hàng, lịch sử mua hàng, thông tin cá nhân và mật khẩu.
- Admin management: khu vực dành cho quản trị viên để vận hành dữ liệu sản phẩm, nội dung, đơn hàng, người dùng và quyền truy cập.

Các thành phần này liên kết với nhau thành một vòng đời mua hàng tương đối đầy đủ: khách hàng truy cập website, khám phá sản phẩm, chọn sản phẩm, thêm vào giỏ, checkout, tạo đơn hàng và theo dõi trạng thái xử lý.

## 1.3. Mô hình MVC

### 1.3.1. Khái niệm MVC

MVC là viết tắt của Model - View - Controller, một mô hình kiến trúc phổ biến trong phát triển ứng dụng web. Mục tiêu của MVC là phân tách trách nhiệm giữa các thành phần trong hệ thống, giúp mã nguồn dễ đọc, dễ bảo trì và dễ mở rộng hơn.

Trong mô hình MVC, Model chịu trách nhiệm biểu diễn dữ liệu và các thao tác liên quan đến dữ liệu. View chịu trách nhiệm hiển thị giao diện cho người dùng. Controller chịu trách nhiệm tiếp nhận request, điều phối xử lý, gọi model hoặc các lớp nghiệp vụ cần thiết và trả về response phù hợp.

Đối với một website bán hàng, MVC giúp tổ chức hệ thống theo các nhóm trách nhiệm rõ ràng. Ví dụ, dữ liệu sản phẩm được biểu diễn qua model, giao diện danh sách sản phẩm được hiển thị bằng view, còn controller điều phối việc nhận request từ người dùng, lấy dữ liệu sản phẩm và trả về trang hiển thị.

### 1.3.2. Vai trò của Model

Model là thành phần đại diện cho dữ liệu và các quy tắc liên quan đến dữ liệu. Trong Laravel, model thường kế thừa từ hệ thống `Eloquent ORM`, giúp ánh xạ bảng trong cơ sở dữ liệu thành đối tượng trong PHP.

Trong website bán hàng, các model thường tương ứng với những thực thể nghiệp vụ như sản phẩm, danh mục, thương hiệu, khách hàng, giỏ hàng, đơn hàng, chi tiết đơn hàng, mã giảm giá và thanh toán. Model giúp hệ thống làm việc với dữ liệu theo hướng đối tượng thay vì luôn phải viết truy vấn SQL trực tiếp.

### 1.3.3. Vai trò của View

View là thành phần chịu trách nhiệm trình bày dữ liệu cho người dùng. Trong Laravel, view thường được viết bằng `Blade`, lưu trong thư mục `resources/views`. View không nên chứa nhiều logic nghiệp vụ phức tạp mà chủ yếu nhận dữ liệu từ controller và hiển thị theo cấu trúc giao diện.

Đối với website bán hàng, view có vai trò rất quan trọng vì đây là nơi người dùng trực tiếp tương tác với hệ thống. Các trang như trang chủ, danh mục, chi tiết sản phẩm, giỏ hàng, checkout, đăng nhập và dashboard admin đều cần được tổ chức thành các view rõ ràng, có khả năng tái sử dụng layout và partial.

### 1.3.4. Vai trò của Controller

Controller là thành phần trung gian giữa request từ người dùng và phần xử lý phía sau. Controller nhận request từ route, kiểm tra dữ liệu đầu vào nếu cần, gọi model hoặc service để xử lý nghiệp vụ, sau đó trả về view, redirect hoặc JSON response.

Trong Laravel, controller thường được lưu trong `app/Http/Controllers`. Với website bán hàng, controller có thể phụ trách các nhóm chức năng như trang chủ, sản phẩm, giỏ hàng, checkout, đơn hàng, tài khoản khách hàng và quản trị. Việc giữ controller mỏng, tập trung vào điều phối, là một nguyên tắc quan trọng để hệ thống dễ bảo trì.

## 1.4. Framework Laravel

### 1.4.1. Tổng quan về Laravel

Laravel là một framework phát triển ứng dụng web mã nguồn mở sử dụng ngôn ngữ `PHP`. Framework này cung cấp sẵn nhiều thành phần nền tảng như routing, controller, middleware, request validation, authentication, authorization, migration, `Eloquent ORM`, `Blade`, queue, cache và session.

Việc sử dụng framework giúp giảm thời gian xây dựng các chức năng cơ bản, đồng thời tạo ra một cấu trúc dự án nhất quán. Thay vì tự xây dựng toàn bộ từ đầu, lập trình viên có thể dựa trên các thành phần đã được chuẩn hóa của Laravel để tập trung vào nghiệp vụ chính của hệ thống.

Trong dự án hiện tại, `composer.json` xác nhận framework chính là `laravel/framework` phiên bản `^11.9`, chạy trên `PHP` phiên bản `^8.2`. Đây là nền tảng backend chính để xây dựng website bán hàng.

### 1.4.2. Laravel 11 trong phát triển website bán hàng

`Laravel 11` phù hợp với website bán hàng vì cung cấp đầy đủ công cụ để phát triển cả giao diện web truyền thống và API. Đối với storefront, Laravel có thể trả về các trang `Blade` server-rendered. Đối với admin và các tích hợp khác, Laravel có thể tổ chức route, middleware, policy, request validation và API theo từng nhóm chức năng.

Trong dự án, route web được chia thành `routes/web/frontend.php` và `routes/web/admin.php`, cho thấy hệ thống phân tách giữa luồng khách hàng và luồng quản trị. Ngoài ra, `routes/api.php` tổ chức các endpoint dạng `v1`, bao gồm nhóm public API, protected API dùng `auth:sanctum` và admin API có kiểm tra quyền. Cách tổ chức này phù hợp với đặc điểm của một website thương mại điện tử cần phục vụ nhiều loại người dùng và nhiều kiểu truy cập.

### 1.4.3. Một số thành phần chính của Laravel

Laravel cung cấp nhiều thành phần quan trọng cho quá trình xây dựng hệ thống:

- Routing: định nghĩa URL, HTTP method và controller xử lý.
- Middleware: xử lý request trước hoặc sau controller, ví dụ xác thực, phân quyền, chống truy cập sai khu vực.
- Controller: điều phối xử lý request.
- Request validation: kiểm tra dữ liệu đầu vào.
- `Eloquent ORM`: thao tác với cơ sở dữ liệu quan hệ thông qua model.
- Migration: định nghĩa và quản lý thay đổi cấu trúc cơ sở dữ liệu.
- `Blade`: xây dựng giao diện server-rendered.
- Session: lưu trạng thái tạm thời giữa các request.
- Authentication và authorization: xác thực danh tính và kiểm soát quyền truy cập.

Các thành phần này tạo thành nền tảng để xây dựng website bán hàng có cấu trúc rõ ràng và phù hợp với quy trình phát triển phần mềm hiện đại.

## 1.5. Blade template engine

### 1.5.1. Khái niệm Blade

`Blade` là template engine mặc định của Laravel, cho phép xây dựng giao diện HTML động bằng cú pháp ngắn gọn và dễ đọc. Blade hỗ trợ kế thừa layout, include partial, vòng lặp, điều kiện, hiển thị dữ liệu, escaping output và nhiều directive tiện ích.

Khác với việc viết PHP thuần trực tiếp trong HTML, Blade giúp giao diện được tổ chức rõ ràng hơn. Các file giao diện thường có phần mở rộng `.blade.php` và được lưu trong `resources/views`.

### 1.5.2. Vai trò của Blade trong website bán hàng

Trong website bán hàng, Blade phù hợp với các trang cần render phía server như trang chủ, danh mục, chi tiết sản phẩm, giỏ hàng, checkout, đăng nhập và các trang quản trị. Dữ liệu được controller hoặc service chuẩn bị, sau đó truyền sang view để hiển thị.

Blade cũng hỗ trợ tái sử dụng giao diện thông qua layout và partial. Ví dụ, các thành phần như header, footer, product card, cart badge, modal hoặc sidebar có thể được tách thành partial để dùng lại ở nhiều trang. Điều này giúp giảm trùng lặp mã giao diện và làm cho hệ thống dễ bảo trì hơn.

Trong dự án hiện tại, thư mục `resources/views` chứa nhiều view và partial cho storefront, admin, checkout, cart, product detail và mail template. Điều này cho thấy Blade là công nghệ giao diện server-rendered chính của hệ thống.

### 1.5.3. Ưu điểm và giới hạn của Blade

Ưu điểm của Blade là dễ học, tích hợp chặt chẽ với Laravel, phù hợp với mô hình MVC và hỗ trợ tốt cho website server-rendered. Blade đặc biệt hiệu quả khi hệ thống cần trả về HTML hoàn chỉnh từ server, đồng thời vẫn có thể kết hợp thêm JavaScript để xử lý các tương tác cục bộ.

Tuy nhiên, Blade không thay thế hoàn toàn frontend framework. Với các giao diện có tương tác phức tạp, cần cập nhật trạng thái liên tục hoặc yêu cầu trải nghiệm giống ứng dụng một trang, hệ thống có thể cần bổ sung JavaScript hoặc framework như `Vue`. Trong dự án hiện tại, `package.json` có `vue`, `pinia` và `vue-router`, nhưng phần cơ sở lý thuyết của chương này chỉ trình bày ở mức công nghệ frontend có trong dự án, không giả định toàn bộ hệ thống là SPA.

## 1.6. Cơ sở dữ liệu quan hệ và MySQL

### 1.6.1. Khái niệm cơ sở dữ liệu quan hệ

Cơ sở dữ liệu quan hệ là mô hình lưu trữ dữ liệu dưới dạng các bảng. Mỗi bảng gồm các dòng và cột, trong đó mỗi dòng biểu diễn một bản ghi, mỗi cột biểu diễn một thuộc tính. Các bảng có thể liên kết với nhau thông qua khóa chính và khóa ngoại.

Mô hình quan hệ phù hợp với website bán hàng vì dữ liệu thương mại điện tử thường có cấu trúc rõ ràng và nhiều mối quan hệ. Ví dụ, sản phẩm thuộc danh mục, sản phẩm thuộc thương hiệu, khách hàng có nhiều đơn hàng, đơn hàng có nhiều dòng chi tiết, mã giảm giá có thể liên quan đến đơn hàng hoặc khách hàng.

### 1.6.2. MySQL trong hệ thống Laravel

`MySQL` là hệ quản trị cơ sở dữ liệu quan hệ phổ biến, được sử dụng rộng rãi trong các ứng dụng web. MySQL hỗ trợ lưu trữ dữ liệu có cấu trúc, truy vấn bằng SQL, tạo chỉ mục, khóa chính, khóa ngoại và transaction.

Trong dự án hiện tại, file `.env.example` cấu hình `DB_CONNECTION=mysql`, còn `config/database.php` có cấu hình kết nối `mysql`. Vì vậy, khi trình bày cơ sở lý thuyết của đề tài, có thể xác định MySQL là hệ quản trị cơ sở dữ liệu quan hệ chính theo cấu hình dự án.

### 1.6.3. Chuẩn hóa dữ liệu và quan hệ bảng

Chuẩn hóa dữ liệu là quá trình tổ chức cơ sở dữ liệu nhằm giảm dư thừa, hạn chế sai lệch dữ liệu và bảo đảm tính nhất quán. Trong website bán hàng, nếu dữ liệu sản phẩm, danh mục, đơn hàng và khách hàng không được tổ chức hợp lý, hệ thống dễ gặp lỗi khi cập nhật, thống kê hoặc mở rộng chức năng.

Một số quan hệ thường gặp trong website bán hàng gồm:

- Một danh mục có nhiều sản phẩm.
- Một thương hiệu có nhiều sản phẩm.
- Một khách hàng có nhiều đơn hàng.
- Một đơn hàng có nhiều chi tiết đơn hàng.
- Một sản phẩm có thể có nhiều hình ảnh, đánh giá hoặc biến thể.
- Một mã giảm giá có thể được áp dụng trong nhiều giao dịch tùy theo điều kiện.

Việc thiết kế đúng quan hệ bảng là nền tảng để các chức năng như tìm kiếm sản phẩm, checkout, quản lý đơn hàng và báo cáo vận hành hoạt động chính xác.

### 1.6.4. Transaction và tính nhất quán dữ liệu

Transaction là cơ chế cho phép nhóm nhiều thao tác cơ sở dữ liệu thành một đơn vị xử lý thống nhất. Nếu tất cả thao tác thành công, transaction được commit; nếu có lỗi, các thay đổi được rollback để tránh dữ liệu ở trạng thái nửa vời.

Trong website bán hàng, transaction đặc biệt quan trọng ở các luồng như tạo đơn hàng, ghi chi tiết đơn hàng, cập nhật thanh toán, áp dụng mã giảm giá và trừ tồn kho nếu hệ thống có quản lý tồn. Một lỗi trong quá trình checkout có thể làm sai lệch dữ liệu nếu hệ thống không bảo đảm tính nguyên tử của các thao tác liên quan.

## 1.7. Eloquent ORM

### 1.7.1. Khái niệm ORM

ORM, viết tắt của Object Relational Mapping, là kỹ thuật ánh xạ dữ liệu giữa cơ sở dữ liệu quan hệ và đối tượng trong ngôn ngữ lập trình. Thay vì luôn viết câu lệnh SQL trực tiếp, lập trình viên có thể thao tác với các model và quan hệ giữa model.

`Eloquent ORM` là ORM mặc định của Laravel. Mỗi model Eloquent thường đại diện cho một bảng trong cơ sở dữ liệu. Model có thể khai báo quan hệ như một-nhiều, nhiều-nhiều, một-một, đồng thời hỗ trợ query builder, scope, accessor, mutator và mass assignment.

### 1.7.2. Vai trò của Eloquent trong website bán hàng

Trong website bán hàng, Eloquent giúp biểu diễn các thực thể nghiệp vụ như sản phẩm, danh mục, thương hiệu, khách hàng, giỏ hàng, đơn hàng và thanh toán. Khi cần lấy danh sách sản phẩm theo danh mục, lấy đơn hàng của khách hàng hoặc lấy chi tiết đơn hàng, hệ thống có thể sử dụng quan hệ Eloquent để mã nguồn rõ ràng hơn.

Eloquent cũng hỗ trợ migration và factory trong hệ sinh thái Laravel. Migration giúp định nghĩa schema theo mã nguồn, còn factory giúp tạo dữ liệu giả phục vụ kiểm tra chất lượng. Trong dự án hiện tại, thư mục `database/migrations` và `database/factories` cho thấy hệ thống có sử dụng các công cụ dữ liệu tiêu chuẩn của Laravel.

### 1.7.3. Lưu ý khi sử dụng ORM

Mặc dù ORM giúp tăng tốc phát triển, lập trình viên vẫn cần hiểu cơ sở dữ liệu quan hệ và SQL. Nếu sử dụng quan hệ không hợp lý, hệ thống có thể gặp vấn đề hiệu năng như truy vấn lặp lại nhiều lần, còn gọi là N+1 query. Ngoài ra, với các báo cáo phức tạp hoặc truy vấn lớn, việc tối ưu SQL, chỉ mục và eager loading vẫn rất quan trọng.

Vì vậy, Eloquent nên được xem là công cụ hỗ trợ truy cập dữ liệu, không thay thế hoàn toàn kiến thức về thiết kế cơ sở dữ liệu và tối ưu truy vấn.

## 1.8. Routing, controller và middleware

### 1.8.1. Routing

Routing là cơ chế ánh xạ URL và HTTP method tới logic xử lý tương ứng. Trong Laravel, route thường được định nghĩa trong các file như `routes/web.php`, `routes/api.php` hoặc các file route được tách nhỏ theo nhu cầu dự án.

Đối với website bán hàng, route giúp tổ chức các luồng truy cập như trang chủ, danh sách sản phẩm, chi tiết sản phẩm, giỏ hàng, checkout, đăng nhập, quản trị và API. Việc phân nhóm route rõ ràng giúp hệ thống dễ đọc, dễ bảo trì và dễ áp dụng middleware.

Trong dự án hiện tại, `routes/web.php` chỉ require hai file `routes/web/frontend.php` và `routes/web/admin.php`. Cách tách này giúp phân biệt khu vực khách hàng và khu vực quản trị. Ngoài ra, `routes/api.php` định nghĩa nhóm API theo prefix `v1`, bao gồm public API, protected API và admin API.

### 1.8.2. Controller

Controller tiếp nhận request đã được route xác định và điều phối xử lý. Một controller có thể trả về view, redirect, JSON response hoặc gọi các lớp service để xử lý nghiệp vụ phức tạp hơn.

Trong website bán hàng, controller thường được chia theo nhóm chức năng. Ví dụ, controller sản phẩm xử lý danh sách và chi tiết sản phẩm; controller giỏ hàng xử lý thêm, sửa, xóa sản phẩm trong giỏ; controller checkout xử lý thông tin giao hàng và tạo đơn; controller admin xử lý các chức năng quản trị.

Nguyên tắc thiết kế tốt là controller không nên chứa quá nhiều logic nghiệp vụ phức tạp. Khi nghiệp vụ tăng lên, các logic này nên được tách sang service, request class hoặc các lớp hỗ trợ phù hợp để bảo đảm controller giữ vai trò điều phối.

### 1.8.3. Middleware

Middleware là lớp trung gian xử lý request trước khi request đi tới controller hoặc trước khi response trả về client. Middleware thường được sử dụng để xác thực, kiểm tra quyền, chống CSRF, giới hạn số lần request, thêm header bảo mật hoặc xử lý điều kiện truy cập.

Trong dự án hiện tại, route admin trong `routes/web/admin.php` sử dụng các middleware như `guest:web`, `auth:web`, `admin.access` và `admin.permission`. Điều này phản ánh yêu cầu cơ bản của website bán hàng: khu vực quản trị phải được bảo vệ, chỉ người dùng đã đăng nhập và có quyền phù hợp mới được truy cập.

Middleware giúp tách logic kiểm soát truy cập khỏi controller. Nhờ đó, controller có thể tập trung vào nghiệp vụ, còn điều kiện truy cập được cấu hình ngay trên route hoặc route group.

## 1.9. Session, authentication, cart và checkout

### 1.9.1. Session trong ứng dụng web

HTTP là giao thức stateless, nghĩa là mỗi request độc lập và server không tự ghi nhớ trạng thái của request trước đó. Session được sử dụng để lưu trạng thái tạm thời giữa nhiều request, ví dụ trạng thái đăng nhập, thông báo flash, dữ liệu giỏ hàng hoặc lựa chọn tạm thời của người dùng.

Trong Laravel, session có thể được lưu bằng nhiều driver như file, database, redis hoặc cookie tùy cấu hình. Dự án hiện tại có `.env.example` cấu hình `SESSION_DRIVER=database`, cho thấy session có thể được lưu trong cơ sở dữ liệu. Cách lưu này phù hợp khi cần quản lý session ổn định hơn trong môi trường triển khai.

### 1.9.2. Authentication

Authentication là quá trình xác thực danh tính người dùng. Trong website bán hàng, authentication thường áp dụng cho hai nhóm chính: khách hàng và quản trị viên. Khách hàng đăng nhập để checkout, xem lịch sử đơn hàng hoặc quản lý tài khoản. Quản trị viên đăng nhập để truy cập khu vực admin.

Laravel cung cấp các thành phần hỗ trợ authentication như guard, provider, password hashing, remember token và middleware `auth`. Dự án hiện tại sử dụng `laravel/ui` và có các route `Auth::routes()` trong `routes/web/frontend.php`, đồng thời có luồng đăng nhập admin riêng trong `routes/web/admin.php`.

Đối với API, authentication có thể được triển khai bằng token. Dự án có dependency `laravel/sanctum` và trong `routes/api.php` có nhóm route dùng middleware `auth:sanctum`, phù hợp với nhu cầu bảo vệ API cho người dùng đã xác thực.

### 1.9.3. Authorization và phân quyền

Authorization là quá trình xác định người dùng đã xác thực có được phép thực hiện một hành động cụ thể hay không. Trong website bán hàng, authorization đặc biệt quan trọng ở khu vực admin, nơi có nhiều nhóm chức năng như quản lý sản phẩm, đơn hàng, nội dung, người dùng, mã giảm giá và cài đặt hệ thống.

Một mô hình phổ biến là RBAC, viết tắt của Role-Based Access Control. Theo mô hình này, quyền được gán cho vai trò, còn người dùng được gán một hoặc nhiều vai trò. Dự án hiện tại sử dụng `spatie/laravel-permission`, một thư viện phổ biến trong Laravel để quản lý role và permission. Các middleware dạng `admin.permission` trong `routes/web/admin.php` cho thấy hệ thống có kiểm soát quyền theo nhóm chức năng.

### 1.9.4. Cart

Cart, hay giỏ hàng, là thành phần lưu các sản phẩm mà khách hàng dự định mua. Giỏ hàng thường bao gồm thông tin sản phẩm, số lượng, giá tại thời điểm chọn, thuộc tính biến thể nếu có và tổng tiền tạm tính.

Về mặt lý thuyết, giỏ hàng có thể được lưu trong session, cơ sở dữ liệu hoặc kết hợp cả hai. Nếu khách chưa đăng nhập, session thường được dùng để giữ giỏ hàng tạm thời. Nếu khách đã đăng nhập, hệ thống có thể đồng bộ giỏ hàng với tài khoản để giữ dữ liệu qua nhiều thiết bị hoặc nhiều phiên truy cập.

Trong dự án hiện tại, các route như `/add-cart-ajax`, `/update-cart`, `/gio-hang`, `/show_quick_cart` và các migration liên quan đến `carts`, `cart_items` cho thấy giỏ hàng là một nghiệp vụ trung tâm của hệ thống.

### 1.9.5. Checkout

Checkout là quá trình chuyển giỏ hàng thành đơn hàng. Đây là một trong những luồng nghiệp vụ quan trọng nhất của website bán hàng vì nó liên quan trực tiếp đến dữ liệu khách hàng, địa chỉ giao hàng, phí vận chuyển, mã giảm giá, phương thức thanh toán và tạo đơn hàng.

Một luồng checkout cơ bản thường gồm các bước:

- Kiểm tra giỏ hàng có sản phẩm hợp lệ.
- Xác định thông tin khách hàng.
- Nhập hoặc chọn địa chỉ giao hàng.
- Tính phí vận chuyển và mã giảm giá nếu có.
- Chọn phương thức thanh toán.
- Xác nhận đơn hàng.
- Tạo đơn hàng và chi tiết đơn hàng trong cơ sở dữ liệu.
- Hiển thị kết quả đặt hàng hoặc hướng dẫn thanh toán.

Trong Laravel, checkout cần được thiết kế cẩn thận với validation, transaction, session, authentication và bảo mật CSRF. Dự án hiện tại có các route như `/checkout`, `/payment`, `/calculate-fee`, `/select-delivery-home`, `/confirm-order` và `/payment/bank-transfer/{order_code}`, thể hiện các bước cơ bản của luồng checkout.

## 1.10. Frontend với HTML, CSS, JavaScript và Vite

### 1.10.1. HTML

`HTML`, viết tắt của HyperText Markup Language, là ngôn ngữ đánh dấu dùng để xây dựng cấu trúc nội dung của trang web. Trong website bán hàng, HTML biểu diễn các thành phần như tiêu đề, menu, danh sách sản phẩm, form tìm kiếm, nút thêm giỏ hàng, bảng đơn hàng và nội dung checkout.

HTML cần được tổ chức có ngữ nghĩa để cải thiện khả năng đọc, khả năng truy cập và hỗ trợ công cụ tìm kiếm. Ví dụ, tiêu đề trang nên dùng đúng cấp heading, form cần có input và label phù hợp, nút thao tác nên dùng đúng phần tử button hoặc anchor tùy ngữ cảnh.

### 1.10.2. CSS

`CSS`, viết tắt của Cascading Style Sheets, là ngôn ngữ dùng để định dạng giao diện. CSS quy định màu sắc, khoảng cách, bố cục, typography, responsive layout và trạng thái tương tác của các thành phần giao diện.

Trong website bán hàng, CSS ảnh hưởng trực tiếp tới trải nghiệm người dùng. Giao diện cần rõ ràng, dễ quét thông tin, hiển thị tốt trên nhiều kích thước màn hình và bảo đảm các thao tác như tìm kiếm, thêm giỏ hàng, xem chi tiết sản phẩm, checkout không gây nhầm lẫn.

Dự án hiện tại có các file CSS trong `resources/css` và sử dụng các dependency như `bootstrap`, `tailwindcss`, `sass`, `autoprefixer` trong `package.json`. Điều này cho thấy phần frontend được tổ chức thông qua pipeline build thay vì chỉ dùng CSS tĩnh rời rạc.

### 1.10.3. JavaScript

`JavaScript` là ngôn ngữ lập trình phía client, dùng để xử lý tương tác trên trình duyệt. Trong website bán hàng, JavaScript thường được dùng cho tìm kiếm gợi ý, thêm giỏ hàng bằng AJAX, cập nhật số lượng sản phẩm, hiển thị modal, validate phía client, gọi API và cải thiện trải nghiệm người dùng.

JavaScript giúp giảm số lần tải lại toàn bộ trang và làm cho giao diện phản hồi nhanh hơn. Tuy nhiên, các logic quan trọng như kiểm tra dữ liệu, tính tiền, xác thực quyền và tạo đơn hàng vẫn cần được xử lý ở phía server để bảo đảm an toàn và nhất quán.

Trong dự án hiện tại, thư mục `resources/js` có các file như `app.js`, `bootstrap.js`, các store và component frontend. Đây là cơ sở để xác định JavaScript là một phần trong công nghệ triển khai giao diện.

### 1.10.4. Vite

`Vite` là công cụ build frontend hiện đại, hỗ trợ phát triển nhanh với dev server và build tối ưu cho production. Trong Laravel, `laravel-vite-plugin` giúp tích hợp Vite với hệ thống asset của framework.

Dự án hiện tại có script `dev` và `build` trong `package.json`, lần lượt chạy `vite` và `vite build`. Điều này cho thấy Vite được dùng để quản lý quá trình biên dịch tài nguyên frontend như JavaScript, CSS, Sass hoặc các tài nguyên liên quan.

Việc sử dụng Vite giúp quy trình phát triển frontend thuận tiện hơn, đặc biệt khi hệ thống có nhiều file CSS, JavaScript và component cần được đóng gói, tối ưu hóa trước khi triển khai.

## 1.11. Kiểm tra chất lượng phần mềm trong Laravel

### 1.11.1. Khái niệm kiểm tra chất lượng phần mềm

Kiểm tra chất lượng phần mềm là quá trình kiểm tra và đánh giá hệ thống nhằm phát hiện lỗi, xác minh chức năng hoạt động đúng yêu cầu và giảm rủi ro khi thay đổi mã nguồn. Đối với website bán hàng, kiểm tra chất lượng có vai trò quan trọng vì hệ thống liên quan trực tiếp đến dữ liệu sản phẩm, khách hàng, đơn hàng, thanh toán và quyền truy cập.

Một lỗi trong luồng giỏ hàng hoặc checkout có thể ảnh hưởng đến doanh thu và trải nghiệm khách hàng. Một lỗi trong khu vực admin có thể làm sai dữ liệu sản phẩm hoặc đơn hàng. Vì vậy, kiểm tra chất lượng không chỉ là bước phụ trợ mà là một phần quan trọng của quy trình phát triển.

### 1.11.2. Các mức kiểm tra chất lượng phổ biến

Trong phát triển ứng dụng web, có một số mức kiểm tra chất lượng thường gặp:

- Integration test: kiểm tra sự phối hợp giữa nhiều thành phần như controller, service, database và external dependency.
- Rà soát hồi quy: kiểm tra để bảo đảm lỗi đã sửa không tái xuất hiện khi hệ thống thay đổi.


### 1.11.3. Kiểm tra chất lượng trong Laravel


Các test trong dự án hiện tại có tên liên quan trực tiếp đến nghiệp vụ như `CartFlowTest`, `CheckoutFlowTest`, `CustomerRegistrationTest`, `ProductDetailPageTest`, `HomepageTest`, `AdminAuthFlowTest` và nhiều test khác. Điều này cho thấy hệ thống có định hướng kiểm tra chất lượng các luồng người dùng quan trọng.

### 1.11.4. Vai trò của kiểm tra chất lượng trong bảo trì

Khi hệ thống phát triển, việc thay đổi một phần có thể ảnh hưởng đến nhiều phần khác. Ví dụ, thay đổi route giỏ hàng có thể ảnh hưởng đến header cart badge, trang giỏ hàng và checkout. Thay đổi middleware admin có thể ảnh hưởng đến quyền truy cập của nhiều nhóm quản trị viên.

Kiểm tra chất lượng giúp phát hiện sớm các lỗi hồi quy, tạo sự tự tin khi refactor và hỗ trợ bảo trì lâu dài. Đối với đồ án lập trình, việc có test cũng giúp chứng minh hệ thống không chỉ được xây dựng về mặt giao diện mà còn được kiểm tra ở các luồng nghiệp vụ chính.

## 1.12. Bảo mật cơ bản cho website bán hàng

### 1.12.1. Đặc thù bảo mật của website bán hàng

Website bán hàng xử lý nhiều loại dữ liệu nhạy cảm như thông tin tài khoản, địa chỉ giao hàng, số điện thoại, lịch sử đơn hàng, mã giảm giá và thông tin quản trị. Vì vậy, bảo mật là yêu cầu nền tảng, không phải chức năng bổ sung.

Mục tiêu của bảo mật trong website bán hàng là bảo vệ dữ liệu người dùng, bảo vệ tài nguyên quản trị, ngăn truy cập trái phép, bảo đảm tính toàn vẹn của đơn hàng và giảm nguy cơ bị khai thác thông qua các lỗ hổng phổ biến của ứng dụng web.

### 1.12.2. Kiểm soát dữ liệu đầu vào

Một nguyên tắc quan trọng trong bảo mật ứng dụng web là không tin tưởng dữ liệu người dùng gửi lên. Tất cả dữ liệu đầu vào từ form, query string, AJAX request hoặc API request cần được kiểm tra về kiểu dữ liệu, định dạng, độ dài, phạm vi giá trị và tính hợp lệ nghiệp vụ.

Trong Laravel, request validation và Form Request class giúp tách logic kiểm tra dữ liệu khỏi controller. Dự án hiện tại có nhiều lớp request trong `app/Http/Requests`, ví dụ các request liên quan đến cart, checkout, product, admin và API. Đây là cơ sở để kiểm soát đầu vào nhất quán hơn.

### 1.12.3. CSRF, XSS và SQL Injection

CSRF, viết tắt của Cross-Site Request Forgery, là kiểu tấn công lừa trình duyệt người dùng đã đăng nhập gửi request không mong muốn đến hệ thống. Laravel hỗ trợ CSRF protection cho route web thông qua token trong form. Với các thao tác thay đổi dữ liệu như thêm giỏ hàng, cập nhật thông tin, tạo đơn hàng hoặc xóa dữ liệu admin, bảo vệ CSRF là yêu cầu quan trọng.

XSS, viết tắt của Cross-Site Scripting, là kiểu tấn công chèn mã độc vào nội dung hiển thị trên trình duyệt. Blade mặc định escape dữ liệu khi sử dụng cú pháp hiển thị thông thường, giúp giảm nguy cơ XSS. Tuy nhiên, lập trình viên vẫn cần cẩn trọng khi hiển thị HTML động, nội dung từ editor hoặc dữ liệu do người dùng nhập.

SQL Injection là kiểu tấn công chèn lệnh SQL độc hại vào truy vấn cơ sở dữ liệu. Eloquent và query builder của Laravel hỗ trợ binding tham số, giúp giảm nguy cơ SQL Injection nếu được sử dụng đúng cách. Tuy nhiên, khi viết raw SQL, cần tránh nối chuỗi trực tiếp từ dữ liệu người dùng.

Dự án hiện tại cũng có các middleware bảo mật như `XSSProtectionMiddleware`, `SQLInjectionProtectionMiddleware` và `SecurityHeadersMiddleware` trong `app/Http/Middleware`, cho thấy bảo mật được xem xét ở mức ứng dụng.

### 1.12.4. Authentication, authorization và bảo vệ khu vực admin

Khu vực admin của website bán hàng thường có quyền thay đổi dữ liệu quan trọng như sản phẩm, đơn hàng, mã giảm giá, nội dung và tài khoản người dùng. Vì vậy, admin cần được bảo vệ bằng authentication và authorization.

Authentication bảo đảm chỉ người dùng đã đăng nhập mới có thể truy cập. Authorization bảo đảm người dùng đã đăng nhập chỉ được thực hiện các thao tác thuộc phạm vi quyền của họ. Trong dự án hiện tại, route admin được đặt trong group `auth:web`, `admin.access` và các group `admin.permission`, phù hợp với nguyên tắc kiểm soát truy cập theo vai trò và quyền.

### 1.12.5. Bảo mật mật khẩu và session

Mật khẩu người dùng không nên lưu dưới dạng văn bản thuần. Laravel hỗ trợ hash mật khẩu bằng các thuật toán an toàn thông qua hệ thống hashing. Ngoài ra, session cần được cấu hình phù hợp để giảm nguy cơ chiếm đoạt phiên đăng nhập, đặc biệt với tài khoản quản trị.

Các chức năng như đăng nhập, quên mật khẩu, reset mật khẩu, đăng xuất và remember token cần được thiết kế cẩn thận. Trong website bán hàng, mất an toàn ở các luồng này có thể dẫn tới truy cập trái phép tài khoản khách hàng hoặc khu vực admin.

### 1.12.6. Bảo mật trong cart và checkout

Cart và checkout là hai khu vực cần kiểm soát kỹ vì liên quan trực tiếp đến giao dịch. Hệ thống không nên tin tưởng giá tiền hoặc tổng tiền gửi từ client. Giá sản phẩm, phí vận chuyển, mã giảm giá và tổng thanh toán cần được tính lại ở server dựa trên dữ liệu đáng tin cậy trong cơ sở dữ liệu.

Trong checkout, hệ thống cần kiểm tra giỏ hàng hợp lệ, thông tin giao hàng hợp lệ, mã giảm giá còn hiệu lực, phương thức thanh toán phù hợp và trạng thái đơn hàng được tạo nhất quán. Nếu có nhiều thao tác ghi dữ liệu, transaction nên được sử dụng để tránh lỗi nửa chừng.

## 1.13. Tiêu chuẩn và quy trình phát triển phần mềm áp dụng

### 1.13.1. Tổ chức mã nguồn theo framework

Một tiêu chuẩn quan trọng khi phát triển bằng Laravel là tuân thủ cấu trúc dự án của framework. Controller đặt trong `app/Http/Controllers`, model đặt trong `app/Models` hoặc các lớp persistence tương ứng, view đặt trong `resources/views`, route đặt trong `routes`, migration đặt trong `database/migrations`, test đặt trong `tests`.

Việc tuân thủ cấu trúc này giúp người phát triển mới dễ tiếp cận dự án, đồng thời giúp hệ thống tận dụng tốt các quy ước tự động của Laravel như autoloading, route loading, view resolution và test discovery.

### 1.13.2. Quản lý dependency

Dự án Laravel sử dụng `Composer` để quản lý dependency PHP thông qua `composer.json` và sử dụng `npm` để quản lý dependency frontend thông qua `package.json`. Đây là quy trình tiêu chuẩn trong các ứng dụng Laravel hiện đại.

Các dependency backend chính của dự án gồm `laravel/framework`, `laravel/sanctum`, `laravel/socialite`, `laravel/ui`, `spatie/laravel-permission`, `barryvdh/laravel-dompdf` và `darkaonline/l5-swagger`. Các dependency frontend và build tool gồm `vite`, `laravel-vite-plugin`, `bootstrap`, `tailwindcss`, `sass`, `axios`, `vue`, `pinia` và `vue-router`.

Khi trình bày trong báo cáo, cần phân biệt rõ công nghệ chính với thư viện hỗ trợ, tránh mô tả các công nghệ không tồn tại trong dự án.

### 1.13.3. Quy trình phát triển và kiểm tra

Quy trình phát triển một chức năng trong website bán hàng thường gồm các bước: xác định yêu cầu, khảo sát chức năng tương tự, thiết kế route và dữ liệu, xây dựng controller hoặc service, tạo view, kiểm tra validation, kiểm tra phân quyền, sau đó kiểm tra thủ công các luồng quan trọng.


### 1.13.4. Nguyên tắc không mô tả sai công nghệ

Một yêu cầu quan trọng của báo cáo kỹ thuật là mô tả đúng hiện trạng công nghệ. Nếu dự án sử dụng `Blade`, không nên mô tả toàn bộ hệ thống như một ứng dụng SPA. Nếu dự án dùng `MySQL`, không nên trình bày như đang sử dụng hệ quản trị khác. Nếu API dùng `Laravel Sanctum`, không nên tự giả định hệ thống dùng OAuth server đầy đủ nếu không có cơ sở trong mã nguồn.

Nguyên tắc này giúp báo cáo có tính trung thực, phản ánh đúng sản phẩm đã xây dựng và tránh tạo khoảng cách giữa tài liệu với hệ thống thực tế.

## 1.14. REST API và trao đổi dữ liệu JSON

### 1.14.1. Khái niệm API trong ứng dụng web

API, viết tắt của Application Programming Interface, là giao diện cho phép các thành phần phần mềm trao đổi dữ liệu và gọi chức năng của nhau theo một quy ước nhất định. Trong ứng dụng web hiện đại, API thường được xây dựng trên giao thức HTTP và trả về dữ liệu ở định dạng JSON để các client khác nhau có thể sử dụng.

Đối với website bán hàng, API có thể phục vụ nhiều mục đích. API có thể cung cấp danh sách sản phẩm cho frontend, hỗ trợ ứng dụng di động, cho phép các thành phần JavaScript lấy dữ liệu bất đồng bộ, phục vụ hệ thống quản trị hoặc làm nền tảng tích hợp với dịch vụ bên ngoài. Việc có API giúp hệ thống linh hoạt hơn vì dữ liệu và nghiệp vụ không chỉ bị giới hạn trong các trang HTML render sẵn.

Trong dự án hiện tại, `routes/api.php` tổ chức các endpoint dưới prefix `v1`, bao gồm các nhóm sản phẩm, danh mục, thương hiệu, authentication, cart, order, review, coupon, blog, settings và admin API. Đây là cơ sở để trình bày API như một phần lý thuyết phù hợp với hệ thống, nhưng vẫn cần lưu ý rằng giao diện chính của website vẫn sử dụng `Blade` chứ không phải chỉ có API.

### 1.14.2. RESTful API

REST, viết tắt của Representational State Transfer, là phong cách thiết kế API dựa trên tài nguyên. Trong RESTful API, mỗi tài nguyên được biểu diễn bằng một URL, còn thao tác với tài nguyên được biểu diễn thông qua HTTP method.

Các HTTP method thường dùng gồm:

- `GET`: lấy dữ liệu từ server.
- `POST`: tạo mới dữ liệu hoặc gửi một yêu cầu xử lý.
- `PUT`: cập nhật toàn bộ tài nguyên.
- `PATCH`: cập nhật một phần tài nguyên.
- `DELETE`: xóa tài nguyên.

Đối với website bán hàng, tài nguyên có thể là sản phẩm, danh mục, thương hiệu, giỏ hàng, đơn hàng, đánh giá hoặc mã giảm giá. Thiết kế API theo REST giúp endpoint dễ hiểu hơn, thuận tiện cho kiểm tra chất lượng, tài liệu hóa và mở rộng.

### 1.14.3. JSON và mã trạng thái HTTP

JSON, viết tắt của JavaScript Object Notation, là định dạng dữ liệu nhẹ, dễ đọc và dễ xử lý trong cả backend lẫn frontend. API của website bán hàng thường trả về JSON vì định dạng này phù hợp với JavaScript, ứng dụng di động và các hệ thống tích hợp.

Bên cạnh dữ liệu trả về, mã trạng thái HTTP cũng có vai trò quan trọng. Một response tốt không chỉ có nội dung đúng mà còn cần trạng thái phù hợp. Ví dụ, `200 OK` cho biết xử lý thành công, `201 Created` cho biết tạo mới thành công, `401 Unauthorized` cho biết người dùng chưa xác thực, `403 Forbidden` cho biết không đủ quyền, `404 Not Found` cho biết không tìm thấy tài nguyên và `422 Unprocessable Entity` thường dùng cho lỗi validation.

Việc sử dụng JSON và mã trạng thái HTTP đúng giúp client xử lý kết quả rõ ràng hơn. Ví dụ, nếu mã giảm giá không hợp lệ, API nên trả về thông báo lỗi và trạng thái phù hợp để frontend hiển thị cho người dùng. Nếu người dùng chưa đăng nhập nhưng gọi API lịch sử đơn hàng, hệ thống cần trả về trạng thái xác thực thay vì trả dữ liệu rỗng gây hiểu nhầm.

### 1.14.4. Versioning trong API

Versioning là kỹ thuật đánh dấu phiên bản API để hệ thống có thể phát triển mà không làm hỏng các client cũ. Khi API đã được sử dụng bởi frontend, ứng dụng di động hoặc bên thứ ba, việc thay đổi cấu trúc response đột ngột có thể gây lỗi ở phía client.

Một cách phổ biến là đặt version trong URL, ví dụ `v1`. Khi hệ thống cần thay đổi lớn, có thể tạo `v2` và duy trì `v1` trong một thời gian chuyển tiếp. Dự án hiện tại sử dụng prefix `v1` trong `routes/api.php`, phù hợp với nguyên tắc versioning cơ bản.

Về mặt lý thuyết, versioning giúp API có vòng đời rõ ràng hơn. Nó cũng hỗ trợ quá trình bảo trì vì lập trình viên có thể biết endpoint nào thuộc phiên bản nào, client nào đang sử dụng phiên bản nào và thay đổi nào có nguy cơ gây breaking change.

## 1.15. Request validation, migration và quản lý dữ liệu

### 1.15.1. Request validation

Request validation là quá trình kiểm tra dữ liệu đầu vào trước khi dữ liệu được sử dụng cho xử lý nghiệp vụ hoặc ghi vào cơ sở dữ liệu. Trong website bán hàng, dữ liệu đầu vào xuất hiện ở nhiều nơi như form đăng ký, đăng nhập, tìm kiếm sản phẩm, thêm giỏ hàng, nhập thông tin giao hàng, áp dụng mã giảm giá, đánh giá sản phẩm và quản trị nội dung.

Nếu không validation, hệ thống dễ gặp lỗi dữ liệu, lỗi bảo mật hoặc trạng thái nghiệp vụ không hợp lệ. Ví dụ, số lượng sản phẩm trong giỏ hàng không thể là số âm; email khách hàng cần đúng định dạng; mật khẩu cần đạt độ dài tối thiểu; mã giảm giá cần tồn tại và còn hiệu lực; quyền admin cần được kiểm tra trước khi cho phép cập nhật dữ liệu.

Laravel hỗ trợ validation trực tiếp trong controller và thông qua Form Request class. Form Request giúp gom các quy tắc kiểm tra vào lớp riêng, làm controller gọn hơn và giúp quy tắc validation có thể tái sử dụng hoặc kiểm tra chất lượng dễ hơn. Dự án hiện tại có nhiều lớp trong `app/Http/Requests`, thể hiện cách tiếp cận tách validation khỏi controller ở nhiều luồng nghiệp vụ.

### 1.15.2. Migration

Migration là cơ chế quản lý thay đổi cấu trúc cơ sở dữ liệu bằng mã nguồn. Thay vì chỉnh sửa database thủ công và khó theo dõi, Laravel cho phép định nghĩa bảng, cột, chỉ mục và quan hệ trong các file migration đặt tại `database/migrations`.

Đối với đồ án lập trình, migration có ý nghĩa quan trọng vì nó giúp mô tả lịch sử phát triển schema của hệ thống. Khi người khác cài đặt dự án, họ có thể chạy migration để tạo cấu trúc dữ liệu tương ứng. Khi cần thêm bảng mới hoặc cột mới, lập trình viên tạo migration mới thay vì sửa trực tiếp migration cũ nếu dự án đã có dữ liệu hoặc đã được chia sẻ.

Trong website bán hàng, migration thường dùng để định nghĩa các bảng như sản phẩm, danh mục, thương hiệu, khách hàng, giỏ hàng, đơn hàng, chi tiết đơn hàng, thanh toán, mã giảm giá, đánh giá và các bảng hỗ trợ vận hành. Việc quản lý schema bằng migration giúp hệ thống minh bạch hơn và thuận lợi cho quá trình triển khai nhiều môi trường.

### 1.15.3. Seeder và factory

Seeder là công cụ dùng để tạo dữ liệu mẫu hoặc dữ liệu mặc định cho hệ thống. Factory là công cụ hỗ trợ tạo dữ liệu giả phục vụ kiểm tra chất lượng. Trong Laravel, seeder và factory thường được sử dụng cùng nhau để chuẩn bị dữ liệu ban đầu hoặc dữ liệu test.

Đối với website bán hàng, dữ liệu mẫu có thể bao gồm danh mục, thương hiệu, sản phẩm, khách hàng, mã giảm giá và tài khoản quản trị. Khi phát triển hoặc kiểm tra chất lượng, dữ liệu mẫu giúp lập trình viên kiểm tra giao diện và luồng nghiệp vụ mà không phải nhập tay quá nhiều.

Factory đặc biệt hữu ích trong kiểm tra chất lượng. Ví dụ, test checkout có thể cần tạo khách hàng, sản phẩm, giỏ hàng và mã giảm giá. Nếu dùng factory, test có thể tạo dữ liệu cần thiết một cách lặp lại và độc lập hơn. Dự án hiện tại có `database/factories` và `database/seeders`, phù hợp với quy trình phát triển Laravel.

### 1.15.4. Quản lý dữ liệu legacy và dữ liệu mới

Trong nhiều dự án thực tế, hệ thống không phải lúc nào cũng được xây dựng hoàn toàn mới. Có trường hợp dự án kế thừa dữ liệu, bảng cũ hoặc quy ước đặt tên cũ. Khi đó, hệ thống phải cân bằng giữa việc giữ tương thích với dữ liệu legacy và chuẩn hóa dần theo cấu trúc mới.

Về mặt lý thuyết, quản lý dữ liệu legacy cần chú ý ba điểm. Thứ nhất, không làm mất dữ liệu đang có. Thứ hai, các thay đổi schema cần có lộ trình rõ ràng. Thứ ba, mã nguồn mới cần được tổ chức để giảm phụ thuộc vào những quy ước cũ nếu chúng gây khó bảo trì.

Trong dự án hiện tại có các dấu hiệu của cả bảng legacy và migration mới, đồng thời có các tài liệu và service liên quan đến migration dữ liệu. Khi viết báo cáo, phần này nên được hiểu là bối cảnh kỹ thuật hỗ trợ phát triển hệ thống, không nên biến Chương 1 thành phần mô tả chi tiết từng bảng vì nội dung đó phù hợp hơn với chương phân tích và thiết kế hệ thống.

## 1.16. Hiệu năng, cache và trải nghiệm người dùng

### 1.16.1. Hiệu năng trong website bán hàng

Hiệu năng là một yếu tố quan trọng của website thương mại điện tử. Người dùng thường rời bỏ trang nếu website tải chậm, phản hồi không ổn định hoặc thao tác giỏ hàng mất quá nhiều thời gian. Đối với doanh nghiệp, hiệu năng ảnh hưởng đến tỷ lệ chuyển đổi, trải nghiệm mua hàng và khả năng vận hành khi lượng truy cập tăng.

Các yếu tố ảnh hưởng đến hiệu năng gồm thời gian truy vấn cơ sở dữ liệu, số lượng request, kích thước ảnh, kích thước CSS và JavaScript, cấu hình cache, chất lượng server, cách tổ chức index trong database và mức độ tối ưu của mã nguồn. Trong Laravel, hiệu năng cũng liên quan đến việc sử dụng eager loading, cache configuration, route cache, config cache và tối ưu asset frontend.

Đối với website bán hàng, các trang cần chú ý hiệu năng gồm trang chủ, danh sách sản phẩm, trang chi tiết sản phẩm, tìm kiếm, giỏ hàng và checkout. Đây là các điểm tiếp xúc chính trong hành trình mua hàng, nên thời gian phản hồi cần được giữ ở mức hợp lý.

### 1.16.2. Cache trong ứng dụng web

Cache là kỹ thuật lưu trữ tạm thời dữ liệu đã tính toán hoặc đã truy vấn để giảm chi phí xử lý trong các lần truy cập tiếp theo. Cache có thể áp dụng ở nhiều tầng như browser cache, application cache, database query cache, route cache, config cache hoặc view cache.

Trong website bán hàng, cache có thể được sử dụng cho các dữ liệu ít thay đổi như danh mục, thương hiệu, cấu hình website, menu, banner hoặc danh sách sản phẩm nổi bật. Tuy nhiên, cache cần được thiết kế cẩn thận để tránh hiển thị dữ liệu lỗi thời, đặc biệt với giá sản phẩm, trạng thái khuyến mãi và tồn kho nếu hệ thống có quản lý tồn kho.

Laravel hỗ trợ hệ thống cache thống nhất qua facade và cấu hình trong `config/cache.php`. Dự án hiện tại có cấu hình `CACHE_STORE=database` trong `.env.example`, cho thấy cache có thể được lưu ở database trong môi trường mặc định. Dù cache giúp tăng hiệu năng, lập trình viên cần có chiến lược xóa hoặc cập nhật cache khi dữ liệu nguồn thay đổi.

### 1.16.3. Tối ưu truy vấn cơ sở dữ liệu

Trong website bán hàng, dữ liệu sản phẩm và đơn hàng có thể tăng nhanh theo thời gian. Nếu truy vấn không được tối ưu, hệ thống có thể chậm khi hiển thị danh sách sản phẩm, tìm kiếm, lọc danh mục hoặc thống kê đơn hàng.

Một số nguyên tắc tối ưu truy vấn gồm sử dụng index cho cột thường lọc hoặc sắp xếp, tránh lấy quá nhiều cột không cần thiết, phân trang danh sách dài, sử dụng eager loading để giảm N+1 query, giới hạn dữ liệu trả về cho API và cân nhắc cache cho dữ liệu ít thay đổi.

Đối với Eloquent, lập trình viên cần hiểu rằng mỗi quan hệ có thể phát sinh truy vấn. Nếu hiển thị danh sách sản phẩm kèm thương hiệu, danh mục, ảnh và giá, cần thiết kế truy vấn hợp lý để tránh số lượng query tăng theo số lượng sản phẩm. Đây là lý do kiến thức về SQL và cơ sở dữ liệu vẫn cần thiết ngay cả khi dùng ORM.

### 1.16.4. Trải nghiệm người dùng trong thương mại điện tử

Trải nghiệm người dùng là tập hợp cảm nhận của người dùng khi tương tác với website. Trong thương mại điện tử, trải nghiệm tốt giúp người dùng tìm được sản phẩm nhanh hơn, tin tưởng thông tin hơn và hoàn tất mua hàng thuận lợi hơn.

Một số nguyên tắc UX quan trọng gồm điều hướng rõ ràng, tìm kiếm dễ thấy, thông tin sản phẩm đầy đủ, nút hành động nổi bật, giỏ hàng dễ kiểm tra, checkout ít gây nhầm lẫn và thông báo lỗi dễ hiểu. Với sản phẩm công nghệ, người dùng thường quan tâm nhiều đến thông số, hình ảnh, đánh giá, thương hiệu và so sánh, vì vậy giao diện cần hỗ trợ quá trình ra quyết định.

Từ góc độ kỹ thuật, UX không chỉ nằm ở giao diện đẹp mà còn liên quan đến tốc độ tải trang, tính ổn định của form, khả năng hoạt động trên thiết bị di động, cách hiển thị lỗi validation và phản hồi sau mỗi thao tác. Một website bán hàng tốt cần kết hợp giữa thiết kế giao diện, xử lý backend và dữ liệu chính xác.

## 1.17. SEO, nội dung và khả năng tiếp cận

### 1.17.1. SEO trong website bán hàng

SEO, viết tắt của Search Engine Optimization, là quá trình tối ưu website để cải thiện khả năng xuất hiện trên công cụ tìm kiếm. Đối với website bán hàng, SEO có vai trò quan trọng vì người dùng thường tìm kiếm sản phẩm, thương hiệu hoặc thông tin tư vấn qua công cụ tìm kiếm trước khi mua.

Các yếu tố SEO cơ bản gồm tiêu đề trang, mô tả meta, URL thân thiện, heading rõ ràng, nội dung có giá trị, tốc độ tải trang, cấu trúc liên kết nội bộ, hình ảnh có mô tả phù hợp và khả năng hiển thị tốt trên thiết bị di động. Với website bán hàng, trang danh mục, trang chi tiết sản phẩm và bài viết tư vấn thường là các trang có giá trị SEO cao.

Trong Laravel, SEO không phải là một tính năng riêng biệt của framework mà là kết quả của cách tổ chức route, view, nội dung, metadata và hiệu năng. Khi dùng `Blade`, hệ thống có thể render sẵn HTML cho các trang sản phẩm và danh mục, điều này thuận lợi cho công cụ tìm kiếm đọc nội dung.

### 1.17.2. URL thân thiện và slug

URL thân thiện là URL dễ đọc, có ý nghĩa với người dùng và công cụ tìm kiếm. Thay vì dùng URL dạng tham số khó hiểu, website bán hàng thường sử dụng slug cho danh mục, thương hiệu hoặc sản phẩm. Ví dụ, một trang chi tiết sản phẩm có thể dùng slug để phản ánh tên sản phẩm trong đường dẫn.

Slug giúp người dùng nhận biết nội dung trang trước khi truy cập, đồng thời hỗ trợ SEO. Tuy nhiên, slug cần được tạo ổn định, tránh trùng lặp và cần xử lý khi tên sản phẩm thay đổi. Trong Laravel, route parameter có thể nhận slug và controller dùng slug để tìm dữ liệu tương ứng.

Về mặt lý thuyết, URL thân thiện cũng góp phần vào khả năng bảo trì. Khi route được tổ chức nhất quán, việc phân tích log truy cập, chia sẻ liên kết và xây dựng sitemap sẽ thuận lợi hơn.

### 1.17.3. Nội dung trong website thương mại điện tử

Nội dung là một phần quan trọng trong website bán hàng. Ngoài dữ liệu sản phẩm, hệ thống còn cần bài viết, banner, video, thông tin liên hệ, chính sách mua hàng, hướng dẫn thanh toán và các nội dung hỗ trợ khách hàng.

Đối với sản phẩm công nghệ, nội dung có vai trò đặc biệt lớn vì người dùng thường cần tìm hiểu thông số, so sánh cấu hình, đọc tư vấn và xem đánh giá trước khi quyết định mua. Nội dung tốt giúp tăng độ tin cậy của website, hỗ trợ SEO và giảm số lượng câu hỏi lặp lại từ khách hàng.

Trong dự án hiện tại, các route và view liên quan đến bài viết, video, contact, slider và các phần nội dung storefront cho thấy hệ thống không chỉ là giỏ hàng và đơn hàng, mà còn có lớp nội dung hỗ trợ bán hàng. Đây là cơ sở lý thuyết để nhìn nhận CMS như một phần của website thương mại điện tử.

### 1.17.4. Khả năng tiếp cận và responsive design

Khả năng tiếp cận là mức độ mà nhiều nhóm người dùng khác nhau có thể sử dụng website, bao gồm người dùng trên thiết bị khác nhau hoặc người dùng có hạn chế nhất định về thị giác, thao tác hoặc thiết bị. Responsive design là kỹ thuật giúp giao diện hiển thị phù hợp trên nhiều kích thước màn hình như desktop, tablet và điện thoại.

Website bán hàng cần responsive vì người dùng hiện nay thường truy cập bằng điện thoại. Các thao tác như tìm kiếm, xem sản phẩm, thêm giỏ hàng và checkout cần hoạt động tốt trên màn hình nhỏ. Nếu giao diện khó sử dụng trên mobile, tỷ lệ rời bỏ giỏ hàng có thể tăng.

Về mặt kỹ thuật, responsive design liên quan đến CSS layout, media query, kích thước ảnh, khoảng cách giữa các nút thao tác, độ lớn chữ và cách tổ chức nội dung. Các framework hoặc thư viện CSS như `Bootstrap` và `Tailwind CSS` có thể hỗ trợ xây dựng giao diện responsive, nhưng chất lượng cuối cùng vẫn phụ thuộc vào cách thiết kế và kiểm tra thực tế.

## 1.18. Tích hợp dịch vụ và các thư viện hỗ trợ

### 1.18.1. Social login

Social login là cơ chế cho phép người dùng đăng nhập bằng tài khoản từ các nền tảng bên ngoài như Google hoặc Facebook. Mục tiêu của social login là giảm rào cản đăng ký, giúp người dùng truy cập nhanh hơn và hạn chế việc phải ghi nhớ thêm một mật khẩu mới.

Trong Laravel, `laravel/socialite` là thư viện phổ biến để tích hợp đăng nhập qua nhà cung cấp bên thứ ba. Dự án hiện tại có dependency `laravel/socialite` và có các route liên quan đến đăng nhập khách hàng bằng Facebook hoặc Google trong `routes/web/frontend.php`. Vì vậy, social login là công nghệ có cơ sở trong dự án.

Về mặt bảo mật, social login cần được triển khai cẩn thận. Hệ thống phải xác định rõ thông tin nào được lấy từ nhà cung cấp bên ngoài, cách liên kết tài khoản xã hội với tài khoản khách hàng và cách xử lý khi email đã tồn tại. Ngoài ra, không nên phụ thuộc hoàn toàn vào dữ liệu phía client mà cần xác thực qua callback hợp lệ.

### 1.18.2. Phân quyền bằng role và permission

Trong khu vực admin, không phải quản trị viên nào cũng nên có toàn bộ quyền. Một người phụ trách sản phẩm có thể không cần quyền quản lý người dùng; người phụ trách đơn hàng có thể không cần quyền thay đổi cài đặt hệ thống. Vì vậy, phân quyền chi tiết giúp giảm rủi ro thao tác nhầm hoặc truy cập vượt phạm vi.

`spatie/laravel-permission` là thư viện hỗ trợ quản lý role và permission trong Laravel. Thư viện này cho phép gán vai trò cho user, gán quyền cho vai trò và kiểm tra quyền trong middleware hoặc code. Dự án hiện tại có dependency này và sử dụng các middleware dạng `admin.permission`, phù hợp với mô hình RBAC.

Về mặt lý thuyết, RBAC giúp hệ thống quản trị có cấu trúc rõ ràng hơn. Khi chức năng admin tăng lên, quyền truy cập có thể được nhóm theo module như quản lý sản phẩm, quản lý đơn hàng, quản lý nội dung, quản lý người dùng, quản lý mã giảm giá và quản lý hệ thống.

### 1.18.3. Tài liệu API và Swagger

Khi hệ thống có API, tài liệu API giúp lập trình viên hiểu endpoint, method, tham số, authentication, response và lỗi có thể xảy ra. Nếu không có tài liệu, việc tích hợp frontend, mobile app hoặc bên thứ ba sẽ khó khăn và dễ sai lệch.

Swagger hoặc OpenAPI là một chuẩn phổ biến để mô tả API theo cấu trúc máy có thể đọc được. Dự án hiện tại có dependency `darkaonline/l5-swagger` và có các file liên quan đến OpenAPI, vì vậy có thể xem tài liệu API là một phần công cụ hỗ trợ phát triển và bảo trì.

Trong báo cáo ĐATN, phần cơ sở lý thuyết chỉ cần trình bày vai trò của tài liệu API ở mức tổng quát. Chi tiết từng endpoint, tham số và response nên đặt ở chương thiết kế hoặc phụ lục kỹ thuật nếu cần.

### 1.18.4. Xuất tài liệu và báo cáo

Website bán hàng thường phát sinh nhu cầu xuất hóa đơn, phiếu giao hàng, đơn hàng hoặc báo cáo dưới dạng file. Trong Laravel, các thư viện như `barryvdh/laravel-dompdf` có thể hỗ trợ tạo PDF từ view hoặc HTML.

Dự án hiện tại có dependency `barryvdh/laravel-dompdf`, vì vậy có thể xác định nhu cầu tạo tài liệu PDF là một khả năng hỗ trợ của hệ thống. Tuy nhiên, Chương 1 chỉ nên trình bày ở mức lý thuyết về vai trò của xuất tài liệu trong hệ thống bán hàng, không nên mô tả chi tiết template hay code sinh PDF.

Về mặt nghiệp vụ, xuất tài liệu giúp admin in đơn hàng, lưu chứng từ, chia sẻ thông tin giao hàng hoặc tạo báo cáo phục vụ quản lý. Đây là một thành phần phụ trợ nhưng có giá trị thực tế trong vận hành thương mại điện tử.

## 1.19. Kết chương

Chương 1 đã trình bày các cơ sở lý thuyết nền tảng cho đề tài xây dựng website bán hàng bằng Laravel. Nội dung chương bao gồm khái niệm website thương mại điện tử, mô hình B2C, các thành phần nghiệp vụ của website bán hàng, mô hình MVC, framework `Laravel 11`, `Blade` template engine, cơ sở dữ liệu quan hệ và `MySQL`, `Eloquent ORM`, routing, controller, middleware, session, authentication, authorization, cart, checkout, frontend với `HTML`, `CSS`, `JavaScript`, `Vite`, kiểm tra chất lượng phần mềm trong Laravel và các nguyên tắc bảo mật cơ bản.

Các nội dung này tạo nền tảng để các chương tiếp theo đi vào phân tích yêu cầu, thiết kế hệ thống, mô tả cơ sở dữ liệu, trình bày quá trình triển khai chức năng và đánh giá kết quả. Việc bám sát công nghệ thực tế của dự án giúp chương cơ sở lý thuyết vừa đáp ứng yêu cầu học thuật của báo cáo đồ án tốt nghiệp, vừa giữ được tính nhất quán với sản phẩm phần mềm đã xây dựng.
