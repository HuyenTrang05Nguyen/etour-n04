# BẢN HIẾN CHƯƠNG DỰ ÁN & ĐĂNG KÝ ĐỀ TÀI (BM-01)
Học phần: Lập trình ứng dụng web trong du lịch 1 (Mã HP: CSE703072)
Đơn vị: Khoa Hệ thống thông tin – Trường CNTT – Đại học Phenikaa

## 1. THÔNG TIN CHUNG
- Nhóm thực hiện: Nhóm N04 (Lớp: K18CNTT1)
- Tên đề tài: CD04 – Du lịch miệt vườn và chợ nổi Đồng bằng sông Cửu Long
- Tên thương hiệu giả định: Miệt Vườn Xanh Tour – Nhóm N04
- Tên miền ảo phát triển: http://etourism.local
- Tên CSDL: etour_n04 (Bảng mã: utf8mb4 / Collation: utf8mb4_unicode_ci)
- Tiền tố bảng (Table prefix): et04_
- Đường dẫn kho mã Git (Private): https://github.com/HuyenTrang05Nguyen/etour-n04.git

## 2. MỤC TIÊU DỰ ÁN (SMART)
- Xây dựng hoàn chỉnh website thương mại điện tử du lịch theo kiến trúc 3 tầng:
  + Tầng Presentation: Child theme etour-child (Storefront), CSS Grid responsive 3 breakpoint, tương tác Vanilla JS.
  + Tầng Application: WordPress, WooCommerce (quản lý 24 tour) và Module plugin etour-cd04 tự viết bằng PHP.
  + Tầng Data: CSDL MariaDB/MySQL với 3 bảng đặc thù et04_etour_*.
- Đạt 100% các tiêu chí từ YC01 đến YC12, vượt qua bộ kiểm thử tự động etour-checker (0 CHƯA ĐẠT) và static_scan.php (0 FAIL).
- Triển khai trực tuyến thành công có chứng chỉ HTTPS và bảo vệ đồ án đúng hạn.

## 3. PHẠM VI & QUY TẮC BẮT BUỘC
- Bán tour: Dùng WooCommerce (24 tour, 5 danh mục, 2 thuộc tính toàn cục Gói x Phương tiện, 3 tour khuyến mãi có đếm ngược).
- Nghiệp vụ riêng: Module etour-cd04 hiển thị lịch mùa vụ (xử lý mùa vắt qua năm), gợi ý tour theo tháng, form tư vấn 5 lớp bảo vệ.
- Cam kết học thuật: Tuyệt đối KHÔNG sử dụng Bootstrap, Tailwind, jQuery trong mã nguồn tự viết; KHÔNG sử dụng Page Builder; KHÔNG dùng plugin đặt chỗ có sẵn.

## 4. MA TRẬN PHÂN CÔNG VAI TRÒ (R1 - R5)
| STT | Họ và tên | Mã sinh viên | Vai trò chuẩn | Trách nhiệm chính trong dự án |
| :---: | :--- | :---: | :---: | :--- |
| 1 | Nguyễn Thị Huyền Trang | 23015179 | R1 (Trưởng nhóm) | Kiến trúc máy chủ, CSDL, quản trị Git, triển khai hosting |
| 2 | Thành viên 2 | ... | R2 (Nghiệp vụ) | Quản trị WooCommerce, nhập dataset, chính sách dữ liệu |
| 3 | Thành viên 3 | ... | R3 (Giao diện) | UI/UX, CSS Grid 3 breakpoint, tối ưu Accessibility |
| 4 | Thành viên 4 | ... | R4 (Lập trình) | Viết plugin etour-cd04, REST API, tương tác Vanilla JS |
| 5 | Thành viên 5 | ... | R5 (Kiểm thử) | Lập 40 Test Cases, đo Lighthouse, an ninh OWASP, báo cáo |

## 5. LỘ TRÌNH VÀ CÁC MỐC QUAN TRỌNG (MILESTONES)
- Mốc M0 (Cuối tuần 1): Đăng ký chủ đề, lập repo Git, chốt Style Guide tokens.css.
- Mốc M1 (Cuối tuần 5): Bản Alpha - Hoàn thành WooCommerce, CSS Grid, trang 404, Lighthouse Vòng 1.
- Mốc M2 (Cuối tuần 9): Bản Beta - Hoàn thành plugin etour-cd04, quét an ninh OWASP, kiểm thử chéo.
- Mốc M3 (Cuối tuần 10): Bản Release - Triển khai hosting HTTPS, đóng gói hồ sơ 9 mục.
- Mốc M4: Vấn đáp bảo vệ đồ án theo lịch của Khoa.