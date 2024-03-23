@php $LANG_TAG = App::currentLocale(); @endphp
<x-layout>
	<div class="navpath"></div>
	<div id="innertext">
		@if ($LANG_TAG == 'es')
			<div>
				<h1 class="headline">Bienvenidos</h1>
				<p>Este portal de datos se ha establecido para promover la colaboración... Reemplazar con texto introductorio en inglés</p>
			</div>
		@elseif($LANG_TAG == 'fr')
			<div>
				<h1 class="headline">Bienvenue</h1>
				<p>Ce portail de données a été créé pour promouvoir la collaboration... Remplacer par le texte d'introduction en anglais</p>
			</div>
		@else
			<div>
				<h1 class="headline">Welcome</h1>
				<p>This data portal has been established to promote collaborative... Replace with introductory text in English.
				If the portal is not meant to be multilingual, remove the unneeded language sections</p>
			</div>
        @endif
	</div>
</x-layout>
