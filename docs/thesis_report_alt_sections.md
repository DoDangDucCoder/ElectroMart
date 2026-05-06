# Phần Bổ Sung Khác Cho Báo Cáo Đồ Án

Tài liệu này được viết để bổ sung thêm nội dung cho báo cáo theo một hướng khác với các file trước đó. Mục tiêu của file này là tạo ra thêm “đất viết” nhưng không lặp lại nguyên văn các phần đã có trong:

- `docs/thesis_report_revised_sections.md`
- `docs/thesis_report_extended_additions.md`
- `docs/thesis_report_expansion_guide.md`

Nội dung bên dưới tập trung vào các góc nhìn mà báo cáo đồ án thường còn thiếu:

- quyết định kiến trúc
- rủi ro kỹ thuật
- chiến lược chuyển đổi từ legacy
- luồng dữ liệu giữa các tầng
- chiến lược hardening các màn legacy
- giá trị ứng dụng thực tế
- bài học rút ra trong quá trình phát triển hệ thống

---

# PHẦN KHÁC CHO CHƯƠNG 3

## 3.x. Phân tích quyết định kiến trúc của đề tài

Trong quá trình xây dựng một hệ thống website thương mại điện tử, lựa chọn kiến trúc là một quyết định có ảnh hưởng lâu dài đến khả năng bảo trì, mở rộng và kiểm tra chất lượng. Đối với đề tài này, hệ thống không được tổ chức theo mô hình MVC thuần túy ở mức cơ bản, mà đã từng bước chuyển sang hướng phân lớp rõ hơn gồm controller, request validation, service nghiệp vụ, support classes, domain layer và persistence layer.

Quyết định này không chỉ mang tính kỹ thuật mà còn mang tính thực tiễn. Trong các dự án nhỏ, việc đặt hầu hết logic trong controller có thể giúp phát triển nhanh ở giai đoạn đầu. Tuy nhiên, khi hệ thống tăng số lượng chức năng như giỏ hàng, thanh toán, flash sale, search suggest, so sánh sản phẩm, chatbot, báo cáo, phân quyền và API, cách tổ chức đó sẽ bộc lộ hạn chế rất rõ. Controller trở nên dài, khó đọc, khó kiểm tra chất lượng và khó tái sử dụng.

Vì vậy, dự án đã chọn hướng tách dần nghiệp vụ sang các lớp `Service`, các request validation, các support classes và các repository/persistence layer. Đây là một quyết định hợp lý đối với một hệ thống đang phát triển theo hướng ngày càng phức tạp hơn.

## 3.x. So sánh giữa mô hình MVC cơ bản và kiến trúc hiện tại của dự án

Nếu chỉ quan sát bề ngoài, dự án vẫn là một ứng dụng Laravel theo tinh thần MVC. Tuy nhiên, khi đi sâu vào mã nguồn, có thể nhận thấy kiến trúc thực tế đã phong phú hơn:

- lớp `View` được tổ chức bằng Blade và các partials tái sử dụng
- lớp `Controller` tiếp nhận request và điều phối luồng
- lớp `Request` đảm nhiệm validation đầu vào
- lớp `Application Services` xử lý nghiệp vụ theo domain
- lớp `Support` gom các quy tắc điều hướng, xác thực và phân quyền
- lớp `Domain` và `Infrastructure/Persistence` hỗ trợ chuẩn hóa dữ liệu và repository

Như vậy, kiến trúc của dự án có thể xem là một bước chuyển tiếp từ MVC truyền thống sang một cấu trúc gần với layered architecture hoặc domain-oriented architecture ở mức phù hợp với quy mô đồ án.

## 3.x. Phân tích lý do cần hiện đại hóa từ hệ thống legacy

Hệ thống legacy thường có ưu điểm là đã có dữ liệu và có luồng hoạt động thực tế. Tuy nhiên, nhược điểm lớn là:

- mã nguồn dễ bị dồn logic vào controller
- validation phân tán và khó kiểm soát
- cấu trúc dữ liệu thiếu nhất quán
- khó thêm API hoặc mở rộng chức năng mới
- khó kiểm tra chất lượng một cách có hệ thống

Chính vì vậy, việc hiện đại hóa dần hệ thống là một hướng đi phù hợp. Thay vì viết lại toàn bộ từ đầu, dự án lựa chọn cách tiếp cận an toàn hơn: giữ cho các luồng cũ tiếp tục vận hành, đồng thời chuẩn hóa dần kiến trúc ở những vùng có tác động lớn như search, checkout, admin permissions, reports và API.

## 3.x. Rủi ro kỹ thuật trong dự án

Một đề tài bám sát thực tế không nên chỉ mô tả những gì hệ thống làm được, mà còn nên chỉ ra các rủi ro kỹ thuật đã hoặc có thể xuất hiện trong quá trình phát triển.

### 3.x.1. Rủi ro do dữ liệu legacy và dữ liệu mới cùng tồn tại

Khi hai lớp dữ liệu tồn tại song song, một số rủi ro có thể phát sinh như:

- cùng một nghiệp vụ nhưng được biểu diễn theo hai mô hình khác nhau
- dễ nhầm lẫn giữa model legacy và model mới
- cập nhật ở một luồng nhưng không đồng bộ kỳ vọng ở luồng khác
- báo cáo hoặc ERD dễ bị mô tả không đúng thực tế nếu chỉ nhìn một phía

### 3.x.2. Rủi ro do luồng storefront và admin dùng nhiều kiểu mã khác nhau

Trong dự án hiện tại, có những phần đã được chuẩn hóa tốt hơn, nhưng cũng có những phần còn phụ thuộc mạnh vào controller legacy và view legacy. Việc xen kẽ nhiều phong cách mã nguồn có thể làm tăng chi phí bảo trì, đặc biệt khi người đọc mới tham gia vào dự án.

### 3.x.3. Rủi ro do thay đổi giao diện ảnh hưởng tới nghiệp vụ

Đối với storefront, những phần như search suggest, quick cart, header account state, checkout gate, compare state hay recommendation panel không chỉ là giao diện. Chúng gắn trực tiếp với session, request, validation và route điều hướng. Vì vậy, một thay đổi nhỏ về UI cũng có thể làm phát sinh lỗi nghiệp vụ nếu không có kiểm tra chất lượng đủ tốt.

### 3.x.4. Rủi ro do quyền truy cập và API

Khi hệ thống có cả admin web và admin API, việc phân quyền cần được kiểm soát chặt chẽ hơn. Nếu chỉ xác thực mà không phân quyền theo capability cụ thể, các endpoint nhạy cảm như quản lý sản phẩm, đơn hàng, thống kê hoặc settings có thể bị truy cập sai phạm vi.

## 3.x. Chiến lược giảm thiểu rủi ro kỹ thuật

Để giảm thiểu các rủi ro nêu trên, dự án đã và nên tiếp tục áp dụng các chiến lược sau:

- tách validation ra khỏi controller
- gom nghiệp vụ phức tạp vào service layer
- dùng support classes để thống nhất điều hướng và phân quyền
- harden các màn legacy thay vì bỏ mặc chúng
- duy trì cách đặt tên và tổ chức route có logic theo nhóm

Việc trình bày các chiến lược này trong báo cáo giúp đồ án có chiều sâu thực tiễn hơn, vì nó cho thấy người thực hiện đã ý thức được không chỉ chức năng, mà cả độ ổn định và khả năng bảo trì của hệ thống.

## 3.x. Phân tích vai trò của dữ liệu hành vi trong hệ thống

Repo hiện có nhiều dấu hiệu cho thấy hệ thống không chỉ quản lý dữ liệu giao dịch, mà còn quan tâm đến dữ liệu hành vi người dùng:

- `product_views`
- `analytics_events`
- `page_views`
- `user_sessions`
- `product_discovery_analytics`
- search-related indexes

Điều này rất đáng được nhấn mạnh trong báo cáo, vì nó cho thấy hệ thống đã bắt đầu vượt ra ngoài phạm vi bán hàng cơ bản, tiến tới hỗ trợ phân tích hành vi truy cập, hiệu quả khám phá sản phẩm và khả năng tối ưu đề xuất nội dung.

## 3.x. Phân tích vai trò của recommendation và discovery

Trong lĩnh vực thương mại điện tử, đặc biệt là bán thiết bị công nghệ, người dùng hiếm khi ra quyết định ngay từ trang đầu tiên. Họ thường cần một quá trình khám phá, so sánh và đối chiếu nhiều lựa chọn. Do đó, các thành phần recommendation và product discovery trong dự án có ý nghĩa quan trọng.

Recommendation không chỉ là một tính năng “đẹp” về mặt giao diện, mà còn là một lớp hỗ trợ quyết định mua hàng. Khi báo cáo phân tích sâu hơn phần này, nó sẽ cho thấy hệ thống không chỉ được xây dựng ở mức hiển thị tĩnh, mà còn có tư duy về hành vi người dùng và hiệu quả chuyển đổi.

## 3.x. Giá trị của việc tách support classes trong thiết kế

Các support classes là một chi tiết nhỏ nhưng rất đáng khai thác trong đồ án. Nếu báo cáo chỉ nói “có controller và service”, người đọc sẽ thấy kiến trúc dừng ở mức phổ biến. Tuy nhiên, sự xuất hiện của các lớp như:

- `StorefrontCustomerAuth`
- `AdminPermissions`
- `AdminAccess`
- `AdminAuthenticator`
- `AdminWorkspace`
- `StorefrontLabel`

cho thấy hệ thống đã có thêm một lớp tổ chức để gom các quy tắc liên quan đến điều hướng, xác thực, phân quyền, shell và nhãn hiển thị. Điều này có giá trị thực tiễn cao vì nó giúp controller gọn hơn và làm cho hành vi hệ thống nhất quán hơn.

---

# PHẦN KHÁC CHO CHƯƠNG 4

## 4.x. Luồng dữ liệu từ request đến response trong storefront

Một cách làm báo cáo dài hơn nhưng vẫn tốt là mô tả rõ luồng dữ liệu giữa các tầng thay vì chỉ liệt kê tên file.

### 4.x.1. Luồng tìm kiếm sản phẩm

Khi người dùng nhập từ khóa trên thanh tìm kiếm, request được gửi tới các endpoint như `autocomplete-ajax` hoặc trang tìm kiếm đầy đủ. Controller tiếp nhận request, sau đó gọi các thành phần xử lý tìm kiếm và recommendation. Dữ liệu kết quả được đẩy về Blade partial để hiển thị gợi ý hoặc danh sách sản phẩm.

Luồng này cho thấy một request tưởng như đơn giản thực chất đi qua nhiều tầng: giao diện, route, controller, request validation, service, truy vấn dữ liệu, view partial và JavaScript tương tác.

### 4.x.2. Luồng thêm giỏ hàng

Khi khách hàng nhấn thêm giỏ hàng, request AJAX được gửi tới controller. Controller gọi `CartService` để xử lý tồn kho, số lượng và trạng thái sản phẩm. Sau khi hoàn tất, hệ thống cập nhật session hoặc dữ liệu tạm, rồi render lại các fragment như badge, hover cart hoặc quick cart. Đây là ví dụ rõ cho luồng nghiệp vụ có sự kết hợp giữa backend, session và giao diện động.

### 4.x.3. Luồng checkout có xác thực

Khi người dùng đi từ giỏ hàng sang checkout, hệ thống phải kiểm tra trạng thái đăng nhập khách hàng. Nếu chưa xác thực, request được điều hướng sang màn đăng nhập. Sau khi xác thực thành công, support class phụ trách lưu đích quay lại và điều hướng đúng về `checkout` hoặc `payment`. Luồng này thể hiện rõ giá trị của support classes và request handling trong hệ thống.

## 4.x. Luồng dữ liệu trong admin

### 4.x.1. Luồng quản lý sản phẩm

Quản trị viên thao tác trên form sản phẩm, gửi request cập nhật tới `ProductController`. Dữ liệu được kiểm tra, sau đó ghi nhận vào cơ sở dữ liệu. Nếu có các phần liên quan như gallery, attribute, document hoặc pricing fields, các thành phần tương ứng sẽ tiếp tục được đồng bộ. Đây là một luồng nhiều bước, không thể xem đơn thuần là “một màn CRUD”.

### 4.x.2. Luồng quản lý đơn hàng

Tại admin, quản trị viên không chỉ xem danh sách đơn mà còn có thể mở chi tiết đơn, in đơn, cập nhật trạng thái, xử lý số lượng và phối hợp với phần delivery. Luồng này cho thấy khu vực quản trị của dự án đã tiến gần hơn tới mô hình điều hành nghiệp vụ thay vì chỉ nhập dữ liệu.

### 4.x.3. Luồng chat inbox hỗ trợ

Khi khách hàng tương tác với chatbot, hội thoại và tin nhắn được ghi nhận ở phía support. Quản trị viên có thể mở inbox, xem hội thoại, phản hồi và phân công. Đây là ví dụ cho thấy luồng dữ liệu của hệ thống không chỉ đi theo hướng bán hàng, mà còn bao gồm cả vòng hỗ trợ sau tương tác.

## 4.x. Chiến lược hardening các màn legacy

Một điểm có thể viết rất tốt trong báo cáo là chiến lược hardening các màn legacy. Thay vì xóa toàn bộ các màn cũ, dự án đã đi theo hướng:

- kiểm tra dữ liệu đầu vào kỹ hơn
- thêm validation ở request layer
- thay raw error bằng fallback hoặc thông báo rõ nghĩa
- chỉnh các fail-state admin cho dễ hiểu hơn
- thêm rà soát hồi quy cho lỗi đã phát hiện

Chiến lược này thể hiện tư duy thực tiễn, bởi trong môi trường phát triển thật, không phải lúc nào cũng có thể viết lại mọi thứ từ đầu.

## 4.x. Giá trị của việc tổ chức view theo partials

Thư mục `resources/views/partials` cho thấy dự án đã tách nhiều thành phần dùng chung ra khỏi các trang chính. Việc tổ chức này đem lại nhiều lợi ích:

- giảm lặp mã giao diện
- dễ sửa một nơi, hiệu lực nhiều nơi
- dễ chuẩn hóa layout storefront và admin
- hỗ trợ tốt cho việc mở rộng UX sau này

Đây là chi tiết đáng viết thêm vì nhiều đồ án chỉ nói “dùng Blade”, nhưng không phân tích được cách Blade được tổ chức như thế nào trong dự án.

## 4.x. Phân tích riêng nhóm frontend assets và backend assets

Hệ thống hiện có tách biệt tài nguyên frontend và backend trong thư mục `public/frontend` và `public/backend`. Việc tách này giúp:

- tổ chức rõ asset theo hai phân hệ
- tránh lẫn CSS/JS giữa storefront và admin
- hỗ trợ quản lý theme và script tốt hơn

Nếu muốn báo cáo dày hơn, có thể dành một tiểu mục ngắn để mô tả riêng cách tổ chức asset này.

## 4.x. Vai trò của request validation đối với chất lượng mã nguồn

Khi số lượng form tăng lên, request validation trở thành lớp rất quan trọng trong việc kiểm soát chất lượng dữ liệu đầu vào. Trong repo hiện tại, việc tách request theo từng module cho thấy hệ thống đã tiến tới một cấu trúc chuyên nghiệp hơn:

- storefront cart requests
- storefront checkout requests
- storefront home requests
- storefront product requests
- admin legacy requests
- catalog requests
- cms requests
- api admin requests

Nội dung này nếu viết thêm sẽ giúp Chương 4 mang hơi hướng kỹ thuật hơn và giảm cảm giác chỉ kể lại giao diện.

## 4.x. Vai trò của hệ thống báo cáo trong repo

Nhiều website bán hàng chỉ dừng ở quản lý sản phẩm và đơn hàng. Tuy nhiên, repo hiện tại đã có các service và controller cho báo cáo lợi nhuận sản phẩm, khám phá sản phẩm và thống kê quản trị. Điều này cho thấy hệ thống đã bắt đầu hỗ trợ ra quyết định, chứ không chỉ dừng ở thao tác nhập liệu và xuất dữ liệu.

Đây là nội dung rất nên khai thác vì nó giúp đề tài có chiều sâu hơn khi so với các đồ án TMĐT thông thường.

## 4.x. Vai trò của mail workflow

Phần mail trong dự án không chỉ dùng để gửi một thông báo đơn lẻ, mà đã có các template, mail data classes và workflow riêng như:

- xác nhận đơn hàng
- khôi phục mật khẩu
- chiến dịch coupon

Việc này cho thấy hệ thống đã có một mức độ “gần ứng dụng thực tế” cao hơn, vì email là một phần quan trọng trong vận hành hệ thống thương mại điện tử.

## 4.x. Vai trò của search suggest và product discovery đối với trải nghiệm người dùng

Thanh tìm kiếm của dự án không còn là một ô nhập tĩnh đơn giản. Nó được mở rộng theo hướng search suggest, trending ideas, product hints và autocomplete. Đồng thời, phần product discovery còn được gắn với analytics và reporting. Đây là một hướng phát triển rất đáng đưa vào báo cáo, vì nó thể hiện rằng dự án đang quan tâm đến cách người dùng khám phá sản phẩm, chứ không chỉ thao tác mua hàng thuần túy.

---

# PHẦN KHÁC CHO CHƯƠNG 5

## 5.x. Phân tích ý nghĩa của kiểm soát chất lượng trong đồ án

Một trong những điểm giúp dự án nổi bật hơn so với nhiều đồ án cùng loại là quá trình rà soát chất lượng và kiểm tra luồng nghiệp vụ. Báo cáo có thể viết thêm về ý nghĩa của hoạt động kiểm soát chất lượng trong chính bối cảnh của dự án.

Đối với một hệ thống hybrid, kiểm soát chất lượng không chỉ giúp phát hiện lỗi mới, mà còn hỗ trợ đánh giá rủi ro khi refactor các phần legacy. Chỉ cần một thay đổi nhỏ ở route, controller, support class hoặc view fragment cũng có thể gây ảnh hưởng tới luồng mua hàng, admin hoặc API.

## 5.x. Đánh giá đề tài từ góc nhìn thực tiễn

Nếu muốn báo cáo có chiều sâu hơn, có thể thêm một mục đánh giá từ góc nhìn thực tiễn, chẳng hạn:

- hệ thống có khả năng dùng như nền tảng cho một website bán hàng quy mô vừa
- storefront đã có đủ các luồng tương tác cốt lõi
- admin đã có các công cụ điều hành tương đối rõ
- API, reports, chatbot và commands làm tăng mức độ hoàn thiện của dự án

Viết theo hướng này sẽ giúp báo cáo có tính thuyết phục hơn, đặc biệt khi cần trả lời câu hỏi “đề tài có ứng dụng thực tế đến đâu”.

## 5.x. Những bài học rút ra từ quá trình thực hiện đề tài

Đây là một mục rất hay nhưng nhiều báo cáo thường bỏ qua. Có thể viết thành 3 nhóm bài học:

### 5.x.1. Bài học về tổ chức mã nguồn

Quá trình phát triển cho thấy việc tách request validation, service, support classes và domain/persistence là cần thiết khi hệ thống lớn dần. Nếu không làm vậy từ sớm, mã nguồn sẽ nhanh chóng khó kiểm soát.

### 5.x.2. Bài học về dữ liệu và tương thích ngược

Trong thực tế, chuyển từ dữ liệu cũ sang mô hình mới không thể làm trong một bước. Việc giữ tương thích ngược nhưng vẫn chuẩn hóa dần là một kỹ năng quan trọng trong phát triển phần mềm thực tế.

### 5.x.3. Bài học về kiểm tra chất lượng và chất lượng

Việc thêm test cho những lỗi đã gặp giúp giảm đáng kể khả năng lỗi quay lại. Đây là một kinh nghiệm rất quan trọng khi làm dự án có nhiều chỉnh sửa liên tục.

## 5.x. Định hướng nghiên cứu tiếp theo

Nếu muốn làm báo cáo mang hơi hướng học thuật hơn nữa, có thể viết thêm một mục “định hướng nghiên cứu tiếp theo”, phân biệt với “hướng phát triển sản phẩm”. Ví dụ:

- nghiên cứu recommendation sâu hơn dựa trên hành vi người dùng
- nghiên cứu cá nhân hóa nội dung storefront
- nghiên cứu tối ưu hiệu năng ở quy mô dữ liệu lớn
- nghiên cứu chuyển đổi hoàn toàn từ hybrid sang kiến trúc chuẩn hóa

Mục này giúp đồ án trông giống một công trình nghiên cứu ứng dụng hơn, thay vì chỉ là mô tả xây dựng website.

---

# PHỤ LỤC BỔ SUNG KHÁC CÓ THỂ THÊM

## Phụ lục D. Bảng gợi ý module và file đại diện

| Module | Một số file đại diện |
|---|---|
| Catalog | `ProductController`, `ProductService`, `ProductVariant`, `CategoryController` |
| Checkout | `CartController`, `CheckoutController`, `CartService`, `CheckoutService` |
| Customer | `CustomerService`, `RegisterCustomerRequest`, `StorefrontCustomerAuth` |
| Reporting | `ProductDiscoveryReportController`, `ProductProfitController`, `StatisticsController` |
| Support | `ChatController`, `ChatbotService`, `ChatInboxService` |

## Phụ lục E. Bảng gợi ý các rủi ro và cách giảm thiểu

| Rủi ro | Ảnh hưởng | Hướng giảm thiểu |
|---|---|---|
| Dữ liệu legacy và mới không đồng nhất | Sai lệch nghiệp vụ | Chuẩn hóa dần, thêm test |
| Controller legacy khó bảo trì | Tăng lỗi runtime | Tách validation và service |
| API thiếu phân quyền chặt | Rủi ro bảo mật | Sanctum + middleware + permissions |

## Phụ lục F. Bảng gợi ý mô tả giá trị của từng nhóm test

| Nhóm test | Giá trị chính |
|---|---|
| Storefront | Bảo vệ luồng mua hàng |
| Admin | Bảo vệ luồng quản trị và phân quyền |
| API | Bảo vệ contract và truy cập dữ liệu |
| Report | Bảo vệ logic thống kê |
| Mail | Bảo vệ nội dung và workflow email |
| Chatbot | Bảo vệ luồng hỗ trợ khách hàng |

---

# Kết luận của file này

Nếu anh muốn báo cáo dài thêm mà không bị lặp lại các file trước, thì các hướng tốt nhất là:

- viết về quyết định kiến trúc
- viết về rủi ro kỹ thuật và chiến lược giảm thiểu
- viết về luồng dữ liệu giữa các tầng
- viết về giá trị thực tiễn của API, reports, chatbot, commands
- viết về bài học rút ra và định hướng nghiên cứu tiếp theo

Đây là nhóm nội dung khác với các phần tôi đã viết trước đó, và rất phù hợp để anh tự gộp thêm vào báo cáo Word.
