# Laravel Shop – Định Hướng Kiến Trúc Mới

## 1. Tầm nhìn

Mục tiêu là tái cấu trúc dự án cũ (Laravel 11) thành kiến trúc rõ ràng, dễ bảo trì, dễ mở rộng. Hệ thống được chia thành các lớp:

- **Presentation**: routes, controller, request/response.
- **Application**: service, job, event, sử dụng domain để xử lý use case.
- **Domain**: entity, data transfer object, rule nghiệp vụ thuần PHP.
- **Infrastructure**: repository, gateway, tích hợp dịch vụ (payment, mail, cache).

Các thành phần phụ trợ (config, helper, policy, exception) được gom vào Shared layer.

## 2. Sơ đồ module logic

```
┌─────────────────────────────────────────────┐
│                 Presentation                 │
│  (Controllers, Requests, Resources, Routes)  │
└───────────────▲───────────────┬─────────────┘
                │               │
                │   Delegates   │
                │               │
┌───────────────┴───────────────▼─────────────┐
│                 Application                 │
│  Services, UseCases, Jobs, Events, Policies │
└───────────────▲───────────────┬─────────────┘
                │   Depends on   │
┌───────────────┴───────────────▼─────────────┐
│                   Domain                    │
│    Entities, ValueObjects, Business Rules   │
└────────────────▲───────────────┬────────────┘
                 │    Contract    │
┌────────────────┴───────────────▼────────────┐
│                Infrastructure                │
│ Repositories, Eloquent Models, Gateways,…   │
└──────────────────────────────────────────────┘
```

## 3. Cấu trúc thư mục đề xuất

```
app/
├── Domain/
│   ├── Catalog/           # Product, Category, Brand entity & rule
│   ├── Customer/
│   ├── Order/
│   ├── Marketing/
│   ├── CMS/
│   └── Shared/            # ValueObject, Enum, Contract chung
├── Application/
│   ├── Catalog/
│   │   ├── Services/
│   │   ├── UseCases/
│   │   └── DTO/
│   ├── Checkout/
│   ├── Marketing/
│   ├── Reporting/
│   └── Shared/
├── Infrastructure/
│   ├── Persistence/
│   │   ├── Eloquent/      # Model Eloquent mới (không tbl_*)
│   │   └── Repositories/
│   ├── Payment/
│   ├── Notification/
│   ├── Filesystem/
│   └── Auth/
├── Http/
│   ├── Controllers/
│   │   ├── Frontend/
│   │   ├── Admin/
│   │   └── Api/
│   ├── Requests/
│   ├── Resources/
│   └── Middleware/
├── Policies/
├── Providers/
└── Support/               # helper, formatting, enumerations

bootstrap/
config/
database/
├── migrations/
├── seeders/
└── factories/
routes/
├── web.php                # Load file con
├── web/frontend.php
├── web/admin.php
└── api.php
resources/
├── views/
│   ├── frontend/
│   ├── admin/
│   └── components/
└── js/
    ├── pages/
    ├── components/
    └── stores/
tests/
├── Feature/
│   ├── Frontend/
│   ├── Admin/
│   └── Api/
└── Unit/
```

### Lưu ý đặt tên
- Bảng DB: snake_case không prefix `tbl_`, ví dụ `products`, `product_categories`.
- Model Eloquent đặt trong `Infrastructure/Persistence/Eloquent`.
- Interface repository khai báo trong `Domain\*\Contracts`.
- DTO và Request sử dụng PascalCase, property camelCase.

## 4. Phân rã module chính

| Module      | Phạm vi chính                                                                 | Tích hợp |
|-------------|-------------------------------------------------------------------------------|----------|
| **Catalog** | Sản phẩm, danh mục, thương hiệu, thuộc tính, gallery                          | Storage |
| **Checkout**| Giỏ hàng, phí vận chuyển, thanh toán, đơn hàng                                | Payment, Shipping |
| **Customer**| Tài khoản khách, địa chỉ, loyalty, lịch sử điểm                               | Auth, Notification |
| **Marketing**| Segment, campaign, template, coupon, automation                              | Mail/SMS/Push |
| **CMS**     | Bài viết, slider, đối tác, trang tĩnh                                         | Media |
| **Reporting**| KPI dashboard, thống kê đơn hàng, marketing, loyalty                         | Cache |

Shared module cung cấp enum (OrderStatus, PaymentChannel), helper format tiền, thời gian.

## 5. Quy tắc route & guard

- `routes/web/frontend.php`: user-facing, middleware `web`, `setLocale`.
- `routes/web/admin.php`: admin dashboard, middleware `web`, `auth:admin`, `role:...`.
- `routes/api.php`: REST/JSON, middleware `api`, rate limit, JWT/Passport nếu mở rộng.
- Web route chính chỉ include các file con để dễ bảo trì.

## 6. Controller & Request

- Mỗi controller nên mỏng: inject service, trả Resource/Response.
- Validation nằm tại `app/Http/Requests/<Module>/<Action>Request.php`.
- Response JSON dùng Resource/Collection; view trả blade component/UI.
- Áp dụng policy/gate ngay trong controller hoặc middleware.

## 7. Service & Repository

- Service xử lý use-case (đặt hàng, cập nhật tồn kho, tạo campaign).
- Repository interface trong Domain, implement Eloquent ở Infrastructure để dễ test/mock.
- Payment/Notification gateway theo pattern Strategy (adapter).
- Queue job đặt trong `app/Application/*/Jobs`.

## 8. Event & Job

- Đặt event trong `app/Application/<Module>/Events`.
- Listener/Job trong cùng module, đăng ký tại `EventServiceProvider`.
- Sử dụng queue cho công việc dài (gửi mail, tính KPI).

## 9. Coding guideline

- Bắt buộc Laravel Pint & PHPStan ở mức baseline.
- DocBlock rõ ràng cho service/repository.
- Đặt test tương ứng mỗi use-case.
- Comment ngắn gọn khi logic phức tạp; ưu tiên code tự giải thích.

## 10. Bước tiếp theo

1. Chuẩn hóa schema (migration + model Eloquent mới).
2. Migrating dữ liệu cũ sang schema mới.
3. Refactor module Catalog làm mẫu (controller → service → repository).
4. Áp dụng tương tự cho Checkout, Customer…

Tài liệu này là nền để triển khai các task refactor tiếp theo.
