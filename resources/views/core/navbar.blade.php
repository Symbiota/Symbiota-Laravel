@php
$LANG_TAG = App::currentLocale();
@endphp

@props(['navigations'])
<script>
function setLanguage(selObj){
	var langVal = selObj.value;
	var d = new Date();
	d.setMonth( d.getMonth() + 1 );
	document.cookie = "lang="+langVal+"; path=/ ; expires="+ d.toUTCString();
	location.reload(true);
}
</script>
<!-- resources/views/tasks.blade.php -->
<div class="menu-wrapper">
    <nav class="top-menu">
        <ul class="menu">
            @foreach ($navigations as $nav)
                <li>
                    <a href="{{ $nav['link'] }}">
                        {!! html_entity_decode($nav['title']) !!}
                    </a>
                </li>
            @endforeach
            <li>
                <select name='language' onchange="setLanguage(this)">
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
            </li>
        </ul>
    </nav>
</div>
