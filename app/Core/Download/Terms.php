<?php

namespace App\Core\Download;

class Terms {
    const SYMBIOTA = 'https://symbiota.org/terms/';
    const DARWIN_CORE = 'https://rs.tdwg.org/dwc/terms/';
    const DUBLIN_CORE = 'http://purl.org/dc/terms/';
    const AUDIO_VISUAL_CORE = 'http://rs.tdwg.org/ac/terms/';
    const ADOBE = 'http://ns.adobe.com/xap/1.0/rights/Owner';
    const IDIGBIO = 'http://portal.idigbio.org/terms/';
    const OBIS = 'http://rs.iobis.org/obis/terms/';

    public static function genTerm(string $base_url, string $term_name): string {
        return $base_url . $term_name;
    }
}
