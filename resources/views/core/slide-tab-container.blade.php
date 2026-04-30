@props(['id' => uniqid(),'tabs' => [] ])
<div
    x-data="{
        tabSelected: 1,
        tabId: $id('{{ $id }}'),
        tabButtonClicked(tabButton){
            this.tabSelected = tabButton.id.replace(this.tabId + '-', '');
            this.tabRepositionMarker(tabButton);
        },
        tabRepositionMarker(tabButton){
            this.$refs.tabMarker.style.width=tabButton.offsetWidth + 'px';
            this.$refs.tabMarker.style.height=tabButton.offsetHeight + 'px';
            this.$refs.tabMarker.style.left=tabButton.offsetLeft + 'px';
        },
        tabContentActive(tabContent){
            // return this.tabSelected == tabContent.id.replace(this.tabId + '-content-', '');
            const tabId = tabContent.id.split('-').slice(-1);
            return this.tabSelected == tabId;
        }
    }"
    x-init="tabRepositionMarker($refs.tabButtons.firstElementChild)"
    class="relative w-full"
>
    <div
        x-ref="tabButtons"
        class="text-base-content bg-base-200 relative flex h-10 w-full items-center rounded-lg p-1 select-none"
    >
        @foreach($tabs as $tab)
            <button
                :id="$id(tabId)"
                @click="tabButtonClicked($el)"
                type="button"
                class="relative z-20 inline-flex h-8 w-full cursor-pointer items-center justify-center rounded-md px-3 text-sm font-medium whitespace-nowrap transition-all"
            >
                {{ $tab }}
            </button>

        @endforeach
        <div x-ref="tabMarker" class="absolute left-0 z-10 h-full w-1/2 duration-300 ease-out" x-cloak>
            <div class="bg-base-100 h-full w-full rounded-md shadow-sm"></div>
        </div>
    </div>
    <div class="content relative mt-2 w-full">{{ $slot }}</div>
</div>
