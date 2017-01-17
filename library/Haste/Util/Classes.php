<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @package haste_plus
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Util;


use Contao\ClassLoader;

class Classes
{
    public static function getParentClasses($strClass, $arrParents = [])
    {
        $strParent = get_parent_class($strClass);
        if ($strParent)
        {
            $arrParents[] = $strParent;

            $arrParents = self::getParentClasses($strParent, $arrParents);
        }

        return $arrParents;
    }

    /**
     * Filter class constants by given prefixes and return the extracted constants.
     *
     * @param  string $strClass            The class that should be searched for constants in.
     * @param array   $arrPrefixes         An array of prefixes that should be used to filter the class constants.
     * @param         $blnReturnValueAsKey boolean Return the extracted array keys from its value, if true.
     *
     * @return array The extracted constants as array.
     */
    public static function getConstantsByPrefixes($strClass, array $arrPrefixes = [], $blnReturnValueAsKey = true)
    {
        $arrExtract = [];

        if (!class_exists($strClass))
        {
            return $arrExtract;
        }

        $objReflection = new \ReflectionClass($strClass);
        $arrConstants  = $objReflection->getConstants();

        if (!is_array($arrConstants))
        {
            return $arrExtract;
        }

        $arrExtract = Arrays::filterByPrefixes($arrConstants, $arrPrefixes);

        return $blnReturnValueAsKey ? array_combine($arrExtract, $arrExtract) : $arrExtract;
    }

    /**
     * Returns all classes in the given namespace
     * @param $strNamespace
     *
     * @return array
     */
    public static function getClassesInNamespace($strNamespace)
    {
        $arrOptions = [];

        foreach (array_keys(ClassLoader::getClasses()) as $strName)
        {
            if (StringUtil::startsWith($strName, $strNamespace))
            {
                $arrOptions[$strName] = $strName;
            }
        }

        asort($arrOptions);

        return $arrOptions;
    }

    /**
     * Returns all children of a given class
     * @param $strNamespace
     *
     * @return array
     */
    public static function getChildClasses($strQualifiedClassName)
    {
        $arrOptions = [];

        foreach (array_keys(ClassLoader::getClasses()) as $strName)
        {
            if (in_array($strQualifiedClassName, static::getParentClasses($strName)))
            {
                $arrOptions[$strName] = $strName;
            }
        }

        asort($arrOptions);

        return $arrOptions;
    }
}