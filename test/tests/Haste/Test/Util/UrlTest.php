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

include_once __DIR__ . '/../../../../../library/Haste/Util/Url.php';

use HeimrichHannot\Haste\Util\Url;

class UrlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider addSchemeProvider
     * @test
     */
    public function testAddScheme($strUrl, $strScheme, $expectedResult)
    {
        $this->assertSame($expectedResult, Url::addScheme($strUrl, $strScheme));
    }

    public function addSchemeProvider()
    {
        // current request -> query to add -> expected result
        return array(
            array(
                'domain.com',
                'http://',
                'http://domain.com'
            ),
            array(
                'http://domain.com',
                'http://',
                'http://domain.com'
            ),
            array(
                'domain.com',
                'https://',
                'https://domain.com'
            ),
            array(
                'https://domain.com',
                'http://',
                'https://domain.com'
            ),
            array(
                'http://domain.com',
                'https://',
                'http://domain.com'
            ),
            array(
                'domain.com',
                'ftp://',
                'ftp://domain.com'
            ),
            array(
                'test@test.de',
                'mailto:',
                'mailto:test@test.de'
            ),
            array(
                'mailto:test@test.de',
                'mailto:',
                'mailto:test@test.de'
            ),
        );
    }
}
