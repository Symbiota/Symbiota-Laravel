<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taxonomy extends Model {
    protected $table = 'taxa';

    protected $primaryKey = 'tid';

    protected $hidden = ['sciName', 'phyloSortSequence', 'nomenclaturalStatus', 'nomenclaturalCode', 'statusNotes', 'hybrid', 'pivot', 'modifiedUid', 'modifiedTimeStamp', 'initialTimeStamp', 'InitialTimeStamp'];

    protected $fillable = [];

    protected $maps = ['sciName' => 'scientificName'];

    protected $appends = ['scientificName'];

    public const RANK_MAP = [ // for indenting taxa
        0 => 1, // non-ranked node
        1 => 2, // organism
        10 => 3, // kingdom
        20 => 4, // subkingdom
        30 => 5, // division
        40 => 6, // subdivision
        50 => 7, // superclass
        60 => 8, // class
        70 => 9, // subclass
        100 => 10, // order
        110 => 11, // suborder
        140 => 12, // family
        150 => 13, // subfamily
        160 => 14, // tribe
        170 => 15, // subtribe
        180 => 16, // genus
        190 => 17, // subgenus
        200 => 18, // section
        210 => 19, // subsection
        220 => 20, // species
        240 => 21, // variety
        250 => 22, // subvariety
        260 => 23, // form
        270 => 24, // subform
        300 => 22, // infraspecies/cultivated
    ];

    public function getScientificNameAttribute() {
        return $this->attributes['sciName'];
    }

    public function commonNames() {
        return $this->hasMany(TaxaVernacular::class, 'tid', 'tid');
    }

    public function externalLinks() {
        return $this->hasMany(TaxaResourceLink::class, 'tid', 'tid');
    }

    public function occurrences() {
        return $this->hasMany(Occurrence::class, 'tidInterpreted', 'tid');
    }

    public function descriptions() {
        return $this->hasMany(TaxonomyDescription::class, 'tid', 'tid');
    }

    public function media() {
        return $this->hasMany(media::class, 'tid', 'tid');
    }
}
