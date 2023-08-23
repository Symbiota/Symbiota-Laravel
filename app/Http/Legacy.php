<?php
// This is assuming the entry point to the legacy app is at `legacy/index.php`
$path = request()->path();

$_SERVER["QUERY_STRING"] = request()->getQueryString();

//Defines Symbini and Symbase variables as Globals in Laravel Scope
include_once(base_path('config') . '/symbini_globals.php');

//Includes Symbini and Symbbase Files in every legacy call
include_once(base_path('legacy') . '/config/symbini.php');

if($path == '' || $path == '/') {
    require base_path('legacy') . '/index.php';
} else {
    $parts = explode(".", $path);

    //If path containts . then assume its pointing at a file
    if(count($parts) <= 1) $path = $path.'/index.php';

    require base_path('legacy') . '/'. $path;
}
