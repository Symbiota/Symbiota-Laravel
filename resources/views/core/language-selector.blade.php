@php
$LANG_TAG = App::currentLocale();
@endphp

@push('js-scripts')
<script>
function setLanguage(selObj){
    var langVal = selObj.value;
    var d = new Date();
    d.setMonth( d.getMonth() + 1 );
    document.cookie = "lang="+langVal+"; path=/ ; expires="+ d.toUTCString();
    location.reload(true);
}
</script>
@endpush

<select class="py-1 px-2 text-xs text-black rounded-md" name='language' onchange="setLanguage(this)">
    <option value="en">
        English
    </option>
    <option value="es" @selected($LANG_TAG == 'es')>
        Espa&ntilde;ol
    </option>
    <option value="fr" @selected($LANG_TAG == 'fr')>
        Français
    </option>
</select>
