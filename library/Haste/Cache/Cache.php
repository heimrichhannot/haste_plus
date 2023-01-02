<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 *
 */

namespace HeimrichHannot\Haste\Cache;

use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\Proxy\PhpfastcacheAbstractProxy;

abstract class Cache extends PhpfastcacheAbstractProxy
{
    protected static $driver = 'files';

    protected static $objInstance;

    /**
     * @param string $driver
     * @param ConfigurationOption $config
     */
    public function __construct(string $driver = 'auto', $config = null)
    {
        $this->instance = parent::__construct($driver, self::getOptions($config));
    }

    /**
     * Return the object instance (Singleton)
     *
     * @return Cache The object instance
     */
    public static function getInstance($config = null): Cache
    {
        if (static::$objInstance === null) {
            static::$objInstance = CacheManager::getInstance(static::$driver, self::getOptions($config));
        }

        return static::$objInstance;
    }

    protected static function extendOptions($config = null): ConfigurationOption
    {
        return $config;
    }

    public static function getOptions($config = null): ConfigurationOption
    {
        if($config === null) {
            $config = CacheManager::getDefaultConfig();
        }

        if (!isset($config['defaultTtl']) || !$config['defaultTtl'])
        {
            $config['defaultTtl'] = 86400;
        }

        $config = static::extendOptions($config);

        return $config;
    }
}
