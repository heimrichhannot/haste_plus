<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Backend;


class Automator extends \Automator
{
	public function purgePhpFastCache()
	{
		if (!is_array($GLOBALS['TL_PURGE']['folders']['phpfastcache']['affected']))
		{
			return false;
		}

		foreach ($GLOBALS['TL_PURGE']['folders']['phpfastcache']['affected'] as $folder)
		{
			// Purge folder
			$objFolder = new \Folder($folder);
			$objFolder->purge();

			// Restore the index.html file
			$objFile = new \File('templates/index.html', true);
			$objFile->copyTo($folder . 'index.html');
		}

		// Also empty the page cache so there are no links to deleted scripts
		$this->purgePageCache();

		// Add a log entry
		$this->log('Purged the phpfastcache cache', 'HeimrichHannot\Haste\Backend\Automator purgePhpFastCache()', TL_CRON);
	}
}