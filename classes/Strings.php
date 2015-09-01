<?php

namespace HeimrichHannot\HastePlus;

	/**
	 * Contao Open Source CMS
	 *
	 * Copyright (C) 2005-2013 Leo Feyer
	 *
	 * @package   haste_plus
	 * @author    d.patzer@heimrich-hannot.de
	 * @license   GNU/LGPL
	 * @copyright Heimrich & Hannot GmbH
	 */

/**
 * helper class for offering string functionality
 */

class Strings
{

	public static function underscoreToCamelCase($strValue, $blnFirstCharCapital = false)
	{
		if ($blnFirstCharCapital == true)
		{
			$strValue[0] = strtoupper($strValue[0]);
		}

		return preg_replace_callback(
			'/_([a-z])/',
			create_function('$c', 'return strtoupper($c[1]);'),
			$strValue
		);
	}

}