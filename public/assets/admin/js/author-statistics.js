'use strict';

document.addEventListener('DOMContentLoaded', function () {
    var chartEl = document.getElementById('dailyViewsChart');
    if (!chartEl) return;

    var labels = JSON.parse(chartEl.dataset.labels || '[]');
    var values = JSON.parse(chartEl.dataset.values || '[]');

    if (!labels.length) {
        chartEl.innerHTML = '<p class="text-center text-clr-muted py-5">Henüz görüntülenme verisi yok.</p>';
        return;
    }

    var maxVal = Math.max.apply(null, values) || 1;
    var containerHeight = chartEl.offsetHeight || 280;
    var barAreaHeight = containerHeight - 30;

    var wrapper = document.createElement('div');
    wrapper.className = 'd-flex align-items-end justify-content-between gap-1';
    wrapper.style.height = barAreaHeight + 'px';
    wrapper.style.paddingTop = '20px';

    labels.forEach(function (label, i) {
        var val = values[i] || 0;
        var heightPercent = maxVal > 0 ? (val / maxVal) * 100 : 0;
        var minHeight = val > 0 ? 4 : 1;
        var barHeight = Math.max(minHeight, (heightPercent / 100) * barAreaHeight);

        var col = document.createElement('div');
        col.className = 'd-flex flex-column align-items-center flex-grow-1';
        col.style.minWidth = '0';

        var bar = document.createElement('div');
        bar.className = 'ast-bar';
        bar.style.width = '100%';
        bar.style.maxWidth = '24px';
        bar.style.height = '0px';
        bar.style.margin = '0 auto';

        var tooltip = document.createElement('div');
        tooltip.className = 'ast-bar-tooltip';
        tooltip.textContent = val.toLocaleString('tr-TR') + ' okunma';
        bar.appendChild(tooltip);

        var labelEl = document.createElement('div');
        labelEl.style.fontSize = '0.6rem';
        labelEl.style.color = 'var(--text-muted)';
        labelEl.style.marginTop = '4px';
        labelEl.style.whiteSpace = 'nowrap';
        labelEl.style.overflow = 'hidden';
        labelEl.style.textOverflow = 'ellipsis';
        labelEl.style.maxWidth = '100%';
        labelEl.textContent = label;

        col.appendChild(bar);
        col.appendChild(labelEl);
        wrapper.appendChild(col);

        requestAnimationFrame(function () {
            setTimeout(function () {
                bar.style.height = barHeight + 'px';
            }, i * 20);
        });
    });

    chartEl.innerHTML = '';
    chartEl.appendChild(wrapper);
});
