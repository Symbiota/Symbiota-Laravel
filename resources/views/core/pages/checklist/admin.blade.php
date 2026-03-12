@php global $SERVER_ROOT, $LANG;

include_once(legacy_path('/classes/ChecklistAdmin.php'));
include_once(legacy_path('/classes/ChecklistVoucherAdmin.php'));
include_once(legacy_path('/classes/ChecklistVoucherReport.php'));
include_once(legacy_path('/classes/utilities/Language.php'));
Language::load([
    'checklists/checklistadmin',
    'checklists/vaconflicts',
    'checklists/voucheradmin',
    'checklists/vamissingtaxa',
    'checklists/checklistadminchildren',
    'checklists/checklistadminmeta'
]);

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
//$clVoucherReport->setCollectionVariables();

$statusStr = '';

$clAdmin = Gate::check('CL_ADMIN', $clid);
$settings = $checklist->defaultSettings? json_decode($checklist->defaultSettings): [];
$dynamicProperties = $checklist->dynamicProperties? json_decode($checklist->dynamicProperties): [];

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
	elseif($action && array_key_exists('cliddel',$_POST)){
		if(!$clManager->deleteChildChecklist($_POST['cliddel'])){
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
$nonVoucheredTaxa = $clVoucherReport->getNonVoucheredTaxa($startPos);
$childChecklists = $clManager->getChildrenChecklist();

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

$childChecklistsItems = [];
$userChecklists = [];

foreach($user->checklists() as $child) {
    $item =  [
        'value' => $child->clid,
        'title' => $child->name,
        'disabled' => false,
    ];
    $userChecklists[] = $item;
    if(!array_key_exists($child->clid, $childChecklists)) {
        $childChecklistsItems[] = $item;
    }
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

$TABS = [
    ['id' => 'admin', 'label' => $LANG['ADMIN'], 'icon' => 'fa-solid fa-user'],
    ['id' => 'description', 'label' => $LANG['DESCRIPTION'], 'icon' => 'fa-solid fa-list'],
    ['id' => 'related-checklists', 'label' => $LANG['RELATEDCHECK'], 'icon' => 'fa-solid fa-jar'],
    ['id' => 'voucher-image', 'label' => $LANG['ADDIMGVOUCHER'], 'icon' => 'fa-solid fa-database'],
    ['id' => 'non-vouchered-taxa', 'label' => $LANG['NON_VOUCHERED'], 'icon' => 'fa-solid fa-database'],
    ['id' => 'missing-taxa', 'label' => $LANG['MISSINGTAXA'], 'icon' => 'fa-solid fa-database'],
    ['id' => 'voucher-conflicts', 'label' => $LANG['VOUCHCONF'], 'icon' => 'fa-solid fa-database'],
    ['id' => 'external-vouchers', 'label' => $LANG['EXTERNALVOUCHERS'], 'icon' => 'fa-solid fa-database'],
    ['id' => 'reports', 'label' => $LANG['REPORTS'], 'icon' => 'fa-solid fa-database'],
];

@endphp
<x-layout class="p-0">
    <div class="max-w-screen-lg px-10 pt-4">
        <x-breadcrumbs :items="[
            ['title' => 'Home', 'href' => url('') ],
            ['title' => 'Return to Checklist', 'href' => url('checklists/' . $clid) ],
            ['title' => 'Checklist Administration' ]
        ]"/>
    </div>
    <x-horizontal-nav.container default_active_tab="admin" :items="$TABS">
        {{-- ADMIN START--}}
        <x-horizontal-nav.tab name="admin" class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <div class="flex">
                    <span class="font-bold text-2xl">
                        {{ $LANG['CURREDIT'] }}
                    </span>

                    <span class="flex flex-grow justify-end">
                        <x-modal>
                            <x-slot name="button">
                                {{ $LANG['ADDEDITOR'] }}
                            </x-slot>
                            <x-slot name="title" class="text-2xl">
                                {{ $LANG['ADDNEWUSER'] }}
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
                <div class="flex flex-col gap-2">
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
                                {{ $LANG['LINKTOPROJECT'] }}
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
                    {{ $LANG['PERMREMOVECHECK'] }}
                </div>
                <hr />
                <p>{{ $LANG['REMOVEUSERCHECK'] }}</p>
                <p class="font-bold text-lg text-warning">{{ $LANG['WARNINGNOUN'] }}</p>
                <x-button :disabled="count($projects) > 0 || count($editors) > 0" >
                    {{ $LANG['DELETECHECK'] }}
                </x-button>
            </div>
        </x-horizontal-nav.tab>
        {{-- ADMIN END --}}

        {{-- DESCRIPTION START--}}
        <x-horizontal-nav.tab name="description">
            <div class="font-bold text-2xl mb-2">
                {{ $LANG['EDITCHECKDET'] }}
            </div>
            <hr class="mb-2" />
            <form class="flex flex-col gap-4">
                <x-input :label="$LANG['CHECKNAME']" id="checklist_name" value="{{ $checklist->name }}"/>
                <x-input :label="$LANG['AUTHORS']" id="checklist_authors" value="{{ $checklist->authors }}" />
                <x-select class="w-full" id="type" :label="$LANG['CHECKTYPE']" :items="[
                    ['value' => 'static', 'title' => $LANG['GENCHECK'], 'disabled' => false],
                    ['value' => 'excludespp', 'title' => $LANG['EXCLUDESPP'], 'disabled' => !$userChecklists],
                    ['value' => 'rarespp', 'title' => $LANG['RARETHREAT'], 'disabled' => !Gate::check('RARE_SPP_ADMIN')]
                ]"/>
                {{-- TODO (Logan) There is a an optional for excluding parent. Generally confusing not sure how to proceed--}}
                <x-select class="w-full" :label="$LANG['EXTSERVICE']" id="externalservice" :items="[
                    ['value' => 0, 'title' => 'None', 'disabled' => false],
                    ['value' => 'iNaturalist', 'title' => 'iNaturalist', 'disabled' => false]
                ]"/>

                {{-- TODO (Logan) toggle this only when iNaturalist is selected --}}
                <x-input :label="$LANG['EXTSERVICEID']" id="externalserviceid" />
                <x-input :label="$LANG['EXTSERVICETAXON']" id="externalserviceiconictaxon" />

                <x-input :label="$LANG['LOC']" id="checklist_locality" value="{{ $checklist->locality }}" />
                <x-input :label="$LANG['CITATION']" id="checklist_citation" value="{{ $checklist->publication }}" />
                <x-rich-editor :label="$LANG['LOC']" id="Abstract">
                    {!! Purify::clean($checklist->abstract) !!}
                </x-rich-editor>

                <x-input :label="$LANG['NOTES']" id="checklist_notes" value="{{ $checklist->notes }}"/>

				{{-- uses $refClArr = $clManager->getReferenceChecklists(); $id $name--}}
                <x-select class="w-full" :label="$LANG['REFERENCE_CHECK']" :items="[
                    ['value' => null, 'title' => 'None selected', 'disabled' => false]
                ]"/>

                {{-- TODO (Logan) point radius tool --}}
                <x-input :label="$LANG['LATCENT']" id="checklist_latitude" value="{{ $checklist->latCentroid }}"/>
                <x-input :label="$LANG['LONGCENT']" id="checklist_longitude" value="{{ $checklist->longCentroid }}"/>
                <x-input :label="$LANG['POINTRAD']" id="checklist_point_radius" value="{{ $checklist->pointRadiusMeters }}" />

                <div>
                    <x-input area :label="$LANG['POLYFOOT']" id="footprintwkt" value="{{ $checklist->footprintGeoJson }}" />
                    <x-button class="mt-2" @click="openWindow('{{ url('tools/map/coordaid') }}?strict=1&mode=polygon')">
                        {{-- TODO (Logan) translation --}}
                        Polygon Tool
                    </x-button>
                </div>

                <div class="flex flex-col gap-2">
                    <x-checkbox id="dsynonyms" :label="$LANG['DISPLAY_SYNONYMS']" :checked="$settings->dsynonyms ?? false"/>
                    <x-checkbox id="dcommon" :label="$LANG['COMMON']" :checked="$settings->dcommon ?? false"/>
                    <x-checkbox id="dimages" :label="$LANG['DISPLAYIMAGES']" :checked="$settings->dimages ?? false" />
                    <x-checkbox id="dvoucherimages" :label="$LANG['DISPLAYVOUCHERIMAGES']" :checked="$settings->dvoucherimages ?? false"/>
                    <x-checkbox id="ddetails" :label="$LANG['SHOWDETAILS']" :checked="$settings->ddetails ?? false"/>

                    {{-- Display images needs these two to be false --}}
                    <x-checkbox id="dvouchers" :label="$LANG['NOTESVOUC']" :checked="$settings->dvouchers ?? false"/>
                    <x-checkbox id="dauthors" :label="$LANG['TAXONAUTHOR']" :checked="$settings->dauthors ?? false"/>

                    <x-checkbox id="dalpha" :label="$LANG['TAXONABC']" :checked="$settings->dalpha ?? false"/>
                    <x-checkbox id="dsubgenera" :label="$LANG['SHOWSUBGENERA']" :checked="$settings->dsubgenera ?? false" />
                    <x-checkbox id="activatekey" :label="$LANG['ACTIVATEKEY']" :checked="$settings->activatekey ?? false" />
                </div>

                <x-input :label="$LANG['DEFAULT_SORT']" id="sortsequence" type="number" value="{{ $checklist->sortSequence }}"/>

                <x-select id="access" class="w-64" :label="$LANG['ACCESS']" defaultValue="{{$checklist->access}}" :items="[
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
                       {{ $LANG['CHILD_CHECKLIST'] }}
                    </span>

                    <span class="flex flex-grow justify-end">
                        <x-modal>
                            <x-slot name="button">
                                {{ $LANG['ADD_CHILD'] }}
                            </x-slot>
                            <x-slot name="title" class="text-2xl">
                                {{ $LANG['ADD_CHILD'] }}
                            </x-slot>
                            <x-slot name="body">
                                <form method="post" class="flex flex-col gap-4">
                                    <x-select id="clidadd" class="w-full" label="Checklist" :items="$childChecklistsItems" />
								    <input type="hidden" name="submitaction" value="addChildChecklist" aria-label="{{ $LANG['ADD_CHILD'] }}" />

                                    <div class="flex align-items gap-2">
                                        <x-button type="submit">Add</x-button>
                                        <x-button variant="error" type="button">Cancel</x-button>
                                    </div>
                                </form>
                            </x-slot>
                        </x-modal>
                    </span>
                </div>
                <hr/>
                <p>{{ $LANG['CHILD_DESCRIBE'] }}</p>
                @if($childChecklists)
                    @foreach ($childChecklists as $child_clid => $child)
                    <div class="flex items-center gap-2 border p-2 border-base-300 bg-base-200 rounded-md">
                        <span class="flex-grow">
                            <x-link target="_blank" href="{{ url('checklists/' . $child_clid) }}">
                                {{ $child['name']}}
                            </x-link>
                        </span>
                        <form method="post">
                            @csrf
                            <input name="cliddel" type="hidden" value="{{ $child_clid }}" />
                            <input name="submitaction" type="hidden" value="delchild" />
                            <button type="submit">
                                <x-icons.delete class="cursor-pointer" />
                            <button>
                        </form>
                    </div>
                    @endforeach
                @else
                <p>{{ $LANG['NO_CHILDREN'] }}</p>
                @endif

                <x-link href="{{ legacy_url('/profile/viewprofile.php?excludeparent=' . $clid) }}">
                    {{ $LANG['CREATE_EXCLUSION_LIST'] }}
                </x-link>
            </div>

            <div class="flex flex-col gap-2">
                <div class="font-bold text-2xl">
                    {{ $LANG['PARENTS'] }}
                </div>
                <hr/>
                @if($parents = $clManager->getParentChecklists())
                <div class="pl-4">
                    @foreach($parents as $parent_clid => $name)
                    <li>
                        <x-link target="_blank" href="{{ url('checklists/' . $parent_clid) }}">
                            {{ $name }}
                        </x-link>
                    </li>
                    @endforeach
                </div>
                @else
                    <p>{{ $LANG['NO_PARENTS'] }}</p>
                @endif
            </div>

            <div class="flex flex-col gap-2">
                <div class="font-bold text-2xl">
                   {{ $LANG['BATCH_PARSE_SP_LIST'] }}
                </div>
                <hr/>
                <p>{{ $LANG['BATCH_PARSE_DESCRIBE'] }}</p>
                <form class="flex flex-col gap-4">
                    <div class="flex gap-4">
                        {{-- TODO (Logan) replace with taxon search? --}}
                        <x-input required id="taxon" :label="$LANG['TAXONOMICNODE']"/>
                        <x-input required id="parsetid" :label="$LANG['PARSETID']"/>
                    </div>
                    <x-select id="targetclid" class="w-full" label="Target Checklist" :items="$userChecklists" />
                    <x-select id="parentclid" class="w-full" label="Parent Checklist" :items="$userChecklists" />
                    <x-select id="targetpid"  class="w-full" label="Add to project" :items="$userProjects" />
                    </div>
                    <x-radio id="transmethod" :defaultValue="$transferMethod" name="transmethod" label="Transfer method" :options="[
                        ['label' => $LANG['TRANSFERTAXA'], 'value' => 0],
                        ['label' => $LANG['COPYTAXA'], 'value' => 1],
                    ]" />
                    <x-checkbox id="parentclid" :label="$LANG['COPYPERMISSIONANDGENERAL']" :checked="$copyAttributes"/>
                    <input name="submitaction" type="hidden" value="parseChecklist" />
                    <x-button>{{ $LANG['PARSE_CHECKLIST'] }}</x-button>
                    <x-link target="_blank" href="{{ legacy_url('/taxa/taxonomy/taxonomydisplay.php') }}">{{ $LANG['OPEN_TAX_THES_EXPLORE'] }}</x-link>
                </form>
            </div>
        </x-horizontal-nav.tab>
        {{-- RELATED CHECKLISTS END --}}

        {{-- ADD IMAGE VOUCHER START--}}
        <x-horizontal-nav.tab name="voucher-image" class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <div class="font-bold text-2xl">
                  {{ $LANG['ADDIMGVOUC'] }}
                </div>
                <hr/>
                <p>{{ $LANG['FORMADDVOUCH'] }}</p>
            </div>
            {{-- Note: Should action collections/editor/observationsubmit.php --}}
            <form class="flex flex-col gap-4" action="{{ legacy_url('collections/editor/observationsubmit.php') }}">
                <x-select name="collid" class="w-full" default="0" :label="$LANG['SELECTVOUCPROJ']" :items="$voucherProjects" />
                <x-button>{{ $LANG['ADDIMGVOUC'] }}</x-button>
            </form>
        </x-horizontal-nav.tab>
        {{-- ADD IMAGE VOUCHER END --}}

        {{-- (TODO Logan possiblity rework feature?) NON-VOUCHERED TAXA START--}}
        <x-horizontal-nav.tab name="non-vouchered-taxa">
            <div class="font-bold text-2xl">
              {{ $LANG['TAXWITHOUTVOUCH'] }}: {{ $clVoucherReport->getNonVoucheredCnt() }} <i class="text-xl fa-solid fa-arrow-rotate-right"></i>
            </div>
            <hr/>
            <p>{{ $LANG['LISTEDBELOWARESPECINSTRUC'] }}</p>
            <x-select label="Display Mode"/>
            @if($nonVoucheredTaxa)
            <div>
            @foreach($nonVoucheredTaxa as $family => $taxa)
            <div>
                <div class="text-lg font-bold">{{ $family }}</div>
                @foreach($taxa as $tid => $taxon)
                <div class="pl-4">
                    <x-link class="text-base" href="{{ url('taxon/' . $taxon['t']) }}">
                        {{ $taxon['s'] }}
                    </x-link>
                    <a target="blank" href="{{ legacy_url('collections/list.php?usethes=1&reset=1&mode=voucher&taxa=' . $taxon['s'] . '&targetclid=' . $clid . '&targettid=' . $taxon['t']) }}">
                        <i class="ml-4 fa-solid fa-list"></i>
                    </a>
                </div>
                @endforeach
            </div>
            @endforeach
            </div>
            @else
            <div class="font-bold text-xl">
                {{ $LANG['ALLTAXACONTAINVOUCH'] }}
            </div>
            @endif
        </x-horizontal-nav.tab>
        {{-- NON-VOUCHERED TAXA END --}}

        {{-- (TODO Logan possiblity rework feature?) MISSING TAXA START--}}
        <x-horizontal-nav.tab name="missing-taxa">
            <div class="font-bold text-2xl">
              <span>
              {{ $displayMode == 2? $LANG['PROBLEMS']: $LANG['POSS_MISSING'] }}:
              </span>
              <i class="text-xl fa-solid fa-arrow-rotate-right"></i>
              {{ $clVoucherReport->getMissingTaxaCount() }}
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
                  {{ $LANG['REPORTS'] }}
                </div>
                <hr/>
                <p>{{ $LANG['ADDITIONAL'] }}</p>
            </div>

            <div class="flex flex-col gap-1">
                <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=fullcsv&clid=' . $clid) }}">
                    {{ $LANG['FULLSPECLIST'] }}
                </x-link>
                @if($vouchersExist = $clVoucherManager->vouchersExist())
                <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=fullvoucherscsv&clid=' . $clid) }}">
                    {{ $LANG['FULLSPECLISTVOUCHER'] }}
                </x-link>
                <x-link target="_blank"
                    href="{{ legacy_url('collections/download/index.php?searchvar=' . urlencode('clid=' . $clVoucherManager->getClidFullStr()) . '&noheader=1') }}">
                    {{ $LANG['VOUCHERONLY'] }}
                </x-link>
                @endif
                <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=fullalloccurcsv&clid=' . $clid) }}">
                    {{ $LANG['FULLSPECLISTALLOCCUR'] }}
                </x-link>
                <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=pensoftxlsx&clid=' . $clid) }}">
                    {{ $LANG['PENSOFT_XLSX_EXPORT'] }}
                </x-link>
                <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=missingoccurcsv&clid=' . $clid) }}">
                    {{ $LANG['SPECMISSTAXA'] }}
                </x-link>
                <x-link href="{{ legacy_url('voucherreporthandler.php?rtype=problemtaxacsv&clid=' . $clid) }}">
                    {{ $LANG['SPECMISSPELLED'] }}
                </x-link>
            </div>
        </x-horizontal-nav.tab>
        {{-- REPORTS END --}}
    </x-horizontal-nav.container>
</x-layout>
