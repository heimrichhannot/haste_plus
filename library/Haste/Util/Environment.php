<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Util;


class Environment
{
	/**
	 * Get all available origins for this contao environment
	 * @return array List of available origins (<protocol> :// <host>)
	 */
	public static function getAvailableOrigins()
	{
		$arrOrigins = array();

		if(!\Config::get('bypassCache'))
		{
			// Try to get the cache key from the mapper array
			if (file_exists(TL_ROOT . '/system/cache/config/origins.php'))
			{
				$arrOrigins = include TL_ROOT . '/system/cache/config/origins.php';
				return $arrOrigins;
			}
		}


		$objRootPages = \PageModel::findPublishedRootPages();

		if($objRootPages !== null)
		{
			while($objRootPages->next())
			{
				if($objRootPages->dns == '')
				{
					continue;
				}

				$strDomain = $objRootPages->useSSL ? 'https://' : 'http://';
				$strDomain .= $objRootPages->dns;
				$arrOrigins[$objRootPages->id] = $strDomain;
			}
		}

		if(!\Config::get('bypassCache'))
		{
			// Generate the page mapper file
			$objCacheFile = new \File('system/cache/config/origins.php', true);
			$objCacheFile->write(sprintf("<?php\n\nreturn %s;\n", var_export($arrOrigins, true)));
			$objCacheFile->close();
		}

		return $arrOrigins;
	}
}