function symbChartSetup(id, name, type, responsive) {
    const ctx = document.getElementById(id);

    const json = ctx.getAttribute("data-chart");
    const label_value_map = JSON.parse(json);

    let count = label_value_map.length;

    const labels = [];
    const values = [];

    for (let item of label_value_map) {
        labels.push(`${item.label} (${item.value})`);
        values.push(item.value);
    }

    let base_colors = [
        "#ff595e",
        "#ff924c",
        "#ffca3a",
        "#c5ca30",
        "#8ac926",
        "#52a675",
        "#1982c4",
        "#4267ac",
        "#6a4c93",
        "#b5a6c9",
    ];

    const relative_max = values[0];

    let colors = [];

    // Color Range is determined by percentage group
    for (let value of values) {
        let group = Math.floor((value / relative_max) * 10);
        if (group === 0) group = 1;

        colors.push(base_colors[10 - group]);
    }

    let options = {
        elements: {
            arc: {
                borderWidth: 0,
            },
        },
        responsive: responsive,
        plugins: {
            legend: {
                responsive: true,
                display: true,
                position: "top",
            },
        },
    };

    if (type == "bar") {
        options.plugins.legend.display = false;
    }

    new Chart(ctx, {
        type,
        data: {
            labels: labels,
            datasets: [
                {
                    label: name,
                    data: values,
                    backgroundColor: colors,
                    hoverOffset: 4,
                },
            ],
        },
        options,
    });
}
window.symbChartSetup = symbChartSetup;
