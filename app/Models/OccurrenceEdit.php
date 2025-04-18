<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccurrenceEdit extends Model {
    protected $table = 'omoccuredits';

    protected $primaryKey = 'ocedid';

    //public $timestamps = false;

    protected $fillable = [
        'occid',
        'tableName',
        'fieldName',
        'fieldValueNew',
        'fieldValueOld',
        'reviewStatus',
        'appliedStatus',
        'editType',
        'isActive',
        'guid',
        'uid',
    ];

    public static function getGroupedByEdit(int $occid) {
        $editsRaw = OccurrenceEdit::where('occid', $occid)
            ->join('users', 'users.uid', 'omoccuredits.uid')
            ->orderBy('initialTimestamp', 'DESC')
            ->orderBy('uid')
            ->selectRaw('omoccuredits.*, COALESCE(name, CONCAT(firstName, lastName)) as name')
            ->get();

        $edits = [];
        $prevEdit = null;
        foreach($editsRaw as $edit) {
            if(!$prevEdit || $prevEdit->initialTimestamp != $edit->initialTimestamp) {
                $prevEdit = $edit;
                array_push($edits, [
                    'uid' => $edit->uid,
                    'name' => $edit->name,
                    'appliedStatus' => $edit->appliedStatus,
                    'initialTimestamp' => $edit->initialTimestamp,
                    'edits' => [ $edit ],
                ]);
            } else {
                $activeIdx = count($edits) - 1;
                array_push($edits[$activeIdx]['edits'], $edit);
            }
        }

        return $edits;
    }
}
