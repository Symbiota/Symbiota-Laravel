@props(['id' => uniqid()])
@pushOnce('js-scripts')
<script type="text/javascript">
    window.document.addEventListener('DOMContentLoaded', function () {
        if(window.tinymce_editor) {
            tinymce_editor.render();
        } else {
            console.error('Tinymce failed to load')
        }
    });
</script>
@endPushOnce
<x-input {{ $attributes }} autocomplete="off" area data-mce-editor="true">
  {{ $slot }}
</x-input>
