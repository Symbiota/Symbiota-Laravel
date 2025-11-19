<?php

// ENV Bridge from laravel config
// Default language
$DEFAULT_LANG = getenv('PORTAL_DEFAULT_LANG');
$DEFAULT_PROJ_ID = getenv('PORTAL_PROJECT_ID');
$DEFAULTCATID = getenv('PORTAL_DEFAULTCATID');
$DEFAULT_TITLE = getenv('PORTAL_DEFAULT_TITLE');

// Add all languages you want to support separated by commas (e.g. en,es);
// currently supported languages: en,es
$EXTENDED_LANG = getenv('PORTAL_EXTENDED_LANG');

$TID_FOCUS = getenv('PORTAL_TID_FOCUS');

// This is the email address used to contact the primary on this portal
$ADMIN_EMAIL = getenv('PORTAL_ADMIN_EMAIL');

// This email address is used for system notifications
// (password reset requests, etc...)
// ex: noreply@yourdomain.edu
$SYSTEM_EMAIL = getenv('PORTAL_SYSTEM_EMAIL');

// ISO-8859-1 or UTF-8
$CHARSET = getenv('PORTAL_CHARSET');

// Typically a UUID
$PORTAL_GUID = getenv('PORTAL_GUID');

// Typically a UUID used to verify access to certain web service
$SECURITY_KEY = getenv('SECURITY_KEY');

// fully qualified domain name or IP address of the server. e.g. 'symbiota.org'
// or 'localhost'
$SERVER_HOST = getenv('APP_URL');

// URL path to project root folder (relative path w/o domain, e.g. '/seinet')
$CLIENT_ROOT = getenv('PORTAL_USE_CLIENT_ROOT') === 'true' ? '/' . getenv('PORTAL_NAME') : '';

// Full path to Symbiota project root folder
$SERVER_ROOT = '/var/www/html/' . getenv('PORTAL_NAME');

// Must be writable by Apache; will use system default if not specified
$TEMP_DIR_ROOT = $SERVER_ROOT . '/temp';

// Must be writable by Apache; will use <SYMBIOTA_ROOT>/temp/logs if not specified
$LOG_PATH = $SERVER_ROOT . '/content/logs';

// Path to CSS files
$CSS_VERSION_RELEASE = getenv('PORTAL_CSS_VERSION_RELEASE');
$CSS_BASE_PATH = $SERVER_HOST . $CLIENT_ROOT . '/css/';

// Path to user uploaded images files.  Used by tinyMCE. This is NOT for
// collection images. See section immediatly below for collection image location
$PUBLIC_IMAGE_UPLOAD_ROOT = '/content/imglib';

// the root for the collection image directory
// Domain path to images, if different from portal
$IMAGE_DOMAIN = '';
// URL path to images
$IMAGE_ROOT_URL = $CLIENT_ROOT . '/temp/images';
// Writable path to images, especially needed for downloading images
$IMAGE_ROOT_PATH = $TEMP_DIR_ROOT . '/images';

// Domain path to images, if different from portal
$ACCESSIBILITY_ACTIVE = getenv('PORTAL_ACCESSIBILITY_ACTIVE');

// Pixel width of web images
$IMG_WEB_WIDTH = getenv('PORTAL_IMG_WEB_WIDTH') ?? 1400;
$IMG_TN_WIDTH = getenv('PORTAL_IMG_TN_WIDTH') ?? 200;
$IMG_LG_WIDTH = getenv('PORTAL_IMG_LG_WIDTH') ?? 3200;

// Files above this size limit and still within pixel width limits will still be
// resaved w/ some compression
$IMG_FILE_SIZE_LIMIT = getenv('PORTAL_IMG_FILE_SIZE_LIMIT') ?? 900000;

// Path used to map/import images uploaded to the iPlant image server
// e.g. /home/shared/project-name/--INSTITUTION_CODE--/,
// the --INSTITUTION_CODE-- text will be replaced with collection's institution
// code
$IPLANT_IMAGE_IMPORT_PATH = getenv('IPLANT_IMAGE_IMPORT_PATH') ?? '';

// 1 = ImageMagick resize images, given that it's installed
// (faster, less memory intensive)
$USE_IMAGE_MAGICK = getenv('PORTAL_USE_IMAGE_MAGICK') ?? 0;
// Needed for OCR function in the occurrence editor page
$TESSERACT_PATH = getenv('PORTAL_TESSERACT_PATH') ?? '';
$NLP_LBCC_ACTIVATED = getenv('PORTAL_NLP_LBCC_ACTIVATED') ?? 0;
$NLP_SALIX_ACTIVATED = getenv('PORTAL_NLP_SALIX_ACTIVATED') ?? 0;

// Module activations
$OCCURRENCE_MOD_IS_ACTIVE = getenv('PORTAL_OCCURRENCE_MOD_IS_ACTIVE') ?? 1;
$FLORA_MOD_IS_ACTIVE = getenv('PORTAL_FLORA_MOD_IS_ACTIVE') ?? 1;
$KEY_MOD_IS_ACTIVE = getenv('PORTAL_KEY_MOD_IS_ACTIVE') ?? 1;

// Configurations for publishing to GBIF
// GBIF username which portal will use to publish
$GBIF_USERNAME = getenv('GBIF_USERNAME') ?? '';
// GBIF password which portal will use to publish
$GBIF_PASSWORD = getenv('GBIF_PASSWORD') ?? '';
// GBIF organization key for organization which is hosting this portal
$GBIF_ORG_KEY = getenv('GBIF_ORG_KEY') ?? '';

// Misc variables
// Default taxonomic search type:
// 1 = Any Name, 2 = Scientific Name, 3 = Family, 4 = Taxonomic Group, 5 = Common Name
$DEFAULT_TAXON_SEARCH = 2;
$SHOULD_USE_MINIMAL_MAP_HEADER = false;

// Needed for Google Map; get from Google
$GOOGLE_MAP_KEY = '';
$MAPBOX_API_KEY = '';

// Display Static Map thumbnails within taxon profile, checklist, etc
$MAP_THUMBNAILS = false;
$ACTIVATE_PALEO = false;

$STORE_STATISTICS = 0;
// Project bounding box; default map centering; (e.g. 42.3;-100.5;18.0;-127)
$MAPPING_BOUNDARIES = getenv('PORTAL_MAPPING_BOUNDARIES');
// Activates HTML5 geolocation services in Map Search
$ACTIVATE_GEOLOCATION = false;
// Needed for setting up Google Analytics
$GOOGLE_ANALYTICS_KEY = '';
// Needed for setting up Google Analytics 4 Tag ID
$GOOGLE_ANALYTICS_TAG_ID = '';
// Now called site key
$RECAPTCHA_PUBLIC_KEY = '';
// Now called secret key
$RECAPTCHA_PRIVATE_KEY = '';

// List of taxonomic authority APIs to use in data cleaning and thesaurus building
// tools, concatenated with commas and order by preference; E.g.:
// array(
// 'COL'=>'', 'WoRMS'=>'', 'bryonames' => '', 'fdex'=>'', 'TROPICOS'=>'', 'EOL'=>''
// )
$TAXONOMIC_AUTHORITIES = ['COL' => '', 'WoRMS' => ''];
// Allows quick entry for host taxa in occurrence editor
$QUICK_HOST_ENTRY_IS_ACTIVE = 0;
// Banner image for glossary exports. Place in images/layout folder.
$GLOSSARY_EXPORT_BANNER = '';
// Controls size of concentric rings that are sampled when building Dynamic Checklist
$DYN_CHECKLIST_RADIUS = 10;
// Display common names in species profile page and checklists displays
$DISPLAY_COMMON_NAMES = 1;
// Activates Specimen Duplicate listings and support features. Mainly relavent
// for herabrium collections
$ACTIVATE_DUPLICATES = 1;
// Activates exsiccati fields within data entry pages; adding link to exsiccati
// search tools to portal menu is recommended
$ACTIVATE_EXSICCATI = 1;
// Activates GeoLocate Toolkit located within the Processing Toolkit menu items
$ACTIVATE_GEOLOCATE_TOOLKIT = true;
// Activates search fields for searching by traits (if trait data have been encoded):
// 0 = trait search off; any number of non-zeros separated by commas
// (e.g., '1,6') = trait search on for the traits with these id numbers in table
// tmtraits.
$SEARCH_BY_TRAITS = 1;

// Activates polar plots, in taxon profile, of the trait states listed:
// 0 = no plot; any number of non-zeros separated by commas (e.g., '1,6') = plots
// appear for the trait states with these id numbers (in table tmstates).
$CALENDAR_TRAIT_PLOTS = '1';

$IGSN_ACTIVATION = 0;

// Host is requiered, others are optional and can be removed
//$SMTP_ARR = array('host'=>'','port'=>587,'username'=>'','password'=>'','timeout'=>60);
$RIGHTS_TERMS = [
    'CC0 1.0 (Public-domain)' => 'https://creativecommons.org/publicdomain/zero/1.0/',
    'CC BY (Attribution)' => 'https://creativecommons.org/licenses/by/4.0/',
    'CC BY-NC (Attribution-Non-Commercial)' => 'https://creativecommons.org/licenses/by-nc/4.0/',
    'CC BY-NC-SA (Attribution-NonCommercial-ShareAlike)' => 'https://creativecommons.org/licenses/by-nc-sa/4.0/',
];

$EDITOR_PROPERTIES = [
'modules-panel' => [
'paleo' => ['status' => 1, 'titleOverride' => 'Paleonotology Terms'],
],
'features' => ['catalogDupeCheck' => 1, 'otherCatNumDupeCheck' => 0, 'dupeSearch' => 1],
'labelOverrides' => [],
'cssTerms' => [
'#recordNumberDiv' => ['float' => 'left', 'margin-right' => '2px'],
'#recordNumberDiv input' => ['width' => '60px'],
'#eventDateDiv' => ['float' => 'left'],
'#eventDateDiv input' => ['width' => '110px'],
],
'customCSS' => [],
'customLookups' => [
'processingStatus' => ['Unprocessed', 'Stage 1', 'Stage 2', 'Pending Review', 'Expert Required', 'Reviewed', 'Closed'],
],
];

/*
 //Default editor properties; properties defined in collection will override these values
 $EDITOR_PROPERTIES = array(
 'modules-panel' => array(
 'paleo' => array('status'=>0,'titleOverride'=>'Paleonotology Terms')
 ),
 'features' => array('catalogDupeCheck'=>1,'otherCatNumDupeCheck'=>0,'dupeSearch'=>1),
 'labelOverrides' => array(),
 'cssTerms' => array(
 '#recordNumberDiv'=>array('float'=>'left','margin-right'=>'2px'),
 '#recordNumberDiv input'=>array('width'=>'60px'),
 '#eventDateDiv'=>array('float'=>'left'),
 '#eventDateDiv input'=>array('width'=>'110px')
 ),
 'customCSS' => array(),
 'customLookups' => array(
 'processingStatus' => array('Unprocessed','Stage 1','Stage 2','Pending Review','Expert Required','Reviewed','Closed')
 )
 );
 // json: {"editorProps":{"modules-panel":{"paleo":{"status":1}}}}
 */

// Should public users be able to create accounts?
$SHOULD_BE_ABLE_TO_CREATE_PUBLIC_USER = true;

$SYMBIOTA_LOGIN_ENABLED = true;
$ENABLE_CROSS_PORTAL = true;

$SHOULD_INCLUDE_CULTIVATED_AS_DEFAULT = false;
$AUTH_PROVIDER = 'oid';
$LOGIN_ACTION_PAGE = 'openIdAuth.php';
$SHOULD_USE_HARVESTPARAMS = false;

$COOKIE_SECURE = false;
if ((! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
    header('strict-transport-security: max-age=600');
    $COOKIE_SECURE = true;
}

$CSSARR = ['../../css/symbiota/customization.css'];
$GEO_JSON_LAYERS = [
    [
        'filename' => 'ACE_Ecoregions_BaileyDerived_2022.geojson',
        'label' => 'Ecoregions California',
        'popup_template' => '<div><b>Ecoregion:</b> [ECOREGION_]</div><div><b>Ecoregion Acres:</b> [Ecoregion1]</div>',
        'template_properties' => [
            'ECOREGION_',
            'Ecoregion1',
        ],
    ],
    [
        'filename' => 'us_counties.geojson',
        'label' => 'US Counties',
        'popup_template' => '<div><b>County:</b> [NAME]</div>',
        'template_properties' => [
            'NAME',
        ],
    ],
];

//Base code shared by all pages; leave as is
include_once 'symbbase.php';
/* --DO NOT ADD ANY EXTRA SPACES BELOW THIS LINE-- */
