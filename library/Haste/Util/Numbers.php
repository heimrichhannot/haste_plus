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
    // currently only optimized for German users
    public static function getReadableNumber($intNumber, $intPrecision = 1, $blnAddDots = true)
    {
        $intNumber = (0 + str_replace(",", "", $intNumber));

        if (!is_numeric($intNumber))
        {
            return false;
        }

        // now filter it;
        if ($intNumber > 1000000000)
        {
            return round(($intNumber / 1000000000), $intPrecision) . ' Mrd';
        }
        else
        {
            if ($intNumber > 1000000)
            {
                return round(($intNumber / 1000000), $intPrecision) . ' Mio';
            }
            else
            {
                if ($intNumber > 1000)
                {
                    if ($blnAddDots)
                    {
                        return static::addDotsToNumber($intNumber);
                    }
                    else
                    {
                        return $intNumber;
                    }
                }
            }
        }

        return number_format($intNumber);
    }

    public static function addDotsToNumber($intNumber)
    {
        return number_format($intNumber, 0, ',', '.');
    }
}