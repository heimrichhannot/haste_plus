<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @package haste_plus
 * @author  Dennis Patzer
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Util;


class Curl
{
    public static function request($strUrl, $arrData = null)
    {
        $objCurl = curl_init();
        curl_setopt($objCurl, CURLOPT_URL, $strUrl);
        curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, 1);

        if (\Config::get('hpProxy'))
        {
            curl_setopt($objCurl, CURLOPT_PROXY, \Config::get('hpProxy'));
        }

        if (is_array($arrData))
        {
            curl_setopt($objCurl, CURLOPT_POST, true);
            curl_setopt($objCurl, CURLOPT_POSTFIELDS, http_build_query($arrData));
            curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, true);
        }

        $strResult = curl_exec($objCurl);
        curl_close($objCurl);

        return $strResult;
    }
}