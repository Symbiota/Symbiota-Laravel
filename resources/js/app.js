import './bootstrap';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus'
import 'htmx.org';

window.Alpine = Alpine;
Alpine.plugin(focus);
queueMicrotask(() => {
    Alpine.start()
});
