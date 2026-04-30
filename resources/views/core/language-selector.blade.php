@php
$LANG_TAG = App::currentLocale();
@endphp

@push('js-scripts')
    <script type="text/javascript">
        function setLanguage(selObj) {
            var langVal = selObj.value;
            var d = new Date();
            d.setMonth(d.getMonth() + 1);
            document.cookie = "lang=" + langVal + "; path=/ ; expires=" + d.toUTCString();
            location.reload(true);
        }
    </script>
@endpush

<select class="text-base-content bg-base-300 rounded-md px-2 py-1 text-xs" name="language" onchange="setLanguage(this)">
    <option value="en">English</option>
    <option value="es" @selected($LANG_TAG == 'es')> Espa&ntilde;ol</option>
    <option value="fr" @selected($LANG_TAG == 'fr')> Français</option>
</select>
