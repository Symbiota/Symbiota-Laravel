<x-layout class="p-10">
		<h1 class="text-5xl text-primary font-bold">Guidelines for Acceptable Use of Data</h1>
        <br/>
		<h2 class="text-2xl text-primary font-bold">Recommended Citation Formats</h2>
		<p>Use one of the following formats to cite data retrieved from the  network:</p>
        <br/>
		<h3 class="text-xl text-primary font-bold">General Citation</h3>
		<blockquote class="bg-base-300 rounded-md p-4">
			Biodiversity occurrence data published by: Name of people or institutional reponsible for maintaining the portal (accessed through the Name of people or institutional reponsible for maintaining the portal Portal, {{url('')}}, 2024-09-08).		</blockquote>
        <br/>
		<h3 class="text-xl text-primary font-bold">Usage of occurrence data from specific institutions</h3>
		<p>Access each collection profile page to find the available citation formats.</p>
        <br/>
		<h4 class="text-lg text-primary font-bold">Example</h4>
		<blockquote class="bg-base-300 rounded-md p-4">
			Name of Institution or Collection. Occurrence dataset http://gh.local/Symbiota/portal/content/dwca/accessed via theFresh Symbiota InstallPortal, http://gh.local/Symbiota, 2022-07-25.		</blockquote>
        <br/>

		<h2 class="text-2xl text-primary font-bold">Occurrence Record Use Policy</h2>
		<div class="ml-4">
				<li>
					While {{ config('app.name') }} will make every effort possible to control and document the quality
					of the data it publishes, the data are made available "as is". Any report of errors in the data should be
					directed to the appropriate curators and/or collections managers.
				</li>
				<li>
					{{ config('app.name') }} cannot assume responsibility for damages resulting from misuse or
					misinterpretation of datasets or from errors or omissions that may exist in the data.
				</li>
				<li>
					It is considered a matter of professional ethics to cite and acknowledge the work of other scientists that
					has resulted in data used in subsequent research. We encourages users to
					contact the original investigator responsible for the data that they are accessing.
				</li>
				<li>
					{{ config('app.name') }} asks that users not redistribute data obtained from this site without permission for data owners.
					However, links or references to this site may be freely posted.
				</li>
		</div>
        <br/>

		<h2 id="media" class="text-2xl text-primary font-bold">Media</h2>
		<p>Media within this website have been generously contributed by their owners to promote education and research. These contributors retain the full copyright for their media.
		Unless stated otherwise, media are made available under the Creative Commons Attribution-ShareAlike
		(<x-link href="https://creativecommons.org/licenses/by-sa/4.0/" target="_blank">CC BY-SA</x-link>).
		Users are allowed to copy, transmit, reuse, and/or adapt content, as long as attribution regarding the source of the content is made. If the content is altered, transformed,
		or enhanced, it may be re-distributed only under the same or similar license by which it was acquired.
		</p>
        <br/>

		<h2 class="text-2xl text-primary font-bold">Notes on Specimen Records and Images</h2>
		<p>Specimens are used for scientific research and because of skilled preparation and careful use they may last for hundreds of years. Some collections have specimens that were
		collected over 100 years ago that are no longer occur within the area. By making these specimens available on the web as images, their availability and value improves without
		an increase in inadvertent damage caused by use. Note that if you are considering making specimens, remember collecting normally requires permission of the landowner and,
		in the case of rare and endangered plants, additional permits may be required. It is best to coordinate such efforts with a regional institution that manages a publicly
		accessible collection.
		</p>
        <br/>

		<p><span class="font-bold">Disclaimer:</span> This data portal may contain specimens and historical records that are culturally sensitive. The collections include specimens dating back over 200 years
		collected from all around the world. Some records may also include offensive language. These records do not reflect the portal community's current viewpoint but rather the
		social attitudes and circumstances of the time period when specimens were collected or cataloged.
		</p>

</x-layout>
