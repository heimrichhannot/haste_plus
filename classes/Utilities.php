<?php

namespace HeimrichHannot\HastePlus;

/**
 * @deprecated since 1.1 - add new functionality to Classes in \HeimrichHannot\Haste\Util
 */

class Utilities
{

	/**
	 * @deprecated since 1.1 - use \HeimrichHannot\Haste\Util\Module
	 * @param      $strModuleType
	 * @param      $strModuleGroup
	 * @param      $strParentModuleType
	 * @param bool $blnBackendModule
	 *
	 * @return bool
	 */
	public static function isSubModuleOf($strModuleType, $strModuleGroup, $strParentModuleType, $blnBackendModule = false)
	{
		return \HeimrichHannot\Haste\Util\Module::isSubModuleOf($strModuleType, $strModuleGroup, $strParentModuleType, $blnBackendModule);
	}

	/**
	 * @deprecated since 1.1 - use \HeimrichHannot\HastePlus\Dca\Calendar::setDateAdded()
	 * @param \DataContainer $objDc
	 *
	 * @return mixed
	 */
	public function setDateAdded(\DataContainer $objDc)
	{
		return \HeimrichHannot\Haste\Dca\Calendar::setDateAdded($objDc);
	}

}