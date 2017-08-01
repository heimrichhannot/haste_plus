<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\InsertTags;


class InsertTags
{

    /**
     * Add additional tags
     *
     * @param $strTag
     * @param $blnCache
     * @param $strCache
     * @param $flags
     * @param $tags
     * @param $arrCache
     * @param $index
     * @param $count
     *
     * @return mixed Return false, if the tag was not replaced, otherwise return the value of the replaced tag
     */
    public function replace($strTag, $blnCache, $strCache, $flags, $tags, $arrCache, $index, $count)
    {
        $elements = explode('::', $strTag);

        switch (strtolower($elements[0]))
        {
            case 'trimsplit':
                if (!$elements[1] || !$elements[2] || is_array($elements[2]) || is_object($elements[2]))
                {
                    return '';
                }

                return serialize(trimsplit($elements[1], $elements[2]));

                break;
            case 'encrypt':
                if (!$elements[1])
                {
                    return '';
                }

                return \Encryption::hash($elements[1]);
            break;
        }

        return false;
    }
}
