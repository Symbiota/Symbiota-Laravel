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

        $normalized['acceptstatus'] = intval($normalized['acceptstatus'] ?? 0);
        $normalized['rankid'] = intval($normalized['rankid'] ?? 0);
        $normalized['securitystatus'] = intval($normalized['securitystatus'] ?? 0);

        if($parentTid = intval($normalized['parenttid'] ?? 0)) {
            $normalized['parenttid'] = $parentTid;
        } else {
            $normalized['parenttid'] = DB::table('taxa')
                ->where('sciName', $normalized['parentname'])
                ->value('tid');
        }

        $normalizedTidAccepted = intval($normalized['tidaccepted'] ?? 0);
        if ($normalized['acceptstatus'] === 0 && $normalizedTidAccepted === null && $normalized['acceptedstr'] !== '') {
            $normalizedTidAccepted = DB::table('taxa')
                ->where('sciName', $normalized['acceptedstr'])
                ->value('tid');
        }
        $normalized['tidaccepted'] = $normalizedTidAccepted;

        return $normalized;
    }
}
