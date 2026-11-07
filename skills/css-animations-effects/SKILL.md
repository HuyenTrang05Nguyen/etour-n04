---
name: css-animations-effects
description: Bộ hiệu ứng chuyển động và đồ họa CSS hiện đại (CSS Animations & Visual Effects) cho website: hiệu ứng kính mờ (Glassmorphism), shimmer tải trang, hiệu ứng rung lắc nhẹ khi thêm giỏ hàng, và các micro-animations tinh tế.
---

# Bộ Hiệu ứng CSS Chuyển động & Đồ họa Hiện đại (CSS Animations & Effects)

Bộ sưu tập các hiệu ứng hình ảnh giúp website trở nên sống động, tương tác trực quan cao và giữ chân người dùng tốt hơn.

---

## 1. Hiệu ứng Kính mờ Sang trọng (Glassmorphism)

Tạo cảm giác hiện đại, trong suốt cho các khối bộ lọc, thanh thông báo hoặc thanh điều hướng nổi:

```css
.glass-panel {
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.4);
  box-shadow: 0 8px 32px 0 rgba(15, 23, 42, 0.08);
  border-radius: 12px;
}
```

---

## 2. Nhịp thở cho Nút / Nhãn Khuyến mãi (Pulse Badge Animation)

Thu hút ánh nhìn của khách hàng vào các tour đang "Hot" hoặc "Ưu đãi có hạn":

```css
@keyframes badge-pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(234, 88, 12, 0.5);
  }
  70% {
    box-shadow: 0 0 0 8px rgba(234, 88, 12, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(234, 88, 12, 0);
  }
}

.badge-pulse {
  animation: badge-pulse 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
}
```

---

## 3. Hiệu ứng Lấp lánh khi Tải trang (Skeleton Shimmer Loading)

Tránh cảm giác trống trải khi đang tải danh sách tour hoặc hình ảnh:

```css
@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

.skeleton-loading {
  background: linear-gradient(
    90deg,
    #f1f5f9 25%,
    #e2e8f0 50%,
    #f1f5f9 75%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 6px;
}
```

---

## 4. Hiệu ứng Rung phản hồi khi Thêm giỏ hàng (Cart Bounce / Feedback)

Tạo cảm giác thành công khi khách hàng bấm đặt tour:

```css
@keyframes btn-bounce {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(0.96);
  }
}

.button:active {
  animation: btn-bounce 0.15s ease-in-out;
}
```

