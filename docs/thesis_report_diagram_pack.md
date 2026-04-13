# BỘ BIỂU ĐỒ BỔ SUNG CHO CHƯƠNG 3

Tài liệu này bổ sung thêm các biểu đồ còn thiếu để phần phân tích và thiết kế nhìn đầy hơn trong báo cáo đồ án. Nếu chỉ dùng các file trước, phần biểu đồ đã có kiến trúc tổng thể và một số sequence hoặc activity quan trọng, nhưng vẫn còn thiếu nhóm biểu đồ tổng quan như use case và ERD trực quan.

Bộ biểu đồ dưới đây được viết theo hướng dễ chuyển sang hình để chèn vào Word. Anh có thể giữ nguyên mã Mermaid ở phụ lục hoặc dựng thành hình bằng Mermaid Live Editor.

---

# 3.x. BIỂU ĐỒ USE CASE TỔNG QUÁT CỦA HỆ THỐNG

```mermaid
flowchart LR
    Customer[Khach hang]
    Admin[Quan tri vien]
    Social[Dich vu dang nhap xa hoi]
    Payment[Cong thanh toan]
    Chatbot[Chatbot ho tro]

    subgraph SYS[He thong website thuong mai dien tu Laravel 11]
        UC1((Xem va tim kiem san pham))
        UC2((Quan ly gio hang va dat hang))
        UC3((Dang ky dang nhap va theo doi don hang))
        UC4((Binh luan danh gia va so sanh san pham))
        UC5((Quan ly catalog noi dung va media))
        UC6((Quan ly don hang coupon va delivery))
        UC7((Quan ly nguoi dung vai tro va quyen))
        UC8((Xem bao cao va thong ke))
        UC9((Ho tro khach hang qua chat inbox))
    end

    Customer --- UC1
    Customer --- UC2
    Customer --- UC3
    Customer --- UC4
    Customer --- UC9

    Admin --- UC5
    Admin --- UC6
    Admin --- UC7
    Admin --- UC8
    Admin --- UC9

    Social --- UC3
    Payment --- UC2
    Chatbot --- UC9
```

**Mô tả dưới hình:**  
Biểu đồ use case tổng quát thể hiện hai tác nhân trung tâm của hệ thống là khách hàng và quản trị viên. Khách hàng chủ yếu tương tác với storefront để xem sản phẩm, tìm kiếm, so sánh, giỏ hàng, thanh toán và theo dõi đơn hàng. Quản trị viên tương tác với khu vực admin để quản lý catalog, nội dung, đơn hàng, người dùng, quyền hạn và báo cáo. Ngoài ra, hệ thống còn liên kết với các tác nhân hỗ trợ như dịch vụ đăng nhập xã hội, cổng thanh toán và chatbot.

---

# 3.x. BIỂU ĐỒ USE CASE PHÍA KHÁCH HÀNG

```mermaid
flowchart LR
    Customer[Khach hang]
    Social[Google Facebook Login]
    Payment[PayPal VNPay MoMo]
    Chatbot[Chatbot]

    subgraph STOREFRONT[Phan he storefront]
        UC1((Xem danh muc va chi tiet san pham))
        UC2((Tim kiem va goi y tim kiem))
        UC3((So sanh san pham))
        UC4((Binh luan va danh gia))
        UC5((Them cap nhat xoa gio hang))
        UC6((Ap dung coupon))
        UC7((Dang ky dang nhap dang xuat))
        UC8((Dang nhap xa hoi))
        UC9((Checkout va xac nhan don hang))
        UC10((Thanh toan))
        UC11((Xem lich su va huy don hang))
        UC12((Tro chuyen ho tro))
    end

    Customer --- UC1
    Customer --- UC2
    Customer --- UC3
    Customer --- UC4
    Customer --- UC5
    Customer --- UC6
    Customer --- UC7
    Customer --- UC8
    Customer --- UC9
    Customer --- UC10
    Customer --- UC11
    Customer --- UC12

    Social --- UC8
    Payment --- UC10
    Chatbot --- UC12
```

**Mô tả dưới hình:**  
Biểu đồ use case phía khách hàng mô tả các nghiệp vụ chính trên storefront. Người dùng có thể duyệt sản phẩm, tìm kiếm, so sánh, thêm vào giỏ hàng, áp dụng mã giảm giá, đăng nhập theo nhiều cách, đi qua checkout, thanh toán và theo dõi lịch sử đơn hàng. Các tác nhân hỗ trợ như cổng thanh toán, dịch vụ đăng nhập xã hội và chatbot được gắn với các use case tương ứng, giúp sơ đồ bám sát hơn với thực tế triển khai của repo.

---

# 3.x. BIỂU ĐỒ USE CASE PHÍA QUẢN TRỊ VIÊN

```mermaid
flowchart LR
    Admin[Quan tri vien]

    subgraph ADMIN[Phan he admin]
        UC1((Dang nhap dashboard admin))
        UC2((Quan ly danh muc va thuong hieu))
        UC3((Quan ly san pham gallery document attribute))
        UC4((Quan ly bai viet video slider))
        UC5((Quan ly coupon don hang delivery))
        UC6((Quan ly users roles permissions))
        UC7((Quick search profile settings))
        UC8((Xem bao cao loi nhuan va discovery))
        UC9((Quan ly chat inbox ho tro))
    end

    Admin --- UC1
    Admin --- UC2
    Admin --- UC3
    Admin --- UC4
    Admin --- UC5
    Admin --- UC6
    Admin --- UC7
    Admin --- UC8
    Admin --- UC9
```

**Mô tả dưới hình:**  
Biểu đồ use case phía quản trị viên thể hiện rõ rằng khu vực admin không chỉ dừng ở CRUD sản phẩm. Ngoài catalog, hệ thống còn có quản lý nội dung, delivery, coupon, người dùng, vai trò, quyền truy cập, báo cáo và hộp thư chat hỗ trợ. Điều này phản ánh đúng hướng phát triển của repo như một hệ thống vận hành nội bộ tương đối hoàn chỉnh.

---

# 3.x. BIỂU ĐỒ PHÂN HỆ API

```mermaid
flowchart LR
    Guest[Guest Client]
    User[Authenticated Customer]
    Admin[Authenticated Admin]

    subgraph API[REST API]
        P1((Public API v1))
        P2((Protected API v1 auth:sanctum))
        P3((Admin API v1 admin.access:sanctum))
    end

    subgraph PUBLIC[Public endpoints]
        A1[products]
        A2[categories]
        A3[brands]
        A4[blog]
        A5[coupons]
        A6[settings]
        A7[health docs chatbot]
    end

    subgraph PROTECTED[Protected endpoints]
        B1[user profile]
        B2[cart]
        B3[orders]
        B4[reviews]
    end

    subgraph ADMINAPI[Admin endpoints]
        C1[products]
        C2[categories]
        C3[brands]
        C4[orders]
        C5[statistics]
    end

    Guest --> P1
    User --> P2
    Admin --> P3

    P1 --> A1
    P1 --> A2
    P1 --> A3
    P1 --> A4
    P1 --> A5
    P1 --> A6
    P1 --> A7

    P2 --> B1
    P2 --> B2
    P2 --> B3
    P2 --> B4

    P3 --> C1
    P3 --> C2
    P3 --> C3
    P3 --> C4
    P3 --> C5
```

**Mô tả dưới hình:**  
Biểu đồ phân hệ API mô tả ba lớp truy cập chính của hệ thống: public API, protected API và admin API. Cấu trúc này bám sát `routes/api.php` và cho thấy hệ thống không chỉ phục vụ giao diện web mà còn có một mặt dịch vụ riêng cho truy vấn dữ liệu, tích hợp và quản trị.

---

# 3.x. ERD TỔNG QUÁT CỦA SCHEMA CHUẨN HÓA

```mermaid
erDiagram
    BRANDS ||--o{ PRODUCTS : brands
    PRODUCT_CATEGORIES ||--o{ PRODUCTS : classifies
    PRODUCTS ||--o{ PRODUCT_VARIANTS : has
    PRODUCTS ||--o{ PRODUCT_MEDIA : has
    PRODUCTS ||--o{ PRODUCT_REVIEWS : receives
    CUSTOMERS ||--o{ CUSTOMER_ADDRESSES : owns
    CUSTOMERS ||--o{ ORDERS : places
    ORDERS ||--|{ ORDER_ITEMS : contains
    ORDERS ||--o| ORDER_SHIPPING : ships_by
    ORDERS ||--o{ PAYMENTS : has
    PAYMENTS ||--o{ PAYMENT_TRANSACTIONS : records
    COUPONS ||--o{ COUPON_REDEMPTIONS : redeemed
    ORDERS ||--o{ COUPON_REDEMPTIONS : applies
    CHAT_CONVERSATIONS ||--o{ CHAT_MESSAGES : contains

    BRANDS {
        bigint id PK
        string name
        string slug
        boolean is_active
    }

    PRODUCT_CATEGORIES {
        bigint id PK
        string name
        string slug
        bigint parent_id FK
    }

    PRODUCTS {
        bigint id PK
        bigint brand_id FK
        bigint primary_category_id FK
        string name
        string slug
        decimal price
        int stock_quantity
    }

    PRODUCT_VARIANTS {
        bigint id PK
        bigint product_id FK
        string sku
        decimal price
        int stock_quantity
    }

    PRODUCT_MEDIA {
        bigint id PK
        bigint product_id FK
        string file_path
        string collection
    }

    PRODUCT_REVIEWS {
        bigint id PK
        bigint product_id FK
        bigint customer_id FK
        int rating
        string status
    }

    CUSTOMERS {
        bigint id PK
        string name
        string email
        string phone
    }

    CUSTOMER_ADDRESSES {
        bigint id PK
        bigint customer_id FK
        string recipient_name
        string address_line1
    }

    ORDERS {
        bigint id PK
        bigint customer_id FK
        bigint customer_address_id FK
        string code
        string status
        decimal total
    }

    ORDER_ITEMS {
        bigint id PK
        bigint order_id FK
        bigint product_id FK
        int quantity
        decimal price
    }

    ORDER_SHIPPING {
        bigint id PK
        bigint order_id FK
        string recipient_name
        decimal shipping_fee
    }

    PAYMENTS {
        bigint id PK
        bigint order_id FK
        string provider
        string status
        decimal amount
    }

    PAYMENT_TRANSACTIONS {
        bigint id PK
        bigint payment_id FK
        string event
        string status
    }

    COUPONS {
        bigint id PK
        string code
        string type
        decimal value
    }

    COUPON_REDEMPTIONS {
        bigint id PK
        bigint coupon_id FK
        bigint order_id FK
        decimal discount_amount
    }

    CHAT_CONVERSATIONS {
        bigint id PK
        string channel
        string status
    }

    CHAT_MESSAGES {
        bigint id PK
        bigint conversation_id FK
        string role
        text content
    }
```

**Mô tả dưới hình:**  
ERD tổng quát của schema chuẩn hóa tập trung vào các nhóm dữ liệu trọng yếu của hệ thống như catalog, customer, order, payment, review và chat support. Sơ đồ này không thay thế hoàn toàn ERD hybrid, nhưng có giá trị lớn trong việc trình bày rõ hướng dữ liệu mà hệ thống đang chuẩn hóa tới.

---

# 3.x. BIỂU ĐỒ ÁNH XẠ DỮ LIỆU HYBRID GIỮA LEGACY VÀ SCHEMA MỚI

```mermaid
flowchart LR
    subgraph LEGACY[Legacy tables]
        L1[tbl_brand]
        L2[tbl_category_product]
        L3[tbl_product]
        L4[tbl_customers]
        L5[tbl_shipping]
        L6[tbl_order]
        L7[tbl_order_details]
        L8[tbl_payment]
        L9[tbl_coupon]
        L10[tbl_comment tbl_rating]
        L11[tbl_category_post]
        L12[tbl_posts]
    end

    subgraph MODERN[Schema moi]
        M1[brands]
        M2[product_categories]
        M3[products product_variants product_media]
        M4[customers]
        M5[customer_addresses order_shipping]
        M6[orders]
        M7[order_items]
        M8[payments payment_transactions]
        M9[coupons coupon_redemptions]
        M10[product_reviews]
        M11[blog_categories]
        M12[blog_posts]
    end

    L1 --> M1
    L2 --> M2
    L3 --> M3
    L4 --> M4
    L5 --> M5
    L6 --> M6
    L7 --> M7
    L8 --> M8
    L9 --> M9
    L10 --> M10
    L11 --> M11
    L12 --> M12
```

**Mô tả dưới hình:**  
Biểu đồ ánh xạ dữ liệu hybrid giúp người đọc nhìn trực tiếp sự chuyển đổi từ lớp bảng legacy sang lớp schema mới. Đây là biểu đồ rất hữu ích trong bối cảnh dự án hiện tại, vì nó cho thấy rõ hệ thống không phải chỉ còn legacy, cũng chưa hoàn toàn là greenfield chuẩn hóa, mà đang ở trạng thái chuyển đổi từng phần.

---

# 3.x. ACTIVITY DIAGRAM CHO NGHIỆP VỤ `POST /confirm-order`

```mermaid
flowchart TD
    A[Khach hang gui request xac nhan don hang] --> B[POST /confirm-order]
    B --> C[ConfirmOrderRequest validate du lieu]
    C --> D{Du lieu hop le?}
    D -- Khong --> E[Tra JSON 422]
    D -- Co --> F[CheckoutController kiem tra session customer]
    F --> G{Da dang nhap?}
    G -- Khong --> H[Tra JSON 401 yeu cau dang nhap]
    G -- Co --> I[CheckoutService.confirmOrder]
    I --> J[Mo transaction]
    J --> K[Lay cart items va tinh tong tien]
    K --> L[Tao order]
    L --> M[Tao order items]
    M --> N[Tao shipping payment neu can]
    N --> O[Commit transaction]
    O --> P[Tra JSON message va order_code]
```

**Mô tả dưới hình:**  
Activity diagram của nghiệp vụ xác nhận đơn hàng cho thấy checkout trong hệ thống không dừng ở render giao diện, mà còn có một bước xử lý backend khá rõ ràng. Sau khi request được kiểm tra hợp lệ và xác minh khách hàng đã đăng nhập, `CheckoutService` thực hiện quy trình tạo đơn, chi tiết đơn, dữ liệu giao hàng và thông tin thanh toán rồi mới trả về mã đơn hàng cho phía client.

---

# 3.x. ACTIVITY DIAGRAM CHO NGHIỆP VỤ TÌM KIẾM VÀ GỢI Ý TÌM KIẾM

```mermaid
flowchart TD
    A[Nguoi dung nhap tu khoa tren thanh tim kiem] --> B{Loai thao tac?}
    B -- Goi y nhanh --> C[POST /autocomplete-ajax]
    B -- Tim kiem day du --> D[GET hoac POST /tim-kiem]

    C --> E[AutocompleteProductsRequest validate query]
    E --> F[resolveSearchSuggestPayload]
    F --> G{Do dai tu khoa >= 2?}
    G -- Co --> H[ProductService.searchSuggestions]
    G -- Khong --> I[Bo qua goi y san pham]
    H --> J[Render partial autocomplete_results]
    I --> J
    J --> K[Tra ket qua goi y cho header]

    D --> L[SearchProductsRequest validate bo loc]
    L --> M[ProductService.searchCatalog]
    M --> N[ProductService.getRecommendedProducts]
    N --> O[ProductDiscoveryTrackingService.trackSearch]
    O --> P[Render trang pages.sanpham.search]
```

**Mô tả dưới hình:**  
Biểu đồ activity của nghiệp vụ tìm kiếm và gợi ý tìm kiếm thể hiện hai luồng song song trong storefront. Luồng thứ nhất là gợi ý động qua `POST /autocomplete-ajax`, dùng để trả về partial kết quả ngay trên header. Luồng thứ hai là tìm kiếm đầy đủ qua `/tim-kiem`, trong đó hệ thống không chỉ truy vấn dữ liệu mà còn kết hợp bộ lọc, recommendation và tracking hành vi tìm kiếm. Đây là nghiệp vụ quan trọng đối với website bán thiết bị công nghệ vì ảnh hưởng trực tiếp đến khả năng khám phá sản phẩm.

---

# 3.x. SEQUENCE DIAGRAM CHO NGHIỆP VỤ TÍNH PHÍ VẬN CHUYỂN

```mermaid
sequenceDiagram
    actor Customer as Khach hang
    participant Browser as Trinh duyet
    participant CheckoutController as CheckoutController
    participant FeeRequest as CalculateFeeRequest
    participant ShippingService as ShippingService

    Customer->>Browser: Chon tinh thanh quan huyen phuong xa
    Browser->>CheckoutController: POST /calculate-fee
    CheckoutController->>FeeRequest: validate(city_id, province_id, ward_id)
    FeeRequest-->>CheckoutController: Du lieu hop le
    CheckoutController->>ShippingService: calculateFee(cityId, provinceId, wardId)
    ShippingService-->>CheckoutController: fee
    CheckoutController->>ShippingService: storeFee(fee)
    CheckoutController-->>Browser: JSON(fee)
```

**Mô tả dưới hình:**  
Sơ đồ sequence của nghiệp vụ tính phí vận chuyển mô tả một luồng AJAX quan trọng trong checkout. Sau khi người dùng chọn các đơn vị hành chính phù hợp, `CalculateFeeRequest` kiểm tra dữ liệu đầu vào, rồi controller gọi `ShippingService` để tính phí tạm tính và lưu phí này vào trạng thái hiện hành của phiên mua hàng. Kết quả được trả về dưới dạng JSON để giao diện cập nhật tổng tiền mà không cần tải lại toàn bộ trang.

---

# 3.x. SEQUENCE DIAGRAM CHO NGHIỆP VỤ ĐĂNG NHẬP QUẢN TRỊ VIÊN

```mermaid
sequenceDiagram
    actor Admin as Quan tri vien
    participant Browser as Trinh duyet
    participant AuthController as AuthController
    participant AdminAuthenticator as AdminAuthenticator
    participant UserModel as User
    participant AdminAccess as AdminAccess
    participant AuthGuard as Auth Guard

    Admin->>Browser: Nhap email va mat khau admin
    Browser->>AuthController: POST /auth-login
    AuthController->>AuthController: validate(email, password, remember)
    AuthController->>AdminAuthenticator: resolveUser(email, password)
    AdminAuthenticator->>UserModel: Tim user theo email
    UserModel-->>AdminAuthenticator: User hoac null
    AdminAuthenticator-->>AuthController: User hop le hoac null
    alt Dang nhap hop le
        AuthController->>AuthGuard: login(user, remember)
        AuthController->>AdminAccess: canAccess(user)
        AdminAccess-->>AuthController: true hoac false
        alt Co quyen admin
            AuthController-->>Browser: Redirect /dashboard
        else Khong co quyen admin
            AuthController->>AuthGuard: logout()
            AuthController-->>Browser: Redirect /admin kem thong bao loi
        end
    else Sai thong tin dang nhap
        AuthController-->>Browser: Redirect /admin kem loi xac thuc
    end
```

**Mô tả dưới hình:**  
Sơ đồ sequence của nghiệp vụ đăng nhập quản trị cho thấy hệ thống không chỉ kiểm tra đúng sai của mật khẩu mà còn kiểm tra cả quyền truy cập vào admin. `AdminAuthenticator` chịu trách nhiệm giải mã logic xác thực, kể cả trường hợp mật khẩu legacy cần nâng cấp sang hash mới. Sau khi đăng nhập thành công, `AdminAccess` tiếp tục kiểm tra xem người dùng có thuộc nhóm vai trò được phép vào admin hay không. Điều này giúp hệ thống tăng tính an toàn và phù hợp với mô hình phân quyền theo vai trò.

---

# 3.x. SEQUENCE DIAGRAM CHO NGHIỆP VỤ CHATBOT STOREFRONT

```mermaid
sequenceDiagram
    actor Customer as Khach hang
    participant Browser as Trinh duyet
    participant ChatController as ChatController
    participant ConversationService as ChatbotConversationService
    participant ChatbotService as ChatbotService

    Customer->>Browser: Mo hop chat
    Browser->>ChatController: GET /chatbot/conversation
    ChatController->>ConversationService: bootstrap(customerId, sessionToken, storefront)
    ConversationService-->>ChatController: conversation
    ChatController->>ConversationService: recentMessages hoac messagesAfter
    ConversationService-->>ChatController: messages
    ChatController-->>Browser: JSON(conversation, messages)

    Customer->>Browser: Gui noi dung can ho tro
    Browser->>ChatController: POST /chatbot/messages
    ChatController->>ConversationService: bootstrap(...)
    ConversationService-->>ChatController: conversation
    ChatController->>ConversationService: recentMessages(8)
    ConversationService-->>ChatController: history
    ChatController->>ConversationService: storeUserMessage(message)
    ChatController->>ChatbotService: reply(message, customerId, ip, history)
    ChatbotService-->>ChatController: bot reply
    ChatController->>ConversationService: storeBotMessage(reply)
    ConversationService-->>ChatController: botMessage
    ChatController-->>Browser: JSON(conversation, user_message, bot_message)
```

**Mô tả dưới hình:**  
Sơ đồ sequence của chatbot storefront cho thấy hệ thống hỗ trợ hội thoại theo cách có trạng thái chứ không chỉ trả lời từng câu rời rạc. Khi người dùng mở hộp chat, controller bootstrap cuộc hội thoại và nạp các tin nhắn gần đây. Khi người dùng gửi nội dung mới, controller lưu tin nhắn của khách, lấy lịch sử hội thoại gần nhất rồi gọi `ChatbotService` để sinh phản hồi. Cuối cùng, cả tin nhắn người dùng và tin nhắn bot đều được lưu lại trong conversation service trước khi trả về phía trình duyệt.

---

# 3.x. BIỂU ĐỒ TRIỂN KHAI TỔNG THỂ

```mermaid
flowchart LR
    subgraph CLIENTS[Phia client]
        C1[Storefront Browser]
        C2[Admin Browser]
        C3[API Client]
    end

    subgraph APP[Laravel 11 Application]
        A1[Web Frontend Routes]
        A2[Web Admin Routes]
        A3[API Routes]
        A4[Controllers Middleware Support]
        A5[Application Services]
        A6[Persistence Layer]
    end

    subgraph DATA[Du lieu]
        D1[(Legacy Tables)]
        D2[(Normalized Schema)]
    end

    subgraph EXT[External services]
        E1[Google Facebook Login]
        E2[PayPal VNPay MoMo]
        E3[Email Workflow]
    end

    C1 --> A1
    C2 --> A2
    C3 --> A3

    A1 --> A4
    A2 --> A4
    A3 --> A4
    A4 --> A5
    A5 --> A6
    A6 --> D1
    A6 --> D2

    A4 --> E1
    A5 --> E2
    A5 --> E3
```

**Mô tả dưới hình:**  
Biểu đồ triển khai tổng thể cho thấy hệ thống có ba đầu vào chính là storefront browser, admin browser và API client. Tất cả các luồng này đều đi qua ứng dụng Laravel 11, trong đó các thành phần route, controller, middleware, support classes, service layer và persistence layer phối hợp với nhau để truy xuất dữ liệu ở cả lớp legacy và lớp schema chuẩn hóa mới. Đồng thời, hệ thống còn liên kết với các dịch vụ ngoài như đăng nhập xã hội, cổng thanh toán và email workflow.

---

# KẾT LUẬN CHO BỘ BIỂU ĐỒ

Với file này, phần biểu đồ của Chương 3 sẽ đầy hơn đáng kể. Kết hợp với file phân tích thiết kế đã có, anh hiện có:

- use case tổng quát
- use case storefront
- use case admin
- kiến trúc tổng thể
- phân hệ API
- ERD chuẩn hóa
- sơ đồ ánh xạ hybrid legacy sang schema mới
- sequence add-cart-ajax
- sequence login -> checkout redirect
- activity search và autocomplete
- sequence calculate-fee
- sequence admin login
- sequence chatbot storefront
- activity update product
- activity confirm-order
- deployment tổng thể

Mức này đã đủ chắc cho một Chương 3 đồ án. Nếu muốn dày thêm nữa, phần còn có thể mở rộng tiếp là biểu đồ search flow, chatbot flow và delivery fee flow.
