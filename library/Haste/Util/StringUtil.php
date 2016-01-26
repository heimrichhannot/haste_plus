<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @package haste_plus
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Util;


class StringUtil extends \Haste\Util\StringUtil
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

	public static function preg_replace_last($strRegExp, $strSubject)
	{
		if (!$strRegExp)
			return $strSubject;

		$strDelimiter = $strRegExp[0];
		$strRegExp = rtrim(ltrim($strRegExp, $strDelimiter), $strDelimiter);

		return preg_replace("$strDelimiter$strRegExp(?!.*$strRegExp)$strDelimiter", '', $strSubject);
	}
}