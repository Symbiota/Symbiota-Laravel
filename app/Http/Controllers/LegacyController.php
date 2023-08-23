<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;

class LegacyController extends Controller {

    public function __invoke(): Response {
        ob_start();

        //Handles Legacy File Routing (Pretty Jankily)
        require app_path('Http') . '/Legacy.php';

        $output = ob_get_clean();

        // be sure to import Illuminate\Http\response
        $response = new Response($output);

        foreach(headers_list() as $headerStr) {
            $header_parts = explode(":", $headerStr);
            if(count($header_parts) > 1 && $header_parts[0] == "Location") {
                $response->setStatusCode('302');
            }
        }

        //Return Proper Content Types for css and js files
        //Note these are the only supported mime types currently
        if(substr(request()->path(), -3) == ".js") {
            $response->header("Content-Type", "text/javascript");
        } else if(substr(request()->path(), -4) == ".css") {
            $response->header("Content-Type", "text/css");
        } else if(substr(request()->path(), -4) == ".jpg") {
            $response->header("Content-Type", "image/jpeg");
        } else if(substr(request()->path(), -4) == ".png") {
            $response->header("Content-Type", "image/png");
        } else if(substr(request()->path(), -4) == ".svg") {
            $response->header("Content-Type", "image/svg+xml");
        }

        //Don't Session Legacy Content
        Cookie::queue(Cookie::forget('PHPSESSID'));
        setcookie('PHPSESSID', null, time() -3600, request()->path(), false);

        return $response;
    }
}
