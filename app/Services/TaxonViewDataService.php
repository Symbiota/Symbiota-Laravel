<?php

namespace App\Services;

use App\Models\Taxonomy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TaxonViewDataService {
    public static function buildTaxonViewData(int $tid, bool $includeMedia = false): array {
        $taxonomy = Taxonomy::find($tid);
        $viewData = [
            'taxon' => TaxonomyQueryService::taxonData($tid),
            'parents' => TaxonomyQueryService::getParents($tid),
            'common_names' => $taxonomy?->commonNames()->get() ?? collect(),
            'occurrence_count' => $taxonomy?->occurrences()->count() ?? 0,
            'children' => TaxonomyQueryService::getDirectChildren($tid, 1),
            'taxa_descriptions' => TaxonomyQueryService::getTaxaDescriptions($tid),
            'external_links' => $taxonomy?->externalLinks()->get() ?? collect(),
        ];

        if ($includeMedia) {
            $viewData['media'] = DB::table('media')
                ->where('tid', $tid)
                ->select('*')
                ->orderBy('sortSequence')
                ->get();
        }

        return $viewData;
    }

    public static function buildTaxonFormOptions(): array {
        $kingdoms = DB::table('taxa')->where('rankID', 10)->select('tid', 'sciName')->get();
        $primaryKingdom = config('portal.primary_taxonomic_kingdom');
        $allTaxonRanks = DB::table('taxonunits')->distinct()->select('rankid', 'rankname')->where('kingdomName', $primaryKingdom)->orderBy('rankid')->orderBy('rankname', 'desc')->get();
        $indContent = [['title' => '', 'value' => '', 'disabled' => false], ['title' => '×', 'value' => '×', 'disabled' => false]];
        $securityOptions = [['title' => 'No Security', 'value' => 0, 'disabled' => false], ['title' => 'Hide Locality Details', 'value' => 1, 'disabled' => false]];
        ! empty($GLOBALS['ACTIVATE_PALEO_DAGGER']) ? $indContent[] = ['title' => '†', 'value' => '†', 'disabled' => false] : null;

        return [
            'kingdoms' => $kingdoms,
            'allTaxonRanks' => $allTaxonRanks,
            'indContent' => $indContent,
            'securityOptions' => $securityOptions,
            'canCreateOrEdit' => Gate::check('TAXON_EDITOR'),
        ];
    }

    public static function prepareUpperTaxonomyEditInfo($taxonEditorObj): array {
        $upperTaxonomyEditInfo = [];
        $upperTaxonomyEditInfo['acceptedArr'] = $taxonEditorObj->getAcceptedArr();
        $upperTaxonomyEditInfo['tid'] = $taxonEditorObj->getTid();
        $upperTaxonomyEditInfo['isAccepted'] = $taxonEditorObj->getIsAccepted();
        $upperTaxonomyEditInfo['parentNameFull'] = strip_tags(html_entity_decode((string) ($taxonEditorObj->getParentNameFull() ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $upperTaxonomyEditInfo['rankId'] = $taxonEditorObj->getRankId();
        $upperTaxonomyEditInfo['family'] = $taxonEditorObj->getFamily();
        $upperTaxonomyEditInfo['parentTid'] = $taxonEditorObj->getParentTid();
        $upperTaxonomyEditInfo['parentName'] = $taxonEditorObj->getParentName();
        $upperTaxonomyEditInfo['taxauthid'] = $taxonEditorObj->getTaxauthid();

        return $upperTaxonomyEditInfo;
    }
}
