@props(['id' => uniqid(), 'values' => []])
{{-- Example Pi Chart For Now --}}
@if(!empty($values))
@push('js-scripts')
<script type="text/javascript" defer>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('{{ $id }}');

        const json = ctx.getAttribute('data-chart');
        const label_value_map = JSON.parse(json);

        //TODO (Logan) Allow for dyanimc settings for other (check chart js for this maybe)
        const labels = Object.keys(label_value_map).slice(0, 3);
        const values = Object.values(label_value_map).slice(0, 3);

        //TODO (Logan) random colors for each label and value
        const colors = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)',
            'rgb(50, 50, 50)',
        ];

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'My First Dataset',
                    data: values,
                    backgroundColor: colors,
                    hoverOffset: 4
                }]
            },
            options: {
                /*
                plugins: {
                    legend: {
                        display: false // This hides all text in the legend and also the labels.
                    }
                }
                */
            }
        });
    });
</script>
@endpush

<div {{ $attributes }}>
    <canvas data-chart="{{ json_encode($values) }}" id="{{ $id }}"></canvas>
</div>
@endif
