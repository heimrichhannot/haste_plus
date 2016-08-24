<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Model;


class UserModel extends \Contao\UserModel
{
	/**
	 * Find active user by id
	 *
	 * @param int   $intId
	 * @param array $arrOptions
	 *
	 * @return \UserModel|\UserModel[]|\Model\Collection|null
	 */
	public static function findActiveById($intId, array $arrOptions = array())
	{
		$t    = static::$strTable;
		$time = \Date::floorToMinute();

		$arrColumns = array("($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.disable=''");

		$arrColumns[] = "$t.id = ?";

		return static::findOneBy($arrColumns, $intId, $arrOptions);
	}
}