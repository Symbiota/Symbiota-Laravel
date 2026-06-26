<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CollMetadataController extends Controller {
    public static function create(Request $request) {
        return view('pages.collections.collmetadata.create', self::createPageData($request));
    }

    public static function store(Request $request) {
        $collmanager = self::collmetadataManager('write', $request);

        if ((string) $request->input('action') !== 'newCollection') {
            return self::redirectParams(null);
        }

        if (! trim((string) $request->input('collType'))) {
            return self::redirectParams(
                null,
                __('misc_collmetadata.SELECT_DATASET_TYPE_REQUIRED'),
                'error',
                0,
                true,
            );
        }

        $collid = $collmanager->collectionInsert($request->all());

        if ($collid) {
            return self::redirectParams(
                (int) $collid,
                __('misc_collmetadata.ADD_SUCCESS') . '! ' . __('misc_collmetadata.ADD_STUFF') . '.',
                'success',
                1,
            );
        }

        return self::redirectParams(
            null,
            (string) $collmanager->getErrorMessage(),
            'error',
            0,
            true,
        );
    }

    public static function edit(Request $request, int $collid) {
        return view('pages.collections.collmetadata.edit', self::editPageData($request, $collid));
    }

    public static function update(Request $request, int $collid) {
        $collmanager = self::collmetadataManager('write', $request);
        $collmanager->setCollid($collid);

        $action = (string) $request->input('action');

        if ($action === 'saveEdits') {
            if ($collmanager->collectionUpdate($request->all()) === true) {
                return redirect(url('collections/' . $collid));
            }

            return self::redirectParams(
                $collid,
                (string) $collmanager->getErrorMessage(),
                'error',
                0,
                true,
            );
        }

        if ($action === 'saveResourceLink') {
            if (! $collmanager->saveResourceLink($request->all())) {
                return self::redirectParams($collid, (string) $collmanager->getErrorMessage(), 'error', 1);
            }

            return self::redirectParams($collid, tabIndex: 1);
        }

        if ($action === 'saveContact') {
            if (! $collmanager->saveContact($request->all())) {
                return self::redirectParams($collid, (string) $collmanager->getErrorMessage(), 'error', 1);
            }

            return self::redirectParams($collid, tabIndex: 1);
        }

        if ($action === 'deleteContact') {
            if (! $collmanager->deleteContact((int) $request->input('contactIndex'))) {
                return self::redirectParams($collid, (string) $collmanager->getErrorMessage(), 'error', 1);
            }

            return self::redirectParams($collid, tabIndex: 1);
        }

        if ($action === 'linkAddress') {
            if (! $collmanager->linkAddress((int) $request->input('iid'))) {
                return self::redirectParams($collid, (string) $collmanager->getErrorMessage(), 'error', 1);
            }

            return self::redirectParams($collid, tabIndex: 1);
        }

        if ($action === 'removeAddress') {
            if (! $collmanager->removeAddress((int) $request->input('removeiid'))) {
                return self::redirectParams($collid, (string) $collmanager->getErrorMessage(), 'error', 1);
            }

            return self::redirectParams($collid, tabIndex: 1);
        }

        return self::redirectParams($collid);
    }

    private static function createPageData(Request $request): array {
        $collmanager = self::collmetadataManager('readonly', $request);
        $rightsTerms = self::rightsTerms();

        return [
            'collid' => null,
            'collection' => [],
            'fullCatArr' => $collmanager->getCategoryArr(),
            'selectedCategories' => [],
            'rightsTerms' => $rightsTerms,
            'rightsState' => self::rightsState(null, $rightsTerms),
            'showGbifPublishing' => self::gbifPublishingEnabled(),
        ];
    }

    private static function editPageData(Request $request, int $collid): array {
        $collmanager = self::collmetadataManager('readonly', $request);
        $rightsTerms = self::rightsTerms();

        $collmanager->setCollid($collid);
        $collection = current($collmanager->getCollectionMetadata()) ?: [];

        if (! $collection) {
            abort(404);
        }

        return [
            'collid' => $collid,
            'collection' => $collection,
            'fullCatArr' => $collmanager->getCategoryArr(),
            'selectedCategories' => $collmanager->getCollectionCategories(),
            'resourceLinks' => self::decodeJsonArray($collection['resourcejson'] ?? ''),
            'contacts' => self::decodeJsonArray($collection['contactjson'] ?? ''),
            'resourceJson' => $collection['resourcejson'] ?? '',
            'contactJson' => $collection['contactjson'] ?? '',
            'address' => $collmanager->getAddress(),
            'institutionOptions' => $collmanager->getInstitutionArr(),
            'languageCodes' => self::languageCodes(),
            'rightsTerms' => $rightsTerms,
            'rightsState' => self::rightsState($collection['rights'] ?? null, $rightsTerms),
            'showGbifPublishing' => self::gbifPublishingEnabled(),
            'tabIndex' => min((int) $request->query('tabindex', 0), 1),
        ];
    }

    private static function collmetadataManager(string $type = 'readonly', ?Request $request = null) {
        global $SERVER_ROOT, $CLIENT_ROOT, $IS_ADMIN, $USERNAME;

        $CLIENT_ROOT = config('portal.use_client_root') ? '/' . config('portal.name') : '';
        $IS_ADMIN = Gate::check('SUPER_ADMIN');
        $USERNAME = (string) ($request?->user()?->username ?? $request?->user()?->name ?? '');

        include_once legacy_path('/classes/OccurrenceCollectionProfile.php');

        return new \OccurrenceCollectionProfile($type);
    }

    //helper function for json arays
    private static function decodeJsonArray(?string $json): array {
        if (! $json) {
            return [];
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }

    //helper function for resource languages
    private static function languageCodes(): array {
        global $EXTENDED_LANG, $DEFAULT_LANG;

        $default = $EXTENDED_LANG ?: $DEFAULT_LANG ?: 'en';
        $codes = array_values(array_filter(array_map('trim', explode(',', strtolower($default)))));

        return $codes ?: ['en'];
    }

    private static function rightsTerms(): array {
        global $RIGHTS_TERMS;

        return $RIGHTS_TERMS ?? [];
    }

    // get the current rights and check for orphaned term
    private static function rightsState(?string $currentRights, array $rightsTerms): array {
        $selected = $currentRights ?: '';
        $hasOrphan = (bool) $currentRights;

        foreach ($rightsTerms as $value) {
            if (self::normalizeRightsUrl($value) === self::normalizeRightsUrl($currentRights)) {
                $selected = $value;
                $hasOrphan = false;
                break;
            }
        }

        return [
            'selected' => $selected,
            'hasOrphan' => $hasOrphan,
        ];
    }

    //helper function for comparing rights URLs
    private static function normalizeRightsUrl(?string $url): string {
        $normalized = strtolower(trim((string) $url));
        $normalized = preg_replace('#^https?:#', '', $normalized);

        return rtrim((string) $normalized, '/');
    }

    // CHeck if the gbif is enabled
    private static function gbifPublishingEnabled(): bool {
        global $GBIF_USERNAME, $GBIF_PASSWORD, $GBIF_ORG_KEY;

        return (bool) ($GBIF_USERNAME && $GBIF_PASSWORD && $GBIF_ORG_KEY);
    }

    //helper function for preserving state
    private static function collmetadataUrl(?int $collid = null, int $tabIndex = 0): string {
        $baseUrl = $collid
            ? route('collections.collmetadata.edit', ['collid' => $collid])
            : route('collections.collmetadata.create');

        if ($tabIndex > 0) {
            return $baseUrl . '?' . http_build_query(['tabindex' => $tabIndex]);
        }

        return $baseUrl;
    }

    private static function redirectParams(?int $collid, string $status = '', ?string $statusType = null, int $tabIndex = 0, bool $withInput = false) {
        $redirect = redirect(self::collmetadataUrl($collid, $tabIndex));

        if ($withInput) {
            $redirect = $redirect->withInput();
        }

        if ($status !== '') {
            $redirect = $redirect
                ->with('status', $status)
                ->with('statusType', $statusType ?? 'error');
        }

        return $redirect;
    }
}
