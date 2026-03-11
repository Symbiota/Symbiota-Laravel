@php global $SERVER_ROOT, $LANG;

include_once(legacy_path('/classes/ChecklistAdmin.php'));
include_once(legacy_path('/classes/ChecklistVoucherAdmin.php'));
include_once(legacy_path('/classes/ChecklistVoucherReport.php'));
include_once(legacy_path('/classes/utilities/Language.php'));
Language::load('checklists/checklistadmin');

# header('Content-Type: text/html; charset='.$CHARSET);
# if(!$SYMB_UID) header('Location: ../profile/index.php?refurl=../checklists/checklistadmin.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$_POST = request()->all();

$clid = request('clid') ? filter_var(request('clid'), FILTER_SANITIZE_NUMBER_INT) : 0;
$pid = array_key_exists('pid', $_REQUEST) ? filter_var($_REQUEST['pid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$targetClid = array_key_exists('targetclid', $_REQUEST) ? filter_var($_REQUEST['targetclid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$transferMethod = array_key_exists('transmethod', $_POST) ? filter_var($_POST['transmethod'], FILTER_SANITIZE_NUMBER_INT) : 0;
$parentClid = array_key_exists('parentclid', $_REQUEST) ? filter_var($_REQUEST['parentclid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$targetPid = array_key_exists('targetpid', $_REQUEST) ? filter_var($_REQUEST['targetpid'], FILTER_SANITIZE_NUMBER_INT) : '';
$copyAttributes = array_key_exists('copyattributes', $_REQUEST) ? filter_var($_REQUEST['copyattributes'], FILTER_SANITIZE_NUMBER_INT) : 0;
$tabIndex = array_key_exists('tabindex', $_REQUEST) ? filter_var($_REQUEST['tabindex'], FILTER_SANITIZE_NUMBER_INT) : 0;
$action = array_key_exists('submitaction', $_REQUEST) ? htmlspecialchars($_REQUEST['submitaction'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : '';
$delclid = array_key_exists('delclid', $_POST) ? htmlspecialchars($_POST['delclid'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : '';
$editoruid = array_key_exists('editoruid', $_POST) ? htmlspecialchars($_POST['editoruid'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : '';
$pointtid = array_key_exists('pointtid', $_POST) ? htmlspecialchars($_POST['pointtid'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : '';
$pointlat = array_key_exists('pointlat', $_POST) ? htmlspecialchars($_POST['pointlat'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : '';
$pointlng = array_key_exists('pointlng', $_POST) ? htmlspecialchars($_POST['pointlng'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : '';
$notes = array_key_exists('notes', $_POST) ? htmlspecialchars($_POST['notes'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : '';
$clidadd = array_key_exists('clidadd', $_POST) ? htmlspecialchars($_POST['clidadd'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : '';
$parsetid = array_key_exists('parsetid', $_POST) ? filter_var($_POST['parsetid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$taxon = array_key_exists('taxon', $_POST) ? htmlspecialchars($_POST['taxon'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : '';

// Added in non voucher taxa
$startPos = (array_key_exists('start', $_REQUEST) ? filter_var($_REQUEST['start'], FILTER_SANITIZE_NUMBER_INT) : 0);
$displayMode = (array_key_exists('displaymode', $_REQUEST) ? filter_var($_REQUEST['displaymode'], FILTER_SANITIZE_NUMBER_INT) : 0);


$validated = request()->validate([
    'pid' => 'integer:strict|numeric',
    'targetclid' => 'integer',
    'parentclid' => 'integer',
    'targetpid' => 'integer',
    'tabindex' => 'integer',
    'delclid' => 'integer',
    'editoruid' => 'integer',
    'pointtid' => 'integer',
    'pointlat' => 'float',
    'pointlng' => 'float',
    'clidAdd' => 'integer',
    'parsetid' => 'integer',
]);

$clManager = new ChecklistAdmin();
if(!$clid && $delclid) $clid = $delclid;
$clManager->setClid($clid);

$clVoucherManager = new ChecklistVoucherAdmin();
$clVoucherManager->setClid($clid);

$clVoucherReport = new ChecklistVoucherReport();
$clVoucherReport->setClid($clid);

$statusStr = '';

$clAdmin = Gate::check('CL_ADMIN', $clid);
$settings = $checklist->defaultSettings? json_decode($checklist->defaultSettings): [];

// TODO (Logan) move this?
if($action == 'submitAdd'){
	if(Gate::check('CL_CREATE')){
		$newClid = $clManager->createChecklist($_POST);
		if($newClid) header('Location: checklist.php?clid='.$newClid);
	}
	//If we made it here the user does not have any checklist roles. cancel further execution.
	$statusStr = $LANG['NO_PERMISSIONS'];
}

if($clAdmin){
	// Submit checklist MetaData edits
	if($action == 'submitEdit'){
		if($clManager->editChecklist($_POST)){
			header('Location: checklist.php?clid=' . $clid . '&pid=' . $pid);
		}
		else{
			$statusStr = $clManager->getErrorMessage();
		}
	}
	elseif($action == 'deleteChecklist'){
		if($clManager->deleteChecklist($delclid)){
			header('Location: ../index.php');
		}
		else $statusStr = $LANG['ERR_DELETING_CHECKLIST'] . ': ' . $clManager->getErrorMessage();
	}
	elseif($action == 'addEditor'){
		$statusStr = $clManager->addEditor($editoruid);
	}
	elseif(array_key_exists('deleteuid',$_REQUEST)){
		$statusStr = $clManager->deleteEditor($_REQUEST['deleteuid']);
	}
	elseif($action == 'addToProject'){
		$statusStr = $clManager->addProject($pid);
	}
	elseif($action == 'deleteProject'){
		$statusStr = $clManager->deleteProject($pid);
	}
	elseif($action == 'addPoint'){
		if(!$clManager->addPoint($pointtid, $pointlat, $pointlng, $notes)){
			$statusStr = $clManager->getErrorMessage();
		}
	}
	elseif($action && array_key_exists('clidadd',$_POST)){
		if(!$clManager->addChildChecklist($clidadd)){
			$statusStr = $LANG['ERR_ADDING_CHILD'];
		}
	}
	elseif($action && array_key_exists('cliddel',$_GET)){
		if(!$clManager->deleteChildChecklist($_GET['cliddel'])){
			$statusStr = $clManager->getErrorMessage();
		}
	}
	elseif($action == 'parseChecklist'){
		$resultArr = $clManager->parseChecklist($parsetid, $taxon, $targetClid, $parentClid, $targetPid, $transferMethod, $copyAttributes);
		if($resultArr){
			$statusStr = '<div>' . $LANG['CHECK_PARSED_SUCCESS'] . '</div>';
			if(isset($resultArr['targetPid'])){
				$targetPid = $resultArr['targetPid'];
				$statusStr .= '<div style="margin-left:15px"><a href="../projects/index.php?pid=' . $targetPid . '" target="_blank" rel="noopener" >' . $LANG['TARGET_PROJ'] . '</a></div>';
			}
			if(isset($resultArr['targetClid'])) $statusStr .= '<div style="margin-left:15px"><a href="checklist.php?clid=' . $resultArr['targetClid'] . '&pid=' . $targetPid . '" target="_blank" rel="noopener" >' . $LANG['TARGET_CHECKLIST'] . '</a></div>';
			if(isset($resultArr['parentClid'])){
				$parentClid = $resultArr['parentClid'];
				$statusStr .= '<div style="margin-left:15px"><a href="checklist.php?clid=' . $resultArr['parentClid'] . '&pid=' . $targetPid . '" target="_blank" rel="noopener" >' . $LANG['PARENT_CHECKLIST'] . '</a></div>';
			}
		}
    } elseif($action == 'resolveconflicts') {
        var_dump($_POST);
        $clVoucherReport->batchTransferConflicts($_POST['occid'], (array_key_exists('removetaxa',$_POST) ? true : false));
    }
}
$clArray = $clManager->getMetaData();
$clArray = $clManager->cleanOutArray($clArray);
$editors = $clManager->getEditors();
$projects = $clManager->getInventoryProjects();
$taxaMissingVouchers = $clVoucherReport->getNewVouchers($startPos, $displayMode);
$conflictArr = $clVoucherReport->getConflictVouchers();

$voucherProjects = [];
foreach($clVoucherManager->getVoucherProjects() as $collId => $name) {
    $voucherProjects[] = [
        'value' => $collId,
        'title' => $name,
        'disabled' => false,
    ];
}

$user = request()->user();
$userProjects = [];
foreach($user->projects() as $project) {
    $userProjects[] = [
        'value' => $project->pid,
        'title' => $project->projname,
        'disabled' => false,
    ];
}

$users = [];
foreach($clManager->getUserList() as $uid => $name) {
    if($name) {
        $users[] = [
            'value' => $uid,
            'title' => $name,
            'disabled' => false
        ];
    }
}

@endphp
<x-layout class="p-0">
    <div class="max-w-screen-lg px-10 pt-4">
        <x-breadcrumbs :items="[
            ['title' => 'Home', 'href' => url('') ],
            ['title' => 'Return to Checklist', 'href' => url('checklists/' . $clid) ],
            ['title' => 'Checklist Administration' ]
        ]"/>
    </div>
    <x-horizontal-nav.container default_active_tab="voucher-conflicts" :items="[
        ['id' => 'admin', 'label' => 'Admin', 'icon' => 'fa-solid fa-user'],
        ['id' => 'description', 'label' => 'Description', 'icon' => 'fa-solid fa-list'],
        ['id' => 'related-checklists', 'label' => 'Related Checklists', 'icon' => 'fa-solid fa-jar'],
        ['id' => 'voucher-image', 'label' => 'Add Image Voucher', 'icon' => 'fa-solid fa-database'],
        ['id' => 'non-vouchered-taxa', 'label' => 'Non-Vouchered Taxa', 'icon' => 'fa-solid fa-database'],
        ['id' => 'missing-taxa', 'label' => 'Missing Taxa', 'icon' => 'fa-solid fa-database'],
        ['id' => 'voucher-conflicts', 'label' => 'Voucher Conflicts', 'icon' => 'fa-solid fa-database'],
        ['id' => 'external-vouchers', 'label' => 'External Voucher Projects', 'icon' => 'fa-solid fa-database'],
        ['id' => 'reports', 'label' => 'Reports', 'icon' => 'fa-solid fa-database'],
    ]">
        {{-- ADMIN START--}}
        <x-horizontal-nav.tab name="admin" class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <div class="flex">
                    <span class="font-bold text-2xl">
                        Current Editors
                    </span>

                    <span class="flex flex-grow justify-end">
                        <x-modal>
                            <x-slot name="button">
                                Add Editor
                            </x-slot>
                            <x-slot name="title" class="text-2xl">
                                Add New User
                            </x-slot>
                            <x-slot name="body">
                                <form class="flex flex-col gap-4">
                                    <x-select id="editoruid" class="w-full" label="Select User" :items="$users" />
								    <input type="hidden" name="submitaction" value="addEditor" aria-label="{{ $LANG['ADDEDITOR'] }}" />

                                    <div class="flex align-items gap-2">
                                        <x-button type="submit">Add</x-button>
                                        <x-button variant="error" type="button">Cancel</x-button>
                                    </div>
                                </form>
                            </x-slot>
                        </x-modal>
                    </span>
                </div>
                <hr />
                <div>
                    @foreach ($editors as $uid => $editor)
                        <div class="flex items-center gap-2 border p-2 border-base-300 bg-base-200 rounded-md">
                            <span class="flex-grow">{{ $editor['name'] }}</span>
                            <form method="post">
                                @csrf
								<input name="pid" type="hidden" value="{{ $pid }}" />
								<input name="deleteuid" type="hidden" value="{{ $uid }}" />
								<input name="submitaction" type="hidden" value="DeleteEditor" />
                                <button type="submit">
                                    <x-icons.delete class="cursor-pointer" />
                                <button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col gap-4">
                <div class="flex">
                    <span class="font-bold text-2xl">
                        {{ $LANG['INVENTORYPROJECTS'] }}
                    </span>

                    <span class="flex flex-grow justify-end">
                        <x-modal>
                            <x-slot name="button" :disabled="count($userProjects) === 0" >
                                Add Project
                            </x-slot>
                            <x-slot name="title" class="text-2xl">
                                Add to a Project
                            </x-slot>
                            <x-slot name="body">
                                <form class="flex flex-col gap-4">
                                    <x-select id="pid" class="w-full" label="Select a Project" :items="$userProjects" />
								    <input type="hidden" name="submitaction" value="addToProject" aria-label="{{ $LANG['SUBMIT_BUTTON'] }}" />

                                    <div class="flex align-items gap-2">
                                        <x-button type="submit">Add</x-button>
                                        <x-button variant="error" type="button">Cancel</x-button>
                                    </div>
                                </form>
                            </x-slot>
                        </x-modal>
                    </span>
                </div>
                <hr />
                @foreach($projects as $linked_pid => $name)
                <div class="flex items-center gap-2 border p-2 border-base-300 bg-base-200 rounded-md">
                    <span class="flex-grow">
                        <x-link href="{{ url('projects/' . $pid) }}">{{ $name }}</x-link>
                    </span>
                    @can('PROJ_ADMIN', $linked_pid)
                    <form method="post">
                        @csrf
                        <input name="pid" type="hidden" value="{{ $linked_pid }}" />
                        <input name="submitaction" type="hidden" value="deleteProject" />
                        <button type="submit">
                            <x-icons.delete class="cursor-pointer" />
                        <button>
                    </form>
                    @endcan
                </div>
                @endforeach
            </div>

            <div class="flex flex-col gap-4">
                <div class="font-bold text-2xl">
                    Permanently Remove Checklist
                </div>
                <hr />
                <p>
                    Before a checklist can be deleted, all editors (except yourself) and inventory project assignments
                    must be removed. Inventory project assignments can only be removed by active managers of the project
                    or a system administrator.
                </p>
                <p class="font-bold text-lg text-warning">WARNING: Action cannot be undone</p>
                <x-button :disabled="count($projects) > 0 || count($editors) > 0" >
                    Delete Checklist
                </x-button>
            </div>
        </x-horizontal-nav.tab>
        {{-- ADMIN END --}}

        {{-- DESCRIPTION START--}}
        <x-horizontal-nav.tab name="description">
            <div class="font-bold text-2xl mb-2">
                Edit Checklist Details
            </div>
            <hr class="mb-2" />
            <form class="flex flex-col gap-4">
                <x-input label="Checklist Name" id="checklist_name" value="{{ $checklist->name }}"/>
                <x-input label="Authors" id="checklist_authors" value="{{ $checklist->authors }}" />

                <x-select class="w-full" label="External Project ID" id="external_project_id" :items="[
                    ['value' => 1, 'title' => 'None', 'disabled' => false],
                    ['value' => 0, 'title' => 'iNaturalist', 'disabled' => false]
                ]"/>

                <x-input label="Locality" id="checklist_locality" value="{{ $checklist->locality }}" />
                <x-input label="Citation" id="checklist_citation" value="{{ $checklist->publication }}" />
                <x-rich-editor label="Abstract" id="Abstract">
                    {!! Purify::clean($checklist->abstract) !!}
                </x-rich-editor>

                <x-input label="Notes" id="checklist_notes" value="{{ $checklist->notes }}"/>

                <x-select class="w-full" label="More Inclusive Reference Checklist" :items="[
                    ['value' => null, 'title' => 'None selected', 'disabled' => false]
                ]"/>

                <x-input label="Latitude" id="checklist_latitude" value="{{ $checklist->latCentroid }}"/>
                <x-input label="Longitude" id="checklist_longitude" value="{{ $checklist->longCentroid }}"/>
                <x-input label="Point Radius" id="checklist_point_radius" value="{{ $checklist->pointRadiusMeters }}" />

                <div>
                    <x-input area label="Polygon Footprint" id="footprintwkt" value="{{ $checklist->footprintGeoJson }}" />
                    <x-button class="mt-2" @click="openWindow('{{ url('tools/map/coordaid') }}?strict=1&mode=polygon')">
                        Polygon Tool
                    </x-button>
                </div>

                <div class="flex flex-col gap-2">
                    <x-checkbox id="dsynonyms" label="Display Synonyms" :checked="$settings->dsynonyms"/>
                    <x-checkbox id="dcommon" label="Common Names" :checked="$settings->dcommon"/>
                    <x-checkbox id="dimages" label="Display as images" :checked="$settings->dimages" />
                    <x-checkbox id="dvoucherimages" label="Use voucher images as the preferred image" :checked="$settings->dvoucherimages"/>
                    <x-checkbox id="ddetails" label="Show Details" :checked="$settings->ddetails"/>

                    {{-- Display images needs these two to be false --}}
                    <x-checkbox id="dvouchers" label="Notes & Vouchers" :checked="$settings->dvouchers"/>
                    <x-checkbox id="dauthors" label="Taxon Authors" :checked="$settings->dauthors"/>

                    <x-checkbox id="dalpha" label="Show Alphabetically" :checked="$settings->dalpha"/>
                    <x-checkbox id="dsubgenera" label="Show subgeneric ranking within scientific name" :checked="$settings->dsubgenera" />
                    <x-checkbox id="activatekey" label="Activate Identification Key" :checked="$settings->activatekey" />
                </div>

                <x-input label="Default Sort Sequence" id="sortsequence" type="number" value="{{ $checklist->sortSequence }}"/>

                <x-select id="access" class="w-64" label="Access" defaultValue="{{$checklist->access}}" :items="[
                            [ 'title' => 'Private', 'value' => 'private', 'disabled' => false],
                            [ 'title' => 'Can view with link', 'value' => 'view_with_link', 'disabled' => false],
                            [ 'title' => 'Public', 'value' => 'public', 'disabled' => false],
                        ]" />

                <x-button type="submit">Save Edits</x-button>
            </form>
        </x-horizontal-nav.tab>
        {{-- DESCRIPTION END --}}

        {{-- RELATED CHECKLISTS START--}}
        <x-horizontal-nav.tab name="related-checklists" class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <div class="flex">
                    <span class="font-bold text-2xl">
                       Children Checklists
                    </span>

                    <span class="flex flex-grow justify-end">
                        <x-button>Add Checklist</x-button>
                    </span>
                </div>
                <hr/>
                <p>
                There are no Children checklists
                </p>

                <x-link href="{{ legacy_url('/profile/viewprofile.php?excludeparent=' . $clid) }}">Create a Species Exclusion List</x-link>
            </div>


            <div class="flex flex-col gap-2">
                <div class="font-bold text-2xl">
                    Parent Checklists
                </div>
                <hr/>
                <p>
                There are no Parent checklists
                </p>
            </div>

            <div class="flex flex-col gap-2">
                <div class="font-bold text-2xl">
                   Batch Parse Species List
                </div>
                <hr/>
                <p>Use the following tool to parse a list into multiple children checklists based on taxonomic nodes (Liliopsida, Eudicots, Pinopsida, etc)</p>
                <form class="flex flex-col gap-4">
                    <div class="flex gap-4">
                        {{-- TODO (Logan) replace with taxon search?--}}
                        <x-input required id="taxon" label="Sci Name"/>
                        <x-input required id="parsetid" label="Taxonomic id"/>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <x-select id="targetclid" class="flex-grow" label="Target Checklist" :items="[]" />
                        <x-select id="parentclid" class="flex-grow" label="Parent Checklist" :items="[]" />
                        <x-select id="targetpid"  class="flex-grow" label="Add to project" :items="[]" />
                    </div>
                    <x-radio id="transmethod" defaultValue="1" name="transmethod" label="Transfer method" :options="[
                        ['label' => 'Transfer method', 'value' => '0'],
                        ['label' => 'Transfer taxa', 'value' => '1'],
                    ]" />
                    <x-checkbox id="parentclid" label="Copy over permissions and general attributes"/>
                    <x-button>Parse Checklist</x-button>
                    <x-link target="_blank" href="{{ legacy_url('/taxa/taxonomy/taxonomydisplay.php') }}">Open Taxonomic Thesaurus Explorer</x-link>
                </form>
            </div>
        </x-horizontal-nav.tab>
        {{-- RELATED CHECKLISTS END --}}

        {{-- ADD IMAGE VOUCHER START--}}
        <x-horizontal-nav.tab name="voucher-image" class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <div class="font-bold text-2xl">
                  Add Image Voucher and Link to Checklist
                </div>
                <hr/>
                <p>This form will allow you to add an image voucher linked to this checklist. If not already present, Scientific name will be added to checklist.</p>
            </div>
            {{-- Note: Should action collections/editor/observationsubmit.php --}}
            <form class="flex flex-col gap-4">
                <x-select class="w-full" label="Voucher Project" :items="$voucherProjects" />
                <x-button>Add Image Voucher and Link to Checklist</x-button>
            </form>
        </x-horizontal-nav.tab>
        {{-- ADD IMAGE VOUCHER END --}}

        {{-- NON-VOUCHERED TAXA START--}}
        <x-horizontal-nav.tab name="non-vouchered-taxa">
            <div class="font-bold text-2xl">
              Taxa without Vouchers: {{ $clVoucherReport->getNonVoucheredCnt() }} <i class="text-xl fa-solid fa-arrow-rotate-right"></i>
            </div>
            <hr/>
            <p> Listed below are species from the checklist that do not have linked specimen vouchers. Click on name to use the search statement above to dynamically query the occurrence dataset for possible voucher specimens. Use the pulldown to the right to display the specimens in a table format. </p>
            <x-select label="Display Mode"/>

            <div class="font-bold text-xl">
                All Taxa Contain Voucher Links
            </div>
        </x-horizontal-nav.tab>
        {{-- NON-VOUCHERED TAXA END --}}

        {{-- MISSING TAXA START--}}
        <x-horizontal-nav.tab name="missing-taxa">
            <div class="font-bold text-2xl">
              Possible Missing Taxa: # <i class="text-xl fa-solid fa-arrow-rotate-right"></i>
            </div>
            <hr/>

            <x-select label="Display Mode"/>
            <p>
            Listed below are taxon names not found in the checklist but are represented by one or more specimens that have a locality matching the above search term.
            </p>

            <div>
                @foreach ([
                'Somelong taxanomic (syn: Synonym)',
                'Somelong taxanomic var. someother taxonomic (syn: Synonym)'
                ] as $item)
                    <div class="flex items-center gap-2">
                        <x-link href="#">
                            {{$item}}
                        </x-link>
                        <i class="fa-solid fa-link"></i>
                    </div>
                @endforeach
            </div>
        </x-horizontal-nav.tab>
        {{-- MISSING TAXA END --}}

        {{-- VOUCHER CONFLICTS START--}}
        <x-horizontal-nav.tab name="voucher-conflicts" class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <div class="font-bold text-2xl">
                    {{ $LANG['VOUCHCONF'] }}
                </div>
                <hr/>
                <p>{{ $LANG['EXPLAIN_PARAGRAPH'] }}</p>
            </div>

            @if(count($conflictArr) > 0)
            <div class="font-bold">{{ $LANG['CONFLICT_COUNT'] }}: {{ count($conflictArr) }}</div>
            <form method="post" class="flex flex-col gap-4">
                @csrf
                <table class="w-full border-seperate text-sm">
                    <thead class="bg-neutral text-neutral-content ">
                        <th class="p-2 w-fit"><x-checkbox label="" onchange="document.querySelectorAll(`input[name='occid[]']`).forEach(v => v.checked=event.target.checked)"/></th>
                        <th class="p-2">{{ $LANG['CHECK_ID'] }}</th>
                        <th class="p-2">{{ $LANG['VOUCHER_SPEC'] }}</th>
                        <th class="p-2">{{ $LANG['CORRECTED_ID'] }}</th>
                        <th class="p-2">{{ $LANG['IDED_BY'] }}</th>
                    </thead>
                    <tbody>
                        @foreach($conflictArr as $id => $conflict)
                        <tr
                        @class([
                            'bg-base-200'=> $loop->even,
                            'bg-base-300' => $loop->odd,
                            'py-4',
                        ])>
                            <td @class(["p-2", "bg-neutral" => $loop->even, "bg-neutral-lighter" => $loop->odd])><x-checkbox name="occid[]" label="" value="{{ $conflict['occid'] }}"/></td>
                            <td class="p-2"><x-link target="_blank" href="{{ legacy_url('checklists/clsppeditor.php?tid=' . $conflict['tid'] .'&clid=' . $conflict['clid']) }}">{{ $conflict['listid'] }}</x-link></td>
                            <td class="p-2">
                                <x-link target="_blank" href="{{ url('occurrence/' . $conflict['occid']) }}">
                                {{ $conflict['recordnumber'] }}
                                </x-link>
                            </td>
                            <td class="p-2">{{ $conflict['specid'] }}</td>
                            <td class="p-2">{{ $conflict['identifiedby'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <input name="submitaction" type="hidden" value="resolveconflicts" />
                <x-checkbox id="removetaxa" :label="$LANG['REMOVE_TAXA']" :checked="true"/>
                <div>{{ $LANG['BATCH_ACTION'] }}:</div>
                <x-button>{{ $LANG['LINK_VOUCHERS'] }}</x-button>
            </form>
            <div>* {{ $LANG['CORRECTED_WILL_ADD'] }}</div>
            @endif
        </x-horizontal-nav.tab>

        {{-- REPORTS START--}}
        <x-horizontal-nav.tab name="external-vouchers" class="flex flex-col gap-4">
        todo external vouchers
        </x-horizontal-nav.tab>

        {{-- REPORTS START--}}
        <x-horizontal-nav.tab name="reports" class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <div class="font-bold text-2xl">
                  Reports
                </div>
                <hr/>
                <p>
                    See the Option Panel on the central page for more versatile export and print options that dynamically incorporate option selections.
                </p>
            </div>

            <div class="flex flex-col gap-1">
                <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=fullcsv&clid=' . $clid) }}">
                    Full species list (CSV)
                </x-link>
                @if($vouchersExist = $clVoucherManager->vouchersExist())
                <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=fullvoucherscsv&clid=' . $clid) }}">
                    Full species list with linked vouchers (CSV)
                </x-link>
                <x-link target="_blank"
                    href="{{ legacy_url('collections/download/index.php?searchvar=' . urlencode('clid=' . $clVoucherManager->getClidFullStr()) . '&noheader=1') }}">
                Linked occurrence vouchers only (DwC-A, CSV, Tab-delmited)
                </x-link>
                @endif
                <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=fullalloccurcsv&clid=' . $clid) }}">
                    Full species list with all occurrences matching search terms (CSV)
                </x-link>
                <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=pensoftxlsx&clid=' . $clid) }}">
                    Pensoft Excel Export (CSV)
                </x-link>
                <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=missingoccurcsv&clid=' . $clid) }}">
                    Specimens of taxa missing from checklist (CSV)
                </x-link>
                <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=problemtaxacsv&clid=' . $clid) }}">
                    Specimens with misspelled, illegal, and problematic scientific Names (CSV)
                </x-link>
            </div>
        </x-horizontal-nav.tab>
        {{-- REPORTS END --}}
    </x-horizontal-nav.container>
</x-layout>
