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
 * helper class for offering contao functionality
 */

class Utilities
{

	public static function isSubModuleOf($strModuleType, $strModuleGroup, $strParentModuleType, $blnBackendModule = false)
	{
		$strIndex = ($blnBackendModule ? 'BE' : 'FE') . '_MOD';

		return (isset($GLOBALS[$strIndex][$strModuleGroup][$strModuleType]) &&
		($GLOBALS[$strIndex][$strModuleGroup][$strModuleType] == $strParentModuleType) ||
			is_subclass_of($GLOBALS[$strIndex][$strModuleGroup][$strModuleType], $strParentModuleType));
	}

	public function setDateAdded(\DataContainer $objDc)
	{
		// Return if there is no active record (override all)
		if (!$objDc->activeRecord || $objDc->activeRecord->dateAdded > 0) {
			return;
		}

		$time = time();

		$strTable = $objDc->__get('table');

		\Database::getInstance()->prepare("UPDATE $strTable SET dateAdded=? WHERE id=?")
			->execute($time, $objDc->activeRecord->id);
	}

}