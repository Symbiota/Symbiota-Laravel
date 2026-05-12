<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Collection extends Model {
    use HasFactory;

    protected $table = 'omcollections';

    protected $primaryKey = 'collID';

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
        return $this->hasMany(Occurrence::class, 'collid', 'collID');
    }

    public function stats() {
        $stats = CollectionStats::query()->where('collid', $this->collID)
            ->select([
                'omcollectionstats.*',
                DB::raw('DATE_FORMAT(uploaddate, "%D %M %Y") as uploaddate'),
            ])
            ->first();

        return $stats;
    }

    public function dwcaPaths() {
        $paths = DB::table('uploadspecparameters')
            ->where('collid', $this->collID)
            ->select(['uspid', 'title', 'path'])
            ->get();

        foreach ($paths as $path) {
            $path->path = str_replace('/archive.do', '/resource.do', trim($path->path));
        }

        return $paths;
    }

    public function isTraitCodingActivated(): bool {
        $results = DB::table('tmtraits')->select('traitid')->limit(1)->get();

        return $results->count() ? true : false;
    }

    public function isMaterialSampleEnabled(): bool {
        return $this->dynamicProperties['editorProps']['modules-panel'][0]['matSample']['status'] ?? false;
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

    public static function get(int $collId) {
        return Cache::remember('collection-' . $collId, now()->addMinutes(1), function () use ($collId) {
            return self::query()->where('collid', $collId)->first();
        });
    }
}
