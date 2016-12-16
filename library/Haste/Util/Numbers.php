<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @package haste_plus
 * @author  Oliver Janke <o.janke@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Util;

class Numbers
{
    public function positiveFloatRegExpHook($strRegexp, $varValue, \Widget $objWidget)
    {
        if ($strRegexp == 'posfloat')
        {
            if (strpos($varValue, ',') != false)
            {
                $objWidget->addError($GLOBALS['TL_LANG']['ERR']['posFloat']['commaFound']);
            }

            if (!preg_match('/^\d+(?:\.\d+)?$/', $varValue))
            {
                $objWidget->addError($GLOBALS['TL_LANG']['ERR']['posFloat']['noFloat']);
            }

            return true;
        }

        return false;
    }
}