<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package Haste_plus
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
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
	// Classes
	'HeimrichHannot\HastePlus\Utilities'   => 'system/modules/haste_plus/classes/Utilities.php',
	'HeimrichHannot\HastePlus\DOM'         => 'system/modules/haste_plus/classes/DOM.php',
	'HeimrichHannot\HastePlus\Arrays'      => 'system/modules/haste_plus/classes/Arrays.php',
	'HeimrichHannot\HastePlus\Strings'     => 'system/modules/haste_plus/classes/Strings.php',
	'HeimrichHannot\HastePlus\Environment' => 'system/modules/haste_plus/classes/Environment.php',
	'HeimrichHannot\HastePlus\Classes'     => 'system/modules/haste_plus/classes/Classes.php',
	'HeimrichHannot\HastePlus\Files'       => 'system/modules/haste_plus/classes/Files.php',
));
