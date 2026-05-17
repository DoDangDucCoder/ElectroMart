# Báo cáo audit H1 danh mục

- Ngày audit: 2026-05-10
- Phạm vi: tất cả active `/danh-muc/{slug}` và alias legacy đang map 301 trong `CategoryService::resolveStorefrontSlug`.
- Tiêu chí OK: active slug HTTP 200 và H1 DOM khớp `product_categories.name`; alias legacy HTTP 301 về slug target và trang target có H1 khớp title DB.

- Active slug kiểm tra: 257.
- Dòng cần chỉnh sửa còn lại: 0.
- Dòng layout cần kiểm tra còn lại: 0.

| Loại | Slug | Target | HTTP | Redirect | H1 hiện tại | Title DB | Trạng thái | Gợi ý sửa H1 | Layout |
|---|---|---|---:|---|---|---|---|---|---|
| Active | `may-inthiet-bi-van-phong` |  | 200 |  | Máy in/Thiết bị văn phòng | Máy in/Thiết bị văn phòng | OK | - | OK |
| Active | `routerthiet-bi-mang` |  | 200 |  | Router/Thiết bị mạng | Router/Thiết bị mạng | OK | - | OK |
| Active | `ban-phimchuotgaming-gear` |  | 200 |  | Bàn phím/Chuột/Gaming gear | Bàn phím/Chuột/Gaming gear | OK | - | OK |
| Active | `man-hinh` |  | 200 |  | Màn hình | Màn hình | OK | - | OK |
| Active | `dong-ho-thong-minh` |  | 200 |  | Đồng hồ thông minh | Đồng hồ thông minh | OK | - | OK |
| Active | `tai-ngheloa` |  | 200 |  | Tai nghe/Loa | Tai nghe/Loa | OK | - | OK |
| Active | `may-tinh-bang` |  | 200 |  | Máy tính bảng | Máy tính bảng | OK | - | OK |
| Active | `sac-cap` |  | 200 |  | Sạc & Cáp | Sạc & Cáp | OK | - | OK |
| Active | `laptop-gaming` |  | 200 |  | Laptop Gaming | Laptop Gaming | OK | - | OK |
| Active | `iphone` |  | 200 |  | iPhone | iPhone | OK | - | OK |
| Active | `dien-thoai` |  | 200 |  | Điện thoại | Điện thoại | OK | - | OK |
| Active | `op-lung-kinh-cuong-luc` |  | 200 |  | Ốp lưng & Kính cường lực | Ốp lưng & Kính cường lực | OK | - | OK |
| Active | `laptop-van-phong` |  | 200 |  | Laptop Văn phòng | Laptop Văn phòng | OK | - | OK |
| Active | `samsung-galaxy` |  | 200 |  | Samsung Galaxy | Samsung Galaxy | OK | - | OK |
| Active | `laptop` |  | 200 |  | Laptop | Laptop | OK | - | OK |
| Active | `android-khac` |  | 200 |  | Android khác | Android khác | OK | - | OK |
| Active | `phu-kien` |  | 200 |  | Phụ kiện | Phụ kiện | OK | - | OK |
| Active | `macbook-pro-m5` |  | 200 |  | Macbook Pro M5 | Macbook Pro M5 | OK | - | OK |
| Active | `man-hinh-msi-pro-mp251l-e2-245-fhd-1920-x-1080-ips-120hz-1-ms` |  | 200 |  | Màn hình Msi PRO MP251L E2 24.5" (FHD 1920 x 1080/ IPS/ 120Hz/ 1 ms) | Màn hình Msi PRO MP251L E2 24.5" (FHD 1920 x 1080/ IPS/ 120Hz/ 1 ms) | OK | - | OK |
| Active | `man-hinh-asus-vy249hgr-238-fhd-ips120hz-oc1ms` |  | 200 |  | Màn hình Asus VY249HGR 23.8” (FHD/ IPS/120Hz OC/1ms) | Màn hình Asus VY249HGR 23.8” (FHD/ IPS/120Hz OC/1ms) | OK | - | OK |
| Active | `man-hinh-acer-ek221q-e3-umwe1sv301-215-fhd-1920-x-1080-ips-100hz-1-ms` |  | 200 |  | Màn hình Acer EK221Q E3 (UM.WE1SV.301) 21.5" (FHD 1920 x 1080/ IPS/ 100Hz/ 1 ms) | Màn hình Acer EK221Q E3 (UM.WE1SV.301) 21.5" (FHD 1920 x 1080/ IPS/ 100Hz/ 1 ms) | OK | - | OK |
| Active | `camera-an-ninh` |  | 200 |  | Camera An ninh | Camera An ninh | OK | - | OK |
| Active | `apple-macbook-air-m2-136-inch-8cpu-8gpu-8gb-256gb` |  | 200 |  | Apple MacBook Air M2 13.6 inch (8CPU / 8GPU/ 8GB/ 256GB) | Apple MacBook Air M2 13.6 inch (8CPU / 8GPU/ 8GB/ 256GB) | OK | - | OK |
| Active | `apple-macbook-air-m2-136-inch-8cpu-8gpu-16gb-256gb` |  | 200 |  | Apple MacBook Air M2 13.6 inch (8CPU / 8GPU/ 16GB/ 256GB) | Apple MacBook Air M2 13.6 inch (8CPU / 8GPU/ 16GB/ 256GB) | OK | - | OK |
| Active | `apple-macbook-air-m2-153-inch-8cpu-10gpu-8gb-256gb` |  | 200 |  | Apple MacBook Air M2 15.3 inch (8CPU / 10GPU/ 8GB/ 256GB) | Apple MacBook Air M2 15.3 inch (8CPU / 10GPU/ 8GB/ 256GB) | OK | - | OK |
| Active | `laptop-dell-inspiron-14-5440-7fn5j-core-7-150u-16gb-1tb-win-11-home-sl-office` |  | 200 |  | Laptop Dell Inspiron 14 5440 - 7FN5J (Core 7 150U/ 16GB/ 1TB/ Win 11 Home SL + Office) | Laptop Dell Inspiron 14 5440 - 7FN5J (Core 7 150U/ 16GB/ 1TB/ Win 11 Home SL + Office) | OK | - | OK |
| Active | `laptop-hp-probook-440-g11-a74b5pt-ultra-5-125u-16gb-512gb-win-11-home` |  | 200 |  | Laptop HP Probook 440 G11 - A74B5PT (Ultra 5-125U/ 16GB/ 512GB/ Win 11 Home) | Laptop HP Probook 440 G11 - A74B5PT (Ultra 5-125U/ 16GB/ 512GB/ Win 11 Home) | OK | - | OK |
| Active | `laptop-msi-prestige-14-ai-studio-c1udxg-058vn-ultra-7-155h-geforce-rtx-3050-16gb-1tb-win-11-home` |  | 200 |  | Laptop Msi Prestige 14 AI Studio C1UDXG-058VN (Ultra 7-155H/ GeForce RTX™ 3050/ 16GB/ 1TB/ Win 11 Home) | Laptop Msi Prestige 14 AI Studio C1UDXG-058VN (Ultra 7-155H/ GeForce RTX™ 3050/ 16GB/ 1TB/ Win 11 Home) | OK | - | OK |
| Active | `laptop-lenovo-ideapad-slim-3-15irh10-83k1000gvn-core-i5-13420h-24gb-512gb-win-11-home-sl-english` |  | 200 |  | Laptop Lenovo IdeaPad Slim 3 15IRH10 - 83K1000GVN (Core i5-13420H/ 24GB/ 512GB/ Win 11 Home SL, English) | Laptop Lenovo IdeaPad Slim 3 15IRH10 - 83K1000GVN (Core i5-13420H/ 24GB/ 512GB/ Win 11 Home SL, English) | OK | - | OK |
| Active | `laptop-lenovo-yoga-slim-7-14ill10-oled-83jx0086vn-ultra-5-226v-16gb-512gb-win-11-home-office-microsoft` |  | 200 |  | Laptop Lenovo Yoga Slim 7 14ILL10 OLED - 83JX0086VN (Ultra 5-226V/ 16GB/ 512GB/ Win 11 Home + Office + Microsoft) | Laptop Lenovo Yoga Slim 7 14ILL10 OLED - 83JX0086VN (Ultra 5-226V/ 16GB/ 512GB/ Win 11 Home + Office + Microsoft) | OK | - | OK |
| Active | `laptop-asus-tuf-gaming-a14-fa401km-rg010ws-ryzen-ai-7-350-geforce-rtx-5060-32gb-1tb-win-11-home-office-microsoft` |  | 200 |  | Laptop Asus TUF Gaming A14 FA401KM-RG010WS (Ryzen AI 7 350/ GeForce RTX™ 5060/ 32GB/ 1TB/ Win 11 Home + Office + Microsoft) | Laptop Asus TUF Gaming A14 FA401KM-RG010WS (Ryzen AI 7 350/ GeForce RTX™ 5060/ 32GB/ 1TB/ Win 11 Home + Office + Microsoft) | OK | - | OK |
| Active | `laptop-lenovo-thinkpad-x9-14-gen-1-21qa006hvn-ultra-5-228v-32gb-1tb-win-11-pro` |  | 200 |  | Laptop Lenovo Thinkpad X9-14 Gen 1 - 21QA006HVN (Ultra 5-228V/ 32GB/ 1TB/ Win 11 Pro) | Laptop Lenovo Thinkpad X9-14 Gen 1 - 21QA006HVN (Ultra 5-228V/ 32GB/ 1TB/ Win 11 Pro) | OK | - | OK |
| Active | `may-in-canon` |  | 200 |  | Máy in Canon | Máy in Canon | OK | - | OK |
| Active | `apple-macbook-pro-m3-142-inch-8cpu-10gpu-8gb-512gb` |  | 200 |  | Apple MacBook Pro M3 14.2 inch (8CPU / 10GPU/ 8GB/ 512GB) | Apple MacBook Pro M3 14.2 inch (8CPU / 10GPU/ 8GB/ 512GB) | OK | - | OK |
| Active | `macbook-pro-m4-pro` |  | 200 |  | Macbook Pro M4 Pro | Macbook Pro M4 Pro | OK | - | OK |
| Active | `laptop-lenovo-ai` |  | 200 |  | Laptop Lenovo AI | Laptop Lenovo AI | OK | - | OK |
| Active | `laptop-dell-inspiron-5440-g14-n4i7204w1-core-7-150u-16gb-512gb-win-11-home-sl-office` |  | 200 |  | Laptop Dell Inspiron 5440 G14 - N4I7204W1 (Core 7 150U/ 16GB/ 512GB/ Win 11 Home SL + Office) | Laptop Dell Inspiron 5440 G14 - N4I7204W1 (Core 7 150U/ 16GB/ 512GB/ Win 11 Home SL + Office) | OK | - | OK |
| Active | `laptop-dell-inspiron-5440-g14-n4i5211w1-core-5-120u-16gb-512gb-win-11-home-sl-office` |  | 200 |  | Laptop Dell Inspiron 5440 G14 - N4I5211W1 (Core 5 120U/ 16GB/ 512GB/ Win 11 Home SL + Office) | Laptop Dell Inspiron 5440 G14 - N4I5211W1 (Core 5 120U/ 16GB/ 512GB/ Win 11 Home SL + Office) | OK | - | OK |
| Active | `laptop-16-inch` |  | 200 |  | Laptop 16 inch | Laptop 16 inch | OK | - | OK |
| Active | `laptop-lg-gram-book-15u50t-gav56a5-i5-1334u-16gb-512gb-win-11-home` |  | 200 |  | Laptop LG Gram Book 15U50T-G.AV56A5 (i5-1334U/ 16GB/ 512GB/ Win 11 Home) | Laptop LG Gram Book 15U50T-G.AV56A5 (i5-1334U/ 16GB/ 512GB/ Win 11 Home) | OK | - | OK |
| Active | `laptop-dell-inspiron-5441-5mnk1-snapdragon-x1p-64-100-16gb-512gb-win-11-home-sl-office` |  | 200 |  | Laptop Dell Inspiron 5441 - 5MNK1 (Snapdragon X1P 64 100/ 16GB/ 512GB/ Win 11 Home SL + Office) | Laptop Dell Inspiron 5441 - 5MNK1 (Snapdragon X1P 64 100/ 16GB/ 512GB/ Win 11 Home SL + Office) | OK | - | OK |
| Active | `ipad-air-cu` |  | 200 |  | iPad Air cũ | iPad Air cũ | OK | - | OK |
| Active | `arm-man-hinh` |  | 200 |  | Arm màn hình | Arm màn hình | OK | - | OK |
| Active | `laptop-acer-aspire-5-a514-56p-742f-nxkhrsv005-i7-1355u-16gb-512gb-win-11-home` |  | 200 |  | Laptop Acer Aspire 5 A514-56P-742F (NX.KHRSV.005) (i7-1355U/ 16GB/ 512GB/ Win 11 Home) | Laptop Acer Aspire 5 A514-56P-742F (NX.KHRSV.005) (i7-1355U/ 16GB/ 512GB/ Win 11 Home) | OK | - | OK |
| Active | `gia-do-dien-thoai` |  | 200 |  | Giá đỡ điện thoại | Giá đỡ điện thoại | OK | - | OK |
| Active | `laptop-acer-swift-go-ai-2024-gen-2-sfg14-73-71zx-ultra-7-155h-16gb-512gb-win-11-home-sl` |  | 200 |  | Laptop Acer Swift Go AI 2024 Gen 2 SFG14-73-71ZX (Ultra 7-155H/ 16GB/ 512GB/ Win 11 Home SL) | Laptop Acer Swift Go AI 2024 Gen 2 SFG14-73-71ZX (Ultra 7-155H/ 16GB/ 512GB/ Win 11 Home SL) | OK | - | OK |
| Active | `laptop-acer-swift-go-ai-oled-sfg14-73-75ym-ultra-7-155h-16gb-512gb-win-11-home-sl-english` |  | 200 |  | Laptop Acer Swift Go AI OLED SFG14-73-75YM (Ultra 7-155H/ 16GB/ 512GB/ Win 11 Home SL, English) | Laptop Acer Swift Go AI OLED SFG14-73-75YM (Ultra 7-155H/ 16GB/ 512GB/ Win 11 Home SL, English) | OK | - | OK |
| Active | `laptop-acer-swift-go-ai-oled-sfg14-73-57fz-ultra-5-125h-16gb-512gb-win-11-home-sl-english` |  | 200 |  | Laptop Acer Swift Go AI OLED SFG14-73-57FZ (Ultra 5-125H/ 16GB/ 512GB/ Win 11 Home SL, English) | Laptop Acer Swift Go AI OLED SFG14-73-57FZ (Ultra 5-125H/ 16GB/ 512GB/ Win 11 Home SL, English) | OK | - | OK |
| Active | `s25-series-cu` |  | 200 |  | S25 Series cũ | S25 Series cũ | OK | - | OK |
| Active | `laptop-msi-sword-16-hx-b14vekg-856vn-i7-14700hx-geforce-rtx-4050-16gb-1tb-win-11-home-sea` |  | 200 |  | Laptop Msi Sword 16 HX B14VEKG-856VN (i7-14700HX/ GeForce RTX™ 4050/ 16GB/ 1TB/ Win 11 Home SEA) | Laptop Msi Sword 16 HX B14VEKG-856VN (i7-14700HX/ GeForce RTX™ 4050/ 16GB/ 1TB/ Win 11 Home SEA) | OK | - | OK |
| Active | `laptop-lenovo-ideapad-slim-3-16irh10-83k20004vn-i7-13620h-24gb-512gb-win-11-home-sl-english` |  | 200 |  | Laptop Lenovo IdeaPad Slim 3 16IRH10 - 83K20004VN (i7-13620H/ 24GB/ 512GB/ Win 11 Home SL, English) | Laptop Lenovo IdeaPad Slim 3 16IRH10 - 83K20004VN (i7-13620H/ 24GB/ 512GB/ Win 11 Home SL, English) | OK | - | OK |
| Active | `laptop-lenovo-ideapad-slim-3-14irh10-83k0000bvn-i5-13420h-16gb-512gb-win-11-home-sl-english` |  | 200 |  | Laptop Lenovo IdeaPad Slim 3 14IRH10 - 83K0000BVN (i5-13420H/ 16GB/ 512GB/ Win 11 Home SL, English) | Laptop Lenovo IdeaPad Slim 3 14IRH10 - 83K0000BVN (i5-13420H/ 16GB/ 512GB/ Win 11 Home SL, English) | OK | - | OK |
| Active | `laptop-acer-aspire-5-a515-58p-9841-i9-13900h-16gb-512gb-win-11-home-sl` |  | 200 |  | Laptop Acer Aspire 5 A515-58P-9841 (i9-13900H/ 16GB/ 512GB/ Win 11 Home SL) | Laptop Acer Aspire 5 A515-58P-9841 (i9-13900H/ 16GB/ 512GB/ Win 11 Home SL) | OK | - | OK |
| Active | `laptop-hp-14-ep1007tu-9z2w1pa-core-7-150u-16gb-512gb-win-11-home` |  | 200 |  | Laptop HP 14-ep1007TU - 9Z2W1PA (Core 7 150U/ 16GB/ 512GB/ Win 11 Home) | Laptop HP 14-ep1007TU - 9Z2W1PA (Core 7 150U/ 16GB/ 512GB/ Win 11 Home) | OK | - | OK |
| Active | `chuot-cong-thai-hoc` |  | 200 |  | Chuột công thái học | Chuột công thái học | OK | - | OK |
| Active | `laptop-msi-katana-15-b13udxk-2410vn-core-i5-13420h-geforce-rtx-3050-16gb-512gb-win-11-home-sl` |  | 200 |  | Laptop Msi Katana 15 B13UDXK-2410VN (Core i5-13420H/ GeForce RTX™ 3050/ 16GB/ 512GB/ Win 11 Home SL) | Laptop Msi Katana 15 B13UDXK-2410VN (Core i5-13420H/ GeForce RTX™ 3050/ 16GB/ 512GB/ Win 11 Home SL) | OK | - | OK |
| Active | `laptop-asus-gaming-v16-v3607vm-rp044w-core-7-240h-geforce-rtx-5060-16gb-512gb-win-11-home` |  | 200 |  | Laptop Asus Gaming V16 V3607VM-RP044W (Core 7 240H/ GeForce RTX™ 5060/ 16GB/ 512GB/ Win 11 Home) | Laptop Asus Gaming V16 V3607VM-RP044W (Core 7 240H/ GeForce RTX™ 5060/ 16GB/ 512GB/ Win 11 Home) | OK | - | OK |
| Active | `laptop-lenovo-thinkbook-14-g8-ial-21sj0070vn-ultra-5-225h-16gb-512gb-win-11-home-sl-english` |  | 200 |  | Laptop Lenovo ThinkBook 14 G8 IAL - 21SJ0070VN (Ultra 5-225H/ 16GB/ 512GB/ Win 11 Home SL, English) | Laptop Lenovo ThinkBook 14 G8 IAL - 21SJ0070VN (Ultra 5-225H/ 16GB/ 512GB/ Win 11 Home SL, English) | OK | - | OK |
| Active | `laptop-asus-vivobook-15-oled-a1505va-l1688w-i7-13620h-16gb-512gb-win-11-home` |  | 200 |  | Laptop Asus Vivobook 15 OLED A1505VA-L1688W (i7-13620H/ 16GB/ 512GB/ Win 11 Home) | Laptop Asus Vivobook 15 OLED A1505VA-L1688W (i7-13620H/ 16GB/ 512GB/ Win 11 Home) | OK | - | OK |
| Active | `laptop-lenovo-thinkpad-x9-14-gen-1-21qa006kvn-ultra-7-258v-32gb-1tb-win-11-pro` |  | 200 |  | Laptop Lenovo ThinkPad X9-14 Gen 1 - 21QA006KVN (Ultra 7-258V/ 32GB/ 1TB/ Win 11 Pro) | Laptop Lenovo ThinkPad X9-14 Gen 1 - 21QA006KVN (Ultra 7-258V/ 32GB/ 1TB/ Win 11 Pro) | OK | - | OK |
| Active | `laptop-asus-tuf-gaming-f16-fx607vj-rl034w-core-5-210h-geforce-rtx-3050-16gb-512gb-win-11-home` |  | 200 |  | Laptop Asus TUF Gaming F16 FX607VJ-RL034W (Core 5 210H/ GeForce RTX™ 3050/ 16GB/ 512GB/ Win 11 Home) | Laptop Asus TUF Gaming F16 FX607VJ-RL034W (Core 5 210H/ GeForce RTX™ 3050/ 16GB/ 512GB/ Win 11 Home) | OK | - | OK |
| Active | `laptop-asus-gaming-v16-v3607vh-rp025w-core-7-240h-geforce-rtx-5050-16gb-512gb-win-11-home` |  | 200 |  | Laptop Asus Gaming V16 V3607VH-RP025W (Core 7 240H/ GeForce RTX™ 5050/ 16GB/ 512GB/ Win 11 Home) | Laptop Asus Gaming V16 V3607VH-RP025W (Core 7 240H/ GeForce RTX™ 5050/ 16GB/ 512GB/ Win 11 Home) | OK | - | OK |
| Active | `laptop-lenovo-ideapad-slim-3-16irh10-83k20001vn-i7-13620h-16gb-512gb-win-11-home-sl` |  | 200 |  | Laptop Lenovo IdeaPad Slim 3 16IRH10 - 83K20001VN (i7-13620H/ 16GB/ 512GB/ Win 11 Home SL) | Laptop Lenovo IdeaPad Slim 3 16IRH10 - 83K20001VN (i7-13620H/ 16GB/ 512GB/ Win 11 Home SL) | OK | - | OK |
| Active | `laptop-asus-gaming-v16-v3607vu-rp343w-core-5-210h-geforce-rtx-4050-16gb-512gb-win-11-home` |  | 200 |  | Laptop Asus Gaming V16 V3607VU-RP343W (Core 5 210H/ GeForce RTX™ 4050/ 16GB/ 512GB/ Win 11 Home) | Laptop Asus Gaming V16 V3607VU-RP343W (Core 5 210H/ GeForce RTX™ 4050/ 16GB/ 512GB/ Win 11 Home) | OK | - | OK |
| Active | `laptop-lenovo-legion-5-15irx10-83ly00a7vn-i7-14700hx-geforce-rtx-5050-16gb-512gb-win-11-home-sl-office` |  | 200 |  | Laptop Lenovo Legion 5 15IRX10 - 83LY00A7VN (i7-14700HX/ GeForce RTX™ 5050/ 16GB/ 512GB/ Win 11 Home SL + Office) | Laptop Lenovo Legion 5 15IRX10 - 83LY00A7VN (i7-14700HX/ GeForce RTX™ 5050/ 16GB/ 512GB/ Win 11 Home SL + Office) | OK | - | OK |
| Active | `laptop-msi-venturepro-16-ai-a1veg-003vn-ultra-7-155h-geforce-rtx-4050-16gb-512gb-win-11-home` |  | 200 |  | Laptop Msi VenturePro 16 AI A1VEG-003VN (Ultra 7-155H/ GeForce RTX™ 4050/ 16GB/ 512GB/ Win 11 Home) | Laptop Msi VenturePro 16 AI A1VEG-003VN (Ultra 7-155H/ GeForce RTX™ 4050/ 16GB/ 512GB/ Win 11 Home) | OK | - | OK |
| Active | `laptop-gigabyte-aorus-16x-asg-53vnc94sh-i7-14650hx-geforce-rtx-4070-16gb-1tb-win-11-home-sl` |  | 200 |  | Laptop Gigabyte AORUS 16X ASG-53VNC94SH (i7-14650HX/ GeForce RTX™ 4070/ 16GB/ 1TB/ Win 11 Home SL) | Laptop Gigabyte AORUS 16X ASG-53VNC94SH (i7-14650HX/ GeForce RTX™ 4070/ 16GB/ 1TB/ Win 11 Home SL) | OK | - | OK |
| Active | `laptop-lenovo-thinkpad-x9-14-gen-1-21qa006gvn-ultra-5-228v-32gb-1tb-win-11-pro` |  | 200 |  | Laptop Lenovo Thinkpad X9-14 Gen 1 - 21QA006GVN (Ultra 5-228V/ 32GB/ 1TB/ Win 11 Pro) | Laptop Lenovo Thinkpad X9-14 Gen 1 - 21QA006GVN (Ultra 5-228V/ 32GB/ 1TB/ Win 11 Pro) | OK | - | OK |
| Active | `laptop-lenovo-thinkpad-x9-14-gen-1-21qa006evn-ultra-5-226v-16gb-1tb-win-11-pro` |  | 200 |  | Laptop Lenovo Thinkpad X9-14 Gen 1 - 21QA006EVN (Ultra 5-226V/ 16GB/ 1TB/ Win 11 Pro) | Laptop Lenovo Thinkpad X9-14 Gen 1 - 21QA006EVN (Ultra 5-226V/ 16GB/ 1TB/ Win 11 Pro) | OK | - | OK |
| Active | `may-tinh-van-phong` |  | 200 |  | Máy tính văn phòng | Máy tính văn phòng | OK | - | OK |
| Active | `may-hut-bui-tineco` |  | 200 |  | Máy hút bụi Tineco | Máy hút bụi Tineco | OK | - | OK |
| Active | `nha-thong-minh` |  | 200 |  | Nhà thông minh | Nhà thông minh | OK | - | OK |
| Active | `man-hinh-dahua-cu` |  | 200 |  | Màn Hình Dahua cũ | Màn Hình Dahua cũ | OK | - | OK |
| Active | `ong-kinh-lens` |  | 200 |  | Ống kính (lens) | Ống kính (lens) | OK | - | OK |
| Active | `laptop-asus-tuf-gaming-f16-fx608jmr-rv048w-i7-14650hx-geforce-rtx-5060-16gb-1tb-win-11-home` |  | 200 |  | Laptop Asus TUF Gaming F16 FX608JMR-RV048W (i7-14650HX/ GeForce RTX™ 5060/ 16GB/ 1TB/ Win 11 Home) | Laptop Asus TUF Gaming F16 FX608JMR-RV048W (i7-14650HX/ GeForce RTX™ 5060/ 16GB/ 1TB/ Win 11 Home) | OK | - | OK |
| Active | `laptop-gigabyte-gaming-a16-cmhh2vn893sh-core-i5-13420h-geforce-rtx-4050-16gb-512gb-win-11-home-sl` |  | 200 |  | Laptop Gigabyte Gaming A16 CMHH2VN893SH (Core i5-13420H/ GeForce RTX™ 4050/ 16GB/ 512GB/ Win 11 Home SL) | Laptop Gigabyte Gaming A16 CMHH2VN893SH (Core i5-13420H/ GeForce RTX™ 4050/ 16GB/ 512GB/ Win 11 Home SL) | OK | - | OK |
| Active | `huawei-cu` |  | 200 |  | Huawei cũ | Huawei cũ | OK | - | OK |
| Active | `laptop-hp-omen-14-fb0134tx-ay8v0pa-ultra-9-185h-geforce-rtx-4060-32gb-1tb-win-11-home-sl-office` |  | 200 |  | Laptop HP OMEN 14-fb0134TX (AY8V0PA) (Ultra 9-185H/ GeForce RTX™ 4060/ 32GB/ 1TB/ Win 11 Home SL + Office) | Laptop HP OMEN 14-fb0134TX (AY8V0PA) (Ultra 9-185H/ GeForce RTX™ 4060/ 32GB/ 1TB/ Win 11 Home SL + Office) | OK | - | OK |
| Active | `iphone-15-pro-cu` |  | 200 |  | iPhone 15 Pro cũ | iPhone 15 Pro cũ | OK | - | OK |
| Active | `vivo-v-cu` |  | 200 |  | vivo V cũ | vivo V cũ | OK | - | OK |
| Active | `oppo-find-series-cu` |  | 200 |  | OPPO Find Series cũ | OPPO Find Series cũ | OK | - | OK |
| Active | `laptop-acer-nitro-propanel-anv15-52-72bm-i7-13620h-geforce-rtx-5050-16gb-512gb-win-11-home-sl` |  | 200 |  | Laptop Acer Nitro ProPanel ANV15-52-72BM (i7-13620H/ GeForce RTX™ 5050/ 16GB/ 512GB/ Win 11 Home SL) | Laptop Acer Nitro ProPanel ANV15-52-72BM (i7-13620H/ GeForce RTX™ 5050/ 16GB/ 512GB/ Win 11 Home SL) | OK | - | OK |
| Active | `card-man-hinh-vga` |  | 200 |  | Card màn hình - VGA | Card màn hình - VGA | OK | - | OK |
| Active | `vga-amd` |  | 200 |  | VGA AMD | VGA AMD | OK | - | OK |
| Active | `lot-chuot-ban-phim` |  | 200 |  | Lót chuột, bàn phím | Lót chuột, bàn phím | OK | - | OK |
| Active | `laptop-gigabyte-gaming-a16-cmhi2vn893sh-i7-13620h-geforce-rtx-4050-16gb-512gb-win-11-home-sl` |  | 200 |  | Laptop Gigabyte Gaming A16 CMHI2VN893SH (i7-13620H/ GeForce RTX™ 4050/ 16GB/ 512GB/ Win 11 Home SL) | Laptop Gigabyte Gaming A16 CMHI2VN893SH (i7-13620H/ GeForce RTX™ 4050/ 16GB/ 512GB/ Win 11 Home SL) | OK | - | OK |
| Active | `laptop-msi-prestige-13-ai-evo-a2vmg-040vn-ultra-7-258v-32gb-1tb-win-11-home` |  | 200 |  | Laptop Msi Prestige 13 AI+ Evo A2VMG-040VN (Ultra 7-258V/ 32GB/ 1TB/ Win 11 Home) | Laptop Msi Prestige 13 AI+ Evo A2VMG-040VN (Ultra 7-258V/ 32GB/ 1TB/ Win 11 Home) | OK | - | OK |
| Active | `laptop-msi-prestige-13-ai-evo-a1mg-062vn-ultra-7-155h-32gb-1tb-win-11-home` |  | 200 |  | Laptop Msi Prestige 13 AI Evo A1MG-062VN (Ultra 7-155H/ 32GB/ 1TB/ Win 11 Home) | Laptop Msi Prestige 13 AI Evo A1MG-062VN (Ultra 7-155H/ 32GB/ 1TB/ Win 11 Home) | OK | - | OK |
| Active | `laptop-asus-tuf-gaming-f16-fx608jhr-rv037w-i7-14650hx-geforce-rtx-5050-16gb-1tb-win-11-home` |  | 200 |  | Laptop Asus TUF Gaming F16 FX608JHR-RV037W (i7-14650HX/ GeForce RTX™ 5050/ 16GB/ 1TB/ Win 11 Home) | Laptop Asus TUF Gaming F16 FX608JHR-RV037W (i7-14650HX/ GeForce RTX™ 5050/ 16GB/ 1TB/ Win 11 Home) | OK | - | OK |
| Active | `laptop-lenovo-ideapad-slim-3-14irh10-83k00009vn-i7-13620h-24gb-512gb-win-11-home-sl-english` |  | 200 |  | Laptop Lenovo IdeaPad Slim 3 14IRH10 - 83K00009VN (i7-13620H/ 24GB/ 512GB/ Win 11 Home SL, English) | Laptop Lenovo IdeaPad Slim 3 14IRH10 - 83K00009VN (i7-13620H/ 24GB/ 512GB/ Win 11 Home SL, English) | OK | - | OK |
| Active | `laptop-msi-venture-a15-ai-a2hmg-003vn-ryzen-7-260-16gb-512gb-win-11-home-sea` |  | 200 |  | Laptop Msi Venture A15 AI A2HMG-003VN (Ryzen 7 260/ 16GB/ 512GB/ Win 11 Home SEA) | Laptop Msi Venture A15 AI A2HMG-003VN (Ryzen 7 260/ 16GB/ 512GB/ Win 11 Home SEA) | OK | - | OK |
| Active | `may-cao-rau-philips` |  | 200 |  | Máy cạo râu Philips | Máy cạo râu Philips | OK | - | OK |
| Active | `laptop-hp-omnibook-x-14-fe1010qu-b53kbpa-snapdragon-x1p-42-100-16gb-512gb-win-11-home-sl-office` |  | 200 |  | Laptop HP OmniBook X 14-fe1010QU - B53KBPA (Snapdragon X1P 42 100/ 16GB/ 512GB/ Win 11 Home SL + Office) | Laptop HP OmniBook X 14-fe1010QU - B53KBPA (Snapdragon X1P 42 100/ 16GB/ 512GB/ Win 11 Home SL + Office) | OK | - | OK |
| Active | `man-hinh-cu` |  | 200 |  | Màn hình cũ | Màn hình cũ | OK | - | OK |
| Active | `man-hinh-viewsonic-cu` |  | 200 |  | Màn Hình ViewSonic cũ | Màn Hình ViewSonic cũ | OK | - | OK |
| Active | `laptop-msi-vector-16-hx-ai-a2xwhg-010vn-ultra-7-255hx-geforce-rtx-5070ti-16gb-512gb-win-11-home` |  | 200 |  | Laptop Msi Vector 16 HX AI A2XWHG-010VN (Ultra 7-255HX/ GeForce RTX™ 5070Ti/ 16GB/ 512GB/ Win 11 Home) | Laptop Msi Vector 16 HX AI A2XWHG-010VN (Ultra 7-255HX/ GeForce RTX™ 5070Ti/ 16GB/ 512GB/ Win 11 Home) | OK | - | OK |
| Active | `man-hinh-samsung-cu` |  | 200 |  | Màn Hình Samsung cũ | Màn Hình Samsung cũ | OK | - | OK |
| Active | `laptop-msi-crosshair-16-hx-ai-d2xwgkg-034vn-ultra-7-255hx-geforce-rtx-5070-16gb-1tb-win-11-home-sl` |  | 200 |  | Laptop Msi Crosshair 16 HX AI D2XWGKG-034VN (Ultra 7-255HX/ GeForce RTX™ 5070/ 16GB/ 1TB/ Win 11 Home SL) | Laptop Msi Crosshair 16 HX AI D2XWGKG-034VN (Ultra 7-255HX/ GeForce RTX™ 5070/ 16GB/ 1TB/ Win 11 Home SL) | OK | - | OK |
| Active | `mainboard-intel` |  | 200 |  | Mainboard Intel | Mainboard Intel | OK | - | OK |
| Active | `mainboard-amd` |  | 200 |  | Mainboard AMD | Mainboard AMD | OK | - | OK |
| Active | `laptop-acer-swift-14-ai-sf14-51-53p9-ultra-5-226v-16gb-1tb-win-11-home-sl` |  | 200 |  | Laptop Acer Swift 14 AI SF14-51-53P9 (Ultra 5-226V/ 16GB/ 1TB/ Win 11 Home SL) | Laptop Acer Swift 14 AI SF14-51-53P9 (Ultra 5-226V/ 16GB/ 1TB/ Win 11 Home SL) | OK | - | OK |
| Active | `loa-goojodoq` |  | 200 |  | Loa GOOJODOQ | Loa GOOJODOQ | OK | - | OK |
| Active | `laptop-lenovo-ideapad-slim-5-oled-15arp10-83j3001dvn-ryzen-7-7735hs-16gb-512gb-win-11-home-sl` |  | 200 |  | Laptop Lenovo IdeaPad Slim 5 OLED 15ARP10 - 83J3001DVN (Ryzen 7 7735HS/ 16GB/ 512GB/ Win 11 Home SL) | Laptop Lenovo IdeaPad Slim 5 OLED 15ARP10 - 83J3001DVN (Ryzen 7 7735HS/ 16GB/ 512GB/ Win 11 Home SL) | OK | - | OK |
| Active | `laptop-lenovo-ideapad-slim-5-oled-15arp10-83j3002svn-ryzen-7-7735hs-32gb-512gb-win-11-home-sl-english` |  | 200 |  | Laptop Lenovo IdeaPad Slim 5 OLED 15ARP10 - 83J3002SVN (Ryzen 7 7735HS/ 32GB/ 512GB/ Win 11 Home SL, English) | Laptop Lenovo IdeaPad Slim 5 OLED 15ARP10 - 83J3002SVN (Ryzen 7 7735HS/ 32GB/ 512GB/ Win 11 Home SL, English) | OK | - | OK |
| Active | `laptop-msi-raider-18-hx-ai-a2xwig-033vn-ultra-9-285hx-geforce-rtx-5080-64gb-4tb-win-11-home` |  | 200 |  | Laptop Msi Raider 18 HX AI A2XWIG-033VN (Ultra 9-285HX/ GeForce RTX™ 5080/ 64GB/ 4TB/ Win 11 Home) | Laptop Msi Raider 18 HX AI A2XWIG-033VN (Ultra 9-285HX/ GeForce RTX™ 5080/ 64GB/ 4TB/ Win 11 Home) | OK | - | OK |
| Active | `laptop-msi-stealth-a16-ai-a3xvgg-208vn-ryzen-ai-9-hx-370-geforce-rtx-4070-32gb-2tb-win-11-home` |  | 200 |  | Laptop Msi Stealth A16 AI+ A3XVGG - 208VN (Ryzen AI 9 HX 370/ GeForce RTX™ 4070/ 32GB/ 2TB/ Win 11 Home) | Laptop Msi Stealth A16 AI+ A3XVGG - 208VN (Ryzen AI 9 HX 370/ GeForce RTX™ 4070/ 32GB/ 2TB/ Win 11 Home) | OK | - | OK |
| Active | `de-tan-nhiet-gia-do` |  | 200 |  | Đế tản nhiệt - Giá đỡ | Đế tản nhiệt - Giá đỡ | OK | - | OK |
| Active | `laptop-asus-vivobook-s16-s3607ca-sh083ws-ultra-7-255h-16gb-512gb-win-11-home-office-microsoft` |  | 200 |  | Laptop Asus Vivobook S16 S3607CA-SH083WS (Ultra 7-255H/ 16GB/ 512GB/ Win 11 Home + Office + Microsoft) | Laptop Asus Vivobook S16 S3607CA-SH083WS (Ultra 7-255H/ 16GB/ 512GB/ Win 11 Home + Office + Microsoft) | OK | - | OK |
| Active | `laptop-asus-vivobook-s16-s3607ca-sh082ws-ultra-5-225h-16gb-512gb-win-11-home-office-microsoft` |  | 200 |  | Laptop Asus Vivobook S16 S3607CA-SH082WS (Ultra 5-225H/ 16GB/ 512GB/ Win 11 Home + Office + Microsoft) | Laptop Asus Vivobook S16 S3607CA-SH082WS (Ultra 5-225H/ 16GB/ 512GB/ Win 11 Home + Office + Microsoft) | OK | - | OK |
| Active | `phu-kien-cu` |  | 200 |  | Phụ kiện cũ | Phụ kiện cũ | OK | - | OK |
| Active | `laptop-lenovo-ideapad-slim-3-14irh10-83k0004jvn-i5-13420h-16gb-512gb-win-11-home-sl-english` |  | 200 |  | Laptop Lenovo IdeaPad Slim 3 14IRH10 - 83K0004JVN (i5-13420H/ 16GB/ 512GB/ Win 11 Home SL, English) | Laptop Lenovo IdeaPad Slim 3 14IRH10 - 83K0004JVN (i5-13420H/ 16GB/ 512GB/ Win 11 Home SL, English) | OK | - | OK |
| Active | `laptop-acer-aspire-14-ai-a14-52m-71yq-ultra-7-256v-16gb-1tb-win-11-home-sl` |  | 200 |  | Laptop Acer Aspire 14 AI A14-52M-71YQ (Ultra 7-256V/ 16GB/ 1TB/ Win 11 Home SL) | Laptop Acer Aspire 14 AI A14-52M-71YQ (Ultra 7-256V/ 16GB/ 1TB/ Win 11 Home SL) | OK | - | OK |
| Active | `ban-chai-dien-philips` |  | 200 |  | Bàn chải điện Philips | Bàn chải điện Philips | OK | - | OK |
| Active | `tui-chong-soc-laptop` |  | 200 |  | Túi chống sốc laptop | Túi chống sốc laptop | OK | - | OK |
| Active | `tivi-lg-65-inch` |  | 200 |  | Tivi LG 65 inch | Tivi LG 65 inch | OK | - | OK |
| Active | `tivi-lg-55-inch` |  | 200 |  | Tivi LG 55 inch | Tivi LG 55 inch | OK | - | OK |
| Active | `tivi-lg-50-inch` |  | 200 |  | Tivi LG 50 inch | Tivi LG 50 inch | OK | - | OK |
| Active | `tivi-lg-43-inch` |  | 200 |  | Tivi LG 43 inch | Tivi LG 43 inch | OK | - | OK |
| Active | `macbook-air` |  | 200 |  | MacBook Air | MacBook Air | OK | - | OK |
| Active | `laptop-asus-tuf-gaming-f16-fx607vu-rl045w-core-5-210h-geforce-rtx-4050-16gb-512gb-win-11-home` |  | 200 |  | Laptop Asus TUF Gaming F16 FX607VU-RL045W (Core 5 210H/ GeForce RTX™ 4050/ 16GB/ 512GB/ Win 11 Home) | Laptop Asus TUF Gaming F16 FX607VU-RL045W (Core 5 210H/ GeForce RTX™ 4050/ 16GB/ 512GB/ Win 11 Home) | OK | - | OK |
| Active | `asus-tuf-a14` |  | 200 |  | Asus Tuf A14 | Asus Tuf A14 | OK | - | OK |
| Active | `iphone-16e-cu` |  | 200 |  | iPhone 16e cũ | iPhone 16e cũ | OK | - | OK |
| Active | `phu-kien-dien-thoai` |  | 200 |  | Phụ kiện điện thoại | Phụ kiện điện thoại | OK | - | OK |
| Active | `oppo-a-series-cu` |  | 200 |  | OPPO A Series cũ | OPPO A Series cũ | OK | - | OK |
| Active | `phu-kien-may-anh` |  | 200 |  | Phụ kiện máy ảnh | Phụ kiện máy ảnh | OK | - | OK |
| Active | `balo-tui-xach` |  | 200 |  | Balo \| Túi xách | Balo \| Túi xách | OK | - | OK |
| Active | `vali-du-lich` |  | 200 |  | Vali du lịch | Vali du lịch | OK | - | OK |
| Active | `tai-nghe-goojodoq` |  | 200 |  | Tai nghe GOOJODOQ | Tai nghe GOOJODOQ | OK | - | OK |
| Active | `sac-nhieu-cong` |  | 200 |  | Sạc nhiều cổng | Sạc nhiều cổng | OK | - | OK |
| Active | `sac-nhanh` |  | 200 |  | Sạc nhanh | Sạc nhanh | OK | - | OK |
| Active | `quat-dung` |  | 200 |  | Quạt đứng | Quạt đứng | OK | - | OK |
| Active | `tai-nghe-dell-alienware` |  | 200 |  | Tai nghe Dell Alienware | Tai nghe Dell Alienware | OK | - | OK |
| Active | `quat-toshiba` |  | 200 |  | Quạt Toshiba | Quạt Toshiba | OK | - | OK |
| Active | `quat-mini` |  | 200 |  | Quạt mini | Quạt mini | OK | - | OK |
| Active | `iphone-14-plus-pro-pro-max` |  | 200 |  | iPhone 14 \| Plus \| Pro \| Pro Max | iPhone 14 \| Plus \| Pro \| Pro Max | OK | - | OK |
| Active | `ram` |  | 200 |  | RAM | RAM | OK | - | OK |
| Active | `laptop-tu-20-den-30-trieu` |  | 200 |  | Laptop từ 20 đến 30 triệu | Laptop từ 20 đến 30 triệu | OK | - | OK |
| Active | `may-loc-khong-khi-tao-am` |  | 200 |  | Máy lọc không khí tạo ẩm | Máy lọc không khí tạo ẩm | OK | - | OK |
| Active | `ban-phim-gaming-steelseries` |  | 200 |  | Bàn phím Gaming SteelSeries | Bàn phím Gaming SteelSeries | OK | - | OK |
| Active | `macbook-neo-cu` |  | 200 |  | MacBook Neo cũ | MacBook Neo cũ | OK | - | OK |
| Active | `may-choi-game-cam-tay-handheld-gaming` |  | 200 |  | Máy chơi game cầm tay Handheld gaming | Máy chơi game cầm tay Handheld gaming | OK | - | OK |
| Active | `poco-cu` |  | 200 |  | Poco cũ | Poco cũ | OK | - | OK |
| Active | `masstel-cu` |  | 200 |  | Masstel cũ | Masstel cũ | OK | - | OK |
| Active | `apple-watch-cu` |  | 200 |  | Apple Watch cũ | Apple Watch cũ | OK | - | OK |
| Active | `tivi-cu` |  | 200 |  | Tivi cũ | Tivi cũ | OK | - | OK |
| Active | `may-hut-bui` |  | 200 |  | Máy hút bụi | Máy hút bụi | OK | - | OK |
| Active | `robot-hut-bui-roborock` |  | 200 |  | Robot hút bụi Roborock | Robot hút bụi Roborock | OK | - | OK |
| Active | `lenovo` |  | 200 |  | Lenovo | Lenovo | OK | - | OK |
| Active | `may-hut-bui-dyson` |  | 200 |  | Máy hút bụi Dyson | Máy hút bụi Dyson | OK | - | OK |
| Active | `msi-katana-sword` |  | 200 |  | MSI Katana / Sword | MSI Katana / Sword | OK | - | OK |
| Active | `chuot-gaming-steelseries` |  | 200 |  | Chuột Gaming SteelSeries | Chuột Gaming SteelSeries | OK | - | OK |
| Active | `ban-phim-co-khong-day-aula-f87-pro-greywood-v4-switch` |  | 200 |  | Bàn phím cơ không dây AULA F87 PRO Greywood V4 switch | Bàn phím cơ không dây AULA F87 PRO Greywood V4 switch | OK | - | OK |
| Active | `galaxy-a` |  | 200 |  | Galaxy A | Galaxy A | OK | - | OK |
| Active | `dan-mipow` |  | 200 |  | Dán Mipow | Dán Mipow | OK | - | OK |
| Active | `s26-series-cu` |  | 200 |  | S26 Series cũ | S26 Series cũ | OK | - | OK |
| Active | `chuot-dareu` |  | 200 |  | Chuột Dareu | Chuột Dareu | OK | - | OK |
| Active | `ban-phim-akko-3098b-plus-black-akko-switch-v3` |  | 200 |  | Bàn phím AKKO 3098B Plus Black (Akko switch v3) | Bàn phím AKKO 3098B Plus Black (Akko switch v3) | OK | - | OK |
| Active | `lot-chuot-steelseries` |  | 200 |  | Lót chuột SteelSeries | Lót chuột SteelSeries | OK | - | OK |
| Active | `cay-nuoc-nong-lanh` |  | 200 |  | Cây nước nóng lạnh | Cây nước nóng lạnh | OK | - | OK |
| Active | `ban-phim-dareu` |  | 200 |  | Bàn phím Dareu | Bàn phím Dareu | OK | - | OK |
| Active | `robot-hut-bui-dreame` |  | 200 |  | Robot hút bụi Dreame | Robot hút bụi Dreame | OK | - | OK |
| Active | `surface-cu` |  | 200 |  | Surface cũ | Surface cũ | OK | - | OK |
| Active | `chuot` |  | 200 |  | Chuột | Chuột | OK | - | OK |
| Active | `micro-thu-am` |  | 200 |  | Micro thu âm | Micro thu âm | OK | - | OK |
| Active | `nubia-cu` |  | 200 |  | Nubia cũ | Nubia cũ | OK | - | OK |
| Active | `tan-nhiet` |  | 200 |  | Tản nhiệt | Tản nhiệt | OK | - | OK |
| Active | `may-rua-bat-bosch` |  | 200 |  | Máy rửa bát Bosch | Máy rửa bát Bosch | OK | - | OK |
| Active | `do-dien-gia-dung` |  | 200 |  | Đồ điện gia dụng | Đồ điện gia dụng | OK | - | OK |
| Active | `may-hut-bui-dreame` |  | 200 |  | Máy hút bụi Dreame | Máy hút bụi Dreame | OK | - | OK |
| Active | `asus` |  | 200 |  | Asus | Asus | OK | - | OK |
| Active | `may-cao-rau-flyco` |  | 200 |  | Máy cạo râu Flyco | Máy cạo râu Flyco | OK | - | OK |
| Active | `lg-cu` |  | 200 |  | LG cũ | LG cũ | OK | - | OK |
| Active | `chuot-gaming-asus` |  | 200 |  | Chuột Gaming Asus | Chuột Gaming Asus | OK | - | OK |
| Active | `acer-predator` |  | 200 |  | Acer Predator | Acer Predator | OK | - | OK |
| Active | `iphone-17e-cu` |  | 200 |  | iPhone 17e cũ | iPhone 17e cũ | OK | - | OK |
| Active | `ipad-pro-cu` |  | 200 |  | iPad Pro cũ | iPad Pro cũ | OK | - | OK |
| Active | `dong-ho-thong-minh-cu` |  | 200 |  | Đồng hồ thông minh cũ | Đồng hồ thông minh cũ | OK | - | OK |
| Active | `redmi-cu` |  | 200 |  | Redmi cũ | Redmi cũ | OK | - | OK |
| Active | `galaxy-a-cu` |  | 200 |  | Galaxy A cũ | Galaxy A cũ | OK | - | OK |
| Active | `nguon-psu` |  | 200 |  | Nguồn PSU | Nguồn PSU | OK | - | OK |
| Active | `do-gia-dung-nha-bep` |  | 200 |  | Đồ gia dụng nhà bếp | Đồ gia dụng nhà bếp | OK | - | OK |
| Active | `robot-hut-bui-ecovacs` |  | 200 |  | Robot hút bụi Ecovacs | Robot hút bụi Ecovacs | OK | - | OK |
| Active | `do-gia-dung` |  | 200 |  | Đồ gia dụng | Đồ gia dụng | OK | - | OK |
| Active | `quat-hoi-nuoc` |  | 200 |  | Quạt hơi nước | Quạt hơi nước | OK | - | OK |
| Active | `op-lung-bao-da` |  | 200 |  | Ốp lưng \| Bao da | Ốp lưng \| Bao da | OK | - | OK |
| Active | `lens` |  | 200 |  | Lens | Lens | OK | - | OK |
| Active | `lot-chuot-corsair` |  | 200 |  | Lót chuột Corsair | Lót chuột Corsair | OK | - | OK |
| Active | `ghe-gaming-msi` |  | 200 |  | Ghế Gaming MSI | Ghế Gaming MSI | OK | - | OK |
| Active | `ghe-gaming-corsair` |  | 200 |  | Ghế gaming Corsair | Ghế gaming Corsair | OK | - | OK |
| Active | `noi-com-dien-tiger` |  | 200 |  | Nồi cơm điện tiger | Nồi cơm điện tiger | OK | - | OK |
| Active | `chuot-gaming-dare-u-em908-queen-hong` |  | 200 |  | Chuột gaming DARE-U EM908 Queen (Hồng) | Chuột gaming DARE-U EM908 Queen (Hồng) | OK | - | OK |
| Active | `may-tinh-bang-cu` |  | 200 |  | Máy tính bảng cũ | Máy tính bảng cũ | OK | - | OK |
| Active | `ghe-gaming-warrior` |  | 200 |  | Ghế gaming Warrior | Ghế gaming Warrior | OK | - | OK |
| Active | `ghe-gaming-e-dra` |  | 200 |  | Ghế gaming E-Dra | Ghế gaming E-Dra | OK | - | OK |
| Active | `case-may-tinh` |  | 200 |  | Case máy tính | Case máy tính | OK | - | OK |
| Active | `lot-chuot-razer-goliathus-chroma-extended-xxl` |  | 200 |  | Lót Chuột Razer Goliathus Chroma Extended - XXL | Lót Chuột Razer Goliathus Chroma Extended - XXL | OK | - | OK |
| Active | `quat-kangaroo` |  | 200 |  | Quạt Kangaroo | Quạt Kangaroo | OK | - | OK |
| Active | `may-anh-cu` |  | 200 |  | Máy ảnh cũ | Máy ảnh cũ | OK | - | OK |
| Active | `man-hinh-edra-cu` |  | 200 |  | Màn Hình Edra cũ | Màn Hình Edra cũ | OK | - | OK |
| Active | `gigabyte` |  | 200 |  | Gigabyte | Gigabyte | OK | - | OK |
| Active | `man-hinh-asus-cu` |  | 200 |  | Màn Hình Asus cũ | Màn Hình Asus cũ | OK | - | OK |
| Active | `macbook-pro-cu` |  | 200 |  | Macbook Pro cũ | Macbook Pro cũ | OK | - | OK |
| Active | `vo-case` |  | 200 |  | Vỏ case | Vỏ case | OK | - | OK |
| Active | `macbook-air-cu` |  | 200 |  | Macbook Air cũ | Macbook Air cũ | OK | - | OK |
| Active | `ban-phim-chuot-newmen-t260-full-sizekailh` |  | 200 |  | Bàn phím + Chuột Newmen T260 (Full size/Kailh) | Bàn phím + Chuột Newmen T260 (Full size/Kailh) | OK | - | OK |
| Active | `hang-khac` |  | 200 |  | Hãng khác | Hãng khác | OK | - | OK |
| Active | `oppo-reno-series-cu` |  | 200 |  | OPPO Reno Series cũ | OPPO Reno Series cũ | OK | - | OK |
| Active | `android-cu` |  | 200 |  | Android cũ | Android cũ | OK | - | OK |
| Active | `dan-dien-thoai-laptop` |  | 200 |  | Dán điện thoại \| Laptop | Dán điện thoại \| Laptop | OK | - | OK |
| Active | `ai` |  | 200 |  | AI | AI | OK | - | OK |
| Active | `hang-cu` |  | 200 |  | Hàng cũ | Hàng cũ | OK | - | OK |
| Active | `cham-soc-suc-khoe-lam-dep` |  | 200 |  | Chăm sóc sức khỏe - Làm đẹp | Chăm sóc sức khỏe - Làm đẹp | OK | - | OK |
| Active | `the-nho-usb` |  | 200 |  | Thẻ nhớ, USB | Thẻ nhớ, USB | OK | - | OK |
| Active | `dong-ho-samsung-cu` |  | 200 |  | Đồng hồ Samsung cũ | Đồng hồ Samsung cũ | OK | - | OK |
| Active | `may-lanh-inverter` |  | 200 |  | Máy lạnh inverter | Máy lạnh inverter | OK | - | OK |
| Active | `man-hinh-gaming` |  | 200 |  | Màn hình gaming | Màn hình gaming | OK | - | OK |
| Active | `man-hinh-van-phong` |  | 200 |  | Màn hình văn phòng | Màn hình văn phòng | OK | - | OK |
| Active | `man-hinh-2k` |  | 200 |  | Màn hình 2K | Màn hình 2K | OK | - | OK |
| Active | `man-hinh-msi-cu` |  | 200 |  | Màn Hình MSI cũ | Màn Hình MSI cũ | OK | - | OK |
| Active | `msi-cu` |  | 200 |  | MSI cũ | MSI cũ | OK | - | OK |
| Active | `laptop-copilot-pc` |  | 200 |  | Laptop Copilot+ PC | Laptop Copilot+ PC | OK | - | OK |
| Active | `ban-phim` |  | 200 |  | Bàn phím | Bàn phím | OK | - | OK |
| Active | `cellphones` |  | 200 |  | CELLPHONES | CELLPHONES | OK | - | OK |
| Active | `asus-cu` |  | 200 |  | Asus cũ | Asus cũ | OK | - | OK |
| Active | `acer-cu` |  | 200 |  | Acer cũ | Acer cũ | OK | - | OK |
| Active | `chuot-asus` |  | 200 |  | Chuột Asus | Chuột Asus | OK | - | OK |
| Active | `thiet-bi-mang` |  | 200 |  | Thiết bị mạng | Thiết bị mạng | OK | - | OK |
| Active | `card-man-hinh` |  | 200 |  | Card màn hình | Card màn hình | OK | - | OK |
| Active | `balo-laptop` |  | 200 |  | Balo laptop | Balo laptop | OK | - | OK |
| Active | `chuot-gaming-zowie` |  | 200 |  | Chuột Gaming Zowie | Chuột Gaming Zowie | OK | - | OK |
| Active | `apple-watch-series-10-cu` |  | 200 |  | Apple watch series 10 cũ | Apple watch series 10 cũ | OK | - | OK |
| Active | `am-thanh` |  | 200 |  | Âm thanh | Âm thanh | OK | - | OK |
| Active | `chuot-khong-day-bluetooth-newmen-d566-dual-mode` |  | 200 |  | Chuột không dây bluetooth Newmen D566 Dual Mode | Chuột không dây bluetooth Newmen D566 Dual Mode | OK | - | OK |
| Active | `chuot-gaming-co-day-asus-tuf-m3-gen-ii-90mp0320-bmua00` |  | 200 |  | Chuột gaming có dây ASUS TUF M3 Gen II (90MP0320-BMUA00) | Chuột gaming có dây ASUS TUF M3 Gen II (90MP0320-BMUA00) | OK | - | OK |
| Active | `dareu` |  | 200 |  | DareU | DareU | OK | - | OK |
| Active | `lot-chuot-pad-chuot-ban-di-chuot` |  | 200 |  | Lót chuột - Pad chuột - Bàn di chuột | Lót chuột - Pad chuột - Bàn di chuột | OK | - | OK |
| Active | `tan-nhiet-cpu` |  | 200 |  | Tản nhiệt CPU | Tản nhiệt CPU | OK | - | OK |
| Active | `lenovo-cu` |  | 200 |  | Lenovo cũ | Lenovo cũ | OK | - | OK |
| Active | `hp-cu` |  | 200 |  | HP cũ | HP cũ | OK | - | OK |
| Active | `chuot-gaming-logitech-g502-hero` |  | 200 |  | Chuột gaming Logitech G502 HERO | Chuột gaming Logitech G502 HERO | OK | - | OK |
| Active | `dell-cu` |  | 200 |  | Dell cũ | Dell cũ | OK | - | OK |
| Active | `gaming-gear` |  | 200 |  | Gaming Gear | Gaming Gear | OK | - | OK |
| Active | `laptop-gaming-cu` |  | 200 |  | Laptop Gaming cũ | Laptop Gaming cũ | OK | - | OK |
| Active | `uu-dai-chuot-gear-cho-ban-moi` |  | 200 |  | Ưu đãi Chuột - Gear cho bạn mới | Ưu đãi Chuột - Gear cho bạn mới | OK | - | OK |
| Active | `ban-phim-gaming-gia-co-dareu-lk145` |  | 200 |  | Bà̀n phím Gaming giả cơ Dareu LK145 | Bà̀n phím Gaming giả cơ Dareu LK145 | OK | - | OK |
| Active | `phu-kien-man-hinh` |  | 200 |  | Phụ kiện Màn hình | Phụ kiện Màn hình | OK | - | OK |
| Active | `tui-xach-du-lich` |  | 200 |  | Túi xách du lịch | Túi xách du lịch | OK | - | OK |
| Active | `thiet-bi-livestream` |  | 200 |  | Thiết bị Livestream | Thiết bị Livestream | OK | - | OK |
| Active | `chuot-may-tinh-khong-day-logitech-b175-den` |  | 200 |  | Chuột máy tính không dây Logitech B175 (Đen) | Chuột máy tính không dây Logitech B175 (Đen) | OK | - | OK |
| Active | `tui-dung-phu-kien` |  | 200 |  | Túi đựng phụ kiện | Túi đựng phụ kiện | OK | - | OK |
| Active | `phu-kien-may-tinh` |  | 200 |  | Phụ kiện máy tính | Phụ kiện máy tính | OK | - | OK |
| Active | `chuot-akko` |  | 200 |  | Chuột AKKO | Chuột AKKO | OK | - | OK |
| Active | `laptop-gaming-intel` |  | 200 |  | Laptop Gaming Intel | Laptop Gaming Intel | OK | - | OK |
| Active | `chuot-ban-phim` |  | 200 |  | Chuột \| Bàn Phím | Chuột \| Bàn Phím | OK | - | OK |
| Active | `linh-kien` |  | 200 |  | Linh kiện | Linh kiện | OK | - | OK |
| Active | `loa` |  | 200 |  | Loa | Loa | OK | - | OK |
| Active | `tai-nghe` |  | 200 |  | Tai nghe | Tai nghe | OK | - | OK |
| Alias 301 | `may-tinh` | `may-tinh-bang` | 301 | http://localhost/danh-muc/may-tinh-bang | Máy tính bảng | Máy tính bảng | OK | - | OK |
| Alias 301 | `tablet` | `may-tinh-bang` | 301 | http://localhost/danh-muc/may-tinh-bang | Máy tính bảng | Máy tính bảng | OK | - | OK |
| Alias 301 | `audio` | `am-thanh` | 301 | http://localhost/danh-muc/am-thanh | Âm thanh | Âm thanh | OK | - | OK |
| Alias 301 | `smartwatch` | `dong-ho-thong-minh` | 301 | http://localhost/danh-muc/dong-ho-thong-minh | Đồng hồ thông minh | Đồng hồ thông minh | OK | - | OK |
| Alias 301 | `gaming` | `laptop-gaming` | 301 | http://localhost/danh-muc/laptop-gaming | Laptop Gaming | Laptop Gaming | OK | - | OK |
