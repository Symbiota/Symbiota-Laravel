import Alpine from "alpinejs";
import focus from "@alpinejs/focus";
import "./components/autocomplete-input";
import "./components/taxonomy-create";
import "./components/toaster";
import "./helpers";

window.Alpine = Alpine;

Alpine.plugin(focus);

queueMicrotask(() => {
    Alpine.start();
});
