<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @package haste_plus
 * @author  Dennis Patzer <d.patzer@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Image;

class Image
{
	public static function getBackgroundHtml($image, $width, $height, $mode='', $class='', $target=null, $force=false)
	{
		return '<div class="image_container background"' . ($class ? ' ' . $class : '') . '" style="background-image: url(' .
			\Image::get($image, $width, $height, $mode, $target, $force) . ')"></div>';
	}
}