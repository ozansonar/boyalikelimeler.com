'use strict';

(function () {
    const data = window.DASHBOARD_DATA;
    if (!data) return;

    const tealRgb  = '20, 184, 166';
    const purpleRgb = '168, 85, 247';
    const blueRgb   = '59, 130, 246';
    const orangeRgb = '249, 115, 22';
    const greenRgb  = '34, 197, 94';

    const gridColor   = 'rgba(255,255,255,0.06)';
    const tickColor   = 'rgba(255,255,255,0.4)';
    const tooltipBg   = 'rgba(15, 23, 42, 0.95)';

    Chart.defaults.color = tickColor;
    Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";

    /* ─── Trend Chart (Line) ─── */
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: data.monthlyUsers.labels,
                datasets: [
                    {
                        label: 'Kullanicilar',
                        data: data.monthlyUsers.values,
                        borderColor: 'rgb(' + tealRgb + ')',
                        backgroundColor: 'rgba(' + tealRgb + ', 0.1)',
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: 'rgb(' + tealRgb + ')',
                    },
                    {
                        label: 'Eserler',
                        data: data.monthlyWorks.values,
                        borderColor: 'rgb(' + purpleRgb + ')',
                        backgroundColor: 'rgba(' + purpleRgb + ', 0.1)',
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: 'rgb(' + purpleRgb + ')',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, pointStyle: 'circle', padding: 20 }
                    },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        borderColor: 'rgba(' + tealRgb + ', 0.3)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { maxRotation: 45 } },
                    y: { grid: { color: gridColor }, beginAtZero: true }
                }
            }
        });
    }

    /* ─── Role Distribution Chart (Doughnut) ─── */
    const roleCtx = document.getElementById('roleChart');
    if (roleCtx) {
        const roleLabels = Object.keys(data.roleDistribution);
        const roleValues = Object.values(data.roleDistribution);
        const roleColors = [
            'rgb(' + tealRgb + ')',
            'rgb(' + purpleRgb + ')',
            'rgb(' + blueRgb + ')',
            'rgb(' + orangeRgb + ')',
            'rgb(' + greenRgb + ')',
        ];

        new Chart(roleCtx, {
            type: 'doughnut',
            data: {
                labels: roleLabels,
                datasets: [{
                    data: roleValues,
                    backgroundColor: roleColors.slice(0, roleLabels.length),
                    borderWidth: 0,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, pointStyle: 'circle', padding: 16 }
                    },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        borderColor: 'rgba(' + purpleRgb + ', 0.3)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                    }
                }
            }
        });
    }

    /* ─── Work Status Chart (Bar) ─── */
    const statusCtx = document.getElementById('workStatusChart');
    if (statusCtx) {
        const statusLabelMap = {
            'pending': 'Beklemede',
            'approved': 'Onaylandi',
            'rejected': 'Reddedildi',
            'revision_requested': 'Revizyon',
            'unpublished': 'Yayinda Degil',
        };
        const statusColorMap = {
            'pending': 'rgba(' + orangeRgb + ', 0.85)',
            'approved': 'rgba(' + greenRgb + ', 0.85)',
            'rejected': 'rgba(239, 68, 68, 0.85)',
            'revision_requested': 'rgba(' + blueRgb + ', 0.85)',
            'unpublished': 'rgba(148, 163, 184, 0.85)',
        };

        const statusLabels = Object.keys(data.workStatus).map(function (k) { return statusLabelMap[k] || k; });
        const statusValues = Object.values(data.workStatus);
        const statusColors = Object.keys(data.workStatus).map(function (k) { return statusColorMap[k] || 'rgba(148,163,184,0.85)'; });

        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: statusLabels,
                datasets: [{
                    label: 'Eser Sayisi',
                    data: statusValues,
                    backgroundColor: statusColors,
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 50,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        borderColor: 'rgba(' + blueRgb + ', 0.3)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: gridColor }, beginAtZero: true }
                }
            }
        });
    }
})();
