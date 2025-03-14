<?php
namespace App\Core\Download;

trait RowMap {
    public static function map_row($unmapped_row) {
        $row = self::$fields;
        foreach($unmapped_row as $key => $value) {
            if(array_key_exists($key, self::$ignores)) continue;

            // Map Casted Values
            if(array_key_exists($key, self::$casts)) {
                if(array_key_exists(self::$casts[$key], $row)) {
                    $row[self::$casts[$key]] = $value;
                }
            }
            // Map DB Values
            else if(array_key_exists($key, $row)) {
                $row[$key] = $value;
            }

            // Generate Row Dervied Values
            foreach(self::$derived as $key => $fn) {
                if(array_key_exists($key, $row) && !$row[$key]) {
                    $row[$key] = self::callDerived($key, $unmapped_row);
                }
            }
        }

        return $row;
    }

    public static function callDerived($key, $arg) {
        if(array_key_exists($key, self::$derived)) {
            return forward_static_call(array(self::class, self::$derived[$key]), $arg);
        }
    }
}

trait DeriveOccurrenceReference {
    private static function derive_references($row) {
        if(array_key_exists('occid', $row)) {
            return url('occurrence/' . $row['occid']);
        } else {
            return null;
        }
    }
}

trait DeriveCombineOccurrenceRecordID {
    private static function derive_combine_occurrence_record_id($row) {
        if(array_key_exists('recordID', $row) && array_key_exists('occurrenceID', $row)) {
            $hasRecordID = $row['recordID'];
            $hasOccurrenceID = $row['occurrenceID'];

            if(!$hasRecordID && $hasOccurrenceID) {
                return $row['occurrenceID'];
            } else if($hasRecordID && !$hasOccurrenceID) {
                return $row['recordID'];
            }
        }
    }
}

trait DeriveTaxonRank{
    private static function derive_taxon_rank($row) {
        //TODO (Logan) get better logic for this
        if(array_key_exists('infraspecificEpithet', $row) && $row['infraspecificEpithet']) {
            return 'Subspecies';
        } else if (array_key_exists('specificEpithet', $row) && $row['specificEpithet']) {
            return 'Species';
        } else if (array_key_exists('genus', $row) && $row['genus']) {
            return 'Genus';
        }
    }
}
