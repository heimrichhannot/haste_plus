<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\Haste\Test\Util;


use HeimrichHannot\Haste\Util\Files;

class FilesTest extends \PHPUnit_Framework_TestCase
{

    public function testSanitizeFileName ()
    {
        $this->assertSame('icon-files.png', Files::sanitizeFileName('icon-files.png'));
        $this->assertSame('2017', Files::sanitizeFileName('2017'));
        $this->assertSame('id-2017', Files::sanitizeFileName('id-2017'));
        $this->assertSame('test_123.jpg', Files::sanitizeFileName('test_123.jpg'));
        $this->assertSame('abc', Files::sanitizeFileName('ABC'));
    }

}