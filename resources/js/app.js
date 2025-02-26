import './bootstrap';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus'
import 'htmx.org';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);
window.Chart = Chart;

window.Alpine = Alpine;
Alpine.plugin(focus);

queueMicrotask(() => {
    Alpine.start()
});
