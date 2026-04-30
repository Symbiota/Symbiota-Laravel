import Alpine from 'alpinejs';
import focus from '@alpinejs/focus'
import './components/autocomplete-input';
import './components/taxonomy-create';
import './components/toaster';
import './helpers';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);
window.Chart = Chart;

window.tinymce_editor = editor;

window.L = L;
window.type = true;
// See Github https://github.com/Leaflet/Leaflet.draw/issues/1013 for context
window.radius = undefined;

window.htmx = htmx;

window.Alpine = Alpine;

Alpine.plugin(focus);

queueMicrotask(() => {
    Alpine.start()
});
