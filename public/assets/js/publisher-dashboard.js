const bySelector = (selector, parent = document) =>
  parent.querySelector(selector);
const all = (selector, parent = document) => [
  ...parent.querySelectorAll(selector),
];

function showToast(message) {
  const toast = bySelector(".toast");
  if (!toast) return;
  toast.textContent = message;
  toast.classList.add("is-visible");
  window.clearTimeout(showToast.timeout);
  showToast.timeout = window.setTimeout(
    () => toast.classList.remove("is-visible"),
    3200,
  );
}

function drawLineChart(canvas, data) {
  if (!canvas || !data) return;
  const context = canvas.getContext("2d");
  const rect = canvas.getBoundingClientRect();
  const width = Math.max(300, Math.round(rect.width));
  const height = Number(canvas.getAttribute("height")) || 290;
  const dpr = window.devicePixelRatio || 1;
  canvas.width = width * dpr;
  canvas.height = height * dpr;
  context.setTransform(dpr, 0, 0, dpr, 0, 0);
  context.clearRect(0, 0, width, height);

  const padding = { top: 18, right: 22, bottom: 32, left: 38 };
  const chartWidth = width - padding.left - padding.right;
  const chartHeight = height - padding.top - padding.bottom;
  const max = Math.ceil(Math.max(...data.revenue, ...data.downloads) / 50) * 50;
  const x = (index) =>
    padding.left + (chartWidth * index) / Math.max(data.labels.length - 1, 1);
  const y = (value) => padding.top + chartHeight - (value / max) * chartHeight;

  context.font = "11px Inter, system-ui, sans-serif";
  context.textAlign = "right";
  context.fillStyle = "#627d98";
  context.strokeStyle = "#e5e7eb";
  context.lineWidth = 1;
  for (let index = 0; index <= 4; index += 1) {
    const value = (max / 4) * index;
    const rowY = y(value);
    context.setLineDash([3, 4]);
    context.beginPath();
    context.moveTo(padding.left, rowY);
    context.lineTo(width - padding.right, rowY);
    context.stroke();
    context.setLineDash([]);
    context.fillText(String(Math.round(value)), padding.left - 7, rowY + 4);
  }
  context.textAlign = "center";
  data.labels.forEach((label, index) =>
    context.fillText(label, x(index), height - 9),
  );

  const line = (values, color) => {
    context.strokeStyle = color;
    context.lineWidth = 3;
    context.lineJoin = "round";
    context.lineCap = "round";
    context.beginPath();
    values.forEach((value, index) =>
      index
        ? context.lineTo(x(index), y(value))
        : context.moveTo(x(index), y(value)),
    );
    context.stroke();
    values.forEach((value, index) => {
      context.fillStyle = color;
      context.beginPath();
      context.arc(x(index), y(value), 3.7, 0, Math.PI * 2);
      context.fill();
    });
  };
  line(data.revenue, "#087e8b");
  line(data.downloads, "#ffca3a");
}

function drawReportChart() {
  const canvas = bySelector("#report-chart");
  if (!canvas) return;
  const months = ["T1", "T2", "T3", "T4", "T5", "T6", "T7"];
  const revenue = [245, 280, 320, 290, 350, 380, 420];
  const profit = [180, 210, 240, 220, 265, 290, 320];
  const context = canvas.getContext("2d");
  const rect = canvas.getBoundingClientRect();
  const width = Math.max(300, Math.round(rect.width));
  const height = Number(canvas.getAttribute("height")) || 290;
  const dpr = window.devicePixelRatio || 1;
  canvas.width = width * dpr;
  canvas.height = height * dpr;
  context.setTransform(dpr, 0, 0, dpr, 0, 0);
  context.clearRect(0, 0, width, height);
  const left = 42,
    right = 18,
    top = 15,
    bottom = 31,
    plotHeight = height - top - bottom,
    plotWidth = width - left - right,
    max = 450;
  context.font = "11px Inter,system-ui";
  context.fillStyle = "#627d98";
  context.textAlign = "right";
  context.strokeStyle = "#e5e7eb";
  for (let i = 0; i <= 4; i += 1) {
    const value = i * 100;
    const y = top + plotHeight - (value / max) * plotHeight;
    context.setLineDash([3, 4]);
    context.beginPath();
    context.moveTo(left, y);
    context.lineTo(width - right, y);
    context.stroke();
    context.setLineDash([]);
    context.fillText(value, left - 7, y + 4);
  }
  const groupWidth = plotWidth / months.length;
  const barWidth = Math.min(25, groupWidth * 0.28);
  months.forEach((month, i) => {
    const center = left + groupWidth * i + groupWidth / 2;
    const revenueHeight = (revenue[i] / max) * plotHeight;
    const profitHeight = (profit[i] / max) * plotHeight;
    context.fillStyle = "#087e8b";
    context.beginPath();
    context.roundRect(
      center - barWidth - 2,
      top + plotHeight - revenueHeight,
      barWidth,
      revenueHeight,
      [5, 5, 0, 0],
    );
    context.fill();
    context.fillStyle = "#ffca3a";
    context.beginPath();
    context.roundRect(
      center + 2,
      top + plotHeight - profitHeight,
      barWidth,
      profitHeight,
      [5, 5, 0, 0],
    );
    context.fill();
    context.fillStyle = "#627d98";
    context.textAlign = "center";
    context.fillText(month, center, height - 8);
  });
}

function downloadCsv(name, headers, rows) {
  const escape = (value) => `"${String(value).replaceAll('"', '""')}"`;
  const content =
    "\uFEFF" +
    [headers, ...rows].map((row) => row.map(escape).join(",")).join("\n");
  const link = document.createElement("a");
  link.href = URL.createObjectURL(
    new Blob([content], { type: "text/csv;charset=utf-8;" }),
  );
  link.download = `${name}.csv`;
  link.click();
  URL.revokeObjectURL(link.href);
}

function initialize() {
  all("[data-toast]").forEach((button) =>
    button.addEventListener("click", () => showToast(button.dataset.toast)),
  );
  const chart = bySelector("#revenue-chart");
  if (chart) {
    const periodData = JSON.parse(chart.dataset.chart || "{}");
    let period = "7days";
    const updateChart = () => drawLineChart(chart, periodData[period]);
    updateChart();
    all("[data-period]").forEach((button) =>
      button.addEventListener("click", () => {
        period = button.dataset.period;
        all("[data-period]").forEach((item) =>
          item.classList.toggle("is-selected", item === button),
        );
        updateChart();
      }),
    );
    window.addEventListener("resize", updateChart);
  }
  drawReportChart();
  window.addEventListener("resize", drawReportChart);

  all("[data-book-search]").forEach((input) =>
    input.addEventListener("input", () => {
      const search = input.value.trim().toLocaleLowerCase("vi");
      let found = 0;
      all("[data-book-row]").forEach((row) => {
        const matches = row.dataset.search
          .toLocaleLowerCase("vi")
          .includes(search);
        row.hidden = !matches;
        if (matches) found += 1;
      });
      const empty = bySelector(".empty-state");
      if (empty) empty.hidden = found !== 0;
    }),
  );
  const codeSearch = bySelector("[data-code-search]");
  codeSearch?.addEventListener("input", () =>
    all("[data-codes-table] tr").forEach((row) => {
      row.hidden = !row.textContent
        .toLocaleLowerCase("vi")
        .includes(codeSearch.value.toLocaleLowerCase("vi"));
    }),
  );

  bySelector("[data-cover-input]")?.addEventListener("change", (event) => {
    const file = event.currentTarget.files?.[0];
    const preview = bySelector("[data-cover-preview]");
    const placeholder = bySelector(".upload-placeholder");
    if (!file || !preview) return;
    preview.src = URL.createObjectURL(file);
    preview.hidden = false;
    if (placeholder) placeholder.hidden = true;
  });
  bySelector("[data-epub-input]")?.addEventListener("change", (event) => {
    const file = event.currentTarget.files?.[0];
    const fileName = bySelector("[data-epub-name]");
    if (!file) return;
    if (!file.name.toLowerCase().endsWith(".epub")) {
      event.currentTarget.value = "";
      showToast("Chỉ chấp nhận tệp có định dạng .epub.");
      return;
    }
    if (fileName) fileName.textContent = file.name;
  });
  // Form [data-book-form] được xử lý trực tiếp bằng PHP POST trong AddBookForm.php


  // Event delegation for promotion modal open/close
  document.addEventListener("click", (event) => {
    const openBtn = event.target.closest("[data-open-promotion]");
    if (openBtn) {
      const modal = bySelector("[data-promotion-modal]");
      if (modal) {
        modal.hidden = false;
        document.body.style.overflow = "hidden";
      }
    }

    const closeBtn = event.target.closest("[data-close-modal]");
    if (closeBtn) {
      const modal = bySelector("[data-promotion-modal]");
      if (modal) {
        modal.hidden = true;
        document.body.style.overflow = "";
      }
    }

    const modal = bySelector("[data-promotion-modal]");
    if (modal && !modal.hidden && event.target === modal) {
      modal.hidden = true;
      document.body.style.overflow = "";
    }
  });
  bySelector("[data-generate-codes]")?.addEventListener("click", () =>
    showToast("Đã tạo 50 mã nháp. Kết nối API để lưu và phân phối mã."),
  );
  all("[data-export]").forEach((button) =>
    button.addEventListener("click", () => {
      const report = button.dataset.export;
      if (report === "activation-codes")
        downloadCsv(
          "ma-kich-hoat",
          ["Mã kích hoạt", "Sản phẩm", "Trạng thái"],
          [
            ["RDL-Y7KP-9H2M", "Đắc Nhân Tâm", "Đã sử dụng"],
            ["RDL-Q8MT-4XKA", "Tâm Lý Học Hành Vi", "Chưa sử dụng"],
          ],
        );
      else
        downloadCsv(
          "bao-cao-nha-xuat-ban",
          ["Chỉ số", "Giá trị"],
          [
            ["Tổng doanh thu", "642.212.000₫"],
            ["Tổng độc giả", "8.547"],
            ["Sách đã bán", "5.218"],
          ],
        );
      showToast("Đã tải tệp CSV.");
    }),
  );
}

if (typeof document !== "undefined")
  document.addEventListener("DOMContentLoaded", initialize);
