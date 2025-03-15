<?php
namespace App\Core\Download;

class Identifers {
    use RowMap;

    static $casts = [
        'occid' => 'coreid',
        'identifierValue' => 'identifier',
        'identifierName' => 'title',
    ];

    static $ignores = [];
    static $derived = [];

    static $fields = [
        'coreid' => null,
        'identifier' => null,
        'title' => null,
        'format' => null,
        'recordID' => null,
        'format' => null,
        'initialTimestamp' => null,
    ];
}
