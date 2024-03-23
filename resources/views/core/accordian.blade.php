@props(['id', 'label', 'name', 'open' => false])
<section>
	<!-- Accordion selector -->
    <input id="{{ $id }}" type="checkbox" name="{{ $name?? $label }}" class="accordion-selector" @checked(old($id, $open)) />
    <!-- Accordion header -->
    <label for="{{ $id }}" class="accordion-header">{{ $label }}</label>

    <div class="content">
       {{ $slot }}
    </div>
</section>
