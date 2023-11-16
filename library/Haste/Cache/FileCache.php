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

use Phpfastcache\Config\ConfigurationOption;

class FileCache extends Cache
{
    protected static $cacheDir = 'system/cache/phpfastcache';

    protected static $driver = 'files';

    protected static function extendOptions($config = null): ConfigurationOption
    {
        if (!$config) {
            $config = new ConfigurationOption();
        }

        $filename = TL_ROOT . '/' . ltrim(self::$cacheDir, '/');

        if (!is_dir($filename)) {
            new \Folder(self::$cacheDir);
        }

        $config['path'] = $filename;

        return $config;
    }
}
