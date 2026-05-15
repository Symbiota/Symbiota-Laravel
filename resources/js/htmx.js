import htmx from "htmx.org";
import "./htmx-extensions/html-streaming";

window.htmx = htmx;

/* This Script cleans up alpine dom manipulations before htmx snapshots the page.
 * Note Other solutions that were tried
 *
 * 1. Snapshotting before alpine does the dom manipulations.
 *  - Sort of worked, but htmx history failed
 */
document.addEventListener("htmx:beforeHistorySave", (evt) => {
    document.querySelectorAll("[x-for]").forEach((item) => {
        item._x_lookup &&
            Object.values(item._x_lookup).forEach((el) => el.remove());
    });
    document.querySelectorAll("[x-if]").forEach((item) => {
        item._x_currentIfEl && item._x_currentIfEl.remove();
    });

    document.querySelectorAll("[x-teleport]").forEach((item) => {
        item._x_teleport && item._x_teleport.remove();
    });

    if (window.tinymce_editor) {
        window.tinymce_editor.remove();
    }
});
