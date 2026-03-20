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
        $stats = DB::table('omcollectionstats as ocs')->where('collid', $this->collid)
            ->select(['ocs.*', DB::raw('DATE_FORMAT(uploaddate, "%D %M %Y") as uploaddate')])
            ->first();

        if ($stats && $stats->dynamicProperties) {
            $stats->dynamicProperties = json_decode($stats->dynamicProperties);
        }

        return $stats;
    }

    //collTypes
    const Specimens = 'Preserved Specimens';

    const GeneralObservations = 'General Observations';

    const Observations = 'Observations';
}
