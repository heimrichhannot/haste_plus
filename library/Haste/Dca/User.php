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

class User extends \Backend
{
	protected static $arrUserOptionsCache = array();
	protected static $arrUserOptionsIdsCache = array();

	public static function getUsersAsOptions(\DataContainer $objDc = null, $blnIncludeId = false)
	{
		if (!$blnIncludeId && !empty(static::$arrUserOptionsCache))
			return static::$arrUserOptionsCache;

		if ($blnIncludeId && !empty(static::$arrUserOptionsIdsCache))
			return static::$arrUserOptionsIdsCache;

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
