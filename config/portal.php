<?php

function getMapBounds() {
    $bounds = env('PORTAL_MAPPING_BOUNDARIES', false);

    if (! $bounds) {
        return [
            [39.993956, -102.084961],
            [37.002553, -94.680176],
        ];
    }

    [$latA, $lngA, $latB, $lngB] = explode(';', $bounds);

    return [
        [floatval($latA), floatval($lngA)],
        [floatval($latB), floatval($lngB)],
    ];
}

return [

    /*
    |--------------------------------------------------------------------------
    | Portal Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your portal. This value is used when the
    | framework needs forward a request to a 'Symbiota' subportal. This value
    | should be the name of the folder than hosts the subportal found in this
    | projects root folder.
    */
    'name' => env('PORTAL_NAME', 'Portal'),
    'use_client_root' => env('PORTAL_USE_CLIENT_ROOT', false),

    /*
    |--------------------------------------------------------------------------
    | Header Variable Customization
    |--------------------------------------------------------------------------
    |
    | The following are used to customize header content and layout.
    | header_title is for the main title for the site.
    | header_sub_title is for the text underneath the main title if any.
    | show_brand indicates whether the brand component should be rendered.
    */
    'header_title' => env('PORTAL_HEADER_TITLE', 'Symbiota Brand New Portal'),
    'header_sub_title' => env('PORTAL_HEADER_SUB_TITLE', 'Redesigned by the Symbiota Support Hub'),
    'show_brand' => env('PORTAL_SHOW_BRAND', true),

    /*
    |--------------------------------------------------------------------------
    | Portal Version
    |--------------------------------------------------------------------------
    |
    | This value is portal version of your symbiota instance. This value is
    | used to check feature parity between symbiota instances and displayed
    | publicly to help with maintence and bug reports.
    |
    */
    'version' => '4.0.0',

    /*
    |--------------------------------------------------------------------------
    | Schema Version
    |--------------------------------------------------------------------------
    |
    | This value is the schema version which this src code runs best with. It is
    | critical to make sure this version and the database schema version stay
    | aligned in order for the code to work properly.
    */
    'schema_version' => '3.3.3',

    /*
    |--------------------------------------------------------------------------
    | Mapping Boundaries
    |--------------------------------------------------------------------------
    |
    | This value is the default map view bounds for the map tools if their is
    | not anything else to focus on.
    */
    'mapping_boundaries' => getMapBounds(),

    /*
    |--------------------------------------------------------------------------
    | Activate Exsiccati
    |--------------------------------------------------------------------------
    |
    | This value is the default map view bounds for the map tools if their is
    | not anything else to focus on.
    */
    'activate_exsiccati' => env('PORTAL_ACTIVATE_EXSICCATI', false),
];
