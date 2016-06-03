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


class FormSubmission
{
	public static function prepareSpecialValueForPrint($varValue, $arrData, $strTable, $objDc, $objItem = null)
	{
		$varValue = deserialize($varValue);
		$arrOpts  = $arrData['options'];
		$arrReference  = $arrData['reference'];
		$strRegExp = $arrData['eval']['rgxp'];

		// get options
		if ((is_array($arrData['options_callback']) || is_callable($arrData['options_callback'])) &&
			!$arrData['reference'])
		{
			if (is_array($arrData['options_callback']))
			{
				$strClass  = $arrData['options_callback'][0];
				$strMethod = $arrData['options_callback'][1];

				$objInstance = \Controller::importStatic($strClass);

				$arrOptionsCallback = @$objInstance->$strMethod($objDc);
			}
			elseif (is_callable($arrData['options_callback']))
			{
				$arrOptionsCallback = @$arrData['options_callback']($objDc);
			}

			$arrOptions = !is_array($varValue) ? array($varValue) : $varValue;

			if ($varValue !== null)
			{
				$varValue = array_intersect_key($arrOptionsCallback, array_flip($arrOptions));
			}
		}

		if ($strRegExp == 'date')
		{
			$varValue = \Date::parse(\Config::get('dateFormat'), $varValue);
		}
		elseif ($strRegExp == 'time')
		{
			$varValue = \Date::parse(\Config::get('timeFormat'), $varValue);
		}
		elseif ($strRegExp == 'datim')
		{
			$varValue = \Date::parse(\Config::get('datimFormat'), $varValue);
		}
		elseif ($arrData['inputType'] == 'tag' && in_array('tags_plus', \ModuleLoader::getActive()))
		{
			if (($arrTags = \HeimrichHannot\TagsPlus\TagsPlus::loadTags($strTable, $objItem->id)) !== null)
				$varValue = implode(', ', $arrTags);
		}
		elseif ($arrData['inputType'] == 'multifileupload')
		{
			if (is_array($varValue))
			{
				$varValue = implode(', ', array_map(
					function ($val) {
						$strPath = Files::getPathFromUuid($val);

						return $strPath ?: $val;
					},
					$varValue
				));
			}
			else
			{
				$strPath = Files::getPathFromUuid($varValue);
				$varValue   = $strPath ?: $varValue;
			}
		}
		elseif (is_array($varValue))
		{
			if (!$arrReference)
			{
				$varValue = array_map(function($varValue) use ($arrOpts) {
					return isset($arrOpts[$varValue]) ? $arrOpts[$varValue] : $varValue;
				}, $varValue);
			}

			$varValue = Arrays::flattenArray($varValue);

			$varValue = array_filter($varValue); // remove empty elements

			$varValue = implode(
				', ',
				array_map(
					function ($varValue) use ($arrReference) {
						if (is_array($arrReference)) {
							return isset($arrReference[$varValue]) ?
								((is_array($arrReference[$varValue])) ? $arrReference[$varValue][0] : $arrReference[$varValue])
								: $varValue;
						} else {
							return $varValue;
						}
					},
					$varValue
				)
			);
		}
		elseif (is_array($arrOpts) && array_is_assoc($arrOpts))
		{
			$varValue = isset($arrOpts[$varValue]) ? $arrOpts[$varValue] : $varValue;
		}
		elseif (is_array($arrReference))
		{
			$varValue = isset($arrReference[$varValue]) ?
				((is_array($arrReference[$varValue])) ? $arrReference[$varValue][0] : $arrReference[$varValue])
				: $varValue;
		}
		elseif ($arrData['inputType'] == 'fileTree')
		{
			if ($arrData['eval']['multiple'] && is_array($varValue))
			{
				$varValue = array_map(
					function ($val) {
						$strPath = Files::getPathFromUuid($val);

						return $strPath ?: $val;
					},
					$varValue
				);
			}
			else
			{
				$strPath = Files::getPathFromUuid($varValue);
				$varValue   = $strPath ?: $varValue;
			}
		}
		elseif (\Validator::isBinaryUuid($varValue))
		{
			$varValue = \StringUtil::binToUuid($varValue);
		}

		// Convert special characters (see #1890)
		return specialchars($varValue);
	}

	public static function prepareSpecialValueForSave($varValue, $arrData, $strTable = null, $intId = 0,
		$varDefault = null, &$arrWidgetErrors = array())
	{
		// Convert date formats into timestamps
		if ($varValue != '' && in_array($arrData['eval']['rgxp'], array('date', 'time', 'datim')))
		{
			try
			{
				$objDate  = new \Date($varValue, \Config::get($arrData['eval']['rgxp'] . 'Format'));
				$varValue = $objDate->tstamp;
			}
			catch (\OutOfBoundsException $e)
			{
				$arrWidgetErrors[] = sprintf($GLOBALS['TL_LANG']['ERR']['invalidDate'], $varValue);
				return $varDefault;
			}
		}

		if ($arrData['eval']['multiple'] && isset($arrData['eval']['csv']))
		{
			$varValue = implode($arrData['eval']['csv'], deserialize($varValue, true));
		}

		if ($arrData['inputType'] == 'tag' && in_array('tags_plus', \ModuleLoader::getActive()))
		{
			$varValue = \HeimrichHannot\TagsPlus\TagsPlus::loadTags($strTable, $intId);
		}

		if ($arrData['eval']['encrypt'])
		{
			$varValue = \Encryption::encrypt($varValue);
		}

		return $varValue;
	}
}