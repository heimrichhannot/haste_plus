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

class Member extends \Backend
{
	protected static $arrMemberOptionsCache = array();
	protected static $arrMemberOptionsIdsCache = array();

	public static function getMembersAsOptions(\DataContainer $objDc = null, $blnIncludeId = false)
	{
		if (!$blnIncludeId && !empty(static::$arrMemberOptionsCache))
			return static::$arrMemberOptionsCache;

		if ($blnIncludeId && !empty(static::$arrMemberOptionsIdsCache))
			return static::$arrMemberOptionsIdsCache;

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

		if ($blnIncludeId)
			static::$arrMemberOptionsIdsCache = $arrOptions;
		else
			static::$arrMemberOptionsCache = $arrOptions;

		return $arrOptions;
	}

	public static function getMembersAsOptionsIncludingIds(\DataContainer $objDc)
	{
		return static::getMembersAsOptions($objDc, true);
	}

}
