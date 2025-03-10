import './bootstrap';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus'
import 'htmx.org';
import L from 'leaflet';
import 'leaflet-draw';
import 'leaflet.markercluster';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);
window.Chart = Chart;

window.L = L;
window.type = true;

window.Alpine = Alpine;
Alpine.plugin(focus);

queueMicrotask(() => {
    Alpine.start()
});
