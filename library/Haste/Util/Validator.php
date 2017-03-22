<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Util;


class Validator extends \Validator
{
    /**
     * Checks if given value is valid price
     *
     * @param  mixed $varValue
     *
     * @return boolean
     */
    public static function isPrice($varValue)
    {
        return preg_match('/^[\d \.-]*$/', $varValue);
    }

    /**
     * Checks if given value is valid International Bank Account Number (IBAN).
     *
     * @param  mixed  $varValue
     * @param boolean $blnMachineFormatOnly If true, the function will not tolerate unclean inputs (eg. spaces, dashes, leading 'IBAN ' or 'IIBAN ', lower case), If false
     *                                      (default), input can be in either: - printed ('IIBAN xx xx xx...' or 'IBAN xx xx xx...'); or machine ('xxxxx') ... string formats.
     *
     * @return boolean
     */
    public static function isIban($varValue, $blnMachineFormatOnly = false)
    {
        return verify_iban($varValue, $blnMachineFormatOnly);
    }


    /**
     * Checks if given value is valid Business Identifier Code (BIC).
     *
     * @param  mixed $varValue
     *
     * @return boolean
     */
    public static function isBic($varValue)
    {
        $pattern = '/^[A-Za-z]{4,} ?[A-Za-z]{2,} ?[A-Za-z0-9]{2,} ?([A-Za-z0-9]{3,})?$/';

        return (boolean) preg_match($pattern, $varValue);
    }
}