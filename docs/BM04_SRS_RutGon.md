# ĐẶC TẢ YÊU CẦU PHẦN MỀM RÚT GỌN (BM-04)
Chủ đề: CD04 – Du lịch miệt vườn và chợ nổi ĐBSCL

## 1. HỒ SƠ NGƯỜI DÙNG & PERSONA TIÊU BIỂU
- Họ và tên: Chị Thảo (35 tuổi)
- Nghề nghiệp: Nhân viên văn phòng tại Hà Nội
- Bối cảnh: Có 2 con nhỏ, muốn đưa gia đình đi du lịch sông nước Tây Nam Bộ vào kỳ nghỉ hè tháng 6.
- Nhu cầu & Hành vi: 
  + Thao tác chủ yếu trên điện thoại di động (Mobile).
  + Muốn biết tháng 6 miền Tây có trái cây gì đang chín rộ, chợ nổi nào họp đông nhất.
  + Muốn chọn tour đi bằng xuồng máy và có thời lượng 2N1Đ để phù hợp với sức khỏe trẻ nhỏ.
  + Muốn nhận báo giá và tư vấn nhanh mà không bắt buộc phải tạo tài khoản phức tạp.

## 2. DANH SÁCH CÂU CHUYỆN NGƯỜI DÙNG (USER STORIES) & TIÊU CHÍ CHẤP NHẬN (AC)

### US1: Tra cứu lịch mùa vụ miệt vườn theo tháng
- Là: Khách du lịch gia đình.
- Tôi muốn: Bấm chọn từng tháng (từ tháng 1 đến 12) để biết trải nghiệm nào (trái cây, lễ hội, cảnh quan) đang vào mùa.
- Tiêu chí chấp nhận (AC):
  + [AC1.1]: Khi bấm vào "Tháng 6", hệ thống gọi REST API và cập nhật danh sách mùa vụ ngay lập tức mà không tải lại trang.
  + [AC1.2]: Xử lý đúng các mục mùa vụ vắt qua năm (ví dụ: month_from = 11, month_to = 2 thì khi bấm tháng 11, 12, 1, 2 đều phải hiển thị; bấm tháng 3 thì ẩn).

### US2: Gợi ý tour WooCommerce phù hợp theo tháng
- Là: Khách du lịch.
- Tôi muốn: Thấy ngay danh sách các tour du lịch phù hợp với tháng mình vừa chọn.
- Tiêu chí chấp nhận (AC):
  + [AC2.1]: Gọi endpoint `GET /etour/v1/suggest?month=6` trả về danh sách tour có liên kết trong bảng `et04_etour_tour_months`.
  + [AC2.2]: Mỗi thẻ tour gợi ý có đầy đủ ảnh, tên tour, giá tiền và nút liên kết trỏ thẳng tới trang chi tiết tour.

### US3: Đếm ngược tới mùa vụ kế tiếp
- Là: Khách lên kế hoạch sớm.
- Tôi muốn: Biết còn bao lâu nữa thì tới mùa của một loại trái cây đặc sản mình yêu thích.
- Tiêu chí chấp nhận (AC):
  + [AC3.1]: Nếu một loại trái cây chưa đến mùa (ví dụ hiện tại tháng 3, mùa tháng 5), thẻ hiển thị dòng chữ: "⏳ Sẽ bắt đầu sau khoảng N tháng nữa".
  + [AC3.2]: Nếu đang đúng mùa vụ, thẻ hiển thị nhãn: "● Đang trong thời điểm mùa vụ!".

### US4: Gửi yêu cầu tư vấn trải nghiệm theo mùa
- Là: Khách hàng vãng lai.
- Tôi muốn: Gửi thông tin liên hệ và tháng dự kiến đi để nhân viên tư vấn gọi lại.
- Tiêu chí chấp nhận (AC):
  + [AC4.1]: Bắt buộc phải tích chọn ô đồng ý với "Chính sách quyền riêng tư" thì mới bấm gửi được (YC10).
  + [AC4.2]: Phía máy chủ kiểm tra đủ 5 lớp bảo vệ; sau khi gửi thành công, chuyển hướng theo mô hình Post/Redirect/Get kèm thông báo xanh `?etour=ok`.

### US5: Đặt combo tour linh hoạt theo biến thể
- Là: Khách hàng.
- Tôi muốn: Tùy chọn Gói (1 ngày / 2N1Đ) và Phương tiện (Xe khách / Xuồng máy) để xem mức giá tương ứng và đặt mua thử nghiệm.
- Tiêu chí chấp nhận (AC):
  + [AC5.1]: Thay đổi lựa chọn biến thể làm cập nhật giá tour tức thời.
  + [AC5.2]: Đặt hàng thành công tạo ra đơn hàng trong trạng thái "Đang xử lý" (Processing).