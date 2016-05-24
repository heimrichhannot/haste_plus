<?php

namespace HeimrichHannot\Haste;
use HeimrichHannot\SymbolicDateTime\Contao\Date;

/**
	 * Contao Open Source CMS
	 *
	 * Copyright (c) 2016 Heimrich & Hannot GmbH
	 *
	 * @package haste_plus
	 * @author  Dennis Patzer <d.patzer@heimrich-hannot.de>
	 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
	 */

class DateUtil {

	/**
	 * Helper function for getting a symbolic date
	 * @param int
	 */
	public static function getSymbolicDate1($start = null, $end = null) {
		switch ($GLOBALS['TL_LANGUAGE']) {
			case 'en':
				if (!$start)
					$start = time();

				if ($end)
					return date('jS ', $start) . XCommonLocalization::getMonthName(date('m', $start), XCommonLocalization::ENGLISH) . date(' Y', $start) . ' &ndash; ' .
					date('jS ', $end) . XCommonLocalization::getMonthName(date('m', $end), XCommonLocalization::ENGLISH) . date(' Y', $end);
				else
					return date('jS ', $start) . XCommonLocalization::getMonthName(date('m', $start), XCommonLocalization::ENGLISH) . date(' Y', $start);
				break;
		}

		if (!$start)
			$start = time();
		if ($end)
			return date('d. ', $start) . XCommonLocalization::getMonthName(date('m', $start)) . date(' Y', $start) . ' &ndash; ' .
				date('d. ', $end) . XCommonLocalization::getMonthName(date('m', $end)) . date(' Y', $end);
		else
			return date('d. ', $start) . XCommonLocalization::getMonthName(date('m', $start)) . date(' Y', $start);
	}

	public static function getSymbolicDateTime1($start = null, $end = null, $startTime = null, $endTime = null) {
		switch ($GLOBALS['TL_LANGUAGE']) {
			case 'en':
				if (!$start)
					$start = time();

				$strTime = '';
				if ($startTime)
					$strTime = ', ' . date('h.i A', $startTime) . ($endTime && $startTime != $endTime ? ' &ndash; ' . date('h.i A', $endTime) : '');

				if ($end)
					return date('jS ', $start) . XCommonLocalization::getMonthName(date('m', $start), XCommonLocalization::ENGLISH) . date(' Y', $start) . ' &ndash; ' .
					date('jS ', $end) . XCommonLocalization::getMonthName(date('m', $end), XCommonLocalization::ENGLISH) . date(' Y', $end) . $strTime;
				else
					return date('jS ', $start) . XCommonLocalization::getMonthName(date('m', $start), XCommonLocalization::ENGLISH) . date(' Y', $start) . $strTime;
				break;
		}

		if (!$start)
			$start = time();

		$strTime = '';
		if ($startTime)
			$strTime = ', ' . date('H:i', $startTime) . ' Uhr' . ($endTime && $startTime != $endTime ? ' &ndash; ' . date('H:i', $endTime) . ' Uhr' : '');

		if ($end)
			return date('d. ', $start) . XCommonLocalization::getMonthName(date('m', $start)) . date(' Y', $start) . ' &ndash; ' .
			date('d. ', $end) . XCommonLocalization::getMonthName(date('m', $end)) . date(' Y', $end) . $strTime;
		else
			return date('d. ', $start) . XCommonLocalization::getMonthName(date('m', $start)) . date(' Y', $start) . $strTime;
	}

	protected static function getDateTimeInterval($strFormat, $intStart = null, $intEnd = null, $strDelimiter = ' &ndash; ',
			$arrStartReplacements = array(), $arrEndReplacements = array())
	{
		$strStart = str_replace(array_keys($arrStartReplacements), array_values($arrStartReplacements),
				\Contao\Date::parse($strFormat, $intStart));

		$strEnd = str_replace(array_keys($arrEndReplacements), array_values($arrEndReplacements),
				\Contao\Date::parse($strFormat, $intEnd));

		if ($intEnd > 0 && $intEnd > $intStart && $strStart != $strEnd)
		{
			return $strStart . $strDelimiter . $strEnd;
		}
		else
		{
			return $strStart;
		}
	}

	public static function getNumericDateInterval($intStart = null, $intEnd = null, $strDelimiter = ' &ndash; ') {
		return static::getDateTimeInterval(\Contao\Date::getNumericDateFormat(), $intStart, $intEnd, $strDelimiter);
	}

	public static function getNumericTimeInterval($intStart = null, $intEnd = null, $strDelimiter = ' &ndash; ') {
		return static::getDateTimeInterval(\Contao\Date::getNumericTimeFormat(), $intStart, $intEnd, $strDelimiter);
	}

	public static function getNumericDateTimeInterval($intStart = null, $intEnd = null, $strDelimiter = ' &ndash; ')
	{
		return static::getDateTimeInterval(\Contao\Date::getNumericDatimFormat(), $intStart,
				$intEnd, $strDelimiter);
	}

	public static function getSeparatedNumericDateTimeInterval($intStartDate = null, $intEndDate = null,
			$intStartTime = null, $intEndTime = null, $strIntervalDelimiter = ' &ndash; ', $strDelimiter = ', ')
	{
		$strStartDate = \Contao\Date::parse(\Contao\Date::getNumericDateFormat(), $intStartDate);
		$strEndDate = \Contao\Date::parse(\Contao\Date::getNumericDateFormat(), $intEndDate);

		$strStartTime = \Contao\Date::parse(\Contao\Date::getNumericTimeFormat(), $intStartTime);
		$strEndTime = \Contao\Date::parse(\Contao\Date::getNumericTimeFormat(), $intEndTime);

		$strResult = $strStartDate;

		if ($intEndDate > 0 && $intEndDate > $intStartDate && $strStartDate != $strEndDate)
		{
			$strResult .= $strIntervalDelimiter . $strEndDate;
		}

		if ($intStartTime > 0)
		{
			if ($intEndTime > $intStartTime && $strStartTime != $strEndTime)
			{
				$strResult .= $strDelimiter . $strStartTime . $strIntervalDelimiter . $strEndTime;
			}
			else
			{
				$strResult .= $strDelimiter . $strStartTime;
			}
		}

		return $strResult;
	}

	public static function getFormattedDateTime($objEvent, $blnSeparatedDateTime = true)
	{
		if ($objEvent->addTime)
		{
			if ($blnSeparatedDateTime)
				$strDateTime = DateUtil::getSeparatedNumericDateTimeInterval($objEvent->startDate, $objEvent->endDate,
					$objEvent->startTime, $objEvent->endTime);
			else
				$strDateTime = DateUtil::getNumericDateInterval($objEvent->startTime, $objEvent->endTime);
		}
		else
			$strDateTime = DateUtil::getNumericDateInterval($objEvent->startDate, $objEvent->endDate);

		return $strDateTime;
	}

	// TODO
//	public static function getSymbolicDate($intStart = null, $intEnd = null, $strDelimiter = ' &ndash; ') {
//		$intStartMonth = date('n', $intStart) - 1;
//		$strStartMonth = isset($GLOBALS['TL_LANG'][$intStartMonth]) ? $GLOBALS['TL_LANG'][$intStartMonth] : '';
//
//		$arrStartReplacements = array(
//			'F' => $strStartMonth
//		);
//
//		$arrEndReplacements = array();
//
//		if ($intEnd > 0)
//		{
//			$intEndMonth = date('n', $intEnd) - 1;
//			$strEndMonth = isset($GLOBALS['TL_LANG'][$intEndMonth]) ? $GLOBALS['TL_LANG'][$intEndMonth] : '';
//
//			$arrEndReplacements['F'] = $strEndMonth;
//		}
//
//		return static::getDateTime(Date::getSymbolicDateFormat(), $intStart, $intEnd, $strDelimiter,
//				$arrStartReplacements, $arrEndReplacements);
//	}
//
//	public static function getSymbolicDateTime($intStart = null, $intEnd = null, $strDelimiter = ' &ndash; ') {
//		return static::getDateTime(\Contao\Date::getNumericDatimFormat(), $intStart, $intEnd, $strDelimiter);
//	}

}