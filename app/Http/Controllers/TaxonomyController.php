<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxonomyController extends Controller {

    public static function taxonData(int $tid) {
        $taxon = DB::table('taxa as t')
            ->leftJoin('taxstatus as ts', 'ts.tid', 't.tid')
            ->where('t.tid', $tid)
            ->where('taxauthid', 1)
            ->select('*')
            ->first();

        return $taxon;
    }

    public static function getParents(int $tid) {
        $parent_tree = DB::select('with RECURSIVE parents as (
	SELECT * from taxstatus where tid = ?
	UNION ALL
	SELECT ts.* from taxstatus as ts, parents as p where ts.tid = p.parenttid and ts.taxauthid = 1 and ts.tid != 1
) SELECT taxa.tid, sciName, parents.family, parenttid, taxa.rankID, rankname
            from parents join taxa on taxa.tid = parents.tid join taxonunits on taxonunits.rankid = taxa.rankID and taxa.kingdomName = taxonunits.kingdomName order by taxa.rankID', [$tid]);
        return $parent_tree;
    }

    public static function getCommonNames(int $tid) {
        $common_names = DB::table('taxavernaculars')->where('tid', $tid)->select('*')->get();
        return $common_names;
    }

    public static function getTaxonOccurrenceStats(int $tid) {
        $occurrence_count = DB::table('omoccurrences')->where('tidInterpreted', $tid)->count('*');
        return $occurrence_count;
    }

    public static function taxon(int $tid) {
        $taxon = self::taxonData($tid);

        $parents = self::getParents($tid);

        $common_names = self::getCommonNames($tid);

        $occurrence_count = self::getTaxonOccurrenceStats($tid);

        return view('pages/taxon/profile', [
            'taxon' => $taxon,
            'parents' => $parents,
            'common_names' => $common_names,
            'occurrence_count' => $occurrence_count
        ]);
    }

    public static function taxonEdit(int $tid) {
        $taxon = self::taxonData($tid);

        return view('pages/taxon/edit', [
            'taxon' => $taxon
        ]);
    }
}
