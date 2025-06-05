<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
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
        $dataset = Dataset::query()->where('datasetID', $dataset_id)->first();

        return view('pages/datasets/profile', [
            'dataset' => $dataset
        ]);
    }
}
