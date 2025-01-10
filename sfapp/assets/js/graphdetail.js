document.addEventListener('DOMContentLoaded', () => {

    /**
     * Creates a chart using Chart.js
     * @param {CanvasRenderingContext2D} context - The canvas context for the chart.
     * @param {string} label - The dataset label to display.
     * @param {array} data - The data points for the chart.
     * @param {string} color - The line color for the chart.
     * @param {number} yMin - Minimum value for the Y-axis.
     * @param {number} yMax - Maximum value for the Y-axis.
     */
    const createChart = (context, label, data, color, yMin, yMax) => {
        if (context) {
            new Chart(context, {
                type: 'line',
                data: {
                    labels: window.chartLabels,
                    datasets: [{
                        label, // Chart title ("Temperature")
                        data, // Data points for the line chart
                        borderColor: color,
                        tension: 0.1,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: yMin,
                            max: yMax,
                        },
                    },
                },
            });
        }
    };

    // Create the Temperature chart with range [0, 50°C]
    createChart(
        document.getElementById('tempChart')?.getContext('2d'),
        'Temperature (°C)',
        window.tempData,
        'rgb(255, 99, 132)',
        0,
        50
    );

    // Create the Humidity chart with range [0, 100%]
    createChart(
        document.getElementById('humChart')?.getContext('2d'),
        'Humidity (%)',
        window.humData,
        'rgb(54, 162, 235)',
        0,
        100
    );

    // Create the CO2 chart with range [300, 1000 ppm]
    createChart(
        document.getElementById('co2Chart')?.getContext('2d'),
        'CO2 (ppm)',
        window.co2Data,
        'rgb(75, 192, 192)',
        300,
        1800
    );
});