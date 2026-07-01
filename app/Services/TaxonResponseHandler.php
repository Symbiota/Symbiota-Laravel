<?php

namespace App\Services;

class TaxonResponseHandler {
    public static function redirectBackWithManagerIssues($editorManager, string $warningTranslationKey = 'taxonomy_taxoneditor.FOLLOWING_WARNINGS') {
        if ($editorManager->getWarningArr()) {
            $statusStr = __($warningTranslationKey) . ': ' . implode(';', $editorManager->getWarningArr());

            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]);
        }

        if ($statusStr = $editorManager->getErrorMessage()) {
            return redirect()->back()->withInput()->withErrors(['error' => $statusStr]);
        }
    }

    public static function resolveUpdateTid(array $postData, $editorManager): ?int {
        $tid = intval($postData['update-tid'] ?? 0);

        if ($tid > 0) {
            return $tid;
        }

        if (method_exists($editorManager, 'getTid')) {
            return intval($editorManager->getTid() ?? 0);
        }

        return null;
    }

    public static function handleStatusReportingAndRouting($statusStr, $editorManager, $redirectRoute, $redirectParams = []) {
        if ($response = self::redirectBackWithManagerIssues($editorManager)) {
            return $response;
        }

        if (in_array($redirectRoute, ['taxon.view', 'taxon.editview', 'taxon.profileEdit'], true)) {
            $redirectTid =  intval($redirectParams['tid'] ?? 0);

            if ($redirectTid === 0) {
                return redirect()->back()->withInput()->withErrors(['error' => __('taxonomy_taxonomyloader.MISSING_TAXON_ID_FOR_PROFILE_REDIRECT')]);
            }

            $redirectParams['tid'] = $redirectTid;
        }

        return redirect()->route($redirectRoute, $redirectParams)->with('success', $statusStr);
    }
}
