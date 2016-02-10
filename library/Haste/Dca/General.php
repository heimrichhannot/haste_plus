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
}