---
name: modern-ui-styling
description: Hướng dẫn và hệ thống thiết kế giao diện web hiện đại (Modern UI Design System) với CSS thuần: bảng màu sinh thái tự nhiên, hệ thống typography, bóng đổ mềm mại (soft elevation shadows), hiệu ứng chuyển động mượt mà và bố cục chuẩn UX/UI.
---

# Kỹ năng Thiết kế Giao diện Web Hiện đại (Modern UI Styling Skill)

Bộ kỹ năng này cung cấp các nguyên tắc, bảng mã màu, biến chuẩn (Design Tokens) và mẫu CSS chuyên nghiệp để nâng tầm giao diện website du lịch miệt vườn trở nên sang trọng, hiện đại và thu hút.

---

## 1. Hệ thống Bảng màu Sinh thái Hiện đại (Eco-Tourism Palette)

Màu sắc đóng vai trò 80% cảm xúc thị giác đầu tiên của người dùng. Với đề tài **CD04 - Du lịch miệt vườn và sông nước Tây Nam Bộ**, bộ mã màu chuẩn gồm:

```css
:root {
  /* Màu chủ đạo (Xanh sinh thái miệt vườn & sông nước) */
  --c-primary: #15803d;        /* Xanh lá tươi mát sang trọng */
  --c-primary-hover: #166534;  /* Xanh đậm khi hover */
  --c-primary-light: #dcfce7;  /* Xanh nhạt làm nền badge / tag */
  
  /* Màu điểm nhấn (Cam trái cây chín / Hoàng hôn sông nước) */
  --c-accent: #ea580c;         /* Cam đất nổi bật, kích thích hành động đặt tour */
  --c-accent-hover: #c2410c;   /* Cam đậm khi tương tác */
  --c-accent-light: #ffedd5;   /* Cam nhạt làm nền tag giảm giá */

  /* Màu sông nước phù sa */
  --c-water: #0284c7;          /* Xanh sông nước miền Tây */
  --c-water-light: #e0f2fe;

  /* Màu nền & Bề mặt (Surface & Background) */
  --bg-page: #f8fafc;          /* Trắng kem xám siêu nhẹ, dịu mắt hơn trắng tinh */
  --bg-card: #ffffff;          /* Thẻ card trắng nổi bật trên nền trang */
  --bg-subtle: #f1f5f9;        /* Nền khối thông tin phụ */

  /* Màu chữ & Tương phản (Accessibility WCAG 2.2 AA) */
  --text-main: #0f172a;        /* Xanh đen than sang trọng, không dùng đen tuyền #000 */
  --text-muted: #64748b;       /* Chữ phụ, ngày tháng, thông tin mô tả */
  --text-light: #94a3b8;       /* Chữ mờ, placeholder */

  /* Viền & Đường phân cách */
  --border-light: #e2e8f0;
  --border-hover: #cbd5e1;

  /* Bo góc hiện đại (Border Radius) */
  --radius-xs: 4px;
  --radius-sm: 8px;
  --radius-md: 12px;           /* Chuẩn cho Card sản phẩm */
  --radius-lg: 16px;           /* Chuẩn cho Banner / Khối lớn */
  --radius-full: 9999px;       /* Chuẩn cho nút Pill / Badge tròn */
}
```

---

## 2. Hệ thống Đổ bóng Đa tầng (Smooth Layered Shadows)

Tránh sử dụng các bóng đổ đen đậm thô ráp (`rgba(0,0,0,0.5)`). Hãy dùng bóng đổ mờ phân tầng giúp giao diện có chiều sâu:

```css
:root {
  /* Bóng đổ mặc định cho Card (nhẹ nhàng, thanh thoát) */
  --shadow-sm: 0 1px 3px 0 rgba(15, 23, 42, 0.06), 0 1px 2px -1px rgba(15, 23, 42, 0.04);
  --shadow-card: 0 4px 6px -1px rgba(15, 23, 42, 0.07), 0 2px 4px -2px rgba(15, 23, 42, 0.05);

  /* Bóng đổ khi rê chuột (Card nổi lên nhẹ) */
  --shadow-hover: 0 12px 24px -4px rgba(15, 23, 42, 0.12), 0 4px 8px -2px rgba(15, 23, 42, 0.06);

  /* Bóng đổ nổi khối cho nút bấm CTA */
  --shadow-btn: 0 4px 14px 0 rgba(21, 128, 61, 0.35);
  --shadow-btn-hover: 0 6px 20px 0 rgba(21, 128, 61, 0.45);
}
```

---

## 3. Hiệu ứng Tương tác Vi mô (Micro-Interactions)

```css
/* Hiệu ứng chuyển động mượt mà cho mọi nút và thẻ */
.smooth-transition {
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Thẻ nâng nhẹ khi rê chuột (Card Hover Lift) */
.card-hover {
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}
.card-hover:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-hover);
  border-color: var(--border-hover);
}

/* Ảnh phóng to nhẹ khi rê chuột vào thẻ (Image Zoom on Hover) */
.img-zoom-container {
  overflow: hidden;
  border-radius: var(--radius-md) var(--radius-md) 0 0;
}
.img-zoom-container img {
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.card-hover:hover .img-zoom-container img {
  transform: scale(1.05);
}
```

---

## 4. Hệ thống Typography Chuẩn Web Hiện đại

* **Font gia đình:** Ưu tiên `Roboto`, `Inter`, `Be Vietnam Pro` hoặc font hệ thống `-apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif`.
* **Chiều cao dòng (`line-height`):** Luôn từ `1.4` đến `1.6` để chữ thoáng, dễ đọc.
* **Tiêu đề h2, h3:** `font-weight: 700`, `letter-spacing: -0.02em`.

---

## 5. Quy tắc Vàng Khi Thiết Kế Bố Cục (Layout Best Practices)
1. **Dùng CSS Grid thay cho Float:** Chia cột bằng `grid-template-columns: repeat(auto-fill, minmax(250px, 1fr))` hoặc `repeat(4, 1fr)`.
2. **Dùng Flexbox với `margin-top: auto`:** Để khóa nút bấm đáy card luôn nằm trên cùng một đường thẳng chân trời.
3. **Giới hạn số dòng tiêu đề:** Dùng `-webkit-line-clamp: 2` và `min-height` để tránh việc tên tour dài/ngắn làm xô lệch giá và nút bấm.

