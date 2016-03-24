<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
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


	public function get($keyword)
	{
		return self::__callStatic(self::$driver, $keyword, self::getOptions());
	}

	public function set($keyword, $data, $timeout=null)
	{
		if($timeout === null)
		{
			$timeout = static::$timeout;
		}

		$options = self::getOptions();

		return self::__callStatic('driver_set', array($keyword, $data, $timeout, self::getOptions()));
	}

	public function delete($keyword)
	{
		return self::__callStatic('driver_delete', array($keyword, self::getOptions()));
	}

	public function clean()
	{
		return self::__callStatic('driver_clean', array(self::getOptions()));
	}

	public function touch($keyword, $timeoutextend)
	{
		return self::__callStatic('driver_touch', array($keyword, $timeoutextend, self::getOptions()));
	}

	public function increment($keyword, $step=1)
	{
		return self::__callStatic('driver_increment', array($keyword, $step, self::getOptions()));
	}

	public function decrement($keyword, $step=1)
	{
		return self::__callStatic('driver_decrement', array($keyword, $step, self::getOptions()));
	}

	public function search($needle, $searchInValues = false)
	{
		return self::__callStatic('driver_search', array($needle, (boolean) $searchInValues, self::getOptions()));
	}

	public function isExisting($keyword)
	{
		return self::__callStatic('driver_isExisting', array($keyword, self::getOptions()));
	}

	public function stats()
	{
		return self::__callStatic('driver_stats', array(self::getOptions()));
	}
}