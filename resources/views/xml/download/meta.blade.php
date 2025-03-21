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
@endphp
<?xml version="1.0" encoding="UTF-8"?>
<archive xmlns="http://rs.tdwg.org/dwc/text/" metadata="eml.xml" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://rs.tdwg.org/dwc/text/   http://rs.tdwg.org/dwc/text/tdwg_dwc_text.xsd">
    <core dateFormat="YYYY-MM-DD" encoding="UTF-8" fieldsTerminatedBy="{{ $fieldsTerminatedBy }}" linesTerminatedBy="{{ $linesTerminatedBy }}"
        fieldsEnclosedBy="{{ $fieldsEnclosedBy }}" ignoreHeaderLines="1" rowType="http://rs.tdwg.org/dwc/terms/Occurrence">
        <files>
            <location>occurrences.csv</location>
        </files>
        <id index="0" />
        @foreach (DarwinCore::$terms as $term => $base_url)
            @empty($base_url)
            <{{ $term }} index="{{ $loop->index }}" />
            @else
            <field index="{{ $loop->index }}" term="{{ $base_url . $term }}" />
            @endempty
        @endforeach
    </core>
    <extension encoding="UTF-8" fieldsTerminatedBy="," linesTerminatedBy="\n" fieldsEnclosedBy="&quot;"
        ignoreHeaderLines="1" rowType="http://rs.tdwg.org/dwc/terms/Identification">
        <files>
            <location>identifications.csv</location>
        </files>
        @foreach (Determinations::$terms as $term => $base_url)
            @empty($base_url)
            <{{ $term }} index="{{ $loop->index }}" />
            @else
            <field index="{{ $loop->index }}" term="{{ $base_url . $term }}" />
            @endempty
        @endforeach
    </extension>
    <extension encoding="UTF-8" fieldsTerminatedBy="," linesTerminatedBy="\n" fieldsEnclosedBy="&quot;"
        ignoreHeaderLines="1" rowType="http://rs.tdwg.org/ac/terms/Multimedia">
        <files>
            <location>multimedia.csv</location>
        </files>
        @foreach (Multimedia::$terms as $term => $base_url)
            @empty($base_url)
            <{{ $term }} index="{{ $loop->index }}" />
            @else
            <field index="{{ $loop->index }}" term="{{ $base_url . $term }}" />
            @endempty
        @endforeach
    </extension>
    <extension encoding="UTF-8" fieldsTerminatedBy="," linesTerminatedBy="\n" fieldsEnclosedBy="&quot;"
        ignoreHeaderLines="1" rowType="http://rs.iobis.org/obis/terms/ExtendedMeasurementOrFact">
        <files>
            <location>measurementOrFact.csv</location>
        </files>
        @foreach (AttributeTraits::$terms as $term => $base_url)
            @empty($base_url)
            <{{ $term }} index="{{ $loop->index }}" />
            @else
            <field index="{{ $loop->index }}" term="{{ $base_url . $term }}" />
            @endempty
        @endforeach
    </extension>
    <extension encoding="UTF-8" fieldsTerminatedBy="," linesTerminatedBy="\n" fieldsEnclosedBy="&quot;"
        ignoreHeaderLines="1" rowType="http://rs.gbif.org/terms/1.0/Identifier">
        <files>
            <location>identifiers.csv</location>
        </files>

        @foreach (Identifiers::$terms as $term => $base_url)
            @empty($base_url)
            <{{ $term }} index="{{ $loop->index }}" />
            @else
            <field index="{{ $loop->index }}" term="{{ $base_url . $term }}" />
            @endempty
        @endforeach
    </extension>
</archive>
