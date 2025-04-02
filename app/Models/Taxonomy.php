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

    public function search(string $search, int $thesaurus_id, TaxaSearchType $search_type = TaxaSearchType::Anyname) {
        $search = str_replace(';',',', $search);
        $search = explode(',', $search);
        // TODO (Logan) implement tree grab
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
                DB::raw('COALESCE(m.thumbnailUrl, m.url) as thumbnailUrl'),
                DB::raw('CASE WHEN t.sciName = "Organism" then "Organism" ELSE COALESCE(tu.rankname, "Kingdom") END as rankname')
            ]);

        $direct_children = $query->get();

        /*
        foreach ($direct_children as $child) {
            if (! $child->thumbnailUrl) {
                DB::table('media')->where($child->tid);
            }
        }*/

        return $direct_children;
    }

    public static function getAllChildren(int $root_tid) {
//         SELECT
//
// from taxa t
// join taxstatus ts on ts.tid = t.tid
// where ts.taxauthid = 1 and ts.tid = ts.tidaccepted
// limit 100;
        //
        $taxon_rank = self::query()
            ->join('taxstatus as ts', 'ts.tid', 'taxa.tid')
            ->where('ts.tid', $root_tid)
            ->where('ts.taxauthid', 1)
            ->select('taxa.rankID', 'taxa.sciName')
            ->first();

        $query = self::query()
            ->join('taxstatus as ts', 'ts.tid', 'taxa.tid')
            ->orderBy('taxa.rankID')
            ->where('ts.taxauthid', 1)
            ->where('taxauthid', 1)
            ->where('rankID', '>', $taxon_rank->rankID)
            ->whereRaw('ts.tid = ts.tidaccepted')
            ->select(['taxa.rankID', 'taxa.tid', 'ts.parenttid', 'taxa.sciName']);

        $tree = [
            $root_tid => true
        ];

        //total / chunking number
        //number of queries and how much memormy we have
        //
        $results = $query->get();

        foreach ($results as $taxon) {
            if(array_key_exists($taxon->parenttid, $tree)) {
                $tree[$taxon->tid] = true;
            }
        }

        return $tree;
/*
        return DB::table('omoccurrences')
            ->whereIn('tidaccepted', array_keys($tree))
            ->limit(100)->get();
*/

        //return 'done';

        /*
        $query->chunk(10000, function (\Illuminate\Support\Collection $taxa) use (&$tree) {
            foreach ($taxa as $taxon) {
                if(array_key_exists($taxon->parenttid, $tree)) {
                    $tree[$taxon->tid] = $taxon;
                }
            }
        });
*/

        return $tree;
    }

    // TODO (Logan) Clean this up before pr taxon-fetching-interface
    private static function oldGetAllChildren(int $tid): array {
        $child_tree = DB::select('with RECURSIVE children as (
	SELECT * from taxstatus where parenttid = ?
	UNION ALL
	SELECT ts.* from taxstatus as ts, children as c where ts.parenttid = c.tid and ts.taxauthid = 1 and ts.tidaccepted = ts.tid
) SELECT taxa.tid, sciName, children.family, parenttid, taxa.rankID, rankname, COALESCE(m.thumbnailUrl, m.url) as thumbnailUrl
            from children join taxa on taxa.tid = children.tid left join media as m on m.tid = taxa.tid join taxonunits on taxonunits.rankid = taxa.rankID and taxa.kingdomName = taxonunits.kingdomName group by taxa.tid order by taxa.rankID', [$tid]);

        return $child_tree;
    }


    /*
     * search where we already have a list of taxon strings
     * search where we already have a list of tids
    */
}

enum TaxaSearchType: string {
    case Anyname = 'anyname';
    case ScientificName = 'sciname';
    case Family = 'family';
    case TaxonomicGroup = 'TaxonomicGroup';
}
