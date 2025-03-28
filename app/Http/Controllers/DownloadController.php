<?php

namespace App\Http\Controllers;

use App\Core\Download\AttributeTraits;
use App\Core\Download\DarwinCore;
use App\Core\Download\Determinations;
use App\Core\Download\Identifiers;
use App\Core\Download\Multimedia;
use App\Core\Download\SymbiotaNative;
use App\Models\Collection;
use App\Models\Occurrence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class DownloadController extends Controller {
    public static function getHigherClassification($tid) {
        $taxa_info = DB::select("
            select t.*, flat_enum_tree.taxa_enums from taxa as t INNER JOIN taxstatus ts ON t.tid = ts.tid
            JOIN (
                SELECT e.tid ,group_concat(CONCAT(t.sciName, ':', t.rankid) ORDER BY t.rankid) as taxa_enums
                    FROM taxaenumtree e
                    INNER JOIN taxa t ON e.parentTid = t.tid
                    WHERE e.taxauthid = 1
                    AND e.tid in (?)
                group by e.tid
            ) as flat_enum_tree on flat_enum_tree.tid = t.tid
            where t.tid in (?) and ts.taxauthid = 1
            ", [$tid, $tid]);

        $result = [];

        foreach ($taxa_info as $key => $value) {
            $ident_tree = [];
            foreach (explode(',', $value->taxa_enums) as $idx => $ident) {
                [$rankName, $rankID] = explode(':', $ident);

                $ident_tree[] = $rankName;

                $order_name = match (intval($rankID)) {
                    10 => 'kingdom',
                    30 => 'phylum',
                    60 => 'class',
                    100 => 'order',
                    140 => 'family',
                    190 => 'subgenus',
                    default => false,
                };

                if ($order_name) {
                    $result[$value->tid][$order_name] = $rankName;
                }
            }

            $result[$value->tid]['higherClassification'] = implode('|', $ident_tree);
            //$result[$value->tid]['unitInd3'] = $value->unitInd3;
            $result[$value->tid]['cultivarEpithet'] = $value->cultivarEpithet;
            $result[$value->tid]['tradeName'] = $value->tradeName;
        }

        return $result;
    }

    public static function downloadPage(Request $request) {
        $params = $request->except(['page', '_token']);

        return view('pages/collections/download');
    }

    public static function downloadFile(Request $request) {
        $params = $request->except(['page', '_token']);

        // TODO (Logan) Add Manditory params to prevent huge query
        if (empty($params['collid'])) {
            return back()->withErrors(['Missing required parameter: collid']);
        }

        $SCHEMA = SymbiotaNative::class;
        if (request('schema') === 'dwc') {
            $SCHEMA = DarwinCore::class;
        }

        //Setup File Names
        $fileNames = [];

        [$file_delimiter, $file_extension] = match (request('file_format')) {
            'tsv' => ["\t", 'tsv'],
            default => [',', 'csv']
        };

        $file_charset = match (request('charset')) {
            'ISO-8859-1' => 'ISO-8859-1',
            default => 'UTF-8'
        };

        $encodeArr = fn ($v) => mb_convert_encoding($v, $file_charset);

        if (request('compressed')) {
            $fileNames['occurrence'] = 'occurrence.csv';
            $fileNames['eml'] = 'eml.xml';
            $fileNames['meta'] = 'meta.xml';
            $fileNames['CITEME'] = 'CITEME.txt';

            if (request('include_determination_history')) {
                $fileNames['identifications'] = 'identifications.' . $file_extension;
            }

            if (request('include_media')) {
                $fileNames['multimedia'] = 'multimedia.' . $file_extension;
            }

            if (request('include_alternative_identifers')) {
                $fileNames['identifiers'] = 'identifiers.' . $file_extension;
            }

            if (request('include_occurrence_trait_attributes')) {
                $fileNames['measurementOrFact'] = 'measurementOrFact.' . $file_extension;
            }
        } else {
            $fileNames['occurrence'] = time() . '-occurrence' . $file_extension;
        }

        //Build Occurrence Query
        $query = Occurrence::buildSelectQuery($request->all());
        if (array_key_exists('associatedSequences', $SCHEMA::$fields)) {
            $geneticsQuery = DB::table('omoccurgenetic')->selectRaw(
                "occid as gen_occid, group_concat(CONCAT_WS(', ', resourcename, title, identifier, locus, resourceUrl) SEPARATOR ' | ') as associatedSequences"
            )->groupBy('occid');
            $query->leftJoinSub($geneticsQuery, 'gen', 'gen.gen_occid', 'o.occid');
        }

        $taxa = [];
        $collids = [];
        $files = [];

        foreach ($fileNames as $key => $fileName) {
            $files[$key] = fopen($fileName, 'w');
        }

        // Define File Schemas
        $file_schemas = [
            'occurrence' => $SCHEMA,
            'multimedia' => Multimedia::class,
            'identifiers' => Identifiers::class,
        ];

        //Write CSV Headers
        foreach ($file_schemas as $file_name => $file_schema) {
            if (array_key_exists($file_name, $files)) {
                if ($file_charset != 'UTF-8') {
                    fputcsv(
                        $files[$file_name],
                        array_map($encodeArr, array_keys($file_schema::$fields)),
                        $file_delimiter
                    );
                }
            }
        }

        //Write Meta data
        if (array_key_exists('meta', $files)) {
            fwrite($files['meta'], view('xml/download/meta', [
                'encoding' => $file_charset,
            ])->render());
        }

        $query
            //This select order matters when dealing with conflicting attribute names
            ->select(['c.*', 'gen.*', 'o.*'])
            ->orderBy('o.occid')
            ->chunk(1000, function (\Illuminate\Support\Collection $occurrences) use (&$files, &$taxa, $SCHEMA, &$collids, $file_delimiter, &$encodeArr) {
                // Process Occurrence Data
                $occids = [];
                foreach ($occurrences as $occurrence) {
                    array_push($occids, $occurrence->occid);

                    if ($occurrence->tidInterpreted) {
                        if (! array_key_exists($occurrence->tidInterpreted, $taxa)) {
                            $taxa[$occurrence->tidInterpreted] = self::getHigherClassification($occurrence->tidInterpreted)[$occurrence->tidInterpreted];
                        }
                    }

                    if ($occurrence->collid && ! array_key_exists($occurrence->collid, $collids)) {
                        array_push($collids, $occurrence->collid);
                    }

                    $unmapped_row = array_merge(
                        (array) $occurrence,
                        $occurrence->tidInterpreted && array_key_exists($occurrence->tidInterpreted, $taxa) ? $taxa[$occurrence->tidInterpreted] : []
                    );

                    $row = $SCHEMA::map_row($unmapped_row);

                    fputcsv($files['occurrence'], array_map($encodeArr, (array) $row), $file_delimiter);
                }

                //Process Media
                if (array_key_exists('multimedia', $files)) {
                    $occ_media = DB::table('media')->select('*')->whereIn('occid', $occids)->get();
                    foreach ($occ_media as $media_row) {
                        $row = Multimedia::map_row((array) $media_row);
                        fputcsv($files['multimedia'], array_map($encodeArr, (array) $row), $file_delimiter);
                    }
                }

                //Process identifiers
                if (array_key_exists('identifiers', $files)) {
                    $occ_identifiers = DB::table('omoccuridentifiers')->select('*')->whereIn('occid', $occids)->get();
                    foreach ($occ_identifiers as $identifier_row) {
                        $row = Identifiers::map_row((array) $identifier_row);
                        fputcsv($files['identifiers'], array_map($encodeArr, (array) $row), $file_delimiter);
                    }
                }

                //Process identifications
                if (array_key_exists('identifications', $files)) {
                    $occ_determinations = DB::table('omoccurdeterminations')->select('*')->whereIn('occid', $occids)->get();
                    foreach ($occ_determinations as $determination_row) {
                        $row = Determinations::map_row((array) $determination_row);
                        fputcsv($files['identifications'], array_map($encodeArr, (array) $row), $file_delimiter);
                    }
                }

                //Process measurementOrFact Not being used currently
                if (array_key_exists('measurementOrFact', $files)) {
                    $occ_attribute_traits = [];
                    foreach ($occ_attribute_traits as $attribute_trait_row) {
                        $row = AttributeTraits::map_row((array) $attribute_trait_row);
                        fputcsv($files['measurementOrFact'], array_map($encodeArr, (array) $row), $file_delimiter);
                    }
                }
            });

        //Write EML data
        if (array_key_exists('eml', $files)) {
            fwrite($files['eml'], view('xml/download/eml', [
                'encoding' => $file_charset,
                'lang' => 'eng',
                'collections' => Collection::query()->whereIn('collid', $collids)->select('*')->get(),
            ])->render());
        }

        //Close all working files
        foreach ($files as $key => $file) {
            fclose($file);
        }

        if (request('compressed')) {
            $zipArchive = new ZipArchive();
            $archiveFileName = 'SymbOuput_' . date('Y-m-d_His') . '_DwC-A.zip';
            if (! ($status = $zipArchive->open($archiveFileName, ZipArchive::CREATE))) {
                exit('FATAL ERROR: unable to create archive file: ' . $status);
            }

            foreach ($fileNames as $key => $file) {
                $zipArchive->addFile($file);
            }

            $zipArchive->close();

            //Delete All working files
            foreach ($fileNames as $key => $file) {
                unlink($file);
            }

            return response()->download(public_path($archiveFileName))->deleteFileAfterSend(true);
        } else {
            return response()->download(public_path($fileNames['occurrence']))->deleteFileAfterSend(true);
        }
    }
}
