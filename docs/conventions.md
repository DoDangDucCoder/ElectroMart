# Coding Conventions

## Requests
- Tất cả controller action nhận input phải dùng FormRequest riêng, extends `App\Http\Requests\BaseFormRequest`.
- Tách riêng:
  - `rules()` khai báo validation.
  - `authorize()` nếu cần check policy/permission (mặc định true).
  - `attributes()` để tuỳ biến tên field cho thông báo lỗi.
- Đặt tên chuẩn: `<Module><Action>Request` (ví dụ `Catalog\CreateProductRequest`).
- Lưu trong `app/Http/Requests/<Module>/`.

## Resources
- API response trả qua Resource Collection, extends `App\Http\Resources\BaseResource`.
- Sử dụng method `toArray()` để map entity/DTO sang JSON.
- Sắp xếp key theo nhóm: `id`, `attributes`, `relationships`, `meta`.
- Collection dùng `Resource::collection($items)` hoặc custom `Collection` class.

## Service Layer
- Controller chỉ inject service (không truy cập thẳng model).
- Service đặt tại `app/Application/<Module>/Services`.
- Service nhận DTO từ FormRequest (`->validated()`) hoặc transformer.

## Repository
- Định nghĩa interface trong `App\Domain\<Module>\Contracts`.
- Implement trong `App\Infrastructure\Persistence\Repositories`.
- Không dùng Query Builder trực tiếp trong controller/service.

## Naming
- Routes frontend: đặt trong `routes/web/frontend.php`, admin: `routes/web/admin.php`.
- Route name dạng `module.action` (vd: `catalog.products.index`).
- Queue job đặt tại `app/Application/<Module>/Jobs`.

## Testing
- Viết Feature test cho từng endpoint mới (đặt trong `tests/Feature/<Module>`).
- Unit test cho service/repository.
- Dùng factory + seeder cho dữ liệu mẫu.

## Commit & Review Checklist
- Pint / PHPStan pass (`vendor/bin/pint`, `vendor/bin/phpstan analyse`).
- Không để credential trong code.
- Thêm entry changelog/docs nếu cần.
