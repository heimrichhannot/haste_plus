<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Database;

define('SQL_CONDITION_OR', 'OR');
define('SQL_CONDITION_AND', 'AND');

class QueryHelper
{
	/**
	 * Create a where condition for fields that contain serialized
	 * @param        $strField The field the condition should be checked against accordances
	 * @param array  $arrValues The values array to check the field against
	 * @param string $strCondition SQL_CONDITION_OR | SQL_CONDITION_AND
	 * @param bool   $blnFallback Set to false if field you know the field was no array in past.
	 *
	 * @return string
	 */
	public static function createWhereForSerializedBlob($strField, array $arrValues, $strCondition = SQL_CONDITION_OR, $blnFallback = true)
	{
		$where = null;

		if(!in_array($strCondition, array(SQL_CONDITION_OR, SQL_CONDITION_AND))) return '';

		foreach($arrValues as $val)
		{
			if($where !== null)
			{
				$where .= " $strCondition ";
			}

			$where .= $strCondition == SQL_CONDITION_AND ? "(" : "";

			$where .= "$strField REGEXP (':\"$val\"')";

			if($blnFallback)
			{
				$where .= " OR $strField=$val"; // backwards compatibility (if field was no array before)
			}

			$where .= $strCondition == SQL_CONDITION_AND ? ")" : "";
		}

		return "($where)";
	}

}