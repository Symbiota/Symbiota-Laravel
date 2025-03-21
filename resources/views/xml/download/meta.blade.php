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
        'terms' => DarwinCore::$terms,
        'file' => 'occurrences.csv',
        'rowtype' => 'http://rs.tdwg.org/dwc/terms/Occurrence',
        'type' => 'core',
    ],
    [
        'terms' => Determinations::$terms,
        'file' => 'identifications.csv',
        'rowtype' => 'http://rs.tdwg.org/dwc/terms/Identification',
        'type' => 'extension',
    ],
    [
        'terms' => Multimedia::$terms,
        'file' => 'multimedia.csv',
        'rowtype' => 'http://rs.tdwg.org/ac/terms/Multimedia',
        'type' => 'extension',
    ],
    [
        'terms' => AttributeTraits::$terms,
        'file' => 'measurementOrFact.csv',
        'rowtype' => 'http://rs.iobis.org/obis/terms/ExtendedMeasurementOrFact',
        'type' => 'extension',
    ],
    [
        'terms' => Identifiers::$terms,
        'file' => 'identifiers.csv',
        'rowtype' => 'http://rs.gbif.org/terms/1.0/Identifier',
        'type' => 'extension',
    ]
];
@endphp

<?xml version="1.0" encoding="UTF-8"?>
<archive xmlns="http://rs.tdwg.org/dwc/text/" metadata="eml.xml" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://rs.tdwg.org/dwc/text/   http://rs.tdwg.org/dwc/text/tdwg_dwc_text.xsd">
    @foreach ($sections as $section)
        <{{ $section['type'] }}
            dateFormat="{{ $dateFormat }}"
            encoding="{{ $encoding }}"
            fieldsTerminatedBy="{{ $fieldsTerminatedBy }}"
            linesTerminatedBy="{{ $linesTerminatedBy }}"
            fieldsEnclosedBy="{{ $fieldsEnclosedBy }}"
            ignoreHeaderLines="1"
            rowType="{{$section['rowtype']}}"
        >
            <files>
                <location>{{ $section['file'] }}</location>
            </files>
            @foreach (DarwinCore::$terms as $term => $base_url)
                @empty($base_url)
                <{{ $term }} index="{{ $loop->index }}" />
                @else
                <field index="{{ $loop->index }}" term="{{ $base_url . $term }}" />
                @endempty
            @endforeach
        </{{ $section['type'] }}>
    @endforeach
</archive>
