// resources/js/charts/bar-chart.js

export function renderBarChart(canvas, chartConfig) {
  const metaTitleKeys    = chartConfig.options?.metaTitleKeys    ?? ['line_name', 'company_name', 'product_name'];
  const maxMetaInTooltip = chartConfig.options?.maxMetaInTooltip ?? 5;
  const showMeta         = chartConfig.options?.showMetaInTooltip ?? false;
  const horizontal       = chartConfig.options?.horizontal       ?? false;

  return new Chart(canvas.getContext('2d'), {
    type: 'bar',
    data: {
      labels:   chartConfig.labels,
      datasets: chartConfig.datasets,   // ← جاهزة من الباك إند
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      indexAxis: horizontal ? 'y' : 'x',
      plugins: {
        legend: { display: false },
        title: {
          display: true,
          text: chartConfig.title,
          font: { size: 14, weight: 'bold' },
        },
        tooltip: {
          // ✅ tooltip عادي: اسم + قيمة
          callbacks: {
            label: (ctx) => {
              const v = ctx.raw ?? 0;
              return `${ctx.label}: ${v}`;
            },
            // ✅ التفاصيل تحت السطر الأساسي — بس لو مفعّلة
            afterBody: (items) => {
              if (!showMeta) return [];
              const item  = items[0];
              const metas = chartConfig.meta?.[item.dataIndex] ?? [];
              if (!Array.isArray(metas) || metas.length === 0) return [];

              const shown = metas.slice(0, maxMetaInTooltip);
              const lines = shown.map((m) => {
                const parts = metaTitleKeys
                  .map((k) => (m?.[k] ?? '').toString().trim())
                  .filter(Boolean);
                return parts.length ? '• ' + parts.join(' — ') : null;
              }).filter(Boolean);

              if (metas.length > maxMetaInTooltip) {
                lines.push(`... و ${metas.length - maxMetaInTooltip} عنصر آخر`);
              }
              return lines;
            },
          },
          // ✅ يمنع الـ tooltip من تغطية الشارت
          displayColors: false,
          padding: 8,
          titleFont:  { size: 13 },
          bodyFont:   { size: 12 },
          maxWidth: 320,
        },
      },
      scales: {
        x: {
          ticks: {
            autoSkip: true,
            maxRotation: 45,
            minRotation: 0,
            // يقصّر الأسماء الطويلة عشان الشكل ما يبوظش
            callback: function (val) {
              const label = this.getLabelForValue(val);
              return label.length > 18 ? label.slice(0, 18) + '…' : label;
            },
          },
        },
        y: {
          beginAtZero: true,
          ticks: { precision: 0 },
        },
      },
      // ✅ أقصى طول للـ tooltip
      animation: { duration: 400 },
    },
  });
}