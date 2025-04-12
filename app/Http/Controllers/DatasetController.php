<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Http\Request;

class DatasetController extends Controller {
    public static function createDataset(Request $request) {
        $user = $request->user();

        $new_dataset = new Dataset();
        $new_dataset->fill($request->all());
        $new_dataset->uid = $user->uid;
        $new_dataset->save();

        return view('pages/user/profile', ['user' => $user])->fragment('datasets');
    }
}
