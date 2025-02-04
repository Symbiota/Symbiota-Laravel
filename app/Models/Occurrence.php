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

    public static $public_fields = [
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

    protected static $hidden_fields = [
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

    protected $fillable = [];

    protected $hidden = [];

    public function __construct() {
        parent::__construct();
        $this->fillable = self::$public_fields;
        $this->hidden = self::$hidden_fields;
    }

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
    public static function buildSelectQuery(Request $request) {
        $query = DB::table('omoccurrences as o')
            ->join('omcollections as c', 'c.collid', '=', 'o.collid');

        // Maybe Can Remove
        if ($request->query('collid')) {
            if (is_array($request->query('collid')) && ! empty($request->query('collid'))) {
                $query->whereIn('c.collid', $request->query('collid'));
            } else {
                $query->where('c.collid', '=', $request->query('collid'));
            }
        }

        foreach ($request->all() as $name => $value) {
            if ((in_array($name, self::$public_fields) || in_array($name, self::$hidden_fields)) && $value) {
                /* Some Values Like with end operator makes more sense */
                if ($name === 'county') {
                    $query->whereLike('o.' . $name, $value . '%');
                } else {
                    $query->where('o.' . $name, '=', $value);
                }
            }
        }

        // TODO create enum for this
        // Add Custom Values
        for ($i = 1; $i < 11; $i++) {
            // Create Constant for this somewhere
            $name = $request->query('q_customfield' . $i);
            $type = $request->query('q_customtype' . $i);
            $value = $request->query('q_customvalue' . $i);

            if (in_array($name, self::$public_fields) || in_array($name, self::$hidden_fields)) {
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
        if ($request->query('sort')) {
            if (($idx = array_search($request->query('sort'), self::$public_fields)) > 0) {
                $query->orderByRaw('ISNULL(o.' . self::$public_fields[$idx] . ') ASC');
            }
            $query->orderBy(
                $request->query('sort'),
                $request->query('sortDirection') === 'DESC' ? 'DESC' : 'ASC'
            );
        }

        // Additional Filters from other tables
        // TODO (Logan) Need to add consts for string literals
        if ($request->query('hasImages')) {
            if ($request->query('hasImages') === 'with_images') {
                $query->whereIn('o.occid', function (Builder $query) {
                    $query->select('i.occid')->from('images as i')->groupBy('i.occid');
                });
            } elseif ($request->query('hasImages') === 'without_images') {
                $query->whereNotIn('o.occid', function (Builder $query) {
                    $query->select('i.occid')->from('images as i')->whereNotNull('i.occid')->groupBy('i.occid');
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

        //footprintGeoJson Searching
        //Boundary Searching
        //Radius Searching

        if ($request->query('taxa')) {
            $taxa = $request->query('taxa');

            //TODO (Logan) Enum this default constants
            $use_thes = $request->query('usethes') ?? 1;
            $use_thes_associations = $request->query('usethes-associations') ?? 2;
            //TODO (Logan) Figure out when this is needed
            $tax_auth_id = $request->query('taxauthid') ?? 2;

            //Todo figure out Occurence Taxa Manager
            $taxon_input = explode(',', $request->query('taxa'));
            $query->whereIn('o.sciName', $taxon_input);
        }

        if ($request->query('elevhigh') && $request->query('elevlow')) {
            $query->where(function (Builder $where) {
                $where
                    ->where('maximumDepthInMeters', '<=', $request->query('elevhigh'))
                    ->where('minimumDepthInMeters', '>=', $request->query('elevlow'));
            });
        }

        return $query;
    }
}
