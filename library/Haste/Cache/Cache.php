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

use phpFastCache\CacheManager;
use phpFastCache\Proxy\phpFastCacheAbstractProxy;

abstract class Cache extends phpFastCacheAbstractProxy
{
    protected static $driver = 'files';

    protected static $objInstance;

    /**
     * @param string $driver
     * @param array $config
     */
    public function __construct($driver = 'auto', array $config = [])
    {
        $this->instance = parent::__construct($driver, self::getOptions());
    }

    /**
     * Return the object instance (Singleton)
     *
     * @return \HeimrichHannot\Haste\Cache\Cache The object instance
     */
    public static function getInstance(array $arrOptions = [])
    {
        if (static::$objInstance === null) {
            static::$objInstance = CacheManager::getInstance(static::$driver, self::getOptions($arrOptions));
        }

        return static::$objInstance;
    }

    protected function extendOptions(array $arrOptions = [])
    {
        return $arrOptions;
    }

    public static function getOptions(array $arrOptions = [])
    {
        $arrOptions['storage']             = static::$driver;
        $arrOptions['ignoreSymfonyNotice'] = true;

        if (!isset($arrOptions['defaultTtl']) || !$arrOptions['defaultTtl'])
        {
            $arrOptions['defaultTtl'] = 86400;
        }

        $arrOptions = static::extendOptions($arrOptions);

        $arrOptions = array_merge(
            CacheManager::getDefaultConfig(),
            $arrOptions
        );

        return $arrOptions;
    }
}
