<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Occurrence;

class DownloadController extends Controller {
    const SYMBIOTA_NATIVE = [
        'occid' => 'id',
        'institutionCode',
        'collectionCode',
        'ownerInstitutionCode',
        'basisOfRecord',
        'occurrenceID',
        'catalogNumber',
        'otherCatalogNumbers',
        //'higherClassification',
        //'kingdom',
        //'phylum',
        //'class',
        //'order',
        'family',
        'scientificName',
        'tidInterpreted' => 'taxonID',
        'scientificNameAuthorship',
        'genus',
        //'subgenus',
        'specificEpithet',
        //'verbatimTaxonRank',
        'infraspecificEpithet',
        //'cultivarEpithet',
        //'tradeName',
        'taxonRank',
        'identifiedBy',
        'dateIdentified',
        'identificationReferences',
        'identificationRemarks',
        'taxonRemarks',
        'identificationQualifier',
        'typeStatus',
        'recordedBy',
        'associatedCollectors',
        'recordNumber',
        'eventDate',
        'eventDate2',
        'year',
        'month',
        'day',
        'startDayOfYear',
        'endDayOfYear',
        'verbatimEventDate',
        'occurrenceRemarks',
        'habitat',
        'substrate',
        'verbatimAttributes',
        'behavior',
        'vitality',
        'fieldNumber',
        'eventID',
        'informationWithheld',
        'dataGeneralizations',
        'dynamicProperties',
        'associatedOccurrences',
        //'associatedSequences',
        'associatedTaxa',
        'reproductiveCondition',
        'establishmentMeans',
        'cultivationStatus',
        'lifeStage',
        'sex',
        'individualCount',
        'samplingProtocol',
        'preparations',
        'locationID',
        'continent',
        'waterBody',
        'islandGroup',
        'island',
        'country',
        'countryCode',
        'stateProvince',
        'county',
        'municipality',
        'locality',
        'locationRemarks',
        'localitySecurity',
        'localitySecurityReason',
        'decimalLatitude',
        'decimalLongitude',
        'geodeticDatum',
        'coordinateUncertaintyInMeters',
        'verbatimCoordinates',
        'georeferencedBy',
        'georeferenceProtocol',
        'georeferenceSources',
        'georeferenceVerificationStatus',
        'georeferenceRemarks',
        'minimumElevationInMeters',
        'maximumElevationInMeters',
        'minimumDepthInMeters',
        'maximumDepthInMeters',
        'verbatimDepth',
        'verbatimElevation',
        'disposition',
        'language',
        'recordEnteredBy',
        'modified',
        //'sourcePrimaryKey',
        'dbpk',
        'collid' => 'collID',
        'recordID',
        //'references',
    ];

    const DARWIN_CORE = [

    ];

    private static function map_alias_keys($keymap) {
        $select_array = [];
        foreach($keymap as $key => $map) {
            if(!is_numeric($key)) {
                array_push($select_array, 'o.' . $key . ' as ' . $map);
            } else {
                array_push($select_array, 'o.' . $map);
            }
        }

        return $select_array;
    }

    private static function get_schema_headers($keymap) {
        $header_arr = [];
        foreach($keymap as $key => $map) {
            if(!is_numeric($key)) {
                array_push($header_arr, $map);
            } else {
                array_push($header_arr, $map);
            }
        }

        return $header_arr;
    }

    public static function downloadPage(Request $request) {
        $params = $request->except(['page', '_token']);

        return view('pages/collections/download');
    }

    public static function downloadFile(Request $request) {
        $params = $request->except(['page', '_token']);

        if (empty($params)) {
            return [];
        }

        $query = Occurrence::buildSelectQuery($request->all());
        $csvFileName = 'symbiota_download.csv';

        $schema = self::map_alias_keys(self::SYMBIOTA_NATIVE);
        $headers = self::get_schema_headers(self::SYMBIOTA_NATIVE);

        $csvFile = fopen($csvFileName, 'w');
        fputcsv($csvFile, $headers);

        $query->select($schema)->orderBy('o.occid')->chunk(100, function (\Illuminate\Support\Collection $occurrences) use ($csvFile) {
            foreach ($occurrences as $occurrence) {
                fputcsv($csvFile, (array) $occurrence);
            }
        });
        fclose($csvFile);

        return response()->download(public_path($csvFileName))->deleteFileAfterSend(true);
    }
}
