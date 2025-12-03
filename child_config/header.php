<div>
<?= Blade::render('header', ['buttonVariant' => 'primary']) ?>
<?= Blade::render('navbar', ['navigations' => [
    ['title' => __('header.home'), 'link' => url('/'), 'htmx' => true],
    ['title' => __('header.collections'), 'link' => url('/collections/search'), 'htmx' => true],
    ['title' => __('header.map_search'), 'link' => legacy_url('/collections/map/index.php')],
    ['title' => __('header.species_checklists'), 'link' => url('/checklists'), 'htmx' => true],
    ['title' => __('header.media'), 'link' => url('/media/search'), 'htmx' => true],
    ['title' => __('header.data_use'), 'link' => url('/usagepolicy'), 'htmx' => true],
    ['title' => __('header.symbiota_help'), 'link' => docs_url()],
    ['title' => __('header.sitemap'), 'link' => url('/sitemap'), 'htmx' => true],
]]) ?>
</div>
