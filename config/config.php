<?php

/**
 * Assets
 */
if(TL_MODE == 'FE')
{
	array_insert($GLOBALS['TL_JAVASCRIPT'], 0, array(
		'haste_plus' => '/system/modules/haste_plus/assets/js/haste_plus.min.js|static',
		'haste_plus_environment' => '/system/modules/haste_plus/assets/js/environment.min.js|static',
		'haste_plus_files' => '/system/modules/haste_plus/assets/js/files.min.js|static',
		'haste_plus_arrays' => '/system/modules/haste_plus/assets/js/arrays.min.js|static',
		'haste_plus_dom' => '/system/modules/haste_plus/assets/js/dom.min.js|static'
	));
}


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['initializeSystem'][] = array('\\HeimrichHannot\\Haste\\Security\\HttpResponse', 'setSecurityHeaders');