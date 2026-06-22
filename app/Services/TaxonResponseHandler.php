<?php

namespace App\Services;

use App\Helpers\InputNormalizer;
use App\Helpers\RedirectResponseHelper;

class TaxonResponseHandler {
    public static function redirectBackWithManagerIssues($editorManager, string $warningTranslationKey = 'taxonomy_taxoneditor.FOLLOWING_WARNINGS') {
        if ($editorManager->getWarningArr()) {
            $statusStr = __($warningTranslationKey) . ': ' . implode(';', $editorManager->getWarningArr());

            return RedirectResponseHelper::backWithError($statusStr);
        }

        if ($statusStr = $editorManager->getErrorMessage()) {
            return RedirectResponseHelper::backWithError($statusStr);
        }
    }

    public static function resolveUpdateTid(array $postData, $editorManager): ?int {
        $tid = InputNormalizer::optionalInt($postData['update-tid'] ?? null);

        if ($tid !== null) {
            return $tid;
        }

        if (method_exists($editorManager, 'getTid')) {
            return InputNormalizer::optionalInt($editorManager->getTid());
        }

        return null;
    }

    public static function handleStatusReportingAndRouting($statusStr, $editorManager, $redirectRoute, $redirectParams = []) {
        if ($response = self::redirectBackWithManagerIssues($editorManager)) {
            return $response;
        }

        if (in_array($redirectRoute, ['taxon.view', 'taxon.editview', 'taxon.profileEdit'], true)) {
            $redirectTid = InputNormalizer::optionalInt($redirectParams['tid'] ?? null);

            if ($redirectTid === null) {
                return RedirectResponseHelper::backWithError('Unable to redirect to taxon profile because the taxon ID was missing.');
            }

            $redirectParams['tid'] = $redirectTid;
        }

        return redirect()->route($redirectRoute, $redirectParams)->with('success', $statusStr);
    }
}
