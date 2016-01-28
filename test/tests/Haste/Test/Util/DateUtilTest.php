<?php

/**
 * Haste utilities for Contao Open Source CMS
 *
 * Copyright (C) 2012-2013 Codefog & terminal42 gmbh
 *
 * @package    Haste
 * @link       http://github.com/codefog/contao-haste/
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Test\Util;

include_once __DIR__ . '/../../../../../library/Haste/Util/DateUtil.php';

use HeimrichHannot\Haste\DateUtil;

class DateUtilTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		// config
		\Config::set('dateFormat', 'd.m.Y');
		\Config::set('timeFormat', 'H:i');
		\Config::set('datimFormat', 'd.m.Y H:i');

		// page
		global $objPage;
		$objPage = new \stdClass();
		$objPage->dateFormat = 'd.m.Y';
		$objPage->timeFormat = 'H:i';
		$objPage->datimFormat = 'd.m.Y H:i';
	}
	/**
	 * @dataProvider getNumericDateIntervalProvider
	 * @test
	 */
	public function testGetNumericDateInterval(
		$intStart = null, $intEnd = null, $strDelimiter = ' &ndash; ', $varExpectedResult
	) {
		$this->assertSame($varExpectedResult, DateUtil::getNumericDateInterval($intStart, $intEnd, $strDelimiter));
	}

	/**
	 * @dataProvider getNumericTimeIntervalProvider
	 * @test
	 */
	public function testGetNumericTimeInterval($intStart = null, $intEnd = null, $strDelimiter = ' &ndash; ', $varExpectedResult)
	{
		$this->assertSame($varExpectedResult, DateUtil::getNumericTimeInterval($intStart, $intEnd, $strDelimiter));
	}

	/**
	 * @dataProvider getNumericDateTimeIntervalProvider
	 * @test
	 */
	public function testGetNumericDateTimeInterval($intStart = null, $intEnd = null, $strDelimiter = ' &ndash; ', $varExpectedResult)
	{
		$this->assertSame($varExpectedResult, DateUtil::getNumericDateTimeInterval($intStart, $intEnd, $strDelimiter));
	}

	/**
	 * @dataProvider getSeparatedNumericDateTimeIntervalProvider
	 * @test
	 */
	public function testGetSeparatedNumericDateTimeInterval($intStartDate = null, $intEndDate = null,
			$intStartTime = null, $intEndTime = null, $strIntervalDelimiter = ' &ndash; ', $strDelimiter = ' , ',
			$varExpectedResult)
	{
		$this->assertSame($varExpectedResult, DateUtil::getSeparatedNumericDateTimeInterval($intStartDate, $intEndDate,
				$intStartTime, $intEndTime, $strIntervalDelimiter, $strDelimiter));
	}

	public function getNumericDateIntervalProvider()
	{
		return array(
			array(
				strtotime('28.01.2016 15:09:51'),
				null,
				' &ndash; ',
				'28.01.2016'
			),
			array(
				strtotime('28.01.2016 15:09:51'),
				strtotime('29.01.2016 17:09:51'),
				' &ndash; ',
				'28.01.2016 &ndash; 29.01.2016'
			),
			array(
				strtotime('28.01.2016 15:09:51'),
				strtotime('28.01.2016 15:09:51'),
				' &ndash; ',
				'28.01.2016'
			),
			array(
				strtotime('28.01.2016 15:09:51'),
				strtotime('27.01.2016 17:09:51'),
				' &ndash; ',
				'28.01.2016'
			)
		);
	}

	public function getNumericTimeIntervalProvider()
	{
		return array(
			array(
				strtotime('28.01.2016 15:09:51'),
				null,
				' &ndash; ',
				'15:09'
			),
			array(
				strtotime('28.01.2016 15:09:51'),
				strtotime('29.01.2016 17:09:51'),
				' &ndash; ',
				'15:09 &ndash; 17:09'
			),
			array(
				strtotime('28.01.2016 15:09:51'),
				strtotime('28.01.2016 17:09:51'),
				' &ndash; ',
				'15:09 &ndash; 17:09'
			),
			array(
				strtotime('28.01.2016 15:09:51'),
				strtotime('28.01.2016 15:09:51'),
				' &ndash; ',
				'15:09'
			),
			array(
				strtotime('28.01.2016 15:09:51'),
				strtotime('27.01.2016 17:09:51'),
				' &ndash; ',
				'15:09'
			)
		);
	}

	public function getNumericDateTimeIntervalProvider()
	{
		return array(
			array(
				strtotime('28.01.2016 15:09:51'),
				null,
				' &ndash; ',
				'28.01.2016 15:09'
			),
			array(
				strtotime('28.01.2016 15:09:51'),
				strtotime('29.01.2016 17:09:51'),
				' &ndash; ',
				'28.01.2016 15:09 &ndash; 29.01.2016 17:09'
			),
			array(
				strtotime('28.01.2016 15:09:51'),
				strtotime('28.01.2016 15:09:51'),
				' &ndash; ',
				'28.01.2016 15:09'
			),
			array(
				strtotime('28.01.2016 15:09:51'),
				strtotime('27.01.2016 17:09:51'),
				' &ndash; ',
				'28.01.2016 15:09'
			),
			array(
				strtotime('28.01.2016 15:09:51'),
				strtotime('28.01.2016 17:09:51'),
				' &ndash; ',
				'28.01.2016 15:09 &ndash; 28.01.2016 17:09'
			)
		);
	}

	public function getSeparatedNumericDateTimeIntervalProvider()
	{
		return array(
			array(
				strtotime('28.01.2016 15:09:51'),
				null,
				null,
				null,
				' &ndash; ',
				', ',
				'28.01.2016'
			),
			array(
				strtotime('28.01.2016 15:09:51'),
				strtotime('29.01.2016 17:09:51'),
				strtotime('28.01.2016 15:09:51'),
				strtotime('29.01.2016 17:09:51'),
				' &ndash; ',
				', ',
				'28.01.2016 &ndash; 29.01.2016, 15:09 &ndash; 17:09'
			),
			array(
				strtotime('28.01.2016 15:09:51'),
				strtotime('28.01.2016 17:09:51'),
				strtotime('28.01.2016 15:09:51'),
				strtotime('28.01.2016 17:09:51'),
				' &ndash; ',
				', ',
				'28.01.2016, 15:09 &ndash; 17:09'
			),
			array(
				strtotime('28.01.2016 15:09:51'),
				strtotime('28.01.2016 15:09:51'),
				strtotime('28.01.2016 15:09:51'),
				strtotime('28.01.2016 15:09:51'),
				' &ndash; ',
				', ',
				'28.01.2016, 15:09'
			)
		);
	}

}
