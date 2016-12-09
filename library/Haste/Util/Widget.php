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
    public static function getBackendFormField($strField, $arrDca, $varValue = null)
    {
        if (!($strClass = $GLOBALS['BE_FFL'][$arrDca['inputType']]))
            return false;

        return new $strClass(\Widget::getAttributesFromDca($arrDca, $strField, $varValue));
    }

    public static function getFrontendFormField($strField, $arrDca, $varValue = null)
    {
        if (!($strClass = $GLOBALS['TL_FFL'][$arrDca['inputType']]))
            return false;

        return new $strClass(\Widget::getAttributesFromDca($arrDca, $strField, $varValue));
    }
}