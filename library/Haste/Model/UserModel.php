<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Model;


use HeimrichHannot\Haste\Database\QueryHelper;

class UserModel extends \Contao\UserModel
{
    /**
     * Find active users by given user groups
     *
     * @param array $arrGroups
     * @param array $arrOptions
     *
     * @return \UserModel|\UserModel[]|\Model\Collection|null
     */
    public static function findActiveByGroups(array $arrGroups, array $arrOptions = array())
    {
        if (empty($arrGroups))
        {
            return null;
        }

        $t    = static::$strTable;
        $time = \Date::floorToMinute();

        $arrColumns = array("($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.disable=''");

        if (!empty(array_filter($arrGroups)))
        {
            $arrColumns[] = QueryHelper::createWhereForSerializedBlob('groups', array_filter($arrGroups));
        }

        return static::findBy($arrColumns, null, $arrOptions);
    }

    /**
     * Find active user by id
     *
     * @param int   $intId
     * @param array $arrOptions
     *
     * @return \UserModel|\UserModel[]|\Model\Collection|null
     */
    public static function findActiveById($intId, array $arrOptions = array())
    {
        $t    = static::$strTable;
        $time = \Date::floorToMinute();

        $arrColumns = array("($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.disable=''");

        $arrColumns[] = "$t.id = ?";

        return static::findOneBy($arrColumns, $intId, $arrOptions);
    }

    public static function hasAccessToField($strTable, $strField)
    {
        if (($objUser = \BackendUser::getInstance()) === null || !is_array(\BackendUser::getInstance()->alexf))
        {
            return false;
        }

        return $objUser->isAdmin || in_array($strTable . '::' . $strField, \BackendUser::getInstance()->alexf);
    }
}