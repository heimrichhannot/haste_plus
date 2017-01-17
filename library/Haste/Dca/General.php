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


use Haste\Geodesy\Datum\WGS84;
use HeimrichHannot\Haste\Util\Arrays;
use HeimrichHannot\Haste\Util\Files;

class General extends \Backend
{
    const PROPERTY_SESSION_ID  = 'sessionID';
    const PROPERTY_AUTHOR      = 'author';
    const PROPERTY_AUTHOR_TYPE = 'authorType';

    const AUTHOR_TYPE_NONE   = 'none';
    const AUTHOR_TYPE_MEMBER = 'member';
    const AUTHOR_TYPE_USER   = 'user';

    public static function addOverridableFields($arrFields, $strSourceTable, $strDestinationTable)
    {
        \Controller::loadDataContainer($strSourceTable);
        \System::loadLanguageFile($strSourceTable);
        $arrSourceDca = $GLOBALS['TL_DCA'][$strSourceTable];

        \Controller::loadDataContainer($strDestinationTable);
        \System::loadLanguageFile($strDestinationTable);
        $arrDestinationDca = &$GLOBALS['TL_DCA'][$strDestinationTable];

        foreach ($arrFields as $strField)
        {
            // add override boolean field
            $strOverrideFieldName = 'override' . ucfirst($strField);

            $arrDestinationDca['fields'][$strOverrideFieldName] = [
                'label'     => &$GLOBALS['TL_LANG'][$strDestinationTable][$strOverrideFieldName],
                'exclude'   => true,
                'inputType' => 'checkbox',
                'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
                'sql'       => "char(1) NOT NULL default ''",
            ];

            $arrDestinationDca['palettes']['__selector__'][] = $strOverrideFieldName;

            // copy field
            $arrDestinationDca['fields'][$strField] = $arrSourceDca['fields'][$strField];

            // subpalette
            $arrDestinationDca['subpalettes'][$strOverrideFieldName] = $strField;
        }
    }

    /**
     * Retrieves a property of given contao model instances by *ascending* priority, i.e. the last instance of $arrInstances
     * will have the highest priority.
     *
     * CAUTION: This function assumes that you have used addOverridableFields() in this class!! That means, that a value in a
     * model instance is only used if it's either the first instance in $arrInstances or "overrideFieldname" is set to true
     * in the instance.
     *
     * @param string $strProperty  The property name to retrieve
     * @param array  $arrInstances An array of instances in ascending priority. Instances can be passed in the following form:
     *                             ['tl_some_table', $intInstanceId] or $objInstance
     *
     * @return mixed
     */
    public static function getOverridableProperty($strProperty, array $arrInstances)
    {
        $varResult = null;
        $arrPreparedInstances = [];

        // prepare instances
        foreach ($arrInstances as $varInstance)
        {
            if (is_array($varInstance))
            {
                if (($objInstance = static::getModelInstance($varInstance[0], $varInstance[1])) !== null)
                {
                    $arrPreparedInstances[] = $objInstance;
                }
            }
            elseif ($varInstance instanceof \Model)
            {
                $arrPreparedInstances[] = $varInstance;
            }
        }

        foreach ($arrPreparedInstances as $i => $objInstance)
        {
            if ($i == 0 || $objInstance->{'override' . ucfirst($strProperty)})
            {
                $varResult = $objInstance->{$strProperty};
            }
        }

        return $varResult;
    }

    /**
     * Adds a date added field to the dca and sets the appropriate callback
     *
     * @param $strDca
     */
    public static function addDateAddedToDca($strDca)
    {
        \Controller::loadDataContainer($strDca);

        $arrDca = &$GLOBALS['TL_DCA'][$strDca];

        $arrDca['config']['onload_callback']['setDateAdded'] = ['HeimrichHannot\Haste\Dca\General', 'setDateAdded', true];

        $arrDca['fields']['dateAdded'] = static::getDateAddedField();
    }

    /**
     * Sets the current date as the date added -> callback function
     *
     * @param \DataContainer $objDc
     */
    public static function setDateAdded(\DataContainer $objDc)
    {
        if ($objDc === null || !$objDc->id)
        {
            return false;
        }

        if (($objModel = static::getModelInstance($objDc->table, $objDc->id)) === null)
        {
            return false;
        }

        // Return if there is no active record (override all)
        if ($objModel->dateAdded > 0)
        {
            return false;
        }

        $objModel->dateAdded = time();
        $objModel->save();
    }


    /**
     * @return array The dca for the data added field
     */
    public static function getDateAddedField()
    {
        return [
            'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
            'sorting' => true,
            'eval'    => ['rgxp' => 'datim', 'doNotCopy' => true],
            'sql'     => "int(10) unsigned NOT NULL default '0'",
        ];
    }

    /**
     * Adds an alias field to the dca and to the desired palettes
     *
     * @param       $strDca
     * @param       $arrGenerateAliasCallback array The callback to call for generating the alias
     * @param       $strPaletteField          String The field after which to insert the alias field in the palettes
     * @param array $arrPalettes              The palettes in which to insert the field
     */
    public static function addAliasToDca($strDca, array $arrGenerateAliasCallback, $strPaletteField, $arrPalettes = ['default'])
    {
        \Controller::loadDataContainer($strDca);

        $arrDca = &$GLOBALS['TL_DCA'][$strDca];

        // add to palettes
        foreach ($arrPalettes as $strPalette)
        {
            $arrDca['palettes'][$strPalette] = str_replace($strPaletteField . ',', $strPaletteField . ',alias,', $arrDca['palettes'][$strPalette]);
        }

        // add field
        $arrDca['fields']['alias'] = static::getAliasField($arrGenerateAliasCallback);
    }

    /**
     * @param $arrGenerateAliasCallback array The callback to call for generating the alias
     *
     * @return array The dca for the alias field
     */
    public static function getAliasField(array $arrGenerateAliasCallback)
    {
        return [
            'label'         => &$GLOBALS['TL_LANG']['MSC']['alias'],
            'exclude'       => true,
            'search'        => true,
            'inputType'     => 'text',
            'eval'          => ['rgxp' => 'alias', 'unique' => true, 'maxlength' => 128, 'tl_class' => 'w50'],
            'save_callback' => [$arrGenerateAliasCallback],
            'sql'           => "varchar(128) COLLATE utf8_bin NOT NULL default ''",
        ];
    }

    /**
     * Adds a button for batch generating aliases
     *
     * @param $strTable
     */
    public static function addAliasButton($strTable)
    {
        \Controller::loadDataContainer($strTable);

        $GLOBALS['TL_DCA'][$strTable]['select']['buttons_callback'] = [
            ['HeimrichHannot\Haste\Dca\General', 'doAddAliasButton'],
        ];
    }

    /**
     * Generic method for automatically generating aliases
     *
     * @param array          $arrButtons
     * @param \DataContainer $dc
     *
     * @return array
     */
    public function doAddAliasButton($arrButtons, \DataContainer $dc)
    {
        // Generate the aliases
        if (\Input::post('FORM_SUBMIT') == 'tl_select' && isset($_POST['alias']))
        {
            $objSessionData = \Session::getInstance()->getData();
            $arrIds         = $objSessionData['CURRENT']['IDS'];

            foreach ($arrIds as $intId)
            {
                $strItemClass = \Model::getClassFromTable($dc->table);

                $objItem = $strItemClass::findByPk($intId);

                if ($objItem === null)
                {
                    continue;
                }

                $dc->id           = $intId;
                $dc->activeRecord = $objItem;

                $strAlias = '';

                // Generate new alias through save callbacks
                foreach ($GLOBALS['TL_DCA'][$dc->table]['fields']['alias']['save_callback'] as $callback)
                {
                    if (is_array($callback))
                    {
                        $this->import($callback[0]);
                        $strAlias = $this->{$callback[0]}->{$callback[1]}($strAlias, $dc);
                    }
                    elseif (is_callable($callback))
                    {
                        $strAlias = $callback($strAlias, $dc);
                    }
                }

                // The alias has not changed
                if ($strAlias == $objItem->alias)
                {
                    continue;
                }

                // Initialize the version manager
                $objVersions = new \Versions($dc->table, $intId);
                $objVersions->initialize();

                // Store the new alias
                \Database::getInstance()->prepare("UPDATE $dc->table SET alias=? WHERE id=?")->execute($strAlias, $intId);

                // Create a new version
                $objVersions->create();
            }

            \Controller::redirect($this->getReferer());
        }

        // Add the button
        $arrButtons['alias'] = '<input type="submit" name="alias" id="alias" class="tl_submit" accesskey="a" value="' . specialchars(
                $GLOBALS['TL_LANG']['MSC']['aliasSelected']
            ) . '"> ';

        return $arrButtons;
    }

    /**
     * @param $varValue mixed The current alias (if available)
     * @param $intId    int The entity's id
     * @param $strTable string The entity's table
     * @param $strAlias string The value to use as a base for the alias
     *
     * @return string
     * @throws \Exception
     */
    public static function generateAlias($varValue, $intId, $strTable, $strAlias)
    {
        $autoAlias = false;

        // Generate alias if there is none
        if ($varValue == '')
        {
            $autoAlias = true;
            $varValue  = \StringUtil::generateAlias($strAlias);
        }

        $objAlias = \Database::getInstance()->prepare("SELECT id FROM $strTable WHERE alias=?")->execute($varValue);

        // Check whether the alias exists
        if ($objAlias->numRows > 1 && !$autoAlias)
        {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
        }

        // Add ID to alias
        if ($objAlias->numRows && $objAlias->id != $intId && $autoAlias || !$varValue)
        {
            $varValue .= '-' . $intId;
        }

        return $varValue;
    }

    public static function getAliasIfAvailable($objItem, $strAutoItem = 'items')
    {
        return ltrim(
            ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ? '/' : '/' . $strAutoItem . '/') . ((!\Config::get('disableAlias')
                                                                                                                  && $objItem->alias
                                                                                                                     != '') ? $objItem->alias : $objItem->id),
            '/'
        );
    }

    /**
     * @deprecated - use Member::getMembersAsOptions() instead
     */
    public static function getMembersAsOptions(\DataContainer $objDc, $blnIncludeId = false)
    {
        return Member::getMembersAsOptions($objDc, $blnIncludeId);
    }

    /**
     * @deprecated - use Member::getMembersAsOptionsIncludingIds() instead
     */
    public static function getMembersAsOptionsIncludingIds(\DataContainer $objDc)
    {
        return Member::getMembersAsOptionsIncludingIds($objDc);
    }

    /**
     * @deprecated - use User::getUsersAsOptions() instead
     */
    public static function getUsersAsOptions(\DataContainer $objDc, $blnIncludeId = false)
    {
        return User::getUsersAsOptions($objDc, $blnIncludeId);
    }

    /**
     * @deprecated - use User::getUsersAsOptionsIncludingIds() instead
     */
    public static function getUsersAsOptionsIncludingIds(\DataContainer $objDc)
    {
        return User::getUsersAsOptionsIncludingIds($objDc);
    }

    public static function findAddressOnGoogleMaps($strStreet, $strPostal, $strCity, $strCountry)
    {
        $strAddress = sprintf('%s, %s %s %s', $strStreet, $strPostal, $strCity, $strCountry);
        $strAddress = urlencode($strAddress);

        $objCurl = curl_init();
        curl_setopt($objCurl, CURLOPT_URL, 'http://maps.googleapis.com/maps/api/geocode/json?address=' . $strAddress . '&sensor=false');
        curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, 1);

        if (\Config::get('hpProxy'))
        {
            curl_setopt($objCurl, CURLOPT_PROXY, \Config::get('hpProxy'));
        }

        $strResult = curl_exec($objCurl);
        curl_close($objCurl);

        // Request failed
        if (!$strResult)
        {
            \System::log('Could not get coordinates for: ' . $strAddress, __METHOD__, TL_ERROR);

            return null;
        }

        $objResponse = json_decode($strResult);

        return new WGS84($objResponse->results[0]->geometry->location->lat, $objResponse->results[0]->geometry->location->lng);
    }

    public static function setCoordinatesForDc($varValue, $objDc)
    {
        $objCoordinates = static::findAddressOnGoogleMaps(
            $objDc->activeRecord->street,
            $objDc->activeRecord->postal,
            $objDc->activeRecord->city,
            $GLOBALS['TL_LANG']['CNT'][$objDc->activeRecord->country]
        );

        return $objCoordinates->getLatitude() . ',' . $objCoordinates->getLongitude();
    }

    public static function setCoordinates($strStreet, $strPostal, $strCity, $strCountry)
    {
        $objCoordinates = static::findAddressOnGoogleMaps($strStreet, $strPostal, $strCity, $strCountry);

        return $objCoordinates->getLatitude() . ',' . $objCoordinates->getLongitude();
    }

    public static function getDataContainers()
    {
        $arrDCA = [];

        $arrModules = \ModuleLoader::getActive();

        if (!is_array($arrModules))
        {
            return $arrDCA;
        }

        foreach ($arrModules as $strModule)
        {
            $strDir = TL_ROOT . '/system/modules/' . $strModule . '/dca';

            if (file_exists($strDir))
            {
                foreach (scandir($strDir) as $strFile)
                {
                    if (substr($strFile, 0, 1) != '.' && file_exists($strDir . '/' . $strFile))
                    {
                        $arrDCA[] = str_replace('.php', '', $strFile);
                    }
                }
            }
        }

        $arrDCA = array_unique($arrDCA);
        sort($arrDCA);

        return $arrDCA;
    }

    public static function getFields(
        $strTable,
        $blnLocalized = true,
        $varInputType = null,
        $arrEvalFilters = [],
        $blnSort = true,
        array $arrSkipFields = ['id', 'tstamp', 'dateAdded', 'pid']
    ) {
        \Controller::loadDataContainer($strTable);
        \System::loadLanguageFile($strTable);

        $arrOptions = [];

        foreach ($GLOBALS['TL_DCA'][$strTable]['fields'] as $strField => $arrData)
        {
            // skip special fields
            if (in_array($strField, $arrSkipFields))
            {
                continue;
            }

            // input type
            if ($varInputType
                && (is_array($varInputType) && !empty($varInputType) ? !in_array($arrData['inputType'], $varInputType)
                    : $arrData['inputType'] != $varInputType)
            )
            {
                continue;
            }

            // eval filters
            if (!empty($arrEvalFilters))
            {
                foreach ($arrEvalFilters as $strKey => $varValue)
                {
                    if (!isset($arrData['eval'][$strKey]) || $arrData['eval'][$strKey] != $varValue)
                    {
                        continue 2;
                    }
                }
            }

            if ($blnLocalized)
            {
                $arrOptions[$strField] = $GLOBALS['TL_LANG'][$strTable][$strField][0] ?: $strField;
            }
            else
            {
                $arrOptions[$strField] = $strField;
            }
        }

        if ($blnSort)
        {
            asort($arrOptions);
        }

        return $arrOptions;
    }

    public static function getEditLink($strModule, $intId, $strLabel = null)
    {
        if ($intId)
        {
            $strLabel = sprintf(specialchars($strLabel ?: $GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $intId);

            return sprintf(
                ' <a href="contao/main.php?do=%s&amp;act=edit&amp;id=%s&amp;rt=%s" title="%s" style="padding-left:3px">%s</a>',
                $strModule,
                $intId,
                \RequestToken::get(),
                $strLabel,
                \Image::getHtml('alias.gif', $strLabel, 'style="vertical-align:top"')
            );
        }
    }

    public static function getModalEditLink($strModule, $intId, $strLabel = null, $strTable = '')
    {
        if ($intId)
        {
            $strLabel = sprintf(specialchars($strLabel ?: $GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $intId);

            return sprintf(
                ' <a href="contao/main.php?do=%s&amp;act=edit&amp;id=%s%s&amp;popup=1&amp;nb=1&amp;rt=%s" title="%s" '
                . 'style="padding-left:3px" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'%s'
                . '\',\'url\':this.href});return false">%s</a>',
                $strModule,
                $intId,
                ($strTable ? '&amp;table=' . $strTable : ''),
                \RequestToken::get(),
                $strLabel,
                $strLabel,
                \Image::getHtml('alias.gif', $strLabel, 'style="vertical-align:top"')
            );
        }
    }

    public static function getArchiveModalEditLink($strModule, $intId, $strTable, $strLabel = null)
    {
        if ($intId)
        {
            $strLabel = sprintf(specialchars($strLabel ?: $GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $intId);

            return sprintf(
                ' <a href="contao/main.php?do=%s&amp;id=%s&amp;table=%s&amp;popup=1&amp;nb=1&amp;rt=%s" title="%s" '
                . 'style="padding-left:3px" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'%s'
                . '\',\'url\':this.href});return false">%s</a>',
                $strModule,
                $intId,
                $strTable,
                \RequestToken::get(),
                $strLabel,
                $strLabel,
                \Image::getHtml('alias.gif', $strLabel, 'style="vertical-align:top"')
            );
        }
    }

    public static function getModelInstance($strTable, $intId)
    {
        $strItemClass = \Model::getClassFromTable($strTable);

        return $strItemClass ? $strItemClass::findByPk($intId) : null;
    }

    public static function getModelInstances($strTable, array $arrOptions = [])
    {
        $strItemClass = \Model::getClassFromTable($strTable);

        return $strItemClass ? $strItemClass::findAll($arrOptions) : null;
    }

    /**
     * Convenience method for lower casing in a save callback
     *
     * @param                $varValue
     * @param \DataContainer $objDc
     */
    public static function lowerCase($varValue, \DataContainer $objDc)
    {
        return trim(strtolower($varValue));
    }

    public static function setSessionIDOnCreate($strTable, $intId, $arrRow, \DataContainer $dc)
    {
        $objModel = static::getModelInstance($strTable, $intId);

        if ($objModel === null || !\Database::getInstance()->fieldExists(static::PROPERTY_SESSION_ID, $strTable))
        {
            return false;
        }

        $objModel->sessionID = session_id();
        $objModel->save();
    }

    public static function addSessionIDFieldAndCallback($strTable)
    {
        \Controller::loadDataContainer($strTable);

        // callback
        $GLOBALS['TL_DCA'][$strTable]['config']['oncreate_callback']['setSessionID'] =
            ['HeimrichHannot\Haste\Dca\General', 'setSessionIDOnCreate'];

        // field
        $GLOBALS['TL_DCA'][$strTable]['fields'][static::PROPERTY_SESSION_ID] = [
            'label' => &$GLOBALS['TL_LANG']['MSC']['haste_plus'][static::PROPERTY_SESSION_ID],
            'sql'   => "varchar(128) NOT NULL default ''",
        ];
    }

    public static function addAuthorFieldAndCallback($strTable)
    {
        \Controller::loadDataContainer($strTable);

        // callbacks
        $GLOBALS['TL_DCA'][$strTable]['config']['oncreate_callback']['setAuthorIDOnCreate']     =
            ['HeimrichHannot\Haste\Dca\General', 'setAuthorIDOnCreate'];
        $GLOBALS['TL_DCA'][$strTable]['config']['onload_callback']['modifyAuthorPaletteOnLoad'] =
            ['HeimrichHannot\Haste\Dca\General', 'modifyAuthorPaletteOnLoad', true];


        // fields
        $GLOBALS['TL_DCA'][$strTable]['fields'][static::PROPERTY_AUTHOR_TYPE] = [
            'label'     => &$GLOBALS['TL_LANG']['MSC']['haste_plus']['authorType'],
            'exclude'   => true,
            'filter'    => true,
            'default'   => static::AUTHOR_TYPE_NONE,
            'inputType' => 'select',
            'options'   => [
                static::AUTHOR_TYPE_NONE,
                static::AUTHOR_TYPE_MEMBER,
                static::AUTHOR_TYPE_USER,
            ],
            'reference' => $GLOBALS['TL_LANG']['MSC']['haste_plus']['authorType'],
            'eval'      => ['doNotCopy' => true, 'submitOnChange' => true, 'mandatory' => true, 'tl_class' => 'w50 clr'],
            'sql'       => "varchar(255) NOT NULL default 'none'",
        ];

        $GLOBALS['TL_DCA'][$strTable]['fields'][static::PROPERTY_AUTHOR] = [
            'label'            => &$GLOBALS['TL_LANG']['MSC']['haste_plus']['author'],
            'exclude'          => true,
            'search'           => true,
            'filter'           => true,
            'inputType'        => 'select',
            'options_callback' => ['HeimrichHannot\Haste\Dca\General', 'getMembersAsOptions'],
            'eval'             => [
                'doNotCopy'          => true,
                'chosen'             => true,
                'includeBlankOption' => true,
                'tl_class'           => 'w50',
            ],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ];
    }

    public static function setAuthorIDOnCreate($strTable, $intId, $arrRow, \DataContainer $dc)
    {
        $objModel = static::getModelInstance($strTable, $intId);

        if ($objModel === null || !\Database::getInstance()->fieldExists(static::PROPERTY_SESSION_ID, $strTable))
        {
            return false;
        }

        if (TL_MODE == 'FE')
        {
            if (FE_USER_LOGGED_IN)
            {
                $objModel->{static::PROPERTY_AUTHOR_TYPE} = static::AUTHOR_TYPE_MEMBER;
                $objModel->{static::PROPERTY_AUTHOR}      = \FrontendUser::getInstance()->id;
                $objModel->save();
            }
        }
        else
        {
            $objModel->{static::PROPERTY_AUTHOR_TYPE} = static::AUTHOR_TYPE_USER;
            $objModel->{static::PROPERTY_AUTHOR}      = \BackendUser::getInstance()->id;
            $objModel->save();
        }
    }

    public static function modifyAuthorPaletteOnLoad(\DataContainer $objDc)
    {
        if (TL_MODE != 'BE')
        {
            return;
        }

        if ($objDc === null || !$objDc->id)
        {
            return false;
        }

        if (($objModel = static::getModelInstance($objDc->table, $objDc->id)) === null)
        {
            return false;
        }

        $arrDca = &$GLOBALS['TL_DCA'][$objDc->table];

        // author handling
        if ($objModel->{static::PROPERTY_AUTHOR_TYPE} == static::AUTHOR_TYPE_NONE)
        {
            unset($arrDca['fields']['author']);
        }

        if ($objModel->{static::PROPERTY_AUTHOR_TYPE} == static::AUTHOR_TYPE_USER)
        {
            $arrDca['fields']['author']['options_callback'] = ['HeimrichHannot\Haste\Dca\User', 'getUsersAsOptions'];
        }
    }

    public static function getTableArchives($strChildTable, array $arrOptions = [])
    {
        \Controller::loadDataContainer($strChildTable);
        \System::loadLanguageFile($strChildTable);

        if (!isset($GLOBALS['TL_DCA'][$strChildTable]['config']['ptable']))
        {
            throw new \Exception('No parent table found for ' . $strChildTable);
        }

        return static::getModelInstances($GLOBALS['TL_DCA'][$strChildTable]['config']['ptable'], $arrOptions);
    }

    public static function getLocalizedFieldname($strField, $strTable)
    {
        \Controller::loadDataContainer($strTable);
        \System::loadLanguageFile($strTable);

        return $GLOBALS['TL_DCA'][$strTable]['fields'][$strField]['label'][0] ?: $strField;
    }
}
