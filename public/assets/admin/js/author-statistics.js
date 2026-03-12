'use strict';

(function () {
    var data = window.astChartData;
    if (!data) return;

    /* ── Dark theme defaults ── */
    var textMuted = 'rgba(255,255,255,0.45)';
    var gridColor = 'rgba(255,255,255,0.06)';
    var tooltipBg = '#1e293b';
    var tooltipBorder = 'rgba(255,255,255,0.1)';
    var tooltipTitle = '#e2e8f0';
    var tooltipBody = '#e2e8f0';

    Chart.defaults.color = textMuted;
    Chart.defaults.borderColor = gridColor;
    Chart.defaults.font.family = 'inherit';

    var defaultTooltip = {
        backgroundColor: tooltipBg,
        borderColor: tooltipBorder,
        borderWidth: 1,
        titleColor: tooltipTitle,
        bodyColor: tooltipBody,
        padding: 10,
        cornerRadius: 8,
        displayColors: true,
        titleFont: { weight: '600', size: 13 },
        bodyFont: { size: 12 }
    };

    var defaultScalesXY = function (beginAtZero) {
        return {
            x: {
                grid: { display: false },
                ticks: { color: textMuted, font: { size: 11 }, maxRotation: 45, autoSkipPadding: 8 }
            },
            y: {
                beginAtZero: beginAtZero !== false,
                grid: { color: gridColor },
                ticks: {
                    color: textMuted,
                    font: { size: 11 },
                    callback: function (val) {
                        if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                        if (val >= 1000) return (val / 1000).toFixed(1) + 'K';
                        return val;
                    }
                }
            }
        };
    };

    /* ═══════════════════════════════════════
       1) Son 7 Gün — Line (Area) Chart
       ═══════════════════════════════════════ */
    createLineChart('chartWeekly', data.weekly, '#14b8a6');

    /* ═══════════════════════════════════════
       2) Son 30 Gün — Line (Area) Chart
       ═══════════════════════════════════════ */
    createLineChart('chartMonthly', data.monthly, '#3b82f6');

    function createLineChart(canvasId, chartData, color) {
        var ctx = document.getElementById(canvasId);
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Okunma',
                    data: chartData.values,
                    borderColor: color,
                    backgroundColor: hexToRgba(color, 0.15),
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: color,
                    pointBorderColor: 'transparent',
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: color,
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: Object.assign({}, defaultTooltip, {
                        callbacks: {
                            label: function (ctx) {
                                return ctx.parsed.y.toLocaleString('tr-TR') + ' okunma';
                            }
                        }
                    })
                },
                scales: defaultScalesXY()
            }
        });
    }

    /* ═══════════════════════════════════════
       3) Son 4 Hafta — Bar Chart
       ═══════════════════════════════════════ */
    createBarChart('chartWeeklyTrend', data.weeklyTrend, '#14b8a6');

    /* ═══════════════════════════════════════
       4) Son 6 Ay — Bar Chart
       ═══════════════════════════════════════ */
    createBarChart('chartMonthlyTrend', data.monthlyTrend, '#a855f7');

    function createBarChart(canvasId, chartData, color) {
        var ctx = document.getElementById(canvasId);
        if (!ctx) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Okunma',
                    data: chartData.values,
                    backgroundColor: hexToRgba(color, 0.7),
                    hoverBackgroundColor: color,
                    borderColor: color,
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 48
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: Object.assign({}, defaultTooltip, {
                        callbacks: {
                            label: function (ctx) {
                                return ctx.parsed.y.toLocaleString('tr-TR') + ' okunma';
                            }
                        }
                    })
                },
                scales: defaultScalesXY()
            }
        });
    }

    /* ═══════════════════════════════════════
       5) Kategori Dağılımı — Doughnut
       ═══════════════════════════════════════ */
    (function () {
        var ctx = document.getElementById('chartCategoryDonut');
        if (!ctx || !data.category.labels.length) return;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.category.labels,
                datasets: [{
                    data: data.category.values,
                    backgroundColor: data.category.colors,
                    borderColor: 'transparent',
                    borderWidth: 2,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: false,
                cutout: '60%',
                plugins: {
                    legend: { display: false },
                    tooltip: Object.assign({}, defaultTooltip, {
                        callbacks: {
                            label: function (ctx) {
                                var total = ctx.dataset.data.reduce(function (a, b) { return a + b; }, 0);
                                var pct = total > 0 ? Math.round((ctx.parsed / total) * 100) : 0;
                                return ctx.label + ': ' + ctx.parsed + ' eser (%' + pct + ')';
                            }
                        }
                    })
                }
            }
        });
    })();

    /* ═══════════════════════════════════════
       6) Eser Karşılaştırma — Horizontal Bar
       ═══════════════════════════════════════ */
    (function () {
        var ctx = document.getElementById('chartWorkComparison');
        if (!ctx || !data.workComparison.labels.length) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.workComparison.labels,
                datasets: [
                    {
                        label: 'Okunma',
                        data: data.workComparison.views,
                        backgroundColor: hexToRgba('#14b8a6', 0.75),
                        hoverBackgroundColor: '#14b8a6',
                        borderColor: '#14b8a6',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false
                    },
                    {
                        label: 'Yorum',
                        data: data.workComparison.comments,
                        backgroundColor: hexToRgba('#3b82f6', 0.75),
                        hoverBackgroundColor: '#3b82f6',
                        borderColor: '#3b82f6',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false
                    },
                    {
                        label: 'Favori',
                        data: data.workComparison.favorites,
                        backgroundColor: hexToRgba('#a855f7', 0.75),
                        hoverBackgroundColor: '#a855f7',
                        borderColor: '#a855f7',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'start',
                        labels: {
                            color: textMuted,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 16,
                            font: { size: 12 }
                        }
                    },
                    tooltip: Object.assign({}, defaultTooltip, {
                        callbacks: {
                            label: function (ctx) {
                                return ctx.dataset.label + ': ' + ctx.parsed.x.toLocaleString('tr-TR');
                            }
                        }
                    })
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: {
                            color: textMuted,
                            font: { size: 11 },
                            callback: function (val) {
                                if (val >= 1000) return (val / 1000).toFixed(1) + 'K';
                                return val;
                            }
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { color: textMuted, font: { size: 11 } }
                    }
                }
            }
        });
    })();

    /* ── Helpers ── */
    function hexToRgba(hex, alpha) {
        var r = parseInt(hex.slice(1, 3), 16);
        var g = parseInt(hex.slice(3, 5), 16);
        var b = parseInt(hex.slice(5, 7), 16);
        return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
    }
})();
