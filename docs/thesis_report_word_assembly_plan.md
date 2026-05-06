# KẾ HOẠCH GHÉP BẢN WORD CUỐI CHO BÁO CÁO ĐỒ ÁN

Tài liệu này dùng để ghép bản Word cuối từ các file markdown đã chuẩn bị trong thư mục `docs`. Mục tiêu của kế hoạch là:

- chọn đúng file nguồn cho từng chương
- tránh chèn trùng nội dung
- giữ nội dung bám repo thật
- thống nhất lại số hình, số bảng và mục lục trước khi nộp

Kế hoạch này được viết dựa trên hiện trạng repo và các file tài liệu đang có tính đến ngày `09/04/2026`.

---

## 1. Kết luận ngắn trước khi ghép

Hiện tại, bộ markdown trong repo đã đủ tốt để tạo thành một bản báo cáo mạnh hơn bản `.docx` đang có. Điểm yếu chính không nằm ở nội dung markdown, mà nằm ở việc bản Word hiện chưa nhập hết các phần đã được chuẩn bị sau này, đồng thời còn giữ một số placeholder như tên sinh viên mẫu, mã sinh viên mẫu, trường, khoa và giảng viên hướng dẫn.

Vì vậy, việc cần làm không phải là viết lại toàn bộ báo cáo từ đầu, mà là:

1. chốt bộ file nguồn chính
2. chèn lại nội dung theo đúng thứ tự
3. thống nhất numbering hình và bảng
4. thêm ảnh chụp giao diện thật của dự án
5. rà soát thông tin cá nhân và mục lục trước khi nộp

---

## 2. Bộ file nên dùng làm nguồn chính

### 2.1. File chính nên dùng

| Vai trò | File nên dùng | Ghi chú |
|---|---|---|
| Chương 2 cơ sở lý thuyết | `docs/thesis_report_theoretical_foundation.md` | Nguồn chính của Chương 2 |
| Bổ sung Chương 2 | `docs/thesis_report_theory_expansion_web_tech_and_reference_sites.md` | Dùng để làm dày thêm Chương 2 |
| Chương 3, 4, 5 nền chính | `docs/thesis_report_revised_sections.md` | Nguồn chính của phần phân tích, xây dựng, kiểm tra chất lượng |
| Bổ sung sâu Chương 3 | `docs/thesis_report_analysis_design_additions.md` | Dùng để tăng độ sát repo của Chương 3 |
| Use case mới theo hướng khách vãng lai | `docs/thesis_report_usecase_plantuml_revised_guest.md` | Nên ưu tiên dùng thay bản use case cũ |

### 2.2. File dùng chọn lọc, không nên chèn nguyên khối

| File | Cách dùng |
|---|---|
| `docs/thesis_report_diagram_pack.md` | Chỉ lấy thêm ERD, activity, deployment, API diagram khi cần |
| `docs/thesis_report_alt_sections.md` | Chỉ lấy ý hoặc một số đoạn cho Chương 5, tránh chèn nguyên vì dễ lặp |
| `docs/thesis_report_extended_additions.md` | Chỉ lấy đoạn nào thực sự còn thiếu, không nhập toàn bộ |
| `docs/thesis_report_usecase_plantuml.md` | Không dùng nếu đã chọn bản `revised_guest` |
| `docs/thesis_report_usecase_modules.md` | Không dùng nếu đã chọn bản PlantUML tương ứng |

### 2.3. File không nên dùng làm nguồn cuối nếu đã có bản mới hơn

- Không dùng bộ use case cũ nếu đã chốt bộ ở `thesis_report_usecase_plantuml_revised_guest.md`.
- Không dùng đồng thời cả “use case có tác nhân hỗ trợ” và “use case có khách vãng lai”.
- Không chèn song song cả Mermaid và PlantUML cho cùng một hình trong thân bài. Chỉ giữ một bản để tránh rối.

---

## 3. Cách ghép Chương 1

### 3.1. Nên giữ gì từ bản Word hiện tại

Chương 1 trong file Word hiện tại đang ở trạng thái tương đối ổn về mặt cấu trúc tổng quan:

- lý do chọn đề tài
- mục tiêu đề tài
- đối tượng và phạm vi nghiên cứu
- phương pháp thực hiện
- ý nghĩa của đề tài
- bố cục đồ án

Những phần này có thể giữ làm nền, nhưng cần rà lại để bảo đảm câu chữ phản ánh đúng dự án thực tế hơn, đặc biệt là:

- website bán thiết bị công nghệ
- có storefront, admin, API
- có dữ liệu legacy và phần chuẩn hóa mới

### 3.2. Việc bắt buộc trước khi nộp

Phải thay toàn bộ placeholder:

- tên sinh viên
- mã sinh viên
- lớp
- khoa hoặc viện
- trường
- giảng viên hướng dẫn
- năm hoặc địa danh nếu còn để mẫu

Nếu không thay các thông tin này, bản Word chưa thể xem là bản nộp.

---

## 4. Cách ghép Chương 2

### 4.1. Bộ nguồn nên ghép

Chương 2 nên được tạo từ hai file:

1. `docs/thesis_report_theoretical_foundation.md`
2. `docs/thesis_report_theory_expansion_web_tech_and_reference_sites.md`

### 4.2. Cách ghép đề xuất

Giữ phần nền từ `thesis_report_theoretical_foundation.md` làm khung chính cho:

- 2.1 đến 2.17

Sau đó, không kết thúc ngay ở `2.18. Kết luận chương`, mà chèn thêm các phần mở rộng rồi mới viết lại kết luận cuối chương.

### 4.3. Thứ tự đánh số nên dùng cho Chương 2

Nên đổi lại thứ tự như sau:

- `2.1` đến `2.17`: giữ từ `thesis_report_theoretical_foundation.md`
- `2.18. Vai trò của HTML trong xây dựng website thương mại điện tử`
- `2.19. Vai trò của CSS trong tổ chức giao diện và trải nghiệm người dùng`
- `2.20. Vai trò của JavaScript trong tương tác động của website thương mại điện tử`
- `2.21. Vai trò của hình ảnh và media trong website bán hàng trực tuyến`
- `2.22. Khách vãng lai trong hệ thống website thương mại điện tử`
- `2.23. Phân tích các website tham chiếu`
- `2.23.1. Thế Giới Di Động`
- `2.23.2. FPT Shop`
- `2.23.3. CellphoneS`
- `2.24. So sánh và rút ra bài học thiết kế từ các website tham chiếu`
- `2.25. Ý nghĩa của nhóm công nghệ HTML, CSS, JavaScript và hình ảnh đối với đề tài`
- `2.26. Kết luận chương`

### 4.4. Lý do ghép theo cách này

Cách ghép trên có ba lợi ích:

- không làm vỡ phần lý thuyết hiện có
- làm Chương 2 dày hơn rõ rệt
- tăng tính cân bằng giữa backend, frontend, UX, media và hành vi người dùng

### 4.5. Nhận xét về độ dài

Hiện tại tổng số từ của:

- `thesis_report_theoretical_foundation.md`
- `thesis_report_theory_expansion_web_tech_and_reference_sites.md`

đã ở mức đủ dày để thường tiệm cận hoặc vượt khoảng 20 trang Word tùy font, cỡ chữ, giãn dòng và lề.

---

## 5. Cách ghép Chương 3

### 5.1. Bộ nguồn nên ghép

Chương 3 nên dùng ba nguồn chính:

1. `docs/thesis_report_revised_sections.md`
2. `docs/thesis_report_analysis_design_additions.md`
3. `docs/thesis_report_usecase_plantuml_revised_guest.md`

### 5.2. Cấu trúc nên dùng cho Chương 3

Khung chính giữ theo `thesis_report_revised_sections.md`:

- `3.1. Mô tả bài toán`
- `3.2. Phân tích yêu cầu hệ thống`
- `3.3. Tác nhân của hệ thống`
- `3.4. Mô tả các sơ đồ UML và mô tả dưới hình`
- `3.5. Đặc tả một số nghiệp vụ chính`
- `3.6. Phân tích dữ liệu và mô hình ERD`
- `3.7. Thiết kế kiến trúc hệ thống`
- `3.8. Thiết kế giao diện tổng quát`
- `3.9. Kết luận chương`

Sau đó chèn nội dung từ `thesis_report_analysis_design_additions.md` vào các vị trí phù hợp:

- phần quy trình phân tích hệ thống: chèn vào cuối mục `3.2` hoặc đầu mục `3.3`
- phần luồng kiến trúc `Request -> Controller -> Service -> Persistence -> Database/View/API`: chèn vào `3.7`
- phần bảng mapping legacy -> schema mới: chèn vào `3.6`
- các sequence/activity bám route thật: chèn vào `3.4` hoặc `3.5`

### 5.3. Cách sửa mục 3.3 Tác nhân của hệ thống

Nếu chốt dùng file `thesis_report_usecase_plantuml_revised_guest.md`, thì mục `3.3` nên sửa lại thành:

- `3.3.1. Khách vãng lai`
- `3.3.2. Khách hàng`
- `3.3.3. Quản trị viên`

Không nên giữ `3.3.3. Tác nhân hỗ trợ` nữa, vì bộ sơ đồ use case mới đã bỏ hướng này.

### 5.4. Bộ hình nên dùng cho Chương 3

Nếu chọn bản use case mới, nên dùng bộ numbering hình như sau:

- `Hình 3.1. Biểu đồ use case tổng quát của hệ thống`
- `Hình 3.2. Biểu đồ use case phần khách vãng lai`
- `Hình 3.3. Biểu đồ use case phần khách hàng`
- `Hình 3.4. Biểu đồ use case phần quản trị viên`
- `Hình 3.5. Sơ đồ hoạt động chức năng tìm kiếm và gợi ý tìm kiếm`
- `Hình 3.6. Sequence diagram nghiệp vụ thêm giỏ hàng bằng AJAX`
- `Hình 3.7. Sequence diagram luồng đăng nhập rồi quay lại checkout`
- `Hình 3.8. Activity diagram nghiệp vụ cập nhật sản phẩm ở admin`
- `Hình 3.9. Sơ đồ quan hệ thực thể ERD của hệ thống`
- `Hình 3.10. Kiến trúc phân tầng của dự án`
- `Hình 3.11` đến `Hình 3.22`: bộ use case theo module

### 5.5. Lưu ý rất quan trọng về numbering

Bản Word hiện tại đang dùng danh mục hình cũ, trong khi bản use case mới đưa thêm `Khách vãng lai` và làm thay đổi nhóm hình đầu chương. Vì vậy, phải chốt một bộ numbering duy nhất.

Khuyến nghị:

- nếu đã dùng file `thesis_report_usecase_plantuml_revised_guest.md`, thì renumber toàn bộ hình Chương 3 theo bộ mới
- không giữ numbering cũ song song với numbering mới
- sau khi chèn xong hình, cập nhật lại `Danh mục hình vẽ`

### 5.6. Những hình nên đưa vào phụ lục thay vì thân bài nếu thiếu trang

Các hình sau có thể đưa vào phụ lục nếu thân chương 3 bị quá dài:

- deployment diagram
- API diagram
- sơ đồ ánh xạ hybrid legacy -> schema mới
- sequence phụ như chatbot, calculate-fee, admin login

Như vậy thân bài vẫn đủ chắc mà không bị quá dày hoặc rối.

---

## 6. Cách ghép Chương 4

### 6.1. Nguồn chính

Chương 4 nên dùng gần như toàn bộ từ:

- `docs/thesis_report_revised_sections.md`

### 6.2. Các mục nên giữ

- `4.1. Môi trường phát triển hệ thống`
- `4.2. Cấu trúc mã nguồn của dự án`
- `4.3. Xây dựng phần khách hàng`
- `4.4. Xây dựng chức năng tài khoản và thanh toán`
- `4.5. Xây dựng khu vực quản trị`
- `4.6. Xây dựng API, báo cáo và các dịch vụ dùng chung`
- `4.7. Console commands và hỗ trợ vận hành`
- `4.8. Tổ chức kiểm tra chất lượng trong mã nguồn`
- `4.9. Kết luận chương`

### 6.3. Điều chỉnh nên làm cho Chương 4

Nên thêm ảnh chụp màn hình thật của:

- trang chủ
- trang chi tiết sản phẩm
- giỏ hàng
- checkout
- dashboard admin
- quản lý sản phẩm
- màn hình báo cáo

Chương 4 sẽ mạnh hơn nhiều nếu có bằng chứng trực quan thay vì chỉ mô tả chữ.

---

## 7. Cách ghép Chương 5

### 7.1. Nguồn chính

Chương 5 nên lấy từ:

- phần Chương 5 trong `docs/thesis_report_revised_sections.md`
- phần bổ sung kiểm tra chất lượng trong `docs/thesis_report_revised_sections.md`
- chọn lọc thêm từ `docs/thesis_report_alt_sections.md` nếu cần mở rộng đánh giá thực tiễn, bài học rút ra, định hướng nghiên cứu

### 7.2. Không nên làm

Không nên dán nguyên toàn bộ `thesis_report_alt_sections.md` vào Chương 5 vì sẽ dễ lặp với các đoạn đánh giá và kết luận đã có.

### 7.3. Nên thêm gì

Nên có:

- bảng kịch bản kiểm tra chất lượng tiêu biểu
- bảng nhóm suite test
- phần đánh giá kết quả đạt được
- phần hạn chế
- phần hướng phát triển

Repo hiện có `46` file kiểm tra, nên khi viết bằng chứng kiểm tra chất lượng có thể nêu điều này như một chỉ dấu về mức độ đầu tư cho chất lượng.

---

## 8. Những phần còn thiếu bắt buộc phải bổ sung vào bản Word

### 8.1. Ảnh chụp giao diện thật

Ít nhất nên có các ảnh sau:

- trang chủ storefront
- danh mục hoặc tìm kiếm sản phẩm
- trang chi tiết sản phẩm
- giỏ hàng
- checkout
- dashboard admin
- quản lý sản phẩm
- báo cáo hoặc statistics

### 8.2. Bảng phụ lục kỹ thuật

Nên thêm hai phụ lục ngắn:

- `Phụ lục A. Bảng module -> route -> controller -> service`
- `Phụ lục B. Bảng legacy table -> schema mới`

### 8.3. Thông tin học thuật đầu bản

Phải điền đủ:

- tên đề tài
- họ tên sinh viên
- mã sinh viên
- lớp
- khoa hoặc viện
- trường
- giảng viên hướng dẫn
- địa danh và năm

---

## 9. Những phần có thể giữ nguyên từ bản Word hiện tại

Các phần sau về cơ bản có thể giữ làm nền, chỉ cần chỉnh câu chữ và cập nhật thông tin:

- lời cảm ơn
- lời cam đoan
- lời mở đầu
- tóm tắt đồ án
- chương 1 khung tổng quan
- danh mục từ viết tắt

---

## 10. Những phần cần thay hoặc làm lại

### 10.1. Cần thay

- phần use case cũ bằng bộ `thesis_report_usecase_plantuml_revised_guest.md`
- phần Chương 2 cũ bằng bản ghép mới từ hai file lý thuyết
- phần danh mục hình cũ bằng numbering mới sau khi chèn lại sơ đồ

### 10.2. Cần làm lại

- mục lục tự động
- danh mục hình
- danh mục bảng
- caption ảnh và caption sơ đồ

---

## 11. Trình tự làm thực tế trong Word

Nên làm theo đúng thứ tự dưới đây:

1. Mở bản Word hiện tại và lưu thành một bản làm việc mới.
2. Điền toàn bộ thông tin cá nhân và thông tin trường lớp.
3. Giữ nguyên phần đầu báo cáo và Chương 1, chỉ chỉnh câu chữ nếu cần.
4. Thay Chương 2 bằng nội dung ghép từ:
   - `thesis_report_theoretical_foundation.md`
   - `thesis_report_theory_expansion_web_tech_and_reference_sites.md`
5. Thay hoặc chỉnh Chương 3 bằng:
   - `thesis_report_revised_sections.md`
   - `thesis_report_analysis_design_additions.md`
   - `thesis_report_usecase_plantuml_revised_guest.md`
6. Chèn hình và ảnh chụp giao diện thật.
7. Giữ Chương 4 và Chương 5 từ `thesis_report_revised_sections.md`, bổ sung chọn lọc nếu cần.
8. Cập nhật lại số hình, số bảng, mục lục.
9. Rà lỗi chính tả, lỗi font, lỗi giãn cách.
10. Xuất bản PDF để kiểm tra lần cuối trước khi nộp.

---

## 12. Checklist chốt cuối trước khi nộp

- Đã thay hết placeholder thông tin cá nhân và trường lớp
- Chương 2 đã ghép thêm phần HTML, CSS, JS, hình ảnh, khách vãng lai, website tham chiếu
- Chương 3 đã dùng bộ use case mới có khách vãng lai
- Đã renumber lại toàn bộ hình Chương 3
- Đã cập nhật danh mục hình và danh mục bảng
- Đã chèn ảnh chụp giao diện thật
- Đã giữ mô tả bám repo ở các phần route, controller, service, schema
- Đã rà lại mục lục tự động
- Đã đọc thử toàn bộ PDF xuất ra

---

## 13. Kết luận

Nếu ghép theo kế hoạch này, bản báo cáo cuối sẽ sát repo hơn rõ rệt so với file Word hiện tại, đồng thời giảm nguy cơ trùng lặp giữa các file markdown. Điểm quan trọng nhất là phải coi `thesis_report_revised_sections.md` là trục chính, sau đó chỉ bổ sung có chọn lọc từ các file mở rộng thay vì ghép tùy hứng.

Nói ngắn gọn, bộ tài liệu hiện tại đã đủ để ra một bản báo cáo mạnh. Việc còn lại là ghép đúng nguồn, đánh số lại cho sạch và thêm bằng chứng hình ảnh thật của hệ thống.
