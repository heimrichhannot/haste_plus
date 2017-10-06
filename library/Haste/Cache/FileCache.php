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

class FileCache extends Cache
{
    protected static $cacheDir = 'system/cache/phpfastcache';

    protected static $driver = 'files';

    protected function extendOptions(array $arrOptions = [])
    {
        if (!is_dir(TL_ROOT . '/' . ltrim(self::$cacheDir, '/'))) {
            new \Folder(self::$cacheDir);
        }

        $arrOptions['path'] = TL_ROOT . '/' . ltrim(self::$cacheDir, '/');

        return $arrOptions;
    }
}