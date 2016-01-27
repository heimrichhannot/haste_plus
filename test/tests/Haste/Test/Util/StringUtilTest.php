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

include_once __DIR__ . '/../../../../../library/Haste/Util/StringUtil.php';

use HeimrichHannot\Haste\Util\StringUtil;

class StringUtilTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider startsWithProvider
     * @test
     */
    public function testStartsWith($haystack, $needle, $expectedResult)
    {
        $this->assertSame($expectedResult, StringUtil::startsWith($haystack, $needle));
    }

    /**
     * @dataProvider endsWithProvider
     * @test
     */
    public function testEndsWith($haystack, $needle, $expectedResult)
    {
        $this->assertSame($expectedResult, StringUtil::endsWith($haystack, $needle));
    }

    public function startsWithProvider()
    {
        // current request -> query to add -> expected result
        return array(
            array(
                'youtube_template',
                'youtube',
                true
            ),
            array(
                'youtube_template',
                'youTube',
                false
            ),
            array(
                '3tubeplayer',
                '3tube',
                true
            ),
            array(
                '3tubeplayer',
                '3',
                true
            ),
        );
    }

    public function endsWithProvider()
    {
        // current request -> query to add -> expected result
        return array(
            array(
                'youtube_template',
                'template',
                true
            ),
            array(
                'youtube_template',
                'Template',
                false
            ),
            array(
                '3tubeplayer',
                'player',
                true
            ),
            array(
                '3tubeplayer',
                'r',
                true
            ),
        );
    }
}
