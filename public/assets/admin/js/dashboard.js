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
                        label: 'Kullanıcılar',
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
            'approved': 'Onaylandı',
            'rejected': 'Reddedildi',
            'revision_requested': 'Revizyon',
            'unpublished': 'Yayında Değil',
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
                    label: 'Eser Sayısı',
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

    /* ─── PWA Monthly Trend (Line) ─── */
    const pwaTrendCtx = document.getElementById('pwaTrendChart');
    if (pwaTrendCtx && data.pwaMonthly) {
        new Chart(pwaTrendCtx, {
            type: 'line',
            data: {
                labels: data.pwaMonthly.labels,
                datasets: [{
                    label: 'PWA Yüklemeleri',
                    data: data.pwaMonthly.values,
                    borderColor: 'rgb(' + tealRgb + ')',
                    backgroundColor: 'rgba(' + tealRgb + ', 0.15)',
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(' + tealRgb + ')',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        borderColor: 'rgba(' + tealRgb + ', 0.3)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: gridColor }, beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    /* ─── PWA Platform Distribution (Doughnut) ─── */
    const pwaPlatformCtx = document.getElementById('pwaPlatformChart');
    if (pwaPlatformCtx && data.pwaPlatforms) {
        const platformLabelMap = {
            android: 'Android',
            ios: 'iOS',
            desktop: 'Masaüstü',
            unknown: 'Bilinmeyen'
        };
        const platformColorMap = {
            android: 'rgba(' + greenRgb + ', 0.85)',
            ios:     'rgba(' + blueRgb + ', 0.85)',
            desktop: 'rgba(' + purpleRgb + ', 0.85)',
            unknown: 'rgba(' + orangeRgb + ', 0.85)'
        };

        const pfLabels = Object.keys(data.pwaPlatforms).map(function (k) { return platformLabelMap[k] || k; });
        const pfValues = Object.values(data.pwaPlatforms);
        const pfColors = Object.keys(data.pwaPlatforms).map(function (k) { return platformColorMap[k] || 'rgba(148,163,184,0.85)'; });

        // Hide chart entirely if there is no data at all
        const hasData = pfValues.some(function (v) { return v > 0; });

        if (hasData) {
            new Chart(pwaPlatformCtx, {
                type: 'doughnut',
                data: {
                    labels: pfLabels,
                    datasets: [{
                        data: pfValues,
                        backgroundColor: pfColors,
                        borderColor: 'rgba(0,0,0,0.2)',
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: tickColor, padding: 12, font: { size: 11 } }
                        },
                        tooltip: {
                            backgroundColor: tooltipBg,
                            padding: 12,
                            cornerRadius: 8,
                        }
                    }
                }
            });
        } else {
            // No data placeholder
            const parent = pwaPlatformCtx.parentElement;
            if (parent) {
                parent.innerHTML = '<div class="text-sm-muted text-center py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Henüz veri yok</div>';
            }
        }
    }
})();
