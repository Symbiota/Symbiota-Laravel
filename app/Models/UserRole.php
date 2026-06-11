<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model {
    protected $table = 'userroles';

    protected $primaryKey = 'userroleid';

    public $timestamps = false;

    protected $fillable = [
        'tableName',
        'tablePK',
        'uid',
        'role',
    ];

    protected $hidden = [];

    /* List of Available Roles */
    public const SUPER_ADMIN = 'SuperAdmin';

    public const RARE_SPP_ADMIN = 'RareSppAdmin';

    public const RARE_SPP_READER_ALL = 'RareSppReadAll';

    public const RARE_SPP_READER = 'RareSppReader';

    public const DATASET_ADMIN = 'DatasetAdmin';

    public const DATASET_EDITOR = 'DatasetEditor';

    public const TAXONOMY = 'Taxonomy';

    public const TAXON_PROFILE = 'TaxonProfile';

    public const KEY_ADMIN = 'KeyAdmin';

    public const KEY_EDITOR = 'KeyEditor';

    public const COLL_ADMIN = 'CollAdmin';

    public const COLL_EDITOR = 'CollEditor';

    public const PROJ_ADMIN = 'ProjAdmin';

    public const CL_ADMIN = 'ClAdmin';

    public const CL_CREATE = 'ClCreate';

    public const GLOSSARY_EDITOR = 'GlossaryEditor';

    public const PERSONAL_OBS_ADMIN = 'PersonalObsAdmin'; // Todo is in current code base but not assignable?

    public const PERSONAL_OBS_EDITOR = 'PersonalObsEditor'; // Todo is in current code base but not assignable?

    public const PERSONAL_OBS_READER = 'PersonalObsReader'; // Todo is in current code base but not assignable?

    public static function roles(): array {
        return [
            self::SUPER_ADMIN,
            self::RARE_SPP_ADMIN,
            self::RARE_SPP_READER_ALL,
            self::RARE_SPP_READER,
            self::DATASET_ADMIN,
            self::DATASET_EDITOR,
            self::TAXONOMY,
            self::TAXON_PROFILE,
            self::KEY_ADMIN,
            self::KEY_EDITOR,
            self::COLL_ADMIN,
            self::COLL_EDITOR,
            self::PROJ_ADMIN,
            self::CL_ADMIN,
            self::CL_CREATE,
            self::GLOSSARY_EDITOR,
        ];
    }

    public function user() {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }
}
