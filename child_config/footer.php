<?php

$logos = [
    [
        'img' => url('/images/logo_nsf.gif'),
        'link' => 'https://www.nsf.gov',
        'title' => 'NSF',
    ],
    [
        'img' => url('/images/logo_idig.png'),
        'link' => 'http://idigbio.org',
        'title' => 'iDigBio',
    ],
    [
        'img' => url('/images/logo-asu-biokic.png'),
        'link' => 'https://biokic.asu.edu',
        'title' => 'Biodiversity Knowledge Integration Center',
    ],
];

$grants = [
    [
        'link' => 'https://www.nsf.gov',
        'label' => '#-------',
        'grant_id' => '',
    ],
];
echo Blade::render('footer', ['grants' => $grants, 'logos' => $logos]);
