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
	/**
	 * Adds a date added field to the dca and sets the appropriate callback
	 * @param $strDca
	 */
	public static function addDateAddedToDca($strDca)
	{
		\Controller::loadDataContainer($strDca);

		$arrDca = &$GLOBALS['TL_DCA'][$strDca];

		$arrDca['config']['onsubmit_callback']['setDateAdded'] = array('HeimrichHannot\\HastePlus\\Utilities', 'setDateAdded');

		$arrDca['fields']['dateAdded'] = static::getDateAddedField();
	}

	/**
	 * Sets the current date as the date added -> callback function
	 * @param \DataContainer $objDc
	 */
	public static function setDateAdded(\DataContainer $objDc)
	{
		// Return if there is no active record (override all)
		if (!$objDc->activeRecord || $objDc->activeRecord->dateAdded > 0) {
			return;
		}

		$time = time();

		$strTable = $objDc->__get('table');

		\Database::getInstance()->prepare("UPDATE $strTable SET dateAdded=? WHERE id=?")
			->execute($time, $objDc->activeRecord->id);
	}

	/**
	 * @return array The dca for the data added field
	 */
	public static function getDateAddedField()
	{
		return array
		(
			'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
			'sorting' => true,
			'eval'    => array('rgxp' => 'datim', 'doNotCopy' => true),
			'sql'     => "int(10) unsigned NOT NULL default '0'"
		);
	}

	/**
	 * Adds an alias field to the dca and to the desired palettes
	 * @param       $strDca
	 * @param       $arrGenerateAliasCallback array The callback to call for generating the alias
	 * @param       $strPaletteField String The field after which to insert the alias field in the palettes
	 * @param array $arrPalettes The palettes in which to insert the field
	 */
	public static function addAliasToDca($strDca, array $arrGenerateAliasCallback, $strPaletteField, $arrPalettes = array('default'))
	{
		\Controller::loadDataContainer($strDca);

		$arrDca = &$GLOBALS['TL_DCA'][$strDca];

		// add to palettes
		foreach ($arrPalettes as $strPalette)
		{
			$arrDca['palettes'][$strPalette] =
					str_replace($strPaletteField . ',', $strPaletteField . ',alias,', $arrDca['palettes'][$strPalette]);
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
		return array
		(
			'label'     => &$GLOBALS['TL_LANG']['MSC']['alias'],
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'alias', 'unique' => true, 'maxlength' => 128, 'tl_class' => 'w50'),
			'save_callback' => array($arrGenerateAliasCallback),
			'sql' => "varchar(128) COLLATE utf8_bin NOT NULL default ''"
		);
	}

	/**
	 * Adds a button for batch generating aliases
	 * @param $strTable
	 */
	public static function addAliasButton($strTable)
	{
		\Controller::loadDataContainer($strTable);

		$GLOBALS['TL_DCA'][$strTable]['select']['buttons_callback'] = array(
			array('HeimrichHannot\Haste\Dca\General', 'doAddAliasButton')
		);
	}

	/**
	 * Generic method for automatically generating aliases
	 *
	 * @param array         $arrButtons
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
			$arrIds = $objSessionData['CURRENT']['IDS'];

			foreach ($arrIds as $intId)
			{
				$strItemClass = \Model::getClassFromTable($dc->table);

				$objItem = $strItemClass::findByPk($intId);

				if ($objItem === null)
				{
					continue;
				}

				$dc->id = $intId;
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
				\Database::getInstance()->prepare("UPDATE $dc->table SET alias=? WHERE id=?")
						->execute($strAlias, $intId);

				// Create a new version
				$objVersions->create();
			}

			\Controller::redirect($this->getReferer());
		}

		// Add the button
		$arrButtons['alias'] = '<input type="submit" name="alias" id="alias" class="tl_submit" accesskey="a" value="'.specialchars($GLOBALS['TL_LANG']['MSC']['aliasSelected']).'"> ';

		return $arrButtons;
	}

	public static function generateAlias($varValue, $intId, $strTable, $strAlias)
	{
		$autoAlias = false;

		// Generate alias if there is none
		if ($varValue == '')
		{
			$autoAlias = true;
			$varValue = \StringUtil::generateAlias($strAlias);
		}

		$objAlias = \Database::getInstance()->prepare("SELECT id FROM $strTable WHERE alias=?")
				->execute($varValue);

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
		return ltrim(((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ? '/' : '/' . $strAutoItem . '/') .
				((!\Config::get('disableAlias') && $objItem->alias != '') ? $objItem->alias : $objItem->id), '/');
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

	public static function setCoordinatesForDc($varValue, $objDc)
	{
		$objCoordinates = WGS84::findAddressOnGoogleMaps($objDc->activeRecord->street, $objDc->activeRecord->postal,
				$objDc->activeRecord->city, $GLOBALS['TL_LANG']['CNT'][$objDc->activeRecord->country]);

		return $objCoordinates->getLatitude() . ',' . $objCoordinates->getLongitude();
	}

	public static function setCoordinates($strStreet, $strPostal, $strCity, $strCountry)
	{
		$objCoordinates = WGS84::findAddressOnGoogleMaps($strStreet, $strPostal, $strCity, $strCountry);

		return $objCoordinates->getLatitude() . ',' . $objCoordinates->getLongitude();
	}

	public static function getDataContainers()
	{
		$arrDCA = array();

		$arrModules = \ModuleLoader::getActive();

		if (!is_array($arrModules)) {
			return $arrDCA;
		}

		foreach ($arrModules as $strModule) {
			$strDir = TL_ROOT . '/system/modules/' . $strModule . '/dca';

			if (file_exists($strDir)) {
				foreach (scandir($strDir) as $strFile) {
					if (substr($strFile, 0, 1) != '.' && file_exists($strDir . '/' . $strFile)) {
						$arrDCA[] = str_replace('.php', '', $strFile);
					}
				}
			}
		}

		$arrDCA = array_unique($arrDCA);
		sort($arrDCA);

		return $arrDCA;
	}

	public static function getFields($strTable, $blnLocalized = true, $varInputType = null, $arrEvalFilters = array(), $blnSort = true) {
		\Controller::loadDataContainer($strTable);
		\System::loadLanguageFile($strTable);

		$arrOptions = array();

		foreach($GLOBALS['TL_DCA'][$strTable]['fields'] as $strField => $arrData) {
			// input type
			if ($varInputType && (is_array($varInputType) && !empty($varInputType) ? !in_array($arrData['inputType'], $varInputType) : $arrData['inputType'] != $varInputType))
				continue;

			// eval filters
			if (!empty($arrEvalFilters))
			{
				foreach ($arrEvalFilters as $strKey => $varValue)
				{
					if (!isset($arrData['eval'][$strKey]) || $arrData['eval'][$strKey] != $varValue)
						continue 2;
				}
			}

			if ($blnLocalized)
				$arrOptions[$strField] = $GLOBALS['TL_LANG'][$strTable][$strField][0] ?: $strField;
			else
				$arrOptions[$strField] = $strField;
		}

		if ($blnSort)
			asort($arrOptions);

		return $arrOptions;
	}

	public static function getEditLink($strModule, $intId, $strLabel = null)
	{
		if ($intId)
		{
			$strLabel = sprintf(specialchars($strLabel ?: $GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $intId);

			return sprintf(' <a href="contao/main.php?do=%s&amp;act=edit&amp;id=%s&amp;rt=%s" title="%s" style="padding-left:3px">%s</a>',
					$strModule, $intId, \RequestToken::get(), $strLabel,
					\Image::getHtml('alias.gif', $strLabel, 'style="vertical-align:top"'));
		}
	}

	public static function getModalEditLink($strModule, $intId, $strLabel = null, $strTable = '')
	{
		if ($intId)
		{
			$strLabel = sprintf(specialchars($strLabel ?: $GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $intId);
			return sprintf(
					' <a href="contao/main.php?do=%s&amp;act=edit&amp;id=%s%s&amp;popup=1&amp;nb=1&amp;rt=%s" title="%s" ' .
 					'style="padding-left:3px" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'%s' .
					'\',\'url\':this.href});return false">%s</a>',
					$strModule, $intId, ($strTable ? '&amp;table=' . $strTable : ''), \RequestToken::get(), $strLabel,
					$strLabel, \Image::getHtml('alias.gif', $strLabel, 'style="vertical-align:top"')
			);
		}
	}

	public static function getModelInstance($strTable, $intId)
	{
		$strItemClass = \Model::getClassFromTable($strTable);

		return $strItemClass::findByPk($intId);
	}

	public static function getFormattedValueByDca($varValue, $arrData, $objDc)
	{
		$varValue = deserialize($varValue);
		$opts  = $arrData['options'];
		$rfrc  = $arrData['reference'];
		$rgxp = $arrData['eval']['rgxp'];

		// Call the options_callback to get the formatted value
		if ((is_array($arrData['options_callback']) || is_callable($arrData['options_callback'])) && !$arrData['reference']) {
			if (is_array($arrData['options_callback'])) {
				$strClass  = $arrData['options_callback'][0];
				$strMethod = $arrData['options_callback'][1];

				$objInstance = \Controller::importStatic($strClass);

				try {
					$options_callback = @$objInstance->$strMethod($objDc);
				} catch (\Exception $e)
				{
					\System::log("$strClass::$strMethod raised an Exception: $e->getMessage()", __METHOD__, TL_ERROR);
				}
			} elseif (is_callable($arrData['options_callback'])) {
				try {
					$options_callback = @$arrData['options_callback']($objDc);
				} catch (\Exception $e)
				{
					$strCallback = serialize($arrData['options_callback']);
					\System::log("$strCallback raised an Exception: $e->getMessage()", __METHOD__, TL_ERROR);
				}
			}

			$arrOptions = !is_array($varValue) ? array($varValue) : $varValue;

			if ($varValue !== null)
				$varValue = array_intersect_key($options_callback, array_flip($arrOptions));
		}

		if ($rgxp == 'date') {
			$varValue = \Date::parse(\Config::get('dateFormat'), $varValue);
		} elseif ($rgxp == 'time') {
			$varValue = \Date::parse(\Config::get('timeFormat'), $varValue);
		} elseif ($rgxp == 'datim') {
			$varValue = \Date::parse(\Config::get('datimFormat'), $varValue);
		} elseif (is_array($varValue)) {
			$varValue = Arrays::flattenArray($varValue);

			$varValue = array_filter($varValue); // remove empty elements

			$varValue = implode(
				', ',
				array_map(
					function ($varValue) use ($rfrc) {
						if (is_array($rfrc)) {
							return isset($rfrc[$varValue]) ? ((is_array($rfrc[$varValue])) ? $rfrc[$varValue][0] : $rfrc[$varValue]) : $varValue;
						} else {
							return $varValue;
						}
					},
					$varValue
				)
			);
		} elseif (is_array($opts) && array_is_assoc($opts)) {
			$varValue = isset($opts[$varValue]) ? $opts[$varValue] : $varValue;
		} elseif (is_array($rfrc)) {
			$varValue = isset($rfrc[$varValue]) ? ((is_array($rfrc[$varValue])) ? $rfrc[$varValue][0] : $rfrc[$varValue]) : $varValue;
		} elseif ($arrData['inputType'] == 'fileTree') {
			if ($arrData['eval']['multiple'] && is_array($varValue)) {
				$varValue = array_map(
					function ($val) {
						$strPath = Files::getPathFromUuid($val);

						return $strPath ?: $val;
					},
					$varValue
				);
			} else {
				$strPath = Files::getPathFromUuid($varValue);
				$varValue   = $strPath ?: $varValue;
			}
		} elseif (\Validator::isBinaryUuid($varValue)) {
			$varValue = \StringUtil::binToUuid($varValue);
		}

		// Convert special characters (see #1890)
		return specialchars($varValue);
	}
}