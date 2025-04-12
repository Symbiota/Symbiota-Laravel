<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dataset extends Model {
    use HasFactory;

    protected $table = 'omoccurdatasets';

    protected $primaryKey = 'datasetID';

    public $timestamps = false;

    public $fillable = [
        'datasetID',
        'bibliographicCitation',
        'name',
        'category',
        'isPublic',
        'parentDatasetID ',
        'includeInSearch',
        'description',
        'datasetIdentifier',
        'notes',
        'dynamicProperties',
        'sortSequence',
        'uid',
        'collid',
    ];

    /**
     * @return Collection<int,Model>
     */
    public static function getUserDatasets(User $user): Collection {
        return self::query()
            ->where('uid', $user->uid)
            ->get();
    }
}
