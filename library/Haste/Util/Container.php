<?php

namespace HeimrichHannot\Haste\Util;

/**
 * Class Container
 *
 * Contains convenience methods for using the contao container
 * @package HeimrichHannot\Haste\Util
 */
class Container
{
    public static function isBackend()
    {
        if (version_compare(VERSION, '4.0', '<')) {
            return TL_MODE == 'BE';
        } else {
            if ($request = static::getCurrentRequest())
            {
                return \System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request);
            }
            else
            {
                return false;
            }
        }
    }

    public static function isFrontend()
    {
        if (version_compare(VERSION, '4.0', '<')) {
            return TL_MODE == 'FE';
        } else {
            if ($request = static::getCurrentRequest())
            {
                return \System::getContainer()->get('contao.routing.scope_matcher')->isFrontendRequest($request);
            }
            else
            {
                return false;
            }
        }
    }

    public static function getCurrentRequest()
    {
        return \System::getContainer()->get('request_stack')->getCurrentRequest();
    }

    public static function getGet($key)
    {
        return \System::getContainer()->get('request_stack')->getCurrentRequest()->query->get($key);
    }

    public static function getPost($key)
    {
        return \System::getContainer()->get('request_stack')->getCurrentRequest()->request->get($key);
    }

    /**
     * @param $text string
     * @param $function string
     * @param $category string Use constants in ContaoContext
     */
    public static function log($text, $function, $category)
    {
        if (version_compare(VERSION, '4.0', '<')) {
            \Controller::log($text, $function, $category);
        } else {
            $level  = ($category === \Contao\CoreBundle\Monolog\ContaoContext::ERROR ? \Psr\Log\LogLevel::ERROR : \Psr\Log\LogLevel::INFO);
            $logger = \System::getContainer()->get('monolog.logger.contao');

            $logger->log($level, $text, ['contao' => new \Contao\CoreBundle\Monolog\ContaoContext($function, $category)]);
        }
    }

    public static function getProjectDir()
    {
        return \System::getContainer()->getParameter('kernel.project_dir');
    }

    public static function getWebDir()
    {
        return \System::getContainer()->getParameter('contao.web_dir');
    }
}