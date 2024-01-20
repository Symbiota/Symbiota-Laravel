@props(['id', 'label', 'name', 'checked' => false])
<section>
	<!-- Accordion selector -->
    <input id="{{ $id }}" type="checkbox" name="{{ $name?? $label }}" class="accordion-selector" @checked($checked) />
    <!-- Accordion header -->
    <label for="{{ $id }}" class="accordion-header">{{ $label }}</label>

    <div class="content">
       {{ $slot }}
    </div>
</section>
