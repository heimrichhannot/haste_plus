<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Database;

class QueryHelper
{
    const SQL_CONDITION_OR  = 'OR';
    const SQL_CONDITION_AND = 'AND';

    const OPERATOR_LIKE          = 'like';
    const OPERATOR_UNLIKE        = 'unlike';
    const OPERATOR_EQUAL         = 'equal';
    const OPERATOR_UNEQUAL       = 'unequal';
    const OPERATOR_LOWER         = 'lower';
    const OPERATOR_LOWER_EQUAL   = 'lowerequal';
    const OPERATOR_GREATER       = 'greater';
    const OPERATOR_GREATER_EQUAL = 'greaterequal';
    const OPERATOR_IN            = 'in';
    const OPERATOR_NOT_IN        = 'notin';

    const OPERATORS = array(
        self::OPERATOR_LIKE,
        self::OPERATOR_UNLIKE,
        self::OPERATOR_EQUAL,
        self::OPERATOR_UNEQUAL,
        self::OPERATOR_LOWER,
        self::OPERATOR_LOWER_EQUAL,
        self::OPERATOR_GREATER,
        self::OPERATOR_GREATER_EQUAL,
        self::OPERATOR_IN,
        self::OPERATOR_NOT_IN,
    );

    /**
     * Create a where condition for fields that contain serialized
     *
     * @param string $strField     The field the condition should be checked against accordances
     * @param array  $arrValues    The values array to check the field against
     * @param string $strCondition SQL_CONDITION_OR | SQL_CONDITION_AND
     * @param bool   $blnFallback  Set to false if field you know the field was no array in past.
     *
     * @return string
     */
    public static function createWhereForSerializedBlob($strField, array $arrValues, $strCondition = self::SQL_CONDITION_OR, $blnFallback = true)
    {
        $where = null;

        if (!in_array($strCondition, array(self::SQL_CONDITION_OR, self::SQL_CONDITION_AND)))
        {
            return '';
        }

        foreach ($arrValues as $val)
        {
            if ($where !== null)
            {
                $where .= " $strCondition ";
            }

            $where .= $strCondition == self::SQL_CONDITION_AND ? "(" : "";

            $where .= "$strField REGEXP (':\"$val\"')";

            if ($blnFallback)
            {
                $where .= " OR $strField=$val"; // backwards compatibility (if field was no array before)
            }

            $where .= $strCondition == self::SQL_CONDITION_AND ? ")" : "";
        }

        return "($where)";
    }

    /**
     * Transforms verbose operators to valid MySQL operators (aka junctors).
     * Supports: like, unlike, equal, unequal, lower, greater, lowerequal, greaterequal, in, notin
     *
     * @param $strVerboseOperator
     *
     * @return string|boolean The transformed operator or false if not supported
     */
    public static function transformVerboseOperator($strVerboseOperator)
    {
        switch ($strVerboseOperator)
        {
            case static::OPERATOR_LIKE:
                return 'LIKE';
                break;
            case static::OPERATOR_UNLIKE:
                return 'NOT LIKE';
                break;
            case static::OPERATOR_EQUAL:
                return '=';
                break;
            case static::OPERATOR_UNEQUAL:
                return '!=';
                break;
            case static::OPERATOR_LOWER:
                return '<';
                break;
            case static::OPERATOR_GREATER:
                return '>';
                break;
            case static::OPERATOR_LOWER_EQUAL:
                return '<=';
                break;
            case static::OPERATOR_GREATER_EQUAL:
                return '>=';
                break;
            case static::OPERATOR_IN:
                return 'IN';
                break;
            case static::OPERATOR_NOT_IN:
                return 'NOT IN';
                break;
        }

        return false;
    }

    /**
     * Computes a MySQL condition appropriate for the given operator
     *
     * @param $strField
     * @param $strOperator
     * @param $varValue
     *
     * @return array Returns array($strQuery, $arrValues)
     */
    public static function computeCondition($strField, $strOperator, $varValue)
    {
        $strOperator = trim(strtolower($strOperator));
        $arrValues   = array();
        $strPattern  = '?';

        switch ($strOperator)
        {
            case static::OPERATOR_UNLIKE:
                $arrValues[] = '%' . $varValue . '%';
                break;
            case static::OPERATOR_EQUAL:
                $arrValues[] = $varValue;
                break;
            case static::OPERATOR_UNEQUAL:
            case '<>':
                $arrValues[] = $varValue;
                break;
            case static::OPERATOR_LOWER:
                $strPattern  = 'CAST(? AS DECIMAL)';
                $arrValues[] = $varValue;
                break;
            case static::OPERATOR_GREATER:
                $strPattern  = 'CAST(? AS DECIMAL)';
                $arrValues[] = $varValue;
                break;
            case static::OPERATOR_LOWER_EQUAL:
                $strPattern  = 'CAST(? AS DECIMAL)';
                $arrValues[] = $varValue;
                break;
            case static::OPERATOR_GREATER_EQUAL:
                $strPattern  = 'CAST(? AS DECIMAL)';
                $arrValues[] = $varValue;
                break;
            case static::OPERATOR_IN:
                $strPattern = '(' . implode(
                        ',',
                        array_map(
                            function ($value)
                            {
                                return '\'' . $value . '\'';
                            },
                            explode(',', $varValue)
                        )
                    ) . ')';
                break;
            case static::OPERATOR_NOT_IN:
                $strPattern = '(' . implode(
                        ',',
                        array_map(
                            function ($value)
                            {
                                return '\'' . $value . '\'';
                            },
                            explode(',', $varValue)
                        )
                    ) . ')';
                break;
            default:
                $arrValues[] = '%' . $varValue . '%';
                break;
        }

        $strOperator = $GLOBALS['TL_LANG']['MSC']['operators'][$strOperator];

        return array("$strField $strOperator $strPattern", $arrValues);
    }

}