<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Identifiers extends Model {
    protected $table = 'omoccuridentifiers';

    protected $primaryKey = 'idomoccuridentifiers';

    public $timestamps = false;

    protected $fillable = [
        'occid',
        'identifierValue',
        'identifierName',
        'format',
        'notes',
        'sortBy',
        'recordID',
        'modifiedUid',
        'modifiedTimestamp',
        'initialTimestamp',
    ];

    protected $hidden = [];

    public function occurrence() {
        return $this->belongsTo(Occurrence::class, 'occid', 'occid');
    }
}
