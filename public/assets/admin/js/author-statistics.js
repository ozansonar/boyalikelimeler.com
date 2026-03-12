'use strict';

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.ast-area-chart').forEach(initAreaChart);
    document.querySelectorAll('.ast-vbar-chart').forEach(initVerticalBarChart);
    initCategoryDonutChart();
    initWorkComparisonChart();
});

/* ════════════════════════════════════════════
   Area Chart (Son 7 Gün / Son 30 Gün)
   ════════════════════════════════════════════ */
function initAreaChart(el) {
    var labels = JSON.parse(el.dataset.labels || '[]');
    var values = JSON.parse(el.dataset.values || '[]');
    var color = el.dataset.color || '#14b8a6';
    var chartHeight = parseInt(el.dataset.height) || 220;

    if (!labels.length) {
        el.innerHTML = '<p class="text-center text-clr-muted py-5">Henüz görüntülenme verisi yok.</p>';
        return;
    }

    var maxVal = Math.max.apply(null, values) || 1;
    var padding = { top: 20, right: 16, bottom: 40, left: 50 };
    var width = el.offsetWidth || 600;
    var height = chartHeight;
    var plotW = width - padding.left - padding.right;
    var plotH = height - padding.top - padding.bottom;

    var svg = createSVG(width, height);

    /* Y-axis grid lines + labels */
    var gridCount = 4;
    for (var g = 0; g <= gridCount; g++) {
        var gy = padding.top + (plotH / gridCount) * g;
        var gridVal = Math.round(maxVal - (maxVal / gridCount) * g);

        var gridLine = svgEl('line', {
            x1: padding.left, y1: gy, x2: width - padding.right, y2: gy,
            stroke: 'rgba(255,255,255,0.06)', 'stroke-width': 1
        });
        svg.appendChild(gridLine);

        var yLabel = svgEl('text', {
            x: padding.left - 8, y: gy + 4,
            fill: 'var(--text-muted)', 'font-size': '10', 'text-anchor': 'end'
        });
        yLabel.textContent = formatNumber(gridVal);
        svg.appendChild(yLabel);
    }

    /* Points calculation */
    var points = [];
    var stepX = labels.length > 1 ? plotW / (labels.length - 1) : plotW / 2;

    for (var i = 0; i < labels.length; i++) {
        var x = padding.left + stepX * i;
        var y = padding.top + plotH - ((values[i] / maxVal) * plotH);
        points.push({ x: x, y: y, val: values[i], label: labels[i] });
    }

    /* Area fill */
    var areaPath = 'M' + points[0].x + ',' + points[0].y;
    for (var i = 1; i < points.length; i++) {
        areaPath += smoothCurve(points[i - 1], points[i]);
    }
    areaPath += ' L' + points[points.length - 1].x + ',' + (padding.top + plotH);
    areaPath += ' L' + points[0].x + ',' + (padding.top + plotH) + ' Z';

    var gradId = 'grad_' + el.id;
    var defs = svgEl('defs');
    var grad = svgEl('linearGradient', { id: gradId, x1: '0', y1: '0', x2: '0', y2: '1' });
    var stop1 = svgEl('stop', { offset: '0%', 'stop-color': color, 'stop-opacity': '0.3' });
    var stop2 = svgEl('stop', { offset: '100%', 'stop-color': color, 'stop-opacity': '0.02' });
    grad.appendChild(stop1);
    grad.appendChild(stop2);
    defs.appendChild(grad);
    svg.appendChild(defs);

    var area = svgEl('path', {
        d: areaPath, fill: 'url(#' + gradId + ')', opacity: '0'
    });
    svg.appendChild(area);

    /* Line */
    var linePath = 'M' + points[0].x + ',' + points[0].y;
    for (var i = 1; i < points.length; i++) {
        linePath += smoothCurve(points[i - 1], points[i]);
    }
    var line = svgEl('path', {
        d: linePath, fill: 'none', stroke: color, 'stroke-width': 2.5,
        'stroke-linecap': 'round', 'stroke-linejoin': 'round',
        'stroke-dasharray': '2000', 'stroke-dashoffset': '2000'
    });
    svg.appendChild(line);

    /* X-axis labels - show limited for readability */
    var labelStep = labels.length <= 7 ? 1 : Math.ceil(labels.length / 7);
    for (var i = 0; i < labels.length; i++) {
        if (i % labelStep !== 0 && i !== labels.length - 1) continue;
        var xLabel = svgEl('text', {
            x: points[i].x, y: height - 8,
            fill: 'var(--text-muted)', 'font-size': '10', 'text-anchor': 'middle'
        });
        xLabel.textContent = labels[i];
        svg.appendChild(xLabel);
    }

    /* Dots + Tooltips */
    var tooltipGroup = svgEl('g');
    points.forEach(function (pt) {
        var dot = svgEl('circle', {
            cx: pt.x, cy: pt.y, r: 0, fill: color, stroke: 'var(--bg-card)', 'stroke-width': 2
        });
        tooltipGroup.appendChild(dot);

        /* Hover area */
        var hoverRect = svgEl('rect', {
            x: pt.x - stepX / 2, y: padding.top, width: stepX, height: plotH,
            fill: 'transparent', cursor: 'pointer'
        });

        var tooltipBg = svgEl('rect', {
            x: pt.x - 40, y: pt.y - 36, width: 80, height: 24,
            rx: 4, fill: 'var(--bg-card)', stroke: 'var(--border-color)', 'stroke-width': 1,
            opacity: 0, 'pointer-events': 'none'
        });
        var tooltipText = svgEl('text', {
            x: pt.x, y: pt.y - 20,
            fill: 'var(--text-primary)', 'font-size': '11', 'font-weight': '600', 'text-anchor': 'middle',
            opacity: 0, 'pointer-events': 'none'
        });
        tooltipText.textContent = pt.val.toLocaleString('tr-TR');

        tooltipGroup.appendChild(tooltipBg);
        tooltipGroup.appendChild(tooltipText);

        hoverRect.addEventListener('mouseenter', function () {
            dot.setAttribute('r', '5');
            tooltipBg.setAttribute('opacity', '1');
            tooltipText.setAttribute('opacity', '1');
        });
        hoverRect.addEventListener('mouseleave', function () {
            dot.setAttribute('r', '3');
            tooltipBg.setAttribute('opacity', '0');
            tooltipText.setAttribute('opacity', '0');
        });
        tooltipGroup.appendChild(hoverRect);
    });
    svg.appendChild(tooltipGroup);

    el.innerHTML = '';
    el.appendChild(svg);

    /* Animate */
    requestAnimationFrame(function () {
        line.style.transition = 'stroke-dashoffset 1s ease';
        line.style.strokeDashoffset = '0';
        area.style.transition = 'opacity 0.8s ease 0.3s';
        area.style.opacity = '1';
        tooltipGroup.querySelectorAll('circle').forEach(function (d, idx) {
            setTimeout(function () {
                d.setAttribute('r', '3');
            }, 300 + idx * 30);
        });
    });
}

/* ════════════════════════════════════════════
   Vertical Bar Chart (Haftalık / Aylık Trend)
   ════════════════════════════════════════════ */
function initVerticalBarChart(el) {
    var labels = JSON.parse(el.dataset.labels || '[]');
    var values = JSON.parse(el.dataset.values || '[]');
    var color = el.dataset.color || '#14b8a6';
    var chartHeight = parseInt(el.dataset.height) || 220;

    if (!labels.length) {
        el.innerHTML = '<p class="text-center text-clr-muted py-5">Henüz veri yok.</p>';
        return;
    }

    var maxVal = Math.max.apply(null, values) || 1;
    var padding = { top: 20, right: 16, bottom: 50, left: 50 };
    var width = el.offsetWidth || 600;
    var height = chartHeight;
    var plotW = width - padding.left - padding.right;
    var plotH = height - padding.top - padding.bottom;

    var svg = createSVG(width, height);

    /* Y-axis grid */
    var gridCount = 4;
    for (var g = 0; g <= gridCount; g++) {
        var gy = padding.top + (plotH / gridCount) * g;
        var gridVal = Math.round(maxVal - (maxVal / gridCount) * g);

        svg.appendChild(svgEl('line', {
            x1: padding.left, y1: gy, x2: width - padding.right, y2: gy,
            stroke: 'rgba(255,255,255,0.06)', 'stroke-width': 1
        }));

        var yLabel = svgEl('text', {
            x: padding.left - 8, y: gy + 4,
            fill: 'var(--text-muted)', 'font-size': '10', 'text-anchor': 'end'
        });
        yLabel.textContent = formatNumber(gridVal);
        svg.appendChild(yLabel);
    }

    /* Bars */
    var barCount = labels.length;
    var groupWidth = plotW / barCount;
    var barWidth = Math.min(groupWidth * 0.6, 60);

    labels.forEach(function (label, i) {
        var barH = maxVal > 0 ? (values[i] / maxVal) * plotH : 0;
        var x = padding.left + groupWidth * i + (groupWidth - barWidth) / 2;
        var y = padding.top + plotH - barH;

        var gradId = el.id + '_bg_' + i;
        var defs = svg.querySelector('defs') || (function () { var d = svgEl('defs'); svg.appendChild(d); return d; })();
        var grad = svgEl('linearGradient', { id: gradId, x1: '0', y1: '0', x2: '0', y2: '1' });
        grad.appendChild(svgEl('stop', { offset: '0%', 'stop-color': color, 'stop-opacity': '0.9' }));
        grad.appendChild(svgEl('stop', { offset: '100%', 'stop-color': color, 'stop-opacity': '0.4' }));
        defs.appendChild(grad);

        var bar = svgEl('rect', {
            x: x, y: padding.top + plotH, width: barWidth, height: 0,
            rx: 4, fill: 'url(#' + gradId + ')', cursor: 'pointer'
        });

        /* Tooltip */
        var tooltipBg = svgEl('rect', {
            x: x + barWidth / 2 - 35, y: y - 30, width: 70, height: 22,
            rx: 4, fill: 'var(--bg-card)', stroke: 'var(--border-color)', 'stroke-width': 1,
            opacity: 0, 'pointer-events': 'none'
        });
        var tooltipText = svgEl('text', {
            x: x + barWidth / 2, y: y - 14,
            fill: 'var(--text-primary)', 'font-size': '11', 'font-weight': '600', 'text-anchor': 'middle',
            opacity: 0, 'pointer-events': 'none'
        });
        tooltipText.textContent = values[i].toLocaleString('tr-TR');

        bar.addEventListener('mouseenter', function () {
            bar.style.opacity = '1';
            tooltipBg.setAttribute('opacity', '1');
            tooltipText.setAttribute('opacity', '1');
        });
        bar.addEventListener('mouseleave', function () {
            bar.style.opacity = '0.85';
            tooltipBg.setAttribute('opacity', '0');
            tooltipText.setAttribute('opacity', '0');
        });

        svg.appendChild(bar);
        svg.appendChild(tooltipBg);
        svg.appendChild(tooltipText);

        /* X label */
        var xLabel = svgEl('text', {
            x: x + barWidth / 2, y: height - 8,
            fill: 'var(--text-muted)', 'font-size': '9', 'text-anchor': 'middle'
        });
        xLabel.textContent = label.length > 14 ? label.substring(0, 14) : label;
        svg.appendChild(xLabel);

        /* Animate */
        requestAnimationFrame(function () {
            setTimeout(function () {
                bar.style.transition = 'y 0.5s ease, height 0.5s ease';
                bar.setAttribute('y', y);
                bar.setAttribute('height', barH);
            }, i * 100);
        });
    });

    el.innerHTML = '';
    el.appendChild(svg);
}

/* ════════════════════════════════════════════
   Category Donut Chart
   ════════════════════════════════════════════ */
function initCategoryDonutChart() {
    var el = document.getElementById('categoryDonutChart');
    if (!el) return;

    var labels = JSON.parse(el.dataset.labels || '[]');
    var values = JSON.parse(el.dataset.values || '[]');
    var colors = JSON.parse(el.dataset.colors || '[]');

    if (!labels.length) return;

    var total = 0;
    for (var i = 0; i < values.length; i++) {
        total += Number(values[i]) || 0;
    }
    if (total === 0) return;

    var size = 180;
    var center = size / 2;
    var radius = 72;
    var innerRadius = 48;
    var startAngle = -Math.PI / 2;

    var svg = createSVG(size, size);
    svg.style.display = 'block';
    svg.style.margin = '0 auto';

    values.forEach(function (rawVal, i) {
        var val = Number(rawVal) || 0;
        var sliceAngle = (val / total) * Math.PI * 2;
        var endAngle = startAngle + sliceAngle;

        if (sliceAngle < 0.01) { startAngle = endAngle; return; }

        var x1 = center + radius * Math.cos(startAngle);
        var y1 = center + radius * Math.sin(startAngle);
        var x2 = center + radius * Math.cos(endAngle);
        var y2 = center + radius * Math.sin(endAngle);
        var ix1 = center + innerRadius * Math.cos(startAngle);
        var iy1 = center + innerRadius * Math.sin(startAngle);
        var ix2 = center + innerRadius * Math.cos(endAngle);
        var iy2 = center + innerRadius * Math.sin(endAngle);
        var largeArc = sliceAngle > Math.PI ? 1 : 0;

        var d = 'M' + x1 + ' ' + y1 +
            ' A' + radius + ' ' + radius + ' 0 ' + largeArc + ' 1 ' + x2 + ' ' + y2 +
            ' L' + ix2 + ' ' + iy2 +
            ' A' + innerRadius + ' ' + innerRadius + ' 0 ' + largeArc + ' 0 ' + ix1 + ' ' + iy1 + ' Z';

        var path = svgEl('path', {
            d: d, fill: colors[i % colors.length], opacity: '0.85', cursor: 'pointer'
        });
        path.style.transition = 'opacity 0.2s, transform 0.2s';
        path.style.transformOrigin = center + 'px ' + center + 'px';

        var titleEl = svgEl('title');
        titleEl.textContent = labels[i] + ': ' + val + ' eser (%' + Math.round((val / total) * 100) + ')';
        path.appendChild(titleEl);

        path.addEventListener('mouseenter', function () { path.style.opacity = '1'; path.style.transform = 'scale(1.04)'; });
        path.addEventListener('mouseleave', function () { path.style.opacity = '0.85'; path.style.transform = 'scale(1)'; });

        svg.appendChild(path);
        startAngle = endAngle;
    });

    /* Center text */
    var centerText = svgEl('text', {
        x: center, y: center - 6,
        fill: 'var(--text-primary)', 'font-size': '22', 'font-weight': '700', 'text-anchor': 'middle'
    });
    centerText.textContent = String(total);
    svg.appendChild(centerText);

    var subText = svgEl('text', {
        x: center, y: center + 14,
        fill: 'var(--text-muted)', 'font-size': '11', 'text-anchor': 'middle'
    });
    subText.textContent = 'eser';
    svg.appendChild(subText);

    el.innerHTML = '';
    el.appendChild(svg);
}

/* ════════════════════════════════════════════
   Work Comparison Horizontal Bar Chart
   ════════════════════════════════════════════ */
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

/* ════════════════════════════════════════════
   SVG Helpers
   ════════════════════════════════════════════ */
function createSVG(w, h) {
    var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('width', '100%');
    svg.setAttribute('height', h);
    svg.setAttribute('viewBox', '0 0 ' + w + ' ' + h);
    svg.setAttribute('preserveAspectRatio', 'xMidYMid meet');
    return svg;
}

function svgEl(tag, attrs) {
    var el = document.createElementNS('http://www.w3.org/2000/svg', tag);
    if (attrs) {
        for (var k in attrs) {
            if (attrs.hasOwnProperty(k)) el.setAttribute(k, attrs[k]);
        }
    }
    return el;
}

function smoothCurve(p1, p2) {
    var cx = (p1.x + p2.x) / 2;
    return ' C' + cx + ',' + p1.y + ' ' + cx + ',' + p2.y + ' ' + p2.x + ',' + p2.y;
}

function formatNumber(n) {
    if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M';
    if (n >= 1000) return (n / 1000).toFixed(1) + 'K';
    return String(n);
}
