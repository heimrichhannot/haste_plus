<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @package ${CARET}
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Util;


class Module
{
	public static function isSubModuleOf($strModuleType, $strParentModuleType, $blnBackendModule = false)
	{
		$strIndex = ($blnBackendModule ? 'BE' : 'FE') . '_MOD';

		foreach ($GLOBALS[$strIndex] as $strModuleGroup => $arrModuleTypes)
		{
			if (isset($GLOBALS[$strIndex][$strModuleGroup][$strModuleType]) &&
				($GLOBALS[$strIndex][$strModuleGroup][$strModuleType] == $strParentModuleType) ||
				is_subclass_of($GLOBALS[$strIndex][$strModuleGroup][$strModuleType], $strParentModuleType))
				return true;
		}

		return false;
	}
}