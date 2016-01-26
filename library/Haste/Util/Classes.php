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