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
     * @param ConfigurationOption|array $config
     */
    public function __construct(string $driver = 'auto', $config = null)
    {
        $this->instance = parent::__construct($driver, self::getOptions());
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
        } elseif (is_array($config)) {
            $configNew = CacheManager::getDefaultConfig();

            if(empty($config['defaultTtl'])) $config['defaultTtl'] .= 86400;

            $config['defaultChmod'] = $config['default_chmod'];
            $config['limitedMemoryByObject'] = $config['limited_memory_each_object'];
            $config['compressData'] = $config['compress_data'];
            unset($config['default_chmod']);
            unset($config['limited_memory_each_object']);
            unset($config['compress_data']);

            foreach ($config as $key => $value) {
                $configNew[$key] = $value;
            }

            $config = $configNew;
        }

        if (!isset($config['defaultTtl']) || !$config['defaultTtl'])
        {
            $config['defaultTtl'] = 86400;
        }

        $config = static::extendOptions($config);

        return $config;
    }
}
