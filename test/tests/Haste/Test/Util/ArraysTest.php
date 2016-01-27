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

include_once __DIR__ . '/../../../../../library/Haste/Util/Arrays.php';

use HeimrichHannot\Haste\Util\Arrays;

class ArraysTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider filterByPrefixesProvider
	 * @test
	 */
	public function testFilterByPrefixes($arrData, $arrPrefixes, $expectedResult)
	{
		$this->assertSame($expectedResult, Arrays::filterByPrefixes($arrData, $arrPrefixes));
	}


	public function filterByPrefixesProvider()
	{
		$arrData = array
		(
			'id'                     => 1,
			'youtube_template'       => 'youtube_default',
			'youtubePrivacy'         => true,
			'youtubePrivacyTemplate' => 'youtubeprivacy_default',
			'youTube'                => 'Fu',
			''                       => 'Bar',
		);

		// current request -> query to add -> expected result
		return array(
			array(
				$arrData,
				array('youtube'),
				array
				(
					'youtube_template'       => 'youtube_default',
					'youtubePrivacy'         => true,
					'youtubePrivacyTemplate' => 'youtubeprivacy_default',
				),
			),
			array(
				$arrData,
				array('youTube'),
				array
				(
					'youTube' => 'Fu',
				),
			),
		);
	}
}
