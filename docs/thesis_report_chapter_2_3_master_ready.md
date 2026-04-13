# BẢN GHÉP COPY-PASTE READY CHO CHƯƠNG 2 VÀ CHƯƠNG 3

File này là bản ghép đã chốt cho hai chương trọng tâm của báo cáo. Nội dung được tổng hợp từ các file markdown đã chuẩn bị trong repo theo phương án đã rà soát, để anh có thể copy lần lượt sang Word mà không phải tự ghép lại thủ công từng phần.

Lưu ý khi dùng file này:

- các sơ đồ PlantUML hoặc Mermaid không được nhúng trực tiếp ở đây; thay vào đó là vị trí chèn hình và mô tả dưới hình
- các hình cần được xuất riêng từ những file nguồn đã ghi chú trong từng mục
- sau khi dán vào Word, cần cập nhật lại numbering hình, mục lục và danh mục hình

---
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

Trước khi đi sâu vào Laravel 11, cần phân biệt hai khái niệm là ngôn ngữ lập trình và framework. Ngôn ngữ lập trình cung cấp cú pháp và cơ chế thực thi để viết logic xử lý, còn framework là bộ khung phần mềm cung cấp cấu trúc tổ chức và các thành phần sẵn có để xây dựng ứng dụng nhanh hơn, nhất quán hơn. Trong phát triển web bằng PHP, framework giúp chuẩn hóa nhiều bài toán lặp lại như định tuyến, validation, làm việc với cơ sở dữ liệu, xác thực, phân quyền và trả response.

Laravel là một framework web mã nguồn mở viết cho PHP, được xây dựng theo hướng hiện đại, dễ đọc và ưu tiên trải nghiệm lập trình viên. Xét về mặt định nghĩa, Laravel không chỉ là tập hợp các thư viện rời rạc mà là một hệ thống quy ước phát triển ứng dụng tương đối hoàn chỉnh. Nhờ đó, người phát triển có thể tổ chức dự án theo cấu trúc rõ ràng hơn, tách biệt phần giao diện, phần nghiệp vụ và phần dữ liệu tốt hơn so với cách viết PHP thuần không có bộ khung nhất quán.

### 2.5.1. Tổng quan về Laravel

Laravel là một framework phát triển web mã nguồn mở sử dụng ngôn ngữ PHP, được thiết kế theo hướng hiện đại, dễ mở rộng và có hệ sinh thái phong phú. Laravel cung cấp sẵn nhiều thành phần quan trọng như routing, middleware, Eloquent ORM, Blade template, request validation, authentication, authorization, event, queue và testing.

Việc sử dụng Laravel giúp giảm thời gian xây dựng các thành phần nền tảng, từ đó người phát triển có thể tập trung nhiều hơn vào nghiệp vụ đặc thù của ứng dụng.

### 2.5.2. Lý do lựa chọn Laravel 11

Laravel 11 là phiên bản hiện đại, ổn định và phù hợp với các dự án cần phát triển nhanh nhưng vẫn bảo đảm tổ chức mã nguồn tốt. Đối với đề tài này, Laravel 11 có một số ưu điểm quan trọng:

- hỗ trợ routing rõ ràng cho web và API
- có middleware để kiểm soát xác thực và phân quyền
- có request validation giúp tách logic kiểm tra đầu vào
- có Eloquent ORM hỗ trợ làm việc với cơ sở dữ liệu quan hệ
- có Blade template phù hợp với mô hình website server-rendered
- có hệ thống test thuận lợi cho feature test

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
- dễ kiểm thử hơn
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
- thuận tiện cho kiểm thử
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

## 2.16. Kiểm thử phần mềm

### 2.16.1. Vai trò của kiểm thử

Kiểm thử phần mềm là quá trình xác minh và đánh giá hệ thống nhằm phát hiện lỗi, kiểm tra sự phù hợp với yêu cầu và giảm nguy cơ phát sinh lỗi trong quá trình vận hành. Đối với một hệ thống có nhiều luồng như thương mại điện tử, kiểm thử có vai trò rất quan trọng vì nghiệp vụ liên quan trực tiếp tới dữ liệu khách hàng và doanh thu.

### 2.16.2. Các mức kiểm thử phổ biến

Một số mức kiểm thử thường gặp gồm:

- unit test để kiểm tra thành phần nhỏ
- integration test để kiểm tra sự phối hợp giữa các thành phần
- feature test để kiểm tra luồng chức năng từ request tới response
- regression test để bảo đảm lỗi cũ không quay trở lại sau khi sửa mã nguồn

Trong bối cảnh ứng dụng web Laravel, feature test đặc biệt có giá trị vì nó phản ánh khá sát cách người dùng và hệ thống thực sự tương tác.

### 2.16.3. Kiểm thử trong hệ thống đang chuyển đổi từ legacy

Khi hệ thống ở trạng thái hybrid giữa legacy và kiến trúc mới, kiểm thử càng trở nên cần thiết. Bất kỳ thay đổi nào ở route, middleware, validation, support class hoặc service đều có thể ảnh hưởng tới nhiều luồng khác nhau. Do đó, về mặt lý thuyết, regression test là một thành phần gần như bắt buộc nếu muốn refactor an toàn.

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

## 2.18. Vai trò của HTML trong xây dựng website thương mại điện tử

Xét về mặt định nghĩa, HTML là viết tắt của HyperText Markup Language, tức ngôn ngữ đánh dấu siêu văn bản. Đây không phải là ngôn ngữ lập trình theo nghĩa truyền thống vì HTML không xử lý logic hay điều kiện nghiệp vụ; thay vào đó, nó dùng các thẻ đánh dấu để mô tả cấu trúc của tài liệu web. Trình duyệt đọc các thẻ HTML để hiểu đâu là tiêu đề, đoạn văn, danh sách, liên kết, bảng, biểu mẫu hay khu vực nội dung chính của trang.

Điểm quan trọng của HTML là tính cấu trúc. Một tài liệu HTML được tổ chức tốt sẽ giúp trình duyệt hiển thị chính xác, công cụ tìm kiếm hiểu được nội dung, đồng thời giúp CSS và JavaScript gắn vào đúng vùng cần thao tác. Trong báo cáo đồ án, việc nêu định nghĩa HTML là cần thiết vì nó làm rõ rằng website thương mại điện tử không bắt đầu từ logic xử lý nghiệp vụ, mà bắt đầu từ việc tổ chức thông tin sản phẩm, nội dung và biểu mẫu theo một cấu trúc web chuẩn.



HTML (HyperText Markup Language) là ngôn ngữ đánh dấu cơ bản dùng để tổ chức cấu trúc nội dung trên web. Trong bất kỳ website thương mại điện tử nào, HTML không chỉ có chức năng hiển thị chữ và hình, mà còn đóng vai trò tạo ra bộ khung logic cho toàn bộ giao diện như header, navigation, danh sách sản phẩm, biểu mẫu đăng nhập, giỏ hàng, checkout, phần đánh giá và footer.

Về mặt lý thuyết, HTML có ý nghĩa đặc biệt vì nó quyết định tính có cấu trúc của dữ liệu giao diện. Một trang chi tiết sản phẩm nếu chỉ được dựng theo kiểu trình bày trực quan mà không có cấu trúc hợp lý sẽ khó mở rộng, khó tối ưu SEO và khó tích hợp với CSS, JavaScript hoặc các công cụ phân tích hành vi người dùng. Ngược lại, khi nội dung được tổ chức bằng các thành phần semantic như `header`, `nav`, `section`, `article`, `main`, `aside`, `footer`, hệ thống sẽ rõ ràng hơn cả với trình duyệt, người phát triển lẫn các công cụ tìm kiếm.

Đối với website thương mại điện tử bán thiết bị công nghệ, HTML còn là nền tảng để mô hình hóa các vùng thông tin có độ chi tiết cao. Ví dụ, một trang sản phẩm cần có khu vực tiêu đề, mô tả ngắn, giá, giá khuyến mãi, tồn kho, biến thể, gallery ảnh, thông số kỹ thuật, bình luận và gợi ý sản phẩm liên quan. Nếu cấu trúc HTML không rõ, những khu vực này sẽ dễ bị trộn lẫn, gây khó khăn cho việc bảo trì giao diện cũng như cho việc gắn dữ liệu từ backend.

Ngoài ra, HTML còn gắn trực tiếp với các form nghiệp vụ như đăng ký, đăng nhập, nhập địa chỉ giao hàng, áp dụng coupon và xác nhận đơn hàng. Trong bối cảnh đó, HTML không đơn thuần là phần “trang trí” của website, mà là lớp biểu diễn đầu tiên của dữ liệu nghiệp vụ. Điều này giúp giải thích vì sao trong các ứng dụng Laravel server-rendered, tổ chức view tốt luôn là một phần của thiết kế hệ thống chứ không chỉ là phần giao diện.

## 2.19. Vai trò của CSS trong tổ chức giao diện và trải nghiệm người dùng

CSS là viết tắt của Cascading Style Sheets, có thể hiểu là ngôn ngữ dùng để mô tả cách trình bày của tài liệu HTML. Nếu HTML trả lời câu hỏi “nội dung này là gì”, thì CSS trả lời câu hỏi “nội dung đó nên được hiển thị như thế nào”. CSS quy định màu sắc, khoảng cách, kiểu chữ, kích thước, vị trí, hiệu ứng chuyển động cơ bản và nhiều yếu tố trực quan khác của giao diện web.

Từ góc nhìn lý thuyết, khái niệm “cascading” trong CSS rất quan trọng vì nó thể hiện cơ chế kế thừa và ưu tiên giữa các quy tắc trình bày. Nhờ cơ chế đó, giao diện có thể được tổ chức thành các lớp style dùng chung và các lớp style đặc thù cho từng màn hình hoặc từng thành phần. Điều này giúp website lớn như thương mại điện tử duy trì được sự nhất quán giữa trang chủ, danh mục, chi tiết sản phẩm, giỏ hàng, checkout và admin mà không cần lặp lại quá nhiều định nghĩa giao diện.

Nếu HTML chịu trách nhiệm về cấu trúc, CSS (Cascading Style Sheets) chịu trách nhiệm về trình bày và tổ chức trực quan của website. Trong môi trường thương mại điện tử, CSS có vai trò đặc biệt quan trọng vì chất lượng trình bày ảnh hưởng trực tiếp tới khả năng đọc thông tin, mức độ tin cậy của hệ thống và hành vi chuyển đổi của người dùng.

Về mặt lý thuyết, CSS giúp tách nội dung khỏi cách trình bày. Sự tách biệt này mang lại nhiều lợi ích: giao diện dễ bảo trì hơn, có thể thay đổi theme mà không làm hỏng logic HTML, và có thể tái sử dụng các quy tắc hiển thị cho nhiều module khác nhau. Đây là một nguyên tắc quan trọng trong phát triển web hiện đại vì hệ thống thương mại điện tử thường có số lượng màn hình lớn như trang chủ, trang danh mục, trang chi tiết sản phẩm, giỏ hàng, checkout, lịch sử đơn hàng và khu vực quản trị.

Trong website bán thiết bị công nghệ, CSS còn ảnh hưởng trực tiếp đến cách người dùng đọc và so sánh thông tin. Một sản phẩm công nghệ thường có nhiều thông số và nhiều mức giá, do đó nếu khoảng cách, kiểu chữ, màu sắc, thứ bậc tiêu đề và bố cục không hợp lý, người dùng sẽ khó ra quyết định mua hàng. Từ góc nhìn này, CSS không chỉ là công cụ làm đẹp, mà là công cụ tổ chức nhận thức thị giác.

Một điểm quan trọng khác là responsive design. Người dùng thương mại điện tử ngày nay truy cập bằng nhiều thiết bị khác nhau, từ desktop đến điện thoại. CSS với các kỹ thuật như flexbox, grid, breakpoint và media query giúp giao diện thích nghi với kích thước màn hình khác nhau mà vẫn bảo đảm nội dung quan trọng như ảnh sản phẩm, giá, nút mua hàng và trường nhập liệu luôn dễ thao tác. Điều này đặc biệt có ý nghĩa trong thương mại điện tử vì chỉ cần trải nghiệm trên thiết bị di động kém cũng có thể làm giảm mạnh tỷ lệ chuyển đổi.

Ở mức học thuật, CSS còn thể hiện mối liên hệ giữa kỹ thuật giao diện và UX. Một giao diện có cấu trúc CSS tốt sẽ giúp người dùng nhận ra nhanh vùng hành động chính, phân biệt rõ thông tin quan trọng với thông tin phụ và giảm tải nhận thức trong quá trình duyệt sản phẩm. Đây là cơ sở lý thuyết quan trọng để lý giải vì sao phần trình bày giao diện cần được xem như một yếu tố của thiết kế hệ thống, không chỉ là phần mỹ thuật.

## 2.20. Vai trò của JavaScript trong tương tác động của website thương mại điện tử

JavaScript là ngôn ngữ lập trình kịch bản được sử dụng chủ yếu ở phía trình duyệt để điều khiển hành vi động của trang web. Khác với HTML và CSS chỉ mô tả cấu trúc và trình bày, JavaScript cho phép giao diện phản ứng với hành động của người dùng như click, nhập liệu, cuộn trang, chọn thuộc tính sản phẩm hoặc gửi request bất đồng bộ. Vì vậy, về mặt định nghĩa, JavaScript là lớp công nghệ giúp website chuyển từ trạng thái hiển thị tĩnh sang trạng thái tương tác động.

Một đặc điểm lý thuyết quan trọng của JavaScript là nó hoạt động trên mô hình sự kiện. Trình duyệt liên tục lắng nghe các sự kiện như nhấn nút, đổi giá trị input, submit form hay tải xong dữ liệu, sau đó JavaScript mới thực hiện các hành vi tương ứng. Trong thương mại điện tử, mô hình này rất phù hợp vì nhiều thao tác của người dùng không cần tải lại toàn bộ trang mà chỉ cần cập nhật từng vùng nhỏ như số lượng giỏ hàng, danh sách gợi ý tìm kiếm, trạng thái coupon hoặc bảng phí vận chuyển.

JavaScript là ngôn ngữ kịch bản phía client, cho phép trang web phản hồi linh hoạt với thao tác của người dùng mà không cần tải lại toàn bộ trang trong mọi trường hợp. Trong lý thuyết phát triển web hiện đại, JavaScript là thành phần giúp nối cấu trúc HTML và cách trình bày CSS với hành vi tương tác thực tế.

Trong website thương mại điện tử, JavaScript thường xuất hiện ở các chức năng như gợi ý tìm kiếm, quickview, cập nhật giỏ hàng bằng AJAX, tính phí vận chuyển, dropdown chọn địa chỉ, chuyển tab thông tin sản phẩm, carousel ảnh, bộ lọc danh mục và các tương tác nhỏ khác. Những thao tác này làm cho hệ thống phản hồi nhanh hơn, giảm số lần tải lại trang và đem lại cảm giác sử dụng mượt hơn.

Tuy nhiên, về mặt lý thuyết cũng cần thấy rằng JavaScript không phải là mục tiêu tự thân. Giá trị của nó nằm ở việc nâng cao khả năng tương tác nhưng vẫn phải đồng bộ với backend. Một thao tác thêm vào giỏ hàng bằng AJAX chỉ có ý nghĩa khi dữ liệu session, coupon, phí vận chuyển và trạng thái quick cart được cập nhật thống nhất. Vì vậy, JavaScript trong thương mại điện tử luôn phải được nhìn như một phần của luồng xuyên suốt từ frontend đến backend, thay vì chỉ là hiệu ứng giao diện.

Đối với đề tài này, JavaScript còn có vai trò trong các phần storefront cần phản hồi theo thời gian thực tương đối như search suggest, add-cart-ajax, hover cart, tính phí vận chuyển và chatbot. Các khu vực này là minh chứng rõ ràng cho việc website không chỉ có dạng server-rendered thuần túy, mà là một mô hình lai trong đó HTML và Blade chịu trách nhiệm render nền, còn JavaScript đảm nhiệm các tương tác động ở từng vùng cục bộ.

Về mặt học thuật, sự phối hợp giữa HTML, CSS và JavaScript phản ánh mô hình phân lớp ở frontend. HTML là lớp cấu trúc, CSS là lớp trình bày, JavaScript là lớp hành vi. Ba lớp này kết hợp với nhau tạo nên giao diện hoàn chỉnh của website thương mại điện tử.

## 2.21. Vai trò của PHP trong phát triển ứng dụng web thương mại điện tử

PHP là viết tắt của cụm Hypertext Preprocessor, là một ngôn ngữ lập trình phía server được thiết kế đặc biệt cho môi trường web. Về mặt định nghĩa, PHP cho phép máy chủ nhận request từ trình duyệt, chạy mã xử lý trên server rồi sinh ra kết quả trả về cho client. Kết quả đó có thể là HTML hoàn chỉnh, JSON cho API hoặc dữ liệu dùng cho các tương tác AJAX. Chính khả năng sinh nội dung động phía server là điểm khác biệt căn bản giữa PHP với HTML, CSS và JavaScript thuần phía client.

Một lý do PHP vẫn có vai trò lớn trong phát triển web là vì nó được thiết kế bám rất sát các bài toán của ứng dụng web truyền thống: làm việc với form, session, cookie, cơ sở dữ liệu, upload file, email và response HTTP. Trong các hệ thống thương mại điện tử, đây đều là các bài toán trung tâm. Vì vậy, khi đưa PHP vào cơ sở lý thuyết, mục tiêu không chỉ là nêu tên ngôn ngữ mà là làm rõ lớp công nghệ chịu trách nhiệm xử lý nghiệp vụ và dữ liệu ở phía server.

PHP là ngôn ngữ lập trình phía server được sử dụng rất rộng rãi trong phát triển ứng dụng web. Khác với HTML, CSS và JavaScript chủ yếu xử lý cấu trúc, trình bày và hành vi ở phía trình duyệt, PHP đảm nhiệm phần logic xử lý trung tâm của hệ thống. Khi người dùng gửi form đăng nhập, thêm sản phẩm vào giỏ hàng, xác nhận đơn hàng hoặc gọi API, phần backend cần có một ngôn ngữ đủ mạnh để tiếp nhận request, kiểm tra dữ liệu, thao tác với cơ sở dữ liệu và trả về response phù hợp. Trong bối cảnh đó, PHP là lớp công nghệ nền cho rất nhiều hệ thống thương mại điện tử.

Về mặt lý thuyết, vai trò lớn nhất của PHP nằm ở chỗ nó cho phép website không chỉ là tập hợp các trang tĩnh, mà trở thành một ứng dụng web có khả năng xử lý nghiệp vụ. Với PHP, hệ thống có thể duy trì session, lưu trạng thái đăng nhập, truy vấn dữ liệu sản phẩm, tính tổng tiền đơn hàng, áp dụng mã giảm giá, kiểm tra quyền truy cập và sinh ra nội dung động theo từng ngữ cảnh sử dụng. Nếu không có lớp xử lý phía server, website thương mại điện tử sẽ không thể vận hành các luồng nghiệp vụ cốt lõi như giỏ hàng, checkout hay quản trị.

Một ưu điểm quan trọng của PHP là tính thực tiễn cao trong môi trường phát triển web. PHP có hệ sinh thái lâu đời, tài liệu phong phú, khả năng tích hợp tốt với máy chủ web và hỗ trợ mạnh cho các hệ quản trị cơ sở dữ liệu quan hệ như MySQL. Đối với bài toán thương mại điện tử, điều này rất phù hợp vì hệ thống cần xử lý đồng thời nhiều thực thể dữ liệu như sản phẩm, danh mục, thương hiệu, khách hàng, đơn hàng, thanh toán, đánh giá và nội dung truyền thông. Việc lựa chọn PHP giúp dự án tận dụng được nền tảng kỹ thuật ổn định để phát triển nhanh nhưng vẫn có thể tổ chức kiến trúc ở mức tương đối tốt.

Đối với đề tài đang thực hiện, PHP không nên chỉ được nhắc tới như “ngôn ngữ dùng để viết Laravel”. Ở góc nhìn học thuật, PHP là nền tảng giúp hình thành lớp server-side của toàn bộ hệ thống. Mọi xử lý như định tuyến request, xác thực, validation, làm việc với session, tương tác dữ liệu và trả HTML hoặc JSON đều dựa trên lớp thực thi này. Vì vậy, đưa PHP vào chương cơ sở lý thuyết là cần thiết để báo cáo không bị lệch quá nhiều sang mức framework mà bỏ qua ngôn ngữ nền của hệ thống.

## 2.22. Mô hình xử lý phía server bằng PHP trong website thương mại điện tử

Để hiểu rõ vai trò của PHP, cần nhìn nó trong mô hình request-response của ứng dụng web. Khi người dùng truy cập một URL hoặc gửi dữ liệu từ form, trình duyệt tạo ra request và gửi đến máy chủ. Lúc này, mã PHP ở phía server sẽ tiếp nhận request, đọc tham số đầu vào, kiểm tra dữ liệu, xử lý nghiệp vụ, truy vấn cơ sở dữ liệu khi cần và sau đó trả response. Response này có thể là một trang HTML được dựng sẵn, một lệnh chuyển hướng, hoặc dữ liệu JSON phục vụ tương tác AJAX và API.

Trong website thương mại điện tử, mô hình xử lý phía server đặc biệt quan trọng vì phần lớn các thao tác đều không thể tin cậy hoàn toàn vào phía client. Ví dụ, khi người dùng nhập số lượng sản phẩm hoặc mã giảm giá, hệ thống không thể chỉ dựa vào dữ liệu gửi lên từ trình duyệt mà phải xác minh lại trên server. Tương tự, khi người dùng đăng nhập hoặc truy cập khu vực quản trị, việc xác thực danh tính và phân quyền cũng phải được thực hiện phía server để tránh rủi ro bảo mật. Điều này cho thấy PHP không chỉ là ngôn ngữ xử lý nội dung động mà còn là lớp kiểm soát tính hợp lệ và an toàn của hệ thống.

Về mặt luồng xử lý, mô hình server-side còn liên quan trực tiếp tới các khái niệm session, cookie và trạng thái ứng dụng. Thương mại điện tử là loại hệ thống có nhiều trạng thái tạm thời như giỏ hàng hiện tại, thông tin khách hàng đã đăng nhập, đích điều hướng sau đăng nhập, coupon đang được áp dụng hoặc địa chỉ giao hàng vừa chọn. Những thông tin này cần được duy trì nhất quán trong suốt phiên làm việc. PHP kết hợp với framework và hệ thống session giúp quản lý tốt các trạng thái đó, từ đó tạo ra trải nghiệm liền mạch giữa nhiều bước thao tác khác nhau.

Khi liên hệ với đề tài, mô hình xử lý phía server là cơ sở để giải thích các luồng thật trong repo như đăng nhập rồi quay lại checkout, thêm sản phẩm vào giỏ bằng AJAX, tính phí vận chuyển, xác nhận đơn hàng hoặc truy cập admin qua middleware phân quyền. Những luồng này đều có điểm chung là dữ liệu được kiểm soát và xử lý ở server rồi mới phản hồi ra view hoặc JSON. Vì vậy, phần lý thuyết về xử lý phía server bằng PHP giúp người đọc hiểu rõ hơn bản chất vận hành của dự án, thay vì chỉ nhìn hệ thống như một tập hợp màn hình giao diện.

## 2.23. Ý nghĩa của framework trong phát triển ứng dụng PHP

Nếu chỉ sử dụng PHP thuần, người phát triển vẫn có thể xây dựng website, nhưng khi hệ thống lớn dần thì mã nguồn rất dễ bị phân tán, lặp logic và khó bảo trì. Các bài toán như routing, validation, authentication, authorization, template rendering, database abstraction, error handling và testing nếu tự tổ chức hoàn toàn từ đầu sẽ tốn nhiều thời gian, đồng thời dễ thiếu nhất quán giữa các module. Chính vì vậy, khái niệm framework trở thành một phần quan trọng trong phát triển ứng dụng web bằng PHP.

Về mặt lý thuyết, framework có thể được hiểu là một bộ khung tổ chức phát triển phần mềm, cung cấp sẵn cấu trúc thư mục, quy tắc làm việc và các thành phần kỹ thuật phổ biến. Framework không thay thế tư duy thiết kế hệ thống, nhưng giúp chuẩn hóa cách cài đặt các thành phần lặp lại. Nhờ đó, người phát triển có thể tập trung nhiều hơn vào bài toán nghiệp vụ đặc thù như catalog, giỏ hàng, checkout, báo cáo hoặc phân quyền, thay vì tốn quá nhiều công sức cho hạ tầng cơ bản.

Đối với website thương mại điện tử, giá trị của framework còn nằm ở việc giảm rủi ro phát sinh từ sự thiếu đồng nhất. Khi hệ thống có nhiều route, nhiều form nhập liệu và nhiều luồng phân quyền khác nhau, việc sử dụng một framework giúp các module bám theo cùng một nguyên tắc xử lý request, validate dữ liệu, truy cập database và trả response. Điều này đặc biệt quan trọng với các dự án có phạm vi tương đối rộng, vừa có storefront, vừa có admin, vừa có REST API như đề tài hiện tại.

Nhìn từ góc độ báo cáo đồ án, đưa phần lý thuyết về framework vào Chương 2 giúp làm rõ rằng việc chọn Laravel không phải là một lựa chọn ngẫu nhiên theo xu hướng, mà là một quyết định có cơ sở kỹ thuật. Ngôn ngữ PHP cung cấp năng lực xử lý phía server, còn framework cung cấp cơ chế tổ chức, chuẩn hóa và tăng khả năng mở rộng cho hệ thống. Sự phân biệt này giúp chương cơ sở lý thuyết chặt hơn và tránh cách trình bày đơn giản hóa rằng chỉ cần “dùng Laravel là đủ”.

## 2.24. Hệ sinh thái Laravel trong phát triển website thương mại điện tử

Ở mục trước, Laravel đã được giới thiệu như một framework hiện đại cho PHP. Tuy nhiên, để chương cơ sở lý thuyết đủ chiều sâu, cần nhìn Laravel không chỉ như một công cụ tạo route hay CRUD, mà như một hệ sinh thái hỗ trợ nhiều mặt của ứng dụng web. Trong phát triển website thương mại điện tử, hệ sinh thái này có ý nghĩa lớn vì hệ thống không chỉ cần hiển thị sản phẩm mà còn cần quản lý xác thực, phiên làm việc, phân quyền, dữ liệu quan hệ, API, queue, mail, console command và testing.

Một điểm mạnh của Laravel là khả năng tích hợp các lớp chức năng này trong một mô hình thống nhất. Routing giúp định nghĩa rõ entry point của web và API. Middleware giúp kiểm soát truy cập. Request validation giúp chuẩn hóa kiểm tra dữ liệu đầu vào. Blade hỗ trợ dựng giao diện server-rendered. Eloquent hỗ trợ mô hình hóa quan hệ dữ liệu. Sanctum phục vụ xác thực API. Các package như `spatie/laravel-permission` mở rộng khả năng phân quyền. Artisan command hỗ trợ vận hành và tự động hóa. Testing framework giúp tạo feature test cho các luồng nghiệp vụ quan trọng. Khi nhìn chung lại, có thể thấy Laravel phù hợp với hệ thống thương mại điện tử không phải chỉ vì “dễ code”, mà vì nó cung cấp một nền tảng đồng bộ để nhiều lớp kỹ thuật hoạt động cùng nhau.

Trong phạm vi đề tài, giá trị của hệ sinh thái Laravel còn thể hiện ở chỗ nó cho phép dự án phát triển theo hướng hybrid nhưng vẫn có trục chuẩn hóa. Mặc dù repo vẫn tồn tại một số thành phần legacy, các lớp mới như request validation, service, persistence, API authentication, permission và feature test đều có thể bám vào các công cụ mà Laravel và hệ sinh thái của nó cung cấp. Điều này có ý nghĩa thực tiễn lớn: hệ thống không cần viết lại toàn bộ từ đầu mà vẫn có thể nâng cấp dần kiến trúc theo từng bước.

Về mặt học thuật, bổ sung nội dung này giúp chương cơ sở lý thuyết tránh bị ngắn ở phần Laravel. Thay vì chỉ dừng ở định nghĩa và vài ưu điểm tổng quát, báo cáo có thể trình bày Laravel như một nền tảng phát triển toàn diện cho website thương mại điện tử. Cách tiếp cận đó phù hợp hơn với chính hiện trạng của dự án, vì repo thực tế cũng đang sử dụng Laravel không chỉ để dựng web page mà còn để xây API, phân quyền, console command và kiểm thử tự động.

## 2.25. Mối quan hệ giữa HTML, CSS, JavaScript, PHP và Laravel trong kiến trúc của website

Khi xét riêng lẻ, HTML, CSS, JavaScript, PHP và Laravel có thể được trình bày như các công nghệ độc lập. Tuy nhiên, trong một website thương mại điện tử hoàn chỉnh, giá trị thực sự của chúng nằm ở cách phối hợp với nhau để tạo thành một hệ thống nhất quán. Từ góc độ kiến trúc, có thể xem HTML là lớp cấu trúc nội dung, CSS là lớp trình bày, JavaScript là lớp hành vi tương tác phía client, PHP là lớp xử lý phía server và Laravel là bộ khung tổ chức toàn bộ quá trình đó trong ứng dụng.

Mối quan hệ này có thể hình dung theo luồng sau: người dùng thao tác trên giao diện được trình bày bằng HTML và CSS; những tương tác cục bộ hoặc bất đồng bộ được hỗ trợ bởi JavaScript; request sau đó được gửi tới server nơi PHP và Laravel tiếp nhận, kiểm tra, xử lý nghiệp vụ, truy vấn cơ sở dữ liệu và trả kết quả lại cho người dùng. Nếu là giao diện web, kết quả thường được render ra HTML thông qua Blade; nếu là API hoặc AJAX, kết quả có thể là JSON. Nhờ vậy, frontend và backend không tách rời hoàn toàn, mà phối hợp theo một chu trình xử lý thống nhất.

Đối với đề tài, mối quan hệ này rất quan trọng vì dự án không đi theo hướng frontend SPA tách biệt hoàn toàn khỏi backend. Thay vào đó, hệ thống chủ yếu là server-rendered bằng Laravel Blade, sau đó tăng cường trải nghiệm bằng các vùng tương tác dùng JavaScript và AJAX. Vì vậy, chương cơ sở lý thuyết cần phản ánh đúng mô hình công nghệ này để tránh gây hiểu nhầm rằng dự án chỉ là backend thuần túy hoặc ngược lại chỉ là frontend nặng về hiệu ứng.

Xét theo góc nhìn đồ án tốt nghiệp, việc trình bày mối liên hệ giữa các công nghệ cũng giúp phần cơ sở lý thuyết dài hơn nhưng không bị rời rạc. Thay vì viết rời từng mục công nghệ một cách lặp lại, báo cáo có thể chỉ ra vai trò riêng của mỗi công nghệ và cách chúng phối hợp để phục vụ các luồng nghiệp vụ của website thương mại điện tử. Cách viết này vừa tăng chiều sâu, vừa giữ được sự liên quan trực tiếp tới dự án đang xây dựng.

## 2.26. Vai trò của hình ảnh và media trong website bán hàng trực tuyến

Trong thương mại điện tử, hình ảnh là một trong những thành phần có ảnh hưởng mạnh nhất tới quyết định mua hàng. Không giống mua hàng trực tiếp tại cửa hàng vật lý, người dùng trực tuyến không thể chạm, cầm hoặc quan sát sản phẩm ở khoảng cách thực. Vì vậy, hình ảnh và media giữ vai trò thay thế một phần trải nghiệm vật lý đó.

Về mặt lý thuyết, hình ảnh sản phẩm thực hiện nhiều chức năng cùng lúc. Thứ nhất, nó cung cấp thông tin trực quan giúp người dùng nhận diện sản phẩm nhanh hơn so với mô tả văn bản. Thứ hai, nó tăng mức độ tin cậy cho website vì sản phẩm được thể hiện rõ, có nhiều góc chụp, có ảnh chi tiết hoặc ảnh bối cảnh sử dụng. Thứ ba, nó làm tăng tính hấp dẫn của giao diện, hỗ trợ marketing và giúp người dùng ghi nhớ thương hiệu tốt hơn.

Đối với website bán thiết bị công nghệ, hình ảnh còn có vai trò lớn hơn do sản phẩm thường liên quan tới thiết kế bên ngoài, cổng kết nối, màu sắc, kích thước, phụ kiện đi kèm và các chi tiết dễ gây so sánh. Bên cạnh ảnh tĩnh, nhiều website còn bổ sung video, gallery mở rộng, ảnh zoom hoặc infographic thông số kỹ thuật để tăng hiệu quả truyền đạt.

Tuy nhiên, về mặt kỹ thuật, sử dụng hình ảnh cũng kéo theo các yêu cầu về tối ưu hiệu năng. Ảnh có dung lượng quá lớn sẽ làm chậm tốc độ tải trang, trong khi ảnh quá nhỏ hoặc chất lượng quá thấp sẽ làm giảm độ tin cậy. Do đó, về mặt lý thuyết, hệ thống thương mại điện tử cần cân bằng giữa chất lượng hiển thị và hiệu năng thông qua các kỹ thuật như resize, nén ảnh, lazy loading, sinh thumbnail và tổ chức media library.

Trong phạm vi đề tài, sự tồn tại của các module liên quan tới gallery, media, thumbnail, video và document cho thấy hình ảnh không chỉ là tài nguyên phụ trợ. Nó là một phần của mô hình dữ liệu và của trải nghiệm mua hàng. Điều này rất đáng đưa vào cơ sở lý thuyết vì nó giúp người đọc thấy rằng xây dựng website thương mại điện tử không chỉ là bài toán form nhập và cơ sở dữ liệu, mà còn là bài toán truyền tải thông tin trực quan.

## 2.27. Khách vãng lai trong hệ thống website thương mại điện tử

Một khái niệm thường bị bỏ qua trong báo cáo nhưng rất quan trọng ở góc nhìn phân tích hệ thống là khách vãng lai. Khách vãng lai là người dùng truy cập website nhưng chưa đăng nhập và chưa thiết lập quan hệ tài khoản với hệ thống. Họ là lớp người dùng đầu tiên mà bất kỳ website thương mại điện tử nào cũng phải phục vụ tốt nếu muốn tăng khả năng chuyển đổi.

Về mặt nghiệp vụ, khách vãng lai thường có thể thực hiện một số thao tác như xem trang chủ, duyệt danh mục, xem chi tiết sản phẩm, tìm kiếm, đọc đánh giá, so sánh sản phẩm và trong nhiều hệ thống còn có thể thêm tạm vào giỏ hàng. Tuy nhiên, các hành vi nhạy cảm hơn như xác nhận đơn hàng, xem lịch sử giao dịch hoặc quản lý hồ sơ cá nhân thường yêu cầu đăng nhập.

Điều này cho thấy khách vãng lai là một tác nhân riêng, không nên bị gộp hoàn toàn với khách hàng đã đăng nhập. Trong lý thuyết phân tích thiết kế, việc tách bạch hai nhóm này giúp use case rõ hơn, phân quyền chặt hơn và phản ánh đúng trải nghiệm người dùng. Nếu không tách, báo cáo dễ mô tả sai rằng mọi người dùng đều có cùng khả năng thao tác, trong khi thực tế hệ thống vẫn phải có các bước chuyển tiếp như đăng nhập trước checkout.

Đối với đề tài này, khái niệm khách vãng lai rất phù hợp với luồng thật của repo. Người dùng có thể xem sản phẩm và duyệt hệ thống mà không cần xác thực, nhưng khi đi vào `checkout`, hệ thống sẽ điều hướng sang đăng nhập rồi mới quay trở lại bước mua hàng. Vì vậy, việc đưa khách vãng lai vào phần cơ sở lý thuyết không chỉ làm rõ hơn use case, mà còn giúp phần phân tích yêu cầu và sơ đồ tác nhân bám thực tế hơn.

## 2.28. Phân tích các website tham chiếu

### 2.28.1. Phân tích website tham chiếu thứ nhất: Thế Giới Di Động

Một website tham chiếu hữu ích cho đề tài là Thế Giới Di Động. Đây là một ví dụ điển hình của website bán thiết bị công nghệ theo mô hình B2C, nơi người dùng được dẫn dắt từ trang chủ tới danh mục, từ danh mục tới trang chi tiết và từ đó sang quyết định mua hàng.

Điểm đáng chú ý về mặt lý thuyết của nhóm website này là cách tổ chức thông tin rất thiên về hỗ trợ ra quyết định. Sản phẩm thường đi kèm ảnh lớn, giá, mức giảm giá, thông tin trả góp, đánh giá người dùng, cấu hình nổi bật và các cụm gợi ý liên quan. Tức là giao diện không chỉ nhằm trưng bày hàng hóa mà còn đóng vai trò như một công cụ tư vấn sơ bộ.

Một bài học quan trọng có thể rút ra là cấu trúc thông tin cần được thiết kế theo hành vi mua hàng thực tế. Người dùng không chỉ muốn biết tên sản phẩm, mà còn muốn so sánh, xem giá hiện tại, biết khuyến mãi và đánh giá sơ bộ chất lượng. Vì vậy, website thương mại điện tử trong đề tài cũng cần tổ chức giao diện theo hướng ưu tiên thông tin quyết định mua hàng thay vì chỉ hiển thị mô tả chung.

### 2.28.2. Phân tích website tham chiếu thứ hai: FPT Shop

FPT Shop là một ví dụ tham chiếu khác có giá trị về mặt lý thuyết, đặc biệt ở cách tổ chức storefront theo nhóm ngành hàng và nhóm khuyến mãi. Một đặc điểm thường thấy ở các website kiểu này là việc kết hợp giữa chức năng bán hàng và chức năng truyền thông marketing ngay trên cùng một bề mặt giao diện.

Về mặt phân tích hệ thống, điều này cho thấy website thương mại điện tử hiện đại không thể xem như một ứng dụng CRUD đơn giản. Nó phải hỗ trợ banner, flash sale, block khuyến mãi, giới thiệu ưu đãi, nội dung truyền thông, landing page và các vùng điều hướng động. Từ đây có thể thấy rõ mối liên hệ giữa quản trị nội dung và quản trị bán hàng trong cùng một hệ thống.

Bài học rút ra cho đề tài là khu vực admin cần có công cụ quản lý nội dung đủ linh hoạt, không chỉ quản lý sản phẩm mà còn quản lý slider, bài viết, video và các vùng truyền thông khác. Đây chính là lý do mà phần cơ sở lý thuyết nên đưa thêm các khái niệm liên quan tới CMS, giao diện marketing và trình bày nội dung sản phẩm.

### 2.28.3. Phân tích website tham chiếu thứ ba: CellphoneS

CellphoneS là một ví dụ tiêu biểu cho mô hình storefront giàu tính so sánh và khuyến nghị. Đối với người dùng mua thiết bị công nghệ, trải nghiệm quan trọng không chỉ là “xem một sản phẩm”, mà là “so sánh giữa nhiều lựa chọn” dựa trên thương hiệu, cấu hình, tầm giá và ưu đãi đi kèm.

Về mặt lý thuyết, điều này làm nổi bật tầm quan trọng của các chức năng product discovery, recommendation, compare và filter. Một website tốt trong lĩnh vực công nghệ cần cho phép người dùng thu hẹp lựa chọn dần, từ đó giảm tải nhận thức khi phải đối diện với quá nhiều sản phẩm có thông số gần nhau.

Từ góc nhìn của đề tài, bài học quan trọng là hệ thống không nên chỉ tập trung vào bước cuối cùng của quy trình mua hàng như giỏ hàng và checkout. Ngược lại, nó cần quan tâm cả giai đoạn khám phá sản phẩm. Việc dự án có search suggest, so sánh sản phẩm, review, recommendation và reporting liên quan tới discovery là hoàn toàn phù hợp với xu hướng của các website bán thiết bị công nghệ thực tế.

## 2.29. So sánh và rút ra bài học thiết kế từ ba website tham chiếu

Khi đặt ba website tham chiếu cạnh nhau, có thể nhận ra một số điểm chung quan trọng về mặt lý thuyết. Thứ nhất, cả ba đều coi storefront là một môi trường hỗ trợ ra quyết định, không chỉ là nơi liệt kê sản phẩm. Thứ hai, hình ảnh, banner, block khuyến mãi và thông tin giá luôn được ưu tiên ở vị trí dễ thấy. Thứ ba, các công cụ hỗ trợ khám phá như tìm kiếm, lọc, so sánh, đánh giá và gợi ý sản phẩm đóng vai trò rất lớn.

Điểm chung tiếp theo là sự liên kết chặt giữa website bán hàng và quản trị nội dung. Một hệ thống thương mại điện tử thực tế phải đồng thời giải quyết ba bài toán: quản lý sản phẩm, hỗ trợ hành vi mua hàng và tổ chức nội dung marketing. Đây cũng chính là lý do mà trong đề tài, việc có thêm các module video, post, slider, chat và report là hợp lý hơn so với cách nhìn website như một hệ thống bán hàng tối giản.

Một bài học nữa là vai trò của khách vãng lai. Cả ba nhóm website tham chiếu đều cho phép người dùng duyệt sản phẩm và nghiên cứu rất sâu trước khi đăng nhập. Điều này xác nhận rằng trong phân tích use case, khách vãng lai nên được xem là tác nhân riêng với phạm vi thao tác khác khách hàng đã đăng nhập.

## 2.30. Ý nghĩa của nhóm công nghệ HTML, CSS, JavaScript, PHP, Laravel và hình ảnh đối với đề tài

Nếu nhìn tổng thể, HTML, CSS, JavaScript, PHP, Laravel và hình ảnh không phải là các mảnh rời rạc, mà là các thành phần cùng tạo nên hệ thống web hoàn chỉnh. HTML tổ chức nội dung, CSS tổ chức cách trình bày, JavaScript tổ chức hành vi tương tác, PHP đảm nhiệm xử lý phía server, Laravel cung cấp bộ khung tổ chức toàn bộ ứng dụng, còn hình ảnh tổ chức phần thông tin trực quan. Khi các yếu tố này phối hợp tốt, hệ thống mới có thể đáp ứng đồng thời yêu cầu kỹ thuật, yêu cầu trải nghiệm người dùng và yêu cầu trình bày sản phẩm.

Trong đề tài này, nhóm công nghệ đó đặc biệt phù hợp với mô hình Laravel server-rendered kết hợp AJAX. Blade giúp sinh HTML phía server, CSS chịu trách nhiệm dựng layout và style, JavaScript đảm nhiệm các tương tác động cục bộ như quick cart, search suggest hay chatbot, PHP và Laravel xử lý request, validation, session, service, persistence và response, còn hệ thống media chịu trách nhiệm hiển thị ảnh và video sản phẩm. Đây là một kiến trúc phù hợp đối với một website bán thiết bị công nghệ quy mô đồ án nhưng đã có nhiều module thực tế.

Vì vậy, bổ sung nhóm nội dung này vào Chương 2 sẽ giúp cơ sở lý thuyết cân bằng hơn giữa frontend, backend, framework, dữ liệu và trải nghiệm người dùng. Báo cáo sẽ phản ánh rõ hơn rằng website thương mại điện tử là một hệ thống tích hợp nhiều lớp, trong đó ngôn ngữ nền, framework, giao diện, hình ảnh và hành vi người dùng đều có vai trò trong sự thành công của sản phẩm.

## 2.31. Ưu điểm và hạn chế của HTML trong xây dựng website thương mại điện tử

Ưu điểm lớn nhất của HTML là tính chuẩn hóa và phổ quát. Hầu như mọi trình duyệt hiện đại đều hỗ trợ HTML, nhờ đó nội dung có thể được hiển thị ổn định trên nhiều thiết bị và môi trường khác nhau. HTML cũng rất phù hợp để biểu diễn những thành phần cốt lõi của website thương mại điện tử như tiêu đề sản phẩm, mô tả, bảng thông số kỹ thuật, form đăng nhập, form checkout, danh sách danh mục và các vùng điều hướng. Một ưu điểm khác là HTML hỗ trợ semantic markup, giúp cải thiện khả năng đọc hiểu của công cụ tìm kiếm và các công cụ hỗ trợ truy cập.

Tuy nhiên, HTML có hạn chế rõ ràng là chỉ đảm nhiệm vai trò mô tả cấu trúc, không thể tự xử lý nghiệp vụ hay hành vi động. Nếu chỉ có HTML, website sẽ dừng lại ở mức hiển thị nội dung mà không có khả năng phản hồi linh hoạt với thao tác người dùng hoặc xử lý quy trình nghiệp vụ phức tạp. Trong thương mại điện tử, hạn chế này buộc HTML phải luôn đi cùng CSS, JavaScript và lớp xử lý phía server.

Từ góc độ đồ án, việc nêu cả ưu điểm và hạn chế của HTML giúp báo cáo tránh cách trình bày một chiều. Nó cho thấy HTML là nền tảng bắt buộc của giao diện web, nhưng chỉ phát huy đúng vai trò khi được đặt trong một kiến trúc tổng thể có sự hỗ trợ của các công nghệ khác.

## 2.32. Ưu điểm và hạn chế của CSS trong tổ chức giao diện

CSS có ưu điểm nổi bật ở khả năng tách phần trình bày khỏi phần nội dung. Nhờ đó, giao diện có thể được quản lý tập trung, thay đổi theme hoặc điều chỉnh bố cục mà không phải viết lại cấu trúc HTML. Đối với website thương mại điện tử, điều này đặc biệt có giá trị vì hệ thống thường có nhiều màn hình dùng lại các thành phần như card sản phẩm, banner, menu, form, bảng giá, nút hành động và các khối nội dung phụ trợ. CSS cũng hỗ trợ responsive design rất tốt, giúp website thích nghi trên desktop, tablet và mobile.

Tuy nhiên, CSS cũng có những hạn chế. Khi dự án lớn dần, việc quản lý nhiều selector, nhiều trạng thái hiển thị và nhiều lớp giao diện có thể trở nên phức tạp nếu không có nguyên tắc tổ chức rõ ràng. Ngoài ra, CSS chỉ chịu trách nhiệm trình bày nên không thể giải quyết các vấn đề liên quan tới dữ liệu, logic nghiệp vụ hay xác thực trạng thái người dùng. Vì vậy, CSS là thành phần rất quan trọng của trải nghiệm người dùng, nhưng không thể tách rời khỏi HTML, JavaScript và backend.

Trong phạm vi đề tài, việc nhấn mạnh ưu điểm và hạn chế của CSS giúp làm rõ vì sao thiết kế giao diện cần được xem là một phần của chất lượng hệ thống. Một bố cục tốt có thể nâng cao trải nghiệm mua hàng, nhưng nếu không được tổ chức hợp lý thì phần giao diện cũng có thể trở thành nguồn gây khó bảo trì khi dự án mở rộng.

## 2.33. Ưu điểm và hạn chế của JavaScript trong phát triển tính năng động

Ưu điểm quan trọng nhất của JavaScript là khả năng tạo tương tác động ngay tại phía client. Người dùng có thể gõ vào ô tìm kiếm và nhận gợi ý gần như tức thời, thêm sản phẩm vào giỏ hàng mà không cần tải lại toàn bộ trang, thay đổi thuộc tính sản phẩm và xem phản hồi giao diện ngay lập tức. Điều này làm tăng đáng kể chất lượng trải nghiệm người dùng trong website thương mại điện tử, nơi tốc độ phản hồi và cảm giác mượt khi thao tác có ảnh hưởng trực tiếp đến quyết định mua hàng.

JavaScript cũng rất linh hoạt khi kết hợp với AJAX để giao tiếp bất đồng bộ với server. Nhờ đó, nhiều phần của hệ thống có thể được cập nhật theo vùng thay vì phải render lại cả trang. Đây là một ưu điểm lớn trong các tính năng như quick cart, search suggest, load comment, tính phí vận chuyển hoặc chatbot.

Tuy nhiên, JavaScript có hạn chế ở chỗ dễ bị lạm dụng nếu đưa quá nhiều logic nghiệp vụ sang phía client. Trong các hệ thống thương mại điện tử, những thông tin quan trọng như giá bán cuối cùng, coupon hợp lệ, trạng thái đơn hàng hoặc quyền truy cập không thể chỉ dựa vào JavaScript mà phải được xác nhận lại ở phía server. Ngoài ra, nếu tổ chức mã phía client thiếu kỷ luật, các đoạn script nhỏ lẻ cũng có thể gây khó bảo trì về sau.

Do đó, trong cơ sở lý thuyết cần nhìn JavaScript như một công nghệ tăng cường tương tác chứ không phải nơi thay thế hoàn toàn backend. Nhận định này rất phù hợp với mô hình của đề tài, nơi JavaScript được dùng để hỗ trợ trải nghiệm động nhưng nghiệp vụ cốt lõi vẫn nằm ở server.

## 2.34. Ưu điểm và hạn chế của PHP trong phát triển backend

PHP có ưu điểm lớn ở tính thực dụng và mức độ phù hợp với môi trường phát triển web. Ngôn ngữ này hỗ trợ tốt xử lý form, session, cookie, truy cập cơ sở dữ liệu, upload file, gửi mail và phản hồi HTTP, tức là hầu hết các tác vụ nền tảng cần có trong một website thương mại điện tử. PHP cũng có cộng đồng lớn, tài liệu phong phú và hệ sinh thái công cụ trưởng thành, giúp việc xây dựng hệ thống trở nên khả thi trong phạm vi đồ án mà vẫn có chiều sâu kỹ thuật.

Một ưu điểm nữa của PHP là khả năng kết hợp tốt với các framework mạnh như Laravel. Nhờ đó, PHP không chỉ phù hợp với các website nhỏ mà còn có thể là nền tảng cho các dự án có kiến trúc rõ ràng hơn, có service layer, API, phân quyền và kiểm thử tự động. Đối với hệ thống đang làm, đây là lợi thế quan trọng vì dự án không chỉ là web bán hàng đơn giản mà còn có admin, API, report và các công cụ vận hành.

Tuy nhiên, PHP cũng có hạn chế nếu bị sử dụng theo lối viết mã thủ công, không có chuẩn tổ chức. Nếu không có framework hoặc nguyên tắc kiến trúc, mã PHP rất dễ bị dồn logic vào controller hoặc trộn lẫn phần xử lý với phần hiển thị. Điều này làm hệ thống khó mở rộng và dễ phát sinh lỗi khi thêm chức năng mới. Do đó, đưa phần hạn chế này vào báo cáo sẽ giúp làm rõ vì sao việc dùng PHP cần đi cùng một kiến trúc hợp lý chứ không chỉ đơn giản là chọn ngôn ngữ.

## 2.35. Ưu điểm và hạn chế của Laravel trong phát triển hệ thống thương mại điện tử

Laravel có ưu điểm rất rõ ở khả năng chuẩn hóa cách phát triển ứng dụng web trên nền PHP. Framework này cung cấp sẵn routing, middleware, request validation, Blade, Eloquent ORM, queue, mail, testing và nhiều công cụ khác, giúp rút ngắn thời gian xây dựng các thành phần nền tảng. Đối với website thương mại điện tử, đây là lợi thế lớn vì hệ thống thường phải triển khai đồng thời nhiều lớp như storefront, admin, API, xác thực, phân quyền, dữ liệu và kiểm thử.

Một ưu điểm khác của Laravel là khả năng mở rộng theo hướng phân tầng. Dù bản thân framework thường được biết đến qua mô hình MVC, nhưng nó vẫn cho phép tổ chức thêm service layer, support classes, repository và domain objects khá thuận lợi. Điều này rất phù hợp với dự án hiện tại, vì repo không dừng ở mức controller gọi model trực tiếp mà đã có xu hướng chuẩn hóa sâu hơn.

Tuy nhiên, Laravel cũng có hạn chế cần nhìn nhận thẳng. Nếu lạm dụng cơ chế tiện lợi sẵn có của framework mà không kiểm soát kiến trúc, dự án vẫn có thể trở nên cồng kềnh, controller phình to hoặc logic bị phân tán không nhất quán. Ngoài ra, với các hệ thống đang chuyển đổi từ legacy, việc đưa Laravel vào không tự động giải quyết toàn bộ vấn đề; vẫn cần chiến lược refactor và kiểm thử phù hợp. Vì vậy, trong báo cáo, Laravel nên được nhìn như một nền tảng mạnh, nhưng hiệu quả của nó vẫn phụ thuộc vào cách tổ chức mã nguồn và quyết định thiết kế của người phát triển.

## 2.36. Vai trò của HTML, CSS, JavaScript, PHP và Laravel trong riêng dự án đang thực hiện

Khi gắn trực tiếp vào dự án hiện tại, HTML giữ vai trò tạo cấu trúc cho các trang storefront và admin được render bằng Blade. Các thành phần như trang chủ, trang danh mục, chi tiết sản phẩm, giỏ hàng, checkout, dashboard quản trị hay các form cấu hình nội dung đều dựa trên lớp cấu trúc này. Nhờ có HTML được tổ chức tương đối rõ, dữ liệu từ controller và service có thể được đưa ra giao diện một cách nhất quán hơn.

CSS trong dự án đóng vai trò tổ chức trải nghiệm nhìn và luồng đọc thông tin. Điều này thể hiện ở cách trình bày card sản phẩm, block flash sale, khu vực tìm kiếm, giao diện checkout, cũng như shell của khu vực admin. Về mặt thực tiễn, CSS giúp các màn hình có tính thống nhất và hỗ trợ tốt hơn cho trải nghiệm sử dụng, nhất là khi người dùng cần đọc nhiều thông số của thiết bị công nghệ trước khi đưa ra quyết định mua hàng.

JavaScript trong dự án được sử dụng như lớp tăng cường tương tác, đặc biệt ở các luồng search suggest, add-cart-ajax, quick cart, hover cart, tính phí vận chuyển, load comment và một số vùng tương tác động khác. Điều này phù hợp với mô hình mà dự án đang dùng: backend vẫn là trung tâm xử lý, còn JavaScript chịu trách nhiệm làm cho giao diện phản hồi nhanh hơn ở các điểm cần thiết.

PHP là lớp ngôn ngữ nền cho toàn bộ xử lý phía server của dự án. Các thao tác như nhận request, kiểm tra dữ liệu, duy trì session khách hàng, xử lý checkout, quản lý đơn hàng, phân quyền admin, xây API và chạy console command đều dựa trên PHP. Nói cách khác, PHP là lớp thực thi mà nếu bỏ đi thì toàn bộ hệ thống sẽ không còn khả năng vận hành nghiệp vụ.

Laravel là bộ khung tổ chức tất cả các thành phần trên thành một ứng dụng thống nhất. Trong dự án này, Laravel không chỉ làm nhiệm vụ định tuyến mà còn cung cấp middleware, request validation, Blade, Eloquent, Sanctum, permission package, console command và nền tảng cho feature test. Nhờ đó, dự án có thể phát triển theo hướng nhiều lớp hơn thay vì dồn toàn bộ xử lý vào một số controller như mô hình CRUD đơn giản.

Nhìn tổng thể, từng công nghệ có một vai trò khác nhau nhưng bổ sung cho nhau trong cùng một kiến trúc. HTML và CSS lo cấu trúc cùng trình bày, JavaScript tăng cường tương tác, PHP xử lý nghiệp vụ phía server và Laravel cung cấp bộ khung chuẩn hóa. Việc làm rõ vai trò này ngay trong Chương 2 giúp phần cơ sở lý thuyết bám sát dự án hơn, đồng thời tạo cầu nối thuận lợi sang Chương 4, nơi các công nghệ đó được nhìn dưới góc độ triển khai thực tế.

## 2.37. Kết luận chương

Chương cơ sở lý thuyết đã trình bày các nền tảng quan trọng phục vụ cho việc phân tích, thiết kế và triển khai hệ thống website thương mại điện tử trên Laravel 11. Ngoài các nội dung cốt lõi như thương mại điện tử, kiến trúc web, MVC, kiến trúc phân lớp, Laravel 11, cơ sở dữ liệu quan hệ, ORM, service layer, REST API, xác thực, phân quyền, bảo mật, AJAX, UML, ERD và kiểm thử phần mềm, chương này còn mở rộng thêm các nền tảng liên quan tới HTML, CSS, JavaScript, PHP, hệ sinh thái Laravel, hình ảnh, media, khách vãng lai và các website tham chiếu trong cùng lĩnh vực.

Việc bổ sung các nội dung này giúp chương cơ sở lý thuyết cân bằng hơn giữa ngôn ngữ nền, framework, frontend, backend, dữ liệu, trải nghiệm người dùng và hành vi mua sắm trực tuyến. Nhờ đó, Chương 2 có thể đạt độ dày tốt hơn theo đúng form đồ án tốt nghiệp mà vẫn không lạc đề, vì mọi phần lý thuyết đều được neo lại với bài toán website thương mại điện tử bán thiết bị công nghệ và với kiến trúc thực tế của dự án đang thực hiện.

---

# CHƯƠNG 3. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG

## 3.1. Mô tả bài toán

Đề tài tập trung xây dựng một website thương mại điện tử bán thiết bị công nghệ trên nền tảng Laravel 11, phục vụ hai nhóm người dùng chính là khách hàng và quản trị viên. Hệ thống hỗ trợ quá trình tra cứu thông tin, tìm kiếm, lựa chọn sản phẩm, mua sắm trực tuyến, đồng thời cung cấp khu vực quản trị để vận hành dữ liệu sản phẩm, nội dung và đơn hàng.

Điểm đặc thù của dự án là hệ thống không được xây dựng hoàn toàn theo mô hình greenfield ngay từ đầu, mà đang trong trạng thái hiện đại hóa dần từ một nền legacy. Vì vậy, trong quá trình phân tích và thiết kế, cần ghi nhận đồng thời hai lớp kiến trúc dữ liệu: lớp dữ liệu legacy đang phục vụ nhiều luồng storefront và admin hiện tại, và lớp schema chuẩn hóa mới được xây dựng qua migrations để phục vụ quá trình refactor, mở rộng API và chuẩn hóa nghiệp vụ.

Do đó, việc phân tích hệ thống không chỉ dừng ở mức mô hình thương mại điện tử tổng quát, mà cần bám sát thực tế triển khai của mã nguồn, bao gồm các lớp controller legacy, các service nghiệp vụ mới, các route web và API, cũng như hệ thống kiểm thử đang tồn tại trong repo.

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
- có kiểm thử tự động cho các luồng quan trọng

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

## 3.2.4. Quy trình phân tích hệ thống

Quá trình phân tích hệ thống của đề tài được thực hiện theo hướng bám repo thay vì mô tả lý thuyết thuần túy. Trước hết, dự án được khảo sát từ ba mặt triển khai chính là storefront web, admin web và REST API. Song song với đó, lớp dữ liệu được nhận diện ở cả hai chiều: lớp bảng legacy đang phục vụ nhiều luồng vận hành hiện tại và lớp schema chuẩn hóa mới được hình thành qua migrations.

Sau bước khảo sát, yêu cầu hệ thống được đối chiếu theo từng nhóm người dùng và từng nhóm nghiệp vụ thật đang tồn tại trong route, controller, service, migration và test suite. Cách tiếp cận này giúp phần phân tích không bị tách rời khỏi mã nguồn, đồng thời tránh mô tả hệ thống như một mô hình lý tưởng không còn ăn khớp với repo.

Tiếp theo, hệ thống được mô hình hóa bằng use case, activity, sequence, ERD và kiến trúc phân tầng. Điểm quan trọng là các sơ đồ không chỉ bám chức năng ở mức tổng quát mà còn phải gắn với các route và luồng thật như POST /add-cart-ajax, GET /checkout, POST /login-customer, POST /update-product/{product_id}.

Cuối cùng, từ kết quả khảo sát và mô hình hóa, phần thiết kế được hoàn thiện theo ba lớp: thiết kế kiến trúc tổng thể, thiết kế dữ liệu hybrid và thiết kế luồng nghiệp vụ. Nhờ vậy, Chương 3 phản ánh đúng đặc thù của một dự án đang hiện đại hóa dần từ nền legacy sang cấu trúc chuẩn hóa hơn.

## 3.3. Tác nhân của hệ thống

Hệ thống nên được mô tả theo ba tác nhân chính thay vì chỉ chia thành khách hàng và quản trị viên như các website bán hàng thông thường.

### 3.3.1. Khách vãng lai

Khách vãng lai là người dùng truy cập website nhưng chưa đăng nhập và chưa thiết lập quan hệ tài khoản với hệ thống. Tác nhân này có thể xem trang chủ, danh mục, thương hiệu, chi tiết sản phẩm, tìm kiếm, nhận gợi ý tìm kiếm, đọc thông tin kỹ thuật, so sánh sản phẩm và đi tới màn hình đăng ký hoặc đăng nhập khi cần.

### 3.3.2. Khách hàng

Khách hàng là người dùng đã có tài khoản hoặc đã xác thực thành công trong hệ thống. Ngoài các khả năng giống khách vãng lai, tác nhân này còn có thể thao tác với giỏ hàng, áp dụng coupon, đi qua checkout, theo dõi lịch sử đơn hàng, hủy đơn hàng trong điều kiện cho phép, gửi bình luận, gửi đánh giá và tham gia các luồng hỗ trợ có lưu trạng thái người dùng.

### 3.3.3. Quản trị viên

Quản trị viên là người dùng khu vực admin, chịu trách nhiệm quản lý catalog, nội dung, media, đơn hàng, coupon, delivery, người dùng, vai trò, quyền hạn, báo cáo và các công cụ vận hành nội bộ như chat inbox, quick search, profile và settings.

## 3.4. Mô tả các sơ đồ UML và mô tả dưới hình

### Hình 3.1. Biểu đồ use case tổng quát của hệ thống

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Biểu đồ use case tổng quát đã được chỉnh lại theo hướng chỉ giữ ba tác nhân chính của hệ thống: khách vãng lai, khách hàng và quản trị viên. Cách tách này phản ánh đúng thực tế vận hành của website hơn vì người truy cập chưa đăng nhập vẫn có thể duyệt sản phẩm và tìm kiếm, còn các thao tác nhạy cảm hơn như checkout, xem lịch sử đơn hàng hay quản trị hệ thống được phân cho các tác nhân tương ứng.

### Hình 3.2. Biểu đồ use case phần khách vãng lai

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Biểu đồ use case phần khách vãng lai nhấn mạnh rằng website không chỉ phục vụ người dùng đã đăng nhập. Một người truy cập chưa có tài khoản vẫn có thể vào trang chủ, duyệt sản phẩm, tìm kiếm, đọc thông tin, so sánh và sau đó mới quyết định chuyển sang bước xác thực. Đây là lý do tác nhân khách vãng lai cần được tách riêng trong báo cáo.

### Hình 3.3. Biểu đồ use case phần khách hàng

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Biểu đồ use case phần khách hàng gom các thao tác cần xác thực, đặc biệt là checkout, lịch sử đơn hàng và các tương tác có tính cá nhân. Cách tách này giúp báo cáo thể hiện rõ ranh giới giữa phần dùng công khai và phần dùng sau đăng nhập.

### Hình 3.4. Biểu đồ use case phần quản trị viên

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Biểu đồ use case phần quản trị viên cho thấy khu vực admin là một phân hệ đầy đủ chứ không chỉ là nơi thêm hoặc sửa sản phẩm. Ngoài catalog, quản trị viên còn phải xử lý nội dung, đơn hàng, phân quyền, báo cáo và các công cụ vận hành nội bộ.

### Hình 3.5. Sơ đồ hoạt động chức năng thêm sản phẩm vào giỏ hàng

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Sơ đồ hoạt động thêm giỏ hàng mô tả quá trình khách hàng chọn sản phẩm, hệ thống kiểm tra trạng thái sản phẩm, tồn kho, số lượng yêu cầu và sau đó cập nhật dữ liệu giỏ hàng theo session hoặc dịch vụ tương ứng. Luồng này bám sát các thao tác AJAX như `add-cart-ajax`, `update-cart-quantity`, `update-quick-cart`.

### Hình 3.6. Sơ đồ hoạt động chức năng đăng ký, đăng nhập khách hàng

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Sơ đồ hoạt động đăng ký, đăng nhập khách hàng mô tả quá trình xác thực tài khoản từ giao diện checkout/storefront, bao gồm kiểm tra dữ liệu đầu vào, tạo session khách hàng và điều hướng trở lại đúng trang trước đó khi người dùng đang ở trong luồng mua hàng hoặc checkout.

### Hình 3.7. Sơ đồ hoạt động chức năng đặt hàng

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Sơ đồ hoạt động đặt hàng mô tả quy trình từ giỏ hàng đến checkout, trong đó khách hàng phải đăng nhập trước khi xác nhận đơn hàng. Hệ thống tiếp nhận thông tin giao hàng, tính phí vận chuyển, chọn phương thức thanh toán và tạo đơn hàng cùng chi tiết đơn hàng.

### Hình 3.8. Sơ đồ hoạt động chức năng quản lý sản phẩm

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Sơ đồ hoạt động quản lý sản phẩm mô tả thao tác của quản trị viên khi thêm mới hoặc cập nhật dữ liệu sản phẩm. Hệ thống kiểm tra dữ liệu đầu vào, lưu dữ liệu qua controller và service, đồng thời xử lý các thành phần liên quan như gallery, attribute, document và cache liên quan.

### Hình 3.9. Sơ đồ quan hệ thực thể ERD của hệ thống

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
ERD của hệ thống phản ánh trạng thái dữ liệu hybrid của dự án. Một mặt, hệ thống vẫn còn các bảng legacy như `tbl_product`, `tbl_customers`, `tbl_order`, `tbl_coupon`, `tbl_category_product`. Mặt khác, lớp migrations mới đã bổ sung schema chuẩn hóa gồm `products`, `product_categories`, `product_variants`, `product_media`, `customers`, `carts`, `cart_items`, `orders`, `order_items`, `payments`, `chat_conversations`, `chat_messages`, `settings` và các bảng analytics, marketing, loyalty. Điều này cho thấy dự án đang trong giai đoạn hiện đại hóa dần từ nền dữ liệu cũ sang kiến trúc mới.

### Hình 3.10. Kiến trúc phân tầng của dự án

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Kiến trúc phân tầng của dự án bao gồm tầng giao diện Blade, tầng controller HTTP, tầng request validation, tầng service nghiệp vụ trong `app/Application`, tầng domain và repository trong `app/Domain` và `app/Infrastructure/Persistence`, cùng với tầng dữ liệu legacy và schema chuẩn hóa mới. Đây là điểm quan trọng làm nên tính đặc thù của repo hiện tại.

### Hình 3.11. Biểu đồ use case module tìm kiếm và khám phá sản phẩm

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Module tìm kiếm và khám phá sản phẩm là vùng dùng công khai của website nên cả khách vãng lai và khách hàng đều có thể truy cập. Biểu đồ này bám các route như `/tim-kiem`, `/autocomplete-ajax`, `/goi-y-san-pham`, `/danh-muc/{slug_category_product}`, `/thuong-hieu/{brandSlug}` và `/tag/{product_tag}`.

### Hình 3.12. Biểu đồ use case module chi tiết sản phẩm và tương tác

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Biểu đồ này tách rõ hai lớp thao tác: khách vãng lai chỉ đọc và xem thông tin, trong khi khách hàng đã xác thực có thể gửi bình luận và đánh giá. Cách phân tách này bám tốt hơn với các route như `/chi-tiet/{product_slug}`, `/quickview`, `/load-comment`, `/send-comment`, `/insert-rating`.

### Hình 3.13. Biểu đồ use case module so sánh sản phẩm

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
So sánh sản phẩm là nghiệp vụ phù hợp với cả khách vãng lai lẫn khách hàng vì đây là bước tiền quyết định mua. Biểu đồ bám module và route `/so-sanh` cùng `CompareController`.

### Hình 3.14. Biểu đồ use case module giỏ hàng và coupon

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Biểu đồ này thể hiện rõ rằng giỏ hàng có thể bắt đầu từ khách vãng lai, nhưng các thao tác sâu hơn như coupon thường gắn chặt hơn với bước mua hàng của khách đã xác thực. Các route chính liên quan là `/add-cart-ajax`, `/update-cart-quantity`, `/update-quick-cart`, `/gio-hang`, `/show_quick_cart`, `/hover-cart`, `/check-coupon`.

### Hình 3.15. Biểu đồ use case module tài khoản khách hàng

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Module tài khoản là nơi ranh giới giữa khách vãng lai và khách hàng thể hiện rõ nhất. Khách vãng lai khởi tạo đăng ký hoặc đăng nhập, còn khách hàng sau xác thực tiếp tục với hồ sơ và đăng xuất. Biểu đồ này bám các route như `/add-customer`, `/login-customer`, `/dang-nhap`, `/dang-xuat-khach-hang`, `/quen-mat-khau`, `/recover-pass`, `/reset-new-pass`.

### Hình 3.16. Biểu đồ use case module checkout, thanh toán và đơn hàng

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Do hệ thống thực tế yêu cầu đăng nhập trước checkout, module này chỉ còn gắn với tác nhân khách hàng. Các route tiêu biểu là `/checkout`, `/payment`, `/calculate-fee`, `/select-delivery-home`, `/confirm-order`, `/history`, `/huy-don-hang`.

### Hình 3.17. Biểu đồ use case module chat hỗ trợ khách hàng

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Trong bản chỉnh này, tác nhân hỗ trợ ngoài hệ thống đã bị loại bỏ; thay vào đó, sơ đồ chỉ giữ khách vãng lai, khách hàng và quản trị viên. Cách biểu diễn này bám hơn với góc nhìn phân tích hệ thống và các route `/chatbot/conversation`, `/chatbot/messages`, `/chat-inbox`, `/chat-inbox/conversations/{conversation}`.

### Hình 3.18. Biểu đồ use case module xác thực và workspace quản trị

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Biểu đồ này giữ nguyên bản chất của module workspace admin nhưng làm gọn actor còn đúng một tác nhân là quản trị viên. Các route tiêu biểu gồm `/admin`, `/auth-login`, `/dashboard`, `/admin/quick-search`, `/admin/profile`, `/admin/settings`, `/logout-auth`.

### Hình 3.19. Biểu đồ use case module quản lý catalog

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Module quản lý catalog bám các route `catalog/categories`, `catalog/brands`, `/add-product`, `/all-product`, `/update-product/{product_id}`, `/product-attribute`, `/add-gallery/{product_id}`, `/delete-document`, `/comment`. Đây là nhóm chức năng lớn nhất trong khu vực quản trị.

### Hình 3.20. Biểu đồ use case module quản lý nội dung

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Biểu đồ use case module quản lý nội dung tiếp tục chỉ dùng một actor lõi là quản trị viên. Nhóm route tiêu biểu gồm `/add-category-post`, `/all-category-post`, `/add-post`, `/all-post`, `/video`, `/manage-slider`, `/information`, `/mail-preview`.

### Hình 3.21. Biểu đồ use case module vận hành đơn hàng và khuyến mãi

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Biểu đồ này mô tả phần vận hành hậu bán hàng trong admin, bám các route `/manage-order`, `/print-order/{checkout_code}`, `/view-order/{order_code}`, `/update-order-qty`, `/delivery`, `/insert-coupon`, `/send-coupon`, `/send-coupon-vip`.

### Hình 3.22. Biểu đồ use case module người dùng, phân quyền và báo cáo

[Chèn hình tương ứng, xuất từ $source.]

**Mô tả dưới hình:**  
Tác nhân `Admin API Client` đã được loại bỏ trong bản chỉnh này để sơ đồ tập trung vào góc nhìn use case của người vận hành hệ thống. Module vẫn bám các route `users`, `/assign-role/{admin_id}`, `/assign-permission/{admin_id}`, `/index-spatie`, `/product-profit`, `/product-discovery-report`, `/api/v1/admin/statistics`.

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

### 3.6.5. Bảng mapping giữa dữ liệu legacy và lớp schema mới

Bảng dưới đây mô tả một số ánh xạ quan trọng giữa lớp dữ liệu cũ và lớp dữ liệu mới trong repo.

| Nhóm nghiệp vụ | Bảng legacy | Schema mới | Ghi chú |
|---|---|---|---|
| Thương hiệu | 	bl_brand | rands | Ánh xạ tương đối trực tiếp qua tên, slug, trạng thái |
| Danh mục sản phẩm | 	bl_category_product | product_categories | Có thêm quan hệ cha con qua parent_id |
| Sản phẩm | 	bl_product | products | Schema mới tách rõ giá, tồn kho, category chính, brand |
| Thuộc tính sản phẩm | 	bl_product_attribute | product_variants, ttributes, ttribute_values | Không phải mapping 1:1 tuyệt đối, là hướng chuẩn hóa sâu hơn |
| Khách hàng | 	bl_customers | customers | Có bổ sung meta, trạng thái hoạt động và dữ liệu chuẩn hóa hơn |
| Địa chỉ giao hàng | 	bl_shipping | customer_addresses, order_shipping | Một bản ghi shipping cũ được tách thành dữ liệu địa chỉ và giao hàng |
| Đơn hàng | 	bl_order | orders | Schema mới tách rõ trạng thái đơn, thanh toán và fulfillment |
| Chi tiết đơn hàng | 	bl_order_details | order_items | Chuẩn hóa từng dòng sản phẩm trong đơn |
| Thanh toán | 	bl_payment | payments, payment_transactions | Dữ liệu thanh toán được tách thành payment chính và lịch sử giao dịch |
| Mã giảm giá | 	bl_coupon | coupons, coupon_redemptions | Schema mới hỗ trợ cả thông tin coupon và lượt sử dụng |
| Bình luận, đánh giá | 	bl_comment, 	bl_rating | product_reviews | Dữ liệu comment và rating được gom về một mô hình review chuẩn hơn |
| Danh mục bài viết | 	bl_category_post | log_categories | Phục vụ chuẩn hóa CMS |
| Bài viết | 	bl_posts | log_posts | Chuẩn hóa nội dung blog, slug và metadata |

Việc bổ sung bảng mapping này giúp phần phân tích dữ liệu bám repo thật hơn, đồng thời làm rõ chiến lược hiện đại hóa dần từ lớp dữ liệu legacy sang lớp schema chuẩn hóa mới.

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

### 3.7.8. Luồng xử lý Request -> Controller -> Service -> Persistence -> Database/View/API

Kiến trúc tổng thể của hệ thống được tổ chức theo hướng phân tầng. Khi người dùng gửi request, route trước tiên xác định controller tiếp nhận luồng xử lý. Trong nhiều trường hợp, request còn đi qua middleware để kiểm tra xác thực, phân quyền hoặc trạng thái phiên làm việc.

Tại controller, request được điều phối và có thể đi qua lớp request validation hoặc support classes để kiểm tra dữ liệu đầu vào, chuẩn hóa thông tin điều hướng hoặc gom các quy tắc dùng chung. Sau đó, controller chuyển phần nghiệp vụ chính sang service layer trong pp/Application.

Từ service layer, hệ thống tiếp tục tương tác với tầng persistence, repository hoặc model để truy vấn và cập nhật dữ liệu trong cơ sở dữ liệu. Kết quả cuối cùng được trả ngược lại cho controller, từ đó controller render view Blade nếu là web hoặc trả JSON response nếu là API. Cách tổ chức này giúp controller giữ vai trò điều phối, service giữ vai trò nghiệp vụ và persistence giữ vai trò dữ liệu, từ đó tăng khả năng bảo trì và mở rộng của toàn hệ thống.

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
