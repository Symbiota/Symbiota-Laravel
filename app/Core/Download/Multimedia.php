<?php

namespace App\Core\Download;

class Multimedia {
    use DeriveOccurrenceReference;
    use RowMap;

    public static $casts = [
        'occid' => 'coreid',
        'originalUrl' => 'identifier',
        //'originalUrl' => 'accessURI',
        'thumbnailUrl' => 'thumbnailAccessURI',
        'url' => 'goodQualityAccessURI',
        'recordID' => 'providerManagedID',
        'intialtimestamp' => 'metadataDate',
    ];

    public static $ignores = [];

    public static $derived = [
        'associatedSpecimenReference' => 'derive_references',
    ];

    public static $fields = [
        'coreid' => null,
        'identifier' => null,
        'accessURI' => null,
        'thumbnailAccessURI' => null,
        'goodQualityAccessURI' => null,
        'format' => null,
        'type' => null,
        'subtype' => null,
        'rights' => null,
        'owner' => null,
        'creator' => null,
        'usageTerms' => null,
        'webStatement' => null,
        'caption' => null,
        'providerManagedID' => null,
        'associatedSpecimenReference' => null,
        //TODO (Logan) figure out how to make this reflect record language
        'metadataLanguage' => 'en',
    ];

    public static $terms = [
        'coreid' => null,
        'identifier' => Terms::DUBLIN_CORE,
        'accessURI' => Terms::AUDIO_VISUAL_CORE,
        'thumbnailAccessURI' => Terms::AUDIO_VISUAL_CORE,
        'goodQualityAccessURI' => Terms::AUDIO_VISUAL_CORE,
        'format' => Terms::DUBLIN_CORE,
        'type' => Terms::DUBLIN_CORE,
        'subtype' => Terms::AUDIO_VISUAL_CORE,
        'rights' => Terms::DUBLIN_CORE,
        'owner' => Terms::ADOBE,
        'creator' => Terms::DUBLIN_CORE,
        'usageTerms' => Terms::ADOBE,
        'webStatement' => Terms::ADOBE,
        'caption' => Terms::AUDIO_VISUAL_CORE,
        'providerManagedID' => Terms::AUDIO_VISUAL_CORE,
        'associatedSpecimenReference' => Terms::AUDIO_VISUAL_CORE,
        'metadataLanguage' => Terms::AUDIO_VISUAL_CORE,
    ];
}
