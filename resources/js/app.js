import './bootstrap';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus'
import 'htmx.org';
import L from 'leaflet';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);
window.Chart = Chart;

window.L = L;

window.Alpine = Alpine;
Alpine.plugin(focus);

queueMicrotask(() => {
    Alpine.start()
});
