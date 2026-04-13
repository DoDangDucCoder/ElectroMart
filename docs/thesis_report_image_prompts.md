# BỘ PROMPT TẠO ẢNH CHO ĐỒ ÁN VÀ DỰ ÁN

File này tổng hợp prompt cho các hình ảnh dễ bị tạo sai khi dùng công cụ AI. Prompt được viết theo giao diện thật trong repo hiện tại, bám các view của storefront, checkout, admin dashboard, report và chat inbox.

Mục tiêu của file:

- giúp anh tạo lại các ảnh gần đúng với giao diện hiện tại của dự án
- tránh việc công cụ tạo ảnh sinh ra UI sai lĩnh vực hoặc sai bố cục
- tách rõ ảnh nào nên tạo bằng prompt và ảnh nào phải chụp thật hoặc xuất trực tiếp từ sơ đồ

## Cách dùng nhanh

- Nếu công cụ tạo ảnh hỗ trợ `prompt` và `negative prompt`, hãy dùng cả hai.
- Nếu công cụ không hỗ trợ `negative prompt`, hãy ghép phần cấm trực tiếp vào cuối prompt chính bằng câu “avoid ...”.
- Nên chọn tỉ lệ ảnh `16:9` hoặc `1440x900` cho ảnh chụp giao diện desktop.
- Với các ảnh báo cáo kỹ thuật như `use case`, `sequence`, `activity`, `ERD`, `deployment`, không nên dùng AI; nên xuất trực tiếp từ `PlantUML` hoặc `Mermaid`.

## Phong cách chung cho toàn bộ ảnh giao diện

### Prompt nền dùng chung

`realistic desktop browser screenshot of a Vietnamese e-commerce website for laptops and technology devices, clean light theme, white background, soft gray surfaces, subtle blue and warm orange accents, Laravel Blade style, Bootstrap-like layout, realistic spacing, Vietnamese UI text, practical business interface, not futuristic, not dribbble mockup, high fidelity, full page screenshot`

### Negative prompt dùng chung

`no purple neon, no dark mode, no fantasy UI, no mobile app layout, no fashion store, no beauty products, no gaming sci-fi interface, no English-only text, no fake gibberish text, no oversimplified landing page, no floating 3D elements, no glassmorphism overload, no dashboard-only layout unless specified`

## 1. Ảnh Storefront

### 1.1. Trang chủ storefront

Mục đích:
- dùng cho phần giới thiệu giao diện khách hàng
- nên chèn ở Chương 4

Prompt:

`realistic full browser screenshot of the homepage of a Vietnamese technology e-commerce website named ShopW, compact topbar with a small brand badge and the text "Ưu đãi chọn lọc mỗi ngày", topbar pills "Giao nhanh nội thành" and "Bảo hành minh bạch", large header with logo, search bar, account area and cart icon, home page with category tabs, campaign board, flash sale block, "Sản phẩm mới nhất" section, grid of laptop and tech product cards with prices in VND, clean Bootstrap-like Vietnamese layout, realistic ecommerce homepage, desktop 1440x900`

Negative prompt:

`no fashion products, no phone-only homepage, no empty hero-only page, no futuristic marketplace, no purple dark theme`

### 1.2. Thanh tìm kiếm có gợi ý động

Mục đích:
- dùng cho phần tìm kiếm và trải nghiệm người dùng

Prompt:

`realistic browser screenshot focused on the header search bar of a Vietnamese technology e-commerce website, user is typing a keyword for a laptop product, dropdown autocomplete panel is open below the search input, showing Vietnamese search suggestions, trending keywords, quick links, product thumbnail suggestions, clean white dropdown, compact header, logo on the left, account and cart on the right, realistic Laravel Blade storefront, desktop view`

Negative prompt:

`no giant search engine page, no mobile UI, no English-only suggestions, no marketplace clutter, no black background`

### 1.3. Trang chi tiết sản phẩm

Mục đích:
- dùng cho phần trình bày sản phẩm và tương tác với khách hàng

Prompt:

`realistic browser screenshot of a Vietnamese product detail page for a laptop on a technology e-commerce website, breadcrumb at top, large image gallery on the left, product title and stock badges on the right, star rating, price panel in VND, quantity input, add to cart button, support service cards with icons, product attributes or versions shown as selectable cards, brand and category metadata, long-form specification and description layout below, elegant light theme, practical ecommerce product page, desktop 1440x900`

Negative prompt:

`no single-column mobile layout, no phone store only, no unrealistic huge hero banner, no dark cyberpunk style`

### 1.4. Trang giỏ hàng

Mục đích:
- dùng cho phần giỏ hàng và coupon

Prompt:

`realistic browser screenshot of a Vietnamese shopping cart page for a Laravel e-commerce website, breadcrumb at top, table layout listing product image, product name, attribute, price, quantity, subtotal, delete icon, coupon form below, buttons "Cập nhật giỏ hàng", "Xóa tất cả", "Đăng nhập để thanh toán" or "Đặt hàng", cart totals panel showing subtotal, discount, shipping fee and total in VND, clean white Bootstrap-like cart page, technology products such as laptops and accessories`

Negative prompt:

`no mini-cart popup only, no checkout form, no fashion items, no futuristic one-page checkout`

### 1.5. Trang đăng nhập trước checkout

Mục đích:
- dùng cho phần xác thực khách hàng

Prompt:

`realistic desktop screenshot of a Vietnamese login and register page connected to checkout flow, ecommerce website for technology products, top hero with title about continuing checkout, progress steps showing "Giỏ hàng", "Đăng nhập", "Thanh toán", summary cards for cart quantity and subtotal, two main cards below: left login form, right registration form, support information cards with lock, user-plus and hotline icons, light professional layout, Vietnamese text, Laravel Blade storefront page`

Negative prompt:

`no generic social network login page, no dark login screen, no banking app, no phone-only screen`

### 1.6. Trang checkout

Mục đích:
- dùng cho phần thanh toán và giao hàng

Prompt:

`realistic browser screenshot of a Vietnamese checkout page for a technology e-commerce website, breadcrumb at top, hero section with payment flow steps "Giỏ hàng", "Thanh toán", "Hoàn tất", shipping information form, account card showing logged-in customer, support cards, location selectors for city province ward, shipping fee calculation section, payment method dropdown, order summary in VND, clean white layout, professional practical ecommerce checkout, desktop view`

Negative prompt:

`no payment gateway full-screen page, no mobile-only checkout, no dark theme, no minimal single button page`

### 1.7. Trang lịch sử đơn hàng

Mục đích:
- dùng cho phần theo dõi đơn hàng sau mua

Prompt:

`realistic desktop browser screenshot of a Vietnamese order history page on a technology e-commerce website, hero section with order statistics, support quick links, list of order cards with order code, status badges, payment label, total order amount in VND, buttons to view details and cancel order, clean white and light gray interface, modern but realistic Laravel storefront page`

Negative prompt:

`no admin order table, no empty dashboard, no futuristic cards, no dark UI`

## 2. Ảnh Admin

### 2.1. Dashboard quản trị

Mục đích:
- dùng cho phần giới thiệu khu vực admin

Prompt:

`realistic desktop admin dashboard screenshot of a Vietnamese Laravel e-commerce management panel, page title "Tổng quan vận hành", hero section with two summary stats, grid of KPI cards for visitors, orders and chat inbox, line chart for revenue and orders, donut chart for data structure, summary cards for products posts orders videos customers, top viewed posts and top viewed products lists, clean admin shell, white cards, soft gray background, blue accent buttons, practical enterprise dashboard, Vietnamese text`

Negative prompt:

`no cryptocurrency dashboard, no fintech dashboard, no dark theme, no generic analytics only, no English labels`

### 2.2. Danh sách sản phẩm trong admin

Mục đích:
- dùng cho phần quản lý catalog

Prompt:

`realistic browser screenshot of a Vietnamese admin product management page, hero section with total products and products on current page, a large striped data table with columns for product name, gallery management, attributes, document, quantity, slug, cost price, base price, sale, final price, profit per unit, image thumbnail, category, brand, display status, edit and delete actions, add product button in the top right, practical Laravel admin UI, desktop widescreen`

Negative prompt:

`no storefront product grid, no minimal SaaS card list, no dark admin panel, no fashion inventory table`

### 2.3. Quản lý đơn hàng trong admin

Mục đích:
- dùng cho phần vận hành đơn hàng

Prompt:

`realistic admin order management screenshot for a Vietnamese e-commerce website, hero section titled "Quản lý đơn hàng", KPI cards for new orders delivered orders cancelled orders, large table with order code, created date, status chips, cancel reason, actions to view and delete, pagination footer, clean white admin cards, practical order operations interface, Vietnamese text, Laravel admin layout`

Negative prompt:

`no logistics map dashboard, no bank transaction table, no POS cashier screen`

### 2.4. Báo cáo product discovery

Mục đích:
- dùng cho phần báo cáo và thống kê

Prompt:

`realistic admin analytics report screenshot for a Vietnamese technology e-commerce website, page title about search and product recommendation report, date filter toolbar, summary cards for search count and recommendation impressions, top keyword list, top filtered categories list, recommendation context list, recommended products table with category brand impression count and last impression time, clean report layout in Vietnamese, enterprise Laravel admin panel`

Negative prompt:

`no stock market dashboard, no social media analytics, no dark data visualization, no English-only report`

### 2.5. Hộp thư chat admin

Mục đích:
- dùng cho phần chatbot và hỗ trợ

Prompt:

`realistic admin chat inbox screenshot for a Vietnamese e-commerce support system, page title "Hộp thư chatbot", summary stats for total conversations customer guest unread resolved, filter toolbar, left sidebar with conversation list and status chips, right panel with chat thread messages from customer chatbot and admin, reply form at bottom, clean modern support desk UI, white cards and gray background, Vietnamese text, Laravel admin layout`

Negative prompt:

`no consumer messenger app, no WhatsApp clone, no neon cyberpunk chat, no mobile app layout`

## 3. Ảnh Bằng Chứng Kiểm Thử

### 3.1. Danh sách file test trong IDE

Khuyến nghị:
- tốt nhất chụp thật từ IDE
- chỉ dùng prompt này nếu bắt buộc phải dựng lại ảnh minh họa

Prompt:

`realistic Visual Studio Code screenshot on Windows showing a Laravel project explorer, expanded tests/Feature folder with many test files such as HomepageTest.php, CartFlowTest.php, CheckoutFlowTest.php, AdminAuthFlowTest.php, AdminProductCrudTest.php, ProductDiscoveryReportTest.php, ChatbotFeatureTest.php, clean developer workspace, code editor on the right, realistic file tree, not stylized`

Negative prompt:

`no fake hacker screen, no terminal only, no macOS UI if Windows layout is requested, no blurry unreadable file names`

### 3.2. Terminal chạy php artisan test

Khuyến nghị:
- tốt nhất chạy thật rồi chụp ảnh thật
- prompt này là phương án dự phòng

Prompt:

`realistic Windows terminal or PowerShell screenshot running "php artisan test" inside a Laravel project, many passing test lines in green, references to storefront admin api and report tests, clear command line output, believable developer environment, no futuristic effects, no fake matrix background, high readability`

Negative prompt:

`no Linux hacker terminal aesthetic, no red failing output if the intention is a successful run, no unreadable tiny text`

### 3.3. Ảnh giao diện liên hệ giữa luồng kiểm thử và màn hình thật

Mục đích:
- dùng ở Chương 5 khi muốn minh họa một test bằng màn hình tương ứng

Prompt:

`realistic split-style thesis illustration showing on the left a browser screenshot of a Vietnamese Laravel e-commerce page and on the right a small code editor or terminal snippet related to its test, academic software testing evidence style, clean light theme, practical not artistic, suitable for a graduation thesis report`

Negative prompt:

`no infographic cartoon style, no giant arrows everywhere, no futuristic HUD, no dark poster look`

## 4. Ảnh Nên Chụp Thật Hoặc Xuất Trực Tiếp, Không Nên Dùng AI

Những ảnh sau nếu dùng AI sẽ rất dễ sai:

- use case diagram
- activity diagram
- sequence diagram
- ERD
- deployment diagram
- kiến trúc tổng thể
- cây thư mục mã nguồn
- terminal test output chính xác
- ảnh code có tên file cụ thể

Khuyến nghị cho các ảnh này:

- dùng file PlantUML hoặc Mermaid đã có trong `docs/`
- xuất hình trực tiếp từ sơ đồ
- hoặc chụp thật từ IDE / terminal / browser

## 5. Danh sách prompt khuyến nghị theo thứ tự chèn vào đồ án

1. Trang chủ storefront
2. Thanh tìm kiếm có gợi ý động
3. Trang chi tiết sản phẩm
4. Trang giỏ hàng
5. Trang đăng nhập trước checkout
6. Trang checkout
7. Trang lịch sử đơn hàng
8. Dashboard quản trị
9. Danh sách sản phẩm admin
10. Quản lý đơn hàng admin
11. Báo cáo product discovery
12. Hộp thư chat admin
13. Danh sách file test trong IDE
14. Terminal chạy `php artisan test`

## 6. Mẹo để ảnh ra đúng hơn

- Luôn ghi rõ đây là `Vietnamese technology e-commerce website`, nếu không AI hay biến thành fashion shop hoặc phone-only shop.
- Luôn yêu cầu `desktop browser screenshot`, nếu không AI hay sinh kiểu mobile app.
- Luôn yêu cầu `Vietnamese UI text`, nếu không ảnh sẽ ra chữ giả hoặc tiếng Anh.
- Với admin, luôn thêm các từ khóa `Laravel admin panel`, `white cards`, `data table`, `report toolbar`, `KPI cards`.
- Với storefront, luôn thêm các từ khóa `light ecommerce layout`, `price in VND`, `laptop and tech products`, `search bar`, `cart`, `checkout`.
- Không nên yêu cầu “beautiful modern UI” quá mạnh; nên yêu cầu `practical`, `realistic`, `business interface`, nếu không ảnh sẽ bị quá giống mockup Dribbble.
