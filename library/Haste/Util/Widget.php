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
     * Add custom regular expression to validate widget input
     *
     * @param string $strRegexp The regular expression
     * @param string $varValue  The current value
     * @param Widget $objWidget The widget
     *
     * @return bool True if the regexp was found, otherwise false
     */
    public function addCustomRegexp($strRegexp, $varValue, \Widget $objWidget)
    {
        $arrFlags = explode('::', $strRegexp);

        switch ($arrFlags[0])
        {
            case 'price':
                if (!preg_match('/^[\d \.-]*$/', $varValue))
                {
                    $objWidget->addError(sprintf($GLOBALS['TL_LANG']['ERR']['digit'], $objWidget->label));
                }

                return true;
                break;
            case 'customDate':

                if (empty($arrFlags[1]))
                {
                    return true;
                }

                if (!preg_match('~^' . \Date::getRegexp($arrFlags[1]) . '$~i', $varValue))
                {
                    $objWidget->addError(sprintf($GLOBALS['TL_LANG']['ERR']['date'], \Date::getInputFormat($arrFlags[1])));
                }
                else
                {
                    // Validate the date (see #5086)
                    try
                    {
                        new \Date($varValue, $arrFlags[1]);
                    } catch (\OutOfBoundsException $e)
                    {
                        $objWidget->addError(sprintf($GLOBALS['TL_LANG']['ERR']['invalidDate'], $varValue));
                    }
                }

                return true;
                break;
            case 'posfloat':
                if (strpos($varValue, ',') != false)
                {
                    $objWidget->addError($GLOBALS['TL_LANG']['ERR']['posFloat']['commaFound']);
                }

                if (!preg_match('/^\d+(?:\.\d+)?$/', $varValue))
                {
                    $objWidget->addError($GLOBALS['TL_LANG']['ERR']['posFloat']['noFloat']);
                }

                return true;
                break;
        }

        return false;
    }

    /**
     * Get an instance of \Widget by passing fieldname and dca data
     *
     * @param        $strField   string The field name
     * @param        $arrDca     array The DCA
     * @param null   $varValue   array
     * @param string $strDbField string The database field name
     * @param string $strTable   The table
     * @param null   $objDca     object The data container
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
     *
     * @param        $strField   string The field name
     * @param        $arrDca     array The DCA
     * @param null   $varValue   array
     * @param string $strDbField string The database field name
     * @param string $strTable   The table
     * @param null   $objDca     object The data container
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