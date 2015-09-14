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
 * helper class for offering dom functionality
 */

class DOM
{

	/**
	 * @param       $strText
	 * @param array $strCssText the css as text (no paths allowed atm)
	 *
	 * @throws \TijsVerkoyen\CssToInlineStyles\Exception
	 */
	public static function convertToInlineCss($strText, array $strCssText)
	{
		// apply the css inliner
		$objCssInliner = new \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles($strText, $strCssText);

		return $objCssInliner->convert();
	}

}