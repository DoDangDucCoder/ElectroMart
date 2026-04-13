# PHẦN BỔ SUNG CHO CHƯƠNG 2. CƠ SỞ LÝ THUYẾT

Tài liệu này được viết để bổ sung trực tiếp cho chương cơ sở lý thuyết của báo cáo. Mục tiêu của file là làm dày hơn phần nền tảng học thuật theo một hướng còn thiếu trong bản hiện tại, đặc biệt ở các nội dung:

- vai trò của HTML, CSS và JavaScript trong website thương mại điện tử
- ý nghĩa của hình ảnh và media trong bán hàng trực tuyến
- khái niệm khách vãng lai trong hệ thống website
- phân tích ba website tham chiếu để rút ra bài học thiết kế cho đề tài

Các phần bên dưới được viết theo hướng Word-ready. Anh có thể ghép trực tiếp vào Chương 2 hoặc dùng như một file bổ sung để làm dày thêm phần cơ sở lý thuyết.

---

## 2.x. Vai trò của HTML trong xây dựng website thương mại điện tử

HTML (HyperText Markup Language) là ngôn ngữ đánh dấu cơ bản dùng để tổ chức cấu trúc nội dung trên web. Trong bất kỳ website thương mại điện tử nào, HTML không chỉ có chức năng hiển thị chữ và hình, mà còn đóng vai trò tạo ra bộ khung logic cho toàn bộ giao diện như header, navigation, danh sách sản phẩm, biểu mẫu đăng nhập, giỏ hàng, checkout, phần đánh giá và footer.

Về mặt lý thuyết, HTML có ý nghĩa đặc biệt vì nó quyết định tính có cấu trúc của dữ liệu giao diện. Một trang chi tiết sản phẩm nếu chỉ được dựng theo kiểu trình bày trực quan mà không có cấu trúc hợp lý sẽ khó mở rộng, khó tối ưu SEO và khó tích hợp với CSS, JavaScript hoặc các công cụ phân tích hành vi người dùng. Ngược lại, khi nội dung được tổ chức bằng các thành phần semantic như `header`, `nav`, `section`, `article`, `main`, `aside`, `footer`, hệ thống sẽ rõ ràng hơn cả với trình duyệt, người phát triển lẫn các công cụ tìm kiếm.

Đối với website thương mại điện tử bán thiết bị công nghệ, HTML còn là nền tảng để mô hình hóa các vùng thông tin có độ chi tiết cao. Ví dụ, một trang sản phẩm cần có khu vực tiêu đề, mô tả ngắn, giá, giá khuyến mãi, tồn kho, biến thể, gallery ảnh, thông số kỹ thuật, bình luận và gợi ý sản phẩm liên quan. Nếu cấu trúc HTML không rõ, những khu vực này sẽ dễ bị trộn lẫn, gây khó khăn cho việc bảo trì giao diện cũng như cho việc gắn dữ liệu từ backend.

Ngoài ra, HTML còn gắn trực tiếp với các form nghiệp vụ như đăng ký, đăng nhập, nhập địa chỉ giao hàng, áp dụng coupon và xác nhận đơn hàng. Trong bối cảnh đó, HTML không đơn thuần là phần “trang trí” của website, mà là lớp biểu diễn đầu tiên của dữ liệu nghiệp vụ. Điều này giúp giải thích vì sao trong các ứng dụng Laravel server-rendered, tổ chức view tốt luôn là một phần của thiết kế hệ thống chứ không chỉ là phần giao diện.

## 2.x. Vai trò của CSS trong tổ chức giao diện và trải nghiệm người dùng

Nếu HTML chịu trách nhiệm về cấu trúc, CSS (Cascading Style Sheets) chịu trách nhiệm về trình bày và tổ chức trực quan của website. Trong môi trường thương mại điện tử, CSS có vai trò đặc biệt quan trọng vì chất lượng trình bày ảnh hưởng trực tiếp tới khả năng đọc thông tin, mức độ tin cậy của hệ thống và hành vi chuyển đổi của người dùng.

Về mặt lý thuyết, CSS giúp tách nội dung khỏi cách trình bày. Sự tách biệt này mang lại nhiều lợi ích: giao diện dễ bảo trì hơn, có thể thay đổi theme mà không làm hỏng logic HTML, và có thể tái sử dụng các quy tắc hiển thị cho nhiều module khác nhau. Đây là một nguyên tắc quan trọng trong phát triển web hiện đại vì hệ thống thương mại điện tử thường có số lượng màn hình lớn như trang chủ, trang danh mục, trang chi tiết sản phẩm, giỏ hàng, checkout, lịch sử đơn hàng và khu vực quản trị.

Trong website bán thiết bị công nghệ, CSS còn ảnh hưởng trực tiếp đến cách người dùng đọc và so sánh thông tin. Một sản phẩm công nghệ thường có nhiều thông số và nhiều mức giá, do đó nếu khoảng cách, kiểu chữ, màu sắc, thứ bậc tiêu đề và bố cục không hợp lý, người dùng sẽ khó ra quyết định mua hàng. Từ góc nhìn này, CSS không chỉ là công cụ làm đẹp, mà là công cụ tổ chức nhận thức thị giác.

Một điểm quan trọng khác là responsive design. Người dùng thương mại điện tử ngày nay truy cập bằng nhiều thiết bị khác nhau, từ desktop đến điện thoại. CSS với các kỹ thuật như flexbox, grid, breakpoint và media query giúp giao diện thích nghi với kích thước màn hình khác nhau mà vẫn bảo đảm nội dung quan trọng như ảnh sản phẩm, giá, nút mua hàng và trường nhập liệu luôn dễ thao tác. Điều này đặc biệt có ý nghĩa trong thương mại điện tử vì chỉ cần trải nghiệm trên thiết bị di động kém cũng có thể làm giảm mạnh tỷ lệ chuyển đổi.

Ở mức học thuật, CSS còn thể hiện mối liên hệ giữa kỹ thuật giao diện và UX. Một giao diện có cấu trúc CSS tốt sẽ giúp người dùng nhận ra nhanh vùng hành động chính, phân biệt rõ thông tin quan trọng với thông tin phụ và giảm tải nhận thức trong quá trình duyệt sản phẩm. Đây là cơ sở lý thuyết quan trọng để lý giải vì sao phần trình bày giao diện cần được xem như một yếu tố của thiết kế hệ thống, không chỉ là phần mỹ thuật.

## 2.x. Vai trò của JavaScript trong tương tác động của website thương mại điện tử

JavaScript là ngôn ngữ kịch bản phía client, cho phép trang web phản hồi linh hoạt với thao tác của người dùng mà không cần tải lại toàn bộ trang trong mọi trường hợp. Trong lý thuyết phát triển web hiện đại, JavaScript là thành phần giúp nối cấu trúc HTML và cách trình bày CSS với hành vi tương tác thực tế.

Trong website thương mại điện tử, JavaScript thường xuất hiện ở các chức năng như gợi ý tìm kiếm, quickview, cập nhật giỏ hàng bằng AJAX, tính phí vận chuyển, dropdown chọn địa chỉ, chuyển tab thông tin sản phẩm, carousel ảnh, bộ lọc danh mục và các tương tác nhỏ khác. Những thao tác này làm cho hệ thống phản hồi nhanh hơn, giảm số lần tải lại trang và đem lại cảm giác sử dụng mượt hơn.

Tuy nhiên, về mặt lý thuyết cũng cần thấy rằng JavaScript không phải là mục tiêu tự thân. Giá trị của nó nằm ở việc nâng cao khả năng tương tác nhưng vẫn phải đồng bộ với backend. Một thao tác thêm vào giỏ hàng bằng AJAX chỉ có ý nghĩa khi dữ liệu session, coupon, phí vận chuyển và trạng thái quick cart được cập nhật thống nhất. Vì vậy, JavaScript trong thương mại điện tử luôn phải được nhìn như một phần của luồng xuyên suốt từ frontend đến backend, thay vì chỉ là hiệu ứng giao diện.

Đối với đề tài này, JavaScript còn có vai trò trong các phần storefront cần phản hồi theo thời gian thực tương đối như search suggest, add-cart-ajax, hover cart, tính phí vận chuyển và chatbot. Các khu vực này là minh chứng rõ ràng cho việc website không chỉ có dạng server-rendered thuần túy, mà là một mô hình lai trong đó HTML và Blade chịu trách nhiệm render nền, còn JavaScript đảm nhiệm các tương tác động ở từng vùng cục bộ.

Về mặt học thuật, sự phối hợp giữa HTML, CSS và JavaScript phản ánh mô hình phân lớp ở frontend. HTML là lớp cấu trúc, CSS là lớp trình bày, JavaScript là lớp hành vi. Ba lớp này kết hợp với nhau tạo nên giao diện hoàn chỉnh của website thương mại điện tử.

## 2.x. Vai trò của hình ảnh và media trong website bán hàng trực tuyến

Trong thương mại điện tử, hình ảnh là một trong những thành phần có ảnh hưởng mạnh nhất tới quyết định mua hàng. Không giống mua hàng trực tiếp tại cửa hàng vật lý, người dùng trực tuyến không thể chạm, cầm hoặc quan sát sản phẩm ở khoảng cách thực. Vì vậy, hình ảnh và media giữ vai trò thay thế một phần trải nghiệm vật lý đó.

Về mặt lý thuyết, hình ảnh sản phẩm thực hiện nhiều chức năng cùng lúc. Thứ nhất, nó cung cấp thông tin trực quan giúp người dùng nhận diện sản phẩm nhanh hơn so với mô tả văn bản. Thứ hai, nó tăng mức độ tin cậy cho website vì sản phẩm được thể hiện rõ, có nhiều góc chụp, có ảnh chi tiết hoặc ảnh bối cảnh sử dụng. Thứ ba, nó làm tăng tính hấp dẫn của giao diện, hỗ trợ marketing và giúp người dùng ghi nhớ thương hiệu tốt hơn.

Đối với website bán thiết bị công nghệ, hình ảnh còn có vai trò lớn hơn do sản phẩm thường liên quan tới thiết kế bên ngoài, cổng kết nối, màu sắc, kích thước, phụ kiện đi kèm và các chi tiết dễ gây so sánh. Bên cạnh ảnh tĩnh, nhiều website còn bổ sung video, gallery mở rộng, ảnh zoom hoặc infographic thông số kỹ thuật để tăng hiệu quả truyền đạt.

Tuy nhiên, về mặt kỹ thuật, sử dụng hình ảnh cũng kéo theo các yêu cầu về tối ưu hiệu năng. Ảnh có dung lượng quá lớn sẽ làm chậm tốc độ tải trang, trong khi ảnh quá nhỏ hoặc chất lượng quá thấp sẽ làm giảm độ tin cậy. Do đó, về mặt lý thuyết, hệ thống thương mại điện tử cần cân bằng giữa chất lượng hiển thị và hiệu năng thông qua các kỹ thuật như resize, nén ảnh, lazy loading, sinh thumbnail và tổ chức media library.

Trong phạm vi đề tài, sự tồn tại của các module liên quan tới gallery, media, thumbnail, video và document cho thấy hình ảnh không chỉ là tài nguyên phụ trợ. Nó là một phần của mô hình dữ liệu và của trải nghiệm mua hàng. Điều này rất đáng đưa vào cơ sở lý thuyết vì nó giúp người đọc thấy rằng xây dựng website thương mại điện tử không chỉ là bài toán form nhập và cơ sở dữ liệu, mà còn là bài toán truyền tải thông tin trực quan.

## 2.x. Khách vãng lai trong hệ thống website thương mại điện tử

Một khái niệm thường bị bỏ qua trong báo cáo nhưng rất quan trọng ở góc nhìn phân tích hệ thống là khách vãng lai. Khách vãng lai là người dùng truy cập website nhưng chưa đăng nhập và chưa thiết lập quan hệ tài khoản với hệ thống. Họ là lớp người dùng đầu tiên mà bất kỳ website thương mại điện tử nào cũng phải phục vụ tốt nếu muốn tăng khả năng chuyển đổi.

Về mặt nghiệp vụ, khách vãng lai thường có thể thực hiện một số thao tác như xem trang chủ, duyệt danh mục, xem chi tiết sản phẩm, tìm kiếm, đọc đánh giá, so sánh sản phẩm và trong nhiều hệ thống còn có thể thêm tạm vào giỏ hàng. Tuy nhiên, các hành vi nhạy cảm hơn như xác nhận đơn hàng, xem lịch sử giao dịch hoặc quản lý hồ sơ cá nhân thường yêu cầu đăng nhập.

Điều này cho thấy khách vãng lai là một tác nhân riêng, không nên bị gộp hoàn toàn với khách hàng đã đăng nhập. Trong lý thuyết phân tích thiết kế, việc tách bạch hai nhóm này giúp use case rõ hơn, phân quyền chặt hơn và phản ánh đúng trải nghiệm người dùng. Nếu không tách, báo cáo dễ mô tả sai rằng mọi người dùng đều có cùng khả năng thao tác, trong khi thực tế hệ thống vẫn phải có các bước chuyển tiếp như đăng nhập trước checkout.

Đối với đề tài này, khái niệm khách vãng lai rất phù hợp với luồng thật của repo. Người dùng có thể xem sản phẩm và duyệt hệ thống mà không cần xác thực, nhưng khi đi vào `checkout`, hệ thống sẽ điều hướng sang đăng nhập rồi mới quay trở lại bước mua hàng. Vì vậy, việc đưa khách vãng lai vào phần cơ sở lý thuyết không chỉ làm rõ hơn use case, mà còn giúp phần phân tích yêu cầu và sơ đồ tác nhân bám thực tế hơn.

## 2.x. Phân tích website tham chiếu thứ nhất: Thế Giới Di Động

Một website tham chiếu hữu ích cho đề tài là Thế Giới Di Động. Đây là một ví dụ điển hình của website bán thiết bị công nghệ theo mô hình B2C, nơi người dùng được dẫn dắt từ trang chủ tới danh mục, từ danh mục tới trang chi tiết và từ đó sang quyết định mua hàng.

Điểm đáng chú ý về mặt lý thuyết của nhóm website này là cách tổ chức thông tin rất thiên về hỗ trợ ra quyết định. Sản phẩm thường đi kèm ảnh lớn, giá, mức giảm giá, thông tin trả góp, đánh giá người dùng, cấu hình nổi bật và các cụm gợi ý liên quan. Tức là giao diện không chỉ nhằm trưng bày hàng hóa mà còn đóng vai trò như một công cụ tư vấn sơ bộ.

Một bài học quan trọng có thể rút ra là cấu trúc thông tin cần được thiết kế theo hành vi mua hàng thực tế. Người dùng không chỉ muốn biết tên sản phẩm, mà còn muốn so sánh, xem giá hiện tại, biết khuyến mãi và đánh giá sơ bộ chất lượng. Vì vậy, website thương mại điện tử trong đề tài cũng cần tổ chức giao diện theo hướng ưu tiên thông tin quyết định mua hàng thay vì chỉ hiển thị mô tả chung.

## 2.x. Phân tích website tham chiếu thứ hai: FPT Shop

FPT Shop là một ví dụ tham chiếu khác có giá trị về mặt lý thuyết, đặc biệt ở cách tổ chức storefront theo nhóm ngành hàng và nhóm khuyến mãi. Một đặc điểm thường thấy ở các website kiểu này là việc kết hợp giữa chức năng bán hàng và chức năng truyền thông marketing ngay trên cùng một bề mặt giao diện.

Về mặt phân tích hệ thống, điều này cho thấy website thương mại điện tử hiện đại không thể xem như một ứng dụng CRUD đơn giản. Nó phải hỗ trợ banner, flash sale, block khuyến mãi, giới thiệu ưu đãi, nội dung truyền thông, landing page và các vùng điều hướng động. Từ đây có thể thấy rõ mối liên hệ giữa quản trị nội dung và quản trị bán hàng trong cùng một hệ thống.

Bài học rút ra cho đề tài là khu vực admin cần có công cụ quản lý nội dung đủ linh hoạt, không chỉ quản lý sản phẩm mà còn quản lý slider, bài viết, video và các vùng truyền thông khác. Đây chính là lý do mà phần cơ sở lý thuyết nên đưa thêm các khái niệm liên quan tới CMS, giao diện marketing và trình bày nội dung sản phẩm.

## 2.x. Phân tích website tham chiếu thứ ba: CellphoneS

CellphoneS là một ví dụ tiêu biểu cho mô hình storefront giàu tính so sánh và khuyến nghị. Đối với người dùng mua thiết bị công nghệ, trải nghiệm quan trọng không chỉ là “xem một sản phẩm”, mà là “so sánh giữa nhiều lựa chọn” dựa trên thương hiệu, cấu hình, tầm giá và ưu đãi đi kèm.

Về mặt lý thuyết, điều này làm nổi bật tầm quan trọng của các chức năng product discovery, recommendation, compare và filter. Một website tốt trong lĩnh vực công nghệ cần cho phép người dùng thu hẹp lựa chọn dần, từ đó giảm tải nhận thức khi phải đối diện với quá nhiều sản phẩm có thông số gần nhau.

Từ góc nhìn của đề tài, bài học quan trọng là hệ thống không nên chỉ tập trung vào bước cuối cùng của quy trình mua hàng như giỏ hàng và checkout. Ngược lại, nó cần quan tâm cả giai đoạn khám phá sản phẩm. Việc dự án có search suggest, so sánh sản phẩm, review, recommendation và reporting liên quan tới discovery là hoàn toàn phù hợp với xu hướng của các website bán thiết bị công nghệ thực tế.

## 2.x. So sánh và rút ra bài học thiết kế từ ba website tham chiếu

Khi đặt ba website tham chiếu cạnh nhau, có thể nhận ra một số điểm chung quan trọng về mặt lý thuyết. Thứ nhất, cả ba đều coi storefront là một môi trường hỗ trợ ra quyết định, không chỉ là nơi liệt kê sản phẩm. Thứ hai, hình ảnh, banner, block khuyến mãi và thông tin giá luôn được ưu tiên ở vị trí dễ thấy. Thứ ba, các công cụ hỗ trợ khám phá như tìm kiếm, lọc, so sánh, đánh giá và gợi ý sản phẩm đóng vai trò rất lớn.

Điểm chung tiếp theo là sự liên kết chặt giữa website bán hàng và quản trị nội dung. Một hệ thống thương mại điện tử thực tế phải đồng thời giải quyết ba bài toán: quản lý sản phẩm, hỗ trợ hành vi mua hàng và tổ chức nội dung marketing. Đây cũng chính là lý do mà trong đề tài, việc có thêm các module video, post, slider, chat và report là hợp lý hơn so với cách nhìn website như một hệ thống bán hàng tối giản.

Một bài học nữa là vai trò của khách vãng lai. Cả ba nhóm website tham chiếu đều cho phép người dùng duyệt sản phẩm và nghiên cứu rất sâu trước khi đăng nhập. Điều này xác nhận rằng trong phân tích use case, khách vãng lai nên được xem là tác nhân riêng với phạm vi thao tác khác khách hàng đã đăng nhập.

## 2.x. Ý nghĩa của nhóm công nghệ HTML, CSS, JavaScript và hình ảnh đối với đề tài

Nếu nhìn tổng thể, HTML, CSS, JavaScript và hình ảnh không phải là bốn mảnh rời rạc, mà là bốn thành phần cùng tạo nên lớp giao diện của website. HTML tổ chức nội dung, CSS tổ chức cách trình bày, JavaScript tổ chức hành vi tương tác, còn hình ảnh tổ chức phần thông tin trực quan. Khi bốn yếu tố này phối hợp tốt, hệ thống mới có thể đáp ứng đồng thời yêu cầu kỹ thuật, yêu cầu trải nghiệm người dùng và yêu cầu trình bày sản phẩm.

Trong đề tài này, nhóm công nghệ đó đặc biệt phù hợp với mô hình Laravel server-rendered kết hợp AJAX. Blade giúp sinh HTML phía server, CSS chịu trách nhiệm dựng layout và style, JavaScript đảm nhiệm các tương tác động cục bộ như quick cart, search suggest hay chatbot, còn hệ thống media chịu trách nhiệm hiển thị ảnh và video sản phẩm. Đây là một kiến trúc giao diện hợp lý đối với một website bán thiết bị công nghệ quy mô đồ án nhưng đã có nhiều module thực tế.

Vì vậy, bổ sung nhóm nội dung này vào Chương 2 sẽ giúp cơ sở lý thuyết bớt thiên lệch về backend thuần túy. Báo cáo sẽ phản ánh rõ hơn rằng website thương mại điện tử là một hệ thống tích hợp nhiều lớp, trong đó giao diện, hình ảnh và tương tác người dùng giữ vai trò rất lớn trong thành công của sản phẩm.

## 2.x. Kết luận cho phần bổ sung

Phần bổ sung này mở rộng chương cơ sở lý thuyết theo bốn hướng: công nghệ frontend cơ bản, vai trò của hình ảnh, khái niệm khách vãng lai và tham chiếu từ ba website thực tế cùng lĩnh vực. Nhờ đó, Chương 2 có thể đầy hơn, cân bằng hơn và gần với bối cảnh xây dựng website thương mại điện tử bán thiết bị công nghệ hơn.

Nếu ghép file này vào sau phần lý thuyết hiện có, chương cơ sở lý thuyết sẽ không còn chỉ xoay quanh kiến trúc, cơ sở dữ liệu và framework, mà còn bao quát thêm trải nghiệm giao diện, media, hành vi người dùng và các mô hình tham chiếu ngoài thực tế.
