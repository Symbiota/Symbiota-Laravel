<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TaxonomyPayloadNormalizer {
    public static function normalizeCreatePayload(array $postData): array {
        $normalized = $postData;

        $stringFields = [
            'author',
            'unitind1',
            'unitname1',
            'unitind2',
            'unitname2',
            'unitind3',
            'unitname3',
            'cultivarEpithet',
            'tradeName',
            'source',
            'notes',
            'parentname',
            'acceptedstr',
            'unacceptabilityreason',
        ];

        foreach ($stringFields as $field) {
            $normalized[$field] = trim((string) ($normalized[$field] ?? ''));
        }

        $normalized['acceptstatus'] = ((int) ($normalized['acceptstatus'] ?? 1) === 1) ? 1 : 0;
        $normalized['rankid'] = optionalInt($normalized['rankid'] ?? null) ?? 0;
        $normalized['securitystatus'] = optionalInt($normalized['securitystatus'] ?? null) ?? 0;

        $normalizedParentTid = optionalInt($normalized['parenttid'] ?? null);
        if ($normalizedParentTid === null && $normalized['parentname'] !== '') {
            $normalizedParentTid = DB::table('taxa')
                ->where('sciName', $normalized['parentname'])
                ->value('tid');
        }
        $normalized['parenttid'] = $normalizedParentTid;

        $normalizedTidAccepted = optionalInt($normalized['tidaccepted'] ?? null);
        if ($normalized['acceptstatus'] === 0 && $normalizedTidAccepted === null && $normalized['acceptedstr'] !== '') {
            $normalizedTidAccepted = DB::table('taxa')
                ->where('sciName', $normalized['acceptedstr'])
                ->value('tid');
        }
        $normalized['tidaccepted'] = $normalizedTidAccepted;

        return $normalized;
    }
}
