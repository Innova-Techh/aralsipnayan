@extends('admin.admin.layouts.app')




@section('content')
    @include('admin.admin.admin-dashboard')
@endsection

@push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Note: this page now uses ApexCharts in the included dashboard partial.
        // Keep Chart.js code guarded to avoid runtime errors when #growthChart isn't a <canvas>.
        (function() {
            const growthEl = document.getElementById('growthChart');
            if (!growthEl || growthEl.tagName !== 'CANVAS' || typeof Chart === 'undefined') {
                return;
            }

            const ctx = growthEl.getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        {
                            label: 'Students',
                            data: [1200, 1350, 1500, 1650, 1800, 1950],
                            backgroundColor: 'rgb(59, 130, 246)',
                            borderRadius: 4,
                            barThickness: 30
                        },
                        {
                            label: 'Assessments',
                            data: [120, 135, 140, 145, 155, 160],
                            backgroundColor: 'rgb(251, 146, 60)',
                            borderRadius: 4,
                            barThickness: 30
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 12 } }
                        },
                        y: {
                            beginAtZero: true,
                            max: 2000,
                            ticks: { stepSize: 500, font: { size: 12 } },
                            grid: { borderDash: [2, 2] }
                        }
                    }
                }
            });
        })();

        console.log('Dashboard loaded successfully');
    </script>
@endpush
