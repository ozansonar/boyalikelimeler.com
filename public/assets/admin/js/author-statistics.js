'use strict';

document.addEventListener('DOMContentLoaded', function () {
    initDailyViewsChart();
    initCategoryDonutChart();
    initWorkComparisonChart();
});

/* ─── Daily Views Bar Chart ─── */
function initDailyViewsChart() {
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
}

/* ─── Category Donut Chart ─── */
function initCategoryDonutChart() {
    var el = document.getElementById('categoryDonutChart');
    if (!el) return;

    var labels = JSON.parse(el.dataset.labels || '[]');
    var values = JSON.parse(el.dataset.values || '[]');
    var colors = JSON.parse(el.dataset.colors || '[]');

    if (!labels.length) return;

    var total = values.reduce(function (a, b) { return a + b; }, 0);
    if (total === 0) return;

    var size = 180;
    var center = size / 2;
    var radius = 72;
    var innerRadius = 48;
    var startAngle = -Math.PI / 2;

    var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('width', size);
    svg.setAttribute('height', size);
    svg.setAttribute('viewBox', '0 0 ' + size + ' ' + size);
    svg.style.display = 'block';
    svg.style.margin = '0 auto';

    values.forEach(function (val, i) {
        var sliceAngle = (val / total) * Math.PI * 2;
        var endAngle = startAngle + sliceAngle;

        var x1 = center + radius * Math.cos(startAngle);
        var y1 = center + radius * Math.sin(startAngle);
        var x2 = center + radius * Math.cos(endAngle);
        var y2 = center + radius * Math.sin(endAngle);

        var ix1 = center + innerRadius * Math.cos(startAngle);
        var iy1 = center + innerRadius * Math.sin(startAngle);
        var ix2 = center + innerRadius * Math.cos(endAngle);
        var iy2 = center + innerRadius * Math.sin(endAngle);

        var largeArc = sliceAngle > Math.PI ? 1 : 0;

        var d = [
            'M', x1, y1,
            'A', radius, radius, 0, largeArc, 1, x2, y2,
            'L', ix2, iy2,
            'A', innerRadius, innerRadius, 0, largeArc, 0, ix1, iy1,
            'Z'
        ].join(' ');

        var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        path.setAttribute('d', d);
        path.setAttribute('fill', colors[i % colors.length]);
        path.style.opacity = '0.85';
        path.style.transition = 'opacity 0.2s, transform 0.2s';
        path.style.transformOrigin = center + 'px ' + center + 'px';
        path.style.cursor = 'pointer';

        var titleEl = document.createElementNS('http://www.w3.org/2000/svg', 'title');
        titleEl.textContent = labels[i] + ': ' + val + ' eser (%' + Math.round((val / total) * 100) + ')';
        path.appendChild(titleEl);

        path.addEventListener('mouseenter', function () {
            path.style.opacity = '1';
            path.style.transform = 'scale(1.04)';
        });
        path.addEventListener('mouseleave', function () {
            path.style.opacity = '0.85';
            path.style.transform = 'scale(1)';
        });

        svg.appendChild(path);
        startAngle = endAngle;
    });

    /* Center text */
    var centerText = document.createElementNS('http://www.w3.org/2000/svg', 'text');
    centerText.setAttribute('x', center);
    centerText.setAttribute('y', center - 6);
    centerText.setAttribute('text-anchor', 'middle');
    centerText.setAttribute('fill', 'var(--text-primary)');
    centerText.setAttribute('font-size', '22');
    centerText.setAttribute('font-weight', '700');
    centerText.textContent = total;
    svg.appendChild(centerText);

    var subText = document.createElementNS('http://www.w3.org/2000/svg', 'text');
    subText.setAttribute('x', center);
    subText.setAttribute('y', center + 14);
    subText.setAttribute('text-anchor', 'middle');
    subText.setAttribute('fill', 'var(--text-muted)');
    subText.setAttribute('font-size', '11');
    subText.textContent = 'eser';
    svg.appendChild(subText);

    el.innerHTML = '';
    el.appendChild(svg);
}

/* ─── Work Comparison Horizontal Bar Chart ─── */
function initWorkComparisonChart() {
    var el = document.getElementById('workComparisonChart');
    if (!el) return;

    var labels = JSON.parse(el.dataset.labels || '[]');
    var views = JSON.parse(el.dataset.views || '[]');
    var comments = JSON.parse(el.dataset.comments || '[]');
    var favorites = JSON.parse(el.dataset.favorites || '[]');

    if (!labels.length) {
        el.innerHTML = '<p class="text-center text-clr-muted py-5">Henüz eser verisi yok.</p>';
        return;
    }

    var maxViews = Math.max.apply(null, views) || 1;

    /* Legend */
    var legend = document.createElement('div');
    legend.className = 'd-flex gap-4 mb-3';
    legend.innerHTML =
        '<div class="d-flex align-items-center gap-2"><span class="ast-legend-dot" style="background:#14b8a6"></span><small class="text-clr-muted">Okunma</small></div>' +
        '<div class="d-flex align-items-center gap-2"><span class="ast-legend-dot" style="background:#3b82f6"></span><small class="text-clr-muted">Yorum</small></div>' +
        '<div class="d-flex align-items-center gap-2"><span class="ast-legend-dot" style="background:#a855f7"></span><small class="text-clr-muted">Favori</small></div>';
    el.appendChild(legend);

    var container = document.createElement('div');
    container.className = 'ast-hbar-list';

    labels.forEach(function (label, i) {
        var row = document.createElement('div');
        row.className = 'ast-hbar-row';

        var labelDiv = document.createElement('div');
        labelDiv.className = 'ast-hbar-label';
        labelDiv.textContent = label;
        labelDiv.title = label;

        var barsDiv = document.createElement('div');
        barsDiv.className = 'ast-hbar-bars';

        var viewWidth = maxViews > 0 ? Math.max(2, (views[i] / maxViews) * 100) : 0;

        var viewBar = document.createElement('div');
        viewBar.className = 'ast-hbar-bar ast-hbar-views';
        viewBar.style.width = '0%';

        var viewTooltip = document.createElement('span');
        viewTooltip.className = 'ast-hbar-value';
        viewTooltip.textContent = views[i].toLocaleString('tr-TR');
        viewBar.appendChild(viewTooltip);

        barsDiv.appendChild(viewBar);

        var statsDiv = document.createElement('div');
        statsDiv.className = 'ast-hbar-stats';
        statsDiv.innerHTML =
            '<span class="ast-hbar-stat text-teal">' + views[i].toLocaleString('tr-TR') + ' <small>okunma</small></span>' +
            '<span class="ast-hbar-stat" style="color:#3b82f6">' + comments[i] + ' <small>yorum</small></span>' +
            '<span class="ast-hbar-stat" style="color:#a855f7">' + favorites[i] + ' <small>favori</small></span>';

        row.appendChild(labelDiv);
        row.appendChild(barsDiv);
        row.appendChild(statsDiv);
        container.appendChild(row);

        requestAnimationFrame(function () {
            setTimeout(function () {
                viewBar.style.width = viewWidth + '%';
            }, i * 60);
        });
    });

    el.appendChild(container);
}
