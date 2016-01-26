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


class DOMUtil
{
	/**
	 * @param       $strText
	 * @param array $strCssText the css as text (no paths allowed atm)
	 *
	 * @throws \TijsVerkoyen\CssToInlineStyles\Exception
	 */
	public static function convertToInlineCss($strText, $strCssText)
	{
		// apply the css inliner
		$objCssInliner = new \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles($strText, $strCssText);

		return $objCssInliner->convert();
	}
}