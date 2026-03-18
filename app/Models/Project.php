<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model {
    protected $table = 'fmprojects';

    protected $primaryKey = 'pid';

    protected $fillable = [
        'projname',
        'managers',
        'fulldescription',
        'notes',
        'isPublic',
    ];

    protected $attributes = [
        'isPublic' => 0
    ];

    public function checklists() {
        return DB::table('fmchecklists as c')
            ->select('link.pid', 'c.defaultSettings', 'c.clid', 'c.name', 'mapChecklist')
            ->leftJoin('fmchklstprojlink as link', 'link.clid', '=', 'c.clid')
            ->where('link.pid', '=', $this->pid)
            ->orderByRaw('-link.pid DESC')
            ->get();
    }
}
