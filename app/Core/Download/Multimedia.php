<?php
namespace App\Core\Download;

class Multimedia {
    use RowMap;
    use DeriveOccurrenceReference;

    static $casts = [
        'mediaID' => 'coreid',
        'originalUrl' => 'identifier',
        //'originalUrl' => 'accessURI',
        'thumbnailUrl' => 'thumbnailAccessURI',
        'url' => 'goodQualityAccessURI',
        'recordID' => 'providerManagedID',
        'intialtimestamp' => 'metadataDate',
    ];

    static $ignores = [];
    static $derived = [
        'associatedSpecimenReference' => 'derive_references'
    ];

    static $fields = [
        'coreid' => null,
        'identifier' => null,
        'accessURI' => null,
        'thumbnailAccessURI' => null,
        'url' => null,
        'format' => null,
        'type' => null,
        'subtype' => null,
        'rights' => null,
        'owner' => null,
        'creator' => null,
        'webStatement' => null,
        'caption' => null,
        'providerManagedID' => null,
        //TODO (Logan) Derived value with server knowlege
        'associatedSpecimenReference' => null,
        //TODO (Logan) figure out how to make this reflect record language
        'metadataLanguage' => "en",
    ];
}
