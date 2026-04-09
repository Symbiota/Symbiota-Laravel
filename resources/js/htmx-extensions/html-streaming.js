import htmx from 'htmx.org';

/* Authors note:
 *
 * This extension was made for the sole purpose of getting around issues with
 * streaming html from the server within a blade template when doing long operations.
 *
 * It isn't the most flexible and will like need some reworks to get it functioning
 * better within the htmx ecosystem.
 *
 * I would only use this for simple use cases for now
 *
 * Extension adds a new swap attribute that will modify the dom when onprogress fires
 * from sent xhr handle.
 *
 * To use add the hx-ext="hx-stream" to element sending the request or a parent element
 * of it. Then change the hx-swap or hx-swap-oob value to use stream which will perform
 * an innerHTML swap whenever new streamed progess comes in.
 *
 */

function swap_on_progress(xhr, target) {
    xhr.onprogress = (e => {
        const el = htmx.find(target);
        el.innerHTML = e.originalTarget.response;
    })
}

function oob_swap_on_progess(xhr) {
    let seen = {};
    xhr.onprogress = (e => {
        const results = document.createElement('div');
        results.innerHTML = e.originalTarget.response

        for(let i = 0; i < results.children.length; i++) {
            const child = results.children[i];
            if(child && child.getAttribute('hx-swap-oob')) {
                if(seen[child.id]) continue;
                const el = document.getElementById(child.id);

                console.log(el)
                if(!el) continue;

                child.id
                el.innerHTML= child.innerHTML;
                seen[child.id] = child.id;
            }
        }
    })
}

window.swap_on_progress = swap_on_progress;

htmx.defineExtension('hx-stream', {
    onEvent: function(name, evt) {
        if(name === 'htmx:beforeRequest') {
            if(evt.target.getAttribute('hx-swap') === 'stream') {
                swap_on_progress(evt.detail.xhr, evt.target.getAttribute('hx-target'));
            } else if (evt.target.getAttribute('hx-swap-oob') === 'stream') {
                oob_swap_on_progess(evt.detail.xhr)
            }
        }
    }
});
