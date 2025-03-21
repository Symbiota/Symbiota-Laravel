<?php

namespace App\Core\Download;

class Identifiers {
    use RowMap;

    public static $casts = [
        'occid' => 'coreid',
        'identifierValue' => 'identifier',
        'identifierName' => 'title',
    ];

    public static $ignores = [];

    public static $derived = [];

    public static $fields = [
        'coreid' => null,
        'identifier' => null,
        'title' => null,
        'format' => null,
        'recordID' => null,
        'initialTimestamp' => null,
    ];

    public static $terms = [
        'coreid' => null,
        'identifier' => Terms::DUBLIN_CORE,
        'title' => Terms::DUBLIN_CORE,
        'format' => Terms::DUBLIN_CORE,
        'recordID' => Terms::SYMBIOTA,
        'initialTimestamp' => Terms::SYMBIOTA,
    ];
}
