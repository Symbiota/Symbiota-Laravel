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
    <div class="max-w-screen-lg px-10 pt-4 mx-auto">
        <x-breadcrumbs :items="[
            ['title' => 'Home', 'href' => url('') ],
            ['title' => 'Return to Checklist', 'href' => url('checklists/' . $clid) ],
            ['title' => 'Checklist Administration' ]
        ]"/>
    </div>
    <x-horizontal-nav.container default_active_tab="description" :items="$TABS">
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
            <x-checklist.form :checklist="$checklist" :userChecklists="$userChecklists" />
        </x-horizontal-nav.tab>
        {{-- DESCRIPTION END --}}

        {{-- RELATED CHECKLISTS START--}}
        <x-horizontal-nav.tab name="related-checklists" class="flex flex-col gap-4">
            <x-checklist.children
                :clid="$clid"
                :childChecklistsItems="$childChecklistsItems"
                :childChecklists="$childChecklists"
                :clManager="$clManager"
            />

            <x-checklist.parents :clManager="$clManager" />

            <x-checklist.batch-parse-species
                :childChecklistsItems="$clManager"
                :transferMethod="$transferMethod"
                :copyAttributes="$copyAttributes"
                :userChecklists="$userChecklists"
                :userProjects="$userProjects"
            />
        </x-horizontal-nav.tab>
        {{-- RELATED CHECKLISTS END --}}

        {{-- ADD IMAGE VOUCHER START--}}
        <x-horizontal-nav.tab name="voucher-image" class="flex flex-col gap-4">
            <x-checklist.add-voucher-image :voucherProjects="$voucherProjects" />
        </x-horizontal-nav.tab>
        {{-- ADD IMAGE VOUCHER END --}}

        {{-- (TODO Logan possiblity rework feature?) NON-VOUCHERED TAXA START--}}
        <x-horizontal-nav.tab name="non-vouchered-taxa">
            <x-checklist.non-vouchered-taxa :clVoucherReport="$clVoucherReport" :nonVoucheredTaxa="$nonVoucheredTaxa" :clid="$clid"/>
        </x-horizontal-nav.tab>
        {{-- NON-VOUCHERED TAXA END --}}

        {{-- (TODO Logan possiblity rework feature?) MISSING TAXA START--}}
        <x-horizontal-nav.tab name="missing-taxa">
            <x-checklist.missing-taxa :displayMode="$displayMode" :clVoucherReport="$clVoucherReport" />
        </x-horizontal-nav.tab>
        {{-- MISSING TAXA END --}}

        {{-- VOUCHER CONFLICTS START--}}
        <x-horizontal-nav.tab name="voucher-conflicts" class="flex flex-col gap-4">
            <x-checklist.voucher-conflicts :conflicts="$conflictArr" />
        </x-horizontal-nav.tab>

        {{-- REPORTS START--}}
        <x-horizontal-nav.tab name="external-vouchers" class="flex flex-col gap-4">
        todo external vouchers
        </x-horizontal-nav.tab>

        {{-- REPORTS START--}}
        <x-horizontal-nav.tab name="reports" class="flex flex-col gap-4">
            <x-checklist.reports :clid="$clid" :clVoucherManager="$clVoucherManager" />
        </x-horizontal-nav.tab>
        {{-- REPORTS END --}}
    </x-horizontal-nav.container>
</x-layout>
