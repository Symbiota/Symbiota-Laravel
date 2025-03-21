<?php

namespace App\Core\Download;

trait DeriveCombineOccurrenceRecordID {
    private static function derive_combine_occurrence_record_id($row) {
        if (array_key_exists('recordID', $row) && array_key_exists('occurrenceID', $row)) {
            $hasRecordID = $row['recordID'];
            $hasOccurrenceID = $row['occurrenceID'];

            if (! $hasRecordID && $hasOccurrenceID) {
                return $row['occurrenceID'];
            } elseif ($hasRecordID && ! $hasOccurrenceID) {
                return $row['recordID'];
            }
        }
    }
}
