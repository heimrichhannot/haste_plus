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

use HeimrichHannot\Haste\Util\Files;

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

    /**
     * Returns path of sized image.
     * @param $uuid mixed binary uuid
     * @param $size int|array
     * @return bool|string
     */
    public static function getSizedImagePath($uuid, $size)
    {
        $container = \System::getContainer();
        $rootDir = $container->getParameter('kernel.project_dir');

        if (!($path = Files::getPathFromUuid($uuid))) {
            return false;
        }

        $size = unserialize($size);

        if (is_array($size))
        {
            if (count($size) < 3)
            {
                return false;
            }
            else
            {
                $size = $size[2];
            }
        }

        return $container->get('contao.image.image_factory')->create(
            $rootDir . '/' . $path, $size
        )->getUrl($rootDir);
    }
}