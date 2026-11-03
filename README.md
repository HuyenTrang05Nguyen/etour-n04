# <Tên website> – Nhóm N<XX> – Chủ đề <CDxx>

Dự án cuối kỳ học phần CSE703072 – Lập trình ứng dụng web trong du lịch 1, Trường Công nghệ thông tin – Đại học Phenikaa.
Website phục vụ học tập – dữ liệu mô phỏng – không phát sinh giao dịch thật.

## 1. Thành viên và vai trò
| MSSV | Họ và tên | Vai trò | Phạm vi mã nguồn chính |
|---|---|---|---|
| | | R1 – Trưởng nhóm, Kiến trúc & Hạ tầng | database/, wp-config, triển khai |
| | | R2 – Nghiệp vụ & Dữ liệu | dữ liệu WooCommerce, database/seed |
| | | R3 – Giao diện UI/UX | themes/etour-child/assets/css |
| | | R4 – Tương tác & Module | plugins/etour-cdxx, assets/js |
| | | R5 – Kiểm thử, An ninh & Tài liệu | tests/, docs/ |

## 2. Địa chỉ truy cập
- Website: https://…
- Trang quản trị: https://…/wp-admin (tài khoản quản trị gửi riêng qua Canvas)
- Tài khoản khách hàng thử nghiệm: khach01 / (mật khẩu môi trường kiểm thử)

## 3. Yêu cầu môi trường
- XAMPP: Apache 2.4, PHP ≥ 8.2, MariaDB ≥ 10.6; WordPress <phiên bản>; WooCommerce <phiên bản>; Storefront <phiên bản>.

## 4. Cài đặt cục bộ từng bước
1. Tạo CSDL `etour_nXX` (utf8mb4_unicode_ci) trong phpMyAdmin.
2. Nhập `database/etour_nXX.sql`.
3. Cài WordPress vào `C:\xampp\htdocs\etourism`, khai báo `wp-config.php` (tiền tố bảng: `etXX_`).
4. Sao chép `wp-content/themes/etour-child` và `wp-content/plugins/etour-cdxx`.
5. Thay URL nếu khác tên miền: `http://etourism.local` → địa chỉ mới.
6. Quản trị → Cài đặt → Đường dẫn tĩnh → Lưu (tạo lại .htaccess).

## 5. Chức năng chính
- …

## 6. REST API
| Phương thức | Endpoint | Quyền | Mô tả |
|---|---|---|---|
| GET | /wp-json/etour/v1/... | Công khai | |

## 7. Kiểm thử
- static_scan.php: … FAIL / … WARN (tests/static_scan.json)
- Lighthouse (mobile) trang chủ: Performance …, Accessibility …, Best Practices …, SEO …

## 8. Nguồn tham khảo và bản quyền hình ảnh
- …
