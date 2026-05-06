# CHƯƠNG 1. CƠ SỞ LÝ THUYẾT

## 1.1. Mở đầu chương

Chương này trình bày các cơ sở lý thuyết làm nền tảng cho việc phân tích, thiết kế và xây dựng hệ thống website thương mại điện tử bán thiết bị công nghệ. Nội dung của chương tập trung vào ba nhóm chính gồm các khái niệm cơ bản liên quan đến đề tài, lý thuyết nền tảng trong phát triển ứng dụng web và các công nghệ, ngôn ngữ lập trình được sử dụng trong quá trình thực hiện hệ thống.

Việc trình bày cơ sở lý thuyết có ý nghĩa quan trọng vì giúp làm rõ bối cảnh học thuật của đề tài, đồng thời tạo nền tảng để các chương sau đi vào phân tích yêu cầu, thiết kế kiến trúc, xây dựng chức năng và đánh giá kết quả thực hiện. Đối với đề tài này, cơ sở lý thuyết không chỉ dừng ở khái niệm thương mại điện tử nói chung mà còn gắn với đặc thù của website bán thiết bị công nghệ và kiến trúc ứng dụng web hiện đại trên nền Laravel 11.

Trong toàn bộ Chương 1 và Chương 2, báo cáo ưu tiên sử dụng thuật ngữ tiếng Việt; các thuật ngữ tiếng Anh được đặt trong ngoặc ở lần xuất hiện đầu tiên để bảo đảm tính chính xác chuyên môn. Theo quy ước này, một số thuật ngữ được dùng thống nhất gồm: giao diện khách hàng (storefront), khu vực quản trị (admin), ca sử dụng (use case), sơ đồ hoạt động (activity diagram), sơ đồ tuần tự (sequence diagram), sơ đồ lớp (class diagram), lớp dịch vụ (service layer), kiểm tra dữ liệu đầu vào (request validation), gợi ý sản phẩm (recommendation), phân tích dữ liệu hành vi (analytics), lược đồ dữ liệu (schema) và dữ liệu cũ (legacy).

## 1.2. Các khái niệm cơ bản liên quan đến đề tài

### 1.2.1. Thương mại điện tử

Thương mại điện tử là hình thức tiến hành một phần hoặc toàn bộ hoạt động mua bán hàng hóa và dịch vụ thông qua các phương tiện điện tử, chủ yếu là Internet. Trong mô hình này, quá trình giới thiệu sản phẩm, tiếp nhận nhu cầu, đặt hàng, thanh toán, chăm sóc khách hàng và quản lý dữ liệu đều có thể được số hóa ở nhiều mức độ khác nhau.

Khác với mô hình kinh doanh truyền thống, thương mại điện tử cho phép doanh nghiệp tiếp cận khách hàng không bị giới hạn nhiều bởi không gian và thời gian. Người dùng có thể tìm kiếm thông tin, so sánh sản phẩm và thực hiện giao dịch ở bất kỳ thời điểm nào. Về phía doanh nghiệp, hệ thống thương mại điện tử giúp mở rộng kênh bán hàng, hỗ trợ tự động hóa quy trình và nâng cao khả năng phân tích hành vi người dùng.

### 1.2.2. Website thương mại điện tử

Website thương mại điện tử là hệ thống phần mềm hoạt động trên nền web, cho phép doanh nghiệp hoặc tổ chức giới thiệu sản phẩm, tiếp nhận đơn hàng, quản lý khách hàng và xử lý các nghiệp vụ liên quan đến bán hàng trực tuyến. Một website thương mại điện tử thông thường không chỉ bao gồm phần giao diện cho khách hàng mà còn có khu vực quản trị để vận hành dữ liệu, nội dung và đơn hàng.

Về mặt chức năng, website thương mại điện tử thường bao gồm các thành phần như danh mục sản phẩm, chi tiết sản phẩm, giỏ hàng, checkout, quản lý đơn hàng, tài khoản người dùng, khuyến mãi và các công cụ hỗ trợ quản trị. Đối với các hệ thống hiện đại, website còn có thể tích hợp thêm API, chatbot, báo cáo, phân quyền và các dịch vụ hỗ trợ vận hành khác.

### 1.2.3. Website thương mại điện tử bán thiết bị công nghệ

Website thương mại điện tử bán thiết bị công nghệ có một số đặc điểm riêng so với nhiều loại website bán hàng khác. Sản phẩm công nghệ thường có nhiều thông số kỹ thuật, nhiều biến thể, nhiều thương hiệu và hành vi mua sắm của người dùng thường mang tính so sánh cao. Vì vậy, hệ thống cần hỗ trợ tốt cho việc trình bày thông tin chi tiết, hình ảnh, tài liệu, đánh giá, so sánh sản phẩm và các công cụ khám phá sản phẩm.

Ngoài ra, khu vực quản trị của website bán thiết bị công nghệ cũng thường phức tạp hơn vì phải xử lý không chỉ dữ liệu sản phẩm mà còn cả nội dung truyền thông, đơn hàng, báo cáo, phân quyền và các công cụ hỗ trợ vận hành. Đây là cơ sở để đề tài lựa chọn mô hình hệ thống có nhiều lớp chức năng thay vì chỉ dừng ở một website bán hàng cơ bản.

### 1.2.4. Khách vãng lai, khách hàng và quản trị viên

Trong một hệ thống thương mại điện tử, người dùng có thể được chia thành nhiều nhóm tác nhân khác nhau.

Khách vãng lai là người truy cập website nhưng chưa đăng nhập. Nhóm người dùng này thường có thể xem trang chủ, duyệt danh mục, xem chi tiết sản phẩm, tìm kiếm, đọc đánh giá và trong nhiều trường hợp có thể thêm tạm sản phẩm vào giỏ hàng. Tuy nhiên, các thao tác như xác nhận đơn hàng hoặc xem lịch sử mua hàng thường yêu cầu đăng nhập.

Khách hàng là người đã có tài khoản hoặc đã đăng nhập vào hệ thống. Nhóm này có thể thực hiện các chức năng sâu hơn như cập nhật thông tin cá nhân, xác nhận đơn hàng, theo dõi lịch sử đơn hàng, đánh giá sản phẩm và nhận các ưu đãi cá nhân hóa.

Quản trị viên là người sử dụng khu vực quản trị của hệ thống để quản lý sản phẩm, danh mục, thương hiệu, bài viết, video, đơn hàng, người dùng, vai trò, quyền truy cập và các báo cáo vận hành. Việc phân biệt rõ ba nhóm đối tượng này giúp mô tả đúng các ca sử dụng và cơ chế phân quyền của hệ thống.

### 1.2.5. Trải nghiệm người dùng trong thương mại điện tử

Trải nghiệm người dùng là yếu tố có ảnh hưởng trực tiếp đến hiệu quả của website thương mại điện tử. Một hệ thống có chức năng đầy đủ nhưng khó sử dụng vẫn có thể làm giảm khả năng giữ chân người dùng và giảm tỷ lệ chuyển đổi.

Trong thương mại điện tử, trải nghiệm người dùng thường gắn với các yếu tố như điều hướng rõ ràng, tìm kiếm thuận tiện, thông tin sản phẩm dễ đọc, hình ảnh trực quan, thao tác thêm giỏ hàng đơn giản, checkout hợp lý và phản hồi trạng thái rõ ràng. Đối với website bán thiết bị công nghệ, trải nghiệm còn liên quan nhiều đến khả năng so sánh sản phẩm, xem thông số kỹ thuật và tham khảo đánh giá trước khi ra quyết định mua.

### 1.2.6. Vai trò của hình ảnh và media trong thương mại điện tử

Trong thương mại điện tử, hình ảnh là một trong những thành phần có ảnh hưởng mạnh nhất tới quyết định mua hàng. Không giống mua bán tại cửa hàng vật lý, người dùng trực tuyến không thể trực tiếp cầm, thử hoặc quan sát sản phẩm ở khoảng cách gần. Vì vậy, hình ảnh và media giữ vai trò thay thế một phần trải nghiệm vật lý đó.

Về mặt lý thuyết, hình ảnh sản phẩm thực hiện nhiều chức năng cùng lúc. Thứ nhất, nó cung cấp thông tin trực quan giúp người dùng nhận diện sản phẩm nhanh hơn so với mô tả văn bản thuần túy. Thứ hai, nó làm tăng mức độ tin cậy cho website vì sản phẩm được thể hiện rõ, có nhiều góc chụp, có ảnh chi tiết hoặc ảnh bối cảnh sử dụng. Thứ ba, nó làm tăng tính hấp dẫn của giao diện, hỗ trợ marketing và giúp người dùng ghi nhớ thương hiệu tốt hơn.

Đối với website bán thiết bị công nghệ, hình ảnh còn có vai trò lớn hơn do sản phẩm thường liên quan tới thiết kế bên ngoài, cổng kết nối, màu sắc, kích thước, phụ kiện đi kèm và các chi tiết dễ gây so sánh. Bên cạnh ảnh tĩnh, nhiều website còn bổ sung video, gallery mở rộng, ảnh zoom hoặc infographic thông số kỹ thuật để tăng hiệu quả truyền đạt. Vì vậy, từ góc độ học thuật, hệ thống media cần được xem là một phần của mô hình dữ liệu và của trải nghiệm người dùng, chứ không chỉ là tài nguyên phụ trợ.

### 1.2.7. Hành vi khám phá sản phẩm và gợi ý sản phẩm (recommendation)

Trong lĩnh vực thương mại điện tử, đặc biệt là bán thiết bị công nghệ, người dùng hiếm khi ra quyết định ngay ở lần xem đầu tiên. Thông thường, họ cần một quá trình khám phá sản phẩm (product discovery) gồm tìm kiếm, lọc, so sánh, đọc đánh giá, xem hình ảnh, tham khảo khuyến mãi và cân nhắc ngân sách. Đây là quá trình người dùng dần thu hẹp lựa chọn trước khi mua hàng.

Gợi ý sản phẩm (recommendation) là cơ chế gợi ý nội dung hoặc gợi ý sản phẩm dựa trên ngữ cảnh, hành vi trước đó hoặc nhóm sản phẩm có liên quan. Trong website thương mại điện tử, recommendation không chỉ là tính năng "đẹp mắt", mà còn là lớp hỗ trợ quyết định mua hàng. Khi người dùng đang xem một sản phẩm, hệ thống có thể gợi ý các model tương tự, sản phẩm cùng phân khúc giá hoặc phụ kiện liên quan.

Từ góc nhìn lý thuyết, product discovery và recommendation phản ánh việc website thương mại điện tử không chỉ giải quyết bài toán hiển thị hàng hóa, mà còn giải quyết bài toán hỗ trợ lựa chọn. Đây là nền tảng phù hợp để đề tài mở rộng sang các nội dung như gợi ý tìm kiếm, recommendation, so sánh sản phẩm và analytics liên quan đến hành vi khám phá.

### 1.2.8. Các mô hình thương mại điện tử phổ biến

Trong thực tế, thương mại điện tử có thể được phân thành nhiều mô hình tùy theo đối tượng tham gia giao dịch.

Mô hình B2C (Business to Customer) là mô hình doanh nghiệp bán hàng trực tiếp cho khách hàng cuối. Đây là mô hình phổ biến nhất đối với các website bán lẻ trực tuyến. Người dùng truy cập hệ thống để xem sản phẩm, thêm vào giỏ hàng, đặt hàng, thanh toán và theo dõi lịch sử giao dịch. Đề tài này phù hợp nhất với mô hình B2C vì hệ thống tập trung vào hai phân hệ chính là giao diện khách hàng (storefront) và khu vực quản trị (admin).

Mô hình B2B (Business to Business) là mô hình giao dịch giữa doanh nghiệp với doanh nghiệp. Các hệ thống B2B thường chú trọng hợp đồng, báo giá, quy trình phê duyệt, quản lý đối tác và mức giá theo nhóm khách hàng. Mặc dù đề tài không triển khai theo hướng B2B, việc trình bày mô hình này giúp làm rõ rằng thương mại điện tử không chỉ giới hạn ở bán lẻ cho người tiêu dùng cuối.

Mô hình C2C (Customer to Customer) là mô hình người dùng giao dịch với nhau thông qua một nền tảng trung gian. Đề tài hiện tại không đi theo hướng C2C, nhưng việc phân biệt C2C với B2C giúp làm rõ rằng hệ thống đang xây dựng là một website có chủ thể vận hành trung tâm, chịu trách nhiệm quản lý catalog, nội dung, đơn hàng và trải nghiệm người dùng.

### 1.2.9. Vai trò của nội dung và CMS trong website thương mại điện tử

Trong nhiều quan niệm đơn giản, website thương mại điện tử chỉ được xem là nơi hiển thị danh sách sản phẩm và xử lý đơn hàng. Tuy nhiên, trên thực tế, nội dung và CMS (Content Management System) có vai trò rất lớn đối với hiệu quả vận hành của hệ thống. Một website bán thiết bị công nghệ không chỉ cần mô tả sản phẩm mà còn cần bài viết tư vấn, video giới thiệu, banner khuyến mãi, trang thông tin, nội dung hỗ trợ SEO và các khối truyền thông phục vụ marketing.

Về mặt học thuật, CMS giúp giải quyết bài toán tổ chức, cập nhật và trình bày nội dung trên website mà không cần can thiệp trực tiếp vào mã nguồn ở mỗi lần thay đổi nhỏ. Khi hệ thống có module bài viết, video, slider hoặc các trang nội dung tĩnh, website sẽ có khả năng hỗ trợ người dùng tốt hơn trong quá trình tìm hiểu sản phẩm và nâng cao khả năng tiếp cận tự nhiên thông qua công cụ tìm kiếm.

Đối với đề tài này, việc đưa CMS vào phạm vi hệ thống là hợp lý vì nó phản ánh đúng nhu cầu của website bán thiết bị công nghệ trong thực tế. Sản phẩm công nghệ thường cần nội dung giải thích, truyền thông và cập nhật thông tin khuyến mãi liên tục. Vì vậy, phần quản trị nội dung không phải là phần rời rạc mà là một thành phần có liên hệ trực tiếp với hiệu quả bán hàng và trải nghiệm người dùng.

## 1.3. Lý thuyết nền tảng cho việc phát triển trang web

### 1.3.1. Mô hình client-server và giao thức HTTP

Phần lớn các ứng dụng web hiện đại được xây dựng trên mô hình client-server. Theo mô hình này, phía client là trình duyệt hoặc ứng dụng gửi yêu cầu, còn phía server là nơi tiếp nhận request, xử lý nghiệp vụ, truy vấn cơ sở dữ liệu và trả về response.

Trong hệ thống thương mại điện tử, client có thể là trình duyệt truy cập giao diện khách hàng hoặc khu vực quản trị, còn server chịu trách nhiệm điều phối tuyến xử lý, xác thực người dùng, xử lý nghiệp vụ và trả về HTML hoặc JSON. Mô hình client-server giúp tách biệt phần hiển thị và phần xử lý trung tâm, từ đó tăng khả năng mở rộng và bảo trì.

HTTP là giao thức truyền tải siêu văn bản được sử dụng để trao đổi dữ liệu giữa client và server. Các phương thức thường dùng gồm `GET`, `POST`, `PUT`, `PATCH` và `DELETE`. Việc hiểu rõ giao thức HTTP là nền tảng quan trọng để xây dựng hệ thống web, đặc biệt khi ứng dụng có cả giao diện web và API.

### 1.3.2. Kiến trúc MVC

MVC là viết tắt của Model - View - Controller, một mô hình kiến trúc phổ biến trong phát triển ứng dụng web. Trong đó, model biểu diễn dữ liệu và các thao tác liên quan đến dữ liệu, view chịu trách nhiệm hiển thị giao diện, còn controller tiếp nhận request, điều phối xử lý và trả kết quả.

Ưu điểm của mô hình MVC là giúp phân tách trách nhiệm giữa các thành phần, làm cho mã nguồn rõ ràng hơn so với cách viết lẫn logic xử lý trong giao diện. Laravel là framework phát triển mạnh trên nền tư duy MVC, do đó đây là một nền tảng lý thuyết quan trọng của đề tài.

### 1.3.3. Kiến trúc phân lớp mở rộng từ MVC

Đối với các hệ thống lớn hơn mức CRUD cơ bản, MVC thuần túy thường chưa đủ để kiểm soát tốt các nghiệp vụ phức tạp. Vì vậy, nhiều dự án hiện đại thường mở rộng thêm các lớp như request validation, service layer, support classes, domain layer và persistence layer.

Cách tiếp cận phân lớp giúp controller gọn hơn, tách phần nghiệp vụ khỏi phần điều phối HTTP và làm cho mã nguồn thuận lợi hơn cho kiểm tra chất lượng, mở rộng và bảo trì. Trong phạm vi đề tài này, việc hệ thống có thêm các lớp `Application`, `Domain`, `Infrastructure/Persistence` và `Support` là biểu hiện của hướng tổ chức phân lớp đó.

### 1.3.4. Giao diện lập trình ứng dụng RESTful API

RESTful API là phong cách thiết kế dịch vụ web trong đó tài nguyên được truy cập thông qua các endpoint và các phương thức HTTP tiêu chuẩn. Trong các hệ thống hiện đại, API không chỉ phục vụ ứng dụng di động mà còn hỗ trợ tích hợp hệ thống, tách lớp dữ liệu và mở rộng khả năng khai thác nghiệp vụ.

RESTful API thường sử dụng JSON làm định dạng trao đổi dữ liệu và kết hợp với mã trạng thái HTTP để mô tả kết quả xử lý. Đối với đề tài này, RESTful API có vai trò quan trọng vì hệ thống không chỉ có giao diện web cho khách hàng và khu vực quản trị mà còn có các nhóm API công khai, API yêu cầu xác thực và API phục vụ quản trị.

### 1.3.5. Cơ sở dữ liệu quan hệ

Cơ sở dữ liệu quan hệ là mô hình tổ chức dữ liệu dưới dạng các bảng có liên hệ với nhau thông qua khóa chính và khóa ngoại. Mô hình này phù hợp với các hệ thống thương mại điện tử vì dữ liệu thường có cấu trúc rõ ràng và liên quan chặt chẽ với nhau, chẳng hạn như sản phẩm, danh mục, thương hiệu, khách hàng, đơn hàng, chi tiết đơn hàng và thanh toán.

Một khái niệm quan trọng khác là chuẩn hóa dữ liệu. Chuẩn hóa giúp giảm dư thừa dữ liệu, hạn chế bất nhất và làm cho cấu trúc dữ liệu dễ bảo trì hơn. Trong đề tài này, khái niệm chuẩn hóa đặc biệt có ý nghĩa vì hệ thống tồn tại song song dữ liệu legacy và lớp schema mới được chuẩn hóa dần.

### 1.3.6. Kiểm tra chất lượng phần mềm trong phát triển web

Kiểm tra chất lượng phần mềm là quá trình xác minh và đánh giá hệ thống nhằm phát hiện lỗi, kiểm tra sự phù hợp với yêu cầu và giảm nguy cơ phát sinh lỗi trong quá trình vận hành. Đối với hệ thống thương mại điện tử, kiểm tra chất lượng có vai trò quan trọng vì nghiệp vụ liên quan trực tiếp đến dữ liệu khách hàng, đơn hàng và tính ổn định của vận hành.


### 1.3.7. Blade Template và tổ chức giao diện server-rendered

Blade là template engine đi kèm Laravel, cho phép xây dựng giao diện server-rendered theo cách rõ ràng và có tổ chức hơn. Về mặt lý thuyết, Blade giữ vai trò kết nối dữ liệu từ controller hoặc service với lớp hiển thị trên trình duyệt. Thay vì viết PHP trộn lẫn trực tiếp vào HTML theo phong cách cũ, Blade cung cấp cú pháp riêng cho điều kiện, vòng lặp, kế thừa layout và chèn các partial dùng chung.

Trong website thương mại điện tử, lợi ích của Blade thể hiện rất rõ. Một hệ thống thường có nhiều màn hình dùng chung các thành phần như header, footer, thanh tìm kiếm, sidebar, card sản phẩm, modal, thông báo trạng thái và form tương tác. Nếu không có cơ chế tái sử dụng hợp lý, mã giao diện sẽ bị lặp nhiều, khó bảo trì và khó mở rộng. Blade giúp tách giao diện thành các layout và partials, từ đó làm cho cấu trúc màn hình rõ ràng hơn.

Đối với đề tài này, Blade phù hợp vì hệ thống được tổ chức theo hướng server-rendered kết hợp thêm một số vùng tương tác động ở phía client. Cách tiếp cận này cân bằng giữa tính đơn giản khi triển khai, khả năng SEO, tốc độ xây dựng giao diện và mức độ phức tạp chấp nhận được trong phạm vi đồ án. Về mặt học thuật, việc nêu Blade trong cơ sở lý thuyết là cần thiết vì đây không chỉ là chi tiết công nghệ, mà còn là một phần của kiến trúc giao diện trong ứng dụng Laravel.

### 1.3.8. Eloquent ORM, Service Layer và Repository Pattern

ORM (Object Relational Mapping) là kỹ thuật ánh xạ dữ liệu từ bảng quan hệ sang các đối tượng trong ngôn ngữ lập trình. Eloquent là ORM của Laravel, cho phép định nghĩa model, mối quan hệ giữa các model và thao tác dữ liệu theo phong cách hướng đối tượng hơn. Ưu điểm của ORM là giúp giảm lượng SQL viết tay trong nhiều trường hợp, làm mã nguồn dễ đọc hơn và biểu diễn quan hệ dữ liệu rõ ràng hơn.

Tuy nhiên, về mặt lý thuyết cũng cần thấy rằng ORM không thay thế hoàn toàn cho tư duy thiết kế dữ liệu. Trong các hệ thống có nhiều nghiệp vụ như thương mại điện tử, người phát triển vẫn phải hiểu rõ quan hệ bảng, chỉ mục, transaction và ảnh hưởng của truy vấn đến hiệu năng. Vì vậy, Eloquent phù hợp cho việc tổ chức truy cập dữ liệu, nhưng nó chỉ phát huy tốt khi được đặt trên một thiết kế cơ sở dữ liệu hợp lý.

Service Layer là lớp trung gian dùng để xử lý nghiệp vụ thay vì để toàn bộ logic nằm trong controller. Một service có thể phụ trách giỏ hàng, checkout, recommendation, báo cáo hoặc xử lý khuyến mãi. Lợi ích của service layer là tách nghiệp vụ khỏi lớp HTTP, tăng khả năng tái sử dụng logic và thuận lợi hơn cho kiểm tra chất lượng.

Repository Pattern là cách trừu tượng hóa thao tác truy cập dữ liệu, giúp business logic bớt phụ thuộc trực tiếp vào chi tiết lưu trữ. Trong thực tế, không phải mọi dự án Laravel đều bắt buộc dùng repository. Tuy nhiên, đối với hệ thống có xu hướng chuẩn hóa kiến trúc, repository là một lựa chọn có giá trị vì giúp tách biệt rõ hơn giữa tầng nghiệp vụ và tầng persistence.

### 1.3.9. Xác thực và phân quyền trong ứng dụng web

Xác thực là quá trình kiểm tra danh tính của người dùng trước khi cho phép truy cập vào các vùng chức năng cần kiểm soát. Trong ứng dụng web, xác thực thường được triển khai thông qua session đối với giao diện server-rendered và token đối với API. Đối với website thương mại điện tử, xác thực có vai trò ở cả phía khách hàng lẫn phía quản trị viên.

Khách hàng thường cần đăng nhập để truy cập vào các chức năng như checkout, lịch sử đơn hàng, cập nhật hồ sơ hoặc các thao tác cá nhân hóa. Trong khi đó, quản trị viên cần xác thực để truy cập vào khu vực quản trị, nơi chứa các chức năng nhạy cảm như chỉnh sửa sản phẩm, quản lý đơn hàng, nội dung, báo cáo và cấu hình hệ thống. Việc phân biệt hai lớp xác thực này giúp hệ thống phản ánh đúng nghiệp vụ và tăng mức độ an toàn.

Phân quyền là bước tiếp theo sau xác thực. Nếu xác thực trả lời câu hỏi "người dùng là ai", thì phân quyền trả lời câu hỏi "người dùng đó được phép làm gì". Trong hệ thống có nhiều vai trò, chỉ xác thực là chưa đủ, vì không phải mọi quản trị viên đều nên có cùng một quyền truy cập. Mô hình RBAC (Role-Based Access Control) là cơ sở lý thuyết phổ biến để tổ chức quyền theo vai trò và gán người dùng vào một hoặc nhiều vai trò thích hợp.

### 1.3.10. Bảo mật trong website thương mại điện tử

Bảo mật là một nguyên tắc xuyên suốt trong phát triển ứng dụng web, đặc biệt đối với các hệ thống thương mại điện tử có liên quan đến dữ liệu khách hàng, đơn hàng, thanh toán và thông tin quản trị. Một trong những nguyên tắc cơ bản nhất là không tin tưởng trực tiếp dữ liệu đầu vào từ phía client. Mọi dữ liệu gửi từ biểu mẫu, truy vấn URL, header hoặc request JSON đều cần được kiểm tra và ràng buộc đầy đủ.

Một số dạng rủi ro phổ biến mà hệ thống web cần lưu ý gồm SQL Injection, Cross-Site Scripting (XSS), Cross-Site Request Forgery (CSRF), truy cập trái phép do thiếu kiểm tra authorization và lộ thông tin nhạy cảm do cách xử lý lỗi không phù hợp. Trong các hệ thống thương mại điện tử, rủi ro bảo mật không chỉ gây lỗi kỹ thuật mà còn có thể làm giảm uy tín của hệ thống và ảnh hưởng đến dữ liệu vận hành.

Các khu vực như đăng nhập, quên mật khẩu, checkout, thanh toán, cấu hình quản trị và API protected thường là những vùng có độ nhạy cảm cao. Vì vậy, về mặt lý thuyết, bảo mật cần được xem như một phần của thiết kế hệ thống chứ không phải phần phụ chỉ xử lý sau khi hoàn thành chức năng.

### 1.3.11. AJAX và tương tác bất đồng bộ

AJAX là kỹ thuật cho phép trình duyệt gửi request tới server và nhận dữ liệu phản hồi mà không cần tải lại toàn bộ trang. Trong website thương mại điện tử, AJAX thường được dùng ở các tính năng như gợi ý tìm kiếm, thêm giỏ hàng nhanh, cập nhật số lượng trong giỏ, tải bình luận, đồng bộ quick cart hoặc phản hồi chatbot.

Về mặt lý thuyết, AJAX giúp cải thiện trải nghiệm người dùng do giảm số lần tải lại trang và làm cho giao diện phản hồi nhanh hơn. Tuy nhiên, AJAX không làm giảm tầm quan trọng của backend. Một thao tác thêm giỏ hàng bằng AJAX chỉ thực sự đúng khi dữ liệu trạng thái, giá, số lượng và session ở phía server được cập nhật nhất quán. Vì vậy, tương tác bất đồng bộ luôn phải được nhìn như một phần của luồng xuyên suốt giữa frontend và backend.

Đối với đề tài này, AJAX có ý nghĩa thực tiễn vì hệ thống không chỉ là website render tĩnh theo từng trang. Một số điểm tương tác quan trọng cần phản hồi linh hoạt để tạo cảm giác sử dụng hiện đại hơn, đặc biệt ở các thao tác liên quan đến tìm kiếm, giỏ hàng và giao tiếp hỗ trợ.

### 1.3.12. UML và ERD trong phân tích thiết kế hệ thống

UML (Unified Modeling Language) là ngôn ngữ mô hình hóa được dùng để biểu diễn các khía cạnh khác nhau của hệ thống phần mềm. Trong báo cáo đồ án, UML giúp trình bày hệ thống ở mức khái quát trước khi đi sâu vào mã nguồn. Các sơ đồ thường gặp gồm sơ đồ ca sử dụng (use case diagram), sơ đồ hoạt động (activity diagram), sơ đồ tuần tự (sequence diagram) và sơ đồ lớp (class diagram).

Sơ đồ ca sử dụng giúp mô tả tác nhân và các chức năng mà hệ thống cung cấp. Sơ đồ hoạt động giúp mô tả luồng xử lý của một nghiệp vụ. Sơ đồ tuần tự thể hiện thứ tự tương tác giữa các thành phần trong một kịch bản cụ thể. Nhờ đó, phần phân tích và thiết kế trở nên trực quan và có tính hệ thống hơn, thay vì chỉ mô tả bằng văn bản thuần túy.

ERD (Entity Relationship Diagram) là sơ đồ thể hiện các thực thể dữ liệu và mối quan hệ giữa chúng. Đối với hệ thống thương mại điện tử, ERD giữ vai trò rất quan trọng vì dữ liệu là nền tảng của toàn bộ các luồng như catalog, cart, checkout, order, review, payment và reporting. Việc đưa UML và ERD vào chương cơ sở lý thuyết giúp tạo cầu nối giữa góc nhìn học thuật và góc nhìn triển khai thực tế.

### 1.3.13. Responsive design và khả năng hiển thị đa thiết bị

Responsive design là nguyên tắc thiết kế giao diện cho phép website thích nghi với nhiều kích thước màn hình khác nhau như desktop, tablet và điện thoại di động. Trong bối cảnh người dùng hiện nay truy cập web bằng nhiều thiết bị, responsive design không còn là lựa chọn phụ mà đã trở thành yêu cầu gần như bắt buộc đối với website thương mại điện tử.

Về mặt lý thuyết, responsive design không chỉ là chuyện co giãn giao diện cho vừa màn hình. Nó còn liên quan đến việc ưu tiên nội dung nào được hiển thị trước, kích thước vùng bấm có đủ thuận tiện hay không, khoảng cách giữa các thành phần có phù hợp cho thao tác cảm ứng hay không và các phần quan trọng như giá, hình ảnh, nút mua hàng có đủ dễ nhìn trên thiết bị nhỏ hay không.

Đối với đề tài này, yếu tố responsive có ý nghĩa thực tiễn rất lớn vì người dùng mua sắm trực tuyến thường truy cập bằng điện thoại. Nếu giao diện không thích nghi tốt, trải nghiệm tìm kiếm, so sánh, thêm giỏ hàng và checkout sẽ bị ảnh hưởng trực tiếp. Do đó, responsive design cần được xem như một phần của lý thuyết nền tảng cho phát triển website thương mại điện tử.

### 1.3.14. Vòng đời xử lý request trong Laravel

Khi người dùng gửi request đến hệ thống Laravel, request trước tiên đi vào entry point của ứng dụng rồi được chuyển qua kernel để đi qua chuỗi middleware. Sau đó, hệ thống thực hiện route matching để xác định controller phù hợp. Tại controller, request có thể tiếp tục đi qua request validation, service layer, repository hoặc model trước khi tạo response trả về cho client.

Việc hiểu vòng đời request có ý nghĩa quan trọng về mặt học thuật vì nó giúp người thực hiện đề tài không chỉ biết “chức năng chạy như thế nào”, mà còn hiểu request đi qua những tầng nào và mỗi tầng chịu trách nhiệm gì. Đây là cơ sở để giải thích các luồng như thêm giỏ hàng, checkout, xác thực người dùng, truy cập admin hoặc gọi API protected.

Trong đề tài này, việc có middleware, request validation, controller, service, persistence và response web/API cho thấy request không được xử lý theo cách ngắn gọn một tầng duy nhất. Vì vậy, việc trình bày vòng đời request trong Laravel giúp chương cơ sở lý thuyết gắn chặt hơn với kiến trúc thực tế của dự án.

### 1.3.15. Session, cookie và quản lý trạng thái người dùng

Một đặc điểm quan trọng của website thương mại điện tử là hệ thống phải duy trì nhiều trạng thái tạm thời trong suốt phiên làm việc của người dùng. Ví dụ, giỏ hàng hiện tại, thông tin khách hàng đã đăng nhập, đích điều hướng sau đăng nhập, coupon đang được áp dụng hoặc địa chỉ giao hàng vừa chọn đều là những trạng thái có ý nghĩa nghiệp vụ. Nếu các trạng thái này không được duy trì nhất quán, trải nghiệm mua hàng sẽ bị đứt đoạn.

Session là cơ chế cho phép server lưu giữ trạng thái liên quan đến một người dùng trong suốt phiên làm việc. Cookie là dữ liệu nhỏ được lưu ở phía trình duyệt để hỗ trợ nhận diện phiên hoặc lưu một số thông tin cần thiết. Trong các ứng dụng web truyền thống và hệ thống Laravel server-rendered, session giữ vai trò rất quan trọng vì nó giúp nối liền nhiều request rời rạc thành một luồng tương tác có tính liên tục.

Từ góc nhìn lý thuyết, session và cookie cho thấy website thương mại điện tử không chỉ là tập hợp các trang HTML độc lập, mà là một ứng dụng có trạng thái. Đề tài hiện tại cũng phản ánh rõ điều này ở các luồng như đăng nhập rồi quay lại checkout, giỏ hàng theo session, ghi nhớ thông tin khách hàng và đồng bộ trạng thái giao diện storefront.

### 1.3.16. Cache và tối ưu hiệu năng

Hiệu năng là một yếu tố rất quan trọng của website thương mại điện tử vì nó ảnh hưởng trực tiếp đến trải nghiệm người dùng, tỷ lệ thoát trang và khả năng chuyển đổi. Một website phản hồi chậm ở các thao tác như mở trang chủ, tải danh mục, xem chi tiết sản phẩm hoặc thêm giỏ hàng có thể làm giảm đáng kể hiệu quả sử dụng.

Cache là kỹ thuật lưu trữ tạm thời kết quả của các phép tính hoặc truy vấn để tái sử dụng ở những lần truy cập sau, từ đó giảm tải cho hệ thống và tăng tốc độ phản hồi. Trong thương mại điện tử, cache thường được áp dụng cho các dữ liệu ít thay đổi trong thời gian ngắn như danh mục sản phẩm, thương hiệu, sản phẩm nổi bật, thiết lập hệ thống hoặc các thống kê tổng hợp.

Về mặt lý thuyết, cache không chỉ là một biện pháp kỹ thuật tăng tốc hệ thống, mà còn là biểu hiện của tư duy tối ưu hóa kiến trúc. Khi hệ thống có quy mô dữ liệu và số lượt truy cập tăng dần, việc tổ chức cache hợp lý sẽ giúp giảm số lượng truy vấn lặp, giảm tải cơ sở dữ liệu và giữ trải nghiệm người dùng ổn định hơn.

### 1.3.17. SEO trong website thương mại điện tử

SEO (Search Engine Optimization) là quá trình tối ưu website để tăng khả năng hiển thị trên các công cụ tìm kiếm. Đối với website thương mại điện tử, SEO có vai trò đặc biệt quan trọng vì nhiều người dùng bắt đầu hành vi mua sắm bằng việc tìm kiếm thông tin trên Google hoặc các nền tảng tìm kiếm khác. Nếu sản phẩm, danh mục hoặc bài viết không được tổ chức tốt cho SEO, khả năng tiếp cận tự nhiên của hệ thống sẽ bị giảm đáng kể.

Về mặt kỹ thuật, SEO liên quan đến nhiều yếu tố như cấu trúc URL, semantic HTML, tiêu đề trang, mô tả ngắn, tốc độ tải trang, khả năng hiển thị trên thiết bị di động, tổ chức nội dung và liên kết nội bộ. Trong website bán thiết bị công nghệ, SEO còn gắn chặt với chất lượng mô tả sản phẩm, bài viết tư vấn và hệ thống phân loại danh mục, thương hiệu, tag hoặc nội dung liên quan.

Từ góc nhìn học thuật, đưa SEO vào cơ sở lý thuyết giúp báo cáo phản ánh đầy đủ hơn bản chất của website thương mại điện tử hiện đại. Website không chỉ cần hoạt động đúng về mặt chức năng mà còn cần có khả năng được người dùng tìm thấy thông qua các công cụ tìm kiếm.

### 1.3.18. Queue, event và tác vụ nền

Khi hệ thống ngày càng có nhiều chức năng, không phải mọi tác vụ đều nên được xử lý đồng bộ ngay trong request chính. Một số thao tác như gửi email, xử lý thống kê, cập nhật chỉ số, sinh báo cáo hoặc đồng bộ thông báo có thể tiêu tốn thời gian và làm chậm phản hồi nếu xử lý trực tiếp trong luồng người dùng đang chờ.

Queue là cơ chế hàng đợi cho phép đẩy các tác vụ nặng hoặc không cần phản hồi ngay ra xử lý nền. Event là cơ chế phát tín hiệu khi một hành động nào đó xảy ra, từ đó cho phép các listener hoặc job khác tiếp nhận xử lý tiếp theo. Về mặt lý thuyết, event và queue giúp làm lỏng liên kết giữa các thành phần trong hệ thống, cải thiện hiệu năng và tạo điều kiện tổ chức nghiệp vụ theo hướng module hóa hơn.

Đối với website thương mại điện tử, đây là nền tảng quan trọng cho các bài toán như gửi mail xác nhận, ghi log hành vi, thống kê hoạt động, xử lý notification hoặc mở rộng các tích hợp hậu xử lý. Việc đề cập đến queue và event trong chương cơ sở lý thuyết giúp báo cáo có chiều sâu hơn về kiến trúc ứng dụng.

### 1.3.19. Logging, monitoring và health check

Logging là quá trình ghi nhận lại các sự kiện, lỗi hoặc thao tác quan trọng phát sinh trong hệ thống. Monitoring là hoạt động theo dõi trạng thái vận hành, hiệu năng và các chỉ số quan trọng của ứng dụng trong quá trình chạy thực tế. Health check là cơ chế kiểm tra nhanh tình trạng của các thành phần cốt lõi như database, cache, storage hoặc API.

Trong hệ thống thương mại điện tử, logging và monitoring có ý nghĩa lớn vì lỗi không chỉ ảnh hưởng tới trải nghiệm người dùng mà còn có thể liên quan tới đơn hàng, thanh toán hoặc dữ liệu quản trị. Nếu hệ thống không có khả năng theo dõi và chẩn đoán trạng thái vận hành, việc phát hiện nguyên nhân sự cố sẽ khó khăn hơn rất nhiều.

Từ góc nhìn học thuật, logging và monitoring cho thấy ứng dụng web không chỉ cần được xây dựng để “chạy được”, mà còn cần được thiết kế để có thể vận hành, quan sát và bảo trì. Đây là dấu hiệu quan trọng phân biệt một hệ thống có tư duy kỹ thuật trưởng thành hơn với một ứng dụng chỉ dừng ở mức mô phỏng chức năng.

### 1.3.20. Mail workflow và chatbot trong hệ thống hỗ trợ người dùng

Trong thương mại điện tử, giao tiếp với người dùng không chỉ diễn ra thông qua giao diện trang web. Email và chatbot là hai lớp hỗ trợ rất quan trọng. Email thường được dùng cho các tình huống như xác nhận đơn hàng, khôi phục mật khẩu, gửi coupon hoặc thông báo liên quan đến tài khoản. Chatbot hỗ trợ người dùng trong việc đặt câu hỏi, nhận thông tin cơ bản hoặc được định hướng tới các thao tác phù hợp.

Về mặt lý thuyết, mail workflow cho thấy hệ thống thương mại điện tử có vòng đời tương tác kéo dài hơn một request trực tiếp trên giao diện. Sau khi một hành động diễn ra trong hệ thống, có thể cần thêm các bước thông báo và hỗ trợ người dùng qua kênh khác. Điều này làm cho kiến trúc ứng dụng có chiều sâu hơn so với một website hiển thị thông tin thuần túy.

Chatbot cũng là một thành phần đáng chú ý ở góc nhìn trải nghiệm người dùng. Dù mức độ thông minh của chatbot có thể khác nhau, về bản chất nó đóng vai trò như một lớp hỗ trợ tương tác nhanh, giảm độ ma sát khi người dùng cần thông tin ngay trên giao diện. Việc đưa mail workflow và chatbot vào phần lý thuyết giúp làm rõ rằng website thương mại điện tử hiện đại không chỉ gồm sản phẩm và đơn hàng, mà còn bao gồm các cơ chế hỗ trợ sau tương tác.

### 1.3.21. Rate limiting và bảo vệ API khỏi lạm dụng

Khi hệ thống cung cấp REST API, việc chỉ xây dựng endpoint là chưa đủ. Một vấn đề quan trọng khác là bảo vệ API khỏi việc bị lạm dụng quá mức, gửi quá nhiều request trong thời gian ngắn hoặc bị khai thác sai mục đích. Đây là lý do rate limiting trở thành một khái niệm quan trọng trong thiết kế hệ thống hiện đại.

Rate limiting là cơ chế giới hạn số lượng request mà một người dùng, một token hoặc một địa chỉ IP có thể gửi trong một khoảng thời gian nhất định. Về mặt lý thuyết, cơ chế này giúp giảm nguy cơ tấn công brute force, giảm tải không cần thiết lên hệ thống và làm cho môi trường API ổn định hơn với người dùng hợp lệ. Trong website thương mại điện tử, rate limiting đặc biệt quan trọng với các khu vực như đăng nhập, tìm kiếm, API public và các endpoint có thể bị lạm dụng tự động.

Việc đưa rate limiting vào phần cơ sở lý thuyết giúp báo cáo bám sát hơn với thực tế phát triển ứng dụng web hiện đại. Hệ thống không chỉ cần đúng về chức năng mà còn phải có cơ chế tự bảo vệ trước các hành vi truy cập bất thường.

### 1.3.22. Di trú dữ liệu từ hệ thống legacy sang schema mới

Một bài toán thực tế thường gặp trong phát triển hệ thống là không phải lúc nào ứng dụng cũng được xây dựng hoàn toàn từ đầu trên một cấu trúc dữ liệu lý tưởng. Trong nhiều trường hợp, hệ thống phải kế thừa dữ liệu cũ hoặc logic cũ và từng bước chuyển đổi sang mô hình mới. Quá trình đó được gọi là di trú dữ liệu hoặc migration từ hệ thống legacy sang schema chuẩn hóa hơn.

Về mặt lý thuyết, bài toán migration không chỉ là sao chép dữ liệu từ bảng này sang bảng khác. Nó còn liên quan đến việc xác định ánh xạ giữa cấu trúc cũ và cấu trúc mới, làm sạch dữ liệu, xử lý trùng lặp, chuẩn hóa định dạng và bảo đảm tính nhất quán sau khi chuyển đổi. Trong hệ thống thương mại điện tử, dữ liệu legacy thường liên quan đến sản phẩm, danh mục, khách hàng, đơn hàng, coupon hoặc nội dung, nên mức độ ảnh hưởng của migration là rất lớn.

Đối với đề tài này, việc tồn tại đồng thời lớp dữ liệu legacy và lớp chuẩn hóa mới làm cho báo cáo có chiều sâu thực tiễn hơn. Điều đó cho thấy hệ thống không chỉ được trình bày như một mô hình lý tưởng, mà còn phản ánh bài toán chuyển đổi dần dần rất thường gặp trong phát triển phần mềm thật.

### 1.3.23. Dữ liệu hành vi người dùng và analytics

Ngoài dữ liệu giao dịch như sản phẩm, đơn hàng và thanh toán, website thương mại điện tử hiện đại còn quan tâm nhiều đến dữ liệu hành vi người dùng. Dữ liệu hành vi bao gồm các thông tin như lượt xem sản phẩm, lượt xem trang, phiên làm việc, từ khóa tìm kiếm, tương tác với khối gợi ý hoặc các sự kiện liên quan đến quá trình khám phá sản phẩm.

Về mặt học thuật, analytics có ý nghĩa ở chỗ nó mở rộng góc nhìn về hệ thống từ “quản lý dữ liệu vận hành” sang “phân tích cách người dùng tương tác với hệ thống”. Nhờ dữ liệu hành vi, người quản trị có thể hiểu rõ hơn sản phẩm nào được quan tâm, khu vực nào của giao diện có hiệu quả hơn, hành vi nào đang dẫn tới chuyển đổi và điểm nào có thể gây thoát phiên.

Đối với website bán thiết bị công nghệ, dữ liệu analytics còn đặc biệt hữu ích vì người dùng thường có hành vi khám phá dài hơn, so sánh nhiều hơn và quay lại nhiều lần trước khi mua. Vì vậy, đưa analytics vào chương cơ sở lý thuyết giúp báo cáo phản ánh đúng xu hướng phát triển của các website thương mại điện tử thực tế.

### 1.3.24. Khả năng bảo trì, nợ kỹ thuật và chiến lược tái cấu trúc

Trong phát triển phần mềm, technical debt hay “nợ kỹ thuật” là khái niệm dùng để chỉ phần chi phí phải trả về sau do các quyết định triển khai nhanh, cấu trúc chưa tối ưu hoặc giải pháp tạm thời trong quá trình phát triển. Một hệ thống có thể chạy tốt trong ngắn hạn nhưng dần trở nên khó bảo trì, khó mở rộng và dễ phát sinh lỗi khi technical debt tích lũy quá nhiều.

Khả năng bảo trì là một yêu cầu phi chức năng quan trọng, đặc biệt với các hệ thống thương mại điện tử có nhiều module và nhiều luồng nghiệp vụ liên quan chặt chẽ. Nếu controller quá dài, logic validation phân tán, dữ liệu không nhất quán hoặc giao diện thiếu chuẩn hóa, chi phí chỉnh sửa về sau sẽ tăng lên rất nhanh.

Tái cấu trúc mã nguồn (refactor) là quá trình tổ chức lại mã nguồn để cải thiện cấu trúc mà không làm thay đổi hành vi quan sát được của hệ thống. Về mặt lý thuyết, đây là cơ chế giúp giảm nợ kỹ thuật (technical debt) và cải thiện khả năng bảo trì theo thời gian. Trong đề tài này, việc từng bước chuẩn hóa từ lớp dữ liệu cũ sang lớp dịch vụ, kiểm tra dữ liệu đầu vào, tầng lưu trữ và các lớp hỗ trợ là một ví dụ điển hình cho chiến lược tái cấu trúc có kiểm soát.

## 1.4. Công nghệ và ngôn ngữ lập trình sử dụng

### 1.4.1. Ngôn ngữ PHP

PHP là ngôn ngữ lập trình phía server được sử dụng rộng rãi trong phát triển web. PHP hỗ trợ tốt các tác vụ như xử lý request, thao tác form, quản lý session, truy cập cơ sở dữ liệu, gửi mail và tạo response HTTP. Đây là ngôn ngữ nền để xây dựng backend của hệ thống trong đề tài này.

Ưu điểm của PHP là tính thực dụng, tài liệu phong phú, cộng đồng lớn và khả năng kết hợp tốt với các framework mạnh như Laravel. Nhờ đó, PHP vẫn là lựa chọn phù hợp cho các hệ thống web có nhiều nghiệp vụ như thương mại điện tử.

### 1.4.2. Framework Laravel 11

Laravel là framework mã nguồn mở viết cho PHP, được thiết kế theo hướng hiện đại, rõ ràng và dễ mở rộng. Laravel cung cấp sẵn nhiều thành phần quan trọng như routing, middleware, request validation, Eloquent ORM, Blade template, queue, mail, authentication và authorization.

Laravel 11 được sử dụng trong đề tài vì phù hợp với việc phát triển cả web và API, đồng thời hỗ trợ tổ chức mã nguồn tốt hơn cho các hệ thống có nhiều phân hệ. Việc sử dụng Laravel giúp rút ngắn thời gian xây dựng các thành phần nền tảng và cho phép tập trung nhiều hơn vào nghiệp vụ của website thương mại điện tử.

### 1.4.3. Blade Template

Blade là template engine đi kèm Laravel, cho phép xây dựng giao diện server-rendered một cách linh hoạt. Blade hỗ trợ layout inheritance, include partials, điều kiện hiển thị và truyền dữ liệu từ controller sang view.

Trong đề tài này, Blade có vai trò quan trọng trong việc xây dựng giao diện storefront và khu vực quản trị. Việc tổ chức view theo layout và partials giúp giảm lặp mã giao diện, tăng khả năng tái sử dụng và thuận tiện cho bảo trì.

### 1.4.4. JavaScript

JavaScript là ngôn ngữ kịch bản phía client, cho phép giao diện phản hồi linh hoạt với thao tác của người dùng. Trong website thương mại điện tử, JavaScript thường được dùng cho các tính năng như gợi ý tìm kiếm, thêm giỏ hàng bằng AJAX, cập nhật quick cart, hiển thị chatbot hoặc xử lý các tương tác giao diện khác.

JavaScript có vai trò quan trọng trong việc nâng cao trải nghiệm người dùng, đặc biệt ở những thao tác yêu cầu phản hồi nhanh mà không cần tải lại toàn bộ trang.

### 1.4.5. Thư viện Vue.js

Vue.js là thư viện JavaScript dùng để xây dựng giao diện người dùng theo hướng component-based. Trong dự án này, Vue được sử dụng ở một số thành phần giao diện như tìm kiếm, điều hướng di động, danh sách sản phẩm hoặc các thành phần tương tác cục bộ.

Việc sử dụng Vue.js giúp tăng tính linh hoạt cho giao diện và hỗ trợ tốt cho mô hình kết hợp giữa server-rendered với các vùng tương tác động ở phía client.

### 1.4.6. CSS và Bootstrap

CSS là ngôn ngữ định kiểu dùng để tổ chức cách trình bày của giao diện web. Bootstrap là một framework CSS phổ biến, cung cấp sẵn nhiều thành phần giao diện như grid, button, form, modal, navbar và nhiều lớp tiện ích khác.

Trong đề tài này, CSS và Bootstrap giúp rút ngắn thời gian xây dựng giao diện, tăng tính thống nhất giữa các màn hình và hỗ trợ hiển thị responsive trên nhiều loại thiết bị. Đây là nền tảng quan trọng cho cả storefront và admin.

### 1.4.7. Tailwind CSS và Vite

Tailwind CSS là một framework utility-first hỗ trợ xây dựng giao diện bằng các lớp tiện ích có sẵn. Vite là công cụ build frontend hiện đại giúp biên dịch và nạp tài nguyên nhanh hơn trong quá trình phát triển.

Việc dự án kết hợp các công cụ này cho thấy hệ thống không chỉ sử dụng giao diện Blade truyền thống mà còn có định hướng chuẩn hóa và tối ưu quy trình phát triển frontend.

### 1.4.8. Hệ quản trị cơ sở dữ liệu MySQL

MySQL là hệ quản trị cơ sở dữ liệu quan hệ được sử dụng phổ biến trong các ứng dụng web. MySQL phù hợp với hệ thống thương mại điện tử vì hỗ trợ tốt cho dữ liệu có cấu trúc, truy vấn quan hệ, transaction và khả năng vận hành ổn định.

Trong đề tài này, MySQL là nơi lưu trữ dữ liệu sản phẩm, danh mục, thương hiệu, khách hàng, đơn hàng, thanh toán, nội dung và nhiều nhóm dữ liệu khác của hệ thống.

### 1.4.9. Laravel Sanctum

Laravel Sanctum là giải pháp xác thực nhẹ cho API trong hệ sinh thái Laravel. Sanctum hỗ trợ quản lý token truy cập và phù hợp với các hệ thống cần xây dựng API protected mà không cần một cơ chế xác thực quá nặng.

Trong đề tài này, Sanctum được sử dụng để hỗ trợ các endpoint API cần xác thực, góp phần hoàn thiện mặt kiến trúc API của hệ thống.

### 1.4.10. Spatie Laravel Permission

`spatie/laravel-permission` là gói thư viện hỗ trợ phân quyền theo vai trò và quyền chi tiết trong Laravel. Gói này cho phép quản lý role, permission và gán quyền truy cập theo từng nhóm chức năng.

Đối với khu vực quản trị của hệ thống, đây là công nghệ phù hợp để kiểm soát quyền truy cập vào các module như sản phẩm, nội dung, đơn hàng, người dùng và báo cáo.

### 1.4.11. Visual Studio Code

Visual Studio Code là môi trường phát triển tích hợp được sử dụng phổ biến trong lập trình web. Công cụ này hỗ trợ tốt cho việc viết mã, quản lý file, gợi ý cú pháp, tích hợp terminal và mở rộng bằng các extension.

Trong quá trình thực hiện đề tài, Visual Studio Code là công cụ thuận tiện để phát triển mã nguồn Laravel, frontend assets và các tệp cấu hình của dự án.

### 1.4.12. DBeaver

DBeaver là công cụ quản trị cơ sở dữ liệu hỗ trợ nhiều hệ quản trị khác nhau, trong đó có MySQL. Công cụ này giúp theo dõi cấu trúc bảng, thực hiện truy vấn SQL, kiểm tra dữ liệu và quản lý cơ sở dữ liệu thuận tiện hơn.

Đối với đề tài này, DBeaver hỗ trợ tốt cho việc quan sát và kiểm tra dữ liệu trong quá trình thiết kế, cài đặt và kiểm tra chất lượng hệ thống.

### 1.4.13. HTML

HTML là ngôn ngữ đánh dấu cơ bản dùng để tổ chức cấu trúc nội dung trên web. Trong bất kỳ website thương mại điện tử nào, HTML không chỉ có chức năng hiển thị chữ và hình, mà còn đóng vai trò tạo ra bộ khung logic cho toàn bộ giao diện như header, navigation, danh sách sản phẩm, biểu mẫu đăng nhập, giỏ hàng, checkout, phần đánh giá và footer.

Về mặt lý thuyết, HTML có ý nghĩa đặc biệt vì nó quyết định tính có cấu trúc của dữ liệu giao diện. Một trang chi tiết sản phẩm nếu chỉ được dựng theo kiểu trình bày trực quan mà không có cấu trúc hợp lý sẽ khó mở rộng, khó tối ưu SEO và khó tích hợp với CSS, JavaScript hoặc các công cụ phân tích hành vi người dùng. Khi nội dung được tổ chức bằng các thành phần semantic như `header`, `nav`, `section`, `article`, `main`, `aside` và `footer`, hệ thống sẽ rõ ràng hơn với cả trình duyệt lẫn người phát triển.

### 1.4.14. Node.js và npm

Mặc dù hệ thống sử dụng PHP và Laravel ở phía server, môi trường Node.js và npm vẫn có vai trò quan trọng trong chu trình phát triển frontend hiện đại. Node.js cung cấp môi trường chạy JavaScript ngoài trình duyệt, còn npm là trình quản lý gói giúp cài đặt và quản lý các thư viện frontend như Vue, Bootstrap, Vite và các plugin liên quan.

Trong bối cảnh phát triển web hiện đại, việc sử dụng Node.js không có nghĩa là backend phải viết bằng JavaScript. Thay vào đó, nó hỗ trợ cho quá trình build tài nguyên tĩnh, tối ưu CSS và JavaScript, chạy công cụ bundler, dev server và quản lý phụ thuộc của phần giao diện. Đây là mô hình rất phổ biến ở các dự án Laravel hiện nay.

### 1.4.15. Composer

Composer là trình quản lý phụ thuộc dành cho PHP. Công cụ này cho phép dự án khai báo các package cần thiết trong file cấu hình và tự động cài đặt chúng theo phiên bản phù hợp. Trong hệ sinh thái PHP nói chung và Laravel nói riêng, Composer là một công cụ gần như bắt buộc.

Đối với đề tài này, Composer giúp quản lý các gói như Laravel framework, Sanctum, Socialite, Spatie Permission, DOMPDF, Swagger và các thư viện hỗ trợ khác. Việc sử dụng Composer góp phần chuẩn hóa môi trường phát triển và giúp việc cài đặt, cập nhật, tái lập dự án trở nên thuận tiện hơn.

### 1.4.16. Git và GitHub

Git là hệ thống quản lý phiên bản phân tán được sử dụng rộng rãi trong phát triển phần mềm. Git cho phép theo dõi thay đổi mã nguồn, quản lý lịch sử chỉnh sửa, tách nhánh làm việc và phục hồi về các trạng thái trước đó khi cần thiết. Đối với đồ án có nhiều lần chỉnh sửa và mở rộng chức năng, Git giúp kiểm soát tiến trình phát triển tốt hơn.

GitHub là nền tảng lưu trữ mã nguồn và cộng tác dựa trên Git. Việc sử dụng GitHub không chỉ có ý nghĩa lưu trữ mà còn giúp quản lý lịch sử commit, đồng bộ mã nguồn và tạo điều kiện thuận lợi cho việc theo dõi quá trình phát triển hệ thống. Từ góc nhìn học thuật, sử dụng Git và GitHub phản ánh cách làm phần mềm có quy trình hơn.

### 1.4.17. Swagger và tài liệu hóa API

Swagger là một công cụ hỗ trợ mô tả, kiểm tra chất lượng và tài liệu hóa API. Trong các hệ thống hiện đại có REST API, tài liệu API giúp người phát triển và người tích hợp hiểu được endpoint, phương thức HTTP, dữ liệu đầu vào, dữ liệu đầu ra và các mã trạng thái liên quan.

Đối với đề tài này, sự xuất hiện của tài liệu API cho thấy hệ thống không chỉ dừng ở web interface mà còn có định hướng mở rộng hoặc tích hợp với các client khác trong tương lai. Việc đưa Swagger vào phần công nghệ sử dụng giúp chương cơ sở lý thuyết bám sát hơn với thực trạng của dự án.

### 1.4.18. Laravel Socialite

Laravel Socialite là gói hỗ trợ đăng nhập xã hội trong hệ sinh thái Laravel, thường được sử dụng cho các kịch bản xác thực qua Google, Facebook hoặc các nhà cung cấp OAuth khác. Về mặt học thuật, Socialite có ý nghĩa ở chỗ nó mở rộng khái niệm xác thực từ mô hình tài khoản nội bộ truyền thống sang mô hình liên kết với nền tảng bên thứ ba.

Trong các website thương mại điện tử, đăng nhập xã hội có thể giúp giảm ma sát cho người dùng ở bước tạo tài khoản hoặc đăng nhập, từ đó tăng khả năng tiếp tục quy trình mua hàng. Sự xuất hiện của Socialite trong dự án cũng cho thấy hệ thống có định hướng nâng cao trải nghiệm xác thực thay vì chỉ dừng ở form đăng nhập cơ bản.

### 1.4.19. DOMPDF và xuất tài liệu

DOMPDF là thư viện hỗ trợ chuyển đổi nội dung HTML sang PDF trong môi trường PHP. Về mặt học thuật, công nghệ này liên quan tới bài toán xuất báo cáo, hóa đơn hoặc tài liệu phục vụ quản trị. Trong nhiều hệ thống thương mại điện tử, nhu cầu in đơn hàng, tạo phiếu, xuất thống kê hoặc sinh tài liệu PDF là khá phổ biến.

Việc đưa DOMPDF vào dự án cho thấy hệ thống không chỉ hướng tới giao diện hiển thị trên web mà còn quan tâm tới nhu cầu xuất dữ liệu ra dạng tài liệu. Đây là một điểm có giá trị thực tiễn, giúp đề tài tiến gần hơn với mô hình ứng dụng có thể hỗ trợ vận hành thật.

### 1.4.20. Axios

Axios là thư viện JavaScript dùng để gửi HTTP request từ phía client tới server, thường được sử dụng trong các thao tác AJAX hoặc giao tiếp với REST API. So với cách dùng `XMLHttpRequest` truyền thống, Axios cung cấp cú pháp rõ ràng hơn, hỗ trợ promise và xử lý request/response thuận tiện hơn.

Trong hệ thống thương mại điện tử, Axios phù hợp với các tác vụ như gọi API lấy dữ liệu sản phẩm, cập nhật trạng thái giỏ hàng, gửi request tìm kiếm hoặc tương tác với các thành phần frontend động. Về mặt học thuật, sự xuất hiện của Axios cho thấy lớp frontend của dự án có khả năng trao đổi dữ liệu có cấu trúc với backend theo cách linh hoạt hơn.

### 1.4.21. Pinia

Pinia là thư viện quản lý state cho Vue.js. Trong các giao diện có nhiều component tương tác với nhau, state management giúp tập trung dữ liệu dùng chung vào một nơi thay vì truyền đi truyền lại qua nhiều lớp component. Điều này làm cho frontend rõ ràng hơn và dễ bảo trì hơn.

Trong bối cảnh website thương mại điện tử, state management đặc biệt hữu ích cho các dữ liệu như giỏ hàng, trạng thái đăng nhập, danh sách yêu thích hoặc bộ lọc sản phẩm. Đối với đề tài này, việc xuất hiện Pinia cho thấy dự án có thêm chiều sâu ở phía frontend và không chỉ dừng ở các script rời rạc.

### 1.4.22. Vue Router

Vue Router là thư viện định tuyến phía client dành cho ứng dụng Vue. Dù dự án hiện tại không được tổ chức như một SPA hoàn chỉnh, sự hiện diện của Vue Router vẫn có ý nghĩa vì nó cho thấy frontend được chuẩn bị khả năng mở rộng cho các vùng giao diện có tính điều hướng nội bộ hoặc component-based rõ hơn.

Từ góc độ học thuật, Vue Router liên quan tới khái niệm client-side routing, tức cách tổ chức điều hướng ở phía trình duyệt thay vì dựa hoàn toàn vào route phía server. Điều này giúp báo cáo bao quát hơn về khả năng phát triển frontend hiện đại của dự án.

### 1.4.23. jQuery

jQuery là thư viện JavaScript từng được sử dụng rất rộng rãi để thao tác DOM, gửi AJAX request và xử lý tương tác trên giao diện. Dù nhiều dự án hiện đại chuyển sang các framework component-based như Vue hoặc React, jQuery vẫn còn giá trị trong nhiều hệ thống có lớp giao diện legacy hoặc các script tương tác cục bộ được phát triển từ trước.

Trong bối cảnh đề tài này, việc nhắc tới jQuery là hợp lý vì nó phản ánh đúng thực tế của nhiều dự án web đang trong quá trình hiện đại hóa: một phần giao diện và script mới có thể dùng công cụ hiện đại hơn, nhưng một số vùng legacy vẫn phụ thuộc vào các cách tổ chức cũ. Về mặt học thuật, đây là một ví dụ tốt cho thấy hệ thống thực tế thường mang tính chuyển tiếp giữa nhiều thế hệ công nghệ.

### 1.4.24. Laravel Mail và workflow email

Laravel Mail là thành phần hỗ trợ gửi email trong hệ sinh thái Laravel. Công cụ này giúp định nghĩa nội dung email, template hiển thị, dữ liệu gửi kèm và tích hợp với các dịch vụ gửi mail thực tế. Trong thương mại điện tử, email là một phần rất quan trọng của vòng đời người dùng, từ xác nhận tài khoản đến xác nhận đơn hàng và khôi phục mật khẩu.

Về mặt học thuật, Laravel Mail không chỉ là chi tiết kỹ thuật để “gửi thư”, mà còn phản ánh lớp giao tiếp ngoài giao diện web. Việc hệ thống có workflow email cho thấy ứng dụng có vòng đời vận hành rộng hơn một request trực tiếp trên trình duyệt và có khả năng duy trì tương tác với người dùng sau khi họ rời khỏi trang hiện tại.

### 1.4.25. Hệ sinh thái Artisan và console commands

Artisan là bộ công cụ dòng lệnh đi kèm Laravel, cho phép thực hiện nhiều tác vụ như tạo mã nguồn khung, chạy migration, clear cache, chạy queue hoặc định nghĩa các command tùy biến cho nghiệp vụ của hệ thống. Trong các dự án có quy mô hơn mức cơ bản, Artisan không chỉ là công cụ phụ trợ mà còn trở thành một phần quan trọng của quy trình vận hành.

Trong đề tài này, console command có ý nghĩa đặc biệt vì hệ thống có thêm các lệnh liên quan đến migration dữ liệu legacy, tối ưu hiệu năng, kiểm tra bảo mật, chuẩn hóa xác thực quản trị hoặc hỗ trợ deployment. Việc đề cập Artisan trong phần công nghệ sử dụng giúp báo cáo phản ánh đúng rằng hệ thống không chỉ có web UI mà còn có lớp hỗ trợ vận hành.

### 1.4.26. Health check và monitoring endpoints

Health check là các endpoint hoặc cơ chế kiểm tra nhanh trạng thái của các thành phần cốt lõi như database, cache, storage hoặc API. Monitoring endpoints là những điểm kiểm tra hoặc thu thập trạng thái để hỗ trợ quan sát hệ thống trong quá trình chạy thực tế. Trong nhiều hệ thống hiện đại, đây là một lớp quan trọng để phục vụ vận hành.

Việc xuất hiện health check trong dự án cho thấy hệ thống có định hướng vượt ra ngoài mức “chạy được trên máy cá nhân”. Về mặt học thuật, nó cho thấy ứng dụng web hiện đại cần không chỉ có chức năng nghiệp vụ mà còn phải có cơ chế kiểm tra tình trạng sức khỏe của chính nó.

### 1.4.27. Bảng 1.1. Tổng hợp các công nghệ chính của đề tài

Bảng 1.1 dưới đây tóm tắt một số công nghệ và công cụ chính được sử dụng trong dự án cùng với vai trò nổi bật của chúng.

| Công nghệ / Công cụ | Nhóm | Vai trò chính trong hệ thống |
|---|---|---|
| PHP | Backend | Ngôn ngữ nền xử lý nghiệp vụ phía server |
| Laravel 11 | Framework | Tổ chức ứng dụng web, routing, middleware, validation, ORM |
| Blade | Template Engine | Render giao diện server-side theo layout và partial |
| JavaScript | Frontend | Tạo tương tác động trên giao diện |
| Vue.js | Frontend Library | Xây dựng component giao diện và các vùng tương tác |
| Bootstrap | CSS Framework | Tạo layout và thành phần giao diện nhanh |
| Tailwind CSS | Utility CSS | Chuẩn hóa và mở rộng phong cách tổ chức giao diện |
| Vite | Build Tool | Build và tối ưu frontend assets |
| MySQL | Database | Lưu trữ dữ liệu quan hệ của hệ thống |
| Sanctum | API Auth | Quản lý xác thực API theo token |
| Spatie Permission | Authorization | Quản lý vai trò và quyền truy cập |
| Socialite | Authentication | Hỗ trợ đăng nhập xã hội |
| DOMPDF | Document Export | Xuất dữ liệu hoặc tài liệu ở dạng PDF |
| Swagger | API Docs | Tài liệu hóa endpoint và contract API |
| DBeaver | Database Tool | Quản trị và kiểm tra cơ sở dữ liệu |
| VS Code | IDE | Phát triển và chỉnh sửa mã nguồn |
| Git/GitHub | Version Control | Quản lý lịch sử thay đổi và cộng tác mã nguồn |

## 1.5. Phân tích các website tham chiếu và bài học thiết kế

### 1.5.1. Website tham chiếu Thế Giới Di Động

Thế Giới Di Động là một ví dụ tham chiếu hữu ích cho đề tài vì đây là website bán thiết bị công nghệ theo mô hình B2C có quy mô lớn và có cấu trúc tương đối rõ ràng. Từ góc nhìn người dùng, website này không đơn thuần đóng vai trò trưng bày sản phẩm mà còn là một môi trường hỗ trợ ra quyết định mua hàng. Người dùng có thể tiếp cận sản phẩm từ nhiều hướng như trang chủ, danh mục, tìm kiếm, gợi ý hoặc các khối nội dung liên quan.

Một điểm đáng chú ý về mặt lý thuyết là cách tổ chức thông tin trên các trang sản phẩm. Thông tin giá bán, khuyến mãi, trạng thái còn hàng, đánh giá, thông số nổi bật, hình ảnh và các đề xuất liên quan thường được đặt ở vị trí rất dễ nhìn. Điều này phản ánh một nguyên tắc quan trọng của UX trong thương mại điện tử: không phải mọi thông tin đều có giá trị như nhau; hệ thống cần ưu tiên hiển thị những dữ liệu có ảnh hưởng trực tiếp tới quyết định mua hàng.

Ngoài ra, website dạng này còn cho thấy vai trò rất lớn của hình ảnh và niềm tin trực quan. Hình ảnh lớn, rõ, nhiều góc chụp, cùng với các khối đánh giá và mô tả ngắn giúp người dùng nhanh chóng hình thành nhận định ban đầu về sản phẩm. Đối với nhóm hàng công nghệ, nơi người dùng thường phải so sánh nhiều model có cấu hình gần nhau, giao diện rõ ràng và có thứ bậc thông tin hợp lý là một lợi thế rất lớn.

Một bài học khác có thể rút ra là website không thể xem như một danh sách sản phẩm đơn thuần. Nó cần đóng vai trò như một “không gian hỗ trợ lựa chọn”, nơi khách hàng được dẫn dắt qua nhiều lớp thông tin từ tổng quan đến chi tiết, từ hình ảnh đến giá, từ review đến gợi ý sản phẩm tương tự. Đây là tư duy rất phù hợp với đề tài đang thực hiện.

### 1.5.2. Website tham chiếu FPT Shop

FPT Shop là một website tham chiếu khác có giá trị lớn về mặt lý thuyết, đặc biệt ở cách kết hợp giữa bán hàng và truyền thông nội dung. Trên nhiều website dạng này, storefront không chỉ bao gồm danh mục sản phẩm và trang chi tiết, mà còn tích hợp nhiều block khuyến mãi, banner, landing page, chương trình ưu đãi và nội dung định hướng hành vi người dùng.

Điều đó cho thấy website thương mại điện tử hiện đại không thể chỉ được nhìn như một ứng dụng CRUD sản phẩm. Nó còn phải hỗ trợ bài toán marketing, điều hướng hành vi, trình bày ưu đãi và tổ chức nội dung truyền thông. Nói cách khác, ranh giới giữa website bán hàng và website nội dung ngày càng mờ đi, đặc biệt trong bối cảnh cạnh tranh thương mại điện tử hiện nay.

Về mặt học thuật, đây là cơ sở để lý giải vì sao một website thương mại điện tử cần có thêm các module quản trị bài viết, video, slider, banner hoặc các thành phần truyền thông khác. Khu vực admin không chỉ phục vụ việc thêm sản phẩm mà còn phải cung cấp công cụ để cập nhật nội dung marketing, điều chỉnh khuyến mãi và điều hướng người dùng tới các khu vực quan trọng.

Từ website tham chiếu này, bài học rút ra cho đề tài là hệ thống cần được tổ chức sao cho storefront và quản trị nội dung có thể cùng tồn tại trong một kiến trúc thống nhất. Đây là lý do mà các chức năng CMS, slider, video và thông tin truyền thông nên được xem là một phần hợp lệ của phạm vi đề tài, chứ không phải phần mở rộng không liên quan.

### 1.5.3. Website tham chiếu CellphoneS

CellphoneS là một ví dụ tiêu biểu cho cách tổ chức storefront giàu tính so sánh và khám phá sản phẩm. Trong lĩnh vực thiết bị công nghệ, hành vi mua hàng rất hiếm khi diễn ra ngay sau lần xem đầu tiên. Người dùng thường cần lọc theo nhu cầu, so sánh giữa nhiều mẫu máy, đọc review, xem mức giá và cân nhắc các ưu đãi đi kèm trước khi quyết định.

Từ góc nhìn đó, website dạng này làm nổi bật tầm quan trọng của các chức năng như filter, search suggest, compare, recommendation và khối gợi ý sản phẩm liên quan. Những chức năng này không phải là phần “trang trí” của giao diện, mà là lớp hỗ trợ quyết định mua hàng. Nếu một website công nghệ chỉ có danh mục và chi tiết sản phẩm nhưng thiếu công cụ khám phá, người dùng sẽ gặp khó khăn hơn khi lựa chọn.

Một điểm quan trọng khác là khả năng dẫn dắt người dùng từ hành vi so sánh sang hành vi mua hàng. Khi hệ thống cung cấp đủ công cụ để thu hẹp lựa chọn và trình bày ưu tiên thông tin một cách hợp lý, khách hàng sẽ có xu hướng tiến gần hơn tới quyết định cuối cùng. Vì vậy, đề tài có thể khai thác các tính năng như so sánh sản phẩm, gợi ý tìm kiếm, recommendation và review như một phần có giá trị thực tiễn cao chứ không chỉ là tính năng bổ sung.

### 1.5.4. So sánh và rút ra bài học thiết kế từ các website tham chiếu

Khi đặt ba website tham chiếu cạnh nhau, có thể nhận ra một số điểm chung quan trọng. Thứ nhất, cả ba đều coi storefront là một môi trường hỗ trợ ra quyết định chứ không chỉ là nơi trưng bày sản phẩm. Thứ hai, hình ảnh, giá bán, ưu đãi, thông số nổi bật và khối gợi ý đều được ưu tiên ở vị trí dễ quan sát. Thứ ba, các công cụ hỗ trợ khám phá như tìm kiếm, lọc, so sánh, đánh giá và recommendation đóng vai trò rất lớn.

Điểm chung tiếp theo là sự liên kết chặt giữa website bán hàng và quản trị nội dung. Một hệ thống thương mại điện tử thực tế không chỉ cần quản lý sản phẩm mà còn phải điều phối banner, bài viết, video, flash sale, chương trình ưu đãi và nhiều khối truyền thông khác. Điều này giải thích vì sao trong các đề tài thương mại điện tử có chiều sâu hơn, phần quản trị nội dung cần được xem là một thành phần hợp lệ của hệ thống.

Một bài học nữa là vai trò của khách vãng lai. Cả ba nhóm website tham chiếu đều cho phép người dùng duyệt sản phẩm và nghiên cứu rất sâu trước khi đăng nhập. Điều đó cho thấy trong phân tích hệ thống, khách vãng lai nên được xem là một tác nhân riêng với phạm vi thao tác khác khách hàng đã đăng nhập. Nếu không làm rõ điều này, mô tả ca sử dụng rất dễ bị sai lệch.

Từ các website tham chiếu trên, đề tài có thể rút ra định hướng thiết kế gồm: ưu tiên khả năng khám phá sản phẩm, tổ chức giao diện theo hành vi quyết định mua hàng, kết hợp chặt giữa bán hàng và nội dung truyền thông, phân biệt rõ các nhóm tác nhân và bảo đảm trải nghiệm người dùng đủ thuận tiện trên cả storefront lẫn khu vực quản trị.

### 1.5.5. Ý nghĩa của việc tham chiếu các website thực tế đối với đề tài

Việc tham chiếu các website thực tế có ý nghĩa rất lớn đối với một đồ án phát triển hệ thống. Nếu chỉ dựa trên lý thuyết sách vở, phần phân tích và thiết kế có thể đúng về mặt khái niệm nhưng vẫn thiếu cảm giác thực tế. Ngược lại, khi quan sát cách các website thật tổ chức storefront, ưu tiên nội dung, dẫn dắt hành vi và kết nối với quản trị nội dung, người thực hiện đề tài sẽ có thêm cơ sở để đưa ra quyết định phù hợp hơn.

Từ góc nhìn học thuật, tham chiếu website thực tế không có nghĩa là sao chép giao diện hoặc chức năng nguyên xi. Mục đích chính là rút ra nguyên tắc thiết kế, cách tổ chức thông tin và bài học về hành vi người dùng. Đây là điểm rất phù hợp với báo cáo đồ án vì giúp chứng minh rằng hệ thống được xây dựng trên cơ sở quan sát thực tế chứ không chỉ dựa trên suy luận chủ quan.

Đối với đề tài này, việc tham chiếu các website cùng lĩnh vực còn giúp xác nhận rằng những thành phần như gợi ý tìm kiếm, so sánh sản phẩm, quản trị nội dung, hình ảnh trực quan, khuyến mãi và phân tách nhóm tác nhân đều là những nội dung hợp lý và có giá trị ứng dụng, chứ không phải phần mở rộng rời rạc.

## 1.6. Ưu điểm và hạn chế của một số công nghệ cốt lõi

### 1.6.1. Ưu điểm và hạn chế của HTML

Ưu điểm lớn nhất của HTML là tính chuẩn hóa và tính phổ quát. Hầu như mọi trình duyệt hiện đại đều hỗ trợ HTML, nhờ đó nội dung có thể được hiển thị ổn định trên nhiều thiết bị và môi trường khác nhau. HTML cũng rất phù hợp để biểu diễn các thành phần cốt lõi của website thương mại điện tử như tiêu đề sản phẩm, mô tả, bảng thông số kỹ thuật, form đăng nhập, form checkout, danh sách danh mục và các vùng điều hướng.

Một ưu điểm khác của HTML là hỗ trợ semantic markup. Khi nội dung được tổ chức bằng các thành phần có ngữ nghĩa rõ ràng, cấu trúc trang sẽ dễ đọc hơn với công cụ tìm kiếm, công cụ hỗ trợ truy cập và cả người phát triển. Điều này có giá trị thực tế trong các hệ thống thương mại điện tử vì SEO, khả năng tái sử dụng bố cục và tổ chức nội dung đều phụ thuộc nhiều vào chất lượng cấu trúc HTML.

Tuy nhiên, HTML có hạn chế rõ ràng là chỉ đảm nhiệm vai trò mô tả cấu trúc, không thể tự xử lý nghiệp vụ hoặc hành vi động. Nếu chỉ có HTML, website sẽ dừng lại ở mức hiển thị nội dung mà không có khả năng phản hồi linh hoạt với thao tác người dùng hoặc xử lý quy trình nghiệp vụ phức tạp. Vì vậy, HTML luôn phải đi cùng CSS, JavaScript và lớp xử lý phía server.

### 1.6.2. Ưu điểm và hạn chế của CSS

CSS có ưu điểm nổi bật ở khả năng tách phần trình bày khỏi phần nội dung. Nhờ đó, giao diện có thể được quản lý tập trung, thay đổi theme hoặc điều chỉnh bố cục mà không phải viết lại cấu trúc HTML. Đối với website thương mại điện tử, điều này đặc biệt có giá trị vì hệ thống thường có nhiều màn hình dùng lại các thành phần như card sản phẩm, banner, menu, form, bảng giá, nút hành động và các khối nội dung phụ trợ.

CSS cũng hỗ trợ responsive design rất tốt, giúp website thích nghi trên desktop, tablet và mobile. Trong thương mại điện tử, đây là yếu tố có tác động trực tiếp tới trải nghiệm và tỷ lệ chuyển đổi vì người dùng ngày nay truy cập bằng nhiều loại thiết bị khác nhau. Một bố cục tốt có thể giúp người dùng nhận diện nhanh sản phẩm, giá, nút mua hàng và các vùng thao tác chính.

Tuy nhiên, CSS có hạn chế ở chỗ việc quản lý nhiều selector, nhiều trạng thái hiển thị và nhiều lớp giao diện có thể trở nên phức tạp nếu không có nguyên tắc tổ chức rõ ràng. Ngoài ra, CSS chỉ chịu trách nhiệm trình bày nên không thể giải quyết các vấn đề liên quan đến dữ liệu, logic nghiệp vụ hay xác thực trạng thái người dùng. Vì vậy, CSS là thành phần rất quan trọng của trải nghiệm, nhưng không thể tách rời khỏi HTML, JavaScript và backend.

### 1.6.3. Ưu điểm và hạn chế của JavaScript

Ưu điểm quan trọng nhất của JavaScript là khả năng tạo tương tác động ngay tại phía client. Người dùng có thể gõ vào ô tìm kiếm và nhận gợi ý gần như tức thời, thêm sản phẩm vào giỏ hàng mà không cần tải lại toàn bộ trang, thay đổi thuộc tính sản phẩm và xem phản hồi giao diện ngay lập tức. Điều này làm tăng đáng kể chất lượng trải nghiệm trong website thương mại điện tử.

JavaScript cũng rất linh hoạt khi kết hợp với AJAX để giao tiếp bất đồng bộ với server. Nhờ đó, nhiều phần của hệ thống có thể được cập nhật theo vùng thay vì phải render lại cả trang. Đây là một ưu điểm lớn trong các tính năng như quick cart, search suggest, load comment, tính phí vận chuyển hoặc chatbot.

Tuy nhiên, JavaScript có hạn chế ở chỗ dễ bị lạm dụng nếu đưa quá nhiều logic nghiệp vụ sang phía client. Trong các hệ thống thương mại điện tử, những thông tin quan trọng như giá bán cuối cùng, coupon hợp lệ, trạng thái đơn hàng hoặc quyền truy cập không thể chỉ dựa vào JavaScript mà phải được xác nhận lại ở phía server. Ngoài ra, nếu tổ chức mã phía client thiếu kỷ luật, các đoạn script nhỏ lẻ cũng có thể gây khó bảo trì về sau.

### 1.6.4. Ưu điểm và hạn chế của PHP

PHP có ưu điểm lớn ở tính thực dụng và mức độ phù hợp với môi trường phát triển web. Ngôn ngữ này hỗ trợ tốt xử lý form, session, cookie, truy cập cơ sở dữ liệu, upload file, gửi mail và phản hồi HTTP. Đây là những tác vụ nền tảng rất quan trọng trong một website thương mại điện tử.

Một ưu điểm khác của PHP là có cộng đồng lớn, tài liệu phong phú và hệ sinh thái công cụ trưởng thành. Nhờ đó, việc xây dựng hệ thống trở nên khả thi trong phạm vi đồ án mà vẫn có thể đi theo hướng chuyên nghiệp hơn nếu kết hợp với framework phù hợp. Đối với đề tài này, PHP là lớp thực thi phía server cho toàn bộ nghiệp vụ cốt lõi.

Tuy nhiên, PHP cũng có hạn chế nếu bị sử dụng theo lối viết mã thủ công, không có chuẩn tổ chức. Nếu không có framework hoặc nguyên tắc kiến trúc rõ ràng, mã PHP rất dễ bị dồn logic vào controller hoặc trộn lẫn phần xử lý với phần hiển thị. Điều này làm hệ thống khó mở rộng và dễ phát sinh lỗi khi thêm chức năng mới. Vì vậy, PHP phát huy tốt nhất khi đi cùng một bộ khung tổ chức phù hợp.

### 1.6.5. Ưu điểm và hạn chế của Laravel

Laravel có ưu điểm rất rõ ở khả năng chuẩn hóa cách phát triển ứng dụng web trên nền PHP. Framework này cung cấp sẵn routing, middleware, request validation, Blade, Eloquent ORM, queue, mail và nhiều công cụ khác, giúp rút ngắn thời gian xây dựng các thành phần nền tảng. Đối với website thương mại điện tử, đây là lợi thế lớn vì hệ thống thường phải triển khai đồng thời nhiều lớp như storefront, admin, API, xác thực, phân quyền và dữ liệu.

Một ưu điểm khác của Laravel là khả năng mở rộng theo hướng phân tầng. Dù bản thân framework thường được biết đến qua mô hình MVC, nhưng nó vẫn cho phép tổ chức thêm service layer, support classes, repository và domain objects khá thuận lợi. Điều này rất phù hợp với các hệ thống đang phát triển dần từ mức đơn giản lên mức phức tạp hơn.

Tuy nhiên, Laravel cũng có hạn chế cần nhìn nhận thẳng. Nếu lạm dụng cơ chế tiện lợi sẵn có của framework mà không kiểm soát kiến trúc, dự án vẫn có thể trở nên cồng kềnh, controller phình to hoặc logic bị phân tán không nhất quán. Ngoài ra, với các hệ thống đang chuyển đổi từ legacy, việc đưa Laravel vào không tự động giải quyết toàn bộ vấn đề; vẫn cần chiến lược refactor và kiểm tra chất lượng phù hợp.

### 1.6.6. Ưu điểm và hạn chế của MySQL

MySQL có ưu điểm ở sự ổn định, mức độ phổ biến và tính phù hợp với dữ liệu có cấu trúc rõ ràng. Các bài toán như quản lý sản phẩm, danh mục, thương hiệu, khách hàng, đơn hàng, thanh toán và bài viết đều có thể được biểu diễn tốt bằng mô hình quan hệ trên MySQL. Hệ quản trị này cũng hỗ trợ transaction, index và các thao tác truy vấn cần thiết cho phần lớn hệ thống thương mại điện tử quy mô vừa.

Một lợi thế nữa của MySQL là dễ triển khai trong môi trường học tập và phát triển web phổ biến. Tài liệu, công cụ quản trị và kinh nghiệm cộng đồng đều rất sẵn có, giúp quá trình thiết kế, cài đặt và kiểm tra chất lượng cơ sở dữ liệu thuận tiện hơn.

Tuy nhiên, MySQL cũng có hạn chế nếu thiết kế schema không tốt hoặc truy vấn không được tối ưu. Dữ liệu dư thừa, chỉ mục thiếu hợp lý hoặc transaction không được cân nhắc kỹ có thể ảnh hưởng đến hiệu năng và tính nhất quán của hệ thống. Vì vậy, chọn MySQL là bước đầu phù hợp, nhưng hiệu quả thực tế vẫn phụ thuộc lớn vào cách tổ chức dữ liệu.

### 1.6.7. Ưu điểm và hạn chế của Vue.js

Vue.js có ưu điểm ở cú pháp tương đối dễ tiếp cận, mô hình component rõ ràng và khả năng tích hợp tốt vào các dự án hiện có mà không cần chuyển toàn bộ frontend sang SPA hoàn chỉnh. Đối với các hệ thống Laravel server-rendered, Vue phù hợp để xử lý các vùng tương tác cục bộ như thanh tìm kiếm, menu di động, danh sách sản phẩm hoặc các widget giao diện.

Một điểm mạnh khác của Vue là khả năng kết hợp tốt với các công cụ hiện đại như Vite, Pinia và Vue Router. Điều này giúp frontend có thêm không gian mở rộng khi cần tăng mức độ tương tác mà vẫn không làm phá vỡ toàn bộ cấu trúc server-rendered của ứng dụng.

Tuy nhiên, Vue cũng có hạn chế nếu bị sử dụng nửa vời hoặc không có chiến lược tổ chức component hợp lý. Nếu một phần giao diện dùng Blade, một phần dùng script truyền thống, một phần khác lại dùng Vue mà không có quy ước rõ, frontend có thể trở nên phân tán và khó bảo trì. Vì vậy, Vue phát huy tốt nhất khi được dùng cho đúng phạm vi và có định hướng tích hợp nhất quán.

### 1.6.8. Ưu điểm và hạn chế của Bootstrap

Bootstrap có ưu điểm rất rõ ở khả năng tăng tốc độ phát triển giao diện. Nhờ có hệ thống grid, các thành phần giao diện dựng sẵn và các lớp tiện ích phổ biến, người phát triển có thể nhanh chóng tạo ra giao diện có cấu trúc tương đối ổn định mà không phải xây dựng mọi thứ từ đầu. Đối với một đồ án có nhiều màn hình như storefront, checkout, admin dashboard và form quản trị, Bootstrap là lựa chọn thực dụng.

Một ưu điểm khác là tính quen thuộc. Vì Bootstrap được sử dụng rộng rãi, tài liệu phong phú và dễ tiếp cận, nên quá trình phát triển và chỉnh sửa giao diện thường thuận tiện hơn. Ngoài ra, Bootstrap cũng hỗ trợ responsive khá tốt, giúp website thích nghi với nhiều kích thước màn hình.

Tuy nhiên, hạn chế của Bootstrap là giao diện có thể trở nên “đại trà” nếu chỉ dùng mặc định mà không có sự tùy biến phù hợp. Bên cạnh đó, khi kết hợp Bootstrap với các hệ thống CSS khác, việc quản lý style có thể trở nên phức tạp hơn nếu thiếu quy tắc tổ chức rõ ràng.

### 1.6.9. Ưu điểm và hạn chế của Tailwind CSS

Tailwind CSS có ưu điểm ở cách tiếp cận utility-first, cho phép xây dựng giao diện bằng các lớp tiện ích nhỏ thay vì phải viết quá nhiều CSS riêng. Điều này giúp tăng tốc độ triển khai ở nhiều tình huống và tạo điều kiện cho việc kiểm soát trực tiếp giao diện ngay tại nơi viết markup.

Tailwind cũng phù hợp với xu hướng frontend hiện đại vì hỗ trợ tốt quá trình chuẩn hóa design system và làm việc với bundler như Vite. Tuy nhiên, hạn chế của Tailwind là markup có thể trở nên dài và khó đọc nếu lạm dụng quá nhiều class utility trong cùng một thành phần. Ngoài ra, khi dự án đang có song song Bootstrap, CSS truyền thống và Tailwind, việc phối hợp các phong cách này cần được kiểm soát cẩn thận.

### 1.6.10. Ưu điểm và hạn chế của Laravel Sanctum

Laravel Sanctum có ưu điểm ở sự gọn nhẹ và mức độ tích hợp tốt với hệ sinh thái Laravel. Sanctum phù hợp với các hệ thống cần xác thực API nhưng không muốn sử dụng một cơ chế quá nặng. Việc phát hành và quản lý token bằng Sanctum tương đối thuận tiện trong các bài toán API protected và admin API.

Tuy nhiên, Sanctum cũng có giới hạn. Nếu hệ thống tiến tới quy mô tích hợp phức tạp hơn với nhiều client bên ngoài, yêu cầu chuẩn hóa OAuth sâu hơn hoặc nhiều dạng flow xác thực phức tạp, Sanctum có thể chưa phải lựa chọn tối ưu nhất. Dù vậy, trong phạm vi đồ án và bài toán hiện tại, Sanctum là một giải pháp hợp lý.

### 1.6.11. Ưu điểm và hạn chế của Spatie Laravel Permission

Spatie Laravel Permission có ưu điểm nổi bật ở khả năng triển khai RBAC tương đối nhanh trong Laravel. Gói này hỗ trợ quản lý role, permission, gán quyền cho người dùng và kiểm tra quyền truy cập ở nhiều mức khác nhau. Đối với khu vực admin có nhiều module, đây là một công cụ rất phù hợp.

Tuy nhiên, khi số lượng quyền tăng lớn, việc tổ chức tên quyền, nhóm quyền và bảo trì ma trận phân quyền cũng trở nên phức tạp hơn. Vì vậy, dùng Spatie Permission hiệu quả không chỉ là cài package, mà còn phải có chiến lược đặt tên và tổ chức permission nhất quán theo nghiệp vụ.

### 1.6.12. Ưu điểm và hạn chế của Vite

Vite có ưu điểm ở tốc độ build nhanh, trải nghiệm phát triển tốt và khả năng làm việc thuận tiện với các công nghệ frontend hiện đại. So với nhiều công cụ build cũ hơn, Vite giúp quá trình phát triển frontend mượt hơn, đặc biệt trong môi trường có Vue, CSS framework và nhiều asset cần bundling.

Tuy nhiên, Vite cũng tạo ra thêm một lớp công cụ trong dự án, đòi hỏi người phát triển phải hiểu thêm về môi trường Node.js, npm và quy trình build frontend. Đối với các nhóm quen với Laravel thuần truyền thống, đây có thể là một điểm tăng độ phức tạp, dù lợi ích lâu dài thường là đáng kể.

### 1.6.13. Ưu điểm và hạn chế của jQuery

jQuery có ưu điểm ở sự đơn giản, quen thuộc và tốc độ triển khai nhanh đối với các tương tác giao diện cục bộ. Trong nhiều hệ thống web truyền thống, jQuery giúp xử lý DOM, gửi AJAX request và tổ chức các đoạn script tương đối gọn mà không cần dựng cả một framework frontend phức tạp.

Tuy nhiên, hạn chế của jQuery là khả năng tổ chức ứng dụng lớn không tốt bằng các framework component-based hiện đại. Khi số lượng tương tác tăng lên, mã jQuery dễ bị phân tán và khó kiểm soát hơn nếu không có quy ước rõ ràng. Vì vậy, jQuery phù hợp với những phần giao diện nhỏ hoặc legacy, nhưng không phải là lựa chọn tối ưu cho mọi lớp frontend hiện đại.

### 1.6.14. Ưu điểm và hạn chế của Axios và state management

Axios có ưu điểm ở cú pháp rõ ràng, hỗ trợ promise và thuận tiện cho việc giao tiếp với REST API. Khi kết hợp với một thư viện quản lý state như Pinia, frontend có thể tổ chức dữ liệu dùng chung tốt hơn và giảm bớt việc truyền dữ liệu rời rạc qua nhiều component.

Tuy nhiên, khi thêm Axios, Pinia và Vue Router vào frontend, mức độ phức tạp của phần giao diện cũng tăng theo. Nếu hệ thống chỉ có một vài tương tác đơn giản, việc đưa quá nhiều công cụ vào có thể làm chi phí tổ chức mã nguồn cao hơn mức cần thiết. Vì vậy, hiệu quả của các công cụ này phụ thuộc vào việc sử dụng đúng phạm vi và có chiến lược tổ chức phù hợp.

### 1.6.15. Ưu điểm và hạn chế của event, queue và command-based operation

Event, queue và command-based operation có ưu điểm ở khả năng tách rời các bước xử lý và hỗ trợ mở rộng hệ thống theo hướng module hóa. Khi một hành động phát sinh trong hệ thống, event có thể được phát ra để các listener hoặc job khác tiếp nhận xử lý. Queue giúp đưa các tác vụ nặng ra xử lý nền, còn command cho phép đóng gói các thao tác vận hành hoặc migration thành các lệnh rõ ràng.

Tuy nhiên, nhược điểm của nhóm công cụ này là làm kiến trúc trở nên phức tạp hơn so với mô hình xử lý đồng bộ trực tiếp. Nếu không có tài liệu, kiểm tra chất lượng và quy ước tổ chức tốt, người mới tham gia dự án có thể gặp khó khăn trong việc lần theo luồng xử lý. Vì vậy, đây là nhóm công cụ mạnh nhưng đòi hỏi kỷ luật kiến trúc cao hơn.

## 1.7. Vai trò của nhóm công nghệ trong riêng dự án đang thực hiện

### 1.7.1. Vai trò của HTML và Blade trong dự án

Trong dự án này, HTML giữ vai trò tạo cấu trúc cho các trang storefront và admin được render bằng Blade. Các thành phần như trang chủ, trang danh mục, trang chi tiết sản phẩm, giỏ hàng, checkout, dashboard quản trị và các form quản lý nội dung đều dựa trên lớp cấu trúc này. Nhờ có HTML được tổ chức tương đối rõ, dữ liệu từ controller và service có thể được đưa ra giao diện một cách nhất quán hơn.

Blade đóng vai trò kết nối cấu trúc HTML với dữ liệu động của Laravel. Layout, partials và cơ chế kế thừa giao diện giúp hệ thống tránh lặp mã, thuận tiện hơn cho việc tách các vùng dùng chung như header, footer, sidebar, scripts stack hoặc shell quản trị. Đây là nền tảng quan trọng giúp giao diện vẫn có thể mở rộng được khi số lượng màn hình tăng lên.

### 1.7.2. Vai trò của CSS, Bootstrap và Tailwind trong dự án

CSS trong dự án giữ vai trò tổ chức trải nghiệm nhìn và luồng đọc thông tin. Điều này thể hiện ở cách trình bày card sản phẩm, block flash sale, khu vực tìm kiếm, giao diện checkout, dashboard quản trị và nhiều thành phần thị giác khác. Về mặt thực tiễn, CSS giúp các màn hình có tính thống nhất và hỗ trợ tốt hơn cho trải nghiệm sử dụng.

Bootstrap được dùng như một bộ khung giao diện quen thuộc, giúp tăng tốc độ triển khai các thành phần cơ bản như grid, form, modal, button, bảng và các cấu phần layout. Đây là lựa chọn phù hợp cho một đồ án cần triển khai nhiều chức năng trong thời gian giới hạn mà vẫn phải bảo đảm giao diện đủ ổn định.

Tailwind CSS xuất hiện như một hướng bổ sung hiện đại hơn cho việc chuẩn hóa tài nguyên frontend. Việc dự án có mặt của Tailwind cho thấy hệ thống đang kết hợp giữa các thành phần giao diện truyền thống và công cụ frontend mới hơn, tạo điều kiện cho việc chuẩn hóa dần mà không cần thay toàn bộ nền giao diện hiện có.

### 1.7.3. Vai trò của JavaScript và Vue.js trong dự án

JavaScript trong dự án được sử dụng như lớp tăng cường tương tác, đặc biệt ở các luồng search suggest, add-cart-ajax, quick cart, hover cart, tính phí vận chuyển, load comment và một số vùng tương tác động khác. Điều này phù hợp với mô hình của hệ thống, nơi backend vẫn là trung tâm xử lý, còn JavaScript chịu trách nhiệm làm cho giao diện phản hồi nhanh hơn ở những điểm cần thiết.

Vue.js được dùng trong một số component giao diện và hỗ trợ cho mô hình frontend theo hướng hiện đại hơn. Vai trò của Vue trong dự án không phải là thay thế hoàn toàn Blade, mà là bổ sung vào những vùng cần tính tái sử dụng và tương tác cao hơn. Đây là cách kết hợp hợp lý trong phạm vi đồ án, vì nó vừa tận dụng được mô hình server-rendered của Laravel, vừa mở ra khả năng tổ chức component cho frontend.

### 1.7.4. Vai trò của PHP và Laravel trong dự án

PHP là lớp ngôn ngữ nền cho toàn bộ xử lý phía server của dự án. Các thao tác như nhận request, kiểm tra dữ liệu, duy trì session khách hàng, xử lý checkout, quản lý đơn hàng, phân quyền admin, xây API và chạy console command đều dựa trên PHP. Nói cách khác, PHP là lớp thực thi mà nếu bỏ đi thì toàn bộ hệ thống sẽ không còn khả năng vận hành nghiệp vụ.


### 1.7.5. Vai trò của MySQL và dữ liệu quan hệ trong dự án

MySQL là nơi lưu trữ dữ liệu cho toàn bộ các phân hệ chính của hệ thống như catalog, khách hàng, đơn hàng, thanh toán, nội dung, khuyến mãi và dữ liệu hỗ trợ khác. Do đây là một website thương mại điện tử có nhiều mối quan hệ rõ ràng giữa các thực thể, việc sử dụng cơ sở dữ liệu quan hệ là một lựa chọn phù hợp.

Một điểm đáng chú ý của dự án là dữ liệu hiện mang tính chuyển tiếp giữa lớp dữ liệu cũ và lớp lược đồ được chuẩn hóa dần. Từ góc nhìn lý thuyết, điều này làm cho phần cơ sở dữ liệu của đề tài trở nên sâu hơn, vì hệ thống không đơn thuần chỉ triển khai từ đầu trên một lược đồ lý tưởng, mà còn phản ánh bài toán thực tế của việc duy trì dữ liệu cũ đồng thời hướng tới kiến trúc tốt hơn.

### 1.7.6. Vai trò của Laravel Sanctum và Spatie Permission trong dự án

Sanctum giữ vai trò hỗ trợ xác thực cho các endpoint API cần kiểm soát truy cập. Trong bối cảnh dự án có cả public API, protected API và admin API, việc sử dụng Sanctum giúp hệ thống quản lý token truy cập theo cách gọn và phù hợp với phạm vi đề tài.

Spatie Laravel Permission giữ vai trò tổ chức quyền truy cập cho khu vực quản trị. Thay vì gộp toàn bộ quyền vào một vai trò admin duy nhất, hệ thống có thể tách quyền theo từng nhóm chức năng như sản phẩm, nội dung, đơn hàng, người dùng, vai trò hoặc hệ thống. Từ góc độ học thuật, điều này làm cho phần phân quyền của dự án bám thực tế hơn nhiều so với mô hình xác thực đơn giản.

### 1.7.7. Vai trò của kiểm tra chất lượng trong dự án

Quy trình kiểm soát chất lượng trong dự án có vai trò như một lớp bảo vệ giúp giảm nguy cơ lỗi quay trở lại sau mỗi lần chỉnh sửa. Đối với một hệ thống có nhiều phân hệ như storefront, admin, API, report, mail và chatbot, chỉ một thay đổi nhỏ cũng có thể ảnh hưởng tới nhiều luồng khác nhau. Vì vậy, kiểm tra chất lượng là yếu tố rất quan trọng để giữ hệ thống ổn định hơn trong quá trình phát triển.


### 1.7.8. Vai trò của routes, controllers và requests trong dự án

Routes là lớp xác định các điểm vào của hệ thống. Chúng quyết định URL nào sẽ gọi đến controller nào, middleware nào được áp dụng và các nhóm chức năng nào được tổ chức cùng nhau. Trong dự án này, việc tách riêng route storefront, route admin và route API phản ánh khá rõ cấu trúc ba mặt triển khai của hệ thống.

Controllers giữ vai trò điều phối request, gọi các lớp nghiệp vụ và trả response phù hợp. Về mặt học thuật, controller không nên chứa toàn bộ logic mà chỉ nên đóng vai trò trung gian giữa HTTP layer và business logic. Điều này đặc biệt quan trọng trong hệ thống có nhiều nghiệp vụ như thương mại điện tử.

Requests validation giữ vai trò chuẩn hóa dữ liệu đầu vào. Thay vì để controller tự kiểm tra từng điều kiện nhỏ, request classes gom các quy tắc kiểm tra thành một lớp riêng, giúp mã nguồn rõ hơn và giảm lỗi do validation phân tán. Trong phạm vi dự án, sự có mặt của nhiều request classes phản ánh xu hướng tổ chức mã nguồn theo hướng chặt chẽ hơn.

### 1.7.9. Vai trò của services, persistence và support classes trong dự án

Services trong dự án là nơi xử lý nhiều nghiệp vụ quan trọng như sản phẩm, giỏ hàng, checkout, báo cáo, chatbot hoặc hỗ trợ vận hành. Việc tách service ra khỏi controller làm cho kiến trúc trở nên hợp lý hơn, đồng thời giúp việc kiểm tra chất lượng và tái sử dụng logic thuận tiện hơn.

Persistence và các repository hoặc model chuẩn hóa mới giúp hệ thống tiến gần hơn tới kiến trúc nhiều tầng, nơi business logic không phụ thuộc quá trực tiếp vào cách lưu trữ dữ liệu. Điều này có giá trị lớn trong bối cảnh dự án đang tồn tại song song lớp legacy và lớp schema chuẩn hóa mới.

Support classes giữ vai trò gom các quy tắc dùng chung như xác thực storefront, phân quyền admin, điều hướng hoặc tổ chức workspace. Đây là lớp thường bị bỏ qua trong các báo cáo đồ án, nhưng lại rất đáng chú ý vì nó cho thấy hệ thống đã đi xa hơn mô hình CRUD thuần.

### 1.7.10. Vai trò của cache, health check và tài liệu API trong dự án

Cache trong dự án giúp tăng tốc độ phản hồi cho các dữ liệu được truy cập thường xuyên như sản phẩm, danh mục, thương hiệu hoặc thống kê tổng hợp. Việc có cache không chỉ là dấu hiệu của tối ưu kỹ thuật, mà còn cho thấy hệ thống có ý thức về hiệu năng và trải nghiệm sử dụng.

Health check giúp theo dõi trạng thái hoạt động của các thành phần cốt lõi như database, cache và storage. Đây là lớp hỗ trợ vận hành có giá trị thực tế cao vì nó giúp kiểm tra nhanh tình trạng hệ thống mà không cần đi sâu vào từng phần mã nguồn.

Tài liệu API giúp mô tả endpoint, request và response một cách có hệ thống. Trong bối cảnh dự án có public API, protected API và admin API, tài liệu hóa API là thành phần quan trọng để hệ thống có thể được hiểu, kiểm tra chất lượng và mở rộng dễ hơn trong tương lai.

### 1.7.11. Vai trò của mail workflow, chatbot và console commands trong dự án

Mail workflow trong dự án phản ánh việc hệ thống có các kênh tương tác ngoài giao diện web trực tiếp. Email xác nhận, khôi phục mật khẩu hoặc thông báo khuyến mãi đều là những thành phần gần với ứng dụng vận hành thực tế hơn so với một website demo cơ bản.

Chatbot và chat inbox phản ánh lớp hỗ trợ người dùng và hỗ trợ sau tương tác. Đây là một điểm mở rộng có giá trị vì nó cho thấy hệ thống không chỉ tập trung vào bán hàng mà còn quan tâm tới dịch vụ và hỗ trợ người dùng trong quá trình sử dụng.

Console commands cho thấy hệ thống có thêm lớp hỗ trợ vận hành, tối ưu, chuẩn hóa dữ liệu hoặc kiểm tra bảo mật. Từ góc nhìn học thuật, sự xuất hiện của command riêng cho migration, optimize, security hoặc deployment là một điểm cộng vì nó phản ánh tư duy phát triển phần mềm theo vòng đời rộng hơn.

### 1.7.12. Vai trò của analytics, report và dữ liệu hành vi trong dự án

Analytics và report trong dự án giúp hệ thống không chỉ dừng ở quản lý dữ liệu vận hành mà còn bước sang lớp hỗ trợ ra quyết định. Các thống kê như sản phẩm được quan tâm, hiệu quả khám phá sản phẩm, chỉ số tổng hợp hoặc trạng thái hệ thống giúp người quản trị nhìn được bức tranh rộng hơn thay vì chỉ thao tác trên từng bản ghi đơn lẻ.

Dữ liệu hành vi là nền tảng để xây dựng recommendation, product discovery và cải thiện giao diện theo hành vi thực tế. Điều này có giá trị rất lớn trong website bán thiết bị công nghệ, nơi người dùng thường trải qua một quá trình tìm hiểu dài trước khi mua. Từ góc độ đồ án, đây là điểm giúp hệ thống có chiều sâu hơn so với mô hình bán hàng cơ bản.

### 1.7.13. Vai trò của lớp legacy và chiến lược chuẩn hóa trong dự án

Một điểm rất đặc thù của dự án là sự tồn tại đồng thời giữa lớp legacy và lớp chuẩn hóa mới. Điều này phản ánh đúng bài toán thực tế của nhiều hệ thống: không thể viết lại toàn bộ ngay lập tức, mà phải vừa giữ hệ thống cũ tiếp tục vận hành, vừa từng bước chuẩn hóa về dữ liệu, route, validation, service và phân quyền.

Từ góc độ kỹ thuật, đây là một thách thức nhưng cũng là một giá trị học thuật. Nó cho phép đề tài không chỉ trình bày một kiến trúc “đẹp trên giấy”, mà còn cho thấy cách hệ thống chuyển tiếp từ trạng thái cũ sang trạng thái tốt hơn. Đây là một chủ đề rất đáng giá trong báo cáo vì nó thể hiện tư duy tổ chức phần mềm thay vì chỉ hiện thực tính năng bề mặt.

## 1.8. Nhận xét về mức độ phù hợp của stack công nghệ đối với đề tài

### 1.8.1. Sự phù hợp với phạm vi đồ án

Stack công nghệ được lựa chọn trong dự án có mức độ phù hợp khá cao với phạm vi của một đồ án tốt nghiệp. Laravel 11 cung cấp đủ nền tảng để triển khai routing, middleware, xác thực, phân quyền, Blade, ORM và API trong cùng một hệ thống. Điều này giúp giảm bớt gánh nặng xây dựng hạ tầng cơ bản từ đầu và cho phép tập trung nhiều hơn vào nghiệp vụ thương mại điện tử.

Việc kết hợp Blade với một số thành phần JavaScript và Vue.js cũng là lựa chọn hợp lý trong bối cảnh đồ án. Nếu đi theo mô hình SPA hoàn chỉnh, mức độ phức tạp của frontend, routing client-side, state management và tổ chức API sẽ tăng đáng kể. Trong khi đó, mô hình server-rendered kết hợp tương tác cục bộ vẫn đủ để thể hiện chiều sâu kỹ thuật mà không làm phạm vi phát triển bị vượt quá khả năng kiểm soát.

### 1.8.2. Sự phù hợp với bài toán website bán thiết bị công nghệ

Đối với bài toán bán thiết bị công nghệ, hệ thống cần xử lý đồng thời nhiều nhóm dữ liệu và nhiều luồng nghiệp vụ: danh mục, thương hiệu, sản phẩm, biến thể, hình ảnh, tìm kiếm, so sánh, giỏ hàng, đơn hàng, nội dung, phân quyền và báo cáo. Stack PHP, Laravel, MySQL, Blade, JavaScript, Vue, Bootstrap và Sanctum là đủ để bao phủ hầu hết các yêu cầu này trong phạm vi dự án.

Ngoài ra, đặc thù của lĩnh vực công nghệ là người dùng thường cần nhiều thông tin, nhiều hình ảnh, khả năng so sánh và trải nghiệm khám phá sản phẩm. Mô hình giao diện hiện tại kết hợp server-rendered và tương tác động cục bộ rất phù hợp với kiểu hành vi này vì nó vừa cho phép tổ chức trang rõ ràng, vừa đủ linh hoạt cho các thao tác cần phản hồi nhanh.

### 1.8.3. Khả năng mở rộng và định hướng phát triển tiếp theo

Một ưu điểm của stack công nghệ hiện tại là vẫn còn nhiều không gian để mở rộng. Từ nền Laravel hiện có, hệ thống có thể phát triển thêm API versioning rõ ràng hơn, mở rộng mobile client, tăng cường recommendation, hoàn thiện reporting hoặc tiếp tục chuẩn hóa lớp domain và persistence. Điều đó cho thấy lựa chọn công nghệ hiện tại không chỉ phù hợp cho hiện tại mà còn tạo nền cho giai đoạn mở rộng sau này.

Tuy nhiên, cũng cần nhìn nhận rằng khi hệ thống ngày càng lớn, yêu cầu về tổ chức mã nguồn, chuẩn hóa dữ liệu, tách biệt trách nhiệm giữa các tầng và kiểm tra chất lượng sẽ trở nên khắt khe hơn. Vì vậy, sự phù hợp của stack công nghệ không chỉ đến từ bản thân công cụ, mà còn phụ thuộc vào cách người phát triển áp dụng chúng một cách nhất quán.

### 1.8.4. Những điểm cần tiếp tục cải thiện trong quá trình phát triển

Mặc dù stack công nghệ hiện tại có mức độ phù hợp cao với phạm vi đồ án, hệ thống vẫn còn những điểm cần tiếp tục cải thiện nếu muốn tiến gần hơn tới một ứng dụng hoàn thiện. Thứ nhất là mức độ chuẩn hóa kiến trúc. Khi dự án phát triển theo thời gian, việc tồn tại song song giữa lớp legacy và lớp chuẩn hóa mới có thể làm tăng độ phức tạp trong bảo trì nếu không tiếp tục được tổ chức lại một cách có hệ thống.

Thứ hai là sự nhất quán của frontend. Khi một hệ thống sử dụng đồng thời Blade, JavaScript truyền thống, Bootstrap, Tailwind và Vue, lợi ích là linh hoạt, nhưng rủi ro là giao diện và cách tổ chức tài nguyên có thể bị phân tán nếu thiếu quy ước dài hạn. Vì vậy, cần tiếp tục chuẩn hóa cách dùng component, partial, assets và các vùng tương tác động để tránh tích lũy nợ kỹ thuật.


### 1.8.5. Rủi ro kỹ thuật khi phát triển hệ thống thương mại điện tử

Một hệ thống thương mại điện tử luôn tồn tại nhiều rủi ro kỹ thuật hơn so với các website thông tin đơn giản. Thứ nhất là rủi ro liên quan đến dữ liệu và tính nhất quán. Các luồng như giỏ hàng, coupon, checkout, thanh toán và đơn hàng đều có liên quan đến nhiều bảng dữ liệu và nhiều trạng thái trung gian. Nếu thiết kế transaction hoặc validation không chặt chẽ, rất dễ phát sinh sai lệch dữ liệu.

Thứ hai là rủi ro liên quan đến phân quyền và bảo mật. Khi hệ thống có cả storefront, admin và API, phạm vi truy cập trở nên phức tạp hơn. Nếu chỉ kiểm tra xác thực mà không kiểm tra quyền chi tiết, các endpoint nhạy cảm có thể bị truy cập sai phạm vi. Đây là lý do mà phần phân quyền, middleware và kiểm soát API cần được chú trọng.

Thứ ba là rủi ro do thay đổi giao diện ảnh hưởng đến nghiệp vụ. Trong thương mại điện tử, nhiều phần giao diện như search suggest, quick cart, compare, checkout gate hoặc chatbot thực chất gắn trực tiếp với session, request, validation và route điều hướng. Vì vậy, thay đổi nhỏ ở UI cũng có thể tạo ra regression nếu không có kiểm tra chất lượng phù hợp.

Thứ tư là rủi ro do mở rộng hệ thống khi mã nguồn chưa được chuẩn hóa đầy đủ. Nếu logic bị dồn quá nhiều vào controller hoặc lẫn giữa legacy và lớp chuẩn hóa mới mà không có chiến lược refactor rõ ràng, chi phí bảo trì sẽ tăng lên nhanh chóng. Từ góc nhìn học thuật, việc nhận diện các rủi ro này giúp báo cáo có chiều sâu thực tiễn hơn và tránh cảm giác chỉ mô tả những gì hệ thống làm được.

### 1.8.6. Tính phù hợp của mô hình chuyển tiếp giữa hệ thống cũ và hiện đại

Nếu chỉ nhìn theo tiêu chuẩn "sạch" về mặt kiến trúc, việc hệ thống tồn tại song song lớp dữ liệu cũ và lớp chuẩn hóa mới có thể bị xem là chưa lý tưởng. Tuy nhiên, từ góc nhìn thực tế phát triển phần mềm, mô hình chuyển tiếp này lại rất phù hợp với nhiều bài toán thực. Nó cho phép hệ thống tiếp tục vận hành các luồng cũ trong khi dần chuẩn hóa các vùng quan trọng như catalog, checkout, API, báo cáo, phân quyền và kiểm tra chất lượng.

Trong phạm vi đồ án, mô hình chuyển tiếp này còn có một lợi thế học thuật đáng kể: nó tạo điều kiện để phân tích cả bài toán kế thừa lẫn bài toán cải tiến. Người thực hiện không chỉ học cách xây dựng chức năng mới, mà còn học cách làm việc với hệ thống đang tồn tại, đánh giá chỗ nào nên giữ, chỗ nào nên tái cấu trúc và chỗ nào cần chuẩn hóa từng bước.

Vì vậy, thay vì xem tính chất chuyển tiếp này là một điểm yếu thuần túy, báo cáo có thể nhìn nó như một đặc điểm thực tế của hệ thống. Từ đó, phần phân tích và thiết kế sẽ phản ánh đúng hơn bối cảnh triển khai của dự án.

### 1.8.7. Giá trị học thuật và giá trị thực tiễn của chương cơ sở lý thuyết

Một chương cơ sở lý thuyết tốt không chỉ có nhiệm vụ “đủ trang”, mà còn phải tạo được nền cho các chương sau. Với đề tài này, giá trị học thuật của chương nằm ở việc hệ thống hóa các khái niệm về thương mại điện tử, kiến trúc web, dữ liệu, bảo mật, API, UX, analytics và các công nghệ phát triển hiện đại. Đây là phần giúp báo cáo có chiều sâu khái niệm và tránh bị biến thành phần mô tả thuần túy.

Về mặt thực tiễn, chương cơ sở lý thuyết cũng đã được gắn chặt với stack công nghệ và cấu trúc triển khai thực tế của mã nguồn: Laravel 11, Blade, Vue, MySQL, Sanctum, phân quyền, quy trình thư điện tử, chatbot, cache, health check, phân tích dữ liệu hành vi, di trú dữ liệu cũ và hệ thống lệnh hỗ trợ vận hành. Nhờ đó, chương này không bị tách rời khỏi dự án mà đóng vai trò như cầu nối giữa nền tảng lý thuyết và việc triển khai ở các phần sau của báo cáo.

## 1.9. Kết luận chương

Chương này đã trình bày các cơ sở lý thuyết quan trọng phục vụ cho việc xây dựng website thương mại điện tử bán thiết bị công nghệ. Nội dung chương bao gồm các khái niệm cơ bản liên quan đến đề tài, các lý thuyết nền tảng trong phát triển ứng dụng web, nhóm công nghệ và ngôn ngữ lập trình được sử dụng, phân tích các website tham chiếu, đánh giá ưu điểm và hạn chế của một số công nghệ cốt lõi, cũng như vai trò của các công nghệ đó trong chính dự án đang thực hiện.

Việc mở rộng cơ sở lý thuyết theo hướng bám sát bài toán và bám sát stack công nghệ thực tế giúp chương này không chỉ đủ dung lượng theo form báo cáo, mà còn có giá trị làm nền cho các chương sau. Từ đây, báo cáo có thể chuyển sang phần phân tích và thiết kế hệ thống với một nền tảng học thuật và kỹ thuật rõ ràng hơn.

---

# CHƯƠNG 2. PHÂN TÍCH THIẾT KẾ HỆ THỐNG

## 2.1. Khảo sát thực trạng

### 2.1.1. Khảo sát giao diện khách hàng của hệ thống

Sau khi xác định được nền tảng lý thuyết ở Chương 1, bước đầu tiên của phần phân tích thiết kế là khảo sát thực trạng hệ thống đang có. Đối với đề tài này, storefront là mặt sử dụng quan trọng nhất vì đây là nơi khách vãng lai và khách hàng trực tiếp tương tác với hệ thống để xem sản phẩm, tìm kiếm, thêm giỏ hàng, thanh toán và theo dõi đơn hàng.

Qua khảo sát cấu trúc route và giao diện thực tế trong mã nguồn, phân hệ giao diện khách hàng hiện đã có tương đối đầy đủ các chức năng cốt lõi của một website thương mại điện tử bán thiết bị công nghệ. Người dùng có thể truy cập trang chủ, xem danh mục, xem sản phẩm theo thương hiệu, tìm kiếm theo từ khóa, xem chi tiết sản phẩm, so sánh sản phẩm, tương tác với giỏ hàng và đi vào luồng checkout. Ngoài ra, hệ thống còn có các phần mở rộng như gợi ý tìm kiếm động, flash sale, recommendation, bình luận, đánh giá và chatbot hỗ trợ.

Một điểm đáng chú ý là phân hệ giao diện khách hàng của dự án không chỉ dừng ở mô hình liệt kê sản phẩm đơn giản. Giao diện được tổ chức theo nhiều lớp nội dung như hero, khối khuyến mãi, sản phẩm nổi bật, gợi ý tìm kiếm và các khối điều hướng nhanh. Điều đó cho thấy hệ thống đã được phát triển theo hướng bám gần hơn với các website thương mại điện tử thực tế, thay vì chỉ dừng ở mức mô phỏng chức năng tối thiểu.

Xét từ góc nhìn kiến trúc, phân hệ giao diện khách hàng hiện là sự kết hợp giữa Blade server-rendered, các partial giao diện dùng chung và một số thành phần JavaScript hoặc Vue ở các vùng cần tương tác động. Đây là một cách tổ chức phù hợp trong phạm vi đồ án vì vừa kiểm soát được mức độ phức tạp, vừa cho phép triển khai những tương tác cần phản hồi nhanh như tìm kiếm gợi ý, quick cart hoặc chatbot.

### 2.1.2. Khảo sát khu vực quản trị

Khu vực quản trị là mặt thứ hai của hệ thống và có vai trò trung tâm đối với vận hành dữ liệu. Không giống storefront chỉ tập trung vào trải nghiệm mua hàng, admin chịu trách nhiệm quản lý sản phẩm, nội dung, đơn hàng, người dùng, quyền truy cập, báo cáo và nhiều tác vụ hỗ trợ nội bộ khác.

Qua khảo sát route `routes/web/admin.php`, controller quản trị và các view trong `resources/views/admin`, có thể thấy khu vực quản trị của dự án không phải chỉ là một tập hợp form CRUD rời rạc. Thay vào đó, nó đã được tổ chức thành một không gian làm việc tương đối rõ với dashboard, sidebar, quick search, profile, settings và các module theo nghiệp vụ như catalog, content, orders, users, roles, reports và chat inbox.

Một đặc điểm quan trọng của khu vực quản trị là cơ chế phân quyền được áp dụng khá rõ ràng. Hệ thống sử dụng middleware và gói phân quyền để kiểm soát khả năng truy cập từng nhóm chức năng. Điều này có ý nghĩa lớn vì trong môi trường quản trị, không phải người dùng nội bộ nào cũng nên có cùng một mức quyền thao tác. Việc khảo sát được cơ chế này là cơ sở để phần phân tích ca sử dụng và phân quyền ở các mục sau bám đúng thực tế hơn.

Ngoài ra, khu vực quản trị của hệ thống còn phản ánh khá rõ tính chất chuyển tiếp của kiến trúc hiện tại. Một số mô-đun đã được chuẩn hóa tốt hơn và có tổ chức hiện đại hơn, trong khi một số luồng dữ liệu cũ vẫn được duy trì nhưng đã được gia cố để giảm lỗi khi vận hành. Đây là một điểm rất quan trọng về mặt phân tích, vì nó cho thấy hệ thống không được xây dựng lại hoàn toàn từ đầu mà đang trong quá trình cải tiến dần.

### 2.1.3. Khảo sát API và các dịch vụ dùng chung

Bên cạnh hai mặt web là storefront và admin, hệ thống còn cung cấp REST API. Đây là một điểm giúp dự án có chiều sâu hơn so với nhiều đồ án thương mại điện tử chỉ dừng ở route web truyền thống. Qua khảo sát `routes/api.php`, có thể chia API của hệ thống thành ba nhóm chính: public API, protected API và admin API.

Public API phục vụ các dữ liệu công khai như danh sách sản phẩm, danh mục, thương hiệu, blog, coupon, settings, health check và docs. Protected API phục vụ các tác vụ gắn với khách hàng đã xác thực như profile, cart, orders và reviews. Admin API phục vụ các chức năng quản trị cần kiểm soát chặt như xác thực quản trị, quản lý products, categories, brands, orders và statistics.

Ngoài API, mã nguồn hiện tại còn có nhiều dịch vụ dùng chung thể hiện qua các service trong `app/Application`. Một số nhóm nổi bật gồm caching, reporting, security, chatbot, mail workflow, migration dữ liệu, tối ưu hiệu năng và thống kê. Việc hệ thống có thêm các lớp này cho thấy mã nguồn không chỉ tập trung vào phần giao diện và CRUD, mà đã có thêm chiều sâu về kiến trúc và hỗ trợ vận hành.

Từ góc độ phân tích thiết kế, sự tồn tại đồng thời của web layer và API layer có ý nghĩa rất lớn. Nó cho thấy hệ thống đang được tổ chức theo hướng có thể mở rộng về sau, chẳng hạn để tích hợp với client khác, mở mobile app hoặc hỗ trợ các dashboard riêng. Điều này làm cho các quyết định về route, xác thực, service layer và dữ liệu trở nên đáng phân tích hơn.

### 2.1.4. Khảo sát hiện trạng dữ liệu và kiến trúc chuyển tiếp

Một điểm đặc thù nhất của dự án là hệ thống không chỉ có một lớp dữ liệu thuần nhất. Thay vào đó, mã nguồn hiện đang tồn tại đồng thời lớp dữ liệu cũ và lớp lược đồ chuẩn hóa mới. Đây là yếu tố ảnh hưởng trực tiếp đến cả phân tích nghiệp vụ, thiết kế dữ liệu lẫn tổ chức mã nguồn.

Qua khảo sát các model cũ trong `app/Models` và hệ thống migration trong `database/migrations`, có thể thấy nhiều luồng của giao diện khách hàng và khu vực quản trị hiện vẫn dựa trên các bảng như `tbl_product`, `tbl_category_product`, `tbl_brand_product`, `tbl_customers`, `tbl_order`, `tbl_coupon` hoặc `tbl_post`. Song song với đó, lớp migration mới đã tạo thêm các bảng chuẩn hóa như `products`, `product_categories`, `product_variants`, `product_media`, `customers`, `carts`, `orders`, `order_items`, `payments`, `blog_posts`, `product_reviews`, `chat_conversations`, `chat_messages`, `analytics_events` và nhiều bảng khác.

Về mặt kiến trúc, điều này làm cho dự án trở thành một hệ thống đang ở trạng thái chuyển tiếp. Nghĩa là, không phải mọi phần đều đã được tái cấu trúc hoàn toàn sang mô hình mới. Một số luồng vẫn chạy ổn định trên nền dữ liệu cũ, trong khi các phần khác đã được tổ chức theo lớp dịch vụ, lớp kiểm tra dữ liệu đầu vào, tầng lưu trữ mới và các hợp đồng API rõ ràng hơn. Tính chất chuyển tiếp này vừa là thách thức, vừa là giá trị của đề tài vì nó phản ánh bài toán chuyển đổi rất thực tế trong phát triển phần mềm.

Từ bước khảo sát hiện trạng này, có thể rút ra kết luận rằng hệ thống có phạm vi lớn hơn một ứng dụng bán hàng cơ bản. Chính vì vậy, phần phân tích và thiết kế ở các mục sau không thể chỉ dừng ở việc mô tả chức năng bề mặt, mà cần phản ánh được kiến trúc, dữ liệu và các luồng xử lý thực sự đang tồn tại trong mã nguồn.

### 2.1.5. Nhận xét về hiện trạng và các vấn đề thiết kế đặt ra

Từ bốn khía cạnh khảo sát ở trên có thể thấy rằng hệ thống đã vượt xa phạm vi của một website giới thiệu sản phẩm thông thường. Mã nguồn hiện bao phủ đồng thời nhiều lớp bài toán: khám phá sản phẩm, vận hành giỏ hàng và checkout, quản trị nội dung, quản trị đơn hàng, phân quyền quản trị, báo cáo, chatbot hỗ trợ, API và theo dõi hành vi người dùng. Điều này tạo ra yêu cầu quan trọng đối với Chương 2: việc phân tích không được dừng ở mức liệt kê màn hình, mà phải làm rõ mối quan hệ giữa tác nhân, nghiệp vụ, route, dữ liệu và các tầng xử lý.

Vấn đề thiết kế đầu tiên được đặt ra là bài toán cân bằng giữa trải nghiệm người dùng và độ phức tạp kỹ thuật. Ở phía storefront, người dùng mong muốn thao tác nhanh, ít bước, phản hồi rõ ràng và có cảm giác liền mạch trong suốt hành trình từ tìm kiếm tới đặt hàng. Trong khi đó, ở phía backend, hệ thống phải quản lý được giá, khuyến mãi, biến thể, tồn kho, phí vận chuyển, trạng thái đơn hàng, lịch sử thanh toán và dữ liệu phân tích. Nếu chỉ tối ưu một phía mà bỏ qua phía còn lại, hệ thống sẽ hoặc khó sử dụng, hoặc khó bảo trì.

Vấn đề thiết kế thứ hai là tính chất chuyển tiếp của hệ thống. Nhiều luồng cũ vẫn đang dựa vào `tbl_*`, trong khi lớp migration mới, lớp dịch vụ và các hợp đồng API đã mở đường cho một kiến trúc chuẩn hóa hơn. Điều này buộc phần phân tích phải nhìn hệ thống như một thực thể đang chuyển đổi, thay vì coi nó là hệ thống đã được thiết kế lại hoàn toàn. Đây cũng là lý do tại sao các quyết định về dữ liệu, dịch vụ và route cần được trình bày theo quan điểm chuyển đổi dần, tránh mô tả lý tưởng hóa.

Vấn đề thiết kế thứ ba là độ rộng của nhóm chức năng quản trị. Khu vực quản trị trong mã nguồn không chỉ có CRUD sản phẩm mà còn có dashboard, quick search, settings, inbox chat, product discovery report, product profit, phân quyền và các nhóm công cụ quản trị hệ thống. Vì vậy, phân tích thiết kế phải tách rõ các nhóm nghiệp vụ quản trị, xác định tác nhân và quyền hạn, đồng thời chỉ ra những điểm có tính vận hành dài hạn như báo cáo, giám sát, truyền thông và hỗ trợ khách hàng.

Vấn đề thiết kế thứ tư là vai trò của dữ liệu phân tích trong hệ thống. Sự tồn tại của `analytics_events`, `product_views`, `product_search_queries`, `product_recommendation_impressions`, `metrics`, `alerts` cho thấy hệ thống không chỉ lưu dữ liệu giao dịch, mà còn theo dõi hành vi và sức khỏe vận hành. Điều này có ý nghĩa rất lớn trong một website thương mại điện tử, bởi quyết định về giao diện, chiến dịch khuyến mãi, recommendation và vận hành đơn hàng đều cần dựa trên dữ liệu thực tế.

Từ những nhận xét trên, có thể thấy Chương 2 cần giải quyết đồng thời ba mục tiêu. Mục tiêu thứ nhất là mô tả trung thực phạm vi nghiệp vụ hiện có của hệ thống. Mục tiêu thứ hai là chuẩn hóa cách nhìn về kiến trúc, dữ liệu và luồng xử lý để người đọc có thể đối chiếu với mã nguồn. Mục tiêu thứ ba là tạo nền cho các chương xây dựng, kiểm tra chất lượng và đánh giá ở phía sau, nơi các chức năng sẽ được giải thích theo mã nguồn và thành phần triển khai cụ thể.

### 2.1.6. Kết luận khảo sát hiện trạng

Khảo sát hiện trạng cho thấy hệ thống website thương mại điện tử trong đề tài có thể được xem như một nền tảng web nhiều phân hệ, trong đó storefront là bề mặt phục vụ người dùng cuối, admin là mặt vận hành nội bộ, còn API và service layer là nền kỹ thuật phục vụ mở rộng. Bối cảnh khảo sát này rất quan trọng vì nó xác định mức độ chi tiết cần có của phần phân tích và thiết kế.

Nói cách khác, Chương 2 không thể được viết như một chương mô tả một website bán hàng nhỏ, chỉ gồm trang chủ, sản phẩm và đơn hàng. Thay vào đó, chương này phải tiếp cận hệ thống như một tập hợp các module phối hợp với nhau: catalog, tìm kiếm, recommendation, cart, checkout, order, payment, CMS, chat, báo cáo, phân quyền, analytics và migration dữ liệu. Đây chính là nền để xây dựng các mục phân tích yêu cầu, mô hình hóa hệ thống, thiết kế giao diện và thiết kế cơ sở dữ liệu ở các phần tiếp theo.

## 2.2. Phân tích yêu cầu

### 2.2.1. Yêu cầu chức năng

#### a) Yêu cầu chức năng phía khách vãng lai và khách hàng

Đối với nhóm người dùng phía storefront, hệ thống cần hỗ trợ các chức năng liên quan đến quá trình khám phá và mua sắm sản phẩm. Trước hết, người dùng cần có khả năng xem trang chủ, duyệt danh mục, xem sản phẩm theo thương hiệu và theo tag. Đây là nhóm chức năng nền giúp người dùng tiếp cận được dữ liệu catalog của hệ thống.

Tiếp theo, hệ thống cần hỗ trợ tìm kiếm sản phẩm theo từ khóa, nhận gợi ý tìm kiếm động và truy cập nhanh tới các kết quả liên quan. Đối với nhóm hàng thiết bị công nghệ, tìm kiếm là một chức năng rất quan trọng vì số lượng sản phẩm, cấu hình và thương hiệu thường khá lớn. Nếu chức năng này yếu, khả năng khám phá sản phẩm của người dùng sẽ giảm rõ rệt.

Ngoài việc tìm kiếm, người dùng cần có thể xem chi tiết sản phẩm, xem hình ảnh, thông số kỹ thuật, bình luận, đánh giá và các nội dung liên quan. Đây là phần hỗ trợ ra quyết định mua hàng. Song song với đó, hệ thống cần có chức năng so sánh sản phẩm để phù hợp hơn với đặc thù mua sắm thiết bị công nghệ.

Về luồng mua hàng, hệ thống cần hỗ trợ thêm sản phẩm vào giỏ hàng, cập nhật số lượng, xóa item khỏi giỏ, áp dụng coupon, tính phí vận chuyển, đi vào checkout, xác nhận đơn hàng và thanh toán. Khi người dùng đã có tài khoản, hệ thống còn cần hỗ trợ theo dõi lịch sử đơn hàng, hủy đơn hàng trong điều kiện cho phép và quản lý trạng thái tài khoản.

Đối với xác thực, hệ thống cần hỗ trợ đăng ký, đăng nhập, đăng xuất, quên mật khẩu, đặt lại mật khẩu và đăng nhập xã hội. Đây là các chức năng có liên quan trực tiếp tới trải nghiệm người dùng và tính liền mạch của luồng mua hàng.

#### b) Yêu cầu chức năng phía quản trị viên

Khu vực quản trị cần hỗ trợ đầy đủ các thao tác vận hành dữ liệu của website. Trước hết là nhóm catalog gồm danh mục sản phẩm, thương hiệu, sản phẩm, thuộc tính, biến thể, gallery, document và bình luận sản phẩm. Đây là nhóm chức năng lõi để duy trì dữ liệu hàng hóa.

Tiếp theo là nhóm nội dung gồm bài viết, danh mục bài viết, video, slider, site info và các nội dung hỗ trợ truyền thông khác. Việc có nhóm chức năng này cho thấy website không chỉ là hệ thống bán hàng mà còn có vai trò cung cấp nội dung marketing và hỗ trợ thông tin.

Nhóm chức năng quản trị đơn hàng bao gồm xem danh sách đơn, xem chi tiết đơn, cập nhật trạng thái, in đơn, quản lý delivery, feeship và coupon. Đây là phần có ảnh hưởng trực tiếp tới quy trình vận hành hậu bán hàng của hệ thống.

Ngoài ra, khu vực quản trị còn cần có chức năng quản lý người dùng quản trị, gán vai trò, gán quyền, xem dashboard, quick search, profile, settings, inbox chat và các báo cáo như product discovery, product profit hoặc admin statistics. Những chức năng này giúp admin trở thành một phân hệ quản lý đầy đủ thay vì chỉ là nơi thêm sản phẩm.

#### c) Yêu cầu chức năng của API

API của hệ thống cần hỗ trợ truy xuất dữ liệu công khai như sản phẩm, thương hiệu, danh mục, blog, coupon, settings, health và docs. Với nhóm người dùng đã xác thực, API cần hỗ trợ profile, cart, order và review. Với phía quản trị viên, API cần hỗ trợ các thao tác quản lý dữ liệu và thống kê dưới cơ chế xác thực, phân quyền chặt chẽ hơn.

Yêu cầu này có ý nghĩa vì nó giúp hệ thống tách riêng các hợp đồng dữ liệu ra khỏi giao diện web. Trong dài hạn, đây là tiền đề để mở rộng sang các client hoặc lớp hiển thị khác nếu cần.

### 2.2.2. Yêu cầu phi chức năng

#### a) Yêu cầu về hiệu năng

Hiệu năng là một yêu cầu phi chức năng đặc biệt quan trọng với website thương mại điện tử. Hệ thống cần phản hồi tốt ở các thao tác thường xuyên như tải trang chủ, tải danh mục, xem chi tiết sản phẩm, tìm kiếm, thêm vào giỏ hàng và xem lịch sử đơn hàng. Với một website bán thiết bị công nghệ, số lượng dữ liệu có thể tăng lên khá nhanh, nên hiệu năng cần được xem xét ngay từ giai đoạn thiết kế.

Trong mã nguồn hiện tại, yêu cầu hiệu năng được phản ánh qua sự xuất hiện của cache, health check, service tối ưu hóa, performance commands và các migration liên quan đến index hoặc analytics. Điều đó cho thấy vấn đề hiệu năng không bị bỏ mặc mà đã được xem như một phần của kiến trúc tổng thể.

#### b) Yêu cầu về bảo mật

Website cần bảo đảm an toàn cho dữ liệu khách hàng và dữ liệu quản trị. Ở phía storefront, hệ thống cần kiểm soát tốt đăng nhập, khôi phục mật khẩu, checkout và các luồng liên quan đến đơn hàng. Ở phía admin, hệ thống cần có cơ chế xác thực riêng, phân quyền chặt, kiểm soát API và hạn chế truy cập sai phạm vi.

Về phía kỹ thuật, các lớp như middleware, request validation, Sanctum, role-permission system, health check và security command cho thấy yêu cầu bảo mật đã được đưa vào hệ thống ở mức có ý thức, thay vì chỉ là phần bổ sung về sau.

#### c) Yêu cầu về khả năng mở rộng

Khả năng mở rộng được thể hiện ở việc dự án không chỉ tổ chức theo MVC đơn thuần mà đã có thêm các lớp `Application`, `Domain`, `Infrastructure/Persistence`, `Support`, `Http/Requests` và API layer. Cấu trúc này cho phép hệ thống mở rộng thêm báo cáo, chatbot, analytics, recommendation, mobile client hoặc các dịch vụ hỗ trợ khác mà không cần phá vỡ toàn bộ mã nguồn hiện có.

Đây là một yêu cầu đặc biệt quan trọng trong bối cảnh hệ thống đang ở trạng thái chuyển tiếp. Nếu không có định hướng mở rộng rõ ràng, việc duy trì đồng thời dữ liệu cũ và lớp chuẩn hóa mới sẽ nhanh chóng làm kiến trúc trở nên khó kiểm soát.

#### d) Yêu cầu về khả năng bảo trì

Do dự án có nhiều module và đang trong giai đoạn hiện đại hóa dần, khả năng bảo trì là yêu cầu rất quan trọng. Hệ thống cần có khả năng tiếp tục chạy ổn định trên nền hiện có, đồng thời cho phép chuẩn hóa dần từng module mà không làm hỏng các luồng đang vận hành.

Điều này giải thích vì sao việc tách request validation, service layer, support classes, cache và console command lại có ý nghĩa lớn hơn trong dự án này so với các đồ án CRUD nhỏ.

#### e) Yêu cầu về trải nghiệm người dùng

Website hướng tới trải nghiệm gần với các trang thương mại điện tử thực tế, thể hiện qua search suggest, flash sale, quick cart, hover cart, compare, chatbot, dashboard quản trị và các phản hồi thao tác rõ ràng. Đây là nhóm yêu cầu phi chức năng liên quan trực tiếp đến cảm nhận sử dụng của người dùng cuối và hiệu quả chuyển đổi của hệ thống.

#### f) Yêu cầu về tính sẵn sàng và độ ổn định vận hành

Đối với một hệ thống thương mại điện tử, việc website hoạt động ổn định có ý nghĩa trực tiếp tới doanh thu và trải nghiệm người dùng. Hệ thống cần duy trì khả năng phản hồi bình thường ở các trang và chức năng quan trọng như trang chủ, danh mục, chi tiết sản phẩm, giỏ hàng, checkout và dashboard quản trị. Điều này lý giải cho sự xuất hiện của các endpoint health check, metrics, alerts và một số command hỗ trợ tối ưu hóa hoặc giám sát vận hành trong mã nguồn.

Từ góc độ phân tích thiết kế, yêu cầu này dẫn tới việc một số luồng cần được thiết kế có tính dự phòng cao hơn, ví dụ cache cho dữ liệu đọc nhiều, validation rõ ràng cho request đầu vào, và cơ chế ghi log hoặc thống kê lỗi để có thể nhận diện sớm sự cố. Trong thực tế vận hành, một hệ thống có đủ chức năng nhưng không ổn định sẽ khó được xem là hệ thống đáp ứng tốt yêu cầu nghiệp vụ.

#### g) Yêu cầu về toàn vẹn dữ liệu và khả năng truy vết

Website thương mại điện tử liên quan tới nhiều dữ liệu nhạy cảm như thông tin khách hàng, địa chỉ giao hàng, giá bán, mã giảm giá, lịch sử đơn hàng và trạng thái thanh toán. Vì vậy, hệ thống cần bảo đảm các thay đổi quan trọng có thể được theo dõi và truy vết lại. Sự hiện diện của các bảng như `payment_transactions`, `coupon_redemptions`, `analytics_events`, `chat_messages` và `metrics` cho thấy yêu cầu truy vết đã được phản ánh ở mức thiết kế dữ liệu.

Khả năng truy vết còn đặc biệt quan trọng trong bối cảnh hệ thống có lớp dữ liệu chuyển tiếp. Nếu không có cơ chế ghi nhận rõ nguồn dữ liệu, lịch sử giao dịch và trạng thái xử lý, việc đối chiếu giữa dữ liệu cũ và lược đồ mới sẽ rất khó khăn. Đây là lý do phần thiết kế cơ sở dữ liệu không chỉ cần đẹp về mặt chuẩn hóa, mà còn cần có khả năng hỗ trợ migration, audit và debug.

### 2.2.3. Đặc tả yêu cầu theo module storefront

Để làm rõ hơn các yêu cầu chức năng đã nêu, có thể đặc tả hệ thống storefront theo từng module nghiệp vụ chính. Cách đặc tả này giúp liên hệ trực tiếp giữa chức năng mà người dùng nhìn thấy với route, controller, service và dữ liệu được hệ thống sử dụng ở phía sau.

#### a) Module trang chủ và khám phá sản phẩm

Module đầu tiên của storefront là trang chủ và vùng khám phá sản phẩm. Người dùng khi truy cập vào website cần nhìn thấy một bức tranh tổng quát về catalog hiện có thông qua banner, khối nổi bật, sản phẩm mới, sản phẩm gợi ý, flash sale, khối thương hiệu và các lối tắt tới danh mục. Về mặt yêu cầu, module này phải bảo đảm tải được các tập dữ liệu có tính đại diện cao, sắp xếp theo mức độ ưu tiên hiển thị và cho phép người dùng chuyển tiếp nhanh tới trang danh mục hoặc trang chi tiết.

Vì đây là cửa vào chính của hệ thống, module trang chủ còn phải đáp ứng các yêu cầu bổ sung như hiển thị đúng trên thiết bị di động, giữ tốc độ tải chấp nhận được dù có nhiều thành phần giao diện, và hỗ trợ các dữ liệu marketing như banner, video, bài viết, liên kết hỗ trợ. Các yêu cầu này tác động trực tiếp tới thiết kế dữ liệu của nhóm bảng `banners`, `videos`, `settings`, `site_links`, `site_contacts` cũng như chiến lược cache cho dữ liệu đọc nhiều.

#### b) Module tìm kiếm, điều hướng danh mục và thương hiệu

Người dùng cần có khả năng tìm kiếm sản phẩm theo từ khóa thông qua ô tìm kiếm toàn cục, nhận gợi ý động trong lúc nhập và truy cập trang kết quả chi tiết khi muốn lọc sâu hơn. Đồng thời, hệ thống phải hỗ trợ duyệt sản phẩm theo danh mục, thương hiệu và tag để phục vụ các hành vi mua sắm khác nhau. Đây là nhóm yêu cầu phản ánh khá đúng đặc thù của website bán thiết bị công nghệ, nơi người dùng thường có nhu cầu đi từ từ khóa rất cụ thể sang tập sản phẩm tương ứng.

Thiết kế cho module này đòi hỏi hệ thống có slug ổn định, chỉ mục tìm kiếm phù hợp, khả năng ghi nhận lịch sử tìm kiếm và số lượng kết quả, đồng thời cho phép trích xuất dữ liệu phục vụ recommendation. Sự xuất hiện của `product_search_queries`, `product_views`, `product_recommendation_impressions` trong lớp migration mới cho thấy hệ thống đã đi xa hơn mức tìm kiếm hiển thị đơn thuần và hướng tới theo dõi hành vi khám phá sản phẩm.

#### c) Module chi tiết sản phẩm và ra quyết định mua

Sau khi tìm được một sản phẩm phù hợp, người dùng cần truy cập trang chi tiết để xem thông tin cụ thể hơn như tên sản phẩm, hình ảnh, giá, giá khuyến mãi, biến thể, tình trạng hàng, mô tả, nội dung chi tiết, tài liệu liên quan, bình luận, đánh giá và các đề xuất đi kèm. Với hàng công nghệ, quyết định mua thường không đến ngay từ trang danh sách mà phụ thuộc lớn vào chất lượng thông tin của trang chi tiết.

Yêu cầu ở đây là hệ thống phải hiển thị được cả dữ liệu định danh lẫn dữ liệu hỗ trợ ra quyết định. Các trường như `thumbnail_path`, `description`, `content`, `meta`, `attributes`, `product_media`, `product_reviews`, `views_count` của nhóm bảng catalog vì vậy không thể được xem là phụ, mà là một phần cốt lõi của thiết kế dữ liệu và giao diện.

#### d) Module so sánh sản phẩm

Đối với sản phẩm công nghệ, việc so sánh cấu hình, giá và thương hiệu là hành vi rất phổ biến. Hệ thống cần hỗ trợ người dùng chọn nhiều sản phẩm và xem bảng đối chiếu theo cùng tiêu chí. Dù đây không phải là bước bắt buộc trong luồng mua hàng, nhưng nó góp phần mạnh vào quá trình ra quyết định và làm tăng giá trị sử dụng của website.

Yêu cầu của module so sánh không chỉ nằm ở giao diện hiển thị bảng so sánh, mà còn ở cách dữ liệu sản phẩm được chuẩn hóa để có thể đặt cạnh nhau theo các trường đồng nhất. Điều này tác động ngược trở lại tới việc tổ chức dữ liệu thuộc tính, biến thể, thông số và mô tả sản phẩm.

#### e) Module giỏ hàng và coupon

Hệ thống phải cho phép người dùng thêm sản phẩm vào giỏ hàng từ nhiều điểm vào khác nhau như danh sách sản phẩm, quickview hoặc trang chi tiết. Sau đó, người dùng cần xem giỏ hàng, cập nhật số lượng, xóa item, áp dụng coupon, kiểm tra tổng tiền và đi vào checkout. Đây là vùng chuyển tiếp trực tiếp giữa giai đoạn khám phá và giai đoạn tạo doanh thu.

Về mặt thiết kế, module này cần xử lý đúng nhiều biến trạng thái: sản phẩm có tồn tại hay không, biến thể có hợp lệ hay không, số lượng có vượt quá giới hạn hay không, coupon có còn hiệu lực hay không, và shipping fee tạm tính có được áp vào tổng tiền hay chưa. Điều đó giải thích vì sao mã nguồn có cả `CartService`, `CouponService`, `ShippingService`, nhóm request validation của giỏ hàng, cũng như lớp dữ liệu `carts`, `cart_items`, `coupons`, `coupon_redemptions`.

#### f) Module tài khoản khách hàng

Khách vãng lai có thể xem sản phẩm và chuẩn bị mua hàng, nhưng để hoàn tất checkout và theo dõi đơn hàng thì hệ thống cần cơ chế tài khoản khách hàng. Các yêu cầu cơ bản gồm đăng ký, đăng nhập, đăng xuất, quên mật khẩu, đặt lại mật khẩu, ghi nhớ phiên làm việc và điều hướng đúng sau đăng nhập. Ngoài ra, hệ thống còn hỗ trợ đăng nhập xã hội, giúp giảm ma sát trong bước xác thực.

Thiết kế của module này phải cân bằng giữa tính đơn giản và bảo mật. Dữ liệu khách hàng cần được lưu tách biệt với người dùng quản trị, có khả năng soft delete, đánh dấu trạng thái xác thực email hoặc số điện thoại, và quản lý nhiều địa chỉ giao hàng khác nhau nếu hệ thống mở rộng theo hướng chuẩn hóa hoàn chỉnh.

#### g) Module checkout, thanh toán và lịch sử đơn hàng

Đây là module có giá trị nghiệp vụ trực tiếp nhất. Sau khi đã có giỏ hàng hợp lệ, khách hàng cần nhập hoặc chọn thông tin giao hàng, tính phí vận chuyển, xác nhận đơn, chọn phương thức thanh toán và theo dõi đơn sau mua. Hệ thống cũng cần hỗ trợ hủy đơn trong phạm vi trạng thái cho phép và hiển thị lịch sử mua hàng của khách.

Về thiết kế, module này liên quan đồng thời tới nhiều nhóm dữ liệu: `customers`, `customer_addresses`, `shipping_fees`, `orders`, `order_items`, `order_shipping`, `payments`, `payment_transactions`. Mỗi thao tác ở checkout phải dẫn tới một thay đổi dữ liệu có kiểm soát, tránh tình trạng tạo đơn thiếu chi tiết, ghi sai tổng tiền hoặc không theo dõi được trạng thái thanh toán.

#### h) Module hỗ trợ, blog và nội dung truyền thông

Ngoài luồng mua hàng chính, storefront còn có các phần blog, video, liên hệ và chatbot. Những phần này không trực tiếp tạo đơn hàng nhưng góp phần nâng cao trải nghiệm, tăng hiểu biết về sản phẩm và hỗ trợ khách hàng trước hoặc sau mua. Vì vậy, hệ thống cần có yêu cầu rõ về quản lý bài viết, danh mục bài viết, video, banner và hội thoại hỗ trợ.

Việc nhóm các chức năng này vào cùng phạm vi storefront có ý nghĩa thiết kế rõ ràng: website thương mại điện tử hiện đại không chỉ là nơi giao dịch, mà còn là nơi nuôi dưỡng nhu cầu và duy trì tương tác với người dùng. Điều đó làm cho phần phân tích giao diện, nội dung và dữ liệu của dự án trở nên đầy đủ hơn.

### 2.2.4. Đặc tả yêu cầu theo module quản trị

Ở phía quản trị, yêu cầu hệ thống cần được đặc tả sâu hơn vì đây là mặt ảnh hưởng trực tiếp tới tính bền vững của dữ liệu và vận hành hằng ngày.

#### a) Module dashboard và không gian làm việc quản trị

Khi quản trị viên đăng nhập, hệ thống cần cung cấp một dashboard thể hiện được các chỉ số trọng tâm như số đơn, xu hướng doanh thu, thống kê đơn theo ngày hoặc theo khoảng thời gian, quick search, profile và settings. Dashboard không chỉ để trình bày số liệu mà còn là điểm vào chính tới các phân hệ khác. Do đó, yêu cầu quan trọng của module này là trực quan, dễ điều hướng và phản hồi nhanh.

#### b) Module quản lý catalog

Quản trị viên cần thêm, sửa, xóa, kích hoạt, vô hiệu hóa danh mục, thương hiệu, sản phẩm, gallery, tài liệu, thuộc tính và biến thể. Yêu cầu ở đây không chỉ là CRUD từng thực thể riêng lẻ, mà còn là bảo đảm tính nhất quán giữa các thực thể. Ví dụ, khi cập nhật sản phẩm, hệ thống phải đồng bộ lại brand, categories, media, document, pricing và cache liên quan; khi xóa hoặc đổi trạng thái danh mục, cần bảo đảm các liên kết tới sản phẩm vẫn được xử lý hợp lý.

#### c) Module quản lý nội dung và truyền thông

Admin cần có khả năng quản lý bài viết, danh mục bài viết, video, slider, site info, liên kết liên hệ và các thành phần truyền thông khác. Yêu cầu của module này là bảo đảm nội dung có thể được tạo, chỉnh sửa, lên lịch, xuất bản và ẩn đi mà không ảnh hưởng tới các phần cốt lõi của website. Đây là nền tảng để storefront vừa bán hàng vừa thực hiện vai trò marketing và cung cấp thông tin.

#### d) Module vận hành đơn hàng, phí giao hàng và khuyến mãi

Quản trị viên cần xem danh sách đơn hàng, mở chi tiết đơn, cập nhật trạng thái, in đơn, cập nhật phí giao hàng, quản lý delivery và quản lý coupon. Các yêu cầu này đặc biệt quan trọng vì chúng liên quan tới trải nghiệm sau mua của khách hàng. Nếu quản lý đơn hàng kém, toàn bộ giá trị của luồng storefront sẽ bị suy giảm.

Từ góc độ thiết kế, module này cần mô hình hóa rõ các trạng thái đơn hàng, trạng thái thanh toán, trạng thái fulfillment, lịch sử áp dụng coupon, phí vận chuyển theo địa bàn và các tình huống ngoại lệ như hủy đơn hoặc cập nhật lại số lượng.

#### e) Module người dùng, vai trò và phân quyền

Do admin có nhiều nhóm thao tác khác nhau, hệ thống phải hỗ trợ quản lý người dùng nội bộ, gán vai trò, gán quyền, kiểm soát truy cập route web và admin API. Đây là nhóm yêu cầu rất quan trọng đối với tính an toàn của toàn bộ website. Repo hiện đang sử dụng Spatie Permission cùng middleware `admin.permission:*`, vì vậy phần phân tích phải ghi nhận rõ đây là một trong những trụ cột kỹ thuật của khu vực quản trị.

#### f) Module chat inbox, báo cáo và công cụ hệ thống

Những module như chat inbox, product discovery report, product profit, mail preview, health check, optimize database, add indexes hoặc slow queries cho thấy admin còn có vai trò vận hành hệ thống ở mức kỹ thuật và phân tích. Yêu cầu ở đây là dữ liệu báo cáo phải bám được các bảng nghiệp vụ thật, có khả năng giúp quản trị viên phát hiện vấn đề hoặc cơ hội tối ưu thay vì chỉ dừng ở thống kê tổng quát.

### 2.2.5. Bảng 2.1. Ma trận yêu cầu chức năng, dữ liệu và ràng buộc

Để hệ thống hóa các yêu cầu trên, có thể tóm tắt mối quan hệ giữa chức năng, tác nhân và dữ liệu liên quan theo bảng sau.

| Nhóm chức năng | Tác nhân chính | Dữ liệu tác động | Điều kiện trước | Kết quả đầu ra |
|---|---|---|---|---|
| Xem trang chủ và khám phá sản phẩm | Khách vãng lai, khách hàng | `products`, `brands`, `product_categories`, `banners`, `videos` | Dữ liệu catalog đã tồn tại và được kích hoạt | Trang chủ hiển thị nội dung và các khối sản phẩm phù hợp |
| Tìm kiếm và gợi ý tìm kiếm | Khách vãng lai, khách hàng | `products`, `product_search_queries`, `product_views` | Từ khóa hợp lệ, catalog có dữ liệu tìm kiếm | Danh sách gợi ý hoặc trang kết quả tìm kiếm |
| Xem chi tiết và so sánh sản phẩm | Khách vãng lai, khách hàng | `products`, `product_media`, `product_variants`, `product_reviews` | Sản phẩm tồn tại và đang hiển thị | Trang chi tiết hoặc bảng so sánh |
| Thêm vào giỏ hàng và áp dụng coupon | Khách vãng lai, khách hàng | Session cart, `carts`, `cart_items`, `coupons` | Sản phẩm hợp lệ, số lượng phù hợp | Giỏ hàng được cập nhật và tổng tiền được tính lại |
| Checkout và tạo đơn hàng | Khách hàng | `customers`, `customer_addresses`, `shipping_fees`, `orders`, `order_items`, `payments` | Người dùng đã đăng nhập, giỏ hàng hợp lệ | Đơn hàng được tạo và trả kết quả xác nhận |
| Theo dõi và hủy đơn hàng | Khách hàng | `orders`, `order_items`, `payments` | Khách là chủ đơn hàng, đơn ở trạng thái cho phép | Lịch sử đơn được hiển thị hoặc đơn được cập nhật trạng thái |
| Quản lý catalog | Quản trị viên | `brands`, `product_categories`, `products`, `product_variants`, `product_media` | Quản trị viên có quyền tương ứng | Dữ liệu catalog được tạo hoặc cập nhật |
| Quản lý nội dung | Quản trị viên | `blog_categories`, `blog_posts`, `videos`, `banners`, `settings` | Người dùng có quyền quản lý nội dung | Nội dung storefront được cập nhật |
| Quản lý đơn hàng và phí giao hàng | Quản trị viên | `orders`, `order_items`, `order_shipping`, `shipping_fees`, `coupons` | Có quyền quản lý đơn hàng hoặc coupon | Trạng thái đơn hoặc phí giao hàng được cập nhật |
| Quản lý người dùng và phân quyền | Quản trị viên cấp hệ thống | `users`, `roles`, `permissions` | Người dùng có quyền quản trị hệ thống | Người dùng nội bộ được cấp vai trò và quyền |
| Chat hỗ trợ và inbox | Khách vãng lai, khách hàng, quản trị viên | `chat_conversations`, `chat_messages` | Kênh chat hoạt động | Hội thoại được lưu và phản hồi được xử lý |
| Báo cáo và analytics | Quản trị viên cấp hệ thống | `analytics_events`, `product_views`, `metrics`, `alerts` | Dữ liệu theo dõi đã được ghi nhận | Dashboard và báo cáo vận hành được hiển thị |

### 2.2.6. Ưu tiên triển khai và mức độ quan trọng của yêu cầu

Nếu xét theo mức độ ảnh hưởng tới tính hoàn chỉnh của website thương mại điện tử, có thể chia yêu cầu thành ba lớp ưu tiên. Lớp ưu tiên cao nhất là các chức năng trực tiếp tạo nên vòng đời mua hàng: catalog, tìm kiếm, chi tiết sản phẩm, giỏ hàng, checkout, đơn hàng và xác thực khách hàng. Đây là các yêu cầu mà nếu thiếu thì hệ thống gần như không thể được xem là một website bán hàng hoàn chỉnh.

Lớp ưu tiên thứ hai là các chức năng hỗ trợ quyết định mua và hỗ trợ vận hành như đánh giá sản phẩm, so sánh, blog, video, coupon, quản trị nội dung, quản trị phí giao hàng và quản lý phân quyền. Chúng không phải là lõi tối thiểu, nhưng khi được bổ sung sẽ nâng hệ thống lên mức có tính thực tế cao hơn nhiều.

Lớp ưu tiên thứ ba là các chức năng gia tăng chiều sâu kỹ thuật như chatbot, analytics, monitoring, health check, optimize database, discovery report, product profit, automation hoặc migration hỗ trợ chuẩn hóa dữ liệu. Đây là nhóm chức năng cho thấy hệ thống không chỉ nhắm tới bài toán "có chạy được", mà đang được định hướng theo hướng bền vững hơn.

### 2.2.7. Bảng 2.2. Luật nghiệp vụ cốt lõi của hệ thống

Để chương phân tích thiết kế có chiều sâu hơn, ngoài yêu cầu chức năng và phi chức năng, cần đặc tả rõ các luật nghiệp vụ. Luật nghiệp vụ là các quy tắc chi phối việc dữ liệu có được chấp nhận, cập nhật hay từ chối trong từng tình huống cụ thể. Với website thương mại điện tử, đây là một phần rất quan trọng vì nhiều lỗi hệ thống phát sinh không phải do thiếu chức năng, mà do vi phạm quy tắc nghiệp vụ.

| Mã luật | Nội dung luật nghiệp vụ | Phạm vi áp dụng | Dữ liệu liên quan | Ý nghĩa thiết kế |
|---|---|---|---|---|
| `BR-01` | Sản phẩm muốn hiển thị trên storefront phải ở trạng thái hoạt động | Catalog, storefront | `products.is_active`, `brands.is_active`, `product_categories.is_active` | Giữ cho dữ liệu hiển thị nhất quán và tránh hiển thị dữ liệu đã vô hiệu hóa |
| `BR-02` | Sản phẩm thêm vào giỏ hàng phải tồn tại và hợp lệ theo biến thể nếu có | Cart, checkout | `products`, `product_variants`, request cart | Bảo đảm giỏ hàng chỉ chứa item có thể xử lý nghiệp vụ |
| `BR-03` | Người dùng phải đăng nhập trước khi hoàn tất checkout | Auth, checkout | Session khách hàng, `customers` | Phân tách rõ phần dùng công khai và phần giao dịch có trách nhiệm |
| `BR-04` | Coupon chỉ được áp dụng khi còn hiệu lực, còn hoạt động và thỏa điều kiện tối thiểu | Cart, marketing | `coupons`, `coupon_redemptions`, `carts` | Ngăn lạm dụng khuyến mãi và giữ đúng chính sách bán hàng |
| `BR-05` | Một coupon có thể bị giới hạn số lượt dùng toàn cục và số lượt dùng trên mỗi khách | Cart, order | `usage_limit`, `usage_per_customer`, `used_count` | Cho phép thiết kế chiến dịch khuyến mãi có kiểm soát |
| `BR-06` | Khi tạo đơn hàng, hệ thống phải lưu snapshot của sản phẩm và giao hàng tại thời điểm mua | Checkout, order | `order_items`, `order_shipping` | Bảo vệ tính đúng đắn của dữ liệu lịch sử giao dịch |
| `BR-07` | Đơn hàng chỉ được hủy khi còn ở trạng thái cho phép | Order history | `orders.status`, `orders.cancelled_at` | Tránh mâu thuẫn giữa khách hàng và quy trình xử lý vận hành |
| `BR-08` | Quản trị viên chỉ được truy cập route thuộc quyền được gán | Admin, security | `roles`, `permissions`, middleware | Bảo vệ dữ liệu và giảm rủi ro thao tác sai phạm vi |
| `BR-09` | Dữ liệu đánh giá sản phẩm có thể cần qua bước duyệt trước khi hiển thị công khai | Review, moderation | `product_reviews.status`, `product_reviews.is_public` | Kiểm soát nội dung do người dùng tạo |
| `BR-10` | Hội thoại chat phải lưu được lịch sử trao đổi và phân biệt nguồn gửi | Chat, support | `chat_conversations`, `chat_messages` | Hỗ trợ chăm sóc khách hàng và truy vết phản hồi |
| `BR-11` | Dữ liệu analytics phải lưu theo sự kiện và thời điểm để phục vụ báo cáo | Analytics, reporting | `analytics_events`, `product_views`, `product_search_queries` | Biến hành vi người dùng thành nguồn dữ liệu cho quản trị |
| `BR-12` | Xóa dữ liệu ở nhiều thực thể nên ưu tiên `soft delete` hơn xóa cứng | Catalog, CMS, customer | `products`, `blog_posts`, `customers`, `banners`, `videos` | Bảo toàn lịch sử và khả năng phục hồi dữ liệu |

Tập luật nghiệp vụ ở trên cho thấy một website thương mại điện tử không thể chỉ được xem như tập màn hình và form nhập liệu. Chính các quy tắc này mới là lớp logic giúp hệ thống vận hành nhất quán, an toàn và có thể kiểm toán được về sau.

### 2.2.8. Bảng 2.3. Ma trận tác nhân và chức năng hệ thống

Để làm rõ phạm vi tác động của từng tác nhân, có thể mô tả hệ thống bằng ma trận tác nhân và chức năng như sau.

| Chức năng / Tác nhân | Khách vãng lai | Khách hàng | Quản trị viên |
|---|---|---|---|
| Xem trang chủ, danh mục, thương hiệu | Có | Có | Không phải chức năng chính |
| Tìm kiếm và gợi ý tìm kiếm | Có | Có | Có thể gián tiếp qua quick search admin |
| Xem chi tiết sản phẩm | Có | Có | Có, ở góc nhìn biên tập dữ liệu |
| So sánh sản phẩm | Có | Có | Không |
| Thêm vào giỏ hàng | Có | Có | Không |
| Áp dụng coupon | Hạn chế hoặc phụ thuộc luồng | Có | Không |
| Đăng ký, đăng nhập, quên mật khẩu | Có | Có | Có nhưng dùng kênh admin riêng |
| Checkout và xác nhận đơn | Không hoàn tất nếu chưa đăng nhập | Có | Không |
| Theo dõi lịch sử và hủy đơn | Không | Có | Có ở góc nhìn quản trị |
| Gửi bình luận, đánh giá | Hạn chế hoặc không | Có | Có ở góc nhìn duyệt và phản hồi |
| Chat hỗ trợ | Có | Có | Có qua chat inbox |
| Quản lý catalog | Không | Không | Có |
| Quản lý nội dung CMS | Không | Không | Có |
| Quản lý đơn hàng, phí giao hàng, coupon | Không | Không | Có |
| Quản lý người dùng, vai trò, quyền | Không | Không | Có ở nhóm admin cấp cao |
| Xem báo cáo và analytics | Không | Không | Có |
| Tối ưu, giám sát, health check | Không | Không | Có ở nhóm hệ thống |

Ma trận này giúp hội đồng hoặc người đọc nhìn rất nhanh ranh giới chức năng giữa ba tác nhân chính. Đây là cách trình bày có giá trị vì nó cô đọng nội dung của ca sử dụng và phân quyền trong một bảng dễ kiểm tra.

### 2.2.9. Bảng 2.4. Ma trận vai trò quản trị và quyền mặc định

Ngoài việc phân biệt tác nhân theo cấp cao, hệ thống còn chia nhỏ người dùng nội bộ thành các vai trò quản trị khác nhau. Mã nguồn hiện định nghĩa tập permission chuẩn trong `App\Support\AdminPermissions` và đi kèm một cấu hình quyền mặc định theo vai trò. Việc đưa ma trận này vào báo cáo giúp phần phân tích bám rất sát với hệ thống thật.

| Vai trò | Quản lý users | Quản lý roles | Quản lý documents | Quản lý products | Quản lý content | Quản lý orders | Quản lý coupons | Quản lý system |
|---|---|---|---|---|---|---|---|---|
| `Super Admin` | Có | Có | Có | Có | Có | Có | Có | Có |
| `Admin` | Có | Có | Có | Có | Có | Có | Có | Có |
| `Editor` | Không | Không | Không | Có | Có | Không | Không | Không |
| `Manager` | Không | Không | Có | Có | Không | Có | Có | Không |
| `Staff` | Không | Không | Không | Không | Không | Không | Không | Không |

**Bảng 2.5. Danh mục quyền quản trị mặc định của hệ thống**

Nếu diễn giải theo nhóm permission, có thể quy về các hằng số:

| Mã permission | Tên permission trong hệ thống | Ý nghĩa nghiệp vụ |
|---|---|---|
| `P-01` | `manage users` | Quản lý tài khoản quản trị nội bộ |
| `P-02` | `manage roles` | Quản lý vai trò và gán quyền |
| `P-03` | `manage documents` | Quản lý tài liệu đính kèm và tải xuống |
| `P-04` | `manage products` | Quản lý catalog sản phẩm, thuộc tính, media |
| `P-05` | `manage content` | Quản lý bài viết, video, slider, nội dung truyền thông |
| `P-06` | `manage orders` | Quản lý đơn hàng, theo dõi và xử lý vận hành giao dịch |
| `P-07` | `manage coupons` | Quản lý mã giảm giá và chiến dịch khuyến mãi |
| `P-08` | `manage system` | Truy cập các công cụ vận hành, báo cáo, chat inbox, settings, health check |

Ma trận này cho thấy hệ thống quản trị không dựa trên một tài khoản “admin toàn quyền” duy nhất, mà tổ chức theo mô hình role-permission tương đối rõ. Đây là một yếu tố rất có giá trị khi đánh giá độ trưởng thành của thiết kế hệ thống.

### 2.2.10. Nhận xét về luật nghiệp vụ và ma trận quyền

Khi kết hợp bảng luật nghiệp vụ với ma trận tác nhân và ma trận vai trò, có thể thấy phần yêu cầu của hệ thống đã được mô tả ở cả ba lớp: người dùng nào được làm gì, trong điều kiện nào thì thao tác được chấp nhận, và dữ liệu nào sẽ bị tác động. Đây chính là nền tốt để chuyển sang phần mô hình hóa, vì các sơ đồ và thiết kế cơ sở dữ liệu phía sau sẽ có điểm tựa rõ ràng hơn.

## 2.3. Mô hình hóa hệ thống

### 2.3.1. Phân tích hành vi qua các kịch bản sử dụng

Để mô hình hóa hệ thống, trước hết cần phân tích các hành vi sử dụng tiêu biểu. Kịch bản đầu tiên là hành vi của khách vãng lai: truy cập trang chủ, tìm kiếm sản phẩm, xem chi tiết, đọc bình luận và chuyển sang đăng ký hoặc đăng nhập khi muốn mua hàng. Kịch bản này cho thấy hệ thống phải phục vụ tốt ngay cả khi người dùng chưa xác thực.

Kịch bản thứ hai là hành vi của khách hàng đã đăng nhập: thêm sản phẩm vào giỏ, cập nhật số lượng, áp dụng coupon, vào checkout, chọn địa chỉ, xác nhận đơn hàng, theo dõi lịch sử mua và hủy đơn trong điều kiện hợp lệ. Đây là chuỗi hành vi cốt lõi làm nên bản chất của hệ thống thương mại điện tử.

Kịch bản thứ ba là hành vi của quản trị viên: đăng nhập vào admin, vào dashboard, quản lý sản phẩm, cập nhật danh mục hoặc thương hiệu, quản lý nội dung, xử lý đơn hàng, phân quyền người dùng, theo dõi chat inbox và xem báo cáo thống kê. Kịch bản này phản ánh mặt vận hành của hệ thống.

Kịch bản thứ tư là hành vi của client sử dụng API: gọi endpoint danh sách sản phẩm, categories, brands hoặc các endpoint protected/admin dưới cơ chế xác thực và phân quyền. Việc phân tích kịch bản API giúp hệ thống không bị giới hạn trong góc nhìn giao diện web.

### 2.3.2. Thiết kế cấu trúc logic

Từ các kịch bản sử dụng trên, hệ thống được mô hình hóa theo hướng phân tầng. Ở mức logic, request đi từ người dùng hoặc client tới route, đi qua middleware, sau đó được controller tiếp nhận. Nếu có dữ liệu đầu vào quan trọng, request validation sẽ kiểm tra trước khi controller chuyển việc xử lý sang service layer.

Service layer là nơi xử lý các nghiệp vụ như sản phẩm, giỏ hàng, checkout, khuyến mãi, báo cáo, chatbot hoặc hỗ trợ vận hành. Sau đó, service tương tác với persistence layer hoặc model để truy xuất dữ liệu trong cơ sở dữ liệu. Kết quả cuối cùng được trả ngược về controller để render view Blade hoặc trả JSON API response.

Thiết kế logic này có ý nghĩa lớn vì nó giúp controller không phải gánh toàn bộ nghiệp vụ. Trong bối cảnh dự án đang ở trạng thái chuyển tiếp, việc tách tầng rõ hơn là một bước quan trọng để chuẩn hóa hệ thống dần dần.

### 2.3.3. Phân rã module nghiệp vụ

Từ khảo sát và yêu cầu đã phân tích, có thể phân rã hệ thống thành các module nghiệp vụ chính gồm: module catalog, module khám phá sản phẩm, module tài khoản khách hàng, module giỏ hàng và checkout, module đơn hàng và thanh toán, module CMS và truyền thông, module hỗ trợ khách hàng, module quản trị người dùng và phân quyền, module analytics và báo cáo, module migration và chuẩn hóa dữ liệu.

Việc phân rã theo module có vai trò quan trọng vì nó giúp thiết kế hệ thống tránh tình trạng nhìn website như một khối đơn nhất. Mỗi module có mục tiêu riêng, tác nhân riêng, dữ liệu riêng và ràng buộc riêng. Chẳng hạn, module catalog thiên về dữ liệu hiển thị và giá bán; module checkout thiên về trạng thái giao dịch; module CMS thiên về chu kỳ xuất bản nội dung; còn module analytics thiên về thu thập sự kiện và tổng hợp số liệu.

Trong mã nguồn, sự phân rã này còn thể hiện phần nào ở cấu trúc thư mục `app/Application` như `Catalog`, `Checkout`, `Customer`, `Order`, `Cms`, `Reporting`, `AI`, `Shared`, và ở các nhóm request validation, controller web hoặc API. Vì vậy, mô hình hóa theo module không chỉ có ý nghĩa lý thuyết mà còn bám sát cách mã nguồn đang được tổ chức.

### 2.3.4. Phân tích trạng thái nghiệp vụ chính

Một hệ thống thương mại điện tử không chỉ là tập hợp thao tác, mà còn là hệ thống chuyển trạng thái của các thực thể chính. Phân tích trạng thái giúp thấy được khi nào dữ liệu được tạo, khi nào được cập nhật và khi nào bị khóa hoặc kết thúc vòng đời.

#### a) Trạng thái của sản phẩm

Sản phẩm có thể được xem ở tối thiểu hai trạng thái nghiệp vụ: hiển thị hoặc không hiển thị. Ở lớp thiết kế mới, điều này phản ánh qua trường `is_active`; trong thực tế vận hành còn có các trạng thái mềm như nổi bật, có khuyến mãi, hết hàng, sắp hết hàng, hoặc chỉ hiển thị ở một số vị trí nhất định. Khi một sản phẩm bị vô hiệu hóa, hệ thống phải xử lý phù hợp ở storefront, admin và API để tránh phát sinh liên kết sai hoặc thao tác mua hàng không hợp lệ.

#### b) Trạng thái của giỏ hàng

Giỏ hàng là trạng thái tạm thời trước khi tạo đơn. Một giỏ hàng có thể trống, có item, đã áp coupon, đã tính shipping fee, hoặc đã được dùng để tạo đơn. Dữ liệu giỏ hàng vì vậy cần lưu cả phần tử chi tiết lẫn các tổng tiền tính toán ở mức snapshot. Khi số lượng sản phẩm, coupon hoặc phí giao hàng thay đổi, trạng thái tổng tiền phải được tính lại nhất quán.

#### c) Trạng thái của đơn hàng

Đơn hàng là thực thể có vòng đời dài hơn và phải được quản lý bằng trạng thái rõ ràng. Lược đồ mới của hệ thống đã phản ánh điều này qua `status`, `payment_status`, `fulfillment_status` cùng các mốc thời gian như `placed_at`, `confirmed_at`, `shipped_at`, `completed_at`, `cancelled_at`. Đây là thiết kế có giá trị lớn vì giúp tách bạch các khía cạnh của một đơn: đơn có thể đã xác nhận nhưng chưa giao, đã giao nhưng chưa thanh toán, hoặc đã hủy và hoàn tiền.

#### d) Trạng thái của thanh toán

Thanh toán không nên bị gộp hết vào trạng thái đơn hàng. Một giao dịch có thể bắt đầu ở trạng thái `pending`, chuyển sang `processing`, rồi `succeeded`, `failed` hoặc `refunded`. Bảng `payments` và `payment_transactions` cho phép mô hình hóa trạng thái này rõ hơn, đồng thời hỗ trợ truy vết các request hoặc webhook phát sinh từ cổng thanh toán.

#### e) Trạng thái của bình luận, đánh giá và chat

Review sản phẩm có thể ở trạng thái chờ duyệt, đã duyệt hoặc bị từ chối; hội thoại chat có thể đang mở, đang chờ phản hồi hoặc đã đóng; tin nhắn có thể do bot, khách hàng hoặc quản trị viên gửi. Việc xem các thành phần này như những thực thể có trạng thái riêng giúp phần thiết kế dễ giải thích hơn khi chuyển sang sơ đồ sequence, class và ERD.

### 2.3.5. Quy tắc nghiệp vụ cốt lõi

Trong mô hình hóa hệ thống, ngoài tác nhân và trạng thái, các quy tắc nghiệp vụ đóng vai trò đặc biệt quan trọng vì chúng quyết định dữ liệu nào được phép đi qua hệ thống.

Quy tắc thứ nhất là quy tắc về hợp lệ sản phẩm khi mua hàng. Sản phẩm muốn được thêm vào giỏ phải tồn tại, đang hoạt động và nếu có biến thể thì biến thể được chọn cũng phải hợp lệ. Trong nhiều tình huống, hệ thống còn cần kiểm tra số lượng đặt mua không vượt quá tồn kho hoặc vượt quá một ngưỡng cho phép.

Quy tắc thứ hai là quy tắc về coupon. Một coupon chỉ được áp dụng khi còn hiệu lực thời gian, còn trạng thái hoạt động, chưa vượt `usage_limit`, chưa vượt `usage_per_customer`, và giỏ hàng đạt `min_order_total` nếu có. Nếu coupon theo kiểu phần trăm và có `max_discount`, hệ thống phải chặn mức giảm tối đa theo đúng dữ liệu cấu hình.

Quy tắc thứ ba là quy tắc về checkout. Người dùng muốn vào checkout phải đăng nhập khách hàng. Nếu khách chưa xác thực, hệ thống phải ghi nhớ đích quay lại và điều hướng hợp lệ về `/dang-nhap`. Đây là quy tắc nghiệp vụ kiêm quy tắc trải nghiệm người dùng, và mã nguồn đã hiện thực điều này qua `StorefrontCustomerAuth`.

Quy tắc thứ tư là quy tắc về đơn hàng. Khi xác nhận đơn, hệ thống phải tạo snapshot của sản phẩm ở thời điểm mua thông qua `order_items`, lưu thông tin giao hàng tại `order_shipping`, ghi nhận tổng tiền ở `orders`, và tạo dữ liệu thanh toán phù hợp nếu có. Điều này giúp đơn hàng vẫn giữ được giá trị lịch sử ngay cả khi catalog thay đổi về sau.

Quy tắc thứ năm là quy tắc về phân quyền quản trị. Không phải mọi quản trị viên đều có thể truy cập mọi route. Mỗi nhóm nghiệp vụ như quản lý sản phẩm, quản lý đơn hàng, quản lý coupon, quản lý roles, quản lý system đều phải đi qua middleware quyền tương ứng. Đây là nguyên tắc bắt buộc để giảm rủi ro vận hành.

### 2.3.6. Kiến trúc triển khai logic theo luồng request

Ở mức logic, hệ thống có thể được hiểu qua luồng Request -> Route -> Middleware -> Controller -> Request Validation -> Service -> Persistence/Model -> Database -> Response. Đây không chỉ là sơ đồ trừu tượng mà là cách nhiều luồng trong mã nguồn đang hoạt động thực tế.

Trong các route web, controller chịu trách nhiệm điều phối request và chuẩn bị view Blade trả về cho người dùng. Trong các route API, controller trả JSON response nhưng vẫn dùng chung nhiều service hoặc dữ liệu nền. Việc tách `Http\Requests` riêng cho nhiều thao tác ở storefront, admin và API cho thấy lớp validation đang được tách dần ra khỏi controller.

Service layer trong `app/Application` là nơi chứa nhiều nghiệp vụ đang được chuẩn hóa như `CartService`, `CheckoutService`, `ShippingService`, `ProductService`, `ProductReviewService`, `ProductDiscoveryReportService`, `ChatInboxService`, `ChatbotConversationService`, `DatabaseOptimizationService`, `CacheService`. Điều này làm cho mô hình logic của hệ thống rõ hơn đáng kể so với kiểu triển khai dồn logic vào controller.

Persistence layer trong dự án chưa hoàn toàn độc lập như các kiến trúc clean architecture hoàn chỉnh, nhưng hướng đi đã tương đối rõ. Các model Eloquent mới, migrations chuẩn hóa và một số lớp hỗ trợ dữ liệu đang đóng vai trò là điểm tựa để dịch chuyển dần khỏi dữ liệu legacy. Vì vậy, mô hình logic của hệ thống cần được trình bày như một kiến trúc phân tầng đang hoàn thiện chứ không phải một kiến trúc đã khép kín tuyệt đối.

## 2.4. Sơ đồ ca sử dụng (use case diagram)

### 2.4.1. Xác định các tác nhân

Hệ thống có ba tác nhân chính. Tác nhân thứ nhất là khách vãng lai, tức người dùng chưa đăng nhập nhưng có thể truy cập phần công khai của giao diện khách hàng (storefront). Tác nhân thứ hai là khách hàng, tức người dùng đã đăng nhập và có thể thực hiện các thao tác sâu hơn như checkout, xem lịch sử đơn hàng hoặc đánh giá sản phẩm. Tác nhân thứ ba là quản trị viên, người sử dụng khu vực quản trị (admin) để quản lý dữ liệu và vận hành hệ thống.

Ngoài ba tác nhân chính, trong một số luồng có thể xem các thành phần như cổng thanh toán, dịch vụ email hoặc đăng nhập xã hội là tác nhân hỗ trợ gián tiếp. Tuy nhiên, trong phần ca sử dụng của đồ án, việc giữ trọng tâm vào ba tác nhân chính giúp sơ đồ rõ ràng và dễ giải thích hơn.

### 2.4.2. Hình 2.1. Sơ đồ ca sử dụng tổng quát

Sơ đồ ca sử dụng tổng quát của hệ thống cần thể hiện ba nhóm tác nhân chính và các nhóm chức năng lớn gồm khám phá sản phẩm, quản lý tài khoản, giỏ hàng và checkout, tương tác hỗ trợ, quản lý catalog, quản lý nội dung, quản lý đơn hàng và quản lý người dùng cùng báo cáo.

Hình 2.1 mô tả phạm vi chức năng chính của hệ thống website thương mại điện tử Laravel 11, trong đó khách vãng lai tương tác với các chức năng công khai, khách hàng tương tác với các chức năng mua hàng và quản lý đơn, còn quản trị viên tương tác với các chức năng vận hành, phân quyền và báo cáo.

### 2.4.3. Hình 2.2. Sơ đồ ca sử dụng phần khách vãng lai

Sơ đồ ca sử dụng phần khách vãng lai cần thể hiện các chức năng như xem trang chủ, xem danh mục, tìm kiếm sản phẩm, nhận gợi ý tìm kiếm, xem chi tiết sản phẩm, đọc bình luận, xem video hoặc nội dung hỗ trợ, so sánh sản phẩm và chuyển sang màn hình đăng nhập hoặc đăng ký.

Việc tách riêng khách vãng lai là cần thiết vì đây là nhóm tác nhân đầu tiên tiếp cận hệ thống. Trong nhiều website, phần này thường bị gộp chung với khách hàng, nhưng đối với đề tài này việc tách riêng giúp làm rõ hơn luồng điều hướng và giới hạn quyền thao tác trước khi xác thực.

### 2.4.4. Hình 2.3. Sơ đồ ca sử dụng phần khách hàng

Sơ đồ ca sử dụng phần khách hàng cần tập trung vào các chức năng sau đăng nhập như thêm vào giỏ hàng, cập nhật giỏ hàng, áp dụng coupon, checkout, thanh toán, xem lịch sử đơn hàng, hủy đơn hàng, gửi bình luận hoặc đánh giá và sử dụng hỗ trợ chat. Đây là phần cốt lõi thể hiện giá trị của hệ thống ở góc nhìn người mua hàng.

Hình 2.3 phản ánh toàn bộ chu trình mua hàng trực tuyến từ bước thêm giỏ hàng đến bước theo dõi đơn hàng sau mua.

### 2.4.5. Hình 2.4. Sơ đồ ca sử dụng phần quản trị viên

Sơ đồ ca sử dụng phần quản trị viên cần mô tả các chức năng quản trị catalog, nội dung, đơn hàng, người dùng, phân quyền, báo cáo và chat inbox. Đây là sơ đồ giúp chứng minh rằng hệ thống có một mặt quản trị riêng, không chỉ là website giao diện khách hàng thông thường.

Hình 2.4 cho thấy khu vực quản trị không chỉ phục vụ thao tác thêm sản phẩm, mà còn bao quát toàn bộ dữ liệu và nhiều quy trình nội bộ của hệ thống, bao gồm cả quyền truy cập, thống kê và hỗ trợ vận hành.

### 2.4.6. Hình 2.5. Sơ đồ ca sử dụng theo module

Ngoài ba sơ đồ tác nhân chính, có thể mở rộng thành các nhóm ca sử dụng theo module như:

- module tìm kiếm và khám phá sản phẩm
- module chi tiết sản phẩm và tương tác
- module so sánh sản phẩm
- module giỏ hàng và coupon
- module tài khoản khách hàng
- module checkout, thanh toán và đơn hàng
- module chat hỗ trợ
- module xác thực và workspace quản trị
- module quản lý catalog
- module quản lý nội dung
- module vận hành đơn hàng và khuyến mãi
- module người dùng, phân quyền và báo cáo

Việc tách ca sử dụng theo module giúp chương phân tích thiết kế có chiều sâu hơn và thuận tiện hơn khi liên hệ với các route thực tế trong mã nguồn.

### 2.4.7. Đặc tả ca sử dụng trọng tâm của khách vãng lai

Đối với tác nhân khách vãng lai, ba ca sử dụng quan trọng nhất là tìm kiếm sản phẩm, xem chi tiết sản phẩm và mở luồng đăng nhập hoặc đăng ký khi muốn chuyển sang mua hàng.

Ca sử dụng tìm kiếm sản phẩm bắt đầu khi người dùng nhập từ khóa vào thanh tìm kiếm. Hệ thống có thể trả gợi ý ngay lập tức hoặc chuyển người dùng tới trang kết quả. Tiền điều kiện là catalog phải có dữ liệu đang hiển thị; hậu điều kiện là người dùng có được danh sách sản phẩm liên quan để tiếp tục khám phá. Ca sử dụng này liên quan tới `SearchProductsRequest`, `AutocompleteProductsRequest`, `HomeController`, dữ liệu `products` và bảng phân tích `product_search_queries`.

Ca sử dụng xem chi tiết sản phẩm bắt đầu khi khách chọn một sản phẩm từ danh sách. Hệ thống phải hiển thị thông tin cơ bản, media, giá, đánh giá, bình luận và các sản phẩm liên quan. Tiền điều kiện là sản phẩm tồn tại và không bị ẩn; hậu điều kiện là người dùng có đủ thông tin để đưa ra quyết định xem thêm, so sánh hoặc thêm vào giỏ hàng.

Ca sử dụng chuyển sang đăng nhập hoặc đăng ký là ca sử dụng đánh dấu ranh giới giữa người dùng công khai và người dùng có tài khoản. Nó có thể được kích hoạt từ trang chi tiết, giỏ hàng hoặc checkout. Từ góc nhìn phân tích thiết kế, đây là ca sử dụng rất quan trọng vì nó cho thấy hệ thống phải duy trì ngữ cảnh phiên làm việc và ý định mua hàng ngay cả trước khi người dùng xác thực.

### 2.4.8. Đặc tả ca sử dụng trọng tâm của khách hàng

Khách hàng đã đăng nhập có phạm vi thao tác sâu hơn nhiều. Các ca sử dụng trọng tâm gồm quản lý giỏ hàng, áp dụng coupon, checkout, theo dõi lịch sử đơn hàng, hủy đơn, gửi đánh giá và sử dụng chat hỗ trợ.

Ca sử dụng quản lý giỏ hàng bao gồm thêm item, cập nhật số lượng, xóa item và xem tổng tiền. Tiền điều kiện là sản phẩm hợp lệ; hậu điều kiện là trạng thái giỏ hàng được cập nhật nhất quán. Đây là ca sử dụng có tần suất sử dụng rất cao và liên quan tới nhiều route AJAX cũng như view partial trên giao diện khách hàng.

Ca sử dụng checkout bắt đầu khi khách hàng muốn xác nhận mua hàng. Hệ thống phải kiểm tra trạng thái đăng nhập, nạp dữ liệu địa chỉ, tính phí vận chuyển, xác nhận coupon và tạo đơn hàng. Nếu bất kỳ bước nào không hợp lệ, hệ thống cần trả người dùng về đúng vị trí với thông báo rõ ràng. Hậu điều kiện của ca sử dụng là đơn hàng được tạo thành công hoặc quy trình bị chặn với lý do cụ thể.

Ca sử dụng xem lịch sử và hủy đơn hàng phản ánh giai đoạn hậu mua. Hệ thống cần hiển thị các đơn hàng gắn với tài khoản khách, hỗ trợ xem chi tiết từng đơn và cho phép hủy nếu đơn chưa đi quá xa trong vòng đời xử lý. Điều này đòi hỏi dữ liệu trạng thái đơn hàng phải rõ và logic nghiệp vụ ở controller hoặc service phải được thiết kế cẩn thận.

### 2.4.9. Đặc tả ca sử dụng trọng tâm của quản trị viên

Đối với quản trị viên, các ca sử dụng trọng tâm xoay quanh ba mục tiêu: duy trì chất lượng catalog, duy trì vận hành thương mại và duy trì an toàn hệ thống.

Ca sử dụng quản lý catalog cho phép thêm, sửa, xóa, bật tắt sản phẩm, danh mục, thương hiệu, ảnh gallery, thuộc tính và tài liệu. Tiền điều kiện là admin đã xác thực và có quyền phù hợp; hậu điều kiện là dữ liệu catalog được cập nhật, cache liên quan được làm mới và giao diện khách hàng phản ánh thay đổi theo đúng trạng thái hiển thị.

Ca sử dụng quản lý đơn hàng cho phép admin xem toàn bộ vòng đời giao dịch từ khi đặt hàng tới khi giao hàng hoặc hủy. Đây là ca sử dụng cần được mô hình hóa cẩn thận vì liên quan tới dữ liệu lịch sử, tính toán doanh thu, nghiệp vụ phí giao hàng và cả xử lý sau bán hàng.

Ca sử dụng quản lý người dùng và phân quyền có ý nghĩa đặc biệt về an toàn. Thay vì cho tất cả admin dùng chung một tài khoản hoặc một tập quyền rộng, hệ thống tổ chức vai trò và quyền thành các nhóm rõ. Điều này giúp báo cáo thể hiện được tư duy thiết kế đúng với một hệ thống có nhiều người dùng nội bộ.

### 2.4.10. Ý nghĩa của ca sử dụng đối với thiết kế hệ thống

Việc đặc tả ca sử dụng theo tác nhân và module giúp chuyển các mô tả chức năng rời rạc thành các tương tác có cấu trúc. Từ ca sử dụng, có thể suy ra route cần có, controller phải xử lý gì, request validation cần kiểm tra những trường nào, service nào nên được tách ra và bảng dữ liệu nào sẽ bị tác động.

Quan trọng hơn, ca sử dụng là cầu nối giữa phần phân tích yêu cầu và phần sơ đồ hoạt động hoặc sơ đồ tuần tự. Nếu ca sử dụng được mô tả mơ hồ, các sơ đồ phía sau sẽ dễ rơi vào tình trạng chỉ đẹp về hình thức nhưng không còn khớp với mã nguồn và kiến trúc thực tế. Với đề tài này, giá trị lớn nhất của ca sử dụng là giữ cho toàn bộ Chương 2 luôn bám được hiện trạng thật của hệ thống.

### 2.4.11. Bảng 2.6. Đặc tả ca sử dụng tìm kiếm sản phẩm

Phần dưới đây trình bày đặc tả theo mẫu bảng chuẩn thường dùng trong báo cáo phân tích thiết kế hệ thống.

| Thuộc tính | Nội dung đặc tả |
|---|---|
| Mã ca sử dụng | `UC-SEARCH-01` |
| Tên ca sử dụng | Tìm kiếm và gợi ý tìm kiếm sản phẩm |
| Tác nhân chính | Khách vãng lai, khách hàng |
| Mục tiêu | Giúp người dùng nhanh chóng tìm được sản phẩm phù hợp theo từ khóa, danh mục hoặc ngữ cảnh gợi ý |
| Mô tả ngắn | Người dùng nhập từ khóa tại thanh tìm kiếm; hệ thống trả về gợi ý nhanh hoặc trang kết quả chi tiết |
| Tiền điều kiện | Catalog đã có dữ liệu; các sản phẩm liên quan đang ở trạng thái hiển thị |
| Kích hoạt | Người dùng bắt đầu nhập từ khóa hoặc gửi form tìm kiếm |
| Luồng chính | 1. Người dùng nhập từ khóa. 2. Hệ thống gọi route gợi ý hoặc tìm kiếm. 3. Request được kiểm tra hợp lệ. 4. Hệ thống truy xuất dữ liệu sản phẩm phù hợp. 5. Kết quả hoặc gợi ý được trả về cho người dùng |
| Luồng thay thế | Nếu từ khóa rỗng hoặc không hợp lệ, hệ thống trả danh sách rỗng hoặc thông báo phù hợp |
| Luồng ngoại lệ | Nếu xảy ra lỗi truy vấn hoặc dữ liệu không khả dụng, hệ thống ghi log và trả phản hồi thất bại có kiểm soát |
| Hậu điều kiện | Người dùng nhận được danh sách sản phẩm phù hợp; dữ liệu tìm kiếm có thể được ghi nhận vào bảng phân tích |
| Dữ liệu liên quan | `products`, `product_categories`, `brands`, `product_search_queries` |
| Route và thành phần liên quan | `/tim-kiem`, `/autocomplete-ajax`, `HomeController`, `SearchProductsRequest`, `AutocompleteProductsRequest` |

Ca sử dụng này được xem là một trong những ca sử dụng lõi của giao diện khách hàng vì nó tác động trực tiếp tới khả năng khám phá sản phẩm. Ở góc độ thương mại điện tử, một chức năng tìm kiếm tốt không chỉ giảm thời gian tìm kiếm của người dùng mà còn tăng xác suất chuyển đổi từ người xem sang người mua.

### 2.4.12. Bảng 2.7. Đặc tả ca sử dụng thêm sản phẩm vào giỏ hàng

| Thuộc tính | Nội dung đặc tả |
|---|---|
| Mã ca sử dụng | `UC-CART-01` |
| Tên ca sử dụng | Thêm sản phẩm vào giỏ hàng |
| Tác nhân chính | Khách vãng lai, khách hàng |
| Mục tiêu | Cho phép người dùng lưu tạm sản phẩm quan tâm để tiếp tục so sánh, tính toán và đi tới bước mua hàng |
| Mô tả ngắn | Người dùng chọn sản phẩm, số lượng, biến thể và gửi yêu cầu thêm vào giỏ |
| Tiền điều kiện | Sản phẩm tồn tại; sản phẩm đang hoạt động; biến thể được chọn hợp lệ nếu có |
| Kích hoạt | Người dùng nhấn nút thêm vào giỏ từ danh sách, quickview hoặc trang chi tiết |
| Luồng chính | 1. Người dùng gửi request thêm giỏ. 2. Hệ thống xác minh dữ liệu đầu vào. 3. `CartService` kiểm tra sản phẩm và cập nhật giỏ. 4. Hệ thống tính lại số lượng item, tổng tiền, coupon hiện hành và phí tạm tính. 5. Giao diện nhận JSON phản hồi và đồng bộ quick cart |
| Luồng thay thế | Nếu sản phẩm đã có sẵn trong giỏ, hệ thống tăng số lượng thay vì tạo dòng mới |
| Luồng ngoại lệ | Nếu sản phẩm không tồn tại, biến thể sai hoặc số lượng không hợp lệ, hệ thống trả lỗi và không thay đổi giỏ |
| Hậu điều kiện | Giỏ hàng được cập nhật nhất quán; người dùng nhìn thấy trạng thái mới của giỏ |
| Dữ liệu liên quan | Session cart, `carts`, `cart_items`, `products`, `product_variants`, `coupons` |
| Route và thành phần liên quan | `/add-cart-ajax`, `CartController`, `AddCartAjaxRequest`, `CartService`, `CouponService`, `ShippingService` |

Ca sử dụng này là điểm chuyển tiếp giữa việc "xem" và "có ý định mua". Vì vậy, nó đòi hỏi phản hồi nhanh, validation rõ ràng và khả năng đồng bộ nhiều vùng giao diện cùng lúc như badge giỏ hàng, hover cart và quick cart.

### 2.4.13. Bảng 2.8. Đặc tả ca sử dụng đăng nhập và quay lại checkout

| Thuộc tính | Nội dung đặc tả |
|---|---|
| Mã ca sử dụng | `UC-AUTH-02` |
| Tên ca sử dụng | Đăng nhập khách hàng và quay lại bước checkout |
| Tác nhân chính | Khách hàng chưa xác thực |
| Mục tiêu | Bảo đảm người dùng không bị mất ngữ cảnh mua hàng khi bị yêu cầu đăng nhập trước checkout |
| Mô tả ngắn | Người dùng truy cập checkout khi chưa đăng nhập; hệ thống chuyển sang trang đăng nhập, sau đó đưa người dùng trở về đúng bước trước đó |
| Tiền điều kiện | Người dùng đã có giỏ hàng hoặc đã khởi tạo ý định mua; checkout yêu cầu xác thực |
| Kích hoạt | Truy cập `GET /checkout` khi chưa có phiên khách hàng hợp lệ |
| Luồng chính | 1. Người dùng mở checkout. 2. Hệ thống phát hiện chưa đăng nhập. 3. `StorefrontCustomerAuth` ghi nhớ đích quay lại. 4. Người dùng đăng nhập thành công. 5. Hệ thống chuyển hướng về `/checkout` |
| Luồng thay thế | Nếu người dùng vào trực tiếp trang đăng nhập, hệ thống có thể điều hướng về trang mặc định sau khi đăng nhập |
| Luồng ngoại lệ | Nếu email hoặc mật khẩu sai, hệ thống trả lỗi xác thực và giữ nguyên màn hình đăng nhập |
| Hậu điều kiện | Phiên khách hàng được tạo; người dùng quay về đúng bước checkout hoặc đích hợp lệ đã lưu |
| Dữ liệu liên quan | Session, `customers`, thông tin intended redirect |
| Route và thành phần liên quan | `/checkout`, `/dang-nhap`, `/login-customer`, `CheckoutController`, `LoginCustomerRequest`, `StorefrontCustomerAuth` |

Ca sử dụng này có ý nghĩa học thuật ở chỗ nó thể hiện sự kết hợp giữa bảo mật và UX. Nếu chỉ yêu cầu đăng nhập mà không duy trì ngữ cảnh, hệ thống sẽ gây gián đoạn hành vi mua hàng và làm giảm tỷ lệ hoàn tất đơn.

### 2.4.14. Bảng 2.9. Đặc tả ca sử dụng xác nhận đơn hàng

| Thuộc tính | Nội dung đặc tả |
|---|---|
| Mã ca sử dụng | `UC-CHECKOUT-01` |
| Tên ca sử dụng | Xác nhận đơn hàng |
| Tác nhân chính | Khách hàng |
| Mục tiêu | Chuyển dữ liệu từ giỏ hàng thành đơn hàng chính thức có thể theo dõi và xử lý vận hành |
| Mô tả ngắn | Khách hàng nhập hoặc chọn thông tin giao hàng, xác nhận dữ liệu thanh toán và gửi yêu cầu tạo đơn |
| Tiền điều kiện | Người dùng đã đăng nhập; giỏ hàng có dữ liệu hợp lệ; sản phẩm trong giỏ còn khả dụng |
| Kích hoạt | Người dùng nhấn xác nhận ở bước checkout |
| Luồng chính | 1. Request checkout được gửi. 2. Dữ liệu đầu vào được kiểm tra. 3. Hệ thống nạp giỏ hàng hiện hành. 4. Tính tổng tiền, giảm giá và phí giao hàng. 5. Tạo `orders`, `order_items`, `order_shipping`, `payments`. 6. Trả kết quả xác nhận cho người dùng |
| Luồng thay thế | Nếu phương thức thanh toán trực tuyến được chọn, hệ thống có thể điều hướng sang bước xử lý thanh toán riêng |
| Luồng ngoại lệ | Nếu giỏ trống, coupon sai, địa chỉ thiếu hoặc xảy ra lỗi transaction, hệ thống hủy thao tác và trả thông báo thích hợp |
| Hậu điều kiện | Đơn hàng được tạo với mã đơn duy nhất; lịch sử giao dịch được lưu lại |
| Dữ liệu liên quan | `carts`, `cart_items`, `customers`, `customer_addresses`, `shipping_fees`, `orders`, `order_items`, `order_shipping`, `payments` |
| Route và thành phần liên quan | `/checkout`, `/calculate-fee`, `/select-delivery-home`, `/confirm-order`, `ConfirmOrderRequest`, `CheckoutService`, `ShippingService` |

Đây là ca sử dụng trung tâm của toàn bộ website. Chất lượng thiết kế ở ca sử dụng này quyết định trực tiếp tới độ tin cậy của hệ thống, vì mọi sai sót ở bước tạo đơn đều ảnh hưởng đến dữ liệu tài chính và trải nghiệm khách hàng.

### 2.4.15. Bảng 2.10. Đặc tả ca sử dụng hủy đơn hàng

| Thuộc tính | Nội dung đặc tả |
|---|---|
| Mã ca sử dụng | `UC-ORDER-02` |
| Tên ca sử dụng | Hủy đơn hàng |
| Tác nhân chính | Khách hàng |
| Mục tiêu | Cho phép khách hàng chủ động dừng giao dịch khi đơn vẫn ở trạng thái phù hợp |
| Mô tả ngắn | Khách hàng mở lịch sử đơn và gửi yêu cầu hủy một đơn cụ thể |
| Tiền điều kiện | Khách hàng đã đăng nhập; đơn hàng thuộc về khách; trạng thái đơn cho phép hủy |
| Kích hoạt | Người dùng chọn thao tác hủy đơn tại màn hình lịch sử mua hàng |
| Luồng chính | 1. Hệ thống nhận mã đơn. 2. Kiểm tra quyền sở hữu đơn. 3. Kiểm tra trạng thái đơn. 4. Cập nhật trạng thái hủy và thời gian `cancelled_at`. 5. Trả thông báo kết quả cho khách hàng |
| Luồng thay thế | Nếu đơn không còn được phép hủy, hệ thống chỉ hiển thị trạng thái hiện tại mà không cho thao tác |
| Luồng ngoại lệ | Nếu đơn không tồn tại hoặc không thuộc về tài khoản hiện tại, hệ thống từ chối xử lý |
| Hậu điều kiện | Đơn hàng được cập nhật trạng thái hủy hoặc giữ nguyên trạng thái cũ nếu không hợp lệ |
| Dữ liệu liên quan | `orders`, `order_items`, `payments` |
| Route và thành phần liên quan | `/history`, `/view-history-order/{order_code}`, `/huy-don-hang`, `OrderController`, `LegacyOrderHistoryService` |

Ca sử dụng hủy đơn hàng đặc biệt hữu ích khi trình bày trong báo cáo vì nó cho thấy hệ thống xử lý cả giai đoạn sau mua, chứ không dừng lại ở thời điểm tạo đơn.

### 2.4.16. Bảng 2.11. Đặc tả ca sử dụng quản lý sản phẩm ở khu vực quản trị

| Thuộc tính | Nội dung đặc tả |
|---|---|
| Mã ca sử dụng | `UC-ADMIN-CATALOG-01` |
| Tên ca sử dụng | Quản lý sản phẩm trong khu vực quản trị |
| Tác nhân chính | Quản trị viên có quyền quản lý sản phẩm |
| Mục tiêu | Cho phép thêm mới, chỉnh sửa, bật tắt hoặc xóa sản phẩm, ảnh, tài liệu và dữ liệu liên quan |
| Mô tả ngắn | Admin thao tác trên form quản lý sản phẩm và gửi dữ liệu cập nhật về hệ thống |
| Tiền điều kiện | Admin đã đăng nhập; có quyền `MANAGE_PRODUCTS`; dữ liệu danh mục và thương hiệu nền tồn tại |
| Kích hoạt | Admin mở form thêm hoặc sửa sản phẩm và gửi request |
| Luồng chính | 1. Admin nhập dữ liệu sản phẩm. 2. Hệ thống kiểm tra hợp lệ. 3. `ProductService` mở transaction và cập nhật dữ liệu chính. 4. Đồng bộ media, document, category relation, pricing. 5. Invalidate cache. 6. Trả về danh sách hoặc màn hình chi tiết với thông báo thành công |
| Luồng thay thế | Nếu chỉ đổi trạng thái hiển thị, hệ thống xử lý route bật tắt mà không cần mở form đầy đủ |
| Luồng ngoại lệ | Nếu sản phẩm không tồn tại, dữ liệu sai định dạng, upload lỗi hoặc transaction lỗi, hệ thống rollback và trả thông báo thất bại |
| Hậu điều kiện | Dữ liệu catalog được cập nhật nhất quán và storefront phản ánh đúng trạng thái mới |
| Dữ liệu liên quan | `products`, `product_variants`, `product_media`, `product_categories`, `brands`, `documents` |
| Route và thành phần liên quan | `/add-product`, `/edit-product/{product_id}`, `/save-product`, `/update-product/{product_id}`, `ProductController`, `ProductService` |

### 2.4.17. Bảng 2.12. Đặc tả ca sử dụng quản lý vai trò và phân quyền

| Thuộc tính | Nội dung đặc tả |
|---|---|
| Mã ca sử dụng | `UC-ADMIN-SEC-01` |
| Tên ca sử dụng | Quản lý vai trò và phân quyền quản trị |
| Tác nhân chính | Quản trị viên cấp hệ thống |
| Mục tiêu | Bảo đảm mỗi người dùng nội bộ chỉ được truy cập các vùng chức năng đúng với trách nhiệm |
| Mô tả ngắn | Admin hệ thống gán vai trò, quyền hoặc thay đổi cấu hình truy cập cho người dùng nội bộ |
| Tiền điều kiện | Admin có quyền `MANAGE_ROLES` hoặc quyền hệ thống tương đương |
| Kích hoạt | Admin mở màn hình vai trò hoặc danh sách người dùng nội bộ |
| Luồng chính | 1. Admin chọn người dùng. 2. Hệ thống nạp các role và permission hiện có. 3. Admin gán hoặc bỏ gán quyền. 4. Hệ thống lưu thay đổi. 5. Middleware và guard áp dụng chính sách mới ở các request tiếp theo |
| Luồng thay thế | Admin chỉ xem thông tin quyền mà chưa thay đổi dữ liệu |
| Luồng ngoại lệ | Nếu người dùng không tồn tại, quyền không hợp lệ hoặc admin không có quyền cấp đủ cao, hệ thống từ chối thao tác |
| Hậu điều kiện | Vai trò và quyền của người dùng nội bộ được cập nhật và có hiệu lực |
| Dữ liệu liên quan | `users`, `roles`, `permissions`, bảng trung gian của Spatie Permission |
| Route và thành phần liên quan | `/create-spatie/{admin_id}`, `/create-permission/{admin_id}`, `/assign-role/{admin_id}`, `/assign-permission/{admin_id}`, `UserRolesController`, middleware `admin.permission:*` |

### 2.4.18. Bảng 2.13. Đặc tả ca sử dụng chat hỗ trợ khách hàng

| Thuộc tính | Nội dung đặc tả |
|---|---|
| Mã ca sử dụng | `UC-CHAT-01` |
| Tên ca sử dụng | Chat hỗ trợ giữa khách hàng và hệ thống |
| Tác nhân chính | Khách vãng lai, khách hàng, quản trị viên |
| Mục tiêu | Hỗ trợ giải đáp nhanh thắc mắc, giữ lịch sử hội thoại và tạo kênh liên hệ trực tiếp với admin |
| Mô tả ngắn | Người dùng gửi tin nhắn ở storefront; hệ thống tạo hoặc nạp conversation; phản hồi được lưu và đồng bộ về inbox quản trị |
| Tiền điều kiện | Kênh chat đang hoạt động; người dùng có session hợp lệ hoặc tài khoản khách hàng |
| Kích hoạt | Người dùng mở widget chat và gửi tin nhắn |
| Luồng chính | 1. Frontend tạo hoặc lấy conversation. 2. Tin nhắn được gửi tới backend. 3. Hệ thống lưu `chat_messages`. 4. Chatbot hoặc admin phản hồi. 5. Lịch sử hội thoại được cập nhật ở cả storefront và admin |
| Luồng thay thế | Nếu chưa có customer đăng nhập, hệ thống dùng `session_token` để nhận diện conversation vô danh |
| Luồng ngoại lệ | Nếu lưu tin nhắn lỗi hoặc không truy xuất được conversation, hệ thống trả lỗi có kiểm soát và ghi nhận phục vụ debug |
| Hậu điều kiện | Tin nhắn và trạng thái hội thoại được ghi nhận đầy đủ; người dùng nhận phản hồi |
| Dữ liệu liên quan | `chat_conversations`, `chat_messages`, `customers`, `users` |
| Route và thành phần liên quan | `/chatbot/conversation`, `/chatbot/messages`, `/chat-inbox`, `ChatController`, `ChatInboxController`, `ChatbotConversationService`, `ChatInboxService` |

### 2.4.19. Nhận xét về bộ bảng đặc tả ca sử dụng

Việc trình bày ca sử dụng dưới dạng bảng giúp tăng tính chuẩn hóa và khả năng đánh giá học thuật của báo cáo. Mỗi bảng đặc tả không chỉ mô tả "hệ thống làm gì", mà còn làm rõ điều kiện trước, dữ liệu bị tác động, nhánh ngoại lệ và các thành phần kỹ thuật liên quan trong mã nguồn. Đây chính là cầu nối hữu ích giữa lý thuyết phân tích thiết kế và hệ thống triển khai thực tế.

## 2.5. Sơ đồ hoạt động (activity diagram)

### 2.5.1. Hình 2.6. Sơ đồ hoạt động tìm kiếm và gợi ý tìm kiếm

Sơ đồ hoạt động của chức năng tìm kiếm bắt đầu khi người dùng nhập từ khóa vào ô tìm kiếm. Hệ thống gửi request gợi ý, trả về danh sách sản phẩm hoặc gợi ý liên quan, sau đó người dùng có thể chọn một kết quả hoặc chuyển sang trang tìm kiếm đầy đủ. Luồng này phản ánh khá rõ hành vi khám phá sản phẩm của người dùng.

### 2.5.2. Hình 2.7. Sơ đồ hoạt động thêm sản phẩm vào giỏ hàng

Luồng bắt đầu khi người dùng chọn sản phẩm và nhấn thêm vào giỏ hàng. Hệ thống kiểm tra tính hợp lệ của sản phẩm, số lượng, tồn kho và biến thể được chọn. Nếu hợp lệ, giỏ hàng được cập nhật và các thành phần liên quan như badge, quick cart hoặc hover cart được đồng bộ lại. Nếu không hợp lệ, hệ thống trả thông báo phù hợp.

### 2.5.3. Hình 2.8. Sơ đồ hoạt động đăng nhập rồi quay lại checkout

Luồng này bắt đầu khi khách hàng truy cập checkout nhưng chưa đăng nhập. Hệ thống ghi nhớ đích quay lại, chuyển người dùng sang màn hình đăng nhập, sau đó sau khi xác thực thành công sẽ điều hướng về đúng bước checkout ban đầu. Đây là một điểm thể hiện rõ giá trị của support class và session handling trong hệ thống.

### 2.5.4. Hình 2.9. Sơ đồ hoạt động cập nhật sản phẩm ở khu vực quản trị

Quản trị viên mở form sản phẩm, nhập dữ liệu mới và gửi yêu cầu cập nhật. Hệ thống kiểm tra dữ liệu đầu vào, ánh xạ payload, xử lý giá, media, document, category relation và cập nhật cache. Nếu xảy ra lỗi, hệ thống quay lại form với thông báo phù hợp. Nếu thành công, dữ liệu được lưu và người quản trị được chuyển về danh sách sản phẩm.

### 2.5.5. Hình 2.10. Sơ đồ hoạt động áp dụng mã giảm giá

Trong hoạt động áp dụng coupon, khách hàng nhập hoặc chọn mã giảm giá ở giỏ hàng. Hệ thống tiếp nhận request, chuẩn hóa mã coupon, kiểm tra sự tồn tại của coupon trong hệ thống, thời gian hiệu lực, trạng thái hoạt động, giới hạn sử dụng, điều kiện giá trị đơn hàng tối thiểu và khả năng dùng lặp trên cùng khách hàng. Nếu bất kỳ điều kiện nào không đạt, hệ thống phải giữ nguyên giỏ hàng và trả thông báo lỗi rõ ràng.

Nếu coupon hợp lệ, hệ thống tiến hành tính toán lại tổng giảm giá, tổng tạm tính và số tiền phải trả sau cùng. Hoạt động này cần được mô hình hóa vì nó thể hiện rõ sự tương tác giữa dữ liệu giỏ hàng và dữ liệu khuyến mãi, đồng thời cho thấy vì sao `CouponService` và `coupon_redemptions` có vai trò quan trọng trong thiết kế tổng thể.

### 2.5.6. Hình 2.11. Sơ đồ hoạt động hủy đơn hàng của khách hàng

Khi khách hàng mở trang lịch sử mua hàng và chọn hủy đơn, hệ thống trước tiên phải xác minh rằng đơn hàng thuộc về chính khách đang đăng nhập. Tiếp đó, hệ thống kiểm tra trạng thái đơn có còn nằm trong phạm vi được phép hủy hay không. Nếu đơn đã được xác nhận sâu, đã giao hoặc đã hoàn tất, thao tác hủy cần bị chặn với thông báo phù hợp.

Nếu đơn còn ở trạng thái cho phép, hệ thống cập nhật trạng thái đơn sang hủy, ghi mốc thời gian `cancelled_at`, đồng thời xử lý các dữ liệu liên quan như payment status hoặc ghi log nghiệp vụ nếu cần. Mô hình hóa activity này giúp phần báo cáo thể hiện rõ website không chỉ hỗ trợ đặt hàng mà còn hỗ trợ quản lý vòng đời giao dịch sau mua.

### 2.5.7. Hình 2.12. Sơ đồ hoạt động trả lời chat ở khu vực quản trị

Ở phía quản trị, một activity quan trọng là tiếp nhận hội thoại hỗ trợ từ storefront, xem nội dung tin nhắn và phản hồi lại cho khách. Luồng hoạt động bắt đầu từ danh sách conversation, admin mở một cuộc hội thoại cụ thể, xem các message trước đó, nhập phản hồi, gửi phản hồi và cập nhật trạng thái conversation nếu cần.

Hoạt động này đòi hỏi dữ liệu hội thoại phải được đồng bộ giữa storefront và admin, đồng thời có khả năng phân biệt người gửi là khách, bot hay quản trị viên. Đây là một luồng hữu ích để minh họa rằng website không chỉ bán hàng mà còn có bộ phận hỗ trợ người dùng gắn trực tiếp với dữ liệu ứng dụng.

### 2.5.8. Nhận xét về các nhánh ngoại lệ trong sơ đồ hoạt động

Điểm quan trọng của sơ đồ hoạt động trong đề tài không nằm ở việc vẽ ra một luồng đẹp mắt, mà nằm ở khả năng chỉ ra các nhánh ngoại lệ. Trong website thương mại điện tử, phần lớn lỗi phát sinh không đến từ luồng thành công, mà đến từ trạng thái dữ liệu không hợp lệ như sản phẩm hết hàng, coupon hết hạn, đơn không thuộc về khách đang đăng nhập hoặc quản trị viên không có quyền truy cập chức năng.

Vì vậy, khi đưa sơ đồ hoạt động vào báo cáo, cần mô tả cả các nhánh rẽ như validation fail, not found, unauthorized, not allowed by status, transaction fail, cache invalidation fail hoặc redirect back với thông báo. Điều này làm cho sơ đồ hoạt động có giá trị học thuật cao hơn và bám sát hơn với tính chất thật của hệ thống.

## 2.6. Sơ đồ tuần tự (sequence diagram)

### 2.6.1. Hình 2.13. Sơ đồ tuần tự thêm giỏ hàng bằng AJAX

Trong nghiệp vụ này, trình duyệt gửi request `POST /add-cart-ajax` tới controller. Controller đi qua request validation, sau đó gọi `CartService` để thêm item. Tiếp theo, hệ thống lấy lại số lượng giỏ hàng, danh sách item, coupon hiện tại, phí vận chuyển và tổng tiền để trả về JSON response. Luồng này cho thấy sự phối hợp giữa frontend AJAX, request validation, service layer và session/cart state.

### 2.6.2. Hình 2.14. Sơ đồ tuần tự đăng nhập và quay lại checkout

Khi khách hàng truy cập checkout nhưng chưa xác thực, controller sử dụng `StorefrontCustomerAuth` để lưu intended redirect rồi chuyển người dùng tới màn hình đăng nhập. Sau khi `POST /login-customer` thành công, controller đọc lại đích điều hướng và trả người dùng về `checkout` hoặc một URL nội bộ hợp lệ. Đây là sequence có giá trị thực tế cao vì phản ánh đúng một điểm tinh chỉnh trải nghiệm mua hàng.

### 2.6.3. Hình 2.15. Sơ đồ tuần tự xác nhận đơn hàng

Luồng xác nhận đơn hàng bắt đầu từ checkout, nơi khách hàng gửi thông tin giao hàng và lựa chọn phương thức thanh toán. Controller nhận request, request class kiểm tra dữ liệu, service layer xử lý kiểm tra giỏ hàng, tính toán, tạo order, tạo order_items, payment records và trạng thái liên quan. Sau cùng, hệ thống trả kết quả xác nhận hoặc điều hướng sang bước thanh toán phù hợp.

### 2.6.4. Hình 2.16. Sơ đồ tuần tự quản lý chat hỗ trợ

Khi người dùng mở chatbot và gửi tin nhắn, hệ thống tạo hoặc nạp conversation, lưu message, gọi service phản hồi và trả nội dung về frontend. Song song với đó, inbox quản trị có thể truy xuất cuộc hội thoại, xem lịch sử và xử lý phản hồi. Đây là một luồng phản ánh rõ việc hệ thống có thêm lớp hỗ trợ người dùng ngoài các nghiệp vụ mua hàng thông thường.

### 2.6.5. Hình 2.17. Sơ đồ tuần tự cập nhật sản phẩm ở khu vực quản trị

Quản trị viên gửi form cập nhật sản phẩm tới `ProductController`. Request validation xác minh dữ liệu, controller ánh xạ payload rồi gọi `ProductService`. Service mở transaction, cập nhật pricing, media, document, categories và các thông tin liên quan, sau đó invalidates cache trước khi controller trả về phản hồi thành công hoặc thất bại. Đây là sequence khá đại diện cho hướng chuẩn hóa business logic trong dự án.

### 2.6.6. Hình 2.18. Sơ đồ tuần tự lấy danh sách sản phẩm qua API

Trong luồng API đọc dữ liệu, client gửi request tới endpoint như `GET /api/v1/products` hoặc các endpoint lọc theo category, brand, latest, featured. Route API tiếp nhận request, chuyển đến `Api\ProductController`, controller kiểm tra tham số, gọi service hoặc lớp truy vấn dữ liệu, nhận kết quả, áp dụng phân trang hoặc filter rồi trả JSON response cho client.

Điểm đáng chú ý của sequence này là response không render view mà trả contract dữ liệu ổn định, thích hợp cho client khác như frontend SPA hoặc mobile app trong tương lai. Khi mô hình hóa sequence này trong báo cáo, cần nhấn mạnh rằng đây là một bằng chứng cho thấy hệ thống đã được tổ chức theo hướng đa kênh hiển thị thay vì thuần Blade server-rendered.

### 2.6.7. Hình 2.19. Sơ đồ tuần tự gửi đánh giá sản phẩm

Khách hàng truy cập trang chi tiết sản phẩm, nhập nội dung đánh giá hoặc số sao rồi gửi request. Controller nhận request, request validation kiểm tra dữ liệu đầu vào, xác minh sản phẩm tồn tại và có thể kiểm tra khách hàng có đủ điều kiện đánh giá hay không. Tiếp đó, `ProductReviewService` hoặc lớp xử lý tương ứng tạo review mới với trạng thái chờ duyệt hoặc công khai tùy thiết kế.

Sequence này có giá trị vì nó cho thấy một luồng tương tác người dùng không kết thúc ngay sau khi lưu vào database. Dữ liệu review còn có vòng đời tiếp theo như chờ duyệt, hiển thị công khai, trả lời hoặc bị từ chối. Nói cách khác, sequence của review là ví dụ điển hình cho thiết kế nghiệp vụ nhiều bước trong một chức năng tưởng chừng đơn giản.

### 2.6.8. Hình 2.20. Sơ đồ tuần tự sinh báo cáo product discovery

Ở phía quản trị, khi admin mở báo cáo product discovery, controller báo cáo nhận request và chuyển xử lý cho `ProductDiscoveryReportService`. Service tập hợp dữ liệu từ `product_views`, `product_search_queries`, `product_recommendation_impressions` và có thể kết hợp với catalog để tạo ra bảng thống kê về mức độ khám phá sản phẩm. Sau khi tổng hợp, controller render kết quả ở giao diện admin hoặc trả về API JSON nếu là admin API.

Sequence này cho thấy một lớp năng lực quan trọng của hệ thống: không chỉ xử lý giao dịch tức thời mà còn tổng hợp dữ liệu hành vi qua nhiều bảng để tạo tri thức quản trị. Đây là điểm làm cho đề tài có chiều sâu hơn so với các đồ án chỉ dừng ở thao tác CRUD.

### 2.6.9. Ý nghĩa của sơ đồ tuần tự đối với việc hiểu mã nguồn

Sơ đồ tuần tự giúp chuyển cái nhìn từ "chức năng có tồn tại" sang "chức năng được phối hợp giữa những thành phần nào". Trong mã nguồn hiện tại, sơ đồ tuần tự làm nổi rõ những thành phần dễ bị bỏ qua nếu chỉ nhìn bề mặt như lớp kiểm tra dữ liệu đầu vào, lớp hỗ trợ, dịch vụ cache, dịch vụ vận chuyển, dịch vụ coupon, dịch vụ hộp thư trò chuyện hoặc các bảng lịch sử giao dịch.

Đặc biệt với dự án đang ở trạng thái chuyển tiếp, sơ đồ tuần tự còn có vai trò làm rõ những nơi nào logic vẫn đi thẳng từ controller xuống lớp dữ liệu cũ, và những nơi nào đã được chuẩn hóa qua lớp dịch vụ. Điều này có giá trị trực tiếp cho việc đánh giá mức độ hoàn thiện của kiến trúc hiện tại và định hướng tái cấu trúc về sau.

## 2.7. Sơ đồ lớp (class diagram)

Sơ đồ lớp ở mức khái niệm của hệ thống có thể tổ chức quanh các nhóm thực thể chính sau: `ProductCategory`, `Brand`, `Product`, `ProductVariant`, `ProductMedia`, `Customer`, `Cart`, `CartItem`, `Order`, `OrderItem`, `Payment`, `Coupon`, `ProductReview`, `ChatConversation`, `ChatMessage`, `User`, `Role` và `Permission`.

Nhóm catalog gồm `ProductCategory`, `Brand`, `Product`, `ProductVariant`, `ProductMedia` và `Attribute`. Nhóm mua hàng gồm `Customer`, `Cart`, `CartItem`, `Order`, `OrderItem`, `OrderShipping`, `Payment` và `PaymentTransaction`. Nhóm nội dung gồm `BlogCategory`, `BlogPost`, `Video`, `Banner` và `Document`. Nhóm hỗ trợ gồm `ProductReview`, `ChatConversation`, `ChatMessage`, `Setting`, `Metric` và `Alert`.

**Hình 2.21. Sơ đồ lớp tổng quát của hệ thống**

Hình 2.21 phản ánh cấu trúc logic giữa các thực thể nghiệp vụ chính và cho thấy mối liên hệ giữa catalog, khách hàng, đơn hàng, nội dung, phân quyền và hỗ trợ người dùng trong hệ thống.

### 2.7.1. Nhóm lớp catalog

Nhóm lớp catalog là hạt nhân của website bán thiết bị công nghệ. `ProductCategory` thể hiện cấu trúc phân loại dữ liệu, có thể có quan hệ cha con thông qua `parent_id`. `Brand` mô tả thương hiệu sản phẩm và đóng vai trò hỗ trợ điều hướng hoặc lọc catalog. `Product` là lớp trung tâm, chứa phần thông tin định danh, giá, mô tả, tình trạng hiển thị và các liên kết tới thương hiệu hoặc danh mục chính.

`ProductVariant` tách phần biến thể ra khỏi `Product`, cho phép một sản phẩm có nhiều cấu hình hoặc phiên bản khác nhau nhưng vẫn giữ chung một bản sắc sản phẩm. `ProductMedia` tách ảnh và tài nguyên media khỏi `Product`, giúp thiết kế dữ liệu linh hoạt hơn. Khi dựng sơ đồ lớp, nhóm lớp catalog nên thể hiện rõ các quan hệ một-nhiều giữa các lớp này và cách `Product` giữ vai trò trung tâm kết nối.

### 2.7.2. Nhóm lớp mua hàng và giao dịch

Nhóm lớp này gồm `Customer`, `CustomerAddress`, `Cart`, `CartItem`, `Order`, `OrderItem`, `OrderShipping`, `Payment`, `PaymentTransaction`, `Coupon`, `CouponRedemption`. Đây là phần mô tả trực tiếp vòng đời mua hàng của khách.

`Customer` có thể có nhiều địa chỉ, nhiều đơn hàng, nhiều đánh giá và có thể sở hữu một giỏ hàng đang hoạt động. `Cart` chứa nhiều `CartItem`, còn `Order` chứa nhiều `OrderItem` và có một snapshot giao hàng tại `OrderShipping`. `Payment` và `PaymentTransaction` tách biệt nhằm mô tả đầy đủ vòng đời thanh toán. Class diagram của nhóm này cần thể hiện rõ quan hệ giữa dữ liệu tạm thời trước mua và dữ liệu lịch sử sau khi mua.

### 2.7.3. Nhóm lớp nội dung, hỗ trợ và phân tích

Ngoài phần giao dịch, hệ thống còn có các lớp như `BlogCategory`, `BlogPost`, `Video`, `Banner`, `Setting`, `ProductReview`, `ChatConversation`, `ChatMessage`, `AnalyticsEvent`, `ProductView`, `Metric`, `Alert`. Đây là các lớp cho thấy website không chỉ để bán hàng mà còn phục vụ nội dung, hỗ trợ khách hàng và giám sát vận hành.

Trong sơ đồ lớp, nhóm lớp này có vai trò mở rộng bức tranh hệ thống, giúp người đọc thấy được mã nguồn hiện tại đang bao phủ nhiều mảng chức năng hơn so với một trang web bán hàng tối giản. Đồng thời, nó cũng giải thích vì sao Chương 2 cần trình bày thêm các phần báo cáo, giám sát vận hành và phân tích dữ liệu.

### 2.7.4. Nhóm lớp người dùng nội bộ và phân quyền

Ở phía quản trị, `User`, `Role`, `Permission` là các lớp có tác động rất lớn tới an toàn hệ thống. Chúng không trực tiếp tạo ra doanh thu, nhưng quyết định ai có thể truy cập, chỉnh sửa hoặc theo dõi từng vùng nghiệp vụ. Khi dựng sơ đồ lớp, cần làm rõ `User` có thể có nhiều role, mỗi role có thể gắn nhiều permission, và các route hoặc API phía admin phụ thuộc trực tiếp vào cấu trúc này.

### 2.7.5. Vai trò của sơ đồ lớp trong báo cáo

Class diagram ở đây không nhằm tái hiện toàn bộ lớp PHP trong mã nguồn, bởi điều đó sẽ làm sơ đồ trở nên quá rối và ít giá trị học thuật. Thay vào đó, sơ đồ nên đóng vai trò là mô hình khái niệm ở mức miền nghiệp vụ, thể hiện các thực thể cốt lõi, quan hệ giữa chúng và trách nhiệm nghiệp vụ chính. Cách tiếp cận này vừa bám được hệ thống thực tế, vừa giúp sơ đồ rõ ràng và dễ diễn giải hơn trong báo cáo.

## 2.8. Mô hình dữ liệu (ERD)

### 2.8.1. Nguyên tắc thiết kế dữ liệu

Thiết kế dữ liệu của hệ thống được xây dựng trên hai nguyên tắc. Nguyên tắc thứ nhất là vẫn phải duy trì được khả năng vận hành của các luồng legacy. Nguyên tắc thứ hai là từng bước chuẩn hóa schema để phục vụ API, báo cáo, analytics và khả năng mở rộng sau này.

### 2.8.2. Hình 2.22. ERD tổng quát của hệ thống

ERD tổng quát của hệ thống cần phản ánh đồng thời hai lớp dữ liệu. Một mặt là lớp dữ liệu cũ đang vận hành ở nhiều luồng của giao diện khách hàng và khu vực quản trị. Mặt khác là lớp lược đồ mới gồm các nhóm bảng được chuẩn hóa hơn cho sản phẩm, giỏ hàng, đơn hàng, thanh toán, nội dung, marketing, loyalty, chat và phân tích dữ liệu.

### 2.8.3. Phân nhóm thực thể trong ERD

Có thể chia các thực thể chính của ERD thành các nhóm sau:

- nhóm catalog: category, brand, product, variant, media, attribute, review
- nhóm checkout và order: customer, address, cart, cart item, shipping fee, order, order item, order shipping, payment, payment transaction, coupon
- nhóm CMS và media: blog category, blog post, video, document, banner, site contact
- nhóm hỗ trợ và phân tích: chat conversation, chat message, setting, page view, product view, analytics event, metric, alert

### 2.8.4. Mối quan hệ chính trong mô hình dữ liệu

`Product` thuộc `Brand` và có thể thuộc nhiều `ProductCategory`. Một `Product` có thể có nhiều `ProductVariant`, nhiều `ProductMedia` và nhiều `ProductReview`. `Customer` có thể có nhiều `CustomerAddress`, nhiều `Order`, nhiều `CartItem` thông qua `Cart`. `Order` có nhiều `OrderItem` và liên kết tới `Payment`, `OrderShipping`. `Coupon` có thể được áp dụng vào nhiều đơn hàng và được theo dõi qua `CouponRedemption`.

Ngoài ra, `ChatConversation` liên kết với `Customer` và có nhiều `ChatMessage`. Các bảng analytics như `ProductView`, `PageView`, `AnalyticsEvent` hoặc `UserSession` đóng vai trò bổ sung cho việc theo dõi hành vi người dùng.

### 2.8.5. Dữ liệu legacy và schema mới

Điểm đặc thù của dự án là một số luồng vẫn bám vào các bảng như `tbl_product`, `tbl_category_product`, `tbl_brand_product`, `tbl_customers`, `tbl_order`, `tbl_coupon`, `tbl_post` và `tbl_video`. Trong khi đó, lớp migration mới mở rộng hệ thống bằng các bảng chuẩn hóa hơn như `products`, `product_categories`, `orders`, `order_items`, `payments`, `blog_posts`, `product_reviews`, `chat_conversations`, `analytics_events`.

Vì vậy, phần ERD cần được trình bày như một mô hình dữ liệu chuyển tiếp, phản ánh trung thực việc hệ thống đang duy trì song song cấu trúc dữ liệu cũ và lớp lược đồ mới được chuẩn hóa dần.

### 2.8.6. Chuẩn hóa dữ liệu và mức độ chuẩn hóa trong hệ thống

Nếu nhìn theo các nguyên tắc chuẩn hóa, có thể thấy lược đồ mới của hệ thống đã đi khá xa so với dữ liệu cũ. Thay vì gộp nhiều loại dữ liệu vào cùng một bảng, thiết kế mới tách sản phẩm, biến thể, media, địa chỉ, thông tin giao hàng chụp lại theo thời điểm, giao dịch thanh toán, lần sử dụng coupon và lịch sử chat thành các bảng riêng. Điều này giúp giảm dư thừa dữ liệu, tăng khả năng tái sử dụng và làm rõ trách nhiệm của từng thực thể.

Tuy nhiên, hệ thống vẫn chưa ở trạng thái “thuần chuẩn hóa” tuyệt đối bởi các bảng legacy vẫn được duy trì cho nhiều luồng đang hoạt động. Chính đặc điểm này làm cho ERD của dự án có hai lớp: lớp vận hành hiện tại và lớp đích kiến trúc. Việc mô tả trung thực hai lớp này trong báo cáo giúp phần phân tích có giá trị thực tiễn cao hơn.

### 2.8.7. Chiến lược chuyển đổi dữ liệu từ legacy sang schema mới

Một trong những khía cạnh quan trọng của thiết kế dữ liệu là chiến lược chuyển đổi. Với dự án này, phương án hợp lý không phải là thay thế đột ngột toàn bộ bảng `tbl_*` bằng schema mới, mà là xây dựng mapping rõ ràng giữa bảng cũ và bảng mới, tạo migration chuẩn, viết command hỗ trợ di trú dữ liệu theo lô và từng bước chuyển logic của ứng dụng sang bảng mới.

Ví dụ, `tbl_product` cần được ánh xạ sang `products`; `tbl_category_product` sang `product_categories`; `tbl_order` sang `orders`; `tbl_order_details` sang `order_items`; `tbl_coupon` sang `coupons`; `tbl_shipping` sang `customer_addresses` hoặc `order_shipping` tùy ngữ cảnh snapshot. Chiến lược này vừa giữ cho hệ thống tiếp tục vận hành, vừa tạo tiền đề để API bám vào schema mới nhất quán hơn.

### 2.8.8. Vai trò của dữ liệu phân tích trong ERD

Điểm khác biệt của dự án là ERD không chỉ có dữ liệu nghiệp vụ giao dịch mà còn có dữ liệu hành vi và vận hành. Các bảng `analytics_events`, `product_views`, `product_search_queries`, `product_recommendation_impressions`, `metrics`, `alerts` phản ánh nhu cầu ghi nhận hành vi người dùng, hiệu quả recommendation, sức khỏe hệ thống và các cảnh báo giám sát.

Việc đưa nhóm dữ liệu này vào ERD mang ý nghĩa lớn ở cả hai mặt. Một mặt, nó giúp hệ thống có khả năng tạo báo cáo quản trị giá trị hơn. Mặt khác, nó buộc thiết kế dữ liệu phải tính tới khối lượng ghi nhận lớn, chỉ mục theo thời gian và khả năng truy vấn tổng hợp. Đây là điểm cho thấy đề tài đã tiếp cận bài toán website thương mại điện tử ở mức rộng hơn giao dịch thuần túy.

### 2.8.9. Ý nghĩa của ERD đối với triển khai sau này

ERD không chỉ là sơ đồ mô tả bảng, mà còn là cơ sở để kiểm tra tính đúng đắn của migration, model, factory, seeder, request validation và report queries. Nếu ERD được thiết kế rõ, việc mở rộng sau này như loyalty, automation marketing, mobile app hay warehouse integration sẽ có nền ổn hơn rất nhiều.

Đối với đề tài này, ERD còn đóng vai trò như một tài liệu “hòa giải” giữa legacy và schema mới. Nó giúp người đọc hiểu tại sao một số luồng hiện vẫn còn đi qua bảng cũ, đồng thời nhìn thấy cấu trúc chuẩn hóa mà hệ thống đang hướng tới.

## 2.9. Thiết kế giao diện người dùng

### 2.9.1. Thiết kế giao diện khách hàng (storefront)

Storefront được tổ chức theo hướng ưu tiên trải nghiệm mua sắm và khám phá sản phẩm. Các vùng giao diện chính gồm header, thanh tìm kiếm, hero/banner, flash sale, danh sách sản phẩm, khối recommendation, footer và chatbot. Mục tiêu của giao diện storefront là giúp người dùng tiếp cận sản phẩm nhanh, đọc thông tin dễ và đi vào luồng mua hàng một cách tự nhiên.

### 2.9.2. Thiết kế giao diện chi tiết sản phẩm và giỏ hàng

Trang chi tiết sản phẩm cần ưu tiên hình ảnh, tên sản phẩm, giá, khuyến mãi, biến thể, thông số kỹ thuật, đánh giá và các nút hành động. Giỏ hàng cần cho phép cập nhật số lượng, xóa item, xem tổng tiền, áp dụng coupon và đi tiếp tới checkout. Hai màn hình này có vai trò rất lớn trong quá trình chuyển đổi từ khám phá sang mua hàng.

### 2.9.3. Thiết kế giao diện checkout và lịch sử đơn hàng

Checkout được thiết kế để giảm ma sát nhưng vẫn bảo đảm đủ dữ liệu cần thiết cho đặt hàng. Luồng này gắn với đăng nhập khách hàng, thông tin giao hàng, tính phí vận chuyển và xác nhận đơn. Phần lịch sử đơn hàng giúp khách hàng theo dõi trạng thái sau mua và tăng cảm giác kiểm soát đối với giao dịch đã thực hiện.

### 2.9.4. Thiết kế giao diện quản trị

Giao diện quản trị được tổ chức theo mô hình dashboard, có sidebar điều hướng, topbar, quick search, profile, settings và các trang chức năng theo module. Thiết kế này hướng tới việc giúp quản trị viên truy cập nhanh tới các nhóm nghiệp vụ lớn như sản phẩm, nội dung, đơn hàng, người dùng, quyền truy cập và báo cáo.

### 2.9.5. Nguyên tắc thiết kế UX/UI của hệ thống

Một số nguyên tắc chính được áp dụng gồm: điều hướng rõ ràng, ưu tiên thông tin quan trọng, phản hồi thao tác dễ hiểu, giảm số bước không cần thiết trong luồng mua hàng, hỗ trợ hiển thị tốt trên nhiều thiết bị và tổ chức giao diện quản trị theo hướng dễ vận hành lâu dài.

### 2.9.6. Phân tích màn hình giao diện khách hàng theo hành trình người dùng

Nếu nhìn storefront như một hành trình, có thể chia thành bốn lớp màn hình chính. Lớp thứ nhất là màn hình thu hút và định hướng như trang chủ, banner, khối danh mục, thương hiệu, flash sale. Lớp thứ hai là màn hình khám phá như danh mục, thương hiệu, kết quả tìm kiếm, tag và recommendation. Lớp thứ ba là màn hình ra quyết định như chi tiết sản phẩm, quickview, so sánh, đánh giá. Lớp thứ tư là màn hình giao dịch như giỏ hàng, checkout, lịch sử đơn hàng và theo dõi đơn.

Mỗi lớp màn hình có mục tiêu khác nhau nên cũng cần nguyên tắc thiết kế khác nhau. Trang chủ cần nhấn mạnh điều hướng và điểm nhấn thị giác. Trang danh mục cần ưu tiên khả năng quét nhanh danh sách sản phẩm. Trang chi tiết cần đặt trọng tâm vào hình ảnh, giá, biến thể và thông tin mua hàng. Giỏ hàng và checkout lại cần tối ưu độ rõ ràng, giảm sai sót và tạo cảm giác kiểm soát cho người dùng.

### 2.9.7. Thiết kế màn hình đăng nhập, checkout và hậu mua

Phần đăng nhập khách hàng trong hệ thống không nên được xem là màn hình độc lập tách rời, mà là một đoạn trong hành trình mua hàng. Vì vậy, thiết kế của nó phải hỗ trợ quay lại đúng đích đến ban đầu nếu khách bị chặn ở checkout. Đây là một chi tiết UX quan trọng và đã được mã nguồn xử lý qua cơ chế nhớ intended redirect.

Màn hình checkout cần giảm tối đa sự phân tán chú ý. Các nhóm thông tin nên được tổ chức theo thứ tự tự nhiên: thông tin người nhận, địa chỉ giao hàng, phí vận chuyển, tóm tắt đơn, phương thức thanh toán, ghi chú và nút xác nhận. Sau khi đặt hàng, phần lịch sử đơn hàng phải đóng vai trò tiếp tục trải nghiệm, giúp khách hàng cảm thấy giao dịch được ghi nhận và có thể tra cứu bất cứ lúc nào.

### 2.9.8. Thiết kế màn hình quản trị theo tác vụ

Khác với storefront, admin phải phục vụ người dùng nội bộ thực hiện nhiều tác vụ lặp lại. Vì vậy, thiết kế giao diện quản trị cần ưu tiên hiệu quả thao tác, nhận diện nhanh trạng thái dữ liệu và giảm số lần phải chuyển màn hình không cần thiết. Sidebar, topbar, quick search, dashboard cards, filter theo thời gian và các bảng dữ liệu là các thành phần quan trọng của bố cục quản trị.

Trong module catalog, form sản phẩm nên gom thông tin theo nhóm như thông tin chung, giá, hình ảnh, tài liệu, danh mục, thương hiệu, trạng thái hiển thị. Trong module đơn hàng, giao diện nên nhấn mạnh mã đơn, trạng thái, tổng tiền, phương thức thanh toán và thông tin giao hàng. Trong module báo cáo, biểu đồ hoặc bảng thống kê cần hiển thị được chỉ số quan trọng mà không làm người quản trị bị quá tải thông tin.

### 2.9.9. Nguyên tắc responsive, phản hồi thao tác và khả năng tiếp cận

Do website được truy cập từ nhiều loại thiết bị, thiết kế giao diện cần được tổ chức responsive, đặc biệt ở các vùng như thanh tìm kiếm, danh sách sản phẩm, quick cart, bảng so sánh, form checkout và các bảng quản trị. Các thành phần quan trọng phải co giãn hợp lý, giữ được thứ tự ưu tiên thông tin và tránh tạo thao tác khó trên màn hình nhỏ.

Phản hồi thao tác cũng là yêu cầu cốt lõi của thiết kế UI. Hệ thống cần cho người dùng biết một thao tác đang được xử lý, đã thành công hay thất bại, bằng các thông báo, badge, thay đổi trạng thái nút hoặc cập nhật vùng giao diện liên quan. Ngoài ra, một website tốt còn nên tính tới khả năng tiếp cận ở mức cơ bản như độ tương phản, cấu trúc heading, label rõ ràng, hành vi form nhất quán và nội dung dễ hiểu.

## 2.10. Thiết kế cơ sở dữ liệu

### 2.10.1. Bảng `users`: Người dùng quản trị

Bảng `users` dùng để lưu thông tin tài khoản người dùng nội bộ hoặc quản trị viên trong hệ thống. Các trường quan trọng thường bao gồm định danh, tên, email, mật khẩu, trạng thái xác thực và các metadata liên quan. Đây là bảng gắn trực tiếp với cơ chế phân quyền và xác thực ở khu vực quản trị.

### 2.10.2. Bảng `product_categories`: Danh mục sản phẩm

Bảng `product_categories` lưu cấu trúc danh mục sản phẩm theo hướng chuẩn hóa hơn so với bảng legacy. Bảng này hỗ trợ tên danh mục, slug, mô tả, trạng thái hiển thị và quan hệ cha con nếu có. Đây là bảng nền cho việc tổ chức catalog của hệ thống.

### 2.10.3. Bảng `brands`: Thương hiệu

Bảng `brands` lưu thông tin thương hiệu sản phẩm như tên thương hiệu, slug, mô tả ngắn, trạng thái và metadata liên quan. Mối quan hệ giữa `brands` và `products` giúp người dùng có thể duyệt sản phẩm theo thương hiệu, đồng thời hỗ trợ quản trị dữ liệu catalog rõ ràng hơn.

### 2.10.4. Bảng `products`: Sản phẩm

Bảng `products` là bảng trung tâm của nhóm catalog. Đây là nơi lưu các thông tin như tên sản phẩm, slug, mô tả, giá, giá khuyến mãi, trạng thái, thương hiệu chính, danh mục chính và nhiều metadata khác liên quan tới hiển thị hoặc bán hàng. Phần lớn các luồng storefront và admin đều có liên quan trực tiếp hoặc gián tiếp đến bảng này.

### 2.10.5. Bảng `product_variants`: Biến thể sản phẩm

Đối với sản phẩm công nghệ có nhiều phiên bản theo màu sắc, bộ nhớ hoặc cấu hình, bảng `product_variants` giữ vai trò mô hình hóa các biến thể đó. Bảng này cho phép tách dữ liệu biến thể ra khỏi bảng `products`, từ đó làm cho cấu trúc dữ liệu linh hoạt và rõ hơn.

### 2.10.6. Bảng `product_media`: Hình ảnh và media sản phẩm

Bảng `product_media` dùng để quản lý hình ảnh, thumbnail, gallery hoặc các tệp media liên quan tới sản phẩm. Việc tách media sang một bảng riêng giúp sản phẩm có thể gắn với nhiều tài nguyên trực quan khác nhau mà không làm cấu trúc `products` trở nên quá nặng.

### 2.10.7. Bảng `customers`: Khách hàng

Bảng `customers` lưu dữ liệu tài khoản khách hàng như tên, email, số điện thoại, mật khẩu, trạng thái xác thực và các thông tin hỗ trợ khác. Bảng này là nền cho các luồng đăng nhập khách hàng, checkout, lịch sử đơn hàng, đánh giá và chat hỗ trợ.

### 2.10.8. Bảng `carts` và `cart_items`: Giỏ hàng

Hai bảng `carts` và `cart_items` mô hình hóa giỏ hàng của khách hàng. `carts` đại diện cho thực thể giỏ hàng, còn `cart_items` đại diện cho từng dòng sản phẩm trong giỏ. Cấu trúc này giúp quản lý linh hoạt số lượng, biến thể, giá trị tạm tính và các trạng thái liên quan tới quá trình mua hàng trước khi tạo đơn.

### 2.10.9. Bảng `orders`: Đơn hàng

Bảng `orders` lưu thực thể đơn hàng ở mức tổng quát, bao gồm mã đơn, khách hàng, trạng thái, tổng tiền, coupon, ghi chú và các thông tin nghiệp vụ liên quan. Đây là bảng trung tâm của luồng mua hàng sau checkout.

### 2.10.10. Bảng `order_items`: Chi tiết đơn hàng

Bảng `order_items` lưu từng sản phẩm nằm trong một đơn hàng cụ thể. Việc tách bảng này ra khỏi `orders` là cần thiết vì một đơn hàng có thể chứa nhiều sản phẩm, nhiều biến thể, số lượng và mức giá khác nhau.

### 2.10.11. Bảng `payments` và `payment_transactions`: Thanh toán

`payments` lưu thông tin thanh toán chính của đơn hàng, còn `payment_transactions` lưu lịch sử hoặc các giao dịch chi tiết liên quan đến quá trình thanh toán. Cách tách này giúp hệ thống mở rộng tốt hơn cho các phương thức thanh toán khác nhau và hỗ trợ theo dõi giao dịch rõ ràng hơn.

### 2.10.12. Bảng `coupons` và `coupon_redemptions`: Khuyến mãi

Bảng `coupons` lưu thông tin mã giảm giá, điều kiện áp dụng, mức giảm và thời gian hiệu lực. Bảng `coupon_redemptions` lưu vết sử dụng coupon, giúp hệ thống theo dõi lịch sử áp dụng và hạn chế lạm dụng mã giảm giá.

### 2.10.13. Bảng `blog_posts`, `videos`, `banners`: Nội dung truyền thông

Nhóm bảng này phục vụ phần nội dung của hệ thống. `blog_posts` và `blog_categories` hỗ trợ bài viết, `videos` hỗ trợ nội dung video, còn `banners` hỗ trợ slider hoặc các khối truyền thông trên giao diện. Sự có mặt của nhóm bảng này chứng minh rằng website có cả vai trò CMS chứ không chỉ là hệ thống bán hàng thuần túy.

### 2.10.14. Bảng `product_reviews`: Bình luận và đánh giá sản phẩm

Bảng `product_reviews` lưu bình luận, nội dung đánh giá, điểm số và thông tin tác giả liên quan tới sản phẩm. Đây là bảng hỗ trợ một trong những yếu tố rất quan trọng của trải nghiệm mua hàng, đặc biệt với nhóm sản phẩm công nghệ cần tham khảo ý kiến người dùng trước đó.

### 2.10.15. Bảng `chat_conversations` và `chat_messages`: Chat hỗ trợ

`chat_conversations` đại diện cho một cuộc hội thoại hỗ trợ, còn `chat_messages` lưu các tin nhắn cụ thể trong cuộc hội thoại đó. Nhóm bảng này cho phép hệ thống hỗ trợ chatbot ở storefront và chat inbox ở phía quản trị, từ đó tạo ra một lớp hỗ trợ người dùng tương đối hoàn chỉnh.

### 2.10.16. Bảng `settings`, `analytics_events`, `page_views`, `user_sessions`: Cấu hình và phân tích hành vi

`settings` giữ vai trò lưu cấu hình hệ thống. `analytics_events`, `page_views` và `user_sessions` hỗ trợ theo dõi hành vi người dùng và trạng thái vận hành. Đây là nhóm bảng giúp hệ thống bước sang lớp phân tích thay vì chỉ lưu trữ dữ liệu giao dịch thuần túy.

### 2.10.17. Đặc tả chi tiết trường dữ liệu và ràng buộc toàn vẹn

Để phần thiết kế cơ sở dữ liệu đủ chiều sâu cho một báo cáo đồ án, ngoài việc mô tả vai trò từng bảng, cần đặc tả thêm các trường dữ liệu quan trọng, các khóa liên kết và các ràng buộc toàn vẹn chính. Phần này tập trung vào những bảng có ảnh hưởng lớn nhất đến kiến trúc nghiệp vụ của hệ thống.

#### a) Nhóm bảng catalog

Nhóm bảng catalog gồm `brands`, `product_categories`, `products`, `product_variants`, `product_media`, `product_category_product`, `attributes`, `attribute_values`. Đây là nhóm quyết định khả năng tổ chức và hiển thị dữ liệu hàng hóa.

**Bảng 2.14. Đặc tả tổng hợp nhóm bảng catalog**

| Bảng | Trường tiêu biểu | Vai trò và ràng buộc chính |
|---|---|---|
| `brands` | `name`, `slug`, `code`, `logo_path`, `is_active`, `display_order`, `meta` | `slug` là định danh thân thiện URL và cần duy nhất; `code` hỗ trợ chuẩn hóa tham chiếu; `is_active` điều khiển hiển thị trên storefront; `display_order` hỗ trợ sắp xếp ở admin và trang chủ |
| `product_categories` | `name`, `slug`, `code`, `parent_id`, `thumbnail_path`, `is_active`, `display_order`, `meta_title`, `meta_description` | `parent_id` tạo cấu trúc cây danh mục; `slug` duy nhất để định tuyến; `is_active` và `display_order` quyết định hiển thị; metadata hỗ trợ SEO |
| `products` | `brand_id`, `primary_category_id`, `name`, `slug`, `sku`, `barcode`, `price`, `sale_price`, `cost_price`, `stock_quantity`, `thumbnail_path`, `description`, `content`, `views_count`, `is_active`, `is_featured`, `meta` | `brand_id` và `primary_category_id` liên kết tới cấu trúc catalog; `slug` và `sku` cần duy nhất; `price`, `sale_price`, `cost_price` phục vụ cả storefront lẫn báo cáo; `stock_quantity` là nền cho kiểm soát bán hàng; `softDeletes` giữ khả năng phục hồi dữ liệu |
| `product_variants` | `product_id`, `name`, `sku`, `price`, `sale_price`, `stock_quantity`, `image_path`, `attributes`, `is_active` | Quan hệ một-nhiều từ `products`; `sku` biến thể cần duy nhất nếu dùng để định danh kho; `attributes` lưu snapshot thuộc tính của biến thể; `is_active` cho phép tắt riêng từng cấu hình |
| `product_media` | `product_id`, `file_path`, `disk`, `collection`, `display_order`, `meta` | Tách media khỏi sản phẩm chính; `collection` giúp phân nhóm như gallery, thumbnail, manual; `display_order` giúp xác định thứ tự hiển thị ảnh |

Từ bảng trên có thể thấy thiết kế catalog của hệ thống đi theo hướng chuẩn hóa khá rõ. Thay vì gộp toàn bộ dữ liệu sản phẩm vào một bảng duy nhất, hệ thống tách phần danh mục, thương hiệu, biến thể và media thành các thực thể độc lập nhưng vẫn liên hệ chặt với `products`. Điều này giúp mở rộng sản phẩm công nghệ theo nhiều cấu hình mà không làm bảng chính phình to và khó kiểm soát.

#### b) Nhóm bảng khách hàng, giỏ hàng và địa chỉ

Nhóm bảng này gồm `customers`, `customer_addresses`, `carts`, `cart_items`, `shipping_fees`. Đây là lớp dữ liệu kết nối giữa hành vi duyệt web và hành vi mua hàng.

**Bảng 2.15. Đặc tả tổng hợp nhóm bảng khách hàng, giỏ hàng và địa chỉ**

| Bảng | Trường tiêu biểu | Vai trò và ràng buộc chính |
|---|---|---|
| `customers` | `name`, `email`, `phone`, `password`, `avatar_path`, `is_active`, `email_verified_at`, `phone_verified_at`, `remember_token`, `meta` | `email` là khóa duy nhất bắt buộc; `phone` có thể duy nhất nếu dùng cho định danh; `password` phải lưu dưới dạng băm; các mốc xác thực giúp phục vụ bảo mật và chăm sóc khách hàng |
| `customer_addresses` | `customer_id`, `label`, `recipient_name`, `recipient_phone`, `email`, `country`, `location_city_id`, `location_province_id`, `location_ward_id`, `address_line1`, `address_line2`, `is_default` | Một khách có thể có nhiều địa chỉ; `is_default` hỗ trợ UX ở checkout; các khóa địa bàn liên kết với `locations`; `softDeletes` giúp giữ lịch sử mà không mất dữ liệu ngay |
| `carts` | `customer_id`, `subtotal`, `discount_amount`, `shipping_fee`, `total`, `items_count`, `coupon_code`, `meta` | Mỗi khách có tối đa một giỏ hàng hoạt động trong schema mới; bảng lưu snapshot tổng hợp để giảm chi phí tính toán lại liên tục; `coupon_code` hỗ trợ truy xuất trạng thái khuyến mãi hiện tại |
| `cart_items` | `cart_id`, `product_id`, `product_variant_id`, `price`, `quantity`, `subtotal`, `meta` | Đây là bảng chi tiết của giỏ hàng; `subtotal` lưu giá trị dòng để tiện render và tính tổng; `product_variant_id` cho phép giỏ hàng phân biệt cấu hình sản phẩm |
| `shipping_fees` | `city_id`, `province_id`, `ward_id`, `amount`, `currency` | Bảng này cho phép thiết kế phí giao hàng theo địa bàn; các chỉ mục trên tổ hợp địa bàn giúp tra cứu nhanh khi tính phí ở checkout |

Điểm quan trọng của nhóm bảng này là dữ liệu trạng thái được giữ ở cả mức tổng và mức chi tiết. `carts` lưu số liệu tổng hợp, còn `cart_items` lưu từng dòng hàng. Cách thiết kế này rất phù hợp cho website thương mại điện tử vì giỏ hàng thường được truy xuất nhiều, hiển thị ở nhiều vùng và cần phản hồi nhanh.

#### c) Nhóm bảng đơn hàng, giao hàng và thanh toán

Đây là nhóm dữ liệu có ảnh hưởng trực tiếp tới doanh thu và độ tin cậy của hệ thống. Thiết kế của các bảng ở nhóm này phải vừa đủ linh hoạt cho nhiều phương thức thanh toán, vừa đủ chặt để lưu lịch sử giao dịch một cách nhất quán.

**Bảng 2.16. Đặc tả tổng hợp nhóm bảng đơn hàng, giao hàng và thanh toán**

| Bảng | Trường tiêu biểu | Vai trò và ràng buộc chính |
|---|---|---|
| `orders` | `code`, `customer_id`, `customer_address_id`, `status`, `payment_status`, `fulfillment_status`, `subtotal`, `discount_total`, `shipping_total`, `tax_total`, `total`, `coupon_code`, `source`, `placed_at`, `confirmed_at`, `shipped_at`, `completed_at`, `cancelled_at`, `meta` | `code` là mã định danh nghiệp vụ duy nhất; ba trường trạng thái tách biệt giúp mô hình hóa vòng đời đơn rõ hơn; các trường tổng tiền là snapshot tài chính ở thời điểm mua; các timestamp hỗ trợ theo dõi tiến trình xử lý |
| `order_items` | `order_id`, `product_id`, `product_variant_id`, `product_name`, `sku`, `attributes`, `price`, `sale_price`, `discount_amount`, `quantity`, `subtotal`, `tax_amount`, `meta` | Dữ liệu dòng đơn phải giữ được snapshot tên sản phẩm và giá tại thời điểm đặt hàng, tránh phụ thuộc hoàn toàn vào catalog hiện tại; đây là lý do `product_name`, `sku`, `attributes` được lưu cùng `product_id` |
| `order_shipping` | `order_id`, `recipient_name`, `recipient_phone`, `email`, `city_id`, `province_id`, `ward_id`, `address_line1`, `shipping_method`, `tracking_number`, `shipping_fee`, `insurance_fee`, `meta` | Đây là snapshot giao hàng độc lập với địa chỉ hồ sơ của khách; nếu khách đổi địa chỉ sau này, đơn hàng vẫn giữ nguyên thông tin giao hàng lịch sử |
| `payments` | `order_id`, `provider`, `reference`, `status`, `currency`, `amount`, `fee`, `paid_at`, `meta` | Một đơn có thể gắn với một hoặc nhiều sự kiện thanh toán tùy chiến lược hệ thống; `provider` cho phép mở rộng COD, VNPay, MoMo, PayPal; `status` mô hình hóa quá trình thanh toán độc lập với trạng thái đơn |
| `payment_transactions` | `payment_id`, `event`, `status`, `reference`, `payload`, `ip_address` | Bảng này phục vụ audit và debug, ghi lại request, response, webhook hoặc refund event; `payload` đặc biệt hữu ích khi cần truy vết sự cố với nhà cung cấp thanh toán |

Về ràng buộc toàn vẹn, `orders` là bảng cha của `order_items` và `order_shipping`, còn `payments` là cha của `payment_transactions`. Những quan hệ này phải được duy trì chặt để tránh xuất hiện dữ liệu mồ côi. Trong bối cảnh hệ thống có nhiều tích hợp thanh toán và xử lý đơn theo giai đoạn, việc tách bảng như trên là quyết định thiết kế rất đúng.

#### d) Nhóm bảng khuyến mãi, nội dung và tương tác người dùng

Ngoài dữ liệu giao dịch, hệ thống còn cần các bảng phục vụ marketing và tương tác.

**Bảng 2.17. Đặc tả tổng hợp nhóm bảng khuyến mãi, nội dung và tương tác người dùng**

| Bảng | Trường tiêu biểu | Vai trò và ràng buộc chính |
|---|---|---|
| `coupons` | `name`, `code`, `type`, `value`, `max_discount`, `min_order_total`, `usage_limit`, `usage_per_customer`, `used_count`, `starts_at`, `expires_at`, `is_stackable`, `is_active`, `applicable_segments`, `meta` | `code` phải duy nhất; `type` quyết định thuật toán tính giảm giá; `usage_limit` và `usage_per_customer` bảo vệ khỏi lạm dụng; `starts_at` và `expires_at` điều khiển hiệu lực thời gian |
| `coupon_redemptions` | `coupon_id`, `customer_id`, `order_id`, `discount_amount` | Bảng này dùng để truy vết coupon đã được dùng bởi ai, cho đơn nào và giảm bao nhiêu tiền; rất quan trọng cho cả báo cáo lẫn chống gian lận |
| `blog_posts` | `blog_category_id`, `title`, `slug`, `excerpt`, `content`, `featured_image_path`, `is_featured`, `status`, `published_at`, `views_count`, `meta_title`, `meta_description`, `meta` | Hỗ trợ chức năng CMS; `status` và `published_at` cho phép lên lịch hoặc lưu nháp; metadata phục vụ SEO |
| `banners` | `title`, `slug`, `image_path`, `link_url`, `placement`, `display_order`, `is_active`, `starts_at`, `ends_at`, `meta` | Điều khiển các khối truyền thông trên giao diện; `placement` cho phép tái sử dụng banner ở nhiều vị trí; thời gian hiển thị giúp chạy chiến dịch ngắn hạn |
| `videos` | `title`, `slug`, `description`, `video_url`, `thumbnail_path`, `is_active`, `display_order`, `views_count`, `meta` | Hỗ trợ nội dung media động; `views_count` giúp theo dõi mức độ quan tâm của người dùng tới video |
| `product_reviews` | `product_id`, `customer_id`, `author_name`, `author_email`, `rating`, `title`, `content`, `parent_id`, `status`, `is_public`, `approved_at`, `meta` | `status` phản ánh quy trình moderation; `parent_id` cho phép reply thread; `rating` hỗ trợ đánh giá số sao; review được tách riêng để không làm nặng bảng sản phẩm |
| `chat_conversations` | `customer_id`, `user_id`, `session_token`, `channel`, `last_message_at`, `meta` | Theo dõi một hội thoại tổng quát; có thể gắn với khách cụ thể hoặc phiên vô danh qua `session_token`; `channel` phân biệt nguồn tương tác |
| `chat_messages` | `conversation_id`, `customer_id`, `user_id`, `sender`, `message`, `intent`, `response_type`, `meta` | Lưu từng tin nhắn trong hội thoại; `sender` phân biệt khách, bot hay hệ thống; `intent` và `response_type` giúp phân tích chất lượng chatbot hoặc luồng hỗ trợ |

Nhóm bảng này cho thấy hệ thống được thiết kế như một nền tảng có yếu tố marketing và chăm sóc khách hàng, chứ không thuần bán hàng. Đây là điểm khiến Chương 2 cần được viết chi tiết hơn mức thường thấy ở các báo cáo đơn giản.

#### e) Nhóm bảng cấu hình, phân tích và giám sát hệ thống

Các bảng `settings`, `analytics_events`, `product_views`, `page_views`, `user_sessions`, `product_search_queries`, `product_recommendation_impressions`, `metrics`, `alerts` tạo nên lớp dữ liệu phục vụ cấu hình và quan sát hệ thống.

**Bảng 2.18. Đặc tả tổng hợp nhóm bảng cấu hình, phân tích và giám sát hệ thống**

| Bảng | Trường tiêu biểu | Vai trò và ràng buộc chính |
|---|---|---|
| `settings` | `key`, `value`, `type`, `group`, `meta` | Dùng để lưu cấu hình có thể thay đổi mà không phải hardcode; `key` duy nhất bảo đảm một cấu hình chỉ có một bản ghi hiện hành |
| `analytics_events` | `event_type`, `customer_id`, `order_id`, `product_id`, `campaign_id`, `payload`, `source`, `ip_address`, `user_agent` | Là bảng sự kiện tổng quát cho hành vi người dùng hoặc chiến dịch; chỉ mục theo `event_type` và thời gian phục vụ truy vấn báo cáo |
| `product_views` | `product_id`, `customer_id`, `ip_address`, `user_agent`, `viewed_at` | Theo dõi lượt xem chi tiết sản phẩm; hỗ trợ recommendation, discovery report và phân tích quan tâm tới sản phẩm |
| `product_search_queries` | `customer_id`, `category_id`, `keyword`, `product_code`, `price_min`, `price_max`, `sort`, `results_count`, `source`, `ip_address`, `searched_at` | Giúp phân tích hành vi tìm kiếm, mức độ hiệu quả của catalog và cơ hội tối ưu search |
| `product_recommendation_impressions` | `product_id`, `customer_id`, `context`, `source_label`, `ip_address`, `impressed_at` | Ghi nhận hệ thống đã hiển thị recommendation nào, ở ngữ cảnh nào và lúc nào |
| `metrics` | `timestamp`, `metrics` | Lưu snapshot chỉ số vận hành tổng hợp theo thời điểm |
| `alerts` | `type`, `category`, `metric`, `value`, `threshold`, `severity`, `resolved`, `resolved_at` | Hỗ trợ cảnh báo vận hành và theo dõi việc đã xử lý sự cố hay chưa |

Nhóm bảng này đóng vai trò đặc biệt trong việc biến website từ một hệ thống phản ứng thụ động thành một hệ thống có khả năng quan sát và cải tiến. Đối với đồ án tốt nghiệp, sự hiện diện của lớp dữ liệu này giúp phần phân tích thiết kế có chiều sâu kỹ thuật rõ rệt.

#### f) Nhận xét về tính toàn vẹn và khả năng mở rộng của thiết kế dữ liệu

Nhìn tổng thể, thiết kế cơ sở dữ liệu của dự án thể hiện ba phẩm chất quan trọng. Thứ nhất là tính phân tách trách nhiệm: mỗi nhóm bảng tập trung vào một loại nhiệm vụ riêng như catalog, giao dịch, nội dung, hỗ trợ hoặc analytics. Thứ hai là tính truy vết: nhiều bảng đã được thiết kế để lưu snapshot hoặc lịch sử thay vì chỉ giữ trạng thái hiện tại. Thứ ba là khả năng mở rộng: thông qua `meta`, `json payload`, bảng trung gian, soft delete và các trường trạng thái, hệ thống có thể bổ sung thêm use case mà không cần phá cấu trúc cũ quá mạnh.

Tất nhiên, do hệ thống đang ở trạng thái chuyển tiếp, vẫn còn tồn tại khoảng cách giữa lược đồ đích và dữ liệu cũ hiện hành. Tuy nhiên, xét trên góc nhìn thiết kế, lược đồ mới của dự án là một nền tảng đủ tốt để tiếp tục chuẩn hóa từng mô-đun trong tương lai.

### 2.10.18. Nhận xét về thiết kế cơ sở dữ liệu

Thiết kế cơ sở dữ liệu của hệ thống cho thấy dự án đã vượt ra ngoài phạm vi một website bán hàng tối giản. Hệ thống không chỉ có bảng sản phẩm và đơn hàng, mà còn có các bảng phục vụ media, nội dung, đánh giá, khuyến mãi, phân quyền, chat và phân tích dữ liệu. Đồng thời, sự tồn tại song song giữa bảng dữ liệu cũ và lược đồ mới phản ánh đúng chiến lược hiện đại hóa dần của hệ thống.

### 2.10.19. Ghi chú thiết kế chỉ mục, `soft delete`, `timestamp` và dữ liệu snapshot

Ngoài việc xác định bảng và trường, một thiết kế cơ sở dữ liệu tốt còn phải xem xét các chi tiết kỹ thuật ảnh hưởng trực tiếp đến hiệu năng, khả năng khôi phục và tính đúng đắn của dữ liệu lịch sử. Với hệ thống trong đề tài, bốn nhóm quyết định kỹ thuật đặc biệt quan trọng là chỉ mục, `soft delete`, `timestamp` và dữ liệu snapshot.

#### a) Vai trò của chỉ mục trong website thương mại điện tử

Website bán thiết bị công nghệ có nhiều truy vấn đọc lặp lại với tần suất cao như tìm sản phẩm theo slug, lọc theo thương hiệu, lọc theo danh mục, nạp danh sách đơn theo trạng thái, tìm coupon theo mã, mở chi tiết bài viết theo slug hoặc lấy danh sách conversation chat theo thời gian cập nhật gần nhất. Nếu các truy vấn này không được hỗ trợ bởi chỉ mục phù hợp, hiệu năng hệ thống sẽ suy giảm rất nhanh khi dữ liệu tăng lên.

Trong lớp migration mới của hệ thống, có thể thấy chỉ mục đã được đặt ở nhiều vị trí có giá trị như `products(is_active, display_order)`, `products(brand_id, primary_category_id)`, `product_categories(parent_id, display_order)`, `orders(customer_id, status)`, `orders(placed_at, created_at)`, `payments(order_id, provider, status)`, `analytics_events(event_type, created_at)`, `chat_conversations(channel, last_message_at)`, `product_search_queries(keyword, searched_at)`, `product_recommendation_impressions(context, impressed_at)` và nhiều chỉ mục khác.

Điều này cho thấy thiết kế dữ liệu của dự án không chỉ quan tâm tới việc “có lưu được dữ liệu”, mà còn quan tâm tới việc “dữ liệu đó sẽ được truy vấn như thế nào”. Đây là điểm rất nên được làm rõ trong báo cáo vì nó phản ánh tư duy thiết kế ở mức triển khai thật, đặc biệt với hệ thống có yếu tố analytics và báo cáo.

#### b) Vai trò của `soft delete` trong quản trị dữ liệu

Nhiều bảng trong schema mới như `products`, `product_categories`, `brands`, `customers`, `orders`, `blog_posts`, `videos`, `banners`, `product_reviews` có sử dụng `softDeletes`. Quyết định này có ý nghĩa rất lớn trong môi trường thương mại điện tử vì dữ liệu không phải lúc nào cũng nên bị xóa vật lý ngay khỏi hệ thống.

Ví dụ, một sản phẩm có thể ngừng bán nhưng vẫn cần giữ lại để đối chiếu với lịch sử đơn hàng, báo cáo lợi nhuận hoặc thống kê hành vi xem sản phẩm. Một khách hàng có thể ngừng hoạt động nhưng dữ liệu đơn hàng và tương tác trước đó vẫn cần tồn tại. Một bài viết hoặc banner có thể được ẩn khỏi storefront nhưng vẫn cần phục hồi lại cho chiến dịch khác trong tương lai.

`Soft delete` giúp cân bằng giữa hai yêu cầu: làm sạch bề mặt hiển thị và bảo toàn lịch sử dữ liệu. Từ góc độ báo cáo, việc ghi nhận rõ điểm này là cần thiết vì nó cho thấy hệ thống đã tính tới vòng đời dữ liệu thực thay vì chỉ thiết kế theo kiểu CRUD đơn giản.

#### c) Vai trò của `timestamp` trong truy vết nghiệp vụ

Hầu như mọi bảng trong lớp migration mới đều có `created_at` và `updated_at`; nhiều bảng còn có thêm các mốc thời gian nghiệp vụ chuyên biệt như `placed_at`, `confirmed_at`, `shipped_at`, `completed_at`, `cancelled_at`, `paid_at`, `approved_at`, `last_message_at`, `searched_at`, `impressed_at`, `viewed_at`. Đây không phải là chi tiết nhỏ, mà là một quyết định thiết kế rất quan trọng.

Trong thương mại điện tử, việc biết “điều gì đã xảy ra” thôi chưa đủ; cần biết “điều đó xảy ra khi nào” để phục vụ vận hành, chăm sóc khách hàng, báo cáo, kiểm toán và gỡ lỗi. Chẳng hạn, chênh lệch giữa `placed_at` và `confirmed_at` phản ánh tốc độ xử lý đơn; chênh lệch giữa `confirmed_at` và `shipped_at` phản ánh tốc độ xuất hàng; `paid_at` dùng để đối chiếu với giao dịch thanh toán; `approved_at` giúp truy vết quy trình duyệt đánh giá sản phẩm.

Như vậy, timestamp trong hệ thống không chỉ là dữ liệu kỹ thuật do framework sinh ra, mà đã được mở rộng thành công cụ mô hình hóa tiến trình nghiệp vụ. Đây là điểm làm cho thiết kế dữ liệu của đề tài đủ sức hỗ trợ các phân tích hậu kỳ và báo cáo vận hành.

#### d) Vai trò của dữ liệu snapshot trong đơn hàng và giao hàng

Một trong những quyết định quan trọng nhất trong thiết kế dữ liệu thương mại điện tử là không phụ thuộc hoàn toàn vào dữ liệu hiện tại của catalog hay hồ sơ khách hàng khi lưu lịch sử giao dịch. Repo đã phản ánh nguyên tắc này qua `order_items` và `order_shipping`.

Ở `order_items`, ngoài khóa ngoại tới `product_id` hoặc `product_variant_id`, hệ thống còn lưu `product_name`, `sku`, `attributes`, `price`, `sale_price`, `discount_amount`, `quantity`, `subtotal`. Điều này bảo đảm rằng ngay cả khi sản phẩm bị đổi tên, thay giá, thay cấu trúc biến thể hoặc thậm chí bị xóa mềm, bản ghi đơn hàng vẫn giữ được thông tin lịch sử đúng tại thời điểm mua.

Tương tự, `order_shipping` giữ snapshot giao hàng độc lập với `customer_addresses`. Nếu khách cập nhật địa chỉ mặc định sau này, đơn hàng đã tạo trước đó vẫn không bị thay đổi thông tin giao nhận. Cách thiết kế snapshot này là một nguyên tắc rất quan trọng và cần được nêu rõ trong báo cáo vì nó thể hiện tư duy bảo toàn lịch sử dữ liệu nghiệp vụ.

#### e) Định hướng chuẩn hóa và mở rộng tiếp theo

Mặc dù schema mới đã khá đầy đủ, vẫn có thể nhận ra một số hướng chuẩn hóa tiếp theo nếu hệ thống tiếp tục phát triển. Thứ nhất là bổ sung nhiều hơn cho `page_views` và `user_sessions`, bởi ở hiện trạng migration hai bảng này mới là khung ban đầu. Thứ hai là chuẩn hóa sâu hơn các bảng liên quan tới loyalty, customer segments, automation hoặc notification nếu các module này được triển khai đầy đủ ở tầng ứng dụng.

Thứ ba là tiếp tục giảm phụ thuộc của các luồng legacy vào `tbl_*`, đặc biệt với các module đã có schema mới tương ứng như catalog, order, coupon, blog và analytics. Khi từng module hoàn tất chuyển đổi, hệ thống sẽ hưởng lợi lớn về độ nhất quán của dữ liệu, độ rõ ràng của test và độ ổn định của API.

#### f) Giá trị của các quyết định kỹ thuật dữ liệu đối với đề tài

Những quyết định như thêm chỉ mục hợp lý, dùng `soft delete`, mở rộng timestamp nghiệp vụ và lưu snapshot dữ liệu có thể không phải là phần đầu tiên người dùng nhìn thấy ở giao diện. Tuy nhiên, chính các quyết định này lại tạo nên sự khác biệt giữa một hệ thống trình diễn ngắn hạn và một hệ thống có thể vận hành, bảo trì và phân tích về sau.

Đối với đồ án tốt nghiệp, việc trình bày rõ các quyết định này giúp chứng minh rằng đề tài không chỉ dừng ở mức “xây giao diện và lưu dữ liệu”, mà đã tiếp cận bài toán thương mại điện tử theo hướng kỹ thuật nghiêm túc hơn, quan tâm cả tới hiệu năng, lịch sử giao dịch, khả năng phục hồi dữ liệu và định hướng mở rộng lâu dài.

### 2.10.20. Bảng 2.19. Đặc tả chi tiết cho `products`

Phần dưới đây trình bày bảng đặc tả chi tiết cột dữ liệu cho các bảng trọng tâm nhất của hệ thống. Nội dung được xây dựng trực tiếp từ các migration và cấu trúc dữ liệu đang được sử dụng trong mã nguồn.

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh duy nhất của sản phẩm |
| `brand_id` | Khóa ngoại | Nullable, `nullOnDelete` | Liên kết tới thương hiệu; cho phép vẫn giữ sản phẩm khi thương hiệu bị gỡ mềm |
| `primary_category_id` | Khóa ngoại | Nullable, `nullOnDelete` | Danh mục chính của sản phẩm dùng cho điều hướng và lọc |
| `name` | Chuỗi | Bắt buộc | Tên sản phẩm hiển thị ở storefront và admin |
| `slug` | Chuỗi | Duy nhất | Định danh URL thân thiện của sản phẩm |
| `sku` | Chuỗi | Nullable, duy nhất | Mã quản lý nội bộ hoặc mã kho |
| `barcode` | Chuỗi | Nullable | Hỗ trợ tích hợp quản lý hàng hóa |
| `unit` | Chuỗi | Nullable | Đơn vị tính hoặc đơn vị đóng gói |
| `price` | Số thập phân | Bắt buộc | Giá niêm yết cơ bản của sản phẩm |
| `sale_price` | Số thập phân | Nullable | Giá khuyến mãi nếu đang giảm |
| `cost_price` | Số thập phân | Nullable | Giá vốn phục vụ báo cáo lợi nhuận |
| `stock_quantity` | Số nguyên không âm | Mặc định `0` | Tồn kho hiện thời của sản phẩm |
| `stock_alert_threshold` | Số nguyên không âm | Mặc định `0` | Ngưỡng cảnh báo sắp hết hàng |
| `is_active` | Boolean | Mặc định `true` | Điều khiển việc sản phẩm có hiển thị ra ngoài hay không |
| `is_featured` | Boolean | Mặc định `false` | Đánh dấu sản phẩm nổi bật |
| `display_order` | Số nguyên không âm | Mặc định `0` | Thứ tự hiển thị ở danh sách hoặc khối đặc biệt |
| `thumbnail_path` | Chuỗi | Nullable | Ảnh đại diện chính |
| `description` | Văn bản dài | Nullable | Mô tả ngắn hoặc mô tả tóm tắt |
| `content` | Văn bản dài | Nullable | Nội dung chi tiết của sản phẩm |
| `views_count` | Số nguyên không âm | Mặc định `0` | Số lượt xem tích lũy |
| `meta` | JSON | Nullable | Trường mở rộng cho các thuộc tính chưa cố định |
| `meta_title` | Chuỗi | Nullable | Tiêu đề SEO |
| `meta_description` | Văn bản | Nullable | Mô tả SEO |
| `created_at`, `updated_at`, `deleted_at` | Timestamp | Tự động, soft delete | Theo dõi vòng đời dữ liệu |

Nhìn từ góc độ thiết kế, `products` là bảng hạt nhân của toàn bộ nhóm catalog. Bảng này được thiết kế đủ giàu thông tin để hỗ trợ cả storefront, admin, SEO, báo cáo và mở rộng về sau mà vẫn giữ vai trò trung tâm rõ ràng.

### 2.10.21. Bảng 2.20. Đặc tả chi tiết cho `product_variants` và `product_media`

#### a) Bảng `product_variants`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh biến thể |
| `product_id` | Khóa ngoại | `cascadeOnDelete` | Gắn biến thể với sản phẩm cha |
| `name` | Chuỗi | Bắt buộc | Tên cấu hình hoặc tên biến thể |
| `sku` | Chuỗi | Nullable, duy nhất | Mã hàng riêng cho từng biến thể |
| `barcode` | Chuỗi | Nullable | Mã quét nếu quản lý kho chi tiết |
| `price` | Số thập phân | Bắt buộc | Giá bán của biến thể |
| `sale_price` | Số thập phân | Nullable | Giá khuyến mãi của biến thể |
| `cost_price` | Số thập phân | Nullable | Giá vốn của biến thể |
| `stock_quantity` | Số nguyên không âm | Mặc định `0` | Tồn kho từng cấu hình |
| `stock_alert_threshold` | Số nguyên không âm | Mặc định `0` | Ngưỡng cảnh báo tồn kho |
| `image_path` | Chuỗi | Nullable | Ảnh đại diện riêng của biến thể |
| `attributes` | JSON | Nullable | Snapshot thuộc tính như màu, dung lượng, kích thước |
| `is_active` | Boolean | Mặc định `true` | Cho phép ẩn hoặc bật riêng từng cấu hình |
| `created_at`, `updated_at`, `deleted_at` | Timestamp | Tự động | Theo dõi vòng đời biến thể |

#### b) Bảng `product_media`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh tài nguyên media |
| `product_id` | Khóa ngoại | `cascadeOnDelete` | Gắn media với sản phẩm |
| `file_path` | Chuỗi | Bắt buộc | Đường dẫn file ảnh, manual hoặc tệp khác |
| `disk` | Chuỗi | Mặc định `public` | Vị trí lưu trữ của file |
| `collection` | Chuỗi | Mặc định `default` | Nhóm media như gallery, thumbnail, manual |
| `display_order` | Số nguyên | Mặc định `0` | Thứ tự hiển thị trong gallery |
| `meta` | JSON | Nullable | Thông tin phụ như kích thước, alt text, loại file |
| `created_at`, `updated_at` | Timestamp | Tự động | Theo dõi cập nhật media |

Hai bảng này giúp `products` không phải gánh quá nhiều dữ liệu đa trị. Đây là minh chứng rõ cho hướng thiết kế chuẩn hóa của dự án.

### 2.10.22. Bảng 2.21. Đặc tả chi tiết cho `customers` và `customer_addresses`

#### a) Bảng `customers`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh khách hàng |
| `name` | Chuỗi | Bắt buộc | Họ tên khách hàng |
| `email` | Chuỗi | Duy nhất | Kênh định danh và xác thực chính |
| `phone` | Chuỗi | Nullable, duy nhất | Số điện thoại hỗ trợ liên hệ và xác minh |
| `password` | Chuỗi | Bắt buộc | Mật khẩu đã băm |
| `avatar_path` | Chuỗi | Nullable | Ảnh đại diện tài khoản |
| `is_active` | Boolean | Mặc định `true` | Trạng thái hoạt động của tài khoản |
| `email_verified_at` | Timestamp | Nullable | Mốc xác thực email |
| `phone_verified_at` | Timestamp | Nullable | Mốc xác thực số điện thoại |
| `remember_token` | Chuỗi | Nullable | Phục vụ ghi nhớ đăng nhập |
| `meta` | JSON | Nullable | Dữ liệu mở rộng như giới tính, ngày sinh, sở thích |
| `created_at`, `updated_at`, `deleted_at` | Timestamp | Tự động | Theo dõi vòng đời tài khoản |

#### b) Bảng `customer_addresses`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh địa chỉ |
| `customer_id` | Khóa ngoại | `cascadeOnDelete` | Gắn địa chỉ với khách hàng |
| `label` | Chuỗi | Nullable | Nhãn gợi nhớ như nhà riêng, văn phòng |
| `recipient_name` | Chuỗi | Bắt buộc | Người nhận hàng |
| `recipient_phone` | Chuỗi | Nullable | Số điện thoại liên hệ khi giao |
| `email` | Chuỗi | Nullable | Email phụ cho liên hệ giao nhận |
| `country` | Chuỗi | Mặc định `Vietnam` | Quốc gia giao hàng |
| `location_city_id`, `location_province_id`, `location_ward_id` | Khóa ngoại | Nullable | Liên kết vùng địa lý để tính phí giao hàng |
| `postal_code` | Chuỗi | Nullable | Mã bưu chính nếu cần |
| `address_line1` | Chuỗi | Bắt buộc | Địa chỉ chính |
| `address_line2` | Chuỗi | Nullable | Địa chỉ bổ sung |
| `is_default` | Boolean | Mặc định `false` | Đánh dấu địa chỉ mặc định |
| `meta` | JSON | Nullable | Ghi chú thêm cho giao nhận |
| `created_at`, `updated_at`, `deleted_at` | Timestamp | Tự động | Theo dõi vòng đời địa chỉ |

### 2.10.23. Bảng 2.22. Đặc tả chi tiết cho `carts` và `cart_items`

#### a) Bảng `carts`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh giỏ hàng |
| `customer_id` | Khóa ngoại | `unique`, `cascadeOnDelete` | Mỗi khách có một giỏ hàng hoạt động trong schema mới |
| `subtotal` | Số thập phân | Mặc định `0` | Tổng tiền hàng trước giảm giá và phí |
| `discount_amount` | Số thập phân | Mặc định `0` | Tổng số tiền giảm bởi coupon hoặc khuyến mãi |
| `shipping_fee` | Số thập phân | Mặc định `0` | Phí giao hàng tạm tính |
| `total` | Số thập phân | Mặc định `0` | Tổng thanh toán hiện thời của giỏ |
| `items_count` | Số nguyên | Mặc định `0` | Tổng số dòng hoặc tổng số lượng tùy logic triển khai |
| `coupon_code` | Chuỗi | Nullable | Mã giảm giá đang được áp dụng |
| `meta` | JSON | Nullable | Dữ liệu hỗ trợ như ghi chú, nguồn tạo giỏ |
| `created_at`, `updated_at` | Timestamp | Tự động | Theo dõi lần cập nhật giỏ gần nhất |

#### b) Bảng `cart_items`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh dòng giỏ hàng |
| `cart_id` | Khóa ngoại | `cascadeOnDelete` | Gắn item với giỏ hàng cha |
| `product_id` | Khóa ngoại | Nullable, `nullOnDelete` | Tham chiếu sản phẩm gốc |
| `product_variant_id` | Khóa ngoại | Nullable, `nullOnDelete` | Tham chiếu cấu hình đã chọn |
| `price` | Số thập phân | Bắt buộc | Giá áp dụng tại thời điểm thêm vào giỏ |
| `quantity` | Số nguyên | Mặc định `1` | Số lượng mua |
| `subtotal` | Số thập phân | Mặc định `0` | Thành tiền dòng hàng |
| `meta` | JSON | Nullable | Dữ liệu bổ sung của item |
| `created_at`, `updated_at` | Timestamp | Tự động | Hỗ trợ theo dõi biến động của dòng giỏ |

### 2.10.24. Bảng 2.23. Đặc tả chi tiết cho `orders`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh đơn hàng |
| `code` | Chuỗi | Duy nhất | Mã đơn nghiệp vụ dùng cho tra cứu |
| `customer_id` | Khóa ngoại | Nullable, `nullOnDelete` | Liên kết tới khách hàng đặt đơn |
| `customer_address_id` | Khóa ngoại | Nullable, `nullOnDelete` | Địa chỉ nguồn nếu đơn lấy từ hồ sơ khách |
| `status` | Enum | Mặc định `pending` | Trạng thái xử lý đơn hàng |
| `payment_status` | Enum | Mặc định `unpaid` | Trạng thái thanh toán |
| `fulfillment_status` | Enum | Mặc định `unfulfilled` | Trạng thái giao vận hoặc xuất hàng |
| `currency` | Chuỗi 3 ký tự | Mặc định `VND` | Đồng tiền giao dịch |
| `subtotal` | Số thập phân | Mặc định `0` | Tổng tiền hàng trước chiết khấu, phí, thuế |
| `discount_total` | Số thập phân | Mặc định `0` | Tổng tiền giảm của đơn |
| `shipping_total` | Số thập phân | Mặc định `0` | Phí giao hàng của đơn |
| `tax_total` | Số thập phân | Mặc định `0` | Thuế nếu có |
| `total` | Số thập phân | Mặc định `0` | Tổng tiền cuối cùng cần thanh toán |
| `coupon_code` | Chuỗi | Nullable | Mã giảm giá áp dụng tại thời điểm đặt |
| `coupon_discount` | Số thập phân | Mặc định `0` | Giá trị giảm do coupon |
| `source` | Chuỗi | Nullable | Nguồn tạo đơn như web, mobile, marketplace |
| `note` | Chuỗi | Nullable | Ghi chú của khách hoặc vận hành |
| `placed_at`, `confirmed_at`, `shipped_at`, `completed_at`, `cancelled_at` | Timestamp | Nullable | Các mốc tiến trình nghiệp vụ |
| `meta` | JSON | Nullable | Thông tin mở rộng của đơn |
| `created_at`, `updated_at`, `deleted_at` | Timestamp | Tự động | Theo dõi vòng đời bản ghi đơn hàng |

Thiết kế bảng `orders` đáng chú ý ở chỗ tách ba lớp trạng thái: xử lý đơn, thanh toán và fulfillment. Đây là cách mô hình hóa rất phù hợp cho các hệ thống thương mại điện tử có vận hành thực tế.

### 2.10.25. Bảng 2.24. Đặc tả chi tiết cho `order_items`, `order_shipping`, `payments`, `payment_transactions`

#### a) Bảng `order_items`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh dòng đơn hàng |
| `order_id` | Khóa ngoại | `cascadeOnDelete` | Gắn dòng hàng với đơn cha |
| `product_id` | Khóa ngoại | Nullable, `nullOnDelete` | Tham chiếu về sản phẩm gốc nếu còn tồn tại |
| `product_variant_id` | Khóa ngoại | Nullable, `nullOnDelete` | Tham chiếu biến thể nếu có |
| `product_name` | Chuỗi | Bắt buộc | Snapshot tên sản phẩm lúc mua |
| `sku` | Chuỗi | Nullable | Snapshot mã sản phẩm hoặc biến thể |
| `attributes` | JSON | Nullable | Snapshot thuộc tính cấu hình |
| `price` | Số thập phân | Bắt buộc | Giá bán cơ bản |
| `sale_price` | Số thập phân | Nullable | Giá bán sau khuyến mãi nếu có |
| `discount_amount` | Số thập phân | Mặc định `0` | Mức giảm riêng của dòng hàng |
| `quantity` | Số nguyên | Bắt buộc | Số lượng đặt mua |
| `subtotal` | Số thập phân | Bắt buộc | Thành tiền của dòng |
| `tax_amount` | Số thập phân | Mặc định `0` | Thuế dòng hàng nếu có |
| `meta` | JSON | Nullable | Thông tin mở rộng |

#### b) Bảng `order_shipping`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh bản ghi giao hàng |
| `order_id` | Khóa ngoại | `cascadeOnDelete` | Gắn giao hàng với đơn |
| `recipient_name` | Chuỗi | Bắt buộc | Người nhận thực tế của đơn |
| `recipient_phone` | Chuỗi | Nullable | Số điện thoại giao nhận |
| `email` | Chuỗi | Nullable | Email liên hệ nếu cần |
| `country` | Chuỗi | Mặc định `Vietnam` | Quốc gia giao hàng |
| `city_id`, `province_id`, `ward_id` | Khóa ngoại | Nullable | Địa bàn giao hàng |
| `postal_code` | Chuỗi | Nullable | Mã bưu chính |
| `address_line1`, `address_line2` | Chuỗi | `address_line1` bắt buộc | Địa chỉ giao nhận chi tiết |
| `shipping_method` | Chuỗi | Nullable | Phương thức giao hàng |
| `tracking_number` | Chuỗi | Nullable | Mã theo dõi vận đơn |
| `shipping_fee` | Số thập phân | Mặc định `0` | Phí giao hàng thực tế |
| `insurance_fee` | Số thập phân | Mặc định `0` | Phí bảo hiểm nếu có |
| `meta` | JSON | Nullable | Thông tin phụ phục vụ giao nhận |

#### c) Bảng `payments`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh giao dịch thanh toán tổng quát |
| `order_id` | Khóa ngoại | `cascadeOnDelete` | Gắn thanh toán với đơn |
| `provider` | Chuỗi | Bắt buộc | Nhà cung cấp thanh toán như COD, VNPay, MoMo, PayPal |
| `reference` | Chuỗi | Nullable | Mã giao dịch từ nhà cung cấp |
| `status` | Enum | Mặc định `pending` | Trạng thái thanh toán |
| `currency` | Chuỗi 3 ký tự | Mặc định `VND` | Đồng tiền |
| `amount` | Số thập phân | Bắt buộc | Số tiền cần thanh toán |
| `fee` | Số thập phân | Mặc định `0` | Phí phát sinh của kênh thanh toán |
| `paid_at` | Timestamp | Nullable | Mốc hoàn tất thanh toán |
| `meta` | JSON | Nullable | Payload phụ của giao dịch |

#### d) Bảng `payment_transactions`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh sự kiện thanh toán |
| `payment_id` | Khóa ngoại | `cascadeOnDelete` | Gắn sự kiện với payment cha |
| `event` | Chuỗi | Bắt buộc | Loại sự kiện như request, response, webhook, refund |
| `status` | Chuỗi | Nullable | Trạng thái mà provider trả về ở sự kiện đó |
| `reference` | Chuỗi | Nullable | Mã đối chiếu của sự kiện |
| `payload` | JSON | Nullable | Nội dung kỹ thuật để debug hoặc audit |
| `ip_address` | Chuỗi | Nullable | Địa chỉ IP liên quan |
| `created_at`, `updated_at` | Timestamp | Tự động | Dùng để truy vết thời điểm phát sinh |

### 2.10.26. Bảng 2.25. Đặc tả chi tiết cho `coupons` và `coupon_redemptions`

#### a) Bảng `coupons`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh coupon |
| `name` | Chuỗi | Bắt buộc | Tên chiến dịch coupon |
| `code` | Chuỗi | Duy nhất | Mã giảm giá nhập bởi người dùng |
| `type` | Enum | Bắt buộc | Loại coupon: `fixed`, `percent`, `shipping` |
| `value` | Số thập phân | Bắt buộc | Giá trị giảm tương ứng với loại coupon |
| `max_discount` | Số thập phân | Nullable | Trần giảm giá khi dùng coupon phần trăm |
| `min_order_total` | Số thập phân | Nullable | Giá trị đơn tối thiểu để áp dụng |
| `usage_limit` | Số nguyên | Nullable | Giới hạn tổng lượt dùng |
| `usage_per_customer` | Số nguyên | Nullable | Giới hạn lượt dùng trên mỗi khách |
| `used_count` | Số nguyên | Mặc định `0` | Số lượt đã sử dụng |
| `starts_at`, `expires_at` | Timestamp | Nullable | Khoảng thời gian hiệu lực |
| `is_stackable` | Boolean | Mặc định `false` | Cho phép dùng chồng với ưu đãi khác hay không |
| `is_active` | Boolean | Mặc định `true` | Trạng thái hoạt động |
| `applicable_segments` | JSON | Nullable | Nhóm khách hàng được áp dụng |
| `meta` | JSON | Nullable | Thông tin mở rộng của chiến dịch |

#### b) Bảng `coupon_redemptions`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh lần sử dụng coupon |
| `coupon_id` | Khóa ngoại | `cascadeOnDelete` | Coupon được dùng |
| `customer_id` | Khóa ngoại | Nullable, `nullOnDelete` | Khách hàng đã sử dụng |
| `order_id` | Khóa ngoại | Nullable, `nullOnDelete` | Đơn hàng áp dụng coupon |
| `discount_amount` | Số thập phân | Mặc định `0` | Số tiền thực tế đã giảm |
| `created_at`, `updated_at` | Timestamp | Tự động | Thời điểm sử dụng coupon |

### 2.10.27. Bảng 2.26. Đặc tả chi tiết cho `blog_posts`, `banners`, `videos`

#### a) Bảng `blog_posts`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh bài viết |
| `blog_category_id` | Khóa ngoại | Nullable, `nullOnDelete` | Danh mục bài viết |
| `title` | Chuỗi | Bắt buộc | Tiêu đề bài viết |
| `slug` | Chuỗi | Duy nhất | URL thân thiện |
| `excerpt` | Văn bản | Nullable | Tóm tắt bài viết |
| `content` | Văn bản dài | Nullable | Nội dung đầy đủ |
| `featured_image_path` | Chuỗi | Nullable | Ảnh đại diện |
| `is_featured` | Boolean | Mặc định `false` | Đánh dấu nổi bật |
| `status` | Enum | Mặc định `draft` | Trạng thái nháp, lên lịch, xuất bản, lưu trữ |
| `published_at` | Timestamp | Nullable | Thời điểm công khai |
| `views_count` | Số nguyên | Mặc định `0` | Lượt xem |
| `meta_title`, `meta_description`, `meta` | Chuỗi/Văn bản/JSON | Nullable | Hỗ trợ SEO và mở rộng |

#### b) Bảng `banners`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh banner |
| `title` | Chuỗi | Nullable | Tên banner hoặc chiến dịch |
| `slug` | Chuỗi | Nullable, duy nhất | Định danh thân thiện |
| `image_path` | Chuỗi | Bắt buộc | Ảnh banner |
| `link_url` | Chuỗi | Nullable | Liên kết khi người dùng bấm banner |
| `placement` | Enum | Mặc định `homepage` | Vị trí hiển thị |
| `display_order` | Số nguyên | Mặc định `0` | Thứ tự hiển thị |
| `is_active` | Boolean | Mặc định `true` | Bật hoặc ẩn banner |
| `starts_at`, `ends_at` | Timestamp | Nullable | Khoảng thời gian chạy banner |
| `meta` | JSON | Nullable | Thông tin mở rộng |

#### c) Bảng `videos`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh video |
| `title` | Chuỗi | Bắt buộc | Tên video |
| `slug` | Chuỗi | Duy nhất | URL thân thiện |
| `description` | Văn bản | Nullable | Mô tả video |
| `video_url` | Chuỗi | Bắt buộc | Đường dẫn nguồn video |
| `thumbnail_path` | Chuỗi | Nullable | Ảnh bìa |
| `is_active` | Boolean | Mặc định `true` | Bật hoặc ẩn video |
| `display_order` | Số nguyên | Mặc định `0` | Thứ tự hiển thị |
| `views_count` | Số nguyên | Mặc định `0` | Lượt xem |
| `meta` | JSON | Nullable | Thông tin phụ |

### 2.10.28. Bảng 2.27. Đặc tả chi tiết cho `product_reviews`, `chat_conversations`, `chat_messages`

#### a) Bảng `product_reviews`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh review |
| `product_id` | Khóa ngoại | `cascadeOnDelete` | Sản phẩm được đánh giá |
| `customer_id` | Khóa ngoại | Nullable, `nullOnDelete` | Khách hàng đánh giá nếu có tài khoản |
| `author_name`, `author_email` | Chuỗi | Nullable | Người đánh giá trong trường hợp không gắn tài khoản |
| `rating` | Số nguyên nhỏ | Mặc định `5` | Số sao đánh giá |
| `title` | Văn bản | Nullable | Tiêu đề review |
| `content` | Văn bản dài | Bắt buộc | Nội dung đánh giá |
| `parent_id` | Số nguyên lớn | Nullable | Hỗ trợ phản hồi theo chuỗi |
| `status` | Enum | Mặc định `pending` | Trạng thái duyệt |
| `is_public` | Boolean | Mặc định `true` | Có hiển thị công khai hay không |
| `approved_at` | Timestamp | Nullable | Mốc duyệt review |
| `meta` | JSON | Nullable | Dữ liệu mở rộng |

#### b) Bảng `chat_conversations`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh hội thoại |
| `customer_id` | Khóa ngoại | Nullable, `nullOnDelete` | Khách hàng của hội thoại |
| `user_id` | Khóa ngoại | Nullable, `nullOnDelete` | Admin phụ trách nếu có |
| `session_token` | Chuỗi | Nullable | Nhận diện hội thoại vô danh |
| `channel` | Chuỗi | Mặc định `storefront` | Kênh khởi tạo hội thoại |
| `last_message_at` | Timestamp | Nullable | Mốc tin nhắn gần nhất |
| `meta` | JSON | Nullable | Trạng thái hoặc dữ liệu hỗ trợ |
| `created_at`, `updated_at` | Timestamp | Tự động | Theo dõi vòng đời hội thoại |

#### c) Bảng `chat_messages`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh tin nhắn |
| `conversation_id` | Khóa ngoại | `cascadeOnDelete` | Thuộc hội thoại nào |
| `customer_id` | Khóa ngoại | Nullable, `nullOnDelete` | Người gửi là khách nếu có |
| `user_id` | Khóa ngoại | Nullable, `nullOnDelete` | Người gửi là admin nếu có |
| `sender` | Enum | Bắt buộc | Kiểu người gửi: `user`, `bot`, `system` |
| `message` | Văn bản | Bắt buộc | Nội dung tin nhắn |
| `intent` | Chuỗi | Nullable | Ý định được chatbot nhận diện |
| `response_type` | Chuỗi | Nullable | Loại phản hồi |
| `meta` | JSON | Nullable | Dữ liệu kỹ thuật bổ sung |
| `created_at`, `updated_at` | Timestamp | Tự động | Theo dõi thời điểm phản hồi |

### 2.10.29. Bảng 2.28. Đặc tả chi tiết cho `analytics_events`, `product_views`, `product_search_queries`, `metrics`, `alerts`

#### a) Bảng `analytics_events`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh sự kiện |
| `event_type` | Chuỗi | Bắt buộc | Loại sự kiện người dùng hoặc hệ thống |
| `customer_id` | Khóa ngoại | Nullable, `nullOnDelete` | Khách gây ra sự kiện nếu xác định được |
| `order_id` | Khóa ngoại | Nullable, `nullOnDelete` | Đơn hàng liên quan |
| `product_id` | Khóa ngoại | Nullable, `nullOnDelete` | Sản phẩm liên quan |
| `campaign_id` | Khóa ngoại | Nullable, `nullOnDelete` | Chiến dịch liên quan |
| `payload` | JSON | Nullable | Dữ liệu chi tiết của sự kiện |
| `source` | Chuỗi | Nullable | Nguồn sinh sự kiện |
| `ip_address` | Chuỗi | Nullable | IP người dùng hoặc nguồn gửi |
| `user_agent` | Chuỗi | Nullable | Dấu vết trình duyệt hoặc client |
| `created_at`, `updated_at` | Timestamp | Tự động | Thời điểm ghi nhận |

#### b) Bảng `product_views`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh lượt xem |
| `product_id` | Khóa ngoại | `cascadeOnDelete` | Sản phẩm được xem |
| `customer_id` | Khóa ngoại | Nullable, `set null` | Khách hàng xem nếu xác định được |
| `ip_address` | Chuỗi | Nullable | Dấu vết người xem |
| `user_agent` | Chuỗi | Nullable | Loại thiết bị hoặc trình duyệt |
| `viewed_at` | Timestamp | Bắt buộc | Thời điểm xem sản phẩm |
| `created_at`, `updated_at` | Timestamp | Tự động | Theo dõi bản ghi |

#### c) Bảng `product_search_queries`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh truy vấn tìm kiếm |
| `customer_id` | Khóa ngoại | Nullable | Người tìm kiếm |
| `category_id` | Khóa ngoại | Nullable | Danh mục đang lọc khi tìm kiếm |
| `keyword` | Chuỗi | Nullable | Từ khóa người dùng nhập |
| `product_code` | Chuỗi | Nullable | Mã sản phẩm được tìm theo code nếu có |
| `price_min`, `price_max` | Số thập phân | Nullable | Khoảng giá lọc |
| `sort` | Chuỗi | Nullable | Quy tắc sắp xếp |
| `results_count` | Số nguyên | Mặc định `0` | Số lượng kết quả trả về |
| `source` | Chuỗi | Mặc định `web` | Kênh phát sinh truy vấn |
| `ip_address` | Chuỗi | Nullable | Dấu vết người tìm kiếm |
| `searched_at` | Timestamp | Bắt buộc | Thời điểm thực hiện tìm kiếm |

#### d) Bảng `metrics`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh snapshot metric |
| `timestamp` | Chuỗi hoặc mốc thời gian logic | Có chỉ mục | Nhãn thời gian của snapshot |
| `metrics` | JSON | Bắt buộc | Tập chỉ số vận hành đã tổng hợp |
| `created_at`, `updated_at` | Timestamp | Tự động | Theo dõi thời điểm lưu |

#### e) Bảng `alerts`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh cảnh báo |
| `type` | Chuỗi | Bắt buộc | Loại cảnh báo |
| `category` | Chuỗi | Bắt buộc | Nhóm cảnh báo |
| `metric` | Chuỗi | Bắt buộc | Chỉ số gây cảnh báo |
| `value` | Số thập phân | Bắt buộc | Giá trị đo được |
| `threshold` | Số thập phân | Bắt buộc | Ngưỡng cho phép |
| `severity` | Chuỗi | Bắt buộc | Mức độ nghiêm trọng |
| `resolved` | Boolean | Mặc định `false` | Cảnh báo đã được xử lý hay chưa |
| `resolved_at` | Timestamp | Nullable | Thời điểm xử lý xong |
| `created_at`, `updated_at` | Timestamp | Tự động | Theo dõi vòng đời cảnh báo |

### 2.10.30. Nhận xét về bộ bảng đặc tả cơ sở dữ liệu

Việc mô tả cơ sở dữ liệu theo từng bảng và từng cột làm cho phần thiết kế CSDL có tính học thuật cao hơn rõ rệt. Thay vì chỉ nêu tên bảng và vai trò chung, phần đặc tả chi tiết giúp người đọc hiểu được mục đích của từng cột, mối liên hệ giữa khóa ngoại với nghiệp vụ và cách mỗi bảng hỗ trợ một phần trong vòng đời dữ liệu của website thương mại điện tử.

Đồng thời, bộ bảng đặc tả này cũng là cơ sở thuận lợi cho các bước tiếp theo như dựng ERD, viết migration, xây model, kiểm tra chất lượng dữ liệu, chuẩn hóa query và mở rộng hệ thống. Với đồ án tốt nghiệp, đây là một thành phần rất nên có nếu muốn chương phân tích thiết kế đạt độ dày và độ tin cậy cao.

### 2.10.31. Bảng 2.29. Đặc tả chi tiết cho `brands` và `product_categories`

#### a) Bảng `brands`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh thương hiệu |
| `name` | Chuỗi | Bắt buộc | Tên thương hiệu hiển thị ở storefront và admin |
| `slug` | Chuỗi | Duy nhất | URL thân thiện cho trang thương hiệu |
| `code` | Chuỗi | Nullable, duy nhất | Mã thương hiệu phục vụ chuẩn hóa tham chiếu |
| `logo_path` | Chuỗi | Nullable | Ảnh logo thương hiệu |
| `description` | Văn bản | Nullable | Mô tả thương hiệu |
| `is_active` | Boolean | Mặc định `true` | Quyết định có hiển thị ở storefront hay không |
| `display_order` | Số nguyên | Mặc định `0` | Thứ tự hiển thị |
| `meta` | JSON | Nullable | Thuộc tính mở rộng |
| `created_at`, `updated_at`, `deleted_at` | Timestamp | Tự động, soft delete | Theo dõi vòng đời dữ liệu |

#### b) Bảng `product_categories`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh danh mục |
| `name` | Chuỗi | Bắt buộc | Tên danh mục |
| `slug` | Chuỗi | Duy nhất | URL thân thiện cho trang danh mục |
| `code` | Chuỗi | Nullable, duy nhất | Mã danh mục hỗ trợ đồng bộ hoặc chuẩn hóa |
| `parent_id` | Khóa ngoại tự tham chiếu | Nullable, `nullOnDelete` | Tạo cấu trúc cây phân cấp danh mục |
| `thumbnail_path` | Chuỗi | Nullable | Ảnh đại diện danh mục |
| `description` | Văn bản | Nullable | Mô tả nội dung danh mục |
| `is_active` | Boolean | Mặc định `true` | Điều khiển hiển thị |
| `display_order` | Số nguyên | Mặc định `0` | Thứ tự ưu tiên |
| `meta_title` | Chuỗi | Nullable | SEO title |
| `meta_description` | Văn bản | Nullable | SEO description |
| `meta` | JSON | Nullable | Thuộc tính mở rộng |
| `created_at`, `updated_at`, `deleted_at` | Timestamp | Tự động, soft delete | Theo dõi vòng đời danh mục |

Hai bảng này là lớp taxonomy cốt lõi của catalog. Chúng quyết định cách dữ liệu sản phẩm được tổ chức, lọc, duyệt và hiển thị ra ngoài. Vì vậy, việc tách riêng đặc tả của chúng là cần thiết trong báo cáo.

### 2.10.32. Bảng 2.30. Đặc tả chi tiết cho `locations`, `shipping_fees`, `settings`

#### a) Bảng `locations`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh đơn vị địa lý |
| `name` | Chuỗi | Bắt buộc | Tên địa bàn |
| `code` | Chuỗi | Nullable, duy nhất | Mã địa bàn nếu dùng đồng bộ dữ liệu |
| `type` | Enum | Bắt buộc | Cấp địa lý: `country`, `city`, `province`, `ward` |
| `parent_id` | Khóa ngoại tự tham chiếu | Nullable, `nullOnDelete` | Xây dựng cây hành chính |
| `display_order` | Số nguyên | Mặc định `0` | Thứ tự sắp xếp |
| `is_active` | Boolean | Mặc định `true` | Địa bàn có còn được sử dụng hay không |
| `meta` | JSON | Nullable | Thông tin mở rộng |
| `created_at`, `updated_at` | Timestamp | Tự động | Theo dõi thay đổi dữ liệu địa lý |

#### b) Bảng `shipping_fees`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh cấu hình phí giao hàng |
| `city_id` | Khóa ngoại | Nullable, `nullOnDelete` | Thành phố áp dụng |
| `province_id` | Khóa ngoại | Nullable, `nullOnDelete` | Tỉnh hoặc quận/huyện áp dụng |
| `ward_id` | Khóa ngoại | Nullable, `nullOnDelete` | Phường/xã áp dụng |
| `amount` | Số thập phân | Mặc định `0` | Mức phí giao hàng |
| `currency` | Chuỗi 3 ký tự | Mặc định `VND` | Đồng tiền tính phí |
| `created_at`, `updated_at` | Timestamp | Tự động | Hỗ trợ truy vết thay đổi phí |

#### c) Bảng `settings`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh bản ghi cấu hình |
| `key` | Chuỗi | Duy nhất | Tên khóa cấu hình |
| `value` | Văn bản | Nullable | Giá trị cấu hình |
| `type` | Chuỗi | Mặc định `string` | Kiểu dữ liệu logic của cấu hình |
| `group` | Chuỗi | Nullable | Nhóm cấu hình như SEO, contact, storefront, system |
| `meta` | JSON | Nullable | Thuộc tính mở rộng |
| `created_at`, `updated_at` | Timestamp | Tự động | Theo dõi lịch sử cập nhật |

Nhóm bảng này cho thấy thiết kế dữ liệu của hệ thống không chỉ phục vụ giao dịch mà còn phục vụ cấu hình vận hành và mô hình hóa địa lý giao hàng. Đây là phần nhiều báo cáo đồ án thường bỏ qua, nhưng lại rất đáng trình bày để tăng giá trị học thuật.

### 2.10.33. Bảng 2.31. Đặc tả chi tiết cho `users`, `roles`, `permissions`

#### a) Bảng `users`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh người dùng nội bộ |
| `name` | Chuỗi | Bắt buộc | Tên quản trị viên |
| `email` | Chuỗi | Duy nhất | Tài khoản đăng nhập |
| `email_verified_at` | Timestamp | Nullable | Mốc xác minh email |
| `password` | Chuỗi | Bắt buộc | Mật khẩu đã băm |
| `remember_token` | Chuỗi | Nullable | Phục vụ ghi nhớ phiên đăng nhập |
| `created_at`, `updated_at` | Timestamp | Tự động | Theo dõi vòng đời tài khoản nội bộ |

#### b) Bảng `roles`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh vai trò |
| `name` | Chuỗi | Kết hợp duy nhất với `guard_name` | Tên vai trò như `Super Admin`, `Editor`, `Manager` |
| `guard_name` | Chuỗi | Kết hợp duy nhất với `name` | Phân biệt guard áp dụng |
| `created_at`, `updated_at` | Timestamp | Tự động | Theo dõi thay đổi vai trò |

#### c) Bảng `permissions`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Số nguyên tăng tự động | Khóa chính | Định danh quyền |
| `name` | Chuỗi | Kết hợp duy nhất với `guard_name` | Tên quyền như `manage products`, `manage system` |
| `guard_name` | Chuỗi | Kết hợp duy nhất với `name` | Guard mà quyền này áp dụng |
| `created_at`, `updated_at` | Timestamp | Tự động | Theo dõi lịch sử chỉnh sửa |

Ba bảng này tạo nên lõi xác thực và phân quyền của khu vực quản trị. Chúng tách biệt hoàn toàn khỏi bảng `customers`, qua đó khẳng định rằng người dùng mua hàng và người dùng nội bộ là hai nhóm tác nhân khác nhau ở mức dữ liệu.

### 2.10.34. Bảng 2.32. Đặc tả chi tiết cho `model_has_roles`, `model_has_permissions`, `role_has_permissions`

Các bảng trung gian của Spatie Permission có thể ít được chú ý hơn các bảng chính, nhưng lại là chìa khóa làm cho mô hình phân quyền của hệ thống hoạt động được trên thực tế.

#### a) Bảng `model_has_roles`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `role_id` | Khóa ngoại | Thuộc khóa chính tổng hợp | Vai trò được gán |
| `model_type` | Chuỗi | Thuộc khóa chính tổng hợp | Loại model nhận vai trò |
| `model_id` | Số nguyên lớn | Thuộc khóa chính tổng hợp | ID của model được gán vai trò |

#### b) Bảng `model_has_permissions`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `permission_id` | Khóa ngoại | Thuộc khóa chính tổng hợp | Quyền được gán trực tiếp |
| `model_type` | Chuỗi | Thuộc khóa chính tổng hợp | Loại model nhận quyền |
| `model_id` | Số nguyên lớn | Thuộc khóa chính tổng hợp | ID của model nhận quyền |

#### c) Bảng `role_has_permissions`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `permission_id` | Khóa ngoại | Thuộc khóa chính tổng hợp | Quyền được gán |
| `role_id` | Khóa ngoại | Thuộc khóa chính tổng hợp | Vai trò sở hữu quyền |

Nhờ ba bảng này, hệ thống có thể triển khai cả hai chiến lược: gán quyền thông qua vai trò và gán quyền trực tiếp cho một tài khoản đặc biệt nếu cần. Từ góc nhìn học thuật, đây là biểu hiện của mô hình RBAC với khả năng mở rộng linh hoạt.

### 2.10.35. Bảng 2.33. Đặc tả chi tiết cho `password_reset_tokens` và `sessions`

#### a) Bảng `password_reset_tokens`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `email` | Chuỗi | Khóa chính | Xác định tài khoản cần đặt lại mật khẩu |
| `token` | Chuỗi | Bắt buộc | Mã đặt lại mật khẩu tạm thời |
| `created_at` | Timestamp | Nullable | Thời điểm sinh token |

#### b) Bảng `sessions`

| Tên cột | Kiểu logic | Ràng buộc | Ý nghĩa nghiệp vụ |
|---|---|---|---|
| `id` | Chuỗi | Khóa chính | Định danh phiên làm việc |
| `user_id` | Khóa ngoại | Nullable, có chỉ mục | Tài khoản admin gắn với phiên |
| `ip_address` | Chuỗi | Nullable | Dấu vết IP của phiên |
| `user_agent` | Văn bản | Nullable | Dấu vết trình duyệt hoặc client |
| `payload` | Văn bản dài | Bắt buộc | Dữ liệu phiên được lưu |
| `last_activity` | Số nguyên | Có chỉ mục | Mốc hoạt động gần nhất của phiên |

Hai bảng này bổ sung góc nhìn xác thực và an toàn cho khu vực quản trị. Chúng giúp hệ thống hỗ trợ quên mật khẩu, khôi phục truy cập và theo dõi trạng thái phiên, là các yếu tố quan trọng đối với một phân hệ admin thực tế.

### 2.10.36. Nhận xét về lớp dữ liệu xác thực, phân quyền và cấu hình

Nếu nhóm bảng catalog và order phản ánh bản chất thương mại điện tử của hệ thống, thì nhóm bảng `users`, `roles`, `permissions`, `sessions`, `settings`, `locations` phản ánh bản chất vận hành và quản trị. Đây là lớp dữ liệu thường bị đánh giá thấp ở các đồ án nhỏ, nhưng lại là nơi thể hiện rõ mức độ nghiêm túc của thiết kế hệ thống.

Việc tách riêng khách hàng và quản trị viên, kết hợp với ma trận quyền rõ ràng và dữ liệu cấu hình hoặc phiên làm việc tương đối đầy đủ, cho thấy hệ thống đã được nhìn nhận như một nền tảng có vận hành nội bộ thực sự, chứ không phải chỉ là website trình diễn bề mặt.

## 2.11. Kết chương

Chương này đã trình bày tương đối đầy đủ quá trình phân tích và thiết kế hệ thống cho website thương mại điện tử bán thiết bị công nghệ được xây dựng trên nền tảng Laravel 11. Nội dung chương được triển khai theo trình tự logic từ khảo sát thực trạng, phân tích yêu cầu, mô hình hóa hệ thống, đặc tả ca sử dụng, sơ đồ hoạt động, sơ đồ tuần tự, sơ đồ lớp, ERD, thiết kế giao diện người dùng cho đến thiết kế cơ sở dữ liệu.

Điểm nổi bật của chương là mọi nội dung đều được xây dựng trên cơ sở bám sát mã nguồn và cấu trúc triển khai thực tế của đề tài. Các phân tích không dừng ở mức mô tả bề mặt chức năng mà đã đi sâu vào cấu trúc tuyến xử lý, tác nhân, luật nghiệp vụ, lớp dịch vụ, cơ chế phân quyền, dữ liệu chuyển tiếp, các bảng chuẩn hóa mới và những yêu cầu phục vụ phân tích dữ liệu, báo cáo và giám sát vận hành. Nhờ đó, chương này không chỉ có giá trị mô tả, mà còn có vai trò như nền tảng học thuật để giải thích vì sao hệ thống được tổ chức theo cấu trúc hiện tại.

Thông qua các bảng đặc tả ca sử dụng, ma trận vai trò, luật nghiệp vụ và các bảng đặc tả cơ sở dữ liệu, Chương 2 đã hình thành được một khung phân tích tương đối chặt chẽ cho toàn bộ đề tài. Đây là cơ sở quan trọng để các phần tiếp theo của báo cáo tiếp tục trình bày quá trình xây dựng, hiện thực chức năng và đánh giá hệ thống theo đúng các quyết định thiết kế đã được xác lập trong chương này.
