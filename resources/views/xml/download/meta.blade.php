@props([
'fieldsTerminatedBy' => ',',
'linesTerminatedBy' => '\n',
'fieldsEnclosedBy' => '"',
'encoding'=> 'UTF-8',
'dateFormat' => "YYYY-MM-DD"
])

@php
use App\Core\Download\Associations;
use App\Core\Download\DarwinCore;
use App\Core\Download\Determinations;
use App\Core\Download\Multimedia;
use App\Core\Download\AttributeTraits;
use App\Core\Download\Identifiers;

$sections = [
    [
        'schema' => DarwinCore::class,
        'file' => 'occurrences.csv',
    ],
    [
        'schema' => Determinations::class,
        'file' => 'identifications.csv',
    ],
    [
        'schema' => Multimedia::class,
        'file' => 'multimedia.csv',
    ],
    [
        'schema' => AttributeTraits::class,
        'file' => 'measurementOrFact.csv',
    ],
    [
        'schema' => Identifiers::class,
        'file' => 'identifiers.csv',
    ]
];
@endphp

<?xml version="1.0" encoding="UTF-8"?>
<archive xmlns="http://rs.tdwg.org/dwc/text/" metadata="eml.xml" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://rs.tdwg.org/dwc/text/   http://rs.tdwg.org/dwc/text/tdwg_dwc_text.xsd">
    @foreach ($sections as $section)
        <{{ $section['schema']::$metaType }}
            dateFormat="{{ $dateFormat }}"
            encoding="{{ $encoding }}"
            fieldsTerminatedBy="{{ $fieldsTerminatedBy }}"
            linesTerminatedBy="{{ $linesTerminatedBy }}"
            fieldsEnclosedBy="{{ $fieldsEnclosedBy }}"
            ignoreHeaderLines="1"
            rowType="{{ $section['schema']::$metaRowType }}"
        >
            <files>
                <location>{{ $section['file'] }}</location>
            </files>
            @foreach ($section['schema']::$terms as $term => $base_url)
                @empty($base_url)
                <{{ $term }} index="{{ $loop->index }}" />
                @else
                <field index="{{ $loop->index }}" term="{{ $base_url . $term }}" />
                @endempty
            @endforeach
        </{{ $section['schema']::$metaType }}>
    @endforeach
</archive>
