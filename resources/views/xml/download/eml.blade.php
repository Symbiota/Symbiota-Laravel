@props([
    'encoding' => 'UTF-8',
    'identifier' => uniqid(),
    'lang' => 'eng',
    'collections' => App\Models\Collection::query()->select('*')->get(),
])
@php
$collection = null;
if(!empty($collections) && count($collections) === 1) {
    $collection = $collections[0];
}
@endphp
<?xml version="1.0" encoding="{{ $encoding }}"?>
<eml:eml xmlns:eml="eml://ecoinformatics.org/eml-2.1.1" xmlns:dc="http://purl.org/dc/terms/"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="eml://ecoinformatics.org/eml-2.1.1 http://rs.gbif.org/schema/eml-gbif-profile/1.0.1/eml.xsd"
    packageId="{{ uniqid() }}" system="https://symbiota.org" scope="system" xml:lang="{{ $lang }}">
    <dataset>
@php
@endphp
        @if(!empty($collection))
        <alternateIdentifier>
            {{ url('collections/' . $collection->collID) }}
        </alternateIdentifier>
        @endif
        <title xml:lang="{{ $lang }}">
            @if(!empty($collection))
                {{ $collection->collectionName }}
            @else
                {{ config('app.name') . ' general data extract' }}
            @endif
        </title>
        <creator>
            <organizationName>{{ config('app.name') }}</organizationName>
            <electronicMailAddress></electronicMailAddress>
            <onlineUrl>{{ url('') }}</onlineUrl>
        </creator>
        <metadataProvider>
            <organizationName>{{ config('app.name') }}</organizationName>
            <electronicMailAddress></electronicMailAddress>
            <onlineUrl>{{ url('') }}</onlineUrl>
        </metadataProvider>
        <pubDate>{{ date('Y-m-d') }}</pubDate>
        <language>{{ $lang }}</language>
        @if(!empty($collection))
        <abstract>
            <para>{{ $collection->fullDescription ?? ''}}</para>
        </abstract>
        @endif
        @if(!empty($collection))
        <contact>
            <organizationName>{{ $collection->collectionName }}</organizationName>
            <phone></phone>
            <electronicMailAddress>{{ $collection->email ?? '' }}</electronicMailAddress>
            <onlineUrl>{{ $collection->homepage ?? '' }}</onlineUrl>
        </contact>
        @endif
        @if(!empty($collection))
        <associatedParty>
            <individualName>
                <surName>{{ $collection->Contact ?? ''}}</surName>
                <givenName></givenName>
            </individualName>
            <electronicMailAddress>{{ $collection->email ?? '' }}</electronicMailAddress>
            <role>contentProvider</role>
        </associatedParty>
        @elseif (Auth::check())
            @php
                $user = Auth::user();

                $firstName = $user->firstName ?? '';
                $lastName = $user->lastName ?? '';

                if((!$firstName || !$lastName) && !empty($user->name)) {
                    $name_parts = explode(' ', $user->name);
                    if(count($name_parts) > 0) {
                        $firstName = $name_parts[0];
                    }
                    if(count($name_parts) == 2) {
                        $lastName = $name_parts[1];
                    } else if (count($name_parts == 3)) {
                        $lastName = $name_parts[2];
                    }
                }
            @endphp
            @if ($firstName && $lastName)
            <associatedParty>
                <individualName>
                    <surName>{{ $firstName }}</surName>
                    <givenName>{{ $lastName }}</givenName>
                </individualName>
                <electronicMailAddress>{{ $user->email ?? '' }}</electronicMailAddress>
                <role>datasetOriginator</role>
            </associatedParty>
            @endif
        @endif
        <intellectualRights>
            <para>To the extent possible under law, the publisher has waived all rights to these data and has dedicated
                them to the <ulink url="http://creativecommons.org/licenses/by-nc/4.0/">
                    <citetitle></citetitle>
                </ulink>
            </para>
        </intellectualRights>
    </dataset>
    <additionalMetadata>
        <metadata>
            <symbiota id="">
                <dateStamp>2025-03-20T16:40:48-07:00</dateStamp>
                <citation identifier="{{ $identifier }}">
                    {{ config('app.name') }}-{{ $identifier}}
                </citation>
                <physical>
                    <characterEncoding>{{ $encoding }}</characterEncoding>
                    <dataFormat>
                        <externallyDefinedFormat>
                            <formatName>Darwin Core Archive</formatName>
                        </externallyDefinedFormat>
                    </dataFormat>
                </physical>
                @foreach ($collections as $collection)
                <collection identifier="{{ $collection->collectionGuid ?? '' }}" id="{{ $collection->collID }}">
                    <alternateIdentifier>
                        {{ url('collections/' . $collection->collID) }}
                    </alternateIdentifier>
                    <parentCollectionIdentifier>{{ $collection->institutionCode }}</parentCollectionIdentifier>
                    <collectionIdentifier></collectionIdentifier>
                    <collectionName>{{ $collection->collectionName }}</collectionName>
                    <resourceLogoUrl>{{ $collection->icon ?? ''}}</resourceLogoUrl>
                    <onlineUrl>{{ $collection->homepage ?? ''}}</onlineUrl>
                    <intellectualRights>{{ $collection->rights ?? ''}}</intellectualRights>
                    @php
                        $contacts = [];
                        try {
                            $contacts = json_decode($collection->contactJson, true);
                        } catch(Throwable $th) {
                            error_log('ERROR: Parsing contact for eml download for collid (' . $collection->collID . ') : ' . $th->getMessage());
                        }
                    @endphp
                    @if(!empty($contacts) && count($contacts) > 0)
                    @foreach ($contacts as $contact)
                    <associatedParty>
                        <individualName>
                            @if(empty($contact['firstName']))
                                <surName>{{ $contact['lastName'] }}</surName>
                                <givenName></givenName>
                            @elseif(empty($contact['lastName']))
                                <surName>{{ $contact['firstName'] }}</surName>
                                <givenName></givenName>
                            @else
                                <surName>{{ $contact['firstName'] }}</surName>
                                <givenName>{{ $contact['lastName'] }}</givenName>
                            @endif
                        </individualName>
                        <electronicMailAddress>{{ $contact['email'] ?? ''}}</electronicMailAddress>
                    </associatedParty>
                    @endforeach
                    @endif
                    <abstract>
                        <para>{{ $collection->fullDescription ?? '' }}</para>
                    </abstract>
                </collection>
                @endforeach
            </symbiota>
        </metadata>
    </additionalMetadata>
</eml:eml>
