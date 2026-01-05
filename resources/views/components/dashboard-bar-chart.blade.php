@props([
    'chartId',
    'title' => '',
    'labels' => [],
    'data' => [],
])

<div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4 bg-white dark:bg-gray-950">
    <div class="font-bold mb-3">{{ $title }}</div>
    <canvas id="{{ $chartId }}" height="160"></canvas>

    <script>
        document.addEventListener("livewire:navigated", () => {
            const ctx = document.getElementById(@js($chartId));
            if (!ctx) return;

            if (ctx._chartInstance) {
                ctx._chartInstance.destroy();
            }

            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @js($labels),
                    datasets: [{
                        label: @js($title),
                        data: @js($data),
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            ctx._chartInstance = chart;
        });
    </script>
</div>
