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
// See Github https://github.com/Leaflet/Leaflet.draw/issues/1013 for context
window.radius = undefined;

window.Alpine = Alpine;
Alpine.plugin(focus);

queueMicrotask(() => {
    Alpine.start()
});
