# Bộ sơ đồ phân tích hệ thống cho báo cáo ĐATN

Tài liệu này tổng hợp các sơ đồ phục vụ báo cáo Đồ án tốt nghiệp cho hệ thống website bán hàng thiết bị công nghệ bằng `Laravel 11`. Nội dung được xây dựng sau khi kiểm tra `DOCUMENTATION_TEMPLATE.md`, `routes/web.php`, `routes/web/frontend.php`, `routes/web/admin.php`, `routes/api.php`, `app/Http/Controllers`, `app/Models`, `app/Application`, `app/Infrastructure/Persistence/Eloquent`, `database/migrations` và `resources/views`.

Các sơ đồ bên dưới chỉ mô tả chức năng có cơ sở trong mã nguồn hiện tại. Những phần chưa xác định được từ code được ghi chú rõ, không tự suy diễn là đã hoàn chỉnh.

## 1. Use Case Diagram tổng quát

### Mục đích

Sơ đồ use case tổng quát mô tả các actor chính và phạm vi chức năng lớn của hệ thống. Các use case được xác định từ route storefront trong `routes/web/frontend.php`, route admin trong `routes/web/admin.php` và API trong `routes/api.php`.

### Code Mermaid

```mermaid
flowchart LR
    Guest["Khách vãng lai"]
    Customer["Khách hàng"]
    Admin["Quản trị viên"]
    ApiClient["API client"]
    Payment["Cổng thanh toán / mock"]

    subgraph Storefront["Storefront"]
        UC01["Xem trang chủ"]
        UC02["Tìm kiếm / autocomplete"]
        UC03["Duyệt danh mục / thương hiệu"]
        UC04["Xem chi tiết sản phẩm"]
        UC05["Bình luận / đánh giá"]
        UC06["So sánh / yêu thích"]
        UC07["Quản lý giỏ hàng"]
        UC08["Áp dụng coupon"]
        UC09["Checkout / đặt hàng"]
        UC10["Xem lịch sử đơn hàng"]
        UC11["Hủy đơn hàng"]
        UC12["Quản lý tài khoản"]
        UC13["Xem blog / video / liên hệ"]
        UC14["Chatbot"]
    end

    subgraph AdminArea["Admin"]
        AD01["Đăng nhập / reset mật khẩu"]
        AD02["Dashboard / thống kê"]
        AD03["Quản lý sản phẩm"]
        AD04["Quản lý danh mục / thương hiệu"]
        AD05["Quản lý thuộc tính / gallery / bình luận"]
        AD06["Quản lý đơn hàng / in đơn"]
        AD07["Quản lý coupon"]
        AD08["Quản lý phí giao hàng"]
        AD09["Quản lý nội dung / video / slider"]
        AD10["Quản lý user / role / permission"]
        AD11["Quản lý tài liệu"]
        AD12["Báo cáo / chat inbox"]
    end

    subgraph API["API v1"]
        API01["Public catalog/content API"]
        API02["Protected cart/order/review API"]
        API03["Admin API"]
    end

    Guest --> UC01
    Guest --> UC02
    Guest --> UC03
    Guest --> UC04
    Guest --> UC06
    Guest --> UC07
    Guest --> UC08
    Guest --> UC09
    Guest --> UC13
    Guest --> UC14

    Customer --> UC05
    Customer --> UC09
    Customer --> UC10
    Customer --> UC11
    Customer --> UC12

    Admin --> AD01
    Admin --> AD02
    Admin --> AD03
    Admin --> AD04
    Admin --> AD05
    Admin --> AD06
    Admin --> AD07
    Admin --> AD08
    Admin --> AD09
    Admin --> AD10
    Admin --> AD11
    Admin --> AD12

    ApiClient --> API01
    ApiClient --> API02
    ApiClient --> API03

    UC09 --> Payment
```

### Giải thích thành phần chính

`Khách vãng lai` có thể xem trang chủ, tìm kiếm, xem sản phẩm, thêm giỏ hàng và checkout theo luồng guest. `Khách hàng` là người có tài khoản trong bảng `customers`, có thể xem lịch sử đơn hàng, hủy đơn khi hợp lệ và quản lý tài khoản. `Quản trị viên` sử dụng khu vực admin được bảo vệ bởi `auth:web`, `admin.access` và `admin.permission`. `API client` đại diện cho client gọi các route trong `routes/api.php`, gồm public API, protected API qua `auth:sanctum` và admin API.

### Ghi chú chưa xác định từ code

Tích hợp thanh toán thật với `MoMo` và `VNPAY` chưa xác định từ code; trong `CheckoutController`, các phương thức `momo_payment` và `vnpay_payment` trả thông báo cổng đang tạm tắt, còn `VnPayMockController` là luồng mock.

## 2. Activity Diagram cho luồng mua hàng

### Mục đích

Sơ đồ mô tả luồng nghiệp vụ mua hàng từ lúc người dùng duyệt sản phẩm đến khi hệ thống tạo đơn hàng. Luồng dựa trên `HomeController`, `ProductController`, `CartController`, `CheckoutController`, `CartService`, `CouponService`, `ShippingService` và `CheckoutService`.

### Code Mermaid

```mermaid
flowchart TD
    Start([Bắt đầu])
    Home["Xem trang chủ / danh sách sản phẩm"]
    Search["Tìm kiếm hoặc chọn danh mục / thương hiệu"]
    Detail["Xem chi tiết sản phẩm"]
    Choose["Chọn số lượng / thuộc tính nếu có"]
    AddCart["Gửi request thêm giỏ hàng: POST /add-cart-ajax hoặc POST /save-cart"]
    ValidateCart{"Sản phẩm, thuộc tính và tồn kho hợp lệ?"}
    CartSession["Lưu item vào session cart"]
    ViewCart["Xem giỏ hàng: GET /gio-hang"]
    CouponDecision{"Người dùng nhập coupon?"}
    ApplyCoupon["Kiểm tra coupon: POST /check-coupon"]
    CouponValid{"Coupon hợp lệ?"}
    Checkout["Vào checkout: GET /checkout"]
    CartEmpty{"Giỏ hàng trống?"}
    Shipping["Nhập địa chỉ, chọn tỉnh/quận/xã, tính phí vận chuyển"]
    Payment["Chọn phương thức thanh toán: GET /payment"]
    Confirm["Xác nhận đơn hàng: POST /confirm-order"]
    ValidOrder{"Dữ liệu checkout hợp lệ?"}
    Transaction["DB transaction tạo orders, order_items, order_shipping, payments"]
    ClearSession["Xóa cart, coupon, shipping fee khỏi session"]
    Success["Trả order_code / hiển thị kết quả"]
    Error["Hiển thị lỗi / redirect về bước phù hợp"]
    End([Kết thúc])

    Start --> Home --> Search --> Detail --> Choose --> AddCart
    AddCart --> ValidateCart
    ValidateCart -- Không --> Error --> End
    ValidateCart -- Có --> CartSession --> ViewCart
    ViewCart --> CouponDecision
    CouponDecision -- Có --> ApplyCoupon --> CouponValid
    CouponValid -- Không --> Error
    CouponValid -- Có --> Checkout
    CouponDecision -- Không --> Checkout
    Checkout --> CartEmpty
    CartEmpty -- Có --> Error
    CartEmpty -- Không --> Shipping --> Payment --> Confirm --> ValidOrder
    ValidOrder -- Không --> Error
    ValidOrder -- Có --> Transaction --> ClearSession --> Success --> End
```

### Giải thích thành phần chính

Giỏ hàng storefront được lưu trong session key `cart` bởi `CartService::SESSION_KEY`. Khi thêm sản phẩm, `CartService@addItem` tìm sản phẩm bằng `ProductService`, kiểm tra thuộc tính nếu có, kiểm tra tồn kho và lưu snapshot giá vào session. Khi đặt hàng, `CheckoutController@confirm_order` gọi `CheckoutService@confirmOrder`; service này dùng `DB::transaction` để tạo `orders`, `order_items`, `order_shipping`, `payments` và ghi nhận coupon redemption nếu có.

### Ghi chú chưa xác định từ code

Luồng trừ tồn kho sau khi tạo đơn hàng chưa xác định rõ từ code đã inspect. Code có kiểm tra tồn kho khi thêm vào giỏ hàng nhưng chưa thấy bước giảm `stock_quantity` trong `CheckoutService@confirmOrder`.

## 3. Activity Diagram cho luồng quản trị sản phẩm/đơn hàng

### Mục đích

Sơ đồ mô tả luồng tổng quát khi quản trị viên đăng nhập, được kiểm tra quyền và thực hiện quản trị sản phẩm hoặc đơn hàng. Luồng dựa trên `routes/web/admin.php`, `AuthController`, `ProductController`, `OrderController`, `AdminPermissions` và các middleware `auth:web`, `admin.access`, `admin.permission`.

### Code Mermaid

```mermaid
flowchart TD
    Start([Bắt đầu])
    LoginPage["Truy cập /admin"]
    SubmitLogin["Gửi form đăng nhập: POST /auth-login"]
    AuthValid{"Email/password hợp lệ?"}
    AdminAccess{"Có quyền truy cập admin?"}
    Dashboard["Vào dashboard: GET /dashboard"]
    SelectTask{"Chọn nghiệp vụ"}

    ProductPerm{"Có quyền manage products?"}
    ProductList["Xem danh sách sản phẩm: GET /admin/products-management"]
    ProductForm["Thêm/sửa sản phẩm: /add-product hoặc /edit-product/{product_id}"]
    ProductValidate{"Dữ liệu sản phẩm hợp lệ?"}
    SaveProduct["Lưu sản phẩm: POST /save-product hoặc /update-product/{product_id}"]
    ProductStatus["Bật/tắt/xóa sản phẩm"]

    OrderPerm{"Có quyền manage orders?"}
    OrderList["Xem danh sách đơn: GET /admin/orders-management"]
    OrderDetail["Xem chi tiết đơn: GET /view-order/{order_code}"]
    OrderUpdate["Cập nhật trạng thái/số lượng: POST /update-order-qty hoặc /update-qty"]
    OrderPrint["In đơn: GET /print-order/{checkout_code}"]

    Denied["Từ chối truy cập / báo lỗi"]
    Result["Hiển thị thông báo kết quả"]
    End([Kết thúc])

    Start --> LoginPage --> SubmitLogin --> AuthValid
    AuthValid -- Không --> Denied --> End
    AuthValid -- Có --> AdminAccess
    AdminAccess -- Không --> Denied
    AdminAccess -- Có --> Dashboard --> SelectTask

    SelectTask -- "Quản lý sản phẩm" --> ProductPerm
    ProductPerm -- Không --> Denied
    ProductPerm -- Có --> ProductList --> ProductForm --> ProductValidate
    ProductValidate -- Không --> Result --> ProductForm
    ProductValidate -- Có --> SaveProduct --> Result --> Dashboard
    ProductList --> ProductStatus --> Result

    SelectTask -- "Quản lý đơn hàng" --> OrderPerm
    OrderPerm -- Không --> Denied
    OrderPerm -- Có --> OrderList --> OrderDetail --> OrderUpdate --> Result --> Dashboard
    OrderDetail --> OrderPrint --> Result
```

### Giải thích thành phần chính

Admin login được xử lý bởi `AuthController@login`. Sau khi đăng nhập, hệ thống kiểm tra quyền truy cập admin qua `AdminAccess`. Nhóm quản lý sản phẩm trong `routes/web/admin.php` nằm dưới middleware `admin.permission:manage products`. Nhóm quản lý đơn hàng nằm dưới middleware `admin.permission:manage orders`. Quản lý sản phẩm dùng `ProductController` và `ProductService`; quản lý đơn hàng dùng `OrderController` và một số service legacy như `LegacyOrderWorkflowService`, `LegacyOrderPresentationService`.

### Ghi chú chưa xác định từ code

Phần admin order hiện vẫn có dấu hiệu dùng bảng/service legacy qua `App\Models\Order` và `LegacyOrderWorkflowService`. Việc admin order đã chuyển hoàn toàn sang bảng chuẩn hóa `orders` chưa xác định từ code.

## 4. Sequence Diagram cho luồng thêm vào giỏ hàng và đặt hàng

### Mục đích

Sơ đồ mô tả tuần tự tương tác giữa người dùng, trình duyệt, route, controller, request validation, service, session và database trong hai luồng: thêm sản phẩm vào giỏ hàng và xác nhận đơn hàng.

### Code Mermaid

```mermaid
sequenceDiagram
    autonumber
    actor User as Người dùng
    participant Browser as Trình duyệt
    participant CartRoute as POST /add-cart-ajax
    participant AddRequest as AddCartAjaxRequest
    participant CartController as CartController
    participant CartService as CartService
    participant ProductService as ProductService
    participant Session as Session cart
    participant CheckoutRoute as POST /confirm-order
    participant ConfirmRequest as ConfirmOrderRequest
    participant CheckoutController as CheckoutController
    participant CheckoutService as CheckoutService
    participant CouponService as CouponService
    participant ShippingService as ShippingService
    participant DB as MySQL Database

    User->>Browser: Chọn sản phẩm và số lượng
    Browser->>CartRoute: Gửi id_product, cart_product_qty, id_attribute
    CartRoute->>AddRequest: Validate dữ liệu
    AddRequest-->>CartController: Dữ liệu hợp lệ
    CartController->>CartService: addItem(productId, quantity, attributeId)
    CartService->>ProductService: findProductById(productId)
    ProductService-->>CartService: Product hoặc null
    alt Sản phẩm không hợp lệ / hết hàng
        CartService-->>CartController: RuntimeException
        CartController-->>Browser: JSON lỗi 422
    else Sản phẩm hợp lệ
        CartService->>Session: Lưu/cập nhật key cart
        Session-->>CartService: Đã lưu
        CartService-->>CartController: Cart item
        CartController-->>Browser: JSON cart_count, cart_items, cart_totals
    end

    User->>Browser: Nhập thông tin checkout và xác nhận
    Browser->>CheckoutRoute: Gửi thông tin giao hàng, thanh toán
    CheckoutRoute->>ConfirmRequest: Validate dữ liệu
    ConfirmRequest-->>CheckoutController: Dữ liệu hợp lệ
    CheckoutController->>CheckoutController: Lấy customer_id từ session nếu có
    CheckoutController->>CheckoutService: confirmOrder(payload, customerId)
    CheckoutService->>CartService: items()
    CartService-->>CheckoutService: Danh sách sản phẩm trong session
    alt Giỏ hàng trống
        CheckoutService-->>CheckoutController: RuntimeException
        CheckoutController-->>Browser: JSON lỗi 422
    else Giỏ hàng có sản phẩm
        CheckoutService->>DB: Bắt đầu transaction
        CheckoutService->>ShippingService: resolve/calculate shipping fee
        CheckoutService->>CouponService: validatedCurrent(subtotal, customerId)
        CheckoutService->>DB: Tạo customer_addresses nếu có customer
        CheckoutService->>DB: Tạo orders
        CheckoutService->>DB: Tạo order_items
        CheckoutService->>DB: Tạo order_shipping
        CheckoutService->>DB: Tạo payments
        CheckoutService->>CouponService: redeem coupon nếu có
        CheckoutService->>CartService: clear()
        CheckoutService->>CouponService: clear()
        CheckoutService->>ShippingService: clearFee()
        CheckoutService->>DB: Commit transaction
        CheckoutService-->>CheckoutController: Order
        CheckoutController-->>Browser: JSON message và order_code
    end
```

### Giải thích thành phần chính

Đoạn đầu của sequence diagram phản ánh `CartController@add_cart_ajax` và `CartService@addItem`. Đoạn sau phản ánh `CheckoutController@confirm_order` và `CheckoutService@confirmOrder`. Việc tạo đơn hàng được thực hiện trong transaction để các bảng `orders`, `order_items`, `order_shipping` và `payments` được ghi nhất quán.

### Ghi chú chưa xác định từ code

Tự động xác nhận thanh toán ngân hàng hoặc callback thanh toán thật từ cổng ngoài chưa xác định từ code.

## 5. Class Diagram mức phân tích

### Mục đích

Sơ đồ lớp mức phân tích mô tả các controller, service và model/entity chính tham gia vào các luồng catalog, giỏ hàng, checkout, đơn hàng và quản trị sản phẩm.

### Code Mermaid

```mermaid
classDiagram
    class HomeController {
        +index()
        +search()
        +products()
        +autocomplete_ajax()
    }

    class ProductController {
        +details_product(product_slug)
        +quickview()
        +send_comment()
        +insert_rating()
        +add_product()
        +save_product()
        +update_product()
        +delete_product()
    }

    class CartController {
        +add_cart_ajax()
        +gio_hang()
        +check_coupon()
        +update_cart()
        +delete_product()
        +show_quick_cart()
    }

    class CheckoutController {
        +checkout()
        +payment()
        +calculate_fee()
        +select_delivery_home()
        +confirm_order()
        +bankTransferInstructions()
    }

    class OrderController {
        +history()
        +view_history_order()
        +huy_don_hang()
        +manage_order()
        +view_order()
        +print_order()
    }

    class CartService {
        +items()
        +addItem(productId, quantity, attributeId)
        +updateQuantity(sessionId, quantity)
        +subtotal()
        +totals(coupon, shippingFee)
        +clear()
    }

    class CheckoutService {
        +confirmOrder(payload, customerId)
        +createOrder(customerId, addressId, paymentMethod)
        -createOrderRecord()
        -createOrderItems()
        -createOrderShipping()
        -createOrderPayment()
    }

    class ProductService {
        +findProductById(id)
        +findProductBySlug(slug)
        +paginateForAdmin(filters, perPage)
        +createProduct(payload, image, document)
        +updateProduct(product, payload, image, document)
        +getRelatedProducts(product, limit)
    }

    class CouponService {
        +apply(code, subtotal, customerId)
        +validatedCurrent(subtotal, customerId)
        +validateCoupon(code, totalAmount, customerId)
        +redeem(coupon, customerId, orderId, discount)
        +clear()
    }

    class ShippingService {
        +calculateFee(cityId, provinceId, wardId)
        +storeFee(fee)
        +getFee()
        +clearFee()
    }

    class Product {
        +id
        +name
        +slug
        +price
        +sale_price
        +stock_quantity
        +brand()
        +primaryCategory()
        +categories()
        +variants()
        +media()
    }

    class Customer {
        +id
        +name
        +email
        +phone
        +addresses()
        +orders()
        +reviews()
    }

    class Order {
        +id
        +code
        +customer_id
        +status
        +payment_status
        +total
        +customer()
        +items()
        +shipping()
        +payments()
    }

    class OrderItem {
        +id
        +order_id
        +product_id
        +product_name
        +quantity
        +subtotal
    }

    class Coupon {
        +id
        +code
        +type
        +value
        +redemptions()
    }

    HomeController --> ProductService
    ProductController --> ProductService
    ProductController --> Product
    CartController --> CartService
    CartController --> CouponService
    CartController --> ShippingService
    CheckoutController --> CartService
    CheckoutController --> CheckoutService
    CheckoutController --> CouponService
    CheckoutController --> ShippingService
    OrderController --> Order
    CartService --> ProductService
    CheckoutService --> CartService
    CheckoutService --> CouponService
    CheckoutService --> ShippingService
    CheckoutService --> Order
    Product "1" --> "0..*" OrderItem
    Customer "1" --> "0..*" Order
    Order "1" --> "1..*" OrderItem
    Coupon "1" --> "0..*" Order
```

### Giải thích thành phần chính

Các controller nhận request từ route và điều phối sang service/model. `CartService` xử lý nghiệp vụ giỏ hàng session. `CheckoutService` xử lý đặt hàng và ghi dữ liệu trong transaction. `ProductService` xử lý truy vấn và quản trị sản phẩm. Các model/entity chính gồm `Product`, `Customer`, `Order`, `OrderItem` và `Coupon`, tương ứng với các bảng chuẩn hóa trong migration.

### Ghi chú chưa xác định từ code

Repo có cả model legacy trong `app/Models` và model chuẩn hóa trong `app/Infrastructure/Persistence/Eloquent`. Sơ đồ này ưu tiên lớp chuẩn hóa đang được dùng trong `CartService`, `CheckoutService`, `ProductController` và `OrderController`; một số luồng admin order vẫn còn dùng service/model legacy.

## 6. ERD dựa trên migration/model hiện có

### Mục đích

Sơ đồ ERD mô tả các thực thể dữ liệu chính và quan hệ giữa chúng, dựa trên `database/migrations` và các quan hệ trong model Eloquent như `Product`, `Customer`, `Order`.

### Code Mermaid

```mermaid
erDiagram
    brands ||--o{ products : has
    product_categories ||--o{ products : primary_category
    product_categories ||--o{ product_categories : parent
    products ||--o{ product_variants : has
    products ||--o{ product_media : has
    products ||--o{ product_reviews : receives
    products ||--o{ product_views : viewed
    products ||--o{ order_items : ordered_as
    products ||--o{ cart_items : added_as
    products ||--o{ product_category_product : maps
    product_categories ||--o{ product_category_product : maps

    customers ||--o{ customer_addresses : has
    customers ||--o{ orders : places
    customers ||--o{ product_reviews : writes
    customers ||--o{ product_views : creates
    customers ||--o| carts : owns
    carts ||--o{ cart_items : contains

    locations ||--o{ locations : parent
    locations ||--o{ customer_addresses : used_by
    locations ||--o{ order_shipping : used_by
    locations ||--o{ shipping_fees : priced_by

    orders ||--o{ order_items : contains
    orders ||--o| order_shipping : ships_to
    orders ||--o{ payments : paid_by
    customer_addresses ||--o{ orders : used_by

    coupons ||--o{ coupon_redemptions : has
    customers ||--o{ coupon_redemptions : uses
    orders ||--o{ coupon_redemptions : applies

    blog_categories ||--o{ blog_posts : has

    users ||--o{ model_has_roles : assigned
    roles ||--o{ model_has_roles : assigned_to
    roles ||--o{ role_has_permissions : includes
    permissions ||--o{ role_has_permissions : included_in

    brands {
        bigint id PK
        string name
        string slug UK
        boolean is_active
    }

    product_categories {
        bigint id PK
        bigint parent_id FK
        string name
        string slug UK
        boolean is_active
    }

    products {
        bigint id PK
        bigint brand_id FK
        bigint primary_category_id FK
        string name
        string slug UK
        decimal price
        decimal sale_price
        integer stock_quantity
        boolean is_active
    }

    product_variants {
        bigint id PK
        bigint product_id FK
        string name
        string sku
        decimal price
        integer stock_quantity
    }

    product_media {
        bigint id PK
        bigint product_id FK
        string file_path
        string collection
    }

    customers {
        bigint id PK
        string name
        string email UK
        string phone UK
        string password
        boolean is_active
    }

    customer_addresses {
        bigint id PK
        bigint customer_id FK
        string recipient_name
        string recipient_phone
        string address_line1
    }

    carts {
        bigint id PK
        bigint customer_id FK
        decimal subtotal
        decimal total
        string coupon_code
    }

    cart_items {
        bigint id PK
        bigint cart_id FK
        bigint product_id FK
        decimal price
        integer quantity
        decimal subtotal
    }

    orders {
        bigint id PK
        string code UK
        bigint customer_id FK
        bigint customer_address_id FK
        string status
        string payment_status
        decimal subtotal
        decimal total
        string coupon_code
    }

    order_items {
        bigint id PK
        bigint order_id FK
        bigint product_id FK
        string product_name
        decimal price
        integer quantity
        decimal subtotal
    }

    order_shipping {
        bigint id PK
        bigint order_id FK
        string recipient_name
        string recipient_phone
        string address_line1
        decimal shipping_fee
    }

    payments {
        bigint id PK
        bigint order_id FK
        string provider
        string status
        decimal amount
    }

    coupons {
        bigint id PK
        string code UK
        string type
        decimal value
        boolean is_active
    }

    coupon_redemptions {
        bigint id PK
        bigint coupon_id FK
        bigint customer_id FK
        bigint order_id FK
        decimal discount_amount
    }
```

### Giải thích thành phần chính

Nhóm catalog gồm `brands`, `product_categories`, `products`, `product_variants`, `product_media`, `product_reviews` và bảng pivot `product_category_product`. Nhóm khách hàng và giỏ hàng gồm `customers`, `customer_addresses`, `carts`, `cart_items`. Nhóm đặt hàng gồm `orders`, `order_items`, `order_shipping`, `payments`. Nhóm coupon gồm `coupons`, `coupon_redemptions`. Nhóm phân quyền admin gồm `users`, `roles`, `permissions` và các bảng pivot của `spatie/laravel-permission`.

### Ghi chú chưa xác định từ code

Một số bảng legacy như `tbl_slider`, `tbl_icons`, `tbl_product_attribute` chỉ còn nên được xem như archive để đối chiếu dữ liệu cũ, không phải dependency runtime chính. Nếu báo cáo cần mô tả đầy đủ phần legacy, cần đối chiếu thêm với `database/import_legacy_tables.sql` hoặc tooling setup legacy đã được guard.

## 7. Sơ đồ kiến trúc tổng quan hệ thống

### Mục đích

Sơ đồ này mô tả kiến trúc tổng quan của dự án theo các lớp chính: client, route, controller, service/application, persistence/model, database và view. Sơ đồ bám theo cấu trúc thực tế trong repo, gồm `routes`, `app/Http/Controllers`, `app/Application`, `app/Infrastructure/Persistence/Eloquent`, `database/migrations` và `resources/views`.

### Code Mermaid

```mermaid
flowchart TD
    Browser["Trình duyệt người dùng"]
    AdminBrowser["Trình duyệt quản trị viên"]
    ApiClient["API client"]

    WebRoutes["Web routes\nroutes/web.php\nroutes/web/frontend.php\nroutes/web/admin.php"]
    ApiRoutes["API routes\nroutes/api.php"]

    StorefrontControllers["Storefront controllers\nHomeController\nProductController\nCartController\nCheckoutController\nOrderController"]
    AdminControllers["Admin controllers\nAuthController\nAdminController\nProductController\nOrderController\nCouponController"]
    ApiControllers["API controllers\nApi/ProductController\nApi/CartController\nApi/OrderController\nApi/Admin/*"]

    ApplicationServices["Application services\nProductService\nCartService\nCheckoutService\nCouponService\nShippingService"]
    DomainLayer["Domain layer\nDTO / Entity / Repository Interface"]
    Persistence["Persistence Eloquent models\nCatalog/Product\nCustomer/Customer\nOrder/Order\nMarketing/Coupon"]
    LegacyModels["Legacy models\napp/Models/*"]
    Database["MySQL database\nmigrations"]
    BladeViews["Blade views\nresources/views"]
    Assets["Frontend assets\nresources/css\nresources/js\nVite"]

    Browser --> WebRoutes
    AdminBrowser --> WebRoutes
    ApiClient --> ApiRoutes

    WebRoutes --> StorefrontControllers
    WebRoutes --> AdminControllers
    ApiRoutes --> ApiControllers

    StorefrontControllers --> ApplicationServices
    AdminControllers --> ApplicationServices
    ApiControllers --> ApplicationServices
    ApplicationServices --> DomainLayer
    ApplicationServices --> Persistence
    ApplicationServices --> LegacyModels
    Persistence --> Database
    LegacyModels --> Database

    StorefrontControllers --> BladeViews
    AdminControllers --> BladeViews
    BladeViews --> Assets
```

### Giải thích thành phần chính

Storefront và admin cùng đi qua `routes/web.php`, nhưng được tách thành `routes/web/frontend.php` và `routes/web/admin.php`. API được tách riêng ở `routes/api.php`. Controller điều phối request, service xử lý nghiệp vụ, model Eloquent chuẩn hóa và model legacy cùng truy cập database. Giao diện chính sử dụng `Blade`, còn CSS/JS được quản lý trong `resources/css`, `resources/js` và build bằng `Vite`.

### Ghi chú chưa xác định từ code

Sơ đồ này mô tả kiến trúc source code, không mô tả hạ tầng triển khai production vì cấu hình server thực tế chưa xác định từ code.

## 8. Sơ đồ phân rã module hệ thống

### Mục đích

Sơ đồ phân rã module giúp trình bày hệ thống theo các khối nghiệp vụ lớn. Các module được xác định từ route, controller, view và migration hiện có.

### Code Mermaid

```mermaid
flowchart TD
    System["Website bán hàng thiết bị công nghệ"]

    Storefront["Storefront"]
    Admin["Admin"]
    API["API v1"]
    Data["Database"]

    Catalog["Catalog\nSản phẩm, danh mục, thương hiệu"]
    CartCheckout["Cart & Checkout\nGiỏ hàng, coupon, giao hàng, đặt hàng"]
    Customer["Customer\nĐăng nhập, tài khoản, lịch sử đơn"]
    Content["Content\nBlog, video, liên hệ, slider"]
    Support["Support\nChatbot, compare, yêu thích"]

    AdminCatalog["Admin Catalog\nSản phẩm, danh mục, thương hiệu, thuộc tính, gallery"]
    AdminOrder["Admin Order\nĐơn hàng, in đơn, giao hàng"]
    AdminContent["Admin Content\nBài viết, video, slider, thông tin site"]
    AdminAccess["Admin Access\nUser, role, permission"]
    AdminReport["Admin Report\nLợi nhuận, product discovery, dashboard"]

    ApiPublic["Public API\nproducts, categories, brands, blog, settings"]
    ApiProtected["Protected API\ncart, orders, reviews, user"]
    ApiAdmin["Admin API\nproducts, categories, brands, orders, statistics"]

    CoreTables["Core tables\nproducts, customers, orders"]
    CmsTables["CMS tables\nblog_posts, videos, banners"]
    AuthTables["Auth tables\nusers, roles, permissions"]

    System --> Storefront
    System --> Admin
    System --> API
    System --> Data

    Storefront --> Catalog
    Storefront --> CartCheckout
    Storefront --> Customer
    Storefront --> Content
    Storefront --> Support

    Admin --> AdminCatalog
    Admin --> AdminOrder
    Admin --> AdminContent
    Admin --> AdminAccess
    Admin --> AdminReport

    API --> ApiPublic
    API --> ApiProtected
    API --> ApiAdmin

    Data --> CoreTables
    Data --> CmsTables
    Data --> AuthTables
```

### Giải thích thành phần chính

Storefront phục vụ người mua hàng. Admin phục vụ quản trị viên. API phục vụ các client kỹ thuật và có nhóm public/protected/admin rõ ràng trong `routes/api.php`. Database được chia thành nhóm core commerce, CMS và auth/permission.

### Ghi chú chưa xác định từ code

Ứng dụng mobile riêng chưa xác định từ code, vì vậy `API client` chỉ được mô tả như tác nhân kỹ thuật, không mô tả một app mobile cụ thể.

## 9. State Diagram trạng thái đơn hàng

### Mục đích

Sơ đồ trạng thái mô tả vòng đời cơ bản của đơn hàng theo enum `status` trong migration `2025_10_23_001400_create_orders_table.php` và logic trong `OrderController`.

### Code Mermaid

```mermaid
stateDiagram-v2
    [*] --> pending: Tạo đơn hàng
    pending --> confirmed: Admin xác nhận
    confirmed --> processing: Bắt đầu xử lý
    processing --> shipped: Giao hàng
    shipped --> completed: Hoàn tất

    pending --> cancelled: Khách/Admin hủy
    confirmed --> cancelled: Hủy trước khi hoàn tất
    processing --> cancelled: Hủy nếu còn cho phép

    completed --> refunded: Hoàn tiền
    cancelled --> [*]
    completed --> [*]
    refunded --> [*]
```

### Giải thích thành phần chính

Bảng `orders` có các trạng thái `pending`, `confirmed`, `processing`, `shipped`, `completed`, `cancelled`, `refunded`. Khi khách đặt hàng qua `CheckoutService`, đơn mới được tạo với `status = pending`. Trong `OrderController`, khách hàng có thể hủy đơn nếu trạng thái không nằm trong `cancelled`, `completed`, `refunded`.

### Ghi chú chưa xác định từ code

Luồng chuyển trạng thái admin từ `pending` sang `confirmed`, `processing`, `shipped`, `completed` trong bảng chuẩn hóa `orders` chưa xác định đầy đủ từ code đã inspect vì phần admin order còn dùng service legacy.

## 10. Sequence Diagram quản lý sản phẩm admin

### Mục đích

Sơ đồ mô tả tuần tự xử lý khi quản trị viên thêm hoặc cập nhật sản phẩm. Luồng dựa trên `routes/web/admin.php`, `ProductController`, `ProductService`, `ProductPricingService`, `BrandRepositoryInterface`, `CategoryRepositoryInterface` và bảng `products`.

### Code Mermaid

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Quản trị viên
    participant Browser as Trình duyệt admin
    participant Middleware as auth:web + admin.access + admin.permission
    participant Route as Admin product route
    participant Controller as ProductController
    participant Pricing as ProductPricingService
    participant ProductService as ProductService
    participant DB as MySQL Database
    participant View as Blade admin view

    Admin->>Browser: Mở form thêm/sửa sản phẩm
    Browser->>Middleware: Request /add-product hoặc /edit-product/{product_id}
    Middleware->>Route: Cho phép nếu có manage products
    Route->>Controller: Gọi add_product hoặc edit_product
    Controller->>DB: Lấy danh mục và thương hiệu
    Controller-->>View: Trả form admin

    Admin->>Browser: Nhập thông tin sản phẩm
    Browser->>Middleware: POST /save-product hoặc /update-product/{product_id}
    Middleware->>Route: Kiểm tra quyền manage products
    Route->>Controller: Gọi save_product hoặc update_product
    Controller->>Controller: validateProductRequest()
    Controller->>Pricing: Chuẩn hóa sale_type, sale_value, sale_price
    Pricing-->>Controller: Pricing payload
    Controller->>ProductService: createProduct hoặc updateProduct
    ProductService->>DB: Ghi products, category pivot, media/document nếu có
    DB-->>ProductService: Lưu thành công
    ProductService-->>Controller: Product
    Controller-->>Browser: Redirect và flash message
```

### Giải thích thành phần chính

Route sản phẩm admin được bảo vệ bởi `admin.permission:manage products`. `ProductController` validate dữ liệu sản phẩm, chuẩn hóa giá và khuyến mãi, sau đó gọi `ProductService` để tạo hoặc cập nhật dữ liệu. Các bảng liên quan gồm `products`, `brands`, `product_categories`, `product_category_product`, `product_media`.

### Ghi chú chưa xác định từ code

Quy trình upload tài liệu/hình ảnh chi tiết phụ thuộc logic trong `ProductService`; sơ đồ chỉ mô tả ở mức phân tích, không đi sâu từng bước lưu file.

## 11. Data Flow Diagram mức ngữ cảnh cho checkout

### Mục đích

Sơ đồ mô tả luồng dữ liệu chính trong nghiệp vụ checkout: dữ liệu từ người dùng, dữ liệu giỏ hàng session, coupon, phí giao hàng và dữ liệu đơn hàng ghi xuống database.

### Code Mermaid

```mermaid
flowchart LR
    Customer["Khách hàng / khách vãng lai"]
    CheckoutUI["Giao diện checkout\nshow_checkout.blade.php\npayment.blade.php"]
    CheckoutController["CheckoutController"]
    CartSession["Session cart"]
    CouponSession["Session coupon"]
    ShippingSession["Session shipping fee"]
    CheckoutService["CheckoutService"]
    CouponService["CouponService"]
    ShippingService["ShippingService"]
    Database["MySQL\ncustomers, customer_addresses,\norders, order_items,\norder_shipping, payments,\ncoupon_redemptions"]

    Customer -->|Thông tin giao hàng, thanh toán| CheckoutUI
    CheckoutUI -->|POST /confirm-order| CheckoutController
    CheckoutController -->|Lấy customer_id nếu có| CheckoutController
    CheckoutController -->|payload + customerId| CheckoutService

    CheckoutService -->|Đọc sản phẩm| CartSession
    CheckoutService -->|Kiểm tra coupon hiện tại| CouponService
    CouponService --> CouponSession
    CheckoutService -->|Tính phí giao hàng| ShippingService
    ShippingService --> ShippingSession

    CheckoutService -->|Tạo đơn hàng trong transaction| Database
    Database -->|order_code| CheckoutService
    CheckoutService -->|Xóa session tạm| CartSession
    CheckoutService -->|Xóa coupon| CouponSession
    CheckoutService -->|Xóa phí giao hàng| ShippingSession
    CheckoutService --> CheckoutController
    CheckoutController -->|JSON message + order_code| CheckoutUI
    CheckoutUI --> Customer
```

### Giải thích thành phần chính

Checkout nhận dữ liệu từ form Blade và session. `CheckoutService` tổng hợp dữ liệu giỏ hàng, coupon, phí giao hàng để tạo đơn hàng. Dữ liệu cuối cùng được ghi vào các bảng chuẩn hóa, trong đó `orders` giữ thông tin tổng quan, `order_items` giữ dòng sản phẩm, `order_shipping` giữ địa chỉ giao hàng và `payments` giữ thông tin thanh toán.

### Ghi chú chưa xác định từ code

Luồng kết nối thanh toán trực tuyến thật và callback cập nhật `payment_status` chưa xác định từ code.

## 12. Sơ đồ phân quyền admin

### Mục đích

Sơ đồ mô tả cách hệ thống kiểm soát quyền admin dựa trên route middleware và `AdminPermissions`.

### Code Mermaid

```mermaid
flowchart TD
    User["Admin user\nusers"]
    Login["Đăng nhập\nAuthController@login"]
    Auth["auth:web"]
    AdminAccess["admin.access"]
    PermissionMiddleware["admin.permission"]

    Roles["roles"]
    Permissions["permissions"]
    ModelRoles["model_has_roles"]
    RolePermissions["role_has_permissions"]

    ManageProducts["manage products\nSản phẩm, danh mục, thương hiệu, gallery, comment"]
    ManageOrders["manage orders\nĐơn hàng, giao hàng, báo cáo lợi nhuận"]
    ManageContent["manage content\nBài viết, slider, video"]
    ManageCoupons["manage coupons\nCoupon, gửi coupon mail"]
    ManageUsers["manage users\nUser admin"]
    ManageRoles["manage roles\nRole/permission"]
    ManageDocuments["manage documents\nTài liệu"]
    ManageSystem["manage system\nSettings, chat inbox, reports"]

    User --> Login --> Auth --> AdminAccess --> PermissionMiddleware
    User --> ModelRoles --> Roles
    Roles --> RolePermissions --> Permissions
    Permissions --> PermissionMiddleware

    PermissionMiddleware --> ManageProducts
    PermissionMiddleware --> ManageOrders
    PermissionMiddleware --> ManageContent
    PermissionMiddleware --> ManageCoupons
    PermissionMiddleware --> ManageUsers
    PermissionMiddleware --> ManageRoles
    PermissionMiddleware --> ManageDocuments
    PermissionMiddleware --> ManageSystem
```

### Giải thích thành phần chính

Admin đăng nhập bằng `AuthController@login`, sau đó phải qua `auth:web` và `admin.access`. Từng nhóm route admin được kiểm tra bằng `admin.permission`. Danh sách quyền chuẩn nằm trong `App\Support\AdminPermissions`, gồm `manage users`, `manage roles`, `manage documents`, `manage products`, `manage content`, `manage orders`, `manage coupons`, `manage system`. Dữ liệu role/permission được lưu qua các bảng của `spatie/laravel-permission`.

### Ghi chú chưa xác định từ code

Ma trận quyền theo từng màn hình chi tiết chưa được mô tả đầy đủ trong code route ở mức từng nút thao tác nhỏ; sơ đồ này mô tả theo nhóm route và permission chính.

## 13. Component Diagram cho các thành phần Laravel chính

### Mục đích

Sơ đồ mô tả các component kỹ thuật chính của hệ thống Laravel, giúp người đọc hiểu cách storefront, admin, API, service, model, view và database phối hợp với nhau.

### Code Mermaid

```mermaid
flowchart TD
    Client["Browser / API Client"]
    Routing["Routing\nweb.php, frontend.php, admin.php, api.php"]
    Middleware["Middleware\nauth:web, admin.access,\nadmin.permission, auth:sanctum"]
    Controllers["Controllers\napp/Http/Controllers"]
    Requests["Form Requests\napp/Http/Requests"]
    Services["Application Services\napp/Application"]
    Repositories["Repository Interfaces\napp/Domain"]
    Eloquent["Eloquent Models\napp/Infrastructure/Persistence/Eloquent"]
    Legacy["Legacy Models\napp/Models"]
    Views["Blade Views\nresources/views"]
    Assets["Assets\nresources/css, resources/js, Vite"]
    Database["MySQL Database"]

    Client --> Routing
    Routing --> Middleware
    Middleware --> Controllers
    Controllers --> Requests
    Controllers --> Services
    Controllers --> Views
    Services --> Repositories
    Services --> Eloquent
    Services --> Legacy
    Eloquent --> Database
    Legacy --> Database
    Views --> Assets
```

### Giải thích thành phần chính

Route tiếp nhận request, middleware kiểm soát xác thực và phân quyền, controller điều phối xử lý, request class validate dữ liệu, service xử lý nghiệp vụ, model truy cập database và view trả giao diện `Blade`. Sơ đồ phản ánh đúng việc repo có cả lớp Eloquent chuẩn hóa và model legacy.

### Ghi chú chưa xác định từ code

Sơ đồ không mô tả hạ tầng ngoài ứng dụng như load balancer, CDN hoặc object storage vì chưa xác định từ code.

## 14. Deployment Diagram mức phân tích

### Mục đích

Sơ đồ mô tả môi trường triển khai logic của ứng dụng Laravel theo mức phân tích. Đây không phải cấu hình server production thực tế, mà là mô hình triển khai tối thiểu suy ra từ công nghệ trong repo.

### Code Mermaid

```mermaid
flowchart TD
    User["Người dùng / Admin"]
    Browser["Web Browser"]
    WebServer["Web Server\nPHP runtime"]
    LaravelApp["Laravel 11 Application"]
    MySQL["MySQL Database"]
    PublicStorage["Public uploads\npublic/uploads"]
    ViteBuild["Built assets\nVite build output"]
    Mail["Mail service\nLaravel mail templates"]
    PaymentMock["Payment routes\nPayPal route / VNPay mock"]

    User --> Browser
    Browser --> WebServer
    WebServer --> LaravelApp
    LaravelApp --> MySQL
    LaravelApp --> PublicStorage
    LaravelApp --> ViteBuild
    LaravelApp --> Mail
    LaravelApp --> PaymentMock
```

### Giải thích thành phần chính

Ứng dụng chạy trên Laravel/PHP, dùng `MySQL` theo cấu hình database, dùng `public/uploads` cho tài nguyên upload và dùng `Vite` cho frontend assets. Repo có mail templates và các controller mail legacy, đồng thời có route PayPal và VNPay mock.

### Ghi chú chưa xác định từ code

Server production, domain, SSL, queue worker, cron, Redis, CDN và cấu hình cloud chưa xác định từ code.

## 15. Sequence Diagram đăng nhập admin

### Mục đích

Sơ đồ mô tả luồng đăng nhập quản trị viên dựa trên `AuthController@login`, route `POST /auth-login`, middleware `throttle:5,1`, bảng `users` và phân quyền admin.

### Code Mermaid

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Quản trị viên
    participant Browser as Trình duyệt
    participant Route as POST /auth-login
    participant Throttle as throttle:5,1
    participant Controller as AuthController
    participant Authenticator as AdminAuthenticator
    participant Auth as Auth guard web
    participant Access as AdminAccess
    participant Session as Session

    Admin->>Browser: Nhập email, password
    Browser->>Route: Gửi form đăng nhập
    Route->>Throttle: Kiểm tra giới hạn request
    Throttle->>Controller: Cho phép xử lý
    Controller->>Controller: Validate email, password
    Controller->>Authenticator: resolveUser(email, password)
    Authenticator-->>Controller: User hoặc null
    alt Không hợp lệ
        Controller-->>Browser: Redirect /admin kèm lỗi
    else Hợp lệ
        Controller->>Auth: login(user, remember)
        Controller->>Session: regenerate()
        Controller->>Access: canAccess(user)
        alt Không có quyền admin
            Controller->>Auth: logout()
            Controller-->>Browser: Redirect /admin kèm lỗi
        else Có quyền admin
            Controller-->>Browser: Redirect admin.dashboard
        end
    end
```

### Giải thích thành phần chính

Route đăng nhập admin nằm trong group `guest:web`. Sau khi xác thực tài khoản, hệ thống gọi `AdminAccess::canAccess` để kiểm tra người dùng có quyền vào admin hay không. Nếu hợp lệ, người dùng được chuyển tới `admin.dashboard`.

### Ghi chú chưa xác định từ code

Cơ chế 2FA cho admin chưa xác định từ code.

## 16. Sequence Diagram áp dụng coupon

### Mục đích

Sơ đồ mô tả luồng người dùng áp dụng mã giảm giá tại giỏ hàng, dựa trên `POST /check-coupon`, `CartController@check_coupon`, `ApplyCouponRequest` và `CouponService`.

### Code Mermaid

```mermaid
sequenceDiagram
    autonumber
    actor User as Người dùng
    participant Browser as Trình duyệt
    participant Route as POST /check-coupon
    participant Request as ApplyCouponRequest
    participant Controller as CartController
    participant CartService as CartService
    participant CouponService as CouponService
    participant CouponRepo as CouponRepository
    participant Session as Session coupon

    User->>Browser: Nhập mã coupon
    Browser->>Route: Gửi coupon
    Route->>Request: Validate dữ liệu
    Request-->>Controller: Coupon hợp lệ về định dạng
    Controller->>CartService: subtotal()
    CartService-->>Controller: Tổng tạm tính
    Controller->>CouponService: apply(code, subtotal, customer_id)
    CouponService->>CouponRepo: findActiveByCode(code)
    CouponRepo-->>CouponService: Coupon hoặc null
    alt Coupon không hợp lệ
        CouponService-->>Controller: RuntimeException
        Controller-->>Browser: Redirect back kèm error
    else Coupon hợp lệ
        CouponService->>CouponService: Kiểm tra usage_limit, usage_per_customer
        CouponService->>CouponService: Tính discount_amount
        CouponService->>Session: Lưu coupon payload
        CouponService-->>Controller: Payload coupon
        Controller-->>Browser: Redirect back kèm message
    end
```

### Giải thích thành phần chính

`CouponService` kiểm tra coupon active, điều kiện tổng đơn, giới hạn số lượt dùng và giới hạn theo khách hàng. Coupon hợp lệ được lưu vào session để dùng lại trong checkout.

### Ghi chú chưa xác định từ code

Giao diện quản trị chi tiết điều kiện coupon theo phân khúc khách hàng có bảng dữ liệu hỗ trợ, nhưng mức hoàn chỉnh của toàn bộ luồng segment coupon chưa xác định từ code đã inspect.

## 17. Activity Diagram đăng ký và đăng nhập khách hàng

### Mục đích

Sơ đồ mô tả luồng khách hàng đăng ký hoặc đăng nhập trong khu vực checkout, dựa trên `CheckoutController`, `RegisterCustomerRequest`, `LoginCustomerRequest` và bảng `customers`.

### Code Mermaid

```mermaid
flowchart TD
    Start([Bắt đầu])
    Choose{"Khách chọn đăng ký hay đăng nhập?"}
    RegisterForm["Mở /dang-ky"]
    LoginForm["Mở /dang-nhap"]
    SubmitRegister["POST /add-customer"]
    SubmitLogin["POST /login-customer"]
    ValidateRegister{"Dữ liệu đăng ký hợp lệ?"}
    ValidateLogin{"Dữ liệu đăng nhập hợp lệ?"}
    Duplicate{"Email/phone trùng?"}
    CreateCustomer["Tạo bản ghi customers\nhash password"]
    FindCustomer["Tìm customer theo email"]
    PasswordOk{"Mật khẩu đúng và customer active?"}
    SetSession["Lưu customer_id vào session"]
    Redirect["Redirect về checkout/payment/history theo intended URL"]
    Error["Hiển thị lỗi"]
    End([Kết thúc])

    Start --> Choose
    Choose -- Đăng ký --> RegisterForm --> SubmitRegister --> ValidateRegister
    ValidateRegister -- Không --> Error --> End
    ValidateRegister -- Có --> Duplicate
    Duplicate -- Có --> Error
    Duplicate -- Không --> CreateCustomer --> SetSession --> Redirect --> End

    Choose -- Đăng nhập --> LoginForm --> SubmitLogin --> ValidateLogin
    ValidateLogin -- Không --> Error
    ValidateLogin -- Có --> FindCustomer --> PasswordOk
    PasswordOk -- Không --> Error
    PasswordOk -- Có --> SetSession
```

### Giải thích thành phần chính

Khách hàng đăng ký qua `POST /add-customer`; hệ thống tạo `Customer` với mật khẩu được `bcrypt`. Khách hàng đăng nhập qua `POST /login-customer`; hệ thống tìm `Customer` theo email và kiểm tra mật khẩu bằng `StorefrontCustomerPassword`. Khi thành công, session lưu `customer_id`.

### Ghi chú chưa xác định từ code

Xác minh email/phone bắt buộc cho khách hàng trước checkout chưa xác định từ code.

## 18. Sequence Diagram hủy đơn hàng

### Mục đích

Sơ đồ mô tả luồng khách hàng hủy đơn hàng qua `POST /huy-don-hang`, dựa trên `OrderController@huy_don_hang` và bảng `orders`.

### Code Mermaid

```mermaid
sequenceDiagram
    autonumber
    actor Customer as Khách hàng
    participant Browser as Trình duyệt
    participant Route as POST /huy-don-hang
    participant Controller as OrderController
    participant Session as Session
    participant OrderModel as StorefrontOrder / Legacy Order
    participant DB as MySQL Database

    Customer->>Browser: Nhập lý do hủy đơn
    Browser->>Route: Gửi order_code và lydo
    Route->>Controller: huy_don_hang(request)
    Controller->>Controller: Validate order_code, lydo
    Controller->>Session: Lấy customer_id
    alt Chưa đăng nhập
        Controller-->>Browser: JSON 401
    else Đã đăng nhập
        Controller->>OrderModel: Tìm đơn theo customer_id và code
        OrderModel-->>Controller: Order hoặc null
        alt Không tìm thấy
            Controller-->>Browser: JSON 404
        else Tìm thấy
            Controller->>Controller: Kiểm tra có thể hủy không
            alt Không thể hủy
                Controller-->>Browser: JSON 422
            else Có thể hủy
                Controller->>DB: Cập nhật status = cancelled, cancelled_at, cancel_reason
                DB-->>Controller: Lưu thành công
                Controller-->>Browser: JSON message, order_code
            end
        end
    end
```

### Giải thích thành phần chính

Với đơn chuẩn hóa, `OrderController` tìm `StorefrontOrder` theo `customer_id` và `code`. Đơn chỉ được hủy nếu trạng thái không nằm trong `cancelled`, `completed`, `refunded`. Với dữ liệu legacy, controller có nhánh xử lý qua `App\Models\Order`.

### Ghi chú chưa xác định từ code

Quy trình hoàn tiền tự động khi hủy đơn chưa xác định từ code.

## 19. Sequence Diagram chatbot storefront

### Mục đích

Sơ đồ mô tả luồng người dùng gửi tin nhắn chatbot, dựa trên route `/chatbot/conversation`, `/chatbot/messages`, `ChatController` và các service chatbot trong `app/Application/AI/Services`.

### Code Mermaid

```mermaid
sequenceDiagram
    autonumber
    actor User as Người dùng
    participant Widget as Chatbot widget
    participant ConversationRoute as GET /chatbot/conversation
    participant MessageRoute as POST /chatbot/messages
    participant Controller as ChatController
    participant ConversationService as ChatbotConversationService
    participant ChatbotService as ChatbotService
    participant RuleBot as RuleBasedChatbotService
    participant DB as chat_conversations / chat_messages

    User->>Widget: Mở chatbot
    Widget->>ConversationRoute: Lấy hội thoại hiện tại
    ConversationRoute->>Controller: conversation()
    Controller->>ConversationService: Resolve/create conversation
    ConversationService->>DB: Đọc hoặc tạo chat_conversations
    DB-->>ConversationService: Conversation
    Controller-->>Widget: JSON conversation

    User->>Widget: Gửi tin nhắn
    Widget->>MessageRoute: POST message
    MessageRoute->>Controller: store()
    Controller->>ConversationService: Lưu user message
    ConversationService->>DB: Tạo chat_messages
    Controller->>ChatbotService: Sinh phản hồi
    ChatbotService->>RuleBot: Xử lý rule-based fallback
    RuleBot-->>ChatbotService: Nội dung phản hồi
    Controller->>ConversationService: Lưu bot response
    ConversationService->>DB: Tạo chat_messages
    Controller-->>Widget: JSON response
```

### Giải thích thành phần chính

Repo có `ChatController`, `RuleBasedChatbotService`, `ChatbotService`, `ChatbotConversationService`, `chat_conversations` và `chat_messages`. Sơ đồ thể hiện chatbot ở mức đã có trong code: hội thoại, tin nhắn người dùng và phản hồi bot.

### Ghi chú chưa xác định từ code

Việc gọi dịch vụ AI bên ngoài có class `OpenAiChatbotClient`, nhưng mức cấu hình và sử dụng thực tế trong môi trường chạy chưa xác định từ code đã inspect.

## 20. Sơ đồ API route group

### Mục đích

Sơ đồ mô tả cách `routes/api.php` chia API thành public API, protected API và admin API.

### Code Mermaid

```mermaid
flowchart TD
    ApiRoot["/api/v1"]

    Public["Public API\nkhông yêu cầu auth:sanctum"]
    Protected["Protected API\nauth:sanctum"]
    AdminApi["Admin API\nauth:sanctum + admin.access:sanctum"]

    Products["Products\n/products, /products/latest,\n/products/search, /products/{slug}"]
    Categories["Categories\n/categories, /categories/tree"]
    Brands["Brands\n/brands"]
    Content["Content\n/blog, /settings, /chatbot"]
    PublicAuth["Auth\n/auth/login, /auth/register, /auth/refresh"]

    UserProfile["User profile\n/user"]
    CartApi["Cart\n/cart, /cart/add, /cart/update, /cart/remove"]
    OrderApi["Orders\n/orders, /orders/{id}, cancel"]
    ReviewApi["Reviews\nPOST/PUT/DELETE /reviews"]

    AdminProducts["Admin products\napiResource products"]
    AdminCatalog["Admin categories / brands"]
    AdminOrders["Admin orders\nstatus update"]
    AdminStats["Admin statistics"]

    ApiRoot --> Public
    ApiRoot --> Protected
    ApiRoot --> AdminApi

    Public --> Products
    Public --> Categories
    Public --> Brands
    Public --> Content
    Public --> PublicAuth

    Protected --> UserProfile
    Protected --> CartApi
    Protected --> OrderApi
    Protected --> ReviewApi

    AdminApi --> AdminProducts
    AdminApi --> AdminCatalog
    AdminApi --> AdminOrders
    AdminApi --> AdminStats
```

### Giải thích thành phần chính

Public API phục vụ dữ liệu catalog, nội dung và auth. Protected API yêu cầu `auth:sanctum`, phục vụ user profile, cart, order và review. Admin API yêu cầu cả `auth:sanctum` và `admin.access:sanctum`, đồng thời kiểm tra `admin.permission` theo nhóm quyền.

### Ghi chú chưa xác định từ code

Client cụ thể sử dụng API, ví dụ app mobile production, chưa xác định từ code.

## 21. ERD mở rộng cho CMS, chat và analytics

### Mục đích

Sơ đồ bổ sung cho ERD chính, tập trung vào các bảng nội dung, chatbot và analytics đã có trong migration.

### Code Mermaid

```mermaid
erDiagram
    blog_categories ||--o{ blog_posts : contains
    customers ||--o{ chat_conversations : starts
    users ||--o{ chat_conversations : handles
    chat_conversations ||--o{ chat_messages : contains
    customers ||--o{ analytics_events : triggers
    products ||--o{ analytics_events : referenced_by
    orders ||--o{ analytics_events : referenced_by
    products ||--o{ product_views : viewed
    customers ||--o{ product_views : views
    customers ||--o{ product_search_queries : searches
    product_categories ||--o{ product_search_queries : filters
    products ||--o{ product_recommendation_impressions : recommended

    blog_categories {
        bigint id PK
        string name
        string slug UK
        boolean is_active
    }

    blog_posts {
        bigint id PK
        bigint blog_category_id FK
        string title
        string slug UK
        string status
        timestamp published_at
    }

    videos {
        bigint id PK
        string title
        string slug UK
        string video_url
        boolean is_active
    }

    chat_conversations {
        bigint id PK
        bigint customer_id FK
        bigint user_id FK
        string session_token
        string channel
        string status
    }

    chat_messages {
        bigint id PK
        bigint conversation_id FK
        bigint customer_id FK
        bigint user_id FK
        string sender
        text message
    }

    analytics_events {
        bigint id PK
        string event_type
        bigint customer_id FK
        bigint order_id FK
        bigint product_id FK
        json payload
    }

    product_views {
        bigint id PK
        bigint product_id FK
        bigint customer_id FK
        string ip_address
        timestamp viewed_at
    }

    product_search_queries {
        bigint id PK
        bigint customer_id FK
        bigint category_id FK
        string keyword
        integer results_count
    }
```

### Giải thích thành phần chính

CMS gồm `blog_categories`, `blog_posts`, `videos`. Chatbot/admin chat gồm `chat_conversations` và `chat_messages`. Analytics gồm `analytics_events`, `product_views`, `product_search_queries` và `product_recommendation_impressions`.

### Ghi chú chưa xác định từ code

Không phải toàn bộ bảng analytics đều chắc chắn đã được hiển thị trên giao diện quản trị; sơ đồ chỉ thể hiện bảng và quan hệ có trong migration.

## 22. Activity Diagram quản lý coupon

### Mục đích

Sơ đồ mô tả luồng quản trị coupon dựa trên `routes/web/admin.php`, `CouponController`, quyền `manage coupons` và bảng `coupons`.

### Code Mermaid

```mermaid
flowchart TD
    Start([Bắt đầu])
    AdminLogin["Admin đã đăng nhập"]
    Permission{"Có quyền manage coupons?"}
    ListCoupon["Xem danh sách coupon\nGET /list-coupon"]
    OpenForm["Mở form thêm coupon\nGET /insert-coupon"]
    Submit["Gửi form\nPOST /insert-coupon-code"]
    Validate{"Dữ liệu coupon hợp lệ?"}
    Save["Lưu vào bảng coupons"]
    Delete{"Admin xóa coupon?"}
    DeleteAction["DELETE /delete-coupon/{coupon_id}"]
    Message["Hiển thị thông báo"]
    Denied["Từ chối truy cập"]
    End([Kết thúc])

    Start --> AdminLogin --> Permission
    Permission -- Không --> Denied --> End
    Permission -- Có --> ListCoupon --> OpenForm --> Submit --> Validate
    Validate -- Không --> Message --> OpenForm
    Validate -- Có --> Save --> Message --> ListCoupon
    ListCoupon --> Delete
    Delete -- Có --> DeleteAction --> Message
    Delete -- Không --> End
```

### Giải thích thành phần chính

Route coupon admin nằm dưới middleware `admin.permission:manage coupons`. Admin có thể mở danh sách coupon, thêm coupon và xóa coupon. Coupon phía khách hàng được sử dụng qua `POST /check-coupon` và `CouponService`.

### Ghi chú chưa xác định từ code

Toàn bộ giao diện quản lý nâng cao như segment coupon, lịch chạy tự động marketing hoặc stacking coupon chưa xác định đầy đủ từ code đã inspect.

## 23. Activity Diagram quản lý nội dung

### Mục đích

Sơ đồ mô tả luồng admin quản lý nội dung như bài viết, danh mục bài viết, slider và video, dựa trên nhóm route `admin.permission:manage content`.

### Code Mermaid

```mermaid
flowchart TD
    Start([Bắt đầu])
    Admin["Admin đã đăng nhập"]
    Permission{"Có quyền manage content?"}
    Choose{"Chọn loại nội dung"}
    CategoryPost["Danh mục bài viết\nCategoryPost"]
    Post["Bài viết\nPostController"]
    Slider["Slider\nSliderController"]
    Video["Video\nVideoController"]
    Form["Mở form thêm/sửa"]
    Submit["Gửi dữ liệu"]
    Validate{"Dữ liệu hợp lệ?"}
    Save["Lưu dữ liệu"]
    DeleteOrStatus["Xóa hoặc bật/tắt nếu route hỗ trợ"]
    Message["Hiển thị thông báo"]
    Denied["Từ chối truy cập"]
    End([Kết thúc])

    Start --> Admin --> Permission
    Permission -- Không --> Denied --> End
    Permission -- Có --> Choose
    Choose --> CategoryPost --> Form
    Choose --> Post --> Form
    Choose --> Slider --> Form
    Choose --> Video --> Form
    Form --> Submit --> Validate
    Validate -- Không --> Message --> Form
    Validate -- Có --> Save --> Message
    Save --> DeleteOrStatus --> Message --> End
```

### Giải thích thành phần chính

Nhóm route content admin trong `routes/web/admin.php` gồm quản lý danh mục bài viết, bài viết, slider và video. Các controller tương ứng là `CategoryPost`, `PostController`, `SliderController` và `VideoController`.

### Ghi chú chưa xác định từ code

Workflow duyệt bài nhiều bước, lịch xuất bản chi tiết hoặc biên tập đa người chưa xác định từ code.

## 24. Sơ đồ quan hệ route - controller - view chính

### Mục đích

Sơ đồ giúp liên kết các route chính với controller và view tương ứng, phục vụ phần mô tả thiết kế giao diện và luồng điều hướng.

### Code Mermaid

```mermaid
flowchart LR
    HomeRoute["GET /, /trang-chu"]
    SearchRoute["GET/POST /tim-kiem, /san-pham"]
    DetailRoute["GET /chi-tiet/{product_slug}"]
    CartRoute["GET /gio-hang"]
    CheckoutRoute["GET /checkout, /payment"]
    HistoryRoute["GET /history, /view-history-order/{order_code}"]
    AdminProductRoute["/add-product, /admin/products-management, /edit-product/{id}"]
    AdminOrderRoute["/admin/orders-management, /view-order/{code}"]

    HomeController["HomeController"]
    ProductController["ProductController"]
    CartController["CartController"]
    CheckoutController["CheckoutController"]
    OrderController["OrderController"]

    HomeView["home.blade.php"]
    SearchView["pages/sanpham/*.blade.php"]
    DetailView["pages/sanpham/show_details.blade.php"]
    CartView["pages/cart/cart_ajax.blade.php"]
    CheckoutView["pages/checkout/*.blade.php"]
    HistoryView["pages/history/*.blade.php"]
    AdminProductView["admin/*product*.blade.php"]
    AdminOrderView["admin/manage_order.blade.php\nadmin/view_order.blade.php"]

    HomeRoute --> HomeController --> HomeView
    SearchRoute --> HomeController --> SearchView
    DetailRoute --> ProductController --> DetailView
    CartRoute --> CartController --> CartView
    CheckoutRoute --> CheckoutController --> CheckoutView
    HistoryRoute --> OrderController --> HistoryView
    AdminProductRoute --> ProductController --> AdminProductView
    AdminOrderRoute --> OrderController --> AdminOrderView
```

### Giải thích thành phần chính

Sơ đồ tập trung vào các route Blade chính. Storefront dùng các view trong `resources/views/pages` và partial storefront; admin dùng `resources/views/admin` và `admin_layout.blade.php`.

### Ghi chú chưa xác định từ code

Sơ đồ không liệt kê toàn bộ route vì `routes/web/frontend.php` và `routes/web/admin.php` có nhiều route chi tiết; mục tiêu là mô tả các tuyến chính cho báo cáo.

## Kết luận

Bộ sơ đồ trên bám theo cấu trúc phân tích thiết kế hệ thống được gợi ý trong `DOCUMENTATION_TEMPLATE.md` cho đề tài lập trình hướng đối tượng. Các actor, module, use case, luồng nghiệp vụ, class và entity đều được xác định từ mã nguồn hiện tại. Những điểm chưa xác định rõ từ code đã được ghi chú để tránh mô tả vượt quá hiện trạng triển khai.

## Phụ lục: Prompt chi tiết để tái tạo bộ sơ đồ

Phần này lưu lại prompt chi tiết có thể dùng lại với AI để tạo hoặc rà soát bộ sơ đồ phân tích thiết kế hệ thống. Prompt yêu cầu AI đọc `DOCUMENTATION_TEMPLATE.md` và inspect code thật trước khi viết, nhằm hạn chế việc suy diễn hoặc mô tả chức năng không có trong dự án.

````text
Hãy đọc dự án Laravel hiện tại và file `DOCUMENTATION_TEMPLATE.md`, sau đó phân tích hệ thống để tạo bộ sơ đồ phục vụ báo cáo ĐATN.

Bối cảnh đề tài:
- Tên đề tài: Xây dựng website bán hàng thiết bị công nghệ bằng Laravel
- Loại đề tài: lập trình, phân tích và thiết kế hệ thống hướng đối tượng
- Công nghệ chính: Laravel 11, PHP, Blade, MySQL, HTML, CSS, JavaScript, Vite
- Mục tiêu tài liệu: phục vụ Chương 2 - Phân tích và thiết kế hệ thống trong báo cáo ĐATN

Yêu cầu bắt buộc:
1. Trước khi viết, phải inspect code thật trong repo, tối thiểu gồm:
   - `DOCUMENTATION_TEMPLATE.md`
   - `composer.json`
   - `package.json`
   - `routes/web.php`
   - `routes/api.php` nếu có
   - các file route được require/include thêm nếu có
   - `app/Http/Controllers`
   - `app/Models`
   - `app/Application` nếu có
   - `app/Infrastructure` nếu có
   - `database/migrations`
   - `resources/views`

2. Chỉ mô tả các chức năng, actor, route, controller, model, bảng dữ liệu và luồng nghiệp vụ thật sự có trong code.
   - Không tự bịa công nghệ.
   - Không tự bịa chức năng.
   - Không mô tả tích hợp thanh toán thật nếu trong code chỉ có mock/disabled.
   - Nếu một thông tin chưa xác định rõ từ code, phải ghi rõ: “chưa xác định từ code”.

3. Bám theo mục “Graduation Thesis Report Guide” trong `DOCUMENTATION_TEMPLATE.md`.
   - Văn phong phải phù hợp báo cáo ĐATN.
   - Nội dung tập trung vào phân tích và thiết kế hệ thống.
   - Không đi quá sâu vào code triển khai chi tiết.
   - Tuy nhiên khi nhắc tên file, class, route, table, command, middleware, service thì phải dùng backtick.

4. Tạo sơ đồ bằng Mermaid hoặc PlantUML để có thể copy trực tiếp vào báo cáo.
   - Ưu tiên Mermaid nếu phù hợp.
   - Mỗi sơ đồ phải có code block đầy đủ.
   - Mỗi sơ đồ phải có tiêu đề riêng.
   - Mỗi sơ đồ phải có mô tả mục đích.
   - Mỗi sơ đồ phải có phần giải thích các thành phần chính.
   - Nếu sơ đồ có actor/use case/class/entity/route thì tên phải bám theo code thật trong dự án.

5. Lưu toàn bộ kết quả vào:
   - `docs/diagrams.md`

Yêu cầu về cấu trúc file `docs/diagrams.md`:
- Mở đầu tài liệu:
  - Nêu mục đích của tài liệu sơ đồ.
  - Nêu phạm vi phân tích.
  - Nêu các nguồn code đã inspect.
  - Nêu nguyên tắc: chỉ mô tả theo code thật, phần chưa rõ ghi “chưa xác định từ code”.

Với mỗi sơ đồ, trình bày theo cấu trúc sau:

## [Số thứ tự]. [Tên sơ đồ]

**Mục đích:**
Mô tả sơ đồ này dùng để làm gì trong báo cáo ĐATN.

**Cơ sở từ code:**
Liệt kê ngắn gọn các file/class/route/model/migration liên quan, ví dụ:
- `routes/web.php`
- `routes/web/frontend.php`
- `routes/web/admin.php`
- `app/Http/Controllers/...`
- `app/Models/...`
- `database/migrations/...`

**Sơ đồ:**
```mermaid
...
```

**Giải thích:**
Giải thích ngắn gọn các actor, module, class, entity, route hoặc bước xử lý chính trong sơ đồ.

**Ghi chú/ràng buộc:**
Nếu có điểm chưa xác định rõ từ code thì ghi rõ “chưa xác định từ code”.

Danh sách sơ đồ cần tạo:

1. Use Case Diagram tổng quát
   - Xác định các actor thật từ hệ thống, ví dụ: khách vãng lai, khách hàng đã đăng nhập, quản trị viên.
   - Xác định các nhóm chức năng thật từ route/controller/view.
   - Không thêm actor hoặc chức năng không có trong code.
   - Thể hiện quan hệ include/extend nếu xác định được từ luồng code.

2. Use Case Diagram cho storefront
   - Tập trung vào người dùng phía website bán hàng.
   - Gồm các chức năng như xem trang chủ, xem danh mục, xem sản phẩm, tìm kiếm, xem chi tiết sản phẩm, giỏ hàng, checkout, lịch sử đơn hàng nếu có trong code.
   - Phân biệt chức năng cần đăng nhập và không cần đăng nhập nếu route/middleware thể hiện rõ.

3. Use Case Diagram cho admin
   - Tập trung vào quản trị viên.
   - Bám theo route admin, controller admin và middleware quyền hạn.
   - Thể hiện các nhóm quyền/quản lý thật có trong code, ví dụ quản lý sản phẩm, đơn hàng, coupon, nội dung, người dùng, vai trò nếu có.

4. Activity Diagram cho luồng mua hàng
   - Mô tả từ lúc người dùng xem sản phẩm, thêm vào giỏ hàng, cập nhật giỏ hàng, nhập thông tin giao hàng, xác nhận đơn hàng.
   - Thể hiện điều kiện giỏ hàng rỗng, chưa đăng nhập, coupon nếu có.
   - Không mô tả thanh toán online thật nếu code chưa hỗ trợ thật.

5. Activity Diagram cho luồng checkout
   - Tập trung chi tiết hơn vào quá trình checkout.
   - Bám theo controller/service checkout thật.
   - Thể hiện session cart, thông tin shipping, coupon, tạo order, order item, payment nếu code có.

6. Activity Diagram cho luồng quản trị sản phẩm
   - Mô tả quản trị viên vào trang sản phẩm, thêm/sửa/xóa/cập nhật trạng thái sản phẩm nếu code có.
   - Thể hiện kiểm tra đăng nhập admin và quyền nếu middleware có.

7. Activity Diagram cho luồng quản trị đơn hàng
   - Mô tả xem danh sách đơn hàng, xem chi tiết, cập nhật trạng thái, hủy/xử lý đơn nếu có.
   - Nếu trạng thái đơn hàng không đầy đủ hoặc không rõ, ghi “chưa xác định từ code”.

8. Activity Diagram cho quản lý coupon
   - Chỉ tạo nếu trong code có route/controller/model/migration coupon.
   - Mô tả tạo/cập nhật/xóa/áp dụng coupon theo code thật.

9. Activity Diagram cho quản lý nội dung/CMS
   - Chỉ tạo nếu code có module blog, banner, page, video hoặc content management.
   - Không tự thêm module CMS nếu code không có.

10. Sequence Diagram cho luồng thêm vào giỏ hàng
    - Thể hiện tương tác giữa User/Browser, route, controller, service/session, model nếu có.
    - Bám theo route thêm giỏ hàng thật.
    - Nếu cart lưu session thì thể hiện rõ session.

11. Sequence Diagram cho luồng đặt hàng
    - Thể hiện Browser, CheckoutController, CheckoutService, database/tables liên quan.
    - Thể hiện tạo order, order items, shipping, payment nếu code có.
    - Thể hiện clear cart/session sau khi đặt hàng nếu code có.

12. Sequence Diagram cho luồng đăng nhập khách hàng
    - Chỉ mô tả nếu route/controller auth frontend có trong code.
    - Thể hiện kiểm tra credentials, session/auth guard nếu xác định được.

13. Sequence Diagram cho luồng đăng nhập admin
    - Bám theo route admin login và middleware admin thật.
    - Thể hiện điều kiện đăng nhập thành công/thất bại.

14. Sequence Diagram cho quản lý sản phẩm admin
    - Thể hiện Admin, Controller, Model/Service, Database.
    - Bám theo controller admin sản phẩm thật.

15. Sequence Diagram cho hủy đơn hàng
    - Chỉ tạo nếu code có route/controller hủy đơn.
    - Thể hiện người dùng gửi yêu cầu hủy, controller xử lý, cập nhật trạng thái đơn nếu có.

16. Sequence Diagram cho chatbot
    - Chỉ tạo nếu code có route/controller/service chatbot.
    - Thể hiện Browser, ChatbotController/API, service, dữ liệu liên quan.
    - Nếu nguồn dữ liệu/AI provider chưa xác định, ghi rõ “chưa xác định từ code”.

17. Class Diagram mức phân tích
    - Dựa trên model, controller, service chính trong repo.
    - Không cần vẽ mọi class trong Laravel framework.
    - Tập trung vào các lớp nghiệp vụ chính như product, category, brand, cart/checkout, order, user, coupon nếu có.
    - Thể hiện quan hệ association/aggregation/composition nếu xác định được từ model/migration.
    - Nếu project có cả legacy model và infrastructure model thì ghi rõ trong giải thích.

18. Class Diagram cho module checkout/order
    - Tập trung vào các class/controller/service/model liên quan đến đặt hàng.
    - Thể hiện trách nhiệm của từng class ở mức phân tích.

19. Class Diagram cho module admin/product
    - Tập trung vào quản lý sản phẩm, danh mục, thương hiệu, hình ảnh nếu có.

20. ERD tổng quát
    - Dựa trên `database/migrations` và quan hệ trong `app/Models`.
    - Thể hiện các bảng chính, khóa chính, khóa ngoại và quan hệ.
    - Không tự thêm bảng không có migration/model.
    - Nếu quan hệ chỉ suy ra từ tên cột nhưng không có foreign key rõ, ghi chú là suy luận từ migration/model.

21. ERD cho bán hàng/order
    - Tập trung vào user/customer, orders, order_items, products, shipping, payments, coupons nếu có.
    - Thể hiện quan hệ một-nhiều/nhiều-một rõ ràng.

22. ERD cho catalog sản phẩm
    - Tập trung vào products, categories, brands, product images, attributes nếu có.
    - Chỉ dùng bảng thật trong migration.

23. State Diagram trạng thái đơn hàng
    - Dựa trên các trạng thái đơn hàng thật có trong code, controller, enum, constant hoặc dữ liệu migration/seeder nếu có.
    - Nếu trạng thái không được chuẩn hóa rõ, ghi “chưa xác định từ code”.

24. Component Diagram kiến trúc Laravel
    - Thể hiện các thành phần: routes, controllers, middleware, services/application layer nếu có, models/ORM, views, database.
    - Bám theo cấu trúc thư mục thật trong repo.
    - Không thêm công nghệ như Redis, queue, Elasticsearch nếu code không có.

25. Deployment Diagram mức phân tích
    - Mô tả triển khai logic của hệ thống Laravel: browser, web server/app server, Laravel app, MySQL, Vite-built assets.
    - Nếu môi trường production cụ thể chưa xác định từ code, ghi “chưa xác định từ code”.
    - Không bịa Docker/cloud nếu repo không có.

26. DFD mức ngữ cảnh
    - Thể hiện các tác nhân ngoài hệ thống và luồng dữ liệu chính: người dùng, quản trị viên, hệ thống website, cơ sở dữ liệu.
    - Nếu có thanh toán mock/disabled thì thể hiện là “chưa tích hợp thanh toán thật” hoặc “payment disabled/mock”.

27. DFD mức 1 cho luồng mua hàng
    - Thể hiện các tiến trình: xem sản phẩm, quản lý giỏ hàng, checkout, tạo đơn hàng, lưu dữ liệu.
    - Bám theo controller/service/table thật.

28. Sơ đồ phân rã module hệ thống
    - Nhóm các module thật trong code: storefront, admin, auth, catalog, cart/checkout, order, coupon, content/blog, chatbot, API nếu có.
    - Không thêm module không có trong code.

29. Sơ đồ route-controller-view chính
    - Map các route chính tới controller và view tương ứng nếu xác định được.
    - Nếu một route dùng closure hoặc view chưa xác định thì ghi rõ.

30. Sơ đồ middleware/phân quyền
    - Thể hiện middleware thật trong route, ví dụ `auth`, `guest`, `admin.access`, `admin.permission` nếu có.
    - Thể hiện nhóm quyền admin thật nếu có constant/config.

31. Sơ đồ API route group
    - Chỉ tạo nếu `routes/api.php` có route.
    - Thể hiện nhóm public API, protected API, admin API nếu có.
    - Ghi rõ middleware như `auth:sanctum`, `admin.access:sanctum` nếu code có.

32. Sơ đồ sitemap/giao diện người dùng
    - Dựa trên `resources/views` và route frontend/admin.
    - Thể hiện các màn hình chính: trang chủ, danh sách sản phẩm, chi tiết sản phẩm, giỏ hàng, checkout, tài khoản, lịch sử đơn, admin dashboard nếu có.

Yêu cầu chất lượng nội dung:
- Mỗi sơ đồ phải giải thích đủ để người đọc báo cáo hiểu được:
  - Ai sử dụng chức năng?
  - Chức năng dùng để làm gì?
  - Dữ liệu nào được đọc/ghi?
  - Thành phần code nào chịu trách nhiệm?
  - Điểm nào chưa xác định từ code?
- Không chỉ đưa code sơ đồ trống hoặc quá ngắn.
- Không dùng tên chung chung như “System”, “Database”, “Controller” nếu có thể thay bằng tên thật từ code.
- Không đưa các quan hệ quá chắc chắn nếu code không chứng minh được.
- Nếu một sơ đồ không thể tạo vì thiếu dữ liệu trong code, vẫn tạo mục tương ứng và ghi rõ lý do chưa xác định từ code.

Yêu cầu kiểm tra sau khi viết:
- Kiểm tra file `docs/diagrams.md` có đủ sơ đồ.
- Kiểm tra tất cả code block Mermaid/PlantUML đều đóng đúng.
- Chạy `git diff --check -- docs/diagrams.md` nếu có thể.
- Cuối cùng báo cáo lại:
  - Đã tạo bao nhiêu sơ đồ.
  - File đã lưu ở đâu.
  - Những phần nào chưa xác định từ code.
  - Có chạy kiểm tra được không.
````
