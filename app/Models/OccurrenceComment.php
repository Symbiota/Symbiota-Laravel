<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class OccurrenceComment extends Model {
    protected $table = 'omoccurcomments';

    protected $primaryKey = 'comid';

    public $timestamps = false;

    const REPORTED = 2;

    protected $fillable = [
        'occid',
        'comment',
        'uid',
        'reviewstatus',
        'parentcomid',
    ];

    public static function getCommentsWithUsername($occurrence) {
        $query = self::where('occid', $occurrence->occid)
            ->join('users', 'users.uid', 'omoccurcomments.uid')
            ->selectRaw('omoccurcomments.*, username');

        if (Gate::check('COLL_EDIT', $occurrence->collid)) {
            return $query->get();
        } else {
            return $query
                ->where('reviewstatus', '!=', self::REPORTED)
                ->get();
        }
    }
}
