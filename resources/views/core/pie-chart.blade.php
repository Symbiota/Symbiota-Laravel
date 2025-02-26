{{-- Example Pi Chart For Now --}}
@pushOnce('js-scripts')
<script type="text/javascript" defer>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('myChart');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: [
                    'Red',
                    'Blue',
                    'Yellow',
                ],
                datasets: [{
                    label: 'My First Dataset',
                    data: [
                        300, 50, 100,
                    ],
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false // This hides all text in the legend and also the labels.
                    }
                }
            }
        });
    });
</script>
@endpushOnce

<div>
    <canvas id="myChart"></canvas>
</div>
