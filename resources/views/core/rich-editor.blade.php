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
<textarea id="editor" autocomplete="off">
  {{ $slot }}
</textarea>
