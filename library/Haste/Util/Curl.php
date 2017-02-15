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
    public static function request($strUrl)
    {
        $objCurl = curl_init();
        curl_setopt($objCurl, CURLOPT_URL, $strUrl);
        curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, 1);

        if (\Config::get('hpProxy'))
        {
            curl_setopt($objCurl, CURLOPT_PROXY, \Config::get('hpProxy'));
        }

        $strResult = curl_exec($objCurl);
        curl_close($objCurl);

        return $strResult;
    }
}