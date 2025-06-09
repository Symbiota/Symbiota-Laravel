<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Occurrence extends Model {
    use HasFactory;

    protected $table = 'omoccurrences';

    protected $primaryKey = 'occid';

    public $timestamps = false;

    public static $searchable_fields = [
        'collid',
        'dbpk',
        'basisOfRecord',
        'occurrenceID',
        'catalogNumber',
        'otherCatalogNumbers',
        'family',
        'scientificName',
        'sciname',
        'genus',
        'specificEpithet',
        'organismID',
        'taxonRank',
        'infraspecificEpithet',
        'institutionCode',
        'collectionCode',
        'scientificNameAuthorship',
        'taxonRemarks',
        'identifiedBy',
        'dateIdentified',
        'identificationReferences',
        'identificationRemarks',
        'identificationQualifier',
        'typeStatus',
        'recordedBy',
        'recordNumber',
        'associatedCollectors',
        'eventDate',
        'eventDate2',
        'verbatimEventDate',
        'eventTime',
        'habitat',
        'substrate',
        'fieldNotes',
        'fieldNumber',
        'eventID',
        'occurrenceRemarks',
        'informationWithheld',
        'dataGeneralizations',
        'associatedTaxa',
        'dynamicProperties',
        'verbatimAttributes',
        'behavior',
        'reproductiveCondition',
        'cultivationStatus',
        'establishmentMeans',
        'lifeStage',
        'sex',
        'individualCount',
        'samplingProtocol',
        'samplingEffort',
        'preparations',
        'locationID',
        'continent',
        'parentLocationID',
        'country',
        'stateProvince',
        'county',
        'municipality',
        'waterBody',
        'islandGroup',
        'island',
        'countryCode',
        'locality',
        'localitySecurity',
        'localitySecurityReason',
        'decimalLatitude',
        'decimalLongitude',
        'geodeticDatum',
        'coordinateUncertaintyInMeters',
        'footprintWKT',
        'locationRemarks',
        'verbatimCoordinates',
        'georeferencedBy',
        'georeferencedDate',
        'georeferenceProtocol',
        'georeferenceSources',
        'georeferenceVerificationStatus',
        'georeferenceRemarks',
        'minimumElevationInMeters',
        'maximumElevationInMeters',
        'verbatimElevation',
        'minimumDepthInMeters',
        'maximumDepthInMeters',
        'verbatimDepth',
        'availability',
        'disposition',
        'storageLocation',
        'modified',
        'language',
        'processingStatus',
        'recordEnteredBy',
        'duplicateQuantity',
        'labelProject',
        'recordID',
        'dateEntered',
    ];

    protected $fillable = [
        'collid',
        'dbpk',
        'basisOfRecord',
        'occurrenceID',
        'catalogNumber',
        'otherCatalogNumbers',
        'family',
        'scientificName',
        'sciname',
        'genus',
        'specificEpithet',
        'datasetID',
        'organismID',
        'taxonRank',
        'infraspecificEpithet',
        'institutionCode',
        'collectionCode',
        'scientificNameAuthorship',
        'taxonRemarks',
        'identifiedBy',
        'dateIdentified',
        'identificationReferences',
        'identificationRemarks',
        'identificationQualifier',
        'typeStatus',
        'recordedBy',
        'recordNumber',
        'associatedCollectors',
        'eventDate',
        'eventDate2',
        'verbatimEventDate',
        'eventTime',
        'habitat',
        'substrate',
        'fieldNotes',
        'fieldNumber',
        'eventID',
        'occurrenceRemarks',
        'informationWithheld',
        'dataGeneralizations',
        'associatedTaxa',
        'dynamicProperties',
        'verbatimAttributes',
        'behavior',
        'reproductiveCondition',
        'cultivationStatus',
        'establishmentMeans',
        'lifeStage',
        'sex',
        'individualCount',
        'samplingProtocol',
        'samplingEffort',
        'preparations',
        'locationID',
        'continent',
        'parentLocationID',
        'country',
        'stateProvince',
        'county',
        'municipality',
        'waterBody',
        'islandGroup',
        'island',
        'countryCode',
        'locality',
        'localitySecurity',
        'localitySecurityReason',
        'decimalLatitude',
        'decimalLongitude',
        'geodeticDatum',
        'coordinateUncertaintyInMeters',
        'footprintWKT',
        'locationRemarks',
        'verbatimCoordinates',
        'georeferencedBy',
        'georeferencedDate',
        'georeferenceProtocol',
        'georeferenceSources',
        'georeferenceVerificationStatus',
        'georeferenceRemarks',
        'minimumElevationInMeters',
        'maximumElevationInMeters',
        'verbatimElevation',
        'minimumDepthInMeters',
        'maximumDepthInMeters',
        'verbatimDepth',
        'availability',
        'disposition',
        'storageLocation',
        'modified',
        'language',
        'processingStatus',
        'recordEnteredBy',
        'duplicateQuantity',
        'labelProject',
        'recordID',
        'dateEntered',
    ];

    protected $hidden = [
        'collection',
        'scientificName',
        'recordedbyid',
        'observerUid',
        'labelProject',
        'recordEnteredBy',
        'associatedOccurrences',
        'previousIdentifications',
        'verbatimCoordinateSystem',
        'coordinatePrecision',
        'footprintWKT',
        'dynamicFields',
        'institutionID',
        'collectionID',
        'genericColumn1',
        'genericColumn2',
    ];

    public static $snakeAttributes = false;

    public function getInstitutionCodeAttribute($value) {
        if (! $value) {
            $value = $this->collection->institutionCode;
        }

        return $value;
    }

    public function getCollectionCodeAttribute($value) {
        if (! $value) {
            $value = $this->collection->collectionCode;
        }

        return $value;
    }

    public function getOccurrenceIDAttribute($value) {
        if (! $value && $this->collection->guidTarget == 'symbiotaUUID') {
            $value = $this->attributes['recordID'];
        }

        return $value;
    }

    public function collection() {
        return $this->belongsTo(Collection::class, 'collid', 'collid');
    }

    public function identification() {
        return $this->hasMany(OccurrenceIdentification::class, 'occid', 'occid');
    }

    public function media() {
        return $this->hasMany(Media::class, 'occid', 'occid');
    }

    public function annotationExternal() {
        return $this->hasMany(OccurrenceAnnotationExternal::class, 'occid', 'occid');
    }

    public function annotationInternal() {
        return $this->hasMany(OccurrenceAnnotationInternal::class, 'occid', 'occid');
    }

    public function annotationInternalColl() {
        return $this->hasMany(OccurrenceAnnotationInternal::class, 'occid', 'occid')->where('collid', 1);
    }

    public function portalPublications() {
        return $this->belongsToMany(PortalPublication::class, 'portaloccurrences', 'occid', 'pubid')->withPivot('remoteOccid');
    }

    /* Produces DB query builder object based on a request for general purpose use
     * This Function's only depedency on eloquent is the protected variables
     */
    public static function buildSelectQuery(array $params) {
        $query = DB::table('omoccurrences as o')
            ->join('omcollections as c', 'c.collid', '=', 'o.collid');

        $ALLOW_ARRAY_SEARCH = ['collid' => true];

        foreach ($params as $name => $value) {
            if (in_array($name, self::$searchable_fields) && $value) {
                if (is_array($value)) {
                    if (! empty($value) && array_key_exists($name, $ALLOW_ARRAY_SEARCH)) {
                        //TODO (Logan) fix this to be general to param
                        $query->whereIn('c.collid', $value);
                    }
                } else {
                    /* Some Values Like with end operator makes more sense */
                    if ($name === 'county') {
                        $query->whereLike('o.' . $name, $value . '%');
                    } else {
                        $query->where('o.' . $name, '=', $value);
                    }
                }
            }
        }

        // TODO create enum for this
        // Add Custom Values
        for ($i = 1; $i < 11; $i++) {
            // Create Constant for this somewhere
            $name = $params['q_customfield' . $i] ?? null;
            $type = $params['q_customtype' . $i] ?? null;
            $value = $params['q_customvalue' . $i] ?? null;

            if (in_array($name, self::$searchable_fields)) {
                match ($type) {
                    'EQUALS' => $query->where($name, '=', $value),
                    'NOT_EQUALS' => $query->where($name, '!=', $value),
                    'STARTS_WITH' => $query->whereLike($name, $value . '%'),
                    'LIKE' => $query->whereLike($name, '%' . $value . '%'),
                    'NOT_LIKE' => $query->whereNotLike($name, '%' . $value . '%'),
                    'GREATER_THAN' => $query->where($name, '>', $value),
                    'LESS_THAN' => $query->where($name, '<', $value),
                    'IS_NULL' => $query->whereNull($name),
                    'NOT_NULL' => $query->whereNotNull($name),
                };

                //$query->orderByRaw('ISNULL(o.' . $name . ') ASC');
            }
        }

        // Decide How Values should be sorted
        if ($sort = $params['sort'] ?? false) {
            if (($idx = array_search($sort, self::$searchable_fields)) > 0) {
                $query->orderByRaw('ISNULL(o.' . self::$searchable_fields[$idx] . ') ASC');
            }
            $query->orderBy(
                'o.' . $sort,
                empty($params['sortDirection']) || $params['sortDirection'] === 'DESC' ? 'DESC' : 'ASC'
            );
        }

        // Additional Filters from other tables
        // TODO (Logan) Need to add consts for string literals
        if ($hasImages = $params['hasImages'] ?? false) {
            if ($hasImages === 'with_images') {
                $query->whereIn('o.occid', function (Builder $query) {
                    $query->select('m.occid')->from('media as m')->groupBy('m.occid')->where('mediaType', '=', 'image');
                });
            } elseif ($hasImages === 'without_images') {
                $query->whereNotIn('o.occid', function (Builder $query) {
                    $query->select('i.occid')->from('media as m')->whereNotNull('i.occid')->where('mediaType', '!=', 'image')->groupBy('i.occid');
                });
            }
        }

        //hasaudio
        //hasgentic
        //hascoords
        //includecult
        //attr(traitsearching: stat)

        //Checklisting
        //clid

        //voucher mode
        //excludecult Does by default?

        //datasetid TODO (Logan) figure out what this is;
        if($datasetID = $params['datasetID'] ?? false) {
            $query->join('omoccurdatasetlink as odlink', 'odlink.occid', 'o.occid')
                ->where('odlink.datasetID', $datasetID);
        }


        //footprintGeoJson Searching
        //Boundary Searching
        //Radius Searching
        if ($taxa = $params['taxa'] ?? false) {
            //TODO (Logan) Enum this default constants
            $use_thes = $params['usethes'] ?? 1;
            $use_thes_associations = $params['usethes-associations'] ?? 2;
            //TODO (Logan) Figure out when this is needed
            $tax_auth_id = $params['taxauthid'] ?? 2;

            if (is_numeric($taxa)) {
                $query->where('tidInterpreted', $taxa);
            } else {
                //Todo figure out Occurence Taxa Manager
                $taxon_input = explode(',', $taxa);
                $query->whereIn('o.sciName', $taxon_input);
            }
        }

        if ($clid = $params['clid'] ?? false) {
            $query->join('fmvouchers as fmv', 'fmv.occid', 'o.occid')
                ->where('clid', $clid);
        }

        if (($elev_high = $params['elevhigh'] ?? false) && ($elev_low = $elev['elevlow'] ?? false)) {
            $query->where(function (Builder $where) {
                $where
                    ->where('maximumDepthInMeters', '<=', $elev_high)
                    ->where('minimumDepthInMeters', '>=', $elev_low);
            });
        }

        return $query;
    }
}
