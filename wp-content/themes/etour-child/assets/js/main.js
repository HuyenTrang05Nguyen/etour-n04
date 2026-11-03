/**
 * E-Tourism Child – main.js (Vanilla JS, không dùng jQuery)
 * Mẫu 2 tương tác chung: (1) đếm ngược khuyến mãi; (2) nút "Lên đầu trang".
 * Nhóm bổ sung các tương tác đặc thù chủ đề theo cùng cấu trúc: mỗi chức năng một hàm init…().
 */
(function () {
  'use strict';

  /** Định dạng số giây còn lại thành "N ngày HH:MM:SS". */
  function formatRemain(sec) {
    const d = Math.floor(sec / 86400);
    const h = Math.floor((sec % 86400) / 3600);
    const m = Math.floor((sec % 3600) / 60);
    const s = sec % 60;
    const pad = (n) => String(n).padStart(2, '0');
    return (d > 0 ? d + ' ngày ' : '') + pad(h) + ':' + pad(m) + ':' + pad(s);
  }

  /** (1) Đếm ngược cho mọi phần tử có thuộc tính data-countdown="ISO-8601". */
  function initCountdown() {
    document.querySelectorAll('[data-countdown]').forEach((box) => {
      const end = Date.parse(box.dataset.countdown);
      const out = box.querySelector('[data-countdown-text]') || box;
      if (Number.isNaN(end)) return; // dữ liệu sai → bỏ qua, không phát sinh lỗi Console
      const tick = () => {
        const remain = Math.max(0, Math.floor((end - Date.now()) / 1000));
        out.textContent = remain > 0 ? formatRemain(remain) : 'Đã kết thúc';
        if (remain === 0) {
          box.classList.add('is-ended');
          clearInterval(timer);
        }
      };
      const timer = setInterval(tick, 1000);
      tick();
    });
  }

  /** (2) Nút lên đầu trang – chỉ xuất hiện khi cuộn quá 600px. */
  function initBackToTop() {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'etc-top';
    btn.setAttribute('aria-label', 'Lên đầu trang');
    btn.textContent = '↑';
    btn.hidden = true;
    document.body.append(btn);
    btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    window.addEventListener('scroll', () => { btn.hidden = window.scrollY < 600; }, { passive: true });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initCountdown();
    initBackToTop();
  });

  // Xuất hàm thuần để kiểm thử đơn vị trong Console: ETC.formatRemain(3661) → "01:01:01"
  window.ETC = { formatRemain };
})();
