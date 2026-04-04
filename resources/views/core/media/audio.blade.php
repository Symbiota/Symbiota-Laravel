@props(['item'])
<audio controls>
	<source src="{{ $item->originalUrl }}" type="{{ $item->format }}"/>
	Your browser does not support the audio element.
</audio>
