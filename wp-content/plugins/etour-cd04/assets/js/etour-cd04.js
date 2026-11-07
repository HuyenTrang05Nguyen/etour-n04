(function () {
  'use strict';

  const cfg = window.ETOUR_CD04 || {};
  const vnd = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', maximumFractionDigits: 0 });

  /* Tạo element nhanh */
  function el(tag, cls, text) {
    const node = document.createElement(tag);
    if (cls) node.className = cls;
    if (text !== undefined) node.textContent = text;
    return node;
  }

  /* Tháng tiếng Việt đầy đủ */
  const MONTHS = ['', 'Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6',
                      'Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'];

  /* ====================================================
     TRANG MÙA VỤ CD04
     ==================================================== */
  function initSeasonsPage() {
    const root = document.querySelector('[data-etour-cd04]');
    if (!root) return;

    const bar         = root.querySelector('[data-month-bar]');
    const meta        = root.querySelector('[data-cd04-meta]');
    const seasonsList = root.querySelector('[data-seasons-list]');
    const toursList   = root.querySelector('[data-tours-list]');
    if (!bar || !meta || !seasonsList || !toursList) return;

    /* --- Tải dữ liệu theo tháng --- */
    async function loadMonth(month) {
      if (!cfg.restUrl) { meta.textContent = 'Lỗi cấu hình REST URL.'; return; }

      // Cập nhật trạng thái active trên thanh tháng
      bar.querySelectorAll('[data-month]').forEach(b => {
        const active = Number(b.dataset.month) === month;
        b.classList.toggle('is-active', active);
        b.setAttribute('aria-pressed', active ? 'true' : 'false');
      });

      meta.textContent = 'Đang tải dữ liệu ' + MONTHS[month] + '…';
      seasonsList.innerHTML = '<p class="etour-loading">Đang tải…</p>';
      toursList.innerHTML   = '<p class="etour-loading">Đang tải…</p>';

      try {
        const u1 = new URL(cfg.restUrl + 'seasons'); u1.searchParams.set('month', month);
        const u2 = new URL(cfg.restUrl + 'suggest'); u2.searchParams.set('month', month);
        const [r1, r2] = await Promise.all([
          fetch(u1, { headers: { Accept: 'application/json' } }),
          fetch(u2, { headers: { Accept: 'application/json' } })
        ]);
        const [sData, tData] = await Promise.all([r1.json(), r2.json()]);

        /* ---- Render mùa vụ ---- */
        if (!sData.items || !sData.items.length) {
          seasonsList.innerHTML = '<p class="etour-muted">Không có mùa vụ nào trong tháng này.</p>';
        } else {
          seasonsList.replaceChildren(...sData.items.map(item => {
            const card = el('div', 'etour-season-card');

            // Tiêu đề + badge tỉnh/loại
            const header = el('div', 'etour-season-card__header');
            const title  = el('h4', 'etour-season-card__title', item.item_name);
            const badge  = el('span', 'etour-badge etour-badge--province',
              item.province + ' · ' + item.item_type +
              ' (T' + item.month_from + '–T' + item.month_to + ')');
            header.append(title, badge);

            // Mô tả
            const desc = el('p', 'etour-season-card__desc', item.description || '');

            // Nút hành động
            const actions = el('div', 'etour-season-card__actions');

            const btnCount = el('a', 'etour-btn-sm etour-btn-sm--outline', '⏱ Đếm ngược tới mùa');
            const monthEnd = new Date(new Date().getFullYear(), item.month_to, 0, 23, 59, 59);
            btnCount.href = '#';
            btnCount.dataset.countdown = monthEnd.toISOString();
            btnCount.addEventListener('click', e => {
              e.preventDefault();
              const diff = monthEnd - Date.now();
              if (diff > 0) {
                const d = Math.floor(diff / 86400000);
                const h = Math.floor((diff % 86400000) / 3600000);
                btnCount.textContent = '⏱ ' + d + 'n ' + h + 'g nữa';
              } else {
                btnCount.textContent = '⏱ Đang trong mùa!';
              }
            });

            const consultUrl = (cfg.consultUrl || '#etour-consult-form') +
              '?season=' + encodeURIComponent(item.item_name) + '&month=' + month;
            const btnConsult = el('a', 'etour-btn-sm etour-btn-sm--primary', 'Tư vấn mùa này');
            btnConsult.href = consultUrl;

            actions.append(btnCount, btnConsult);
            card.append(header, desc, actions);
            return card;
          }));
        }

        /* ---- Render tour gợi ý ---- */
        if (!tData.items || !tData.items.length) {
          toursList.innerHTML = '<p class="etour-muted">Chưa có tour gợi ý cho tháng này.</p>';
        } else {
          toursList.replaceChildren(...tData.items.map(tour => {
            const card = el('div', 'etour-tour-card');

            // Tiêu đề
            const h4 = el('h4', 'etour-tour-card__title');
            const a  = el('a', '', tour.name);
            a.href = tour.permalink;
            h4.append(a);

            // Meta: mã + danh mục
            const meta2 = el('p', 'etour-tour-card__meta',
              'Mã: ' + tour.sku + (tour.category ? ' · Danh mục: ' + tour.category : ''));

            // Giá
            const price = el('p', 'etour-tour-card__price',
              tour.price > 0 ? 'Giá từ: ' + vnd.format(tour.price) : 'Liên hệ');

            // Ảnh (nếu có)
            const imgWrap = el('div', 'etour-tour-card__img-wrap');
            if (tour.image) {
              const img = document.createElement('img');
              img.src = tour.image; img.alt = tour.name; img.loading = 'lazy';
              imgWrap.append(img);
            }

            // Nút
            const btn = el('a', 'etour-btn-sm etour-btn-sm--green', 'Xem &amp; Đặt tour ngay');
            btn.innerHTML = 'Xem &amp; Đặt tour ngay';
            btn.href = tour.permalink;

            card.append(imgWrap, h4, meta2, price, btn);
            return card;
          }));
        }

        /* ---- Meta summary ---- */
        meta.textContent = 'Đang xem ' + MONTHS[month] + ': Có ' +
          sData.count + ' mùa vụ đặc trưng và ' + tData.count + ' tour miệt vườn phù hợp.';
        meta.className = 'etour-cd04__meta etour-cd04__meta--loaded';

      } catch (err) {
        meta.textContent = 'Lỗi tải dữ liệu. Vui lòng thử lại.';
        console.error('[etour-cd04]', err);
      }
    }

    /* --- Sự kiện click tháng --- */
    bar.addEventListener('click', e => {
      const btn = e.target.closest('[data-month]');
      if (!btn) return;
      loadMonth(Number(btn.dataset.month));
    });

    /* --- Tải tháng mặc định --- */
    loadMonth(Number(root.dataset.currentMonth) || new Date().getMonth() + 1);
  }

  /* ====================================================
     ĐỒNG HỒ ĐẾM NGƯỢC (dùng chung trên mọi trang)
     ==================================================== */
  function initCountdown() {
    document.querySelectorAll('.etour-countdown-timer[data-countdown]').forEach(function(timer) {
      const end = new Date(timer.dataset.countdown).getTime();
      if (isNaN(end)) return;

      const dEl = timer.querySelector('[data-countdown-days]');
      const hEl = timer.querySelector('[data-countdown-hours]');
      const mEl = timer.querySelector('[data-countdown-mins]');
      const sEl = timer.querySelector('[data-countdown-secs]');
      if (!dEl || !hEl || !mEl || !sEl) return;

      function tick() {
        const diff = end - Date.now();
        if (diff <= 0) {
          [dEl, hEl, mEl, sEl].forEach(e => e.textContent = '00');
          return;
        }
        dEl.textContent = String(Math.floor(diff / 86400000)).padStart(2, '0');
        hEl.textContent = String(Math.floor((diff % 86400000) / 3600000)).padStart(2, '0');
        mEl.textContent = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
        sEl.textContent = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
      }
      tick();
      setInterval(tick, 1000);
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    initSeasonsPage();
    initCountdown();
  });

})();
