<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatasetController extends Controller {
    public static function createDataset(Request $request) {
        $user = $request->user();

        $new_dataset = new Dataset();
        $new_dataset->fill($request->all());
        $new_dataset->uid = $user->uid;
        $new_dataset->save();

        return view('pages/user/profile', ['user' => $user])->fragment('datasets');
    }

    public static function datasetProfilePage(int $dataset_id) {
        $user = request()->user();
        $dataset = Dataset::query()
            ->leftJoin('userroles as ur', 'ur.uid', DB::raw('?', $user->uid))
            ->where('datasetID', $dataset_id)
            ->where(function($query) use($user) {
                $query
                    ->orWhere('role', UserRole::SUPER_ADMIN)
                    ->orWhere('omoccurdatasets.uid', $user->uid)
                    ->orWhere(function($query) {
                        $query->whereRaw('tablePK = datasetID')
                            ->whereIn('role', [
                                UserRole::DATASET_ADMIN,
                                UserRole::DATASET_EDITOR
                        ]);
                    });
            });

        dd($dataset->toRawSql());
        $dataset = $dataset->first();

        return view('pages/datasets/profile', [
            'dataset' => $dataset
        ]);
    }
}
