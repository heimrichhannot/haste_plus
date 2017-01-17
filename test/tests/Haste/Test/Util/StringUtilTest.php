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
        return [
            [
                'youtube_template',
                'youtube',
                true
            ],
            [
                'youtube_template',
                'youTube',
                false
            ],
            [
                '3tubeplayer',
                '3tube',
                true
            ],
            [
                '3tubeplayer',
                '3',
                true
            ],
        ];
    }

    public function endsWithProvider()
    {
        // current request -> query to add -> expected result
        return [
            [
                'youtube_template',
                'template',
                true
            ],
            [
                'youtube_template',
                'Template',
                false
            ],
            [
                '3tubeplayer',
                'player',
                true
            ],
            [
                '3tubeplayer',
                'r',
                true
            ],
        ];
    }
}
