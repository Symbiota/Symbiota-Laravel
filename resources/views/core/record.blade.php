@props(['name' => 'Name Here', 'icon'=>"", 'link'=>'#'])
<tr>
    <td style="width:40px;height:35px">
	    <img src="{{ $icon }}" alt="" style="border:0px;width:30px;height:30px;" />
    <td/>

	<td style="width:25px;padding-top:8px;">
		<div>
            <input data-role="none" id="cat-Input" tabindex="1" name="cat" checked value="" type="checkbox"/>
		</div>
    </td>
    <td>
        <div class="collectiontitle">
            {{ $name }} <a href="{{ $link }}"target="_blank"> more info...</a>
        </div>
    <td>
</tr>
