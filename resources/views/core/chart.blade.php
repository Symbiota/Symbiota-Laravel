@props([
    'id' => uniqid(),
    'values' => [],
    'name',
    'type',
])
{{-- Example Pi Chart For Now --}}
@if(!empty($values))
@push('js-scripts')
<script type="text/javascript" defer>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('{{ $id }}');

        const json = ctx.getAttribute('data-chart');
        const label_value_map = JSON.parse(json);
        const type = "{{ $type }}";

        let count = label_value_map.length;

        const labels = label_value_map.map(v => `${v.label} (${v.value})` );
        const values = label_value_map.map(v => v.value);

        let base_colors = [
            '#ff595e',
            '#ff924c',
            '#ffca3a',
            '#c5ca30',
            '#8ac926',
            '#52a675',
            '#1982c4',
            '#4267ac',
            '#6a4c93',
            '#b5a6c9',
        ];

        const relative_max = values[0];

        let colors = [];

        // Color Range is determined by percentage group
        for(let value of values) {
            let group = Math.floor((value / relative_max) * 10);
            if(group === 0) group = 1;

            colors.push(base_colors[10 - group]);
        }

        let options = {
            elements: {
                arc: {
                    borderWidth: 0
                }
            },
            plugins: {
                legend: {
                    responsive: true,
                    display: true,
                    position: 'top',
                }
            }
        }

        if(type == 'bar') {
            options.plugins.legend.display = false;
        }

        new Chart(ctx, {
            type,
            data: {
                labels: labels,
                datasets: [{
                    label: '{{ $name }}',
                    data: values,
                    backgroundColor: colors,
                    hoverOffset: 4
                }]
            },
            options,
        });
    });
</script>
@endpush

<div {{ $attributes }} class="relative">
    <canvas data-chart="{{ json_encode($values) }}" id="{{ $id }}"></canvas>
</div>
@endif
