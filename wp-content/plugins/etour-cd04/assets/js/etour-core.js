/**
 * E-Tourism Core – JavaScript thuần (Vanilla JS), không phụ thuộc jQuery.
 * 1) Nạp danh sách điểm đến từ REST API bằng Fetch và kết xuất vào DOM.
 * 2) Kiểm tra hợp lệ form phía client (máy chủ vẫn kiểm tra lại).
 */
(function () {
  'use strict';

  const cfg = window.ETOUR || {};
  const vnd = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', maximumFractionDigits: 0 });

  /* ---------- 1. Fetch + render ---------- */
  async function loadPlaces(province) {
    const url = new URL(cfg.restUrl + 'places');
    if (province) url.searchParams.set('province', province);
    const res = await fetch(url, { headers: { Accept: 'application/json' } });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.json();
  }

  function card(item) {
    // Tạo phần tử bằng createElement + textContent để tránh XSS (không dùng innerHTML với dữ liệu).
    const el = document.createElement('article');
    el.className = 'etour-card';
    const h = document.createElement('h3');
    h.textContent = item.name;
    const meta = document.createElement('p');
    meta.className = 'etour-card__meta';
    meta.textContent = item.province + ' · ' + item.category;
    const sum = document.createElement('p');
    sum.textContent = item.summary || '';
    const price = document.createElement('p');
    price.className = 'etour-card__price';
    price.textContent = item.price > 0 ? 'Từ ' + vnd.format(item.price) : 'Miễn phí tham quan';
    el.append(h, meta, sum, price);
    return el;
  }

  function renderPlaces(box, data) {
    box.replaceChildren();
    if (!data.items.length) {
      const p = document.createElement('p');
      p.className = 'etour-muted';
      p.textContent = 'Không có điểm đến phù hợp.';
      box.append(p);
      return;
    }
    const frag = document.createDocumentFragment();
    data.items.forEach((it) => frag.append(card(it)));
    box.append(frag);
  }

  async function initPlaces() {
    const box = document.querySelector('[data-etour-places]');
    if (!box) return;
    const select = document.querySelector('[data-etour-filter]');
    try {
      const all = await loadPlaces('');
      renderPlaces(box, all);
      // Sinh danh sách tỉnh duy nhất cho bộ lọc.
      [...new Set(all.items.map((i) => i.province))].sort((a, b) => a.localeCompare(b, 'vi')).forEach((pv) => {
        select.append(new Option(pv, pv));
      });
      select.addEventListener('change', async () => {
        box.setAttribute('aria-busy', 'true');
        try {
          renderPlaces(box, await loadPlaces(select.value));
        } finally {
          box.removeAttribute('aria-busy');
        }
      });
    } catch (err) {
      box.replaceChildren();
      const p = document.createElement('p');
      p.className = 'etour-alert etour-alert--err';
      p.textContent = 'Không tải được dữ liệu. Vui lòng thử lại sau.';
      box.append(p);
      console.warn('[etour] places:', err.message); // cảnh báo, không phải lỗi chưa xử lý
    }
  }

  /* ---------- 2. Client-side validation ---------- */
  function initForm() {
    const form = document.querySelector('[data-etour-form]');
    if (!form) return;
    const errBox = form.querySelector('[data-etour-error]');
    const date = form.querySelector('#etour-date');
    const today = new Date();
    today.setMinutes(today.getMinutes() - today.getTimezoneOffset());
    date.min = today.toISOString().slice(0, 10); // không cho chọn ngày quá khứ

    form.addEventListener('submit', (e) => {
      const msgs = [];
      const v = (sel) => form.querySelector(sel).value.trim();
      if (!v('#etour-place')) msgs.push('Chọn điểm đến.');
      if (v('#etour-name').length < 2) msgs.push('Họ và tên tối thiểu 2 ký tự.');
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v('#etour-email'))) msgs.push('Email không đúng định dạng.');
      if (!/^0\d{9}$/.test(v('#etour-phone'))) msgs.push('Số điện thoại gồm 10 chữ số, bắt đầu bằng 0.');
      if (!v('#etour-date') || v('#etour-date') < date.min) msgs.push('Ngày khởi hành phải từ hôm nay trở đi.');
      const g = Number(v('#etour-guests'));
      if (!Number.isInteger(g) || g < 1 || g > 50) msgs.push('Số khách từ 1 đến 50.');
      if (!form.querySelector('#etour-consent').checked) msgs.push('Cần đồng ý với Chính sách quyền riêng tư.');
      if (msgs.length) {
        e.preventDefault();
        errBox.textContent = msgs.join(' ');
        errBox.hidden = false;
        errBox.focus();
      }
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initPlaces();
    initForm();
  });
})();
