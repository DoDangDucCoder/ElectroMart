# FINAL DEMO CHECKLIST

## 1. Thong tin ban nop

- Branch hien tai: `fix/qa-report-final`
- Commit cuoi truoc khi dong bang: `dad68651 fix: brand logo upload and province-level shipping fee fallback`
- Ngay dong bang: 2026-05-22
- Database backup: `C:\Users\Admin\Desktop\shop\db_backups\shopbanhang_final_demo_20260522_021753.sql`
- Moi truong chay: Laravel 11, PHP 8.2 XAMPP, MariaDB/MySQL local, URL mac dinh `http://localhost:8000`.
- Lenh da chay truoc khi dong bang: `php artisan optimize:clear`, `php artisan route:list`, `php artisan migrate:status`.

## 2. Tai khoan demo

- Admin demo: `admin@shop.com` - Super Admin.
- Customer demo: `dodangduc@gmail.com` - DO DANG DUC.
- Coupon demo: `SAVE50K` - Tiet kiem 50k.
- San pham nen dung khi demo: `iPhone 16 Pro Max 256GB` - slug `iphone-16-pro-max-256gb`.
- Don hang demo neu can: `WVHJ4SQJCL` - trang thai hien tai `pending`.
- Khong ghi mat khau that trong file nay neu nop cong khai.

## 3. Kich ban demo de xuat

1. Mo trang chu.
2. Vao danh sach san pham.
3. Loc, tim kiem, sap xep san pham.
4. Mo chi tiet san pham.
5. Them vao gio.
6. Vao gio hang.
7. Chon san pham thanh toan.
8. Checkout COD.
9. Kiem tra phi van chuyen.
10. Ap dung coupon.
11. Dat hang.
12. Xem trang dat hang thanh cong.
13. Vao tai khoan khach xem lich su don.
14. Vao admin xem don hang.
15. Admin cap nhat trang thai don.
16. Khach hang xem trang thai moi.
17. Admin them/sua san pham.
18. Admin them/sua thuong hieu co logo.
19. Admin quan ly coupon, phi van chuyen, bai viet.

## 4. Chuc nang da kiem tra pass

- Storefront.
- Product detail.
- Cart.
- Checkout.
- Shipping fee.
- Coupon.
- Customer account.
- Wishlist.
- Admin product CRUD.
- Admin category CRUD.
- Admin brand CRUD.
- Admin coupon CRUD.
- Admin shipping CRUD.
- Admin order update.
- Admin customer lock/unlock.
- Blog/Post CRUD.
- Contact.
- Permission.
- Responsive/console.

## 5. Luu y khi demo

- Khong demo payment gateway that neu VNPay/MoMo/Stripe chua cau hinh production.
- Neu co phan "sap ra mat" thi noi ro day la module mo rong.
- Khong chay lai seed hoac migration fresh khi demo.
- Khong xoa du lieu that.
- Neu tao du lieu demo, dung tien to `QA_TEST_`.
- Truoc khi trinh bay, tu tay bam lai 1 luong duy nhat: san pham -> gio hang -> checkout -> admin cap nhat don.

## 6. Ket luan

- Du an da san sang demo/cham o muc do do an tot nghiep sau cac vong QA va fix.
- Rui ro nho con lai: cac cong thanh toan online neu chua co credential production thi chi nen gioi thieu, khong demo giao dich that.
- Can merge branch hien tai vao `main` neu `main` la branch nop bai/cham diem cua nhom.
