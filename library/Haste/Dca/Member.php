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

namespace HeimrichHannot\Haste\Dca;


use HeimrichHannot\Haste\Database\QueryHelper;
use HeimrichHannot\Haste\Util\Arrays;

class Member extends \Backend
{
    protected static $arrMemberOptionsCache                = [];
    protected static $arrMemberOptionsIdsCache             = [];
    protected static $arrMemberOptionsEmailIdCache         = [];
    protected static $arrMemberOptionsEmailIdCacheByGroups = [];

    public static function getMembersAsOptions(\DataContainer $objDc = null, $blnIncludeId = false)
    {
        if (!$blnIncludeId && !empty(static::$arrMemberOptionsCache))
        {
            return static::$arrMemberOptionsCache;
        }

        if ($blnIncludeId && !empty(static::$arrMemberOptionsIdsCache))
        {
            return static::$arrMemberOptionsIdsCache;
        }

        $objDatabase = \Database::getInstance();
        $objMembers  = $objDatabase->execute('SELECT id, firstname, lastname, email FROM tl_member');
        $arrOptions  = [];

        if ($objMembers->numRows > 0)
        {
            if ($blnIncludeId)
            {
                $arrIds     = array_values($objMembers->fetchEach('id'));
                $arrOptions = Arrays::concatArrays(
                    ' ',
                    $objMembers->fetchEach('firstname'),
                    $objMembers->fetchEach('lastname'),
                    array_map(function ($val) { return '(ID ' . $val . ')'; }, array_combine($arrIds, $arrIds))
                );
            }
            else
            {
                $arrOptions = Arrays::concatArrays(' ', $objMembers->fetchEach('firstname'), $objMembers->fetchEach('lastname'));
            }
        }

        asort($arrOptions);

        if ($blnIncludeId)
        {
            static::$arrMemberOptionsIdsCache = $arrOptions;
        }
        else
        {
            static::$arrMemberOptionsCache = $arrOptions;
        }

        return $arrOptions;
    }

    public static function getMembersAsOptionsIncludingIds(\DataContainer $objDc)
    {
        return static::getMembersAsOptions($objDc, true);
    }

    public static function getMembersAsOptionsIncludingEmailAndId(\DataContainer $objDc = null)
    {
        if (!empty(static::$arrMemberOptionsEmailIdCache))
        {
            return static::$arrMemberOptionsEmailIdCache;
        }

        $objDatabase = \Database::getInstance();
        $objMembers  = $objDatabase->execute('SELECT id, firstname, lastname, email FROM tl_member');
        $arrOptions  = [];

        if ($objMembers->numRows > 0)
        {
            while ($objMembers->next())
            {
                $arrAttributes = [];

                if ($objMembers->email)
                {
                    $arrAttributes[] = $objMembers->email;
                }

                $arrAttributes[] = 'ID ' . $objMembers->id;

                $arrOptions[$objMembers->id] = $objMembers->firstname . ' ' . $objMembers->lastname . ' (' . implode(', ', $arrAttributes) . ')';
            }
        }

        asort($arrOptions);

        static::$arrMemberOptionsEmailIdCache = $arrOptions;

        return $arrOptions;
    }

    public static function getMembersAsOptionsIncludingEmailAndIdByGroups(\DataContainer $objDc = null, array $arrGroups)
    {
        $strGroupKey = implode('_', $arrGroups);

        if (!empty(static::$arrMemberOptionsEmailIdCacheByGroups[$strGroupKey]))
        {
            return static::$arrMemberOptionsEmailIdCacheByGroups[$strGroupKey];
        }

        $objDatabase = \Database::getInstance();
        $objMembers  = $objDatabase->execute(
            'SELECT id, firstname, lastname, email, groups FROM tl_member WHERE ' . QueryHelper::createWhereForSerializedBlob('groups', $arrGroups)
        );
        $arrOptions  = [];

        if ($objMembers->numRows > 0)
        {
            while ($objMembers->next())
            {
                $arrAttributes = [];

                if ($objMembers->email)
                {
                    $arrAttributes[] = $objMembers->email;
                }

                $arrAttributes[] = 'ID ' . $objMembers->id;

                $arrOptions[$objMembers->id] = $objMembers->lastname . ', ' . $objMembers->firstname . ' (' . implode(', ', $arrAttributes) . ')';
            }
        }

        asort($arrOptions);

        $arrMemberOptionsEmailIdCacheByGroups[$strGroupKey] = $arrOptions;

        return $arrOptions;
    }
}