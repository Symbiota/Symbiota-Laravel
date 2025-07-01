<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class Taxonomy extends Model {
    protected $table = 'taxa';

    protected $primaryKey = 'tid';

    protected $hidden = ['sciName', 'phyloSortSequence', 'nomenclaturalStatus', 'nomenclaturalCode', 'statusNotes', 'hybrid', 'pivot', 'modifiedUid', 'modifiedTimeStamp', 'initialTimeStamp', 'InitialTimeStamp'];

    protected $fillable = [];

    protected $maps = ['sciName' => 'scientificName'];

    protected $appends = ['scientificName'];

    public function getScientificNameAttribute() {
        return $this->attributes['sciName'];
    }

    public function descriptions() {
        return $this->hasMany(TaxonomyDescription::class, 'tid', 'tid');
    }

    public function media() {
        return $this->hasMany(Media::class, 'tid', 'tid');
    }

    public static function findTaxonAndChildren(string $search, int $thesaurus_id, TaxaSearchType $search_type = TaxaSearchType::Anyname) {
        $search = str_replace(';',',', $search);
        $search = explode(',', $search);

        // $use_thes = isset($params['usethes']) ?1 : 0;
        // $use_thes_associations = $params['usethes-associations'] ?? 2;

        //TODO (Logan) Figure out when this is needed
        // $tax_auth_id = $params['taxauthid'] ?? 1;

        $base_query = self::query()
            ->join('taxstatus as ts', 'taxa.tid', 'ts.tid')
            // Check if thesaurus_id is meant to be taxauthid
            ->where('ts.taxauthid', $thesaurus_id)
            ->whereIn('taxa.sciName', array_map('trim', $search))
            ->select('ts.tidaccepted as tid');

        $children_query = self::query()
            ->join('taxstatus as ts', 'taxa.tid', 'ts.tid')
            ->join('taxaenumtree as te', 'te.parenttid', 'ts.tidaccepted')
            ->where('ts.taxauthid', $thesaurus_id)
            ->whereIn('taxa.sciName', array_map('trim', $search))
            ->select('te.tid');

        $taxa_query = $base_query->union($children_query);

        return $taxa_query->get();
    }

    public static function getDirectChildren(int $tid) {
        $query = DB::table('taxa as t')
            ->join('taxstatus as ts', 'ts.tid', 't.tid')
            ->leftJoin('media as m', function (JoinClause $query) {
                $query->on('m.tid', 't.tid')
                    ->where('m.mediaType', 'image');
            })
            ->leftJoin('taxonunits as tu', function (JoinClause $query) {
                $query->on('tu.rankid', 't.rankID')
                    ->whereRaw('tu.kingdomName = COALESCE(t.kingdomName, t.sciName)');
            })->where('ts.taxauthid', 1)
            ->where('ts.parenttid', $tid)
            ->groupBy('t.tid')
            ->select([
                't.tid',
                'sciName', 'ts.family', 'parenttid', 't.rankID',
                DB::raw('ts.tidaccepted = ts.tid as accepted'),
                DB::raw('COALESCE(m.thumbnailUrl, m.url) as thumbnailUrl'),
                DB::raw('CASE WHEN t.sciName = "Organism" then "Organism" ELSE COALESCE(tu.rankname, "Kingdom") END as rankname')
            ]);
        $direct_children = $query->get();

        return $direct_children;
    }

    // TODO (Logan) ed/group what this should be named
    public static function getTaxaChecklist() {
        // See occurrence select builder function
        // What we need
        // taxa string
        // usethes (1 - if present, 0 if not)
        // taxontype (numeric)?

        // handle , delimited
    }
}

enum TaxaSearchType: string {
    case Anyname = 'anyname';
    case ScientificName = 'sciname';
    case Family = 'family';
    case TaxonomicGroup = 'TaxonomicGroup';
}
