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
 * helper class for offering class functionality
 */

class Classes
{

	public static function getParentClasses($strClass, $arrParents=array()) {
		$strParent = get_parent_class($strClass);
		if ($strParent)
		{
			$arrParents[] = $strParent;

			$arrParents = self::getParentClasses($strParent, $arrParents);
		}
		return $arrParents;
	}

}