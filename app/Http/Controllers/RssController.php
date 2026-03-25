<?php

namespace App\Http\Controllers;

class RssController extends Controller {
    protected $rssFeedGenerator;

    public function show() {

        // TODO implement cache
        // Try to get the RSS feed from cache
        //$rssXml = cache()->remember('rss_feed', 60, function () {
        //    return $this->$rssFeedGenerator->getFullRss();
        //});

        global $SERVER_ROOT;
        include_once legacy_path('/classes/DwcArchiverCore.php');

        $rssFeedGenerator = new \DwcArchiverCore();
        $rssXml = $rssFeedGenerator->getFullRss();

        // Return the RSS feed as XML with the appropriate content-type header
        return response($rssXml, 200)->header('Content-Type', 'application/xml');
    }
}
