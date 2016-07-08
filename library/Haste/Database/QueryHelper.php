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

	/**
	 * Transforms verbose operators to valid MySQL operators (aka junctors).
	 * Supports: like, unlike, equal, unequal, lower, greater, lowerequal, greaterequal, in, notin
	 * @param $strVerboseOperator
	 *
	 * @return string|boolean The transformed operator or false if not supported
	 */
	public static function transformVerboseOperator($strVerboseOperator)
	{
		switch ($strVerboseOperator)
		{
			case 'like':
				return 'LIKE';
				break;
			case 'unlike':
				return 'NOT LIKE';
				break;
			case 'equal':
				return '=';
				break;
			case 'unequal':
				return '!=';
				break;
			case 'lower':
				return '<';
				break;
			case 'greater':
				return '>';
				break;
			case 'lowerequal':
				return '<=';
				break;
			case 'greaterequal':
				return '>=';
				break;
			case 'in':
				return 'IN';
				break;
			case 'notin':
				return 'NOT IN';
				break;
		}

		return false;
	}

	/**
	 * Computes a MySQL condition appropriate for the given operator
	 * @param $strField
	 * @param $strOperator
	 * @param $varValue
	 *
	 * @return array Returns array($strQuery, $arrValues)
	 */
	public static function computeCondition($strField, $strOperator, $varValue)
	{
		$strOperator = trim(strtolower($strOperator));
		$arrValues = array();
		$strPattern = '?';

		switch ($strOperator)
		{
			case 'unlike':
				$arrValues[] = '%' . $varValue . '%';
				break;
			case '=':
				$arrValues[] = $varValue;
				break;
			case '!=':
			case '<>':
				$arrValues[] = $varValue;
				break;
			case '<':
				$strPattern = 'CAST(? AS DECIMAL)';
				$arrValues[] = $varValue;
				break;
			case '>':
				$strPattern = 'CAST(? AS DECIMAL)';
				$arrValues[] = $varValue;
				break;
			case '<=':
				$strPattern = 'CAST(? AS DECIMAL)';
				$arrValues[] = $varValue;
				break;
			case '>=':
				$strPattern = 'CAST(? AS DECIMAL)';
				$arrValues[] = $varValue;
				break;
			case 'in':
				$strPattern = '(' . implode(',', array_map(function($value) {
						return '\'' . $value . '\'';
					}, explode(',', $varValue))) . ')';
				break;
			case 'not in':
				$strPattern = '(' . implode(',', array_map(function($value) {
						return '\'' . $value . '\'';
					}, explode(',', $varValue))) . ')';
				break;
			default:
				$arrValues[] = '%' . $varValue . '%';
				break;
		}

		return array("$strField $strOperator $strPattern", $arrValues);
	}

}