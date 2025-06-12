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
        $dataset_query = Dataset::query()->where('datasetID', $dataset_id);

        if ($user) {
            $dataset_query
                ->leftJoin('userroles as ur', 'ur.uid', DB::raw($user->uid))
                ->where(function ($query) use ($user) {
                    $query->where(function ($query) use ($user) {
                        $query
                            ->where('omoccurdatasets.uid', $user->uid)
                            ->orwhere('role', UserRole::SUPER_ADMIN)
                            ->orwhere(function ($query) {
                                $query
                                    ->whereIn('role', [UserRole::DATASET_ADMIN, UserRole::DATASET_EDITOR])
                                    ->where('tablePK', 'datasetID');
                            });
                    })
                        ->orWhere('isPublic', 1);
                });

        } else {
            $dataset_query->where('isPublic', 1);
        }

        $dataset = $dataset_query->first();

        return view('pages/datasets/profile', [
            'dataset' => $dataset,
        ]);
    }
}
