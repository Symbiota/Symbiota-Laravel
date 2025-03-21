<?php

namespace App\Core\Download;

trait DeriveOccurrenceReference {
    private static function derive_references($row) {
        if (array_key_exists('occid', $row)) {
            $path = '/occurrence/' . $row['occid'];
            try {
                return url($path);
            } catch (\Throwable $th) {
                return $path;
            }
        } else {
            return;
        }
    }
}
