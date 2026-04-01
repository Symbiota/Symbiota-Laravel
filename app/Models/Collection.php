<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Collection extends Model {
    use HasFactory;

    protected $table = 'omcollections';

    protected $primaryKey = 'collid';

    public $timestamps = false;

    protected $fillable = [
        'institutionCode', 'collectionCode', 'collectionName', 'collectionID', 'datasetID', 'datasetName', 'fullDescription', 'resourceJson', 'IndividualUrl', 'contactJson',
        'latitudeDecimal', 'longitudeDecimal', 'icon', 'collType', 'managementType', 'publicEdits', 'collectionGuid', 'rightsHolder', 'rights', 'usageTerm', 'dwcaUrl',
        'bibliographicCitation', 'accessRights', 'sortSeq',
    ];

    protected $hidden = ['securityKey', 'guidTarget', 'aggKeysStr', 'dwcTermJson', 'publishToGbif', 'publishToIdigbio', 'dynamicProperties'];

    protected $casts = [
        'dynamicProperties' => 'json',
        'aggKeysStr' => 'json',
        'dwcTermJson' => 'json',
    ];

    public function occurrence() {
        return $this->hasMany(Occurrence::class, 'collid', 'collid');
    }

    public function stats() {
        $stats = CollectionStats::query()->where('collid', $this->collid)
            ->select([
                'omcollectionstats.*',
                DB::raw('DATE_FORMAT(uploaddate, "%D %M %Y") as uploaddate')
            ])
            ->first();
        return $stats;
    }

    public function dwcaPaths() {
        $paths = DB::table('uploadspecparameters')
            ->where('collid', $this->collid)
            ->select(['uspid', 'title', 'path'])
            ->get();

        foreach($paths as $path) {
            $path->path = str_replace('/archive.do', '/resource.do', trim($path->path));
        }

        return $paths;
    }


    public function isTraitCodingActivated(): bool {
        $results = DB::table('tmtraits')->select('traitid')->limit(1)->get();
        return $results->count() ? true: false;
    }

    //collTypes
    const Specimens = 'Preserved Specimens';
    const GeneralObservations = 'General Observations';
    const Observations = 'Observations';

    public function isSpecimens() {
        return $this->collType === self::Specimens;
    }

    public function isObservations() {
        return $this->collType === self::GeneralObservations || $this->collType === self::Observations;
    }
}
