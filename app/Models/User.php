<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $primaryKey = 'uid';

    const CREATED_AT = 'initialTimestamp';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'firstName',
        'title',
        'lastName',
        'email',
        'institution',
        'department',
        'address',
        'state',
        'city',
        'zip',
        'country',
        'password',
        //Note this is really a orcid
        'dynamicProperties',
        'oauth_provider',
        'guid',
        'access_token',
        'refresh_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'access_token',
        'refresh_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'dynamicProperties' => 'array',
        'password' => 'hashed',
    ];

    public function roles() {
        return $this->hasMany(UserRole::class, 'uid');
    }

    public function hasOneRoles(array $roles) {
        $query = UserRole::query()
            ->where('uid', $this->uid)
            ->select('uid', 'role', 'tablePK');

        $query->where(function ($query) use ($roles) {
            foreach ($roles as $key => $value) {
                if (is_numeric($key)) {
                    $query->orWhere('role', $value);
                } else {
                    $query->orWhere(function (Builder $q) use ($key, $value) {
                        $q->where('role', $key)
                            ->where('tablePK', $value);
                    });
                }
            }
        });

        return $query->first() ? true : false;
    }

    public function canViewChecklist(int $clid) {
        $select = ['userroles.uid', 'role', 'tablePK'];
        $super_admin_query = UserRole::query()
            ->where('role', UserRole::SUPER_ADMIN)
            ->where('uid', $this->uid)
            ->select($select);

        $query = UserRole::query()
            ->join('fmchecklists as fmc', function ($join) {
                $join->on('fmc.clid', 'userroles.tablePK')
                    ->whereRaw('userroles.tableName = "fmchecklists"');
            })
            ->where('userroles.uid', $this->uid)
            ->where(function ($builder) {
                $builder->where('role', UserRole::CL_ADMIN)
                    ->orWhereRaw('fmc.access = "public"');
            })
            ->union($super_admin_query)
            ->select($select);

        return $query->first() ? true : false;
    }

    public function checklists() {
        return DB::table('fmchecklists')
            ->leftJoin('userroles as ur', 'tablePK', 'clid')
            ->where(function ($query) {
                $query
                    ->whereIn('role', [UserRole::CL_ADMIN])
                    ->where('ur.uid', $this->uid);
            })
            ->orWhere('fmchecklists.uid', $this->uid)
            ->distinct()
            ->select('fmchecklists.*')
            ->get();
    }

    public function projects() {
        return DB::table('fmprojects')
            ->leftJoin('userroles as ur', 'tablePK', 'pid')
            ->where(function ($query) {
                $query
                    ->whereIn('role', [UserRole::PROJ_ADMIN])
                    ->where('ur.uid', $this->uid);
            })
            ->distinct()
            ->select('fmprojects.*')
            ->get();
    }

    public function collections() {
        if ($this->hasOneRoles([UserRole::SUPER_ADMIN])) {
            return DB::table('omcollections')->get();
        }

        return DB::table('omcollections')
            ->leftJoin('userroles as ur', 'tablePK', 'collid')
            ->where(function ($query) {
                $query
                    ->whereIn('role', [UserRole::COLL_EDITOR, UserRole::COLL_ADMIN])
                    ->where('ur.uid', $this->uid);
            })
            ->distinct()
            ->select('omcollections.*')
            ->get();
    }

    public function datasets() {
        return DB::table('omoccurdatasets')
            ->where('uid', $this->uid)
            ->get();
    }
}
