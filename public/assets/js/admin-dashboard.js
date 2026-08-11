/**
 * Admin Dashboard JavaScript Module
 * Handles Bar Chart rendering, tab switching, search filtering, and UI toasts.
 */

document.addEventListener('DOMContentLoaded', () => {
  initChartSwitcher();
  initToastHandlers();
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
    labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
    values: [45000000, 52000000, 48000000, 61000000, 55000000, 67000000, 72000000]
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

  const tabButtons = document.querySelectorAll('.tab-btn');
  
  tabButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      tabButtons.forEach(b => b.classList.remove('active'));
      e.target.classList.add('active');
      currentTab = e.target.getAttribute('data-tab') || 'Tháng này';
      renderBarChart(canvas, chartDataset[currentTab]);
    });
  });

  // Initial draw
  renderBarChart(canvas, chartDataset[currentTab]);

  // Redraw on window resize
  window.addEventListener('resize', () => {
    renderBarChart(canvas, chartDataset[currentTab]);
  });
}

/**
 * Custom Canvas Bar Chart Renderer
 */
function renderBarChart(canvas, data) {
  const ctx = canvas.getContext('2d');
  const dpr = window.devicePixelRatio || 1;
  const rect = canvas.getBoundingClientRect();

  canvas.width = rect.width * dpr;
  canvas.height = rect.height * dpr;
  ctx.scale(dpr, dpr);

  const width = rect.width;
  const height = rect.height;

  ctx.clearRect(0, 0, width, height);

  const paddingLeft = 60;
  const paddingBottom = 40;
  const paddingTop = 20;
  const paddingRight = 20;

  const chartWidth = width - paddingLeft - paddingRight;
  const chartHeight = height - paddingTop - paddingBottom;

  const maxVal = Math.max(...data.values) * 1.15;
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
    const val = data.values[idx];
    const barHeight = (val / maxVal) * chartHeight;

    const xCenter = paddingLeft + idx * stepX + stepX / 2;
    const barX = xCenter - barWidth / 2;
    const barY = paddingTop + chartHeight - barHeight;

    // X Label
    ctx.fillStyle = '#6B7280';
    ctx.fillText(label, xCenter, paddingTop + chartHeight + 10);

    // Rounded top bar
    ctx.fillStyle = '#087E8B';
    const radius = Math.min(8, barHeight / 2);

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
    const valText = `${(val / 1000000).toFixed(1)}M`;
    ctx.fillText(valText, xCenter, barY - 14);
  });
}

/* Toast Notifications */
function initToastHandlers() {
  const toast = document.getElementById('adminToast');
  
  window.showToast = function(message) {
    if (!toast) return;
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => {
      toast.classList.remove('show');
    }, 3000);
  };

  // Delegate click for data-toast attributes
  document.addEventListener('click', (e) => {
    const target = e.target.closest('[data-toast]');
    if (target) {
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
