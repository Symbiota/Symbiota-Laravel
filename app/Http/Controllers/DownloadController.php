<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Occurrence;
use App\Models\Collection;
use Illuminate\Support\Facades\DB;

class DownloadController extends Controller {
    public static function getHigherClassification($tid) {
        $taxa_info = DB::select("
            select t.*, flat_enum_tree.taxa_enums from taxa as t INNER JOIN taxstatus ts ON t.tid = ts.tid
            JOIN (
                SELECT e.tid ,group_concat(CONCAT(t.sciName, ':', t.rankid) ORDER BY t.rankid) as taxa_enums
                    FROM taxaenumtree e
                    INNER JOIN taxa t ON e.parentTid = t.tid
                    WHERE e.taxauthid = 1
                    AND e.tid in (?)
                group by e.tid
            ) as flat_enum_tree on flat_enum_tree.tid = t.tid
            where t.tid in (?) and ts.taxauthid = 1
            ", [$tid, $tid]);

        $result = [];

        foreach ($taxa_info as $key => $value) {
            $ident_tree = [];
            foreach (explode(',', $value->taxa_enums) as $idx => $ident) {
                [$rankName, $rankID] = explode(':', $ident);

                $ident_tree[] = $rankName;

                $order_name = match(intval($rankID)) {
                    10 => 'kingdom',
                    30 => 'phylum',
                    60 => 'class',
                    100 => 'order',
                    140 => 'family',
                    190 => 'subgenus',
                    default => false,
                };

                if($order_name) {
                    $result[$value->tid][$order_name] = $rankName;
                }
            }

            $result[$value->tid]['higherClassification'] = implode("|", $ident_tree);
            //$result[$value->tid]['unitInd3'] = $value->unitInd3;
            $result[$value->tid]['cultivarEpithet'] = $value->cultivarEpithet;
            $result[$value->tid]['tradeName'] = $value->tradeName;
        }

        return $result;
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
        //$SCHEMA = SymbiotaNative::class;
        $SCHEMA = DarwinCore::class;

        if(array_key_exists('associatedSequences', $SCHEMA::$fields)) {
            $geneticsQuery = DB::table('omoccurgenetic')->selectRaw(
                "occid as gen_occid, group_concat(CONCAT_WS(', ', resourcename, title, identifier, locus, resourceUrl) SEPARATOR ' | ') as associatedSequences"
            )->groupBy('occid');
            $query->leftJoinSub($geneticsQuery, 'gen', 'gen.gen_occid', 'o.occid');
        }

        $csvFileName = 'symbiota_download.csv';

        $results = [];
        $taxa = [];

        $OUTPUT_CSV = false;

        $csvFile = null;
        if($OUTPUT_CSV) {
            $csvFile = fopen($csvFileName, 'w');
            fputcsv($csvFile, array_keys($SCHEMA::$fields));
        }

        //This order matters when dealing with conflicting attribute names
        $query->select(['c.*', 'gen.*', 'o.*'])->orderBy('o.occid')->chunk(100, function (\Illuminate\Support\Collection $occurrences) use ($csvFile, &$taxa, &$results, $OUTPUT_CSV, $SCHEMA) {
            foreach ($occurrences as $occurrence) {
                $row = $SCHEMA::$fields;

                if($occurrence->tidInterpreted) {
                    if(!array_key_exists($occurrence->tidInterpreted, $taxa)) {
                        $taxa[$occurrence->tidInterpreted] = self::getHigherClassification($occurrence->tidInterpreted)[$occurrence->tidInterpreted];
                    }
                }

                $unmapped_row = array_merge(
                    (array) $occurrence,
                    $occurrence->tidInterpreted && array_key_exists($occurrence->tidInterpreted, $taxa)? $taxa[$occurrence->tidInterpreted]: []
                );
                foreach($unmapped_row as $key => $value) {
                    if(array_key_exists($key, $SCHEMA::$ignores)) continue;

                    // Map Casted Values
                    if(array_key_exists($key, $SCHEMA::$casts)) {
                        if(array_key_exists($SCHEMA::$casts[$key], $row)) {
                            $row[$SCHEMA::$casts[$key]] = $value;
                        }
                    }
                    // Map DB Values
                    else if(array_key_exists($key, $row)) {
                        $row[$key] = $value;
                    }

                    // Generate Row Dervied Values
                    foreach($SCHEMA::$derived as $key => $fn) {
                        if(array_key_exists($key, $row) && !$row[$key]) {
                            $row[$key] = $SCHEMA::callDerived($key, $unmapped_row);
                        }
                    }
                }

                if($OUTPUT_CSV) {
                    fputcsv($csvFile, (array) $row);
                } else {
                    array_push($results, $row);
                }
            }
        });

        if($OUTPUT_CSV) {
            fclose($csvFile);
            return response()->download(public_path($csvFileName))->deleteFileAfterSend(true);
        } else {
            return $results;
        }
    }
}

trait DeriveOccurrenceReference {
    private static function derive_references($row) {
        if(array_key_exists('occid', $row)) {
            return url('occurrence/' . $row['occid']);
        } else {
            return null;
        }
    }
}

trait DeriveCombineOccurrenceRecordID {
    private static function derive_combine_occurrence_record_id($row) {
        if(array_key_exists('recordID', $row) && array_key_exists('occurrenceID', $row)) {
            $hasRecordID = $row['recordID'];
            $hasOccurrenceID = $row['occurrenceID'];

            if(!$hasRecordID && $hasOccurrenceID) {
                return $row['occurrenceID'];
            } else if($hasRecordID && !$hasOccurrenceID) {
                return $row['recordID'];
            }
        }
    }
}

trait DeriveTaxonRank{
    private static function derive_taxon_rank($row) {
        //TODO (Logan) get better logic for this
        if(array_key_exists('infraspecificEpithet', $row) && $row['infraspecificEpithet']) {
            return 'Subspecies';
        } else if (array_key_exists('specificEpithet', $row) && $row['specificEpithet']) {
            return 'Species';
        } else if (array_key_exists('genus', $row) && $row['genus']) {
            return 'Genus';
        }
    }
}

trait CalledDerived {
    public static function callDerived($key, $arg) {
        if(array_key_exists($key, self::$derived)) {
            return forward_static_call(array(self::class, self::$derived[$key]), $arg);
        }
    }
}

class SymbiotaNative {
    use DeriveOccurrenceReference;
    use DeriveCombineOccurrenceRecordID;
    use CalledDerived;

    static $select = [''];

    static $casts = [
        'occid' => 'id',
        'tidInterpreted' => 'taxonID',
        'taxonRank' => 'verbatimTaxonRank',
        'sourcePrimaryKey-dbpk' => 'dbpk',
        'dateLastModified' => 'modified'
    ];

    static $ignores = [];

    static $fields = [
        'id' => '',
        'institutionCode' => '',
        'collectionCode' => '',
        'ownerInstitutionCode' => '',
        'basisOfRecord' => '',
        'occurrenceID' => '',
        'catalogNumber' => '',
        'otherCatalogNumbers' => '',
        'higherClassification' => '',
        'kingdom' => '',
        'phylum' => '',
        'class' => '',
        'order' => '',
        'subgenus' => '',
        'family' => '',
        'scientificName' => '',
        'taxonID' => '',
        'scientificNameAuthorship' => '',
        'genus' => '',
        'specificEpithet' => '',
        'verbatimTaxonRank' => '',
        'infraspecificEpithet' => '',
        'cultivarEpithet' => '',
        'tradeName' => '',
        'taxonRank' => '',
        'identifiedBy' => '',
        'dateIdentified' => '',
        'identificationReferences' => '',
        'identificationRemarks' => '',
        'taxonRemarks' => '',
        'identificationQualifier' => '',
        'typeStatus' => '',
        'recordedBy' => '',
        'associatedCollectors' => '',
        'recordNumber' => '',
        'eventDate' => '',
        'eventDate2' => '',
        'year' => '',
        'month' => '',
        'day' => '',
        'startDayOfYear' => '',
        'endDayOfYear' => '',
        'verbatimEventDate' => '',
        'occurrenceRemarks' => '',
        'habitat' => '',
        'substrate' => '',
        'verbatimAttributes' => '',
        'behavior' => '',
        'vitality' => '',
        'fieldNumber' => '',
        'eventID' => '',
        'informationWithheld' => '',
        'dataGeneralizations' => '',
        'dynamicProperties' => '',
        'associatedOccurrences' => '',
        'associatedSequences' => '',
        'associatedTaxa' => '',
        'reproductiveCondition' => '',
        'establishmentMeans' => '',
        'cultivationStatus' => '',
        'lifeStage' => '',
        'sex' => '',
        'individualCount' => '',
        'samplingProtocol' => '',
        'preparations' => '',
        'locationID' => '',
        'continent' => '',
        'waterBody' => '',
        'islandGroup' => '',
        'island' => '',
        'country' => '',
        'countryCode' => '',
        'stateProvince' => '',
        'county' => '',
        'municipality' => '',
        'locality' => '',
        'locationRemarks' => '',
        'localitySecurity' => '',
        'localitySecurityReason' => '',
        'decimalLatitude' => '',
        'decimalLongitude' => '',
        'geodeticDatum' => '',
        'coordinateUncertaintyInMeters' => '',
        'verbatimCoordinates' => '',
        'georeferencedBy' => '',
        'georeferenceProtocol' => '',
        'georeferenceSources' => '',
        'georeferenceVerificationStatus' => '',
        'georeferenceRemarks' => '',
        'minimumElevationInMeters' => '',
        'maximumElevationInMeters' => '',
        'minimumDepthInMeters' => '',
        'maximumDepthInMeters' => '',
        'verbatimDepth' => '',
        'verbatimElevation' => '',
        'disposition' => '',
        'language' => '',
        'recordEnteredBy' => '',
        'modified' => '',
        'sourcePrimaryKey-dbpk' => '',
        'collid' => '',
        'recordID' => '',
        'references' => '',
    ];

    static $derived = [
        'references' => 'derive_references',
        'recordID' => 'derive_combine_occurrence_record_id',
        'occurrenceID' => 'derive_combine_occurrence_record_id'
    ];
}

class DarwinCore {
    use DeriveOccurrenceReference;
    use DeriveCombineOccurrenceRecordID;
    use DeriveTaxonRank;
    use CalledDerived;

    static $casts = [
        'occid' => 'id',
        'tidInterpreted' => 'taxonID',
        'taxonRank' => 'verbatimTaxonRank',
        'collectionGuid' => 'collectionID',
        'dateLastModified' => 'modified'
    ];

    static $ignores = [];

    static $fields = [
        'id' => '',
        'institutionCode' => '',
        'collectionCode' => '',
        'ownerInstitutionCode' => '',
        'collectionID' => '',
        'basisOfRecord' => '',
        'occurrenceID' => '',
        'catalogNumber' => '',
        'otherCatalogNumbers' => '',
        'higherClassification' => '',
        'kingdom' => '',
        'phylum' => '',
        'class' => '',
        'order' => '',
        'family' => '',
        'scientificName' => '',
        'taxonID' => '',
        'scientificNameAuthorship' => '',
        'genus' => '',
        'subgenus' => '',
        'specificEpithet' => '',
        //not Casting Correctly
        'verbatimTaxonRank' => '',
        'infraspecificEpithet' => '',
        'cultivarEpithet' => '',
        //Using verbatimTaxonRank which it shouldn't
        'taxonRank' => '',
        'identifiedBy' => '',
        'dateIdentified' => '',
        'identificationReferences' => '',
        'identificationRemarks' => '',
        'taxonRemarks' => '',
        'identificationQualifier' => '',
        'typeStatus' => '',
        'recordedBy' => '',
        'recordNumber' => '',
        'eventDate' => '',
        'year' => '',
        'month' => '',
        'day' => '',
        'startDayOfYear' => '',
        'endDayOfYear' => '',
        'verbatimEventDate' => '',
        'occurrenceRemarks' => '',
        'habitat' => '',
        'behavior' => '',
        'vitality' => '',
        'fieldNumber' => '',
        'eventID' => '',
        'informationWithheld' => '',
        'dataGeneralizations' => '',
        'dynamicProperties' => '',
        'associatedOccurrences' => '',
        'associatedSequences' => '',
        'associatedTaxa' => '',
        'reproductiveCondition' => '',
        'establishmentMeans' => '',
        'lifeStage' => '',
        'sex' => '',
        'individualCount' => '',
        'samplingProtocol' => '',
        'preparations' => '',
        'locationID' => '',
        'continent' => '',
        'waterBody' => '',
        'islandGroup' => '',
        'island' => '',
        'country' => '',
        'countryCode' => '',
        'stateProvince' => '',
        'county' => '',
        'municipality' => '',
        'locality' => '',
        'locationRemarks' => '',
        'decimalLatitude' => '',
        'decimalLongitude' => '',
        'geodeticDatum' => '',
        'coordinateUncertaintyInMeters' => '',
        'verbatimCoordinates' => '',
        'georeferencedBy' => '',
        'georeferenceProtocol' => '',
        'georeferenceSources' => '',
        'georeferenceVerificationStatus' => '',
        'georeferenceRemarks' => '',
        'minimumElevationInMeters' => '',
        'maximumElevationInMeters' => '',
        'minimumDepthInMeters' => '',
        'maximumDepthInMeters' => '',
        'verbatimDepth' => '',
        'verbatimElevation' => '',
        'disposition' => '',
        'language' => '',
        'recordEnteredBy' => '',
        'modified' => '',
        'rights' => '',
        'rightsHolder' => '',
        'accessRights' => '',
        'recordID' => '',
        'references' => '',
    ];

    static $derived = [
        'references' => 'derive_references',
        'recordID' => 'derive_combine_occurrence_record_id',
        'occurrenceID' => 'derive_combine_occurrence_record_id',
        'taxonRank' => 'derive_taxon_rank'
    ];
}
