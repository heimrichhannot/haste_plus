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
	/**
	 * @param      $strModuleType string The Module type (e.g. mod_event)
	 * @param      $strParentModuleClass string The parent module's class (e.g. HeimrichHannot\Some\ModuleTest)
	 * @param bool $blnBackendModule bool Turn to true if the module is a backend module
	 *
	 * @return bool
	 */
	public static function isSubModuleOf($strModuleType, $strParentModuleClass, $blnBackendModule = false)
	{
		$strIndex = ($blnBackendModule ? 'BE' : 'FE') . '_MOD';

		foreach ($GLOBALS[$strIndex] as $strModuleGroup => $arrModuleTypes)
		{
			if (isset($GLOBALS[$strIndex][$strModuleGroup][$strModuleType]) &&
				($GLOBALS[$strIndex][$strModuleGroup][$strModuleType] == $strParentModuleClass) ||
				is_subclass_of($GLOBALS[$strIndex][$strModuleGroup][$strModuleType], $strParentModuleClass))
				return true;
		}

		return false;
	}

	public static function getModules($strType, $blnIncludeSubModules = true, $blnBackendModule = false)
	{
		$arrOptions = array();

		if (!$blnIncludeSubModules)
		{
			if (($objModules = \ModuleModel::findByType($strType)) !== null)
			{
				$arrOptions = array_combine($objModules->fetchEach('id'), $objModules->fetchEach('name'));

				asort($arrOptions);
			}
		}
		else
		{
			if (($objModules = \ModuleModel::findAll()) !== null)
			{
				while ($objModules->next())
				{
					if ($strClass = static::getModuleClass($strType))
					{
						if (static::isSubModuleOf($objModules->type, $strClass, $blnBackendModule))
						{
							$arrOptions[$objModules->id] = $objModules->name;
						}
					}
				}

				asort($arrOptions);
			}
		}

		return $arrOptions;
	}

	public static function getModuleClass($strType, $blnBackendModule = false)
	{
		$strIndex = ($blnBackendModule ? 'BE' : 'FE') . '_MOD';

		foreach ($GLOBALS[$strIndex] as $strModuleGroup => $arrModuleTypes)
		{
			if (isset($GLOBALS[$strIndex][$strModuleGroup][$strType]))
				return $GLOBALS[$strIndex][$strModuleGroup][$strType];
		}

		return false;
	}

}