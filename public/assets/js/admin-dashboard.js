/**
 * Admin Dashboard JavaScript Module
 * Handles Bar Chart rendering, tab switching, search filtering, action buttons, and UI toasts.
 */

document.addEventListener('DOMContentLoaded', () => {
  initChartSwitcher();
  initToastHandlers();
  initActionButtons();
});

/* Chart Data Configuration */
const chartDataset = {
  'Hôm nay': {
    labels: ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00'],
    values: [5200000, 8400000, 12100000, 9500000, 15300000, 18200000, 14000000]
  },
  'Tuần này': {
    labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
    values: [28000000, 35000000, 42000000, 39000000, 51000000, 68000000, 74000000]
  },
  'Tháng này': {
    labels: ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4'],
    values: [45000000, 52000000, 67000000, 72000000]
  },
  'Năm nay': {
    labels: ['Q1', 'Q2', 'Q3', 'Q4'],
    values: [145000000, 182000000, 195000000, 240000000]
  }
};

let currentTab = 'Tháng này';

function initChartSwitcher() {
  const canvas = document.getElementById('revenueBarChart');
  if (!canvas) return;

  // Read real data passed from PHP DB query via data-chart attribute
  const rawChartData = canvas.getAttribute('data-chart');
  let dynamicChartData = null;
  if (rawChartData) {
    try {
      dynamicChartData = JSON.parse(rawChartData);
    } catch (e) {
      console.error('Failed to parse chart data', e);
    }
  }

  const activeDataset = dynamicChartData || chartDataset;
  const tabButtons = document.querySelectorAll('.tab-btn');

  tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      tabButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentTab = btn.getAttribute('data-tab') || 'Tháng này';
      const dataToRender = activeDataset[currentTab] || { labels: ['—'], values: [0] };
      renderBarChart(canvas, dataToRender);
    });
  });

  // Initial draw
  const initialData = activeDataset[currentTab] || { labels: ['—'], values: [0] };
  renderBarChart(canvas, initialData);

  // Redraw on window resize
  window.addEventListener('resize', () => {
    const currentData = activeDataset[currentTab] || { labels: ['—'], values: [0] };
    renderBarChart(canvas, currentData);
  });
}

/**
 * Custom Canvas Bar Chart Renderer
 */
function renderBarChart(canvas, data) {
  const ctx = canvas.getContext('2d');
  const dpr = window.devicePixelRatio || 1;
  const container = canvas.parentElement || canvas;
  const rect = container.getBoundingClientRect();

  const width = rect.width || 600;
  const height = rect.height || 280;

  canvas.width = width * dpr;
  canvas.height = height * dpr;
  ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

  ctx.clearRect(0, 0, width, height);

  const paddingLeft = 60;
  const paddingBottom = 40;
  const paddingTop = 20;
  const paddingRight = 20;

  const chartWidth = width - paddingLeft - paddingRight;
  const chartHeight = height - paddingTop - paddingBottom;

  const maxVal = Math.max(...data.values, 100000) * 1.15;
  const stepCount = 5;

  // Draw Grid & Y-Axis Labels
  ctx.strokeStyle = '#E5E7EB';
  ctx.lineWidth = 1;
  ctx.fillStyle = '#6B7280';
  ctx.font = '12px sans-serif';
  ctx.textAlign = 'right';
  ctx.textBaseline = 'middle';

  for (let i = 0; i <= stepCount; i++) {
    const yVal = (maxVal / stepCount) * i;
    const yPos = paddingTop + chartHeight - (i / stepCount) * chartHeight;

    // Grid line
    ctx.beginPath();
    ctx.setLineDash([3, 3]);
    ctx.moveTo(paddingLeft, yPos);
    ctx.lineTo(width - paddingRight, yPos);
    ctx.stroke();
    ctx.setLineDash([]);

    // Y Label in Million/Short format
    const labelText = yVal >= 1000000 ? `${(yVal / 1000000).toFixed(0)}M` : `${yVal.toLocaleString('vi-VN')}đ`;
    ctx.fillText(labelText, paddingLeft - 10, yPos);
  }

  // Draw X-Axis Labels & Bars
  const barCount = data.labels.length;
  const stepX = chartWidth / barCount;
  const barWidth = Math.min(stepX * 0.45, 48);

  ctx.textAlign = 'center';
  ctx.textBaseline = 'top';

  data.labels.forEach((label, idx) => {
    const val = data.values[idx] || 0;
    const barHeight = (val / maxVal) * chartHeight;

    const xCenter = paddingLeft + idx * stepX + stepX / 2;
    const barX = xCenter - barWidth / 2;
    const barY = paddingTop + chartHeight - barHeight;

    // X Label
    ctx.fillStyle = '#6B7280';
    ctx.fillText(label, xCenter, paddingTop + chartHeight + 10);

    // Rounded top bar
    ctx.fillStyle = '#087E8B';
    const radius = Math.min(8, Math.max(0, barHeight / 2));

    if (barHeight > 0) {
      ctx.beginPath();
      ctx.moveTo(barX, barY + radius);
      ctx.arcTo(barX, barY, barX + radius, barY, radius);
      ctx.arcTo(barX + barWidth, barY, barX + barWidth, barY + radius, radius);
      ctx.lineTo(barX + barWidth, barY + barHeight);
      ctx.lineTo(barX, barY + barHeight);
      ctx.closePath();
      ctx.fill();

      // Formatted value on top of bar
      ctx.fillStyle = '#102A43';
      ctx.font = '11px sans-serif';
      const valText = val >= 1000000 ? `${(val / 1000000).toFixed(1)}M` : `${val.toLocaleString('vi-VN')}đ`;
      ctx.fillText(valText, xCenter, barY - 14);
    }
  });
}

/* Toast Notifications */
function initToastHandlers() {
  const toast = document.getElementById('adminToast');

  window.showToast = function (message) {
    if (!toast) return;
    toast.textContent = message;
    toast.classList.add('show');
    clearTimeout(window.toastTimer);
    window.toastTimer = setTimeout(() => {
      toast.classList.remove('show');
    }, 3000);
  };

  // Delegate click for data-toast attributes
  document.addEventListener('click', (e) => {
    const target = e.target.closest('[data-toast]');
    if (target && !target.classList.contains('btn-action')) {
      const msg = target.getAttribute('data-toast');
      if (msg) showToast(msg);
    }
  });

  // Search input handler
  const searchInput = document.querySelector('.admin-search-input');
  if (searchInput) {
    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        const query = searchInput.value.trim();
        if (query) {
          showToast(`Đã tìm kiếm với từ khóa: "${query}"`);
        }
      }
    });
  }
}

/* Action buttons handler for Book Review (Duyệt, Từ chối, Yêu cầu sửa) */
function initActionButtons() {
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-action');
    if (!btn) return;

    const toastMsg = btn.getAttribute('data-toast');
    if (toastMsg && window.showToast) {
      window.showToast(toastMsg);
    }

    const card = btn.closest('.pending-book-card, .book-row');
    const bookId = btn.getAttribute('data-book-id') || (card ? card.getAttribute('data-book-id') : 0);
    const bookTitle = btn.getAttribute('data-book-title') || (card ? card.getAttribute('data-book-title') : '');

    let newStatus = 'published';
    if (btn.classList.contains('btn-reject')) {
      newStatus = 'rejected';
    } else if (btn.classList.contains('btn-request-edit')) {
      newStatus = 'draft';
    }

    // Send POST request to persist status update in MySQL database
    const formData = new FormData();
    formData.append('admin_action', 'update_book_status');
    formData.append('book_id', bookId || 0);
    formData.append('book_title', bookTitle || '');
    formData.append('status', newStatus);

    fetch(window.location.href, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    }).catch(err => console.error('Failed to update book status:', err));

    // Update real-time stats cards on top if present
    const pendingEl = document.getElementById('statPendingCount');
    const approvedEl = document.getElementById('statApprovedTodayCount');
    const rejectedEl = document.getElementById('statRejectedTodayCount');
    const navBadgeEl = document.getElementById('navPendingBadge');

    let newPendingVal = 0;
    if (pendingEl) {
      const current = parseInt(pendingEl.textContent, 10) || 0;
      newPendingVal = Math.max(0, current - 1);
      pendingEl.textContent = newPendingVal;
    } else if (navBadgeEl) {
      const current = parseInt(navBadgeEl.textContent, 10) || 0;
      newPendingVal = Math.max(0, current - 1);
    }

    if (navBadgeEl) {
      if (newPendingVal > 0) {
        navBadgeEl.textContent = newPendingVal;
      } else {
        navBadgeEl.style.display = 'none';
      }
    }

    if (btn.classList.contains('btn-approve') && approvedEl) {
      const current = parseInt(approvedEl.textContent, 10) || 0;
      approvedEl.textContent = current + 1;
    } else if (btn.classList.contains('btn-reject') && rejectedEl) {
      const current = parseInt(rejectedEl.textContent, 10) || 0;
      rejectedEl.textContent = current + 1;
    }

    // Update status badge visually
    if (card) {
      const badge = card.querySelector('.status-badge-yellow, [class*="status-badge"], span[style*="background"]');

      if (btn.classList.contains('btn-approve')) {
        if (badge) {
          badge.textContent = 'Đã duyệt';
          badge.className = 'status-badge-green';
          badge.style.backgroundColor = '#C6F6D5';
          badge.style.color = '#22543D';
        }
        btn.parentElement.innerHTML = `<span style="font-size:0.8rem;color:#22543D;font-weight:600;">✓ Đã phê duyệt</span>`;
      } else if (btn.classList.contains('btn-reject')) {
        if (badge) {
          badge.textContent = 'Từ chối';
          badge.className = 'status-badge-red';
          badge.style.backgroundColor = '#FED7D7';
          badge.style.color = '#9B2C2C';
        }
        btn.parentElement.innerHTML = `<span style="font-size:0.8rem;color:#9B2C2C;font-weight:600;">✕ Đã từ chối</span>`;
      } else if (btn.classList.contains('btn-request-edit')) {
        if (badge) {
          badge.textContent = 'Yêu cầu sửa';
          badge.style.backgroundColor = '#FEEBC8';
          badge.style.color = '#7B341E';
        }
        btn.parentElement.innerHTML = `<span style="font-size:0.8rem;color:#D69E2E;font-weight:600;">✎ Đã yêu cầu sửa</span>`;
      }
    }
  });
}
