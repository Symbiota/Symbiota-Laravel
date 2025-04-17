<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccurrenceComment extends Model {
    protected $table = 'omoccurcomments';

    protected $primaryKey = 'comid';

    protected $fillable = [
        'occid',
        'comment',
        'uid',
        'reviewstatus',
        'parentcomid',
    ];

    public static function getCommentsWithUsername(int $occid) {
        return self::where('occid', $occid)->join(
            'users', 'users.uid', 'omoccurcomments.uid'
        )->selectRaw('omoccurcomments.*, username')->get();
    }
}
