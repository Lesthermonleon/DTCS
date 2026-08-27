/**
 * DITC HIMS — Centralized Chart.js Styling Engine
 * Standardized medical visual design, theme awareness & responsive helpers.
 */
window.HIMSChart = (function () {
    const COLOR_PRIMARY = '#15803d';
    const COLOR_PRIMARY_FILL = 'rgba(21, 128, 61, 0.08)';
    const COLOR_CRITICAL = '#dc2626';
    const GREEN_PALETTE = ['#15803d', '#166534', '#22c55e', '#86efac', '#648071', '#173b2a', '#dcfce7', '#f0fdf4'];

    function isDarkMode() {
        return document.documentElement.getAttribute('data-theme') === 'dark' ||
               document.documentElement.getAttribute('data-bs-theme') === 'dark';
    }

    function getGridColor() {
        return isDarkMode() ? '#1E3630' : '#eef1f5';
    }

    function getTickColor() {
        return isDarkMode() ? '#94A3B8' : '#929aaa';
    }

    function getTooltipBg() {
        return '#172033';
    }

    function getBaseOptions(unitLabel) {
        const gridColor = getGridColor();
        const tickColor = getTickColor();
        const tooltipBg = getTooltipBg();

        const options = {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1200,
                easing: 'easeOutQuart'
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: tooltipBg,
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: { size: 12, weight: '600' },
                    bodyFont: { size: 13 },
                    displayColors: false
                }
            },
            scales: {
                x: {
                    ticks: { color: tickColor, font: { size: 12 } },
                    grid: { display: false },
                    border: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: tickColor, precision: 0, font: { size: 12 } },
                    grid: { color: gridColor },
                    border: { display: false }
                }
            }
        };

        if (unitLabel) {
            options.plugins.tooltip.callbacks = {
                label: function (context) {
                    const value = context.parsed.y !== undefined ? context.parsed.y : context.parsed;
                    return ' ' + value.toLocaleString() + ' ' + unitLabel;
                }
            };
        }

        return options;
    }

    function getLineDataset(label, data, customOptions) {
        return Object.assign({
            label: label || 'Activity',
            data: data || [],
            borderColor: COLOR_PRIMARY,
            backgroundColor: COLOR_PRIMARY_FILL,
            borderWidth: 3,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: COLOR_PRIMARY,
            pointBorderWidth: 3,
            pointRadius: 4,
            pointHoverRadius: 7,
            fill: true,
            tension: 0.4
        }, customOptions || {});
    }

    return {
        PRIMARY: COLOR_PRIMARY,
        PRIMARY_FILL: COLOR_PRIMARY_FILL,
        CRITICAL: COLOR_CRITICAL,
        PALETTE: GREEN_PALETTE,
        isDarkMode: isDarkMode,
        getBaseOptions: getBaseOptions,
        getLineDataset: getLineDataset,

        createLineChart: function (ctx, label, labels, data, unitLabel) {
            if (typeof Chart === 'undefined' || !ctx) return null;

            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [getLineDataset(label, data)]
                },
                options: getBaseOptions(unitLabel)
            });
        },

        createDoughnutChart: function (ctx, labels, data, customColors) {
            if (typeof Chart === 'undefined' || !ctx) return null;
            const dark = isDarkMode();
            const colors = customColors || GREEN_PALETTE;

            const options = {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1200, easing: 'easeOutQuart' },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: getTickColor(),
                            font: { size: 11 },
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: getTooltipBg(),
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 12 },
                        bodyFont: { size: 13 },
                        displayColors: true
                    }
                },
                cutout: '70%'
            };

            return new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors.slice(0, data.length),
                        borderWidth: 2,
                        borderColor: dark ? '#172B26' : '#FFFFFF'
                    }]
                },
                options: options
            });
        }
    };
})();
