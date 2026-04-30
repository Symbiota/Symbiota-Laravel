<x-input {{ $attributes }} autocomplete="off" area data-mce-editor="true" x-init="tinymce_editor.render(`#${$el.id}`)">
    {{ $slot }}
</x-input>
