<?php
namespace App\Core\Download;

class Determinations {
    use RowMap;

    static $casts = [
        'occid' => 'coreid',
        'sciname' => 'scientificName',
        'dateLastModified' => 'modified',
        'taxonRank' => 'verbatimTaxonRank',
    ];

    static $ignores = [];
    static $derived = [];

    static $fields = [
        'coreid' => null,
        'identifiedBy' => null,
        'dateIdentified' => null,
        'identificationQualifier' => null,
        'scientificName' => null,
        'scientificNameAuthorship' => null,
        'genus' => null,
        'specificEpithet' => null,
        'taxonRank' => null,
        'infraspecificEpithet' => null,
        'identificationReferences' => null,
        'identificationRemarks' => null,
        'recordID' => null,
        'modified' => null,
    ];
}
