<style>
    .tabs {
        display: flex;
        flex-wrap: wrap;
        max-width: 100%;
        border: 1px solid #d3d3d3;
        border-radius: 4px;
    }
    .tabs__label {
        padding: 10px 16px;
        cursor: pointer;
        border-bottom: 3px solid #ddddd;
    }
    .tabs__radio {
        display: none;
    }
    .tabs__content {
        order: 1;
        width: 100%;
        display: none;
        border-bottom: 3px solid #ddddd;
    }

    .tabs__radio:checked+.tabs__label {
        color: #009578;
        border-bottom: 2px solid #009578;
    }

    .tabs__radio:checked+.tabs__label+.tabs__content {
        display: initial;
        border-bottom: 2px solid #009578;
    }
</style>
<div class="tabs" id="group1">
    {{ $slot }}
</div>
