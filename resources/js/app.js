import './bootstrap';
import Alpine from 'alpinejs';
import 'htmx.org';

window.Alpine = Alpine;
queueMicrotask(() => {
    Alpine.start()
});
