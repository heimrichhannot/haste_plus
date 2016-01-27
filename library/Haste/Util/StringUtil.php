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
		if ($blnFirstCharCapital == true) {
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
		if (!$strRegExp) {
			return $strSubject;
		}

		$strDelimiter = $strRegExp[0];
		$strRegExp    = rtrim(ltrim($strRegExp, $strDelimiter), $strDelimiter);

		return preg_replace("$strDelimiter$strRegExp(?!.*$strRegExp)$strDelimiter", '', $strSubject);
	}

	/**
	 * Check for the occurrence at the start of the string
	 * @param $haystack The string to search in
	 * @param $needle The needle
	 *
	 * @return bool
	 */
	public static function startsWith($haystack, $needle)
	{
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}


	/**
	 * Check for the occurrence at the end of the string
	 * @param $haystack The string to search in
	 * @param $needle The needle
	 *
	 * @return bool
	 */
	public static function endsWith($haystack, $needle)
	{
		// search forward starting from end minus needle length characters
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
	}
}