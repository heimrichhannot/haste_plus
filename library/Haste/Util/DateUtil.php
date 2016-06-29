<?php

namespace HeimrichHannot\Haste;

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

	public static function getNumericDateTimeInterval($intStart = null, $intEnd = null, $strIntervalDelimiter = ' &ndash; ',
		$strDelimiter = ', ')
	{
		if (date(\Contao\Date::getNumericDateFormat(), $intStart) == date(\Contao\Date::getNumericDateFormat(), $intEnd))
		{
			return static::getNumericDateInterval($intStart, $intEnd, $strIntervalDelimiter) . $strDelimiter .
				static::getNumericTimeInterval($intStart, $intEnd, $strIntervalDelimiter);
		}
		else
		{
			return static::getDateTimeInterval(\Contao\Date::getNumericDatimFormat(), $intStart,
				$intEnd, $strIntervalDelimiter);
		}
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

	public static function getTimeElapsed($intDatime, $intCompareTo = null)
	{
		$objDatime = new \DateTime();
		$objDatime->setTimestamp($intDatime);

		if (!is_null($intCompareTo))
		{
			$objCompareTo = new \DateTime();
			$objCompareTo->setTimestamp($intCompareTo);
		}
		else
		{
			$objCompareTo = new \DateTime('now');
		}

		$intDiff = $objCompareTo->format('U') - $objDatime->format('U');
		$intDayDiff = floor($intDiff / 86400);

		if(is_nan($intDayDiff) || $intDayDiff < 0)
		{
			return '';
		}

		if($intDayDiff == 0)
		{
			if($intDiff < 60)
			{
				return $GLOBALS['TL_LANG']['MSC']['datediff']['just_now'];
			}
			elseif($intDiff < 120)
			{
				return $GLOBALS['TL_LANG']['MSC']['datediff']['min_ago'];
			}
			elseif($intDiff < 3600)
			{
				return sprintf($GLOBALS['TL_LANG']['MSC']['datediff']['nmins_ago'], floor($intDiff/60));
			}
			elseif($intDiff < 7200)
			{
				return $GLOBALS['TL_LANG']['MSC']['datediff']['hour_ago'];
			}
			elseif($intDiff < 86400)
			{
				return sprintf($GLOBALS['TL_LANG']['MSC']['datediff']['nhours_ago'], floor($intDiff/3600));
			}
		}
		elseif($intDayDiff == 1)
		{
			return $GLOBALS['TL_LANG']['MSC']['datediff']['yesterday'];
		}
		elseif($intDayDiff < 7)
		{
			return sprintf($GLOBALS['TL_LANG']['MSC']['datediff']['ndays_ago'], $intDayDiff);
		}
		elseif($intDayDiff == 7)
		{
			return $GLOBALS['TL_LANG']['MSC']['datediff']['week_ago'];
		}
		elseif($intDayDiff < (7*6))
		{ // Modifications Start Here
			// 6 weeks at most
			return sprintf($GLOBALS['TL_LANG']['MSC']['datediff']['nweeks_ago'], ceil($intDayDiff/7));
		}
		elseif($intDayDiff < 365)
		{
			return sprintf($GLOBALS['TL_LANG']['MSC']['datediff']['nmonths_ago'], ceil($intDayDiff/(365/12)));
		}
		else
		{
			$years = round($intDayDiff/365);
			return sprintf(($years > 1 ? $GLOBALS['TL_LANG']['MSC']['datediff']['years_ago'] : $GLOBALS['TL_LANG']['MSC']['datediff']['year_ago']), $years);
		}

		return '';
	}

	public static function getTimePeriodInSeconds($arrTimePeriod)
	{
		$arrTimePeriod = deserialize($arrTimePeriod, true);

		if (!isset($arrTimePeriod['unit']) || !isset($arrTimePeriod['value']))
			return null;

		$intFactor = 1;
		switch ($arrTimePeriod['unit'])
		{
			case 'm':
				$intFactor = 60;
				break;
			case 'h':
				$intFactor = 60*60;
				break;
			case 'd':
				$intFactor = 24*60*60;
				break;
		}

		return $arrTimePeriod['value'] * $intFactor;
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