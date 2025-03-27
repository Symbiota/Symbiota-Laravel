<?php

namespace App\Core\Download;

class Determinations {
    use RowMap;

    public static $metaType = 'extension';

    public static $metaRowType = 'http://rs.tdwg.org/dwc/terms/Identification';

    public static $casts = [
        'occid' => 'coreid',
        'sciname' => 'scientificName',
        'dateLastModified' => 'modified',
        'taxonRank' => 'verbatimTaxonRank',
    ];

    public static $ignores = [];

    public static $derived = [];

    public static $fields = [
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

    public static $terms = [
        'coreid' => null,
        'identifiedBy' => Terms::DARWIN_CORE,
        'dateIdentified' => Terms::DARWIN_CORE,
        'identificationQualifier' => Terms::DARWIN_CORE,
        'scientificName' => Terms::DARWIN_CORE,
        'scientificNameAuthorship' => Terms::DARWIN_CORE,
        'genus' => Terms::DARWIN_CORE,
        'specificEpithet' => Terms::DARWIN_CORE,
        'taxonRank' => Terms::DARWIN_CORE,
        'infraspecificEpithet' => Terms::DARWIN_CORE,
        'identificationReferences' => Terms::DARWIN_CORE,
        'identificationRemarks' => Terms::DARWIN_CORE,
        'recordID' => Terms::IDIGBIO,
        'modified' => Terms::DUBLIN_CORE,
    ];
}
