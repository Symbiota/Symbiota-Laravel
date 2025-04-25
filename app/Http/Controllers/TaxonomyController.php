<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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

    public static function getParents(int $tid): array {
        $parent_tree = DB::select('with RECURSIVE parents as (
	SELECT * from taxstatus where tid = ?
	UNION ALL
	SELECT ts.* from taxstatus as ts, parents as p where ts.tid = p.parenttid and ts.taxauthid = 1 and ts.tid != 1
) SELECT DISTINCT taxa.tid, sciName, parents.family, parenttid, taxa.rankID, rankname
            from parents join taxa on taxa.tid = parents.tid join taxonunits on taxonunits.rankid = taxa.rankID and taxa.kingdomName = taxonunits.kingdomName order by taxa.rankID', [$tid]);

        return $parent_tree;
    }

    public static function getDirectChildren(int $tid) {
        $query = DB::table('taxa as t')
            ->join('taxstatus as ts', 'ts.tid', 't.tid')
            ->leftJoin('media as m', function (JoinClause $query) {
                $query->on('m.tid', 't.tid')
                    ->where('m.mediaType', 'image');
            })
            ->join('taxonunits as tu', function (JoinClause $query) {
                $query->on('tu.rankid', 't.rankID')
                    ->whereRaw('tu.kingdomName = t.kingdomName');
            })->where('ts.taxauthid', 1)
            ->where('ts.parenttid', $tid)
            ->groupBy('t.tid')
            ->select(['t.tid', 'sciName', 'ts.family', 'parenttid', 't.rankID', 'rankname', DB::raw('COALESCE(m.thumbnailUrl, m.url) as thumbnailUrl')]);

        $direct_children = $query->get();

        foreach ($direct_children as $child) {
            if (! $child->thumbnailUrl) {
                DB::table('media')->where($child->tid);
            }
        }

        return $direct_children;
    }

    // Be very Careful when calling this function can be very slow depending on the tid
    public static function getAllChildren(int $tid): array {
        $child_tree = DB::select('with RECURSIVE children as (
	SELECT * from taxstatus where parenttid = ?
	UNION ALL
	SELECT ts.* from taxstatus as ts, children as c where ts.parenttid = c.tid and ts.taxauthid = 1 and ts.tidaccepted = ts.tid
) SELECT taxa.tid, sciName, children.family, parenttid, taxa.rankID, rankname, COALESCE(m.thumbnailUrl, m.url) as thumbnailUrl
            from children join taxa on taxa.tid = children.tid left join media as m on m.tid = taxa.tid join taxonunits on taxonunits.rankid = taxa.rankID and taxa.kingdomName = taxonunits.kingdomName group by taxa.tid order by taxa.rankID', [$tid]);

        return $child_tree;
    }

    public static function getCommonNames(int $tid) {
        $common_names = DB::table('taxavernaculars')->where('tid', $tid)->select('*')->get();

        return $common_names;
    }

    public static function getTaxonOccurrenceStats(int $tid) {
        $occurrence_count = DB::table('omoccurrences')->where('tidInterpreted', $tid)->count('*');

        return $occurrence_count;
    }

    public static function getExternalLinks(int $tid) {
        $external_links_query = DB::table('taxaresourcelinks as trl')
            ->where('trl.tid', $tid)
            ->select('*');

        return $external_links_query
            ->get();
    }

    public static function getTaxaDescriptions(int $tid) {
        $statements = DB::table('taxadescrblock as tdb')->join('taxadescrstmts as tds', 'tds.tdbid', 'tdb.tdbid')
            ->where('tdb.tid', $tid)
            ->select('tdProfileID', 'source', 'sourceUrl', 'heading', 'statement')
            ->get();

        $taxa_descriptions = [];

        foreach ($statements as $statement) {
            if ($taxa_descriptions[$statement->tdProfileID] ?? false) {
                $taxa_descriptions[$statement->tdProfileID]['statements'][$statement->heading] = $statement->statement;
            } else {
                $taxa_descriptions[$statement->tdProfileID] = [
                    'source' => $statement->source,
                    'sourceUrl' => $statement->sourceUrl,
                    'statements' => [],
                ];
            }
        }

        return $taxa_descriptions;
    }

    public static function taxon(int $tid) {
        $taxon = self::taxonData($tid);

        $parents = self::getParents($tid);

        $common_names = self::getCommonNames($tid);
        $children = self::getDirectChildren($tid);

        $occurrence_count = self::getTaxonOccurrenceStats($tid);
        $taxa_descriptions = self::getTaxaDescriptions($tid);
        $external_links = self::getExternalLinks($tid);

        return view('pages/taxon/profile', [
            'taxon' => $taxon,
            'parents' => $parents,
            'common_names' => $common_names,
            'occurrence_count' => $occurrence_count,
            'children' => $children,
            'taxa_descriptions' => $taxa_descriptions,
            'external_links' => $external_links,
        ]);
    }

    public static function taxonEdit(int $tid) {
        $taxon = self::taxonData($tid);

        $parents = self::getParents($tid);

        $common_names = self::getCommonNames($tid);
        $children = self::getDirectChildren($tid);

        $occurrence_count = self::getTaxonOccurrenceStats($tid);
        $taxa_descriptions = self::getTaxaDescriptions($tid);
        $external_links = self::getExternalLinks($tid);

        $taxa_media = DB::table('media')
            ->where('tid', $tid)
            ->select('*')
            ->orderBy('sortSequence')
            ->get();

        return view('pages/taxon/edit', [
            'taxon' => $taxon,
            'parents' => $parents,
            'common_names' => $common_names,
            'occurrence_count' => $occurrence_count,
            'children' => $children,
            'taxa_descriptions' => $taxa_descriptions,
            'external_links' => $external_links,
            'media' => $taxa_media,
        ]);
    }

    public static function creationPage(Request $request) {
        // $collections = DB::table('omcollections')->select('*')->get();

        return view('pages/taxonomy/taxonomycreator', []);
    }
}
