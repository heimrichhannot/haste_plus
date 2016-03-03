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

namespace HeimrichHannot\Haste\Dca;


use HeimrichHannot\Haste\Util\Arrays;

class General
{
	public static function setDateAdded(\DataContainer $objDc)
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

	public static function getMembersAsOptions(\DataContainer $objDc, $blnIncludeId = false)
	{
		$objDatabase = \Database::getInstance();
		$objMembers = $objDatabase->execute('SELECT id, firstname, lastname FROM tl_member');
		$arrOptions = array();

		if ($objMembers->numRows > 0)
		{
			if ($blnIncludeId)
			{
				$arrIds = array_values($objMembers->fetchEach('id'));
				$arrOptions = Arrays::concatArrays(' ', $objMembers->fetchEach('firstname'), $objMembers->fetchEach('lastname'),
						array_map(function($val) {return '(ID ' . $val . ')';}, array_combine($arrIds, $arrIds)));
			}
			else
			{
				$arrOptions = Arrays::concatArrays(' ', $objMembers->fetchEach('firstname'), $objMembers->fetchEach('lastname'));
			}
		}

		asort($arrOptions);

		return $arrOptions;
	}

	public static function getMembersAsOptionsIncludingIds(\DataContainer $objDc)
	{
		return static::getMembersAsOptions($objDc, true);
	}

	public static function getUsersAsOptions(\DataContainer $objDc, $blnIncludeId = false)
	{
		$objDatabase = \Database::getInstance();
		$objMembers = $objDatabase->execute('SELECT id, name FROM tl_user');
		$arrOptions = array();

		if ($objMembers->numRows > 0)
		{
			if ($blnIncludeId)
			{
				$arrIds = array_values($objMembers->fetchEach('id'));
				$arrOptions = Arrays::concatArrays(' ', $objMembers->fetchEach('name'),
						array_map(function($val) {return '(ID ' . $val . ')';}, array_combine($arrIds, $arrIds)));
			}
			else
			{
				$arrOptions = $objMembers->fetchEach('name');
			}
		}

		asort($arrOptions);

		return $arrOptions;
	}

	public static function getUsersAsOptionsIncludingIds(\DataContainer $objDc)
	{
		return static::getUsersAsOptions($objDc, true);
	}
}