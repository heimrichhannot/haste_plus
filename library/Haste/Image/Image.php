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
    public static function getBackgroundHtml($image, $width, $height, $mode = '', $class = '', $target = null, $force = false)
    {
        return '<span class="background' . ($class ? ' ' . $class : '') . '" style="display: block; background-image: url(' . static::get(
            $image,
            $width,
            $height,
            $mode,
            $target,
            $force
        ) . ')"></span>';
    }

    public static function get($image, $width, $height, $mode = '', $target = null, $force = false)
    {
        return \Image::get(str_replace(\Environment::get('url'), '', $image), $width, $height, $mode, $target, $force);
    }

    public static function getExifTagsAsOptions()
    {
        if (!class_exists('PHPExif\Exif'))
        {
            throw new \Exception(
                'Couldn\'t find PHPExif. Please install it via "composer require miljar/php-exif:0.6.3" since it\'s necessary for HeimrichHannot\Haste\Image\image.'
            );
        }

        $arrConsts = array();

        $objRef = new \ReflectionClass('PHPExif\Exif');
        if ($objRef !== null)
        {
            $arrConsts = array_values($objRef->getConstants());
            $arrConsts[] = 'custom';
        }

        return $arrConsts;
    }
}