<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Security;

/**
 * Class HttpResponse
 * @package HeimrichHannot\Haste\Security
 *
 * @deprecated Don't work contao 4. Should be replaced by a compiler pass implementation.
 */
class HttpResponse
{
    /**
     * Set security headers
     *
     * NOTE ABOUT CONTAO 4: This won't work with contao 4 anymore. See readme about further informations!
     *
     * @param $strBuffer
     * @param $strTemplate
     * @return mixed
     *
     *
     */
    public static function setSecurityHeaders($strBuffer, $strTemplate)
    {
        if (\Config::get('headerAddXFrame'))
        {
            static::addXFrame();
        }

        if (\Config::get('headerAllowOrigins'))
        {
            static::allowOrigins();
        }

        return $strBuffer;
    }

    /**
     * Protect against IFRAME Clickjacking
     */
    public static function addXFrame()
    {
        global $objPage;

        $arrSkipPages = deserialize(\Config::get('headerXFrameSkipPages'), true);

        if (in_array($objPage->id, $arrSkipPages))
        {
            return;
        }

        header("X-Frame-Options: SAMEORIGIN");
    }

    /**
     * Set Access-Control-Allow-Origins if user request is
     * part of current contao environment
     *
     * otherwise the following error:
     * 'Response to preflight request doesn't pass access control check: No 'Access-Control-Allow-Origin' header is present on the requested resource.'
     * may occur if origins are not given
     */
    public static function allowOrigins()
    {
        $arrPaths = \HeimrichHannot\Haste\Util\Environment::getAvailableOrigins();

        $arrReferer    = parse_url(\Environment::get('httpReferer'));
        $strRefereHost = $arrReferer['scheme'] . '://' . $arrReferer['host'];

        // check if current request url http://<host> is part of available origins
        if (!empty($arrPaths) && is_array($arrPaths) && in_array($strRefereHost, $arrPaths))
        {
            header('Access-Control-Allow-Origin: ' . $strRefereHost);
            header('Access-Control-Allow-Headers: X-Requested-With');
        }
    }
}