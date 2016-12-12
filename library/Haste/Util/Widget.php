<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @package haste_plus
 * @author  Dennis Patzer
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Util;

class Widget
{
    /**
     * Get an instance of \Widget by passing fieldname and dca data
     * @param        $strField string The field name
     * @param        $arrDca array The DCA
     * @param null   $varValue array
     * @param string $strDbField string The database field name
     * @param string $strTable The table
     * @param null   $objDca object The data container
     *
     * @return bool
     */
    public static function getBackendFormField($strField, array $arrDca, $varValue = null, $strDbField = '', $strTable = '', $objDca = null)
    {
        if (!($strClass = $GLOBALS['BE_FFL'][$arrDca['inputType']]))
        {
            return false;
        }

        return new $strClass(\Widget::getAttributesFromDca($arrDca, $strField, $varValue, $strDbField, $strTable, $objDca));
    }

    /**
     * Get an instance of \Widget by passing fieldname and dca data
     * @param        $strField string The field name
     * @param        $arrDca array The DCA
     * @param null   $varValue array
     * @param string $strDbField string The database field name
     * @param string $strTable The table
     * @param null   $objDca object The data container
     *
     * @return bool
     */
    public static function getFrontendFormField($strField, array $arrDca, $varValue = null, $strDbField = '', $strTable = '', $objDca = null)
    {
        if (!($strClass = $GLOBALS['TL_FFL'][$arrDca['inputType']]))
        {
            return false;
        }

        return new $strClass(\Widget::getAttributesFromDca($arrDca, $strField, $varValue, $strDbField, $strTable, $objDca));
    }
}