import './bootstrap';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus'
import 'htmx.org';
import L from 'leaflet';
import 'leaflet-draw';
import 'leaflet.markercluster';
import './components/autocomplete-input';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);
window.Chart = Chart;

window.L = L;
window.type = true;
// See Github https://github.com/Leaflet/Leaflet.draw/issues/1013 for context
window.radius = undefined;

window.Alpine = Alpine;
Alpine.plugin(focus);

//Custom Functions
function openWindow(link = "", title = "", options = "resizable=0,width=900,height=630,left=20,top=20") {
    let mapWindow = open(link,
        title,
        options,
    );
    if (mapWindow.opener == null) mapWindow.opener = self;
    mapWindow.focus();
}

window.openWindow = openWindow;

function copyUrl(urlOverride) {
    const url = urlOverride ? urlOverride: window.location;
    const type = "text/plain";

    const clipboardItemData = {
        [type]: url,
    };

    const clipboardItem = new ClipboardItem(clipboardItemData);
    navigator.clipboard.write([clipboardItem]).then(res => console.log('success'), error => console.log(error));
}
window.copyUrl = copyUrl;

queueMicrotask(() => {
    Alpine.start()
});
