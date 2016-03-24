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

abstract class Cache extends CacheManager
{
	protected static $timeout = 86400; // 24 Hours

	protected static $driver = 'files';

	/**
	 * @param string $storage
	 * @param array $config
	 * @return DriverAbstract
	 */
	public static function getInstance($storage = 'auto', $config = array())
	{
		return parent::getInstance($storage, self::getOptions());
	}

	protected function extendOptions(array $arrOptions = array())
	{
		return $arrOptions;
	}

	public static function getOptions()
	{
		$arrOptions = array();

		$arrOptions['storage'] = static::$driver;

		$arrOptions = static::extendOptions($arrOptions);

		$arrOptions = array_merge(
			\phpFastCache\Core\phpFastCache::$config,
			$arrOptions
		);
		
		return $arrOptions;
	}
}