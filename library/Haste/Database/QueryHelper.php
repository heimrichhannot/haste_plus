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

use HeimrichHannot\Haste\Util\Arrays;

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

    const ON_DUPLICATE_KEY_IGNORE = 'IGNORE';
    const ON_DUPLICATE_KEY_UPDATE = 'UPDATE';

    const OPERATORS = [
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
    ];

    /**
     * Process a query in pieces, run callback within each cycle
     *
     * @param  string   $strCountQuery The query that count the total rows, must contain "Select COUNT(*) as total"
     * @param   string  $strQuery      The query, with the rows that should be iterated over
     * @param  callable $callback      A callback that should be triggered after each cycle, contains $arrRows of current cycle
     * @param string    $strKey        The key of the value that should be set as key identifier for the returned result array entries
     * @param int       $intBulkSize   The bulk size
     *
     * @return bool|int False if nothing to do, otherwise return the total number of processes entities
     */
    public static function processInPieces($strCountQuery, $strQuery, $callback = null, $strKey = null, $intBulkSize = 5000)
    {

        $objTotal = \Database::getInstance()->execute($strCountQuery);

        if ($objTotal->total < 1)
        {
            return false;
        }

        $intBulkSize = intval($intBulkSize);
        $intTotal    = $objTotal->total;
        $intCycles   = $intTotal / $intBulkSize;

        for ($i = 0; $i <= $intCycles; $i++)
        {
            $objResult = \Database::getInstance()->prepare($strQuery)->limit($intBulkSize, $i * $intBulkSize)->execute();

            if ($objResult->numRows < 1)
            {
                return false;
            }

            if (is_callable($callback))
            {
                $arrReturn = [];

                while (($arrRow = $objResult->fetchAssoc()) !== false)
                {
                    if ($strKey)
                    {
                        $arrReturn[$arrRow[$strKey]] = $arrRow;
                        continue;
                    }

                    $arrReturn[] = $arrRow;
                }

                call_user_func_array($callback, [$arrReturn]);
            }
        }

        return $intTotal;
    }


    /**
     * Bulk insert SQL of given data
     *
     * @param          $strTable                The database table, where new items should be stored inside
     * @param array    $arrData                 An array of values associated to its field
     * @param array    $arrFixedValues          A array of fixed values associated to its field that should be set for each row as fixed values
     * @param mixed    $onDuplicateKey          null = Throw error on duplicates, self::ON_DUPLICATE_KEY_IGNORE = ignore error duplicates (skip this entries),
     *                                          self::ON_DUPLICATE_KEY_UPDATE = update existing entries
     * @param callable $callback                A callback that should be triggered after each cycle, contains $arrValues of current cycle
     * @param callable $itemCallback            A callback to change the insert values for each items, contains $arrValues as first argument, $arrFields as
     *                                          second, $arrOriginal as third, expects an array as return value with same order as $arrFields, if no array is
     *                                          returned, insert of the row will be skipped item insert
     * @param int      $intBulkSize             The bulk size
     * @param string   $strPk                   The primary key of the current table (default: id)
     *
     * @return null Ff nothing was done return null
     */
    public static function doBulkInsert(
        $strTable,
        array $arrData = [],
        array $arrFixedValues = [],
        $onDuplicateKey = null,
        $callback = null,
        $itemCallback = null,
        $intBulkSize = 100,
        $strPk = 'id'
    ) {
        if (!\Database::getInstance()->tableExists($strTable) || empty($arrData))
        {
            return null;
        }

        $arrFields = \Database::getInstance()->getFieldNames($strTable, true);
        Arrays::removeValue($strPk, $arrFields); // unset id
        $arrFields = array_values($arrFields);

        $intBulkSize = intval($intBulkSize);

        $strQuery          = '';
        $strDuplicateQuery = '';
        $strStartQuery     = sprintf(
            'INSERT %s INTO %s (%s) VALUES ',
            $onDuplicateKey == self::ON_DUPLICATE_KEY_IGNORE ? 'IGNORE' : '',
            $strTable,
            implode(',', $arrFields)
        );

        if ($onDuplicateKey == self::ON_DUPLICATE_KEY_UPDATE)
        {
            $strDuplicateQuery = ' ON DUPLICATE KEY UPDATE ' . implode(
                    ',',
                    array_map(
                        function ($val)
                        {
                            // escape double quotes
                            return $val . ' = VALUES(' . $val . ')';
                        },
                        $arrFields
                    )
                );
        }

        $i = 0;

        $arrColumnWildcards = array_map(function ($val) { return '?'; }, $arrFields);

        foreach ($arrData as $strKey => $varData)
        {
            if ($i == 0)
            {
                $arrValues = [];
                $arrReturn = [];
                $strQuery  = $strStartQuery;
            }

            $arrColumns = $arrColumnWildcards;

            if ($varData instanceof \Model)
            {
                $varData = $varData->row();
            }

            foreach ($arrFields as $n => $strField)
            {
                $varValue = $varData[$strField] ?: 'DEFAULT';

                if (in_array($strField, array_keys($arrFixedValues)))
                {
                    $varValue = $arrFixedValues[$strField];
                }

                // replace SQL Keyword DEFAULT within wildcards ?
                if ($varValue == 'DEFAULT')
                {
                    $arrColumns[$n] = 'DEFAULT';
                    continue;
                }

                $arrReturn[$i][$strField] = $varValue;
            }

            // manipulate the item
            if (is_callable($itemCallback))
            {
                $varCallback = call_user_func_array($itemCallback, [$arrReturn[$i], $arrFields, $varData]);

                if (!is_array($varCallback))
                {
                    continue;
                }


                foreach ($arrFields as $n => $strField)
                {
                    $varValue = $varCallback[$strField] ?: 'DEFAULT';

                    // replace SQL Keyword DEFAULT within wildcards ?
                    if ($varValue == 'DEFAULT')
                    {
                        $arrColumns[$n] = 'DEFAULT';
                        continue;
                    }

                    $arrColumns[$n]           = '?';
                    $arrReturn[$i][$strField] = $varValue;
                }
            }

            // add values to insert array
            $arrValues = array_merge($arrValues, array_values($arrReturn[$i]));

            $strQuery .= '(' . implode(',', $arrColumns) . '),';

            $i++;

            if ($intBulkSize == $i)
            {
                $strQuery = rtrim($strQuery, ',');

                if ($onDuplicateKey == self::ON_DUPLICATE_KEY_UPDATE)
                {
                    $strQuery .= $strDuplicateQuery;
                }

                \Database::getInstance()->prepare($strQuery)->execute($arrValues);

                if (is_callable($callback))
                {
                    call_user_func_array($callback, [$arrReturn]);
                }

                $strQuery = '';

                $i = 0;
            }
        }

        // remaining elements < $intBulkSize
        if ($strQuery)
        {
            $strQuery = rtrim($strQuery, ',');

            if ($onDuplicateKey == self::ON_DUPLICATE_KEY_UPDATE)
            {
                $strQuery .= $strDuplicateQuery;
            }

            \Database::getInstance()->prepare($strQuery)->execute($arrValues);

            if (is_callable($callback))
            {
                call_user_func_array($callback, [$arrReturn]);
            }
        }
    }

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

        if (!in_array($strCondition, [self::SQL_CONDITION_OR, self::SQL_CONDITION_AND]))
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
                $where .= " OR $strField='$val'"; // backwards compatibility (if field was no array before)
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
        $arrValues   = [];
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

        return ["$strField $strOperator $strPattern", $arrValues];
    }

}
