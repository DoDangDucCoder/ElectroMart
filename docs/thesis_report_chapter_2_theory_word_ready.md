# BẢN RIÊNG COPY-PASTE READY CHO CHƯƠNG 2

File này được tách riêng từ bản master để anh có thể chèn trực tiếp vào Word cho tiện. Nội dung đã gộp theo đúng các yêu cầu bổ sung gần nhất:

- kéo dày phần cơ sở lý thuyết theo form đồ án tốt nghiệp
- bổ sung và mở rộng các nội dung về HTML, CSS, JavaScript, PHP, Laravel
- thêm định nghĩa, vai trò, ưu điểm, hạn chế và liên hệ với chính dự án đang làm
- bỏ phần so sánh theo yêu cầu cuối để bản này gọn và dễ dán vào Word hơn

Lưu ý khi dùng file này:

- đây là bản chỉ chứa riêng Chương 2
- numbering mục đã được làm sạch lại sau khi bỏ phần so sánh
- nội dung ưu tiên theo hướng học thuật, không đi quá sâu vào mô tả triển khai ở Chương 4

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

Nếu chỉ sử dụng PHP thuần, người phát triển vẫn có thể xây dựng website, nhưng khi hệ thống lớn dần thì mã nguồn rất dễ bị phân tán, lặp logic và khó bảo trì. Các bài toán như routing, validation, authentication, authorization, template rendering, database abstraction và error handling nếu tự tổ chức hoàn toàn từ đầu sẽ tốn nhiều thời gian, đồng thời dễ thiếu nhất quán giữa các module. Chính vì vậy, khái niệm framework trở thành một phần quan trọng trong phát triển ứng dụng web bằng PHP.

Về mặt lý thuyết, framework có thể được hiểu là một bộ khung tổ chức phát triển phần mềm, cung cấp sẵn cấu trúc thư mục, quy tắc làm việc và các thành phần kỹ thuật phổ biến. Framework không thay thế tư duy thiết kế hệ thống, nhưng giúp chuẩn hóa cách cài đặt các thành phần lặp lại. Nhờ đó, người phát triển có thể tập trung nhiều hơn vào bài toán nghiệp vụ đặc thù như catalog, giỏ hàng, checkout, báo cáo hoặc phân quyền, thay vì tốn quá nhiều công sức cho hạ tầng cơ bản.

Đối với website thương mại điện tử, giá trị của framework còn nằm ở việc giảm rủi ro phát sinh từ sự thiếu đồng nhất. Khi hệ thống có nhiều route, nhiều form nhập liệu và nhiều luồng phân quyền khác nhau, việc sử dụng một framework giúp các module bám theo cùng một nguyên tắc xử lý request, validate dữ liệu, truy cập database và trả response. Điều này đặc biệt quan trọng với các dự án có phạm vi tương đối rộng, vừa có storefront, vừa có admin, vừa có REST API như đề tài hiện tại.

Nhìn từ góc độ báo cáo đồ án, đưa phần lý thuyết về framework vào Chương 2 giúp làm rõ rằng việc chọn Laravel không phải là một lựa chọn ngẫu nhiên theo xu hướng, mà là một quyết định có cơ sở kỹ thuật. Ngôn ngữ PHP cung cấp năng lực xử lý phía server, còn framework cung cấp cơ chế tổ chức, chuẩn hóa và tăng khả năng mở rộng cho hệ thống. Sự phân biệt này giúp chương cơ sở lý thuyết chặt hơn và tránh cách trình bày đơn giản hóa rằng chỉ cần “dùng Laravel là đủ”.

## 2.24. Hệ sinh thái Laravel trong phát triển website thương mại điện tử

Ở mục trước, Laravel đã được giới thiệu như một framework hiện đại cho PHP. Tuy nhiên, để chương cơ sở lý thuyết đủ chiều sâu, cần nhìn Laravel không chỉ như một công cụ tạo route hay CRUD, mà như một hệ sinh thái hỗ trợ nhiều mặt của ứng dụng web. Trong phát triển website thương mại điện tử, hệ sinh thái này có ý nghĩa lớn vì hệ thống không chỉ cần hiển thị sản phẩm mà còn cần quản lý xác thực, phiên làm việc, phân quyền, dữ liệu quan hệ, API, queue, mail và console command.



Về mặt học thuật, bổ sung nội dung này giúp chương cơ sở lý thuyết tránh bị ngắn ở phần Laravel. Thay vì chỉ dừng ở định nghĩa và vài ưu điểm tổng quát, báo cáo có thể trình bày Laravel như một nền tảng phát triển toàn diện cho website thương mại điện tử. Cách tiếp cận đó phù hợp hơn với chính hiện trạng của dự án, vì repo thực tế cũng đang sử dụng Laravel không chỉ để dựng web page mà còn để xây API, phân quyền, console command và kiểm tra chất lượng.

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


## 2.29. Ý nghĩa của nhóm công nghệ HTML, CSS, JavaScript, PHP, Laravel và hình ảnh đối với đề tài

Nếu nhìn tổng thể, HTML, CSS, JavaScript, PHP, Laravel và hình ảnh không phải là các mảnh rời rạc, mà là các thành phần cùng tạo nên hệ thống web hoàn chỉnh. HTML tổ chức nội dung, CSS tổ chức cách trình bày, JavaScript tổ chức hành vi tương tác, PHP đảm nhiệm xử lý phía server, Laravel cung cấp bộ khung tổ chức toàn bộ ứng dụng, còn hình ảnh tổ chức phần thông tin trực quan. Khi các yếu tố này phối hợp tốt, hệ thống mới có thể đáp ứng đồng thời yêu cầu kỹ thuật, yêu cầu trải nghiệm người dùng và yêu cầu trình bày sản phẩm.

Trong đề tài này, nhóm công nghệ đó đặc biệt phù hợp với mô hình Laravel server-rendered kết hợp AJAX. Blade giúp sinh HTML phía server, CSS chịu trách nhiệm dựng layout và style, JavaScript đảm nhiệm các tương tác động cục bộ như quick cart, search suggest hay chatbot, PHP và Laravel xử lý request, validation, session, service, persistence và response, còn hệ thống media chịu trách nhiệm hiển thị ảnh và video sản phẩm. Đây là một kiến trúc phù hợp đối với một website bán thiết bị công nghệ quy mô đồ án nhưng đã có nhiều module thực tế.

Vì vậy, bổ sung nhóm nội dung này vào Chương 2 sẽ giúp cơ sở lý thuyết cân bằng hơn giữa frontend, backend, framework, dữ liệu và trải nghiệm người dùng. Báo cáo sẽ phản ánh rõ hơn rằng website thương mại điện tử là một hệ thống tích hợp nhiều lớp, trong đó ngôn ngữ nền, framework, giao diện, hình ảnh và hành vi người dùng đều có vai trò trong sự thành công của sản phẩm.

## 2.30. Ưu điểm và hạn chế của HTML trong xây dựng website thương mại điện tử

Ưu điểm lớn nhất của HTML là tính chuẩn hóa và phổ quát. Hầu như mọi trình duyệt hiện đại đều hỗ trợ HTML, nhờ đó nội dung có thể được hiển thị ổn định trên nhiều thiết bị và môi trường khác nhau. HTML cũng rất phù hợp để biểu diễn những thành phần cốt lõi của website thương mại điện tử như tiêu đề sản phẩm, mô tả, bảng thông số kỹ thuật, form đăng nhập, form checkout, danh sách danh mục và các vùng điều hướng. Một ưu điểm khác là HTML hỗ trợ semantic markup, giúp cải thiện khả năng đọc hiểu của công cụ tìm kiếm và các công cụ hỗ trợ truy cập.

Tuy nhiên, HTML có hạn chế rõ ràng là chỉ đảm nhiệm vai trò mô tả cấu trúc, không thể tự xử lý nghiệp vụ hay hành vi động. Nếu chỉ có HTML, website sẽ dừng lại ở mức hiển thị nội dung mà không có khả năng phản hồi linh hoạt với thao tác người dùng hoặc xử lý quy trình nghiệp vụ phức tạp. Trong thương mại điện tử, hạn chế này buộc HTML phải luôn đi cùng CSS, JavaScript và lớp xử lý phía server.

Từ góc độ đồ án, việc nêu cả ưu điểm và hạn chế của HTML giúp báo cáo tránh cách trình bày một chiều. Nó cho thấy HTML là nền tảng bắt buộc của giao diện web, nhưng chỉ phát huy đúng vai trò khi được đặt trong một kiến trúc tổng thể có sự hỗ trợ của các công nghệ khác.

## 2.31. Ưu điểm và hạn chế của CSS trong tổ chức giao diện

CSS có ưu điểm nổi bật ở khả năng tách phần trình bày khỏi phần nội dung. Nhờ đó, giao diện có thể được quản lý tập trung, thay đổi theme hoặc điều chỉnh bố cục mà không phải viết lại cấu trúc HTML. Đối với website thương mại điện tử, điều này đặc biệt có giá trị vì hệ thống thường có nhiều màn hình dùng lại các thành phần như card sản phẩm, banner, menu, form, bảng giá, nút hành động và các khối nội dung phụ trợ. CSS cũng hỗ trợ responsive design rất tốt, giúp website thích nghi trên desktop, tablet và mobile.

Tuy nhiên, CSS cũng có những hạn chế. Khi dự án lớn dần, việc quản lý nhiều selector, nhiều trạng thái hiển thị và nhiều lớp giao diện có thể trở nên phức tạp nếu không có nguyên tắc tổ chức rõ ràng. Ngoài ra, CSS chỉ chịu trách nhiệm trình bày nên không thể giải quyết các vấn đề liên quan tới dữ liệu, logic nghiệp vụ hay xác thực trạng thái người dùng. Vì vậy, CSS là thành phần rất quan trọng của trải nghiệm người dùng, nhưng không thể tách rời khỏi HTML, JavaScript và backend.

Trong phạm vi đề tài, việc nhấn mạnh ưu điểm và hạn chế của CSS giúp làm rõ vì sao thiết kế giao diện cần được xem là một phần của chất lượng hệ thống. Một bố cục tốt có thể nâng cao trải nghiệm mua hàng, nhưng nếu không được tổ chức hợp lý thì phần giao diện cũng có thể trở thành nguồn gây khó bảo trì khi dự án mở rộng.

## 2.32. Ưu điểm và hạn chế của JavaScript trong phát triển tính năng động

Ưu điểm quan trọng nhất của JavaScript là khả năng tạo tương tác động ngay tại phía client. Người dùng có thể gõ vào ô tìm kiếm và nhận gợi ý gần như tức thời, thêm sản phẩm vào giỏ hàng mà không cần tải lại toàn bộ trang, thay đổi thuộc tính sản phẩm và xem phản hồi giao diện ngay lập tức. Điều này làm tăng đáng kể chất lượng trải nghiệm người dùng trong website thương mại điện tử, nơi tốc độ phản hồi và cảm giác mượt khi thao tác có ảnh hưởng trực tiếp đến quyết định mua hàng.

JavaScript cũng rất linh hoạt khi kết hợp với AJAX để giao tiếp bất đồng bộ với server. Nhờ đó, nhiều phần của hệ thống có thể được cập nhật theo vùng thay vì phải render lại cả trang. Đây là một ưu điểm lớn trong các tính năng như quick cart, search suggest, load comment, tính phí vận chuyển hoặc chatbot.

Tuy nhiên, JavaScript có hạn chế ở chỗ dễ bị lạm dụng nếu đưa quá nhiều logic nghiệp vụ sang phía client. Trong các hệ thống thương mại điện tử, những thông tin quan trọng như giá bán cuối cùng, coupon hợp lệ, trạng thái đơn hàng hoặc quyền truy cập không thể chỉ dựa vào JavaScript mà phải được xác nhận lại ở phía server. Ngoài ra, nếu tổ chức mã phía client thiếu kỷ luật, các đoạn script nhỏ lẻ cũng có thể gây khó bảo trì về sau.

Do đó, trong cơ sở lý thuyết cần nhìn JavaScript như một công nghệ tăng cường tương tác chứ không phải nơi thay thế hoàn toàn backend. Nhận định này rất phù hợp với mô hình của đề tài, nơi JavaScript được dùng để hỗ trợ trải nghiệm động nhưng nghiệp vụ cốt lõi vẫn nằm ở server.

## 2.33. Ưu điểm và hạn chế của PHP trong phát triển backend

PHP có ưu điểm lớn ở tính thực dụng và mức độ phù hợp với môi trường phát triển web. Ngôn ngữ này hỗ trợ tốt xử lý form, session, cookie, truy cập cơ sở dữ liệu, upload file, gửi mail và phản hồi HTTP, tức là hầu hết các tác vụ nền tảng cần có trong một website thương mại điện tử. PHP cũng có cộng đồng lớn, tài liệu phong phú và hệ sinh thái công cụ trưởng thành, giúp việc xây dựng hệ thống trở nên khả thi trong phạm vi đồ án mà vẫn có chiều sâu kỹ thuật.

Một ưu điểm nữa của PHP là khả năng kết hợp tốt với các framework mạnh như Laravel. Nhờ đó, PHP không chỉ phù hợp với các website nhỏ mà còn có thể là nền tảng cho các dự án có kiến trúc rõ ràng hơn, có service layer, API, phân quyền và kiểm tra chất lượng. Đối với hệ thống đang làm, đây là lợi thế quan trọng vì dự án không chỉ là web bán hàng đơn giản mà còn có admin, API, report và các công cụ vận hành.

Tuy nhiên, PHP cũng có hạn chế nếu bị sử dụng theo lối viết mã thủ công, không có chuẩn tổ chức. Nếu không có framework hoặc nguyên tắc kiến trúc, mã PHP rất dễ bị dồn logic vào controller hoặc trộn lẫn phần xử lý với phần hiển thị. Điều này làm hệ thống khó mở rộng và dễ phát sinh lỗi khi thêm chức năng mới. Do đó, đưa phần hạn chế này vào báo cáo sẽ giúp làm rõ vì sao việc dùng PHP cần đi cùng một kiến trúc hợp lý chứ không chỉ đơn giản là chọn ngôn ngữ.

## 2.34. Ưu điểm và hạn chế của Laravel trong phát triển hệ thống thương mại điện tử

Laravel có ưu điểm rất rõ ở khả năng chuẩn hóa cách phát triển ứng dụng web trên nền PHP. Framework này cung cấp sẵn routing, middleware, request validation, Blade, Eloquent ORM, queue, mail và nhiều công cụ khác, giúp rút ngắn thời gian xây dựng các thành phần nền tảng. Đối với website thương mại điện tử, đây là lợi thế lớn vì hệ thống thường phải triển khai đồng thời nhiều lớp như storefront, admin, API, xác thực, phân quyền và dữ liệu.

Một ưu điểm khác của Laravel là khả năng mở rộng theo hướng phân tầng. Dù bản thân framework thường được biết đến qua mô hình MVC, nhưng nó vẫn cho phép tổ chức thêm service layer, support classes, repository và domain objects khá thuận lợi. Điều này rất phù hợp với dự án hiện tại, vì repo không dừng ở mức controller gọi model trực tiếp mà đã có xu hướng chuẩn hóa sâu hơn.

Tuy nhiên, Laravel cũng có hạn chế cần nhìn nhận thẳng. Nếu lạm dụng cơ chế tiện lợi sẵn có của framework mà không kiểm soát kiến trúc, dự án vẫn có thể trở nên cồng kềnh, controller phình to hoặc logic bị phân tán không nhất quán. Ngoài ra, với các hệ thống đang chuyển đổi từ legacy, việc đưa Laravel vào không tự động giải quyết toàn bộ vấn đề; vẫn cần chiến lược refactor và kiểm tra chất lượng phù hợp. Vì vậy, trong báo cáo, Laravel nên được nhìn như một nền tảng mạnh, nhưng hiệu quả của nó vẫn phụ thuộc vào cách tổ chức mã nguồn và quyết định thiết kế của người phát triển.

## 2.35. Vai trò của HTML, CSS, JavaScript, PHP và Laravel trong riêng dự án đang thực hiện

Khi gắn trực tiếp vào dự án hiện tại, HTML giữ vai trò tạo cấu trúc cho các trang storefront và admin được render bằng Blade. Các thành phần như trang chủ, trang danh mục, chi tiết sản phẩm, giỏ hàng, checkout, dashboard quản trị hay các form cấu hình nội dung đều dựa trên lớp cấu trúc này. Nhờ có HTML được tổ chức tương đối rõ, dữ liệu từ controller và service có thể được đưa ra giao diện một cách nhất quán hơn.

CSS trong dự án đóng vai trò tổ chức trải nghiệm nhìn và luồng đọc thông tin. Điều này thể hiện ở cách trình bày card sản phẩm, block flash sale, khu vực tìm kiếm, giao diện checkout, cũng như shell của khu vực admin. Về mặt thực tiễn, CSS giúp các màn hình có tính thống nhất và hỗ trợ tốt hơn cho trải nghiệm sử dụng, nhất là khi người dùng cần đọc nhiều thông số của thiết bị công nghệ trước khi đưa ra quyết định mua hàng.

JavaScript trong dự án được sử dụng như lớp tăng cường tương tác, đặc biệt ở các luồng search suggest, add-cart-ajax, quick cart, hover cart, tính phí vận chuyển, load comment và một số vùng tương tác động khác. Điều này phù hợp với mô hình mà dự án đang dùng: backend vẫn là trung tâm xử lý, còn JavaScript chịu trách nhiệm làm cho giao diện phản hồi nhanh hơn ở các điểm cần thiết.

PHP là lớp ngôn ngữ nền cho toàn bộ xử lý phía server của dự án. Các thao tác như nhận request, kiểm tra dữ liệu, duy trì session khách hàng, xử lý checkout, quản lý đơn hàng, phân quyền admin, xây API và chạy console command đều dựa trên PHP. Nói cách khác, PHP là lớp thực thi mà nếu bỏ đi thì toàn bộ hệ thống sẽ không còn khả năng vận hành nghiệp vụ.


Nhìn tổng thể, từng công nghệ có một vai trò khác nhau nhưng bổ sung cho nhau trong cùng một kiến trúc. HTML và CSS lo cấu trúc cùng trình bày, JavaScript tăng cường tương tác, PHP xử lý nghiệp vụ phía server và Laravel cung cấp bộ khung chuẩn hóa. Việc làm rõ vai trò này ngay trong Chương 2 giúp phần cơ sở lý thuyết bám sát dự án hơn, đồng thời tạo cầu nối thuận lợi sang Chương 4, nơi các công nghệ đó được nhìn dưới góc độ triển khai thực tế.

## 2.36. Kết luận chương

Chương cơ sở lý thuyết đã trình bày các nền tảng quan trọng phục vụ cho việc phân tích, thiết kế và triển khai hệ thống website thương mại điện tử trên Laravel 11. Ngoài các nội dung cốt lõi như thương mại điện tử, kiến trúc web, MVC, kiến trúc phân lớp, Laravel 11, cơ sở dữ liệu quan hệ, ORM, service layer, REST API, xác thực, phân quyền, bảo mật, AJAX, UML, ERD và kiểm tra chất lượng phần mềm, chương này còn mở rộng thêm các nền tảng liên quan tới HTML, CSS, JavaScript, PHP, hệ sinh thái Laravel, hình ảnh, media, khách vãng lai và các website tham chiếu trong cùng lĩnh vực.

Việc bổ sung các nội dung này giúp chương cơ sở lý thuyết cân bằng hơn giữa ngôn ngữ nền, framework, frontend, backend, dữ liệu, trải nghiệm người dùng và hành vi mua sắm trực tuyến. Nhờ đó, Chương 2 có thể đạt độ dày tốt hơn theo đúng form đồ án tốt nghiệp mà vẫn không lạc đề, vì mọi phần lý thuyết đều được neo lại với bài toán website thương mại điện tử bán thiết bị công nghệ và với kiến trúc thực tế của dự án đang thực hiện.

---
