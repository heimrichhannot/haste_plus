<?php

/**
 * Config
 */
$GLOBALS['TL_CONFIG']['phpfastcachePath'] = 'system/cache/phpfastcache/';

/**
 * Add jquery to backend
 */
if (TL_MODE == 'BE') {
    $strJQueryPath = version_compare(VERSION, '4.0', '<') ? 'assets/jquery/core/' . $GLOBALS['TL_ASSETS']['JQUERY']
        . '/jquery.min.js' : 'assets/jquery/js/jquery.min.js';

    array_insert(
        $GLOBALS['TL_JAVASCRIPT'],
        0,
        [
            'jquery'            => $strJQueryPath,
            'jquery-noconflict' => 'system/modules/haste_plus/assets/js/jquery-noconflict.min.js'
        ]
    );
}

/**
 * Assets
 */
if (TL_MODE == 'BE') {
    array_insert(
        $GLOBALS['TL_JAVASCRIPT'],
        1,
        [
            'haste_plus'             => '/system/modules/haste_plus/assets/js/haste_plus.min.js|static',
            'haste_plus_environment' => '/system/modules/haste_plus/assets/js/environment.min.js|static',
            'haste_plus_files'       => '/system/modules/haste_plus/assets/js/files.min.js|static',
            'haste_plus_arrays'      => '/system/modules/haste_plus/assets/js/arrays.min.js|static',
            'haste_plus_dom'         => '/system/modules/haste_plus/assets/js/dom.min.js|static',
            'haste_plus_geo'         => '/system/modules/haste_plus/assets/js/geo.min.js|static',
            'haste_plus_util'        => '/system/modules/haste_plus/assets/js/util.min.js|static',
        ]
    );
}

if (TL_MODE == 'FE') {

    $GLOBALS['TL_COMPONENTS']['google_charts'] = [
        'js' => [
            'files' => [
                'system/modules/haste_plus/assets/js/vendor/visualization/charts/loader.js|static',
                'system/modules/haste_plus/assets/js/vendor/load-charts.js|static',
                'system/modules/haste_plus/assets/js/vendor/geoxml3.js|static'
            ],
            'sort'  => 0, // invoke always before all other javascript
        ],
    ];

    $GLOBALS['TL_COMPONENTS']['haste_plus'] = [
        'js' => [
            'files' => [
                'system/modules/haste_plus/assets/js/haste_plus.min.js|static',
            ],
            'sort'  => 1, // invoke always after jquery
        ],
    ];

    $GLOBALS['TL_COMPONENTS']['haste_plus.environment'] = [
        'js' => [
            'files' => [
                'system/modules/haste_plus/assets/js/environment.min.js|static',
            ],
            'sort'  => 1,  // invoke always after jquery
        ],
    ];

    $GLOBALS['TL_COMPONENTS']['haste_plus.files'] = [
        'js' => [
            'files' => [
                'system/modules/haste_plus/assets/js/files.min.js|static',
            ],
            'sort'  => 1,  // invoke always after jquery
        ],
    ];

    $GLOBALS['TL_COMPONENTS']['haste_plus.array'] = [
        'js' => [
            'files' => [
                'system/modules/haste_plus/assets/js/arrays.min.js|static',
            ],
            'sort'  => 1,  // invoke always after jquery
        ],
    ];

    $GLOBALS['TL_COMPONENTS']['haste_plus.dom'] = [
        'js' => [
            'files' => [
                'system/modules/haste_plus/assets/js/dom.min.js|static',
            ],
            'sort'  => 1,  // invoke always after jquery
        ],
    ];

    $GLOBALS['TL_COMPONENTS']['haste_plus.geo'] = [
        'js' => [
            'files' => [
                'system/modules/haste_plus/assets/js/geo.min.js|static',
            ],
            'sort'  => 1,  // invoke always after jquery
        ],
    ];

    $GLOBALS['TL_COMPONENTS']['haste_plus.util'] = [
        'js' => [
            'files' => [
                'system/modules/haste_plus/assets/js/util.min.js|static',
            ],
            'sort'  => 1,  // invoke always after jquery
        ],
    ];
}


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



