<?php
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables as IlluminateLoadEnvironmentVariables;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

//Manually Bootstrap env so that we can use the variables in legacy code
(new IlluminateLoadEnvironmentVariables)->bootstrap($app);

/*
|--------------------------------------------------------------------------
| Load Legacy Routing
|--------------------------------------------------------------------------
|
| Not all the code is ported over to laravel so some extra code is needed
| to handle the legacy routes. This is handled here at the entrypoint
| because if use laravel routes would strip the legacy globals and porting
| them would cause potential undefined behavior. Another option of running
| the Legacy code via the public folder was considered but this had an
| on conflicting routes and lack of fine grain route overrides.
|
*/

/* Routes that we want to fall through to laravel implementation */
$legacy_routes = [
    'index.php' => '/',
    //'sitemap.php' => '/sitemap',
];

/* Generate Legacy Redirects to Laravel */
$legacy_black_list = [];
foreach ($legacy_routes as $route => $redirect) {
    $legacy_black_list['/' . $_ENV['PORTAL_NAME'] . '/' . $route] = $redirect;
}

/* Parse URI */
$query_pos = strpos($_SERVER['REQUEST_URI'], '?');
$uri = $query_pos?
    substr($_SERVER['REQUEST_URI'], 0, $query_pos):
    $_SERVER['REQUEST_URI'];

/* Clean out host url if present */
$https = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://');
$app_url = str_replace($https, '', $_ENV["APP_URL"]);
$uri = str_replace($app_url, '', $uri);

$mime_types = [
    'aac' => 'audio/aac',
    'abw' => 'application/x-abiword',
    'arc' => 'application/x-freearc',
    'avif' => 'image/avif',
    'avi' => 'video/x-msvideo',
    'azw' => 'application/vnd.amazon.ebook',
    'bin' => 'application/octet-stream',
    'bmp' => 'image/bmp',
    'bz' => 'application/x-bzip',
    'bz2' => 'application/x-bzip2',
    'cda' => 'application/x-cdf',
    'csh' => 'application/x-csh',
    'css' => 'text/css',
    'csv' => 'text/csv',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'eot' => 'application/vnd.ms-fontobject',
    'epub' => 'application/epub+zip',
    'gz' => 'application/gzip',
    'gif' => 'image/gif',
    'htm' => 'text/html',
    'html' => 'text/html',
    'ico' => 'image/vnd.microsoft.icon',
    'ics' => 'text/calendar',
    'jar' => 'application/java-archive',
    'jpeg' => 'image/jpeg',
    'jpg' => 'image/jpeg',
    'js' => 'text/javascript',
    'json' => 'application/json',
    'jsonld' => 'application/ld+json',
    'mid' => 'audio/midi audio/x-midi',
    'midi' => 'audio/midi audio/x-midi',
    'mjs' => 'text/javascript',
    'mp3' => 'audio/mpeg',
    'mp4' => 'video/mp4',
    'mpeg' => 'video/mpeg',
    'mpkg' => 'application/vnd.apple.installer+xml',
    'odp' => 'application/vnd.oasis.opendocument.presentation',
    'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    'odt' => 'application/vnd.oasis.opendocument.text',
    'oga' => 'audio/ogg',
    'ogv' => 'video/ogg',
    'ogx' => 'application/ogg',
    'opus' => 'audio/opus',
    'otf' => 'font/otf',
    'png' => 'image/png',
    'pdf' => 'application/pdf',
    'php' => 'application/x-httpd-php',
    'ppt' => 'application/vnd.ms-powerpoint',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'rar' => 'application/vnd.rar',
    'rtf' => 'application/rtf',
    'sh' => 'application/x-sh',
    'svg' => 'image/svg+xml',
    'swf' => 'application/x-shockwave-flash',
    'tar' => 'application/x-tar',
    'tif' => 'image/tiff',
    'tiff' => 'image/tiff',
    'ts' => 'video/mp2t',
    'ttf' => 'font/ttf',
    'txt' => 'text/plain',
    'vsd' => 'application/vnd.visio',
    'wav' => 'audio/wav',
    'weba' => 'audio/webm',
    'webm' => 'video/webm',
    'webp' => 'image/webp',
    'woff' => 'font/woff',
    'woff2' => 'font/woff2',
    'xhtml' => 'application/xhtml+xml',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'xml' => 'text/xml',
    'xul' => 'application/vnd.mozilla.xul+xml',
    'zip' => 'application/zip',
    '3gp' => 'video/3gpp',
    '3g2' => 'video/3gpp2',
    '7z' => 'application/x-7z-compressed'
];

if($blacklist_redirect = $legacy_black_list[$uri]) {
    header('Location:' . $blacklist_redirect);
} else if(preg_match("/^\/Portal.*\.(.*)/", $uri, $matches)) {
    try {
        [$path, $file_type] = $matches;
        if($file_type === "php"){
            include_once(__DIR__ . '/../' . $_ENV['PORTAL_NAME'] . '/config/symbini.php');
            include_once(__DIR__ . '/..' . $uri);
        } else if($mime = $mime_types[$file_type]) {
            header("Content-Type: " . $mime);
            echo file_get_contents('/var/www/html' . $uri);
        }
    } catch(Throwable $e) {
        echo $e->getMessage();
    }

// Do Laravel stuff if legacy route doesn't exist or its black listed
} else {
$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
}
