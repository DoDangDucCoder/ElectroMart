# CHƯƠNG 2. CƠ SỞ LÝ THUYẾT

## 2.1. Khái niệm về thương mại điện tử

Thương mại điện tử là hình thức tiến hành một phần hoặc toàn bộ hoạt động mua bán hàng hóa và dịch vụ thông qua các phương tiện điện tử, chủ yếu là mạng Internet. Trong môi trường này, quá trình giới thiệu sản phẩm, tiếp nhận nhu cầu, đặt hàng, thanh toán, chăm sóc khách hàng và quản lý dữ liệu đều có thể được số hóa ở nhiều mức độ khác nhau.

Khác với mô hình kinh doanh truyền thống, thương mại điện tử cho phép doanh nghiệp tiếp cận người dùng không bị giới hạn nhiều bởi không gian và thời gian. Người mua có thể tra cứu sản phẩm, so sánh giá, đọc đánh giá và thực hiện giao dịch ở bất kỳ thời điểm nào. Về phía doanh nghiệp, hệ thống thương mại điện tử giúp mở rộng kênh bán hàng, giảm chi phí vận hành thủ công, hỗ trợ phân tích hành vi người dùng và nâng cao khả năng tự động hóa.

Trong phạm vi đề tài này, hệ thống được định hướng theo mô hình website thương mại điện tử phục vụ hoạt động bán thiết bị công nghệ. Vì vậy, nền tảng lý thuyết không chỉ dừng ở khái niệm bán hàng trực tuyến nói chung mà còn cần gắn với đặc thù của nhóm sản phẩm công nghệ, nơi người dùng thường có nhu cầu tìm hiểu chi tiết, so sánh nhiều cấu hình và cân nhắc trước khi ra quyết định mua.

## 2.2. Các mô hình thương mại điện tử phổ biến

Trong thực tế, thương mại điện tử có thể được phân thành nhiều mô hình tùy theo đối tượng tham gia giao dịch.

### 2.2.1. Mô hình B2C

B2C (Business to Customer) là mô hình doanh nghiệp bán hàng trực tiếp cho khách hàng cuối. Đây là mô hình phổ biến nhất đối với các website bán lẻ trực tuyến. Người dùng truy cập hệ thống để xem sản phẩm, thêm vào giỏ hàng, đặt hàng, thanh toán và theo dõi lịch sử giao dịch.

Đề tài này phù hợp nhất với mô hình B2C vì hệ thống tập trung vào hai phân hệ chính là storefront cho khách hàng và admin cho quản trị viên. Toàn bộ luồng nghiệp vụ như tìm kiếm sản phẩm, áp dụng mã giảm giá, đặt hàng, thanh toán và quản lý đơn đều phản ánh đúng đặc trưng của mô hình B2C.

### 2.2.2. Mô hình B2B

B2B (Business to Business) là mô hình giao dịch giữa doanh nghiệp với doanh nghiệp. Các hệ thống B2B thường chú trọng hợp đồng, báo giá, quản lý đối tác, mức giá theo nhóm khách hàng và quy trình xét duyệt nội bộ. So với B2C, B2B thường có vòng đời giao dịch dài hơn và nhiều ràng buộc nghiệp vụ hơn.

Mặc dù đề tài không triển khai theo hướng B2B, việc nhận diện mô hình này vẫn có ý nghĩa lý thuyết vì nó cho thấy thương mại điện tử không chỉ là bán lẻ trực tiếp cho người tiêu dùng.

### 2.2.3. Mô hình C2C

C2C (Customer to Customer) là mô hình người dùng giao dịch với nhau thông qua một nền tảng trung gian, ví dụ các sàn giao dịch hoặc nền tảng rao bán. Mô hình này thường yêu cầu cơ chế trung gian thanh toán, kiểm duyệt nội dung và đánh giá mức độ uy tín giữa các người dùng.

Đề tài hiện tại không đi theo hướng C2C, nhưng sự khác biệt giữa C2C và B2C giúp làm rõ bản chất của hệ thống đang xây dựng: hệ thống có một chủ thể vận hành trung tâm quản lý catalog, giá bán, nội dung và đơn hàng.

## 2.3. Đặc trưng của website thương mại điện tử bán thiết bị công nghệ

Website thương mại điện tử bán thiết bị công nghệ có một số đặc điểm riêng so với các nhóm sản phẩm thông thường.

Thứ nhất, thông tin sản phẩm thường phức tạp hơn. Một sản phẩm công nghệ có thể đi kèm nhiều thông số kỹ thuật, thuộc tính, biến thể, phụ kiện và nội dung mô tả chuyên sâu. Vì vậy, hệ thống cần hỗ trợ cấu trúc dữ liệu đủ rõ để thể hiện danh mục, thương hiệu, biến thể, media, tài liệu và đánh giá.

Thứ hai, hành vi người dùng thường mang tính so sánh cao. Trước khi mua, người dùng có xu hướng xem nhiều sản phẩm, so sánh cấu hình, đọc đánh giá và tham khảo khuyến mãi. Do đó, các chức năng như search suggest, recommendation, compare, review và video giới thiệu có vai trò lớn hơn so với một website bán hàng đơn giản.

Thứ ba, khu vực quản trị thường không chỉ dừng ở CRUD cơ bản. Vì số lượng dữ liệu lớn và thay đổi thường xuyên, hệ thống quản trị cần hỗ trợ tổ chức nội dung, điều phối đơn hàng, phân quyền, báo cáo và các công cụ hỗ trợ vận hành.

Chính các đặc điểm này là cơ sở lý thuyết giải thích vì sao đề tài cần nhiều hơn một mô hình bán hàng tối thiểu, và vì sao kiến trúc hệ thống phải được tổ chức đủ linh hoạt để hỗ trợ mở rộng.

## 2.4. Kiến trúc ứng dụng web

### 2.4.1. Mô hình client-server

Phần lớn các ứng dụng web hiện đại, trong đó có website thương mại điện tử, được xây dựng trên mô hình client-server. Ở mô hình này, phía client là trình duyệt web hoặc ứng dụng tiêu thụ dữ liệu, còn phía server là nơi tiếp nhận request, xử lý nghiệp vụ, truy vấn dữ liệu và trả về response.

Đối với hệ thống thương mại điện tử, client có thể là trình duyệt truy cập storefront hoặc admin, trong khi server đảm nhiệm routing, authentication, business logic, database access và trả về HTML hoặc JSON. Mô hình client-server giúp tách biệt phần hiển thị khỏi phần xử lý trung tâm, từ đó tăng tính mở rộng và khả năng bảo trì.

### 2.4.2. Mô hình MVC

MVC (Model - View - Controller) là mô hình kiến trúc phổ biến trong phát triển ứng dụng web. Trong đó:

- Model đại diện cho dữ liệu và các thao tác liên quan đến dữ liệu
- View chịu trách nhiệm hiển thị giao diện cho người dùng
- Controller tiếp nhận request, điều phối xử lý và trả kết quả

Laravel là framework được xây dựng mạnh trên nền tư duy MVC. Nhờ đó, việc tổ chức mã nguồn trở nên rõ ràng hơn so với cách viết logic lẫn trực tiếp trong giao diện. Tuy nhiên, đối với các hệ thống lớn hơn mức CRUD cơ bản, MVC thuần túy thường chưa đủ để kiểm soát tốt các nghiệp vụ phức tạp.

### 2.4.3. Kiến trúc phân lớp mở rộng từ MVC

Khi hệ thống phát triển lớn dần, chỉ dùng MVC cơ bản có thể khiến controller trở nên quá tải. Vì vậy, nhiều dự án hiện đại thường mở rộng thêm các lớp như:

- request validation để kiểm tra dữ liệu đầu vào
- service layer để xử lý nghiệp vụ
- support classes để gom các quy tắc dùng chung
- repository hoặc persistence layer để chuẩn hóa truy cập dữ liệu
- domain objects để biểu diễn các khái niệm nghiệp vụ ở mức rõ hơn

Cách tiếp cận này thường được gọi là layered architecture hoặc kiến trúc phân tầng. Trong phạm vi đề tài, đây là nền tảng lý thuyết quan trọng để lý giải việc hệ thống không chỉ dừng ở controller và view, mà còn có thêm `Application`, `Domain`, `Infrastructure/Persistence` và `Support`.

## 2.5. Framework Laravel 11

### 2.5.1. Tổng quan về Laravel

Laravel là một framework phát triển web mã nguồn mở sử dụng ngôn ngữ PHP, được thiết kế theo hướng hiện đại, dễ mở rộng và có hệ sinh thái phong phú. Laravel cung cấp sẵn nhiều thành phần quan trọng như routing, middleware, Eloquent ORM, Blade template, request validation, authentication, authorization, event và queue.

Việc sử dụng Laravel giúp giảm thời gian xây dựng các thành phần nền tảng, từ đó người phát triển có thể tập trung nhiều hơn vào nghiệp vụ đặc thù của ứng dụng.

### 2.5.2. Lý do lựa chọn Laravel 11

Laravel 11 là phiên bản hiện đại, ổn định và phù hợp với các dự án cần phát triển nhanh nhưng vẫn bảo đảm tổ chức mã nguồn tốt. Đối với đề tài này, Laravel 11 có một số ưu điểm quan trọng:

- hỗ trợ routing rõ ràng cho web và API
- có middleware để kiểm soát xác thực và phân quyền
- có request validation giúp tách logic kiểm tra đầu vào
- có Eloquent ORM hỗ trợ làm việc với cơ sở dữ liệu quan hệ
- có Blade template phù hợp với mô hình website server-rendered

Những ưu điểm này rất phù hợp với yêu cầu của một hệ thống thương mại điện tử có cả storefront, admin và REST API.

### 2.5.3. Vòng đời xử lý request trong Laravel

Khi người dùng gửi request đến hệ thống, Laravel tiếp nhận request thông qua entry point của ứng dụng. Sau đó, request đi qua kernel, các middleware, route matching và controller. Tại controller, hệ thống có thể tiếp tục gọi request validation, service, repository hoặc model để xử lý dữ liệu trước khi trả về response.

Hiểu được vòng đời request là cơ sở lý thuyết quan trọng giúp phân tích đúng luồng xử lý của các chức năng như thêm giỏ hàng, checkout, login, admin CRUD hoặc API response.

## 2.6. Blade Template và tổ chức giao diện

Blade là template engine đi kèm Laravel, cho phép xây dựng giao diện server-rendered một cách linh hoạt. Blade hỗ trợ layout inheritance, include partials, conditionals, loops và binding dữ liệu từ controller sang view.

Trong các hệ thống thương mại điện tử, Blade có ưu điểm là:

- dễ tổ chức giao diện theo layout
- dễ tái sử dụng các thành phần như header, footer, sidebar, modal
- thuận tiện cho các website ưu tiên server rendering
- dễ kết hợp với JavaScript và AJAX ở những vùng tương tác cục bộ

Về mặt lý thuyết, tổ chức view theo partials là một kỹ thuật giúp tăng khả năng tái sử dụng và giảm lặp mã giao diện. Đối với đề tài, đây là nền tảng phù hợp để giải thích cách xây dựng storefront và admin shell theo nhiều thành phần dùng chung.

## 2.7. Middleware và Request Validation

### 2.7.1. Middleware

Middleware là lớp trung gian xử lý request trước khi request tới controller hoặc trước khi response được trả về client. Trong một hệ thống web, middleware thường được dùng để:

- xác thực người dùng
- kiểm tra quyền truy cập
- giới hạn khu vực truy cập theo vai trò
- ghi log hoặc kiểm soát các điều kiện tiền xử lý

Đối với hệ thống thương mại điện tử, middleware đặc biệt quan trọng ở các khu vực như admin, checkout, user profile và API protected routes.

### 2.7.2. Request Validation

Request validation là kỹ thuật tách riêng logic kiểm tra dữ liệu đầu vào ra khỏi controller. Thay vì để controller tự viết nhiều điều kiện kiểm tra, hệ thống có thể định nghĩa một lớp request riêng cho từng form hoặc từng luồng nghiệp vụ.

Lợi ích của request validation bao gồm:

- làm controller gọn hơn
- tăng khả năng tái sử dụng quy tắc kiểm tra
- dễ kiểm tra chất lượng hơn
- giảm lỗi do kiểm tra đầu vào không nhất quán

Trong lý thuyết thiết kế hệ thống web, đây là một bước quan trọng để chuyển từ ứng dụng CRUD đơn giản sang ứng dụng có cấu trúc tốt hơn.

## 2.8. Cơ sở dữ liệu quan hệ và MySQL

### 2.8.1. Khái niệm về cơ sở dữ liệu quan hệ

Cơ sở dữ liệu quan hệ là mô hình tổ chức dữ liệu dưới dạng các bảng có mối liên hệ với nhau thông qua khóa chính và khóa ngoại. Mỗi bảng đại diện cho một nhóm thực thể hoặc một nhóm thông tin có ý nghĩa nghiệp vụ riêng.

Trong hệ thống thương mại điện tử, cơ sở dữ liệu quan hệ thường phù hợp vì dữ liệu có cấu trúc rõ ràng và có nhiều liên kết như:

- sản phẩm thuộc danh mục nào
- sản phẩm thuộc thương hiệu nào
- khách hàng có bao nhiêu đơn hàng
- đơn hàng chứa những sản phẩm nào
- thanh toán nào gắn với đơn hàng nào

### 2.8.2. Chuẩn hóa dữ liệu

Chuẩn hóa dữ liệu là quá trình tổ chức bảng và quan hệ giữa các bảng để giảm dư thừa dữ liệu, hạn chế bất nhất và nâng cao khả năng bảo trì. Trong thực tế, chuẩn hóa không phải lúc nào cũng được áp dụng tuyệt đối, nhưng về mặt lý thuyết, nó là nền tảng quan trọng để thiết kế schema hợp lý.

Đối với đề tài này, khái niệm chuẩn hóa đặc biệt có ý nghĩa vì hệ thống đang tồn tại song song giữa lớp bảng legacy và lớp schema mới được chuẩn hóa dần thông qua migrations.

### 2.8.3. Giao dịch và tính nhất quán dữ liệu

Trong các nghiệp vụ như checkout, tạo đơn hàng, thanh toán hoặc cập nhật trạng thái đơn, dữ liệu thường liên quan tới nhiều bảng khác nhau. Vì vậy, khái niệm transaction trong cơ sở dữ liệu là rất quan trọng. Transaction giúp bảo đảm rằng một nhóm thao tác sẽ được thực hiện trọn vẹn hoặc rollback khi có lỗi, từ đó giữ tính nhất quán của hệ thống.

Trong thương mại điện tử, đây là yêu cầu có giá trị thực tiễn cao vì sai lệch dữ liệu đơn hàng, thanh toán hoặc tồn kho có thể gây hậu quả trực tiếp tới vận hành.

## 2.9. Eloquent ORM và truy cập dữ liệu

ORM (Object Relational Mapping) là kỹ thuật ánh xạ dữ liệu từ bảng quan hệ sang các đối tượng trong ngôn ngữ lập trình. Eloquent là ORM của Laravel, cho phép định nghĩa model, quan hệ giữa các model và thao tác dữ liệu theo cách hướng đối tượng hơn.

Ưu điểm của ORM bao gồm:

- giảm lượng truy vấn SQL viết tay trong nhiều trường hợp
- biểu diễn quan hệ dữ liệu rõ ràng hơn
- giúp mã nguồn dễ đọc hơn khi làm việc với dữ liệu
- hỗ trợ tốt cho các thao tác CRUD phổ biến

Tuy nhiên, về mặt lý thuyết cũng cần lưu ý rằng ORM không thay thế hoàn toàn tư duy thiết kế dữ liệu. Đối với các hệ thống lớn, người phát triển vẫn cần hiểu rõ quan hệ bảng, chỉ mục, transaction và hiệu năng truy vấn.

## 2.10. Service Layer và Repository Pattern

### 2.10.1. Service Layer

Service layer là lớp trung gian chịu trách nhiệm xử lý nghiệp vụ thay vì để toàn bộ logic nằm trong controller. Một service thường tập trung vào một nhóm bài toán cụ thể như giỏ hàng, checkout, recommendation, báo cáo hoặc chăm sóc khách hàng.

Lợi ích của service layer:

- tách nghiệp vụ khỏi controller
- tăng khả năng tái sử dụng logic
- thuận tiện cho kiểm tra chất lượng
- giúp controller chỉ còn vai trò điều phối

Đối với hệ thống có nhiều chức năng như đề tài này, service layer là một cơ sở lý thuyết quan trọng để giải thích việc tổ chức mã nguồn theo domain.

### 2.10.2. Repository Pattern

Repository pattern là cách trừu tượng hóa thao tác truy cập dữ liệu, giúp business logic bớt phụ thuộc trực tiếp vào chi tiết lưu trữ. Về mặt lý thuyết, repository giúp:

- tăng tính tách biệt giữa nghiệp vụ và persistence
- thuận lợi hơn khi thay đổi cách truy cập dữ liệu
- hỗ trợ viết test hoặc mock dependency dễ hơn

Trong thực tế, không phải mọi ứng dụng Laravel đều bắt buộc dùng repository pattern. Tuy nhiên, với các dự án đang hướng tới chuẩn hóa kiến trúc, repository là một lựa chọn có giá trị khi nghiệp vụ ngày càng phức tạp.

## 2.11. REST API

### 2.11.1. Khái niệm REST

REST (Representational State Transfer) là phong cách thiết kế dịch vụ web trong đó tài nguyên được truy cập thông qua các endpoint và các phương thức HTTP tiêu chuẩn. REST thường được dùng để xây dựng API cho web, mobile app hoặc tích hợp hệ thống.

### 2.11.2. Các phương thức HTTP thường dùng

Trong REST API, các phương thức HTTP thường mang ý nghĩa như sau:

- `GET`: lấy dữ liệu
- `POST`: tạo mới tài nguyên
- `PUT` hoặc `PATCH`: cập nhật tài nguyên
- `DELETE`: xóa tài nguyên

Việc phân tách rõ phương thức HTTP giúp API dễ hiểu, nhất quán và thuận lợi cho việc mở rộng.

### 2.11.3. Định dạng JSON và mã trạng thái

REST API thường sử dụng JSON làm định dạng dữ liệu trao đổi. Bên cạnh đó, response còn đi kèm mã trạng thái HTTP để mô tả kết quả xử lý, ví dụ:

- `200 OK` cho truy vấn thành công
- `201 Created` cho tạo mới thành công
- `401 Unauthorized` khi chưa xác thực
- `403 Forbidden` khi không đủ quyền
- `404 Not Found` khi không tìm thấy tài nguyên
- `422 Unprocessable Entity` khi dữ liệu đầu vào không hợp lệ

Về mặt lý thuyết, sử dụng mã trạng thái phù hợp là một phần quan trọng của thiết kế API có chất lượng.

### 2.11.4. Versioning trong API

Khi hệ thống cần phát triển lâu dài, versioning là kỹ thuật quan trọng để tránh làm hỏng các client cũ khi API thay đổi. Việc tổ chức endpoint theo phiên bản như `v1` là một thực hành phổ biến, giúp hệ thống mở rộng an toàn hơn theo thời gian.

## 2.12. Xác thực và phân quyền

### 2.12.1. Xác thực

Xác thực là quá trình kiểm tra danh tính của người dùng. Trong ứng dụng web, xác thực thường diễn ra thông qua session đối với giao diện web và thông qua token đối với API. Đối với website thương mại điện tử, xác thực có thể áp dụng cho:

- khách hàng đăng nhập để checkout và xem lịch sử đơn hàng
- quản trị viên đăng nhập để sử dụng khu vực admin
- API client truy cập các endpoint protected

### 2.12.2. Phân quyền

Phân quyền là quá trình xác định người dùng đã xác thực được phép thực hiện hành động nào. Trong hệ thống có nhiều vai trò, chỉ xác thực là chưa đủ. Ví dụ, quản trị viên quản lý sản phẩm không nhất thiết phải có quyền xem toàn bộ cài đặt hệ thống.

Một mô hình phổ biến trong phân quyền là RBAC (Role-Based Access Control), trong đó quyền được gắn với vai trò, còn người dùng được gán vào một hoặc nhiều vai trò. Mô hình này đặc biệt phù hợp với khu vực quản trị của website thương mại điện tử.

### 2.12.3. Laravel Sanctum và quản lý token

Laravel Sanctum là giải pháp xác thực nhẹ cho API trong hệ sinh thái Laravel. Sanctum hỗ trợ quản lý token truy cập và phù hợp với các hệ thống cần triển khai API protected mà không muốn sử dụng giải pháp quá nặng.

Về mặt lý thuyết, việc sử dụng Sanctum cho phép hệ thống kết hợp tốt giữa web authentication và API authentication trong cùng một nền tảng.

### 2.12.4. Phân quyền bằng vai trò và quyền chi tiết

Trong các hệ thống admin hiện đại, phân quyền thường cần chi tiết tới từng nhóm chức năng như sản phẩm, nội dung, đơn hàng, người dùng, báo cáo hoặc settings. Việc sử dụng mô hình vai trò và quyền chi tiết giúp:

- giảm rủi ro truy cập sai phạm vi
- dễ tổ chức bộ máy quản trị
- thuận lợi cho bảo trì và kiểm soát hệ thống

## 2.13. Bảo mật trong ứng dụng web thương mại điện tử

### 2.13.1. Kiểm soát đầu vào

Một nguyên tắc cơ bản của bảo mật ứng dụng web là không tin tưởng trực tiếp dữ liệu từ phía người dùng. Tất cả dữ liệu đầu vào cần được kiểm tra, ràng buộc kiểu dữ liệu, định dạng và phạm vi giá trị để giảm nguy cơ lỗi và tấn công.

### 2.13.2. Chống tấn công phổ biến

Hệ thống web nói chung và website thương mại điện tử nói riêng cần lưu ý một số dạng tấn công phổ biến:

- SQL Injection
- Cross-Site Scripting (XSS)
- Cross-Site Request Forgery (CSRF)
- truy cập trái phép do thiếu kiểm tra authorization
- lộ thông tin nhạy cảm do xử lý lỗi không phù hợp

Về mặt lý thuyết, bảo mật không phải là một thành phần tách rời, mà phải được xem như một nguyên tắc xuyên suốt từ routing, validation, database access tới response handling.

### 2.13.3. Bảo mật trong luồng thanh toán và tài khoản

Các khu vực như đăng nhập, quên mật khẩu, checkout, payment callback và admin settings thường là những vùng nhạy cảm của hệ thống. Vì vậy, đây là những nơi cần kiểm tra kỹ xác thực, dữ liệu đầu vào, redirect và quyền truy cập.

Đối với hệ thống thương mại điện tử, sự cẩn trọng ở các khu vực này ảnh hưởng trực tiếp tới an toàn dữ liệu khách hàng và độ tin cậy của hệ thống.

## 2.14. AJAX và tương tác bất đồng bộ

AJAX là kỹ thuật cho phép trình duyệt gửi request tới server và nhận dữ liệu phản hồi mà không cần tải lại toàn bộ trang. Trong website thương mại điện tử, AJAX được dùng rất phổ biến ở các tính năng như:

- gợi ý tìm kiếm
- thêm giỏ hàng nhanh
- cập nhật số lượng sản phẩm trong giỏ
- tải bình luận hoặc đánh giá
- đồng bộ các thành phần nhỏ như badge hoặc quick cart

Về mặt lý thuyết, AJAX giúp cải thiện trải nghiệm người dùng do giảm số lần tải lại trang và làm giao diện phản hồi nhanh hơn. Tuy nhiên, nó cũng yêu cầu backend trả dữ liệu đúng định dạng và frontend xử lý trạng thái một cách nhất quán.

## 2.15. UML và ERD trong phân tích thiết kế hệ thống

### 2.15.1. UML

UML (Unified Modeling Language) là ngôn ngữ mô hình hóa được sử dụng để biểu diễn các khía cạnh khác nhau của hệ thống phần mềm. Trong báo cáo đồ án, UML giúp trình bày hệ thống ở mức khái quát trước khi đi sâu vào mã nguồn.

Một số loại sơ đồ UML thường dùng gồm:

- Use Case Diagram để mô tả tác nhân và chức năng
- Activity Diagram để mô tả luồng xử lý
- Sequence Diagram để mô tả thứ tự tương tác giữa các thành phần
- Class Diagram để mô tả cấu trúc lớp hoặc thực thể ở mức khái niệm

### 2.15.2. ERD

ERD (Entity Relationship Diagram) là sơ đồ thể hiện các thực thể dữ liệu và mối quan hệ giữa chúng. Đối với hệ thống thương mại điện tử, ERD có vai trò đặc biệt quan trọng vì dữ liệu là nền tảng cho toàn bộ các luồng như catalog, cart, checkout, order, review, payment và reporting.

Trong lý thuyết phân tích hệ thống, ERD giúp:

- xác định các nhóm dữ liệu chính
- làm rõ quan hệ giữa các bảng
- hỗ trợ quá trình xây dựng schema
- giảm nguy cơ thiếu hoặc chồng chéo dữ liệu

## 2.16. Kiểm tra chất lượng phần mềm

### 2.16.1. Vai trò của kiểm tra chất lượng

Kiểm tra chất lượng phần mềm là quá trình xác minh và đánh giá hệ thống nhằm phát hiện lỗi, kiểm tra sự phù hợp với yêu cầu và giảm nguy cơ phát sinh lỗi trong quá trình vận hành. Đối với một hệ thống có nhiều luồng như thương mại điện tử, kiểm tra chất lượng có vai trò rất quan trọng vì nghiệp vụ liên quan trực tiếp tới dữ liệu khách hàng và doanh thu.

### 2.16.2. Các mức kiểm tra chất lượng phổ biến

Một số mức kiểm tra chất lượng thường gặp gồm:

- integration test để kiểm tra sự phối hợp giữa các thành phần
- rà soát hồi quy để bảo đảm lỗi cũ không quay trở lại sau khi sửa mã nguồn


### 2.16.3. Kiểm tra chất lượng trong hệ thống đang chuyển đổi từ legacy

Khi hệ thống ở trạng thái hybrid giữa legacy và kiến trúc mới, kiểm tra chất lượng càng trở nên cần thiết. Bất kỳ thay đổi nào ở route, middleware, validation, support class hoặc service đều có thể ảnh hưởng tới nhiều luồng khác nhau. Do đó, về mặt lý thuyết, rà soát hồi quy là một thành phần gần như bắt buộc nếu muốn refactor an toàn.

## 2.17. Trải nghiệm người dùng trong thương mại điện tử

Trải nghiệm người dùng là yếu tố có ảnh hưởng trực tiếp tới hiệu quả của website thương mại điện tử. Một hệ thống kỹ thuật đúng nhưng khó dùng vẫn có thể tạo ra tỷ lệ thoát cao và giảm khả năng chuyển đổi.

Một số nguyên tắc UX cơ bản trong thương mại điện tử gồm:

- điều hướng rõ ràng
- tìm kiếm thuận tiện
- thông tin sản phẩm dễ đọc
- quá trình thêm giỏ hàng đơn giản
- checkout ít bước nhưng đủ an toàn
- phản hồi trạng thái rõ ràng
- hỗ trợ so sánh, đánh giá và lịch sử đơn hàng

Đối với đề tài này, các nguyên tắc trên là nền tảng lý thuyết giúp giải thích vì sao hệ thống cần các thành phần như search suggest, flash sale, compare, chatbot, history và admin tools.

## 2.18. Kết luận chương

Chương cơ sở lý thuyết đã trình bày các nền tảng quan trọng phục vụ cho việc phân tích, thiết kế và triển khai hệ thống website thương mại điện tử trên Laravel 11. Các nội dung được đề cập bao gồm thương mại điện tử, mô hình B2C, kiến trúc web, MVC và kiến trúc phân lớp, Laravel 11, Blade, middleware, request validation, cơ sở dữ liệu quan hệ, ORM, service layer, REST API, xác thực và phân quyền, bảo mật, AJAX, UML, ERD, kiểm tra chất lượng phần mềm và trải nghiệm người dùng.

Những cơ sở lý thuyết này là nền tảng để các chương sau đi vào phân tích yêu cầu, thiết kế kiến trúc, triển khai chức năng và đánh giá hệ thống theo đúng hiện trạng của dự án.
