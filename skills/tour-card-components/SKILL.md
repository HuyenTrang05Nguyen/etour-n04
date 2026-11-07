---
name: tour-card-components
description: Thư viện thành phần giao diện (UI Component Library) bằng CSS thuần chuẩn cho website du lịch miệt vườn: Thẻ tour bo góc đổ bóng sang trọng, nút bấm Gradient CTA, nhãn Sale nổi bật, ô tìm kiếm nhanh và thanh phân trang hiện đại.
---

# Thư viện Thành phần Giao diện Du lịch Miệt Vườn (Tour Card Components)

Bộ thành phần giao diện được viết sẵn bằng CSS thuần, tương thích hoàn hảo với WooCommerce và Storefront/Child Theme, giúp biến giao diện thô cứng thành giao diện thương mại điện tử du lịch bắt mắt.

---

## 1. Mẫu Thẻ Tour Chuẩn Sang Trọng (Tour Product Card)

```css
/* Container của thẻ tour */
.tour-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
  position: relative;
  height: 100%;
}

.tour-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.12);
  border-color: #cbd5e1;
}

/* Phần bọc ảnh tour */
.tour-card__img-wrap {
  position: relative;
  width: 100%;
  aspect-ratio: 4 / 3;
  overflow: hidden;
  background-color: #f1f5f9;
}

.tour-card__img-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
  display: block;
}

.tour-card:hover .tour-card__img-wrap img {
  transform: scale(1.06);
}

/* Nhãn Sale / Ưu đãi gắn trên ảnh */
.tour-card__badge-sale {
  position: absolute;
  top: 12px;
  right: 12px;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: #ffffff;
  font-size: 0.75rem;
  font-weight: 700;
  padding: 0.35rem 0.75rem;
  border-radius: 9999px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 4px 8px rgba(220, 38, 38, 0.35);
  z-index: 2;
}

/* Nhãn địa phương / Mùa vụ gắn góc dưới ảnh */
.tour-card__tag-location {
  position: absolute;
  bottom: 10px;
  left: 10px;
  background-color: rgba(15, 23, 42, 0.75);
  backdrop-filter: blur(4px);
  color: #ffffff;
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.25rem 0.65rem;
  border-radius: 6px;
  z-index: 2;
}

/* Nội dung thông tin bên dưới ảnh */
.tour-card__body {
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

/* Tiêu đề tour: Giới hạn đúng 2 dòng, căn ngang đều nhau */
.tour-card__title {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0f172a;
  line-height: 1.4;
  margin: 0 0 0.5rem 0;
  min-height: 2.8em;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  text-decoration: none;
}

.tour-card__title:hover {
  color: #15803d;
}

/* Giá tour */
.tour-card__price-box {
  margin: 0.5rem 0 1rem 0;
  min-height: 2.2em;
  display: flex;
  align-items: baseline;
  gap: 0.5rem;
}

.tour-card__price-current {
  font-size: 1.15rem;
  font-weight: 800;
  color: #ea580c; /* Cam điểm nhấn */
}

.tour-card__price-old {
  font-size: 0.85rem;
  color: #94a3b8;
  text-decoration: line-through;
}

/* Nút CTA bám đáy card */
.tour-card__btn {
  margin-top: auto;
  width: 100%;
  padding: 0.75rem 1rem;
  background-color: #15803d;
  color: #ffffff;
  font-weight: 600;
  font-size: 0.95rem;
  text-align: center;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  text-decoration: none;
  display: block;
  transition: background-color 0.2s ease, box-shadow 0.2s ease;
  box-shadow: 0 4px 10px rgba(21, 128, 61, 0.2);
}

.tour-card__btn:hover {
  background-color: #166534;
  box-shadow: 0 6px 14px rgba(21, 128, 61, 0.35);
  color: #ffffff;
}
```

---

## 2. Nút Bấm Hiện Đại (Modern Button Styles)

```css
/* Nút chính Gradient Xanh Sinh Thái */
.btn-primary-gradient {
  background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
  color: #ffffff !important;
  border: none;
  padding: 0.75rem 1.5rem;
  font-size: 0.95rem;
  font-weight: 600;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
  transition: all 0.25s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-primary-gradient:hover {
  background: linear-gradient(135deg, #15803d 0%, #166534 100%);
  box-shadow: 0 6px 18px rgba(22, 163, 74, 0.45);
  transform: translateY(-2px);
}

/* Nút phụ Viền Mỏng Sang Trọng (Outline) */
.btn-outline-modern {
  background: transparent;
  color: #15803d !important;
  border: 1.5px solid #15803d;
  padding: 0.7rem 1.4rem;
  font-size: 0.95rem;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.2s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-outline-modern:hover {
  background-color: #dcfce7;
  color: #166534 !important;
  border-color: #166534;
}
```

---

## 3. Thanh Phân Trang Tròn Đẹp (Modern Pagination)

```css
.woocommerce-pagination .page-numbers {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.4rem;
  border: none;
  padding: 1.5rem 0;
}

.woocommerce-pagination .page-numbers li {
  display: inline-block;
  margin: 0;
}

.woocommerce-pagination .page-numbers li .page-numbers {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.9rem;
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  color: #334155;
  transition: all 0.2s ease;
}

.woocommerce-pagination .page-numbers li .page-numbers:hover {
  background-color: #f1f5f9;
  border-color: #cbd5e1;
  color: #0f172a;
}

.woocommerce-pagination .page-numbers li .page-numbers.current {
  background-color: #15803d !important;
  border-color: #15803d !important;
  color: #ffffff !important;
  box-shadow: 0 4px 10px rgba(21, 128, 61, 0.3);
}
```

