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

use Contao\Config;
use Contao\Controller;
use Contao\DataContainer;
use Contao\Model;
use Contao\Model\Collection;
use Haste\Geodesy\Datum\WGS84;
use Haste\Util\Url;
use HeimrichHannot\Haste\Util\Curl;

class General extends \Backend
{
    const PROPERTY_SESSION_ID  = 'sessionID';
    const PROPERTY_AUTHOR      = 'author';
    const PROPERTY_AUTHOR_TYPE = 'authorType';

    const AUTHOR_TYPE_NONE   = 'none';
    const AUTHOR_TYPE_MEMBER = 'member';
    const AUTHOR_TYPE_USER   = 'user';

	const GOOGLE_MAPS_GEOCODE_URL = 'https://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false';

    /**
     * Set initial $varData from dca
     *
     * @param string $strTable Dca table name
     * @param mixed $varData Object or array
     *
     * @return mixed Object or array with the default values
     */
    public static function setDefaultsFromDca($strTable, $varData = null)
    {
        \Controller::loadDataContainer($strTable);

        if (empty($GLOBALS['TL_DCA'][$strTable])) {
            return $varData;
        }

        // Get all default values for the new entry
        foreach ($GLOBALS['TL_DCA'][$strTable]['fields'] as $k => $v) {
            // Use array_key_exists here (see #5252)
            if (array_key_exists('default', $v)) {
                if (is_object($varData)) {
                    $varData->{$k} = is_array($v['default']) ? serialize($v['default']) : $v['default'];

                    // Encrypt the default value (see #3740)
                    if ($GLOBALS['TL_DCA'][$strTable]['fields'][$k]['eval']['encrypt']) {
                        $varData->{$k} = \Encryption::encrypt($varData->{$k});
                    }
                } else {
                    if ($varData === null) {
                        $varData = [];
                    }

                    if (is_array($varData)) {
                        $varData[$k] = is_array($v['default']) ? serialize($v['default']) : $v['default'];

                        // Encrypt the default value (see #3740)
                        if ($GLOBALS['TL_DCA'][$strTable]['fields'][$k]['eval']['encrypt']) {
                            $varData[$k] = \Encryption::encrypt($varData[$k]);
                        }
                    }
                }
            }
        }

        return $varData;
    }

    /**
     * Retrieves an array from a dca config (in most cases eval) in the following priorities:
     *
     * 1. The value associated to $arrArray[$strProperty]
     * 2. The value retrieved by $arrArray[$strProperty . '_callback'] which is a callback array like ['Class', 'method']
     * 3. The value retrieved by $arrArray[$strProperty . '_callback'] which is a function closure array like ['Class', 'method']
     *
     * @param array $arrArray
     * @param       $strProperty
     * @param array $arrArgs
     *
     * @return mixed|null The value retrieved in the way mentioned above or null
     */
    public static function getConfigByArrayOrCallbackOrFunction(array $arrArray, $strProperty, array $arrArgs = [])
    {
        if (isset($arrArray[$strProperty])) {
            return $arrArray[$strProperty];
        }

        if (is_array($arrArray[$strProperty . '_callback'])) {
            $arrCallback = $arrArray[$strProperty.'_callback'];

            $instance = Controller::importStatic($arrCallback[0]);

            return call_user_func_array([$instance, $arrCallback[1]], $arrArgs);
        } elseif (is_callable($arrArray[$strProperty . '_callback'])) {
            return call_user_func_array($arrArray[$strProperty . '_callback'], $arrArgs);
        }

        return null;
    }

    public static function addOverridableFields($arrFields, $strSourceTable, $strDestinationTable, $arrCheckboxDca = [])
    {
        \Controller::loadDataContainer($strSourceTable);
        \System::loadLanguageFile($strSourceTable);
        $arrSourceDca = $GLOBALS['TL_DCA'][$strSourceTable];

        \Controller::loadDataContainer($strDestinationTable);
        \System::loadLanguageFile($strDestinationTable);
        $arrDestinationDca = &$GLOBALS['TL_DCA'][$strDestinationTable];

        foreach ($arrFields as $strField) {
            // add override boolean field
            $strOverrideFieldName = 'override' . ucfirst($strField);

            $arrDestinationDca['fields'][$strOverrideFieldName] = array_merge([
                'label'     => &$GLOBALS['TL_LANG'][$strDestinationTable][$strOverrideFieldName],
                'exclude'   => true,
                'inputType' => 'checkbox',
                'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
                'sql'       => "char(1) NOT NULL default ''",
            ], $arrCheckboxDca);

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
     * @param string $strProperty The property name to retrieve
     * @param array $arrInstances An array of instances in ascending priority. Instances can be passed in the following form:
     *                             ['tl_some_table', $intInstanceId] or $objInstance
     *
     * @return mixed
     */
    public static function getOverridableProperty($strProperty, array $arrInstances)
    {
        $varResult            = null;
        $arrPreparedInstances = [];

        // prepare instances
        foreach ($arrInstances as $varInstance) {
            if (is_array($varInstance)) {
                if (($objInstance = static::getModelInstance($varInstance[0], $varInstance[1])) !== null) {
                    $arrPreparedInstances[] = $objInstance;
                }
            } elseif ($varInstance instanceof \Model) {
                $arrPreparedInstances[] = $varInstance;
            }
        }

        foreach ($arrPreparedInstances as $i => $objInstance) {
            if ($i == 0 || $objInstance->{'override' . ucfirst($strProperty)}) {
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
        if ($objDc === null || !$objDc->id || ($objDc->activeRecord && $objDc->activeRecord->dateAdded > 0)) {
            return false;
        }

        \Database::getInstance()->prepare("UPDATE $objDc->table SET dateAdded=? WHERE id=? AND dateAdded = 0")->execute(time(), $objDc->id);
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
     * @param array $arrPalettes The palettes in which to insert the field
     */
    public static function addAliasToDca($strDca, array $arrGenerateAliasCallback, $strPaletteField, $arrPalettes = ['default'])
    {
        \Controller::loadDataContainer($strDca);

        $arrDca = &$GLOBALS['TL_DCA'][$strDca];

        // add to palettes
        foreach ($arrPalettes as $strPalette) {
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
            ['HeimrichHannot\Haste\Dca\General', 'doAddAliasButton']
        ];
    }

    /**
     * Generic method for automatically generating aliases
     *
     * @param array $arrButtons
     * @param \DataContainer $dc
     *
     * @return array
     */
    public function doAddAliasButton($arrButtons, \DataContainer $dc)
    {
        // Generate the aliases
        if (\Input::post('FORM_SUBMIT') == 'tl_select' && isset($_POST['alias'])) {
            if (version_compare(VERSION, '4.0', '<')) {
                $objSessionData = \Session::getInstance()->getData();
                $arrIds         = $objSessionData['CURRENT']['IDS'];
            } else {
                /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $objSession */
                $objSession = \System::getContainer()->get('session');
                $session    = $objSession->all();
                $arrIds     = $session['CURRENT']['IDS'];
            }

            $strItemClass = \Model::getClassFromTable($dc->table);

            if (!class_exists($strItemClass)) {
                return $arrButtons;
            }

            foreach ($arrIds as $intId) {
                $objItem = $strItemClass::findByPk($intId);

                if ($objItem === null) {
                    continue;
                }

                $dc->id           = $intId;
                $dc->activeRecord = $objItem;

                $strAlias = '';

                // Generate new alias through save callbacks
                foreach ($GLOBALS['TL_DCA'][$dc->table]['fields']['alias']['save_callback'] as $callback) {
                    if (is_array($callback)) {
                        $this->import($callback[0]);
                        $strAlias = $this->{$callback[0]}->{$callback[1]}($strAlias, $dc);
                    } elseif (is_callable($callback)) {
                        $strAlias = $callback($strAlias, $dc);
                    }
                }

                // The alias has not changed
                if ($strAlias == $objItem->alias) {
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
        if (version_compare(VERSION, '4.0', '<')) {
            $arrButtons['alias'] = '<input type="submit" name="alias" id="alias" class="tl_submit" accesskey="a" value="' . specialchars(
                    $GLOBALS['TL_LANG']['MSC']['aliasSelected']
                ) . '"> ';
        } else {
            $arrButtons['alias'] = '<button type="submit" name="alias" id="alias" class="tl_submit" accesskey="a">' .
                $GLOBALS['TL_LANG']['MSC']['aliasSelected'] .
                '</button> ';
        }

        return $arrButtons;
    }

    /**
     * @param $varValue mixed The current alias (if available)
     * @param $intId    int The entity's id
     * @param $strTable string The entity's table
     * @param $strBase string The value to use as a base for the alias
     * @param $blnKeepUmlauts bool Set to true if German umlauts should be kept
     *
     * @return string
     * @throws \Exception
     */
    public static function generateAlias($varValue, $intId, $strTable, $strBase, $blnKeepUmlauts = true)
    {
        $autoAlias = false;

        // Generate alias if there is none
        if ($varValue == '') {
            $autoAlias = true;
            $varValue  = \StringUtil::generateAlias($strBase);
        }

        if (!$blnKeepUmlauts) {
            $varValue = preg_replace(['/ä/i', '/ö/i', '/ü/i', '/ß/i'], ['ae', 'oe', 'ue', 'ss'], $varValue);
        }

        $objAlias = \Database::getInstance()->prepare("SELECT id FROM $strTable WHERE alias=?")->execute($varValue);

        // Check whether the alias exists
        if ($objAlias->numRows > 1 && !$autoAlias) {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
        }

        // Add ID to alias
        if ($objAlias->numRows && $objAlias->id != $intId && $autoAlias || !$varValue) {
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
        $address = sprintf('%s, %s %s %s', $strStreet, $strPostal, $strCity, $strCountry);
        return static::findFuzzyAddressOnGoogleMaps($address);
    }



    public static function findFuzzyAddressOnGoogleMaps($address)
    {
		$url = sprintf(static::GOOGLE_MAPS_GEOCODE_URL, urlencode($address));

		if (in_array('dlh_googlemaps', \ModuleLoader::getActive()) && Config::get('dlh_googlemaps_apikey')) {
			$apiKey = Config::get('dlh_googlemaps_apikey');
			$url = Url::addQueryString('key='.$apiKey, $url);
		}

		$strResult = Curl::request($url);

		// Request failed
		if (!$strResult) {
			\System::log('Could not get coordinates for: ' . $address, __METHOD__, TL_ERROR);

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
        $dca = [];

        foreach ($GLOBALS['BE_MOD'] as $arrSection) {
            foreach ($arrSection as $strModule => $arrModule) {
                foreach ($arrModule as $strKey => $varValue) {
                    if (isset($arrModule['tables']) && is_array($arrModule['tables'])) {
                        $dca = array_merge($dca, $arrModule['tables']);
                    }
                }
            }
        }

        $dca = array_unique($dca);

        asort($dca);

        return array_values($dca);
    }

    /**
     * @param $strTable
     * @param $blnLocalized
     * @param array|string|null $varInputType
     * @param $arrEvalFilters
     * @param $blnSort
     * @param array $arrSkipFields
     * @return array
     */
    public static function getFields(
        $strTable,
        $blnLocalized = true,
        $varInputType = null,
        $arrEvalFilters = [],
        $blnSort = true,
        array $arrSkipFields = []
    ) {
        \Controller::loadDataContainer($strTable);
        \System::loadLanguageFile($strTable);

        $arrOptions = [];

        $evaluateInputType = false;
        if (!empty($varInputType)) {
            if (is_string($varInputType)) {
                $varInputType = [$varInputType];
            }
            $evaluateInputType = true;
        }

        foreach ($GLOBALS['TL_DCA'][$strTable]['fields'] as $strField => $arrData) {
            // skip special fields
            if (in_array($strField, $arrSkipFields)) {
                continue;
            }

            // input type
            if ($evaluateInputType) {
                if (empty($arrData['inputType']) || !in_array($arrData['inputType'], $varInputType)) {
                    continue;
                }
            }

            // eval filters
            if (!empty($arrEvalFilters)) {
                foreach ($arrEvalFilters as $strKey => $varValue) {
                    if (!isset($arrData['eval'][$strKey]) || $arrData['eval'][$strKey] != $varValue) {
                        continue 2;
                    }
                }
            }

            if ($blnLocalized) {
                $arrOptions[$strField] = $GLOBALS['TL_LANG'][$strTable][$strField][0] ?: $strField;
            } else {
                $arrOptions[$strField] = $strField;
            }
        }

        if ($blnSort) {
            asort($arrOptions);
        }

        return $arrOptions;
    }

    public static function getEditLink($strModule, $intId, $strLabel = null)
    {
        if ($intId) {
            $strLabel = sprintf(specialchars($strLabel ?: $GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $intId);

            return sprintf(
                ' <a href="contao/main.php?do=%s&amp;act=edit&amp;id=%s&amp;rt=%s" title="%s" style="padding-left: 5px; padding-top: 2px; display: inline-block;">%s</a>',
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
        if ($intId) {
            $strLabel = sprintf(specialchars($strLabel ?: $GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $intId);

            return sprintf(
                ' <a href="contao/main.php?do=%s&amp;act=edit&amp;id=%s%s&amp;popup=1&amp;nb=1&amp;rt=%s" title="%s" '
                . 'style="padding-left: 5px; padding-top: 2px; display: inline-block;" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'%s' . '\',\'url\':this.href});return false">%s</a>',
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
        if ($intId) {
            $strLabel = sprintf(specialchars($strLabel ?: $GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $intId);

            return sprintf(
                ' <a href="contao/main.php?do=%s&amp;id=%s&amp;table=%s&amp;popup=1&amp;nb=1&amp;rt=%s" title="%s" '
                . 'style="padding-left:3px; float: right" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'%s' . '\',\'url\':this.href});return false">%s</a>',
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

    public static function valueChangedInCallback($newValue, DataContainer $dc)
    {
        if (null !== ($entity = static::getModelInstance($dc->table, $dc->id))) {
            return $newValue != $entity->{$dc->field};
        }

        return true;
    }

    public static function getModelInstancePropertyValue($property, $table, $id)
    {
        if (null !== ($entity = static::getModelInstance($table, $id))) {
            return $entity->{$property};
        }

        return null;
    }

    public static function getModelInstance($strTable, $intId)
    {
        $strItemClass = \Model::getClassFromTable($strTable);

        return class_exists($strItemClass) ? $strItemClass::findByPk($intId) : null;
    }

    public static function getModelInstanceIfId($varInstance, $strTable)
    {
        if ($varInstance instanceof Model) {
            return $varInstance;
        }

        if ($varInstance instanceof Collection) {
            return $varInstance->current();
        }

        return static::getModelInstance($strTable, $varInstance);
    }

    public static function getModelInstances($strTable, array $arrOptions = [])
    {
        $strItemClass = \Model::getClassFromTable($strTable);

        return class_exists($strItemClass) ? $strItemClass::findAll($arrOptions) : null;
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

        if ($objModel === null || !\Database::getInstance()->fieldExists(static::PROPERTY_SESSION_ID, $strTable)) {
            return false;
        }

        $objModel->sessionID = session_id();
        $objModel->save();
    }

    public static function addSessionIDFieldAndCallback($strTable)
    {
        \Controller::loadDataContainer($strTable);

        // callback
        $GLOBALS['TL_DCA'][$strTable]['config']['oncreate_callback']['setSessionID'] = ['HeimrichHannot\Haste\Dca\General', 'setSessionIDOnCreate'];

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
        $GLOBALS['TL_DCA'][$strTable]['config']['oncreate_callback']['setAuthorIDOnCreate']     = ['HeimrichHannot\Haste\Dca\General', 'setAuthorIDOnCreate'];
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

        if ($objModel === null
            || !\Database::getInstance()->fieldExists(static::PROPERTY_AUTHOR_TYPE, $strTable)
            || !\Database::getInstance()->fieldExists(static::PROPERTY_AUTHOR, $strTable)
        ) {
            return false;
        }

        if (TL_MODE == 'FE') {
            if (FE_USER_LOGGED_IN) {
                $objModel->{static::PROPERTY_AUTHOR_TYPE} = static::AUTHOR_TYPE_MEMBER;
                $objModel->{static::PROPERTY_AUTHOR}      = \FrontendUser::getInstance()->id;
                $objModel->save();
            }
        } else {
            $objModel->{static::PROPERTY_AUTHOR_TYPE} = static::AUTHOR_TYPE_USER;
            $objModel->{static::PROPERTY_AUTHOR}      = \BackendUser::getInstance()->id;
            $objModel->save();
        }
    }

    public static function modifyAuthorPaletteOnLoad(\DataContainer $objDc)
    {
        if (TL_MODE != 'BE') {
            return;
        }

        if ($objDc === null || !$objDc->id) {
            return false;
        }

        if (($objModel = static::getModelInstance($objDc->table, $objDc->id)) === null) {
            return false;
        }

        $arrDca = &$GLOBALS['TL_DCA'][$objDc->table];

        // author handling
        if ($objModel->{static::PROPERTY_AUTHOR_TYPE} == static::AUTHOR_TYPE_NONE) {
            unset($arrDca['fields']['author']);
        }

        if ($objModel->{static::PROPERTY_AUTHOR_TYPE} == static::AUTHOR_TYPE_USER) {
            $arrDca['fields']['author']['options_callback'] = ['HeimrichHannot\Haste\Dca\User', 'getUsersAsOptions'];
        }
    }

    public static function getTableArchives($strChildTable, array $arrOptions = [])
    {
        \Controller::loadDataContainer($strChildTable);
        \System::loadLanguageFile($strChildTable);

        if (!isset($GLOBALS['TL_DCA'][$strChildTable]['config']['ptable'])) {
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

    public static function getOptionsFromDca($strTable, $strField, $blnLocalizeOptions = true, \DataContainer $objDc = null)
    {
        \Controller::loadDataContainer($strTable);

        $arrOptions  = [];
        $arrFieldDca = $GLOBALS['TL_DCA'][$strTable]['fields'][$strField];

        if ((is_array($arrFieldDca['options_callback']) || is_callable($arrFieldDca['options_callback'])) && !$arrFieldDca['reference']) {
            if (is_array($arrFieldDca['options_callback'])) {
                $strClass  = $arrFieldDca['options_callback'][0];
                $strMethod = $arrFieldDca['options_callback'][1];

                $arrOptions = @$strClass->{$strMethod}($objDc);
            } elseif (is_callable($arrFieldDca['options_callback'])) {
                $arrOptions = @$arrFieldDca['options_callback']($objDc);
            }
        }

        if (is_array($arrFieldDca['options'])) {
            $arrOptions = $arrFieldDca['options'];
        }

        if (!$blnLocalizeOptions) {
            return $arrOptions;
        } else {
            if (!empty($arrOptions) && is_array($arrFieldDca['reference'])) {
                $arrReference = $arrFieldDca['reference'];

                return array_combine($arrOptions, array_map(function ($varValue) use ($arrReference) {
                    return $arrReference[$varValue];
                }, $arrOptions));
            }
        }

        return $arrOptions;
    }

    public static function checkUrl($varValue, \DataContainer $objDc)
    {
        if ($varValue && strpos($varValue, 'http://') === false && strpos($varValue, 'https://') === false) {
            $varValue = 'http://' . $varValue;
        }

        return $varValue;
    }
}
