@php
    function formatName ($collection) {
        $abr = $collection['instcode'] . (('-' . $collection['collcode']) ?? '');
        return $collection['collname'] . ' (' . $abr . ')';
    }
@endphp
<x-layout>

    <div id="innertext">
    <form name="collform1" action="" method="post" onsubmit="">
        <div style="margin:0px 0px 10px 5px;">
        <input
            id="dballcb"
            name="db[]"
            class="specobs"
            value='all'
            type="checkbox" checked />
        Select/Deselect <a href="misc/collprofiles.php">all Collections</a>
        </div>
        <div style="clear:both;">&nbsp;</div>
        </form>


    <x-tabs>
        <x-tab :id="'tab1'" :label="'Tab #1'">
    <table>
        <tbody>
            @foreach ($specArr as $spec)

            <tr>
            <td colspan="4"><div style="margin:10px;padding:10px 20px;border:inset"><table><tbody>
                @foreach ($spec as $cId => $collection)
                    @if(is_array($collection))
                        <x-record
                            :name="formatName($collection)"
                            :icon="$collection['icon']"
                            :link="'/collections/misc/collprofiles.php?collid='.$cId"
                        />
                    @endif
                @endforeach
            </tbody></table></div></td>
            </tr>
            @endforeach
        </tbody>
    </table>

        </x-tab>
        <x-tab :id="'tab2'" :label="'Tab #2'">
            <div>
                My Tab 2 content
            </div>
        </x-tab>
    </x-tabs>

    </div>

</x-layout>

