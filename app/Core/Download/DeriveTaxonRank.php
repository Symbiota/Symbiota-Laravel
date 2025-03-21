<?php

namespace App\Core\Download;

trait DeriveTaxonRank {
    private static function derive_taxon_rank($row) {
        //TODO (Logan) get better logic for this
        if (array_key_exists('infraspecificEpithet', $row) && $row['infraspecificEpithet']) {
            return 'Subspecies';
        } elseif (array_key_exists('specificEpithet', $row) && $row['specificEpithet']) {
            return 'Species';
        } elseif (array_key_exists('genus', $row) && $row['genus']) {
            return 'Genus';
        }
    }
}
