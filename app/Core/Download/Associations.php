<?php

namespace App\Core\Download;

class Associations {
    use RowMap;

    public static $metaType = 'extension';

    public static $metaRowType = '';

    public static $casts = [];

    public static $ignores = [];

    public static $derived = [];

    public static $fields = [
        'resourceRelationshipID' => null,
        'resourceID' => null,
        'relationshipOfResourceID' => null,
        'relatedResourceID' => null,
        'relationshipOfResource' => null,
        'relationshipAccordingTo' => null,
        'relationshipEstablishedDate' => null,
        'relationshipRemarks' => null,
        'scientificName' => null,
        'associd' => null,
        'associationType' => null,
        'subType' => null,
        'objectID' => null,
        'identifier' => null,
        'basisOfRecord' => null,
        'tid' => null,
        'locationOnHost' => null,
        'conditionOfAssociate' => null,
        'imageMapJSON' => null,
        'dynamicProperties' => null,
        'sourceIdentifier' => null,
        'recordID' => null,
        'createdUid' => null,
        'modifiedTimestamp' => null,
        'modifiedUid' => null,
        'initialtimestamp' => null,
    ];

    public static $terms = [
        'resourceRelationshipID' => Terms::DARWIN_CORE,
        'resourceID' => Terms::DARWIN_CORE,
        'relationshipOfResourceID' => Terms::DARWIN_CORE,
        'relatedResourceID' => Terms::DARWIN_CORE,
        'relationshipOfResource' => Terms::DARWIN_CORE,
        'relationshipAccordingTo' => Terms::DARWIN_CORE,
        'relationshipEstablishedDate' => Terms::DARWIN_CORE,
        'relationshipRemarks' => Terms::DARWIN_CORE,
        'scientificName' => Terms::SYMBIOTA,
        'associd' => Terms::SYMBIOTA,
        'associationType' => Terms::SYMBIOTA,
        'subType' => Terms::SYMBIOTA,
        'objectID' => Terms::SYMBIOTA,
        'identifier' => Terms::SYMBIOTA,
        'basisOfRecord' => Terms::SYMBIOTA,
        'tid' => Terms::SYMBIOTA,
        'locationOnHost' => Terms::SYMBIOTA,
        'conditionOfAssociate' => Terms::SYMBIOTA,
        'imageMapJSON' => Terms::SYMBIOTA,
        'dynamicProperties' => Terms::SYMBIOTA,
        'sourceIdentifier' => Terms::SYMBIOTA,
        'recordID' => Terms::SYMBIOTA,
        'createdUid' => Terms::SYMBIOTA,
        'modifiedTimestamp' => Terms::SYMBIOTA,
        'modifiedUid' => Terms::SYMBIOTA,
        'initialtimestamp' => Terms::SYMBIOTA,
    ];
}
