<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @package haste_plus
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Util;

class Url extends \Haste\Util\Url
{
    /**
     * Check an url for existing scheme and add if it is missing
     *
     * @param        $strUrl    The url
     * @param string $strScheme Name of the scheme
     *
     * @return string The url with scheme
     */
    public static function addScheme($strUrl, $strScheme = 'http://')
    {
        return parse_url($strUrl, PHP_URL_SCHEME) === null ? $strScheme . $strUrl : $strUrl;
    }

    public static function getCurrentUrlWithoutParameters()
    {
        return \Environment::get('url') . parse_url(\Environment::get('uri'), PHP_URL_PATH);
    }

    public static function getCurrentUrl($blnIncludeRequestUri = true, $blnIncludeParameters = true)
    {
        $strUrl = \Environment::get('url') . ($blnIncludeRequestUri ? \Environment::get('requestUri') : '');

        if (!$blnIncludeParameters)
        {
            $strUrl = static::removeAllParametersFromUri($strUrl);
        }

        return $strUrl;
    }

    public static function getUrl($includeRequestUri = true, $includeFragments = true, $includeParameters = true)
    {
        $strUrl =
            \Environment::get('url') . ($includeRequestUri ? \Environment::get('requestUri') : '') . ($includeFragments ? static::getUriFragments(
                \Environment::get('url')
            ) : '');

        if (!$includeParameters)
        {
            $strUrl = static::removeAllParametersFromUri($strUrl);
        }

        return $strUrl;
    }

    public static function getUrlBasename($includeExtension = false, $includeParameters = false)
    {
        $arrPathInfo =
            pathinfo($includeParameters ? \Environment::get('requestUri') : static::removeAllParametersFromUri(\Environment::get('requestUri')));

        return $includeExtension ? $arrPathInfo['basename'] : $arrPathInfo['filename'];
    }

    public static function getUriParameters($uri)
    {
        $arrParsed         = parse_url($uri);
        $arrParsedExploded = [];
        if (isset($arrParsed['query']))
        {
            foreach (explode('&', $arrParsed['query']) as $currentParameter)
            {
                $arrCurrentParameterExploded                        = explode('=', $currentParameter);
                $arrParsedExploded[$arrCurrentParameterExploded[0]] = $arrCurrentParameterExploded[1];
            }
        }

        return $arrParsedExploded;
    }

    public static function getUriWithoutParameters($uri)
    {
        $arrParsed = parse_url($uri);

        $host = \Environment::get('url');

        if(isset($arrParsed['host']))
        {
            // support non scheme urls like //youtube.com/embed/…
            if(StringUtil::startsWith($uri, '//')){
                $host = '//' . $arrParsed['host'];
            }
        }

        return rtrim($host, '/') . '/' . ltrim($arrParsed['path'], '/');
    }

    public static function getUriFragments($uri)
    {
        $arrParsed = parse_url($uri);

        return $arrParsed['fragment'] ?? null;
    }

    public static function removeParameterFromUri($uri, $key)
    {
        $arrParameters = static::getUriParameters($uri);
        unset($arrParameters[$key]);

        return static::getUriWithoutParameters($uri) . (!empty($arrParameters) ? '?' . http_build_query($arrParameters) : '')
               . (static::getUriFragments($uri) ? '#' . static::getUriFragments($uri) : '');
    }

    public static function removeParametersFromUri($uri, $arrKeys)
    {
        $arrParameters = static::getUriParameters($uri);
        foreach ($arrKeys as $strKey)
        {
            unset($arrParameters[$strKey]);
        }

        return static::getUriWithoutParameters($uri) . (!empty($arrParameters) ? '?' . http_build_query($arrParameters) : '')
               . (static::getUriFragments($uri) ? '#' . static::getUriFragments($uri) : '');
    }

    public static function removeAllParametersFromUri($uri)
    {
        $arrParameters = static::getUriParameters($uri);
        foreach ($arrParameters as $strKey => $strValue)
        {
            unset($arrParameters[$strKey]);
        }

        return static::getUriWithoutParameters($uri) . (!empty($arrParameters) ? '?' . http_build_query($arrParameters) : '')
               . (static::getUriFragments($uri) ? '#' . static::getUriFragments($uri) : '');
    }

    public static function removeAllParametersFromUriBut($uri, $arrKeys)
    {
        $arrParameters = static::getUriParameters($uri);
        foreach ($arrParameters as $strKey => $strValue)
        {
            if (!in_array($strKey, $arrKeys))
            {
                unset($arrParameters[$strKey]);
            }
        }

        return static::getUriWithoutParameters($uri) . (!empty($arrParameters) ? '?' . http_build_query($arrParameters) : '')
               . (static::getUriFragments($uri) ? '#' . static::getUriFragments($uri) : '');
    }

    public static function addParameterToUri($uri, $key, $value)
    {
        $arrParameters       = static::getUriParameters($uri);
        $arrParameters[$key] = $value;

        return static::getUriWithoutParameters($uri) . (!empty($arrParameters) ? '?' . http_build_query($arrParameters) : '')
               . (static::getUriFragments($uri) ? '#' . static::getUriFragments($uri) : '');
    }

    public static function addParametersToUri($uri, array $arrParameters)
    {
        $strResult = $uri;
        foreach ($arrParameters as $strKey => $strValue)
        {
            $strResult = static::addParameterToUri($strResult, $strKey, $strValue);
        }

        return $strResult;
    }

    public static function replaceParameterInUri($uri, $key, $value)
    {
        return static::addParameterToUri(static::removeParameterFromUri($uri, $key), $key, $value);
    }

    public static function getTld()
    {
        $arrParsed       = parse_url(\Environment::get('base'));
        $arrHostExploded = explode('.', $arrParsed['host']);

        return $arrHostExploded[count($arrHostExploded) - 1];
    }

    /**
     * Helper function for creating a &/=-concatenated string out of the $_GET superglobal.
     * ATTENTION: doesn't contain the leading "?".
     *
     * @param mixed - remove one or more certain parameters
     */
    public static function getConcatenatedGetString($remove)
    {
        $result = [];
        foreach ($_GET as $k => $v)
        {
            if ((is_array($remove) && !in_array($k, $remove)) || (!is_array($remove) && $k != $remove))
            {
                $result[] = $k . '=' . $v;
            }
        }

        return implode('&', $result);
    }

    public static function getParametersFromUri($strUri)
    {
        $arrResult = [];
        parse_str(parse_url($strUri, PHP_URL_QUERY), $arrResult);

        return $arrResult;
    }

    public static function generateFrontendUrl($intPage)
    {
        if (($objPageJumpTo = \PageModel::findByPk($intPage)) !== null)
        {
            return \Controller::generateFrontendUrl($objPageJumpTo->row());
        }

        return false;
    }

    public static function generateAbsoluteUrl($intPage)
    {
        $strDomain  = static::getRootDomain($intPage);
        $strRequest = static::generateFrontendUrl($intPage);

        if ($strDomain !== false && $strRequest !== false)
        {
            return $strDomain . '/' . $strRequest;
        }
        else
        {
            return false;
        }
    }

    public static function getRootDomain($intPage)
    {
        if (($objTarget = \PageModel::findByPk($intPage)) !== null)
        {
            if ($objTarget->type == 'root')
            {
                return static::doGetRootDomain($objTarget);
            }
            else
            {
                if (($objParents = \PageModel::findParentsById($objTarget->id)) !== null)
                {
                    while ($objParents->next())
                    {
                        if ($objParents->type == 'root')
                        {
                            return static::doGetRootDomain($objParents);
                        }
                    }
                }
            }
        }

        return false;
    }

    private static function doGetRootDomain($objPage)
    {
        return ($objPage->useSSL ? 'https://' : 'http://') . $objPage->dns;
    }

    public static function getJumpToPageObject($intJumpTo)
    {
        global $objPage;

        if ($intJumpTo && $intJumpTo != $objPage->id && ($objTargetPage = \PageModel::findByPk($intJumpTo)) !== null)
        {
            return $objTargetPage;
        }

        return $objPage;
    }

    public static function getJumpToPageUrl($intJumpTo, $blnAbsolute = false)
    {
        $strUrl = \Controller::generateFrontendUrl(static::getJumpToPageObject($intJumpTo)->row());

        if ($blnAbsolute)
        {
            $strHost = \Environment::get('url');

            if (strpos($strUrl, $strHost) === false)
            {
                $strUrl = $strHost . '/' . $strUrl;
            }
        }

        return $strUrl;
    }

    /**
     * Adds the auto_item to a page's url
     *
     * @param        $objPage
     * @param        $objEvent
     * @param string $strAutoItemType
     *
     * @return string
     */
    public static function addAutoItemToPageUrl($objPage, $objItem, $strAutoItemType = 'items')
    {
        $strAutoItem =
            ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ? '/' : '/' . $strAutoItemType . '/') . ((!\Config::get('disableAlias')
                                                                                                                      && $objItem->alias
                                                                                                                         != '') ? $objItem->alias : $objItem->id);

        return \Controller::generateFrontendUrl($objPage->row(), $strAutoItem);
    }

    /**
     * Redirect to another page
     *
     * @param string  $strLocation The target URL
     * @param integer $intStatus   The HTTP status code (defaults to 303)
     */
    public static function redirect($strLocation, $intStatus=303)
    {
        if (headers_sent())
        {
            exit;
        }

        $strLocation = str_replace('&amp;', '&', $strLocation);

        // Make the location an absolute URL
        if (!preg_match('@^https?://@i', $strLocation))
        {
            $strLocation = \Environment::get('base') . ltrim($strLocation, '/');
        }

        // Ajax request
        if (\Environment::get('isAjaxRequest'))
        {
            header('HTTP/1.1 204 No Content');
            header('X-Ajax-Location: ' . $strLocation);
        }
        else
        {
            // Add the HTTP header
            switch ($intStatus)
            {
                case 301:
                    header('HTTP/1.1 301 Moved Permanently');
                    break;

                case 302:
                    header('HTTP/1.1 302 Found');
                    break;

                case 303:
                    header('HTTP/1.1 303 See Other');
                    break;

                case 307:
                    header('HTTP/1.1 307 Temporary Redirect');
                    break;
            }

            header('Location: ' . $strLocation);
        }

        exit;
    }
}

