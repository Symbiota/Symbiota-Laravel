<?php

namespace App\Services;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class TaxonomyQueryService {
    public static function taxonData(int $tid) {
        return DB::table('taxa as t')
            ->leftJoin('taxstatus as ts', 'ts.tid', 't.tid')
            ->where('t.tid', $tid)
            ->where('ts.taxauthid', 1)
            ->select(
                't.*',
                'ts.tidaccepted',
                'ts.taxauthid',
                'ts.parenttid',
                'ts.family',
                'ts.taxonomicStatus',
                'ts.taxonomicSource',
                'ts.sourceIdentifier',
                'ts.UnacceptabilityReason',
                'ts.notes as statusNotes',
                'ts.SortSequence',
                'ts.modifiedUid as statusModifiedUid',
                'ts.modifiedTimestamp as statusModifiedTimestamp',
                'ts.initialtimestamp as statusInitialTimestamp'
            )
            ->first();
    }

    public static function getParents(int $tid): array {
        return DB::select('with RECURSIVE parents as (
	SELECT * from taxstatus where tid = ?
	UNION ALL
	SELECT ts.* from taxstatus as ts, parents as p where ts.tid = p.parenttid and ts.taxauthid = 1 and ts.tid != 1
) SELECT DISTINCT taxa.tid, sciName, parents.family, parenttid, taxa.rankID, rankname
            from parents join taxa on taxa.tid = parents.tid join taxonunits on taxonunits.rankid = taxa.rankID and taxa.kingdomName = taxonunits.kingdomName order by taxa.rankID', [$tid]);
    }

    public static function getDirectChildren(int $tid, int $displayAuthor = 0) {
        $query = DB::table('taxa as t')
            ->join('taxstatus as ts', 'ts.tid', 't.tid')
            ->leftJoin('media as m', function (JoinClause $join) {
                $join->on('m.tid', 't.tid')
                    ->where('m.mediaType', 'image');
            })
            ->join('taxonunits as tu', function (JoinClause $join) {
                $join->on('tu.rankid', 't.rankID')
                    ->whereRaw('tu.kingdomName = t.kingdomName');
            })
            ->where('ts.taxauthid', 1)
            ->where('ts.parenttid', $tid)
            ->groupBy('t.tid')
            ->select(array_filter(['t.tid', 'sciName', $displayAuthor ? 't.author' : null, 'ts.family', 'parenttid', 't.rankID', 'rankname', DB::raw('COALESCE(m.thumbnailUrl, m.url) as thumbnailUrl')]));

        $direct_children = $query->get();

        foreach ($direct_children as $child) {
            if (! $child->thumbnailUrl) {
                DB::table('media')->where($child->tid);
            }
        }

        return $direct_children;
    }

    // Be very careful when calling this function — can be very slow depending on the tid
    public static function getAllChildren(int $tid): array {
        return DB::select('with RECURSIVE children as (
	SELECT * from taxstatus where parenttid = ?
	UNION ALL
	SELECT ts.* from taxstatus as ts, children as c where ts.parenttid = c.tid and ts.taxauthid = 1 and ts.tidaccepted = ts.tid
) SELECT taxa.tid, sciName, children.family, parenttid, taxa.rankID, rankname, COALESCE(m.thumbnailUrl, m.url) as thumbnailUrl
            from children join taxa on taxa.tid = children.tid left join media as m on m.tid = taxa.tid join taxonunits on taxonunits.rankid = taxa.rankID and taxa.kingdomName = taxonunits.kingdomName group by taxa.tid order by taxa.rankID', [$tid]);
    }

    public static function getTaxaDescriptions(int $tid): array {
        $statements = DB::table('taxadescrblock as tdb')
            ->join('taxadescrstmts as tds', 'tds.tdbid', 'tdb.tdbid')
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
}
