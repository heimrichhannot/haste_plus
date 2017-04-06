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
	protected static $timeout = 86400; // 24 Hours

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
    public static function getInstance()
    {
        if (static::$objInstance === null)
        {
            static::$objInstance = CacheManager::getInstance(static::$driver, self::getOptions());
        }

        return static::$objInstance;
    }

	protected function extendOptions(array $arrOptions = [])
	{
		return $arrOptions;
	}

	public static function getOptions()
	{
		$arrOptions = [];

		$arrOptions['storage'] = static::$driver;

		$arrOptions = static::extendOptions($arrOptions);

		$arrOptions = array_merge(
			CacheManager::getDefaultConfig(),
			$arrOptions
		);
		
		return $arrOptions;
	}
}
