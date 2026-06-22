<?php

namespace App\Services;

class TaxonomyMutationService {
    public static function getTaxonomyEditorManager($tid = null) {
        include_once legacy_path('/classes/TaxonomyEditorManager.php');
        $taxonEditorObj = new \TaxonomyEditorManager();
        $tid = (int) $tid;
        if ($tid) {
            $taxonEditorObj->setTid($tid);
            $taxonEditorObj->setTaxon();
        }

        return $taxonEditorObj;
    }

    public static function handleTaxonEditsAction($editorManager, array $postData) {
        return $editorManager->submitTaxonEdits($postData);
    }

    public static function handleUpdateTaxStatusAction($editorManager, array $postData) {
        return $editorManager->submitTaxStatusEdits($postData['parenttid'], $postData['tidaccepted']);
    }

    public static function handleSynonymEditsAction($editorManager, array $postData) {
        $tid = $postData['tid'] ?? null;

        return $editorManager->submitSynonymEdits($postData['tidsyn'], $tid, $postData['unacceptabilityreason'], $postData['notes'], $postData['sortsequence']);
    }

    public static function handleLinkToAcceptedAction($editorManager, array $postData) {
        $deleteOther = array_key_exists('deleteother', $postData);

        return $editorManager->submitAddAcceptedLink($postData['tidaccepted'], $deleteOther);
    }

    public static function handleDeleteAcceptedLinkAction($editorManager, array $postData) {
        return $editorManager->removeAcceptedLink($postData['deltidaccepted']);
    }

    public static function handleChangeToAcceptedAction($editorManager, array $postData) {
        $tid = $postData['tid'] ?? null;
        $tidAccepted = $postData['tidaccepted'];
        $switchAcceptance = array_key_exists('switchacceptance', $postData);

        return $editorManager->submitChangeToAccepted($tid, $tidAccepted, $switchAcceptance);
    }

    public static function handleChangeToNotAcceptedAction($editorManager, array $postData) {
        $tid = $postData['tid'] ?? null;
        $tidAccepted = $postData['tidaccepted'];

        return $editorManager->submitChangeToNotAccepted($tid, $tidAccepted, $postData['unacceptabilityreason'], $postData['notes']);
    }

    public static function handleUpdateHierarchyAction($editorManager, array $postData) {
        $tid = $postData['tid'] ?? null;
        $editorManager->rebuildHierarchy($tid);

        return true;
    }

    public static function handleRemapTaxonAction($editorManager, array $postData) {
        $statusStr = '';
        $remapStatus = $editorManager->transferResources($postData['remaptid']);
        if ($editorManager->getWarningArr()) {
            $statusStr = __('taxonomy_taxoneditor.FOLLOWING_WARNINGS') . ': ' . implode(';', $editorManager->getWarningArr());
        }

        if ($remapStatus) {
            return __('taxonomy_taxoneditor.SUCCESS_REMAPPING') . ' ' . $statusStr;
        }

        return $editorManager->getErrorMessage();
    }

    public static function handleDeleteTaxonAction($editorManager) {
        $statusStr = '';
        $delStatus = $editorManager->deleteTaxon();
        if ($editorManager->getWarningArr()) {
            $statusStr = __('taxonomy_taxoneditor.FOLLOWING_WARNINGS') . ': ' . implode(';', $editorManager->getWarningArr());
        }

        if ($delStatus) {
            return __('taxonomy_taxonomydelete.SUCCESS_DELETING') . ' ' . $statusStr;
        }

        return $editorManager->getErrorMessage();
    }

    public static function processUpdateAction(string $editType, $editorManager, array $postData) {
        return match ($editType) {
            'taxonedits' => self::handleTaxonEditsAction($editorManager, $postData),
            'updatetaxstatus' => self::handleUpdateTaxStatusAction($editorManager, $postData),
            'synonymedits' => self::handleSynonymEditsAction($editorManager, $postData),
            'linkToAccepted' => self::handleLinkToAcceptedAction($editorManager, $postData),
            'deltidaccepted' => self::handleDeleteAcceptedLinkAction($editorManager, $postData),
            'changetoaccepted' => self::handleChangeToAcceptedAction($editorManager, $postData),
            'changeToNotAccepted' => self::handleChangeToNotAcceptedAction($editorManager, $postData),
            'updatehierarchy' => self::handleUpdateHierarchyAction($editorManager, $postData),
            'remapTaxon' => self::handleRemapTaxonAction($editorManager, $postData),
            'deleteTaxon' => self::handleDeleteTaxonAction($editorManager),
            default => 'Unsupported edit type',
        };
    }
}