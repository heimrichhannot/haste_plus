<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'HeimrichHannot',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Library
	'HeimrichHannot\Haste\Dca\Calendar'        => 'system/modules/haste_plus/library/Haste/Dca/Calendar.php',
	'HeimrichHannot\Haste\Util\Format'         => 'system/modules/haste_plus/library/Haste/Util/Format.php',
	'HeimrichHannot\Haste\Util\Module'         => 'system/modules/haste_plus/library/Haste/Util/Module.php',
	'HeimrichHannot\Haste\Util\Url'            => 'system/modules/haste_plus/library/Haste/Util/Url.php',
	'HeimrichHannot\Haste\Util\Arrays'         => 'system/modules/haste_plus/library/Haste/Util/Arrays.php',
	'HeimrichHannot\Haste\Util\StringUtil'     => 'system/modules/haste_plus/library/Haste/Util/StringUtil.php',
	'HeimrichHannot\Haste\DateUtil'            => 'system/modules/haste_plus/library/Haste/Util/DateUtil.php',
	'HeimrichHannot\Haste\Util\DOMUtil'        => 'system/modules/haste_plus/library/Haste/Util/DOMUtil.php',
	'HeimrichHannot\Haste\Util\Classes'        => 'system/modules/haste_plus/library/Haste/Util/Classes.php',
	'HeimrichHannot\Haste\Util\Files'          => 'system/modules/haste_plus/library/Haste/Util/Files.php',
	'HeimrichHannot\Haste\Map\GoogleMapMarker' => 'system/modules/haste_plus/library/Haste/Maps/GoogleMapMarker.php',
	'HeimrichHannot\Haste\Map\GoogleMap'       => 'system/modules/haste_plus/library/Haste/Maps/GoogleMap.php',

	// Classes
	'HeimrichHannot\HastePlus\Utilities'       => 'system/modules/haste_plus/classes/Utilities.php',
	'HeimrichHannot\HastePlus\DOM'             => 'system/modules/haste_plus/classes/DOM.php',
	'HeimrichHannot\HastePlus\Arrays'          => 'system/modules/haste_plus/classes/Arrays.php',
	'HeimrichHannot\HastePlus\Strings'         => 'system/modules/haste_plus/classes/Strings.php',
	'HeimrichHannot\HastePlus\Environment'     => 'system/modules/haste_plus/classes/Environment.php',
	'HeimrichHannot\HastePlus\Classes'         => 'system/modules/haste_plus/classes/Classes.php',
	'HeimrichHannot\HastePlus\Files'           => 'system/modules/haste_plus/classes/Files.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'dlh_googlemaps_haste' => 'system/modules/haste_plus/templates/maps/frontend',
	'dlh_marker'           => 'system/modules/haste_plus/templates/maps/elements',
));
