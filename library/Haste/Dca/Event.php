<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @package haste_plus
 * @author  Dennis Patzer
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Dca;


use HeimrichHannot\Haste\DateUtil;

class Event extends \Backend
{
	/**
	 * Returns the event title concatenated with its date/time
	 * @param $varEvent object|int The event id or the event object itself
	 */
	public static function getDateTimeFormattedEvent($varEvent, $blnSeparatedDateTime = true, $strFormat = '%s (%s)')
	{
		$objEvent = $varEvent;
		if (is_numeric($varEvent))
		{
			if (($objEvent = \CalendarEventsModel::findByPk($varEvent)) === null)
				return '';
		}

		$strDateTime = DateUtil::getFormattedDateTime($objEvent, $blnSeparatedDateTime);

		return sprintf($strFormat, $objEvent->title, $strDateTime);
	}
}