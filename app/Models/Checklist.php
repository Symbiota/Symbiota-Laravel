<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model {
    protected $table = 'fmchecklists';
    protected $primaryKey = 'clid';
    public $timestamps = false;

    protected $fillable = [];

    protected $hidden = [];

    protected $casts = [
        'defaultSettings' => 'json',
        'dynamicSql' => 'json'
    ];

    /**
     * Begin querying the model.
     *
     * @return \Illuminate\Database\Eloquent\Builder<static(Checklist)>
     */
    public static function clid(int $clid) {
        return self::query()->where('clid', $clid);
    }

    /**
     * Begin querying the model.
     *
     * @return \Illuminate\Database\Eloquent\Collection<Project>
     */
    public function projects(): \Illuminate\Database\Eloquent\Collection {
        return Project::query()
            ->join('fmchklstprojlink as cpl', 'cpl.pid', 'fmprojects.pid')
            ->where('clid', $this->clid)
            ->get();
    }

    /**
     * Begin querying the model.
     *
     * class Editor {
     *   public int $uid;
     *   public string $uname;
     *   public string $username;
     *   public string $assignedby
     * }
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function editors(): \Illuminate\Database\Eloquent\Collection {
        return User::query()
            ->join('userroles', 'userroles.uid', 'users.uid')
            ->join('users as assignee', 'assignee.uid', 'userroles.uidassignedby')
            ->where('userroles.role', UserRole::CL_ADMIN)
            ->where('userroles.tablepk', $this->clid)
            ->selectRaw('users.uid, CONCAT_WS(", ",users.lastname,users.firstname) AS uname, users.username, CONCAT_WS(", ",assignee.lastname,assignee.firstname) AS assignedby')
            ->orderBy('users.lastname')
            ->orderBy('users.firstname')
            ->get();
    }
}
