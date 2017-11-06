<?php

/**
 * Config
 */
$GLOBALS['TL_CONFIG']['phpfastcachePath'] = 'system/cache/phpfastcache/';

/**
 * Add jquery to backend
 */
if (TL_MODE == 'BE') {
    $strJQueryPath = version_compare(VERSION, '4.0', '<') ? 'assets/jquery/core/' . $GLOBALS['TL_ASSETS']['JQUERY'] . '/jquery.min.js' : 'assets/jquery/js/jquery.min.js';
    if (isset($GLOBALS['TL_JAVASCRIPT']['jquery'])) {
        unset($GLOBALS['TL_JAVASCRIPT']['jquery']);
    }
    if (isset($GLOBALS['TL_JAVASCRIPT']['jquery-noconflict'])) {
        unset($GLOBALS['TL_JAVASCRIPT']['jquery-noconflict']);
    }
    array_insert($GLOBALS['TL_JAVASCRIPT'], 0, [
        'jquery'            => $strJQueryPath,
        'jquery-noconflict' => 'system/modules/haste_plus/assets/js/jquery-noconflict.min.js',
    ]);
}

/**
 * Assets
 */
array_insert($GLOBALS['TL_JAVASCRIPT'], 1, [
    'haste_plus'             => '/system/modules/haste_plus/assets/js/haste_plus.min.js|static',
    'haste_plus_environment' => '/system/modules/haste_plus/assets/js/environment.min.js|static',
    'haste_plus_files'       => '/system/modules/haste_plus/assets/js/files.min.js|static',
    'haste_plus_arrays'      => '/system/modules/haste_plus/assets/js/arrays.min.js|static',
    'haste_plus_dom'         => '/system/modules/haste_plus/assets/js/dom.min.js|static',
    'haste_plus_geo'         => '/system/modules/haste_plus/assets/js/geo.min.js|static',
    'haste_plus_util'        => '/system/modules/haste_plus/assets/js/util.min.js|static',
    'google_charts_loader'   => 'system/modules/haste_plus/assets/js/vendor/visualization/charts/loader.js|static',
    'google_charts'          => 'system/modules/haste_plus/assets/js/vendor/load-charts.js|static',
    'geoxml3'                => 'system/modules/haste_plus/assets/js/vendor/geoxml3.js|static',
]);

$GLOBALS['TL_COMPONENTS']['google_charts'] = [
    'js' => [
        'system/modules/haste_plus/assets/js/vendor/visualization/charts/loader.js|static',
        'system/modules/haste_plus/assets/js/vendor/load-charts.js|static',
        'system/modules/haste_plus/assets/js/vendor/geoxml3.js|static',
    ],
];

$GLOBALS['TL_COMPONENTS']['haste_plus'] = [
    'js' => [
        'system/modules/haste_plus/assets/js/haste_plus.min.js|static',
    ],
];

$GLOBALS['TL_COMPONENTS']['haste_plus.environment'] = [
    'js' => [
        'system/modules/haste_plus/assets/js/environment.min.js|static',
    ],
];

$GLOBALS['TL_COMPONENTS']['haste_plus.files'] = [
    'js' => [
        'system/modules/haste_plus/assets/js/files.min.js|static',
    ],
];

$GLOBALS['TL_COMPONENTS']['haste_plus.array'] = [
    'js' => [
        'system/modules/haste_plus/assets/js/arrays.min.js|static',
    ],
];

$GLOBALS['TL_COMPONENTS']['haste_plus.dom'] = [
    'js' => [
        'system/modules/haste_plus/assets/js/dom.min.js|static',
    ],
];

$GLOBALS['TL_COMPONENTS']['haste_plus.geo'] = [
    'js' => [
        'system/modules/haste_plus/assets/js/geo.min.js|static',
    ],
];

$GLOBALS['TL_COMPONENTS']['haste_plus.util'] = [
    'js' => [
        'system/modules/haste_plus/assets/js/util.min.js|static',
    ],
];


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['modifyFrontendPage'][]            = ['\\HeimrichHannot\\Haste\\Security\\HttpResponse', 'setSecurityHeaders'];
$GLOBALS['TL_HOOKS']['addCustomRegexp']['haste_plus']   = ['HeimrichHannot\Haste\Util\Widget', 'addCustomRegexp'];
$GLOBALS['TL_HOOKS']['replaceInsertTags']['haste_plus'] = ['HeimrichHannot\Haste\InsertTags\InsertTags', 'replace'];

/**
 * PurgeData
 */
$GLOBALS['TL_PURGE']['folders']['phpfastcache'] = [
    'affected' => [\Config::get('phpfastcachePath')],
    'callback' => ['\\HeimrichHannot\\Haste\\Backend\\Automator', 'purgePhpFastCache'],
];



