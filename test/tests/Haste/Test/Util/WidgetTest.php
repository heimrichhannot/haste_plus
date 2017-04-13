<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Test\Util;

include_once __DIR__ . '/../../../../../library/Haste/Util/Widget.php';

use HeimrichHannot\Haste\Util\Widget;

class WidgetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test maxlength rgxp for given html input
     *
     * @test
     */
    public function testMaxLengthRgxpForHtmlSuccessWithEmptyTags()
    {
        $strRegexp = 'maxlength::113';

        // 113 characters without html [nbsp] at the end is non-breaking space
        $varValue =
            '<h1>HTML Ipsum Presents</h1><p>Pellentesque habitant morbi tristique senectus et netus et malesuada[nbsp]fames ac turpis egestas.</p><p></p><br />[nbsp] ';

        $arrData = [
            'inputType' => 'textarea',
            'eval'      => ['rte' => 'tinyMCE'],
        ];

        $objWidget = new \TextArea(\Widget::getAttributesFromDca($arrData, 'teaser', $varValue, 'teaser', 'tl_news'));

        $objUtil = new Widget();
        $objUtil->addCustomRegexp($strRegexp, $varValue, $objWidget);

        $this->assertFalse($objWidget->hasErrors());
    }

    /**
     * Test maxlength rgxp for given html input
     *
     * @test
     */
    public function testMaxLengthRgxpForHtmlSuccess()
    {
        $strRegexp = 'maxlength::112';

        // 112 characters without html
        $varValue =
            '<h1>HTML Ipsum Presents</h1><p>Pellentesque habitant morbi tristique senectus et netus et malesuada[nbsp]fames ac turpis egestas.</p>';

        $arrData = [
            'inputType' => 'textarea',
            'eval'      => ['rte' => 'tinyMCE'],
        ];

        $objWidget = new \TextArea(\Widget::getAttributesFromDca($arrData, 'teaser', $varValue, 'teaser', 'tl_news'));

        $objUtil = new Widget();
        $objUtil->addCustomRegexp($strRegexp, $varValue, $objWidget);

        $this->assertFalse($objWidget->hasErrors());
    }


    /**
     * Test maxlength rgxp for given html input
     *
     * @test
     */
    public function testMaxLengthRgxpForHtmlWithEmptyInput()
    {
        $strRegexp = 'maxlength::120';

        $varValue =
            '';

        $arrData = [
            'inputType' => 'textarea',
            'eval'      => ['rte' => 'tinyMCE'],
        ];

        $objWidget = new \TextArea(\Widget::getAttributesFromDca($arrData, 'teaser', $varValue, 'teaser', 'tl_news'));

        $objUtil = new Widget();
        $objUtil->addCustomRegexp($strRegexp, $varValue, $objWidget);

        $this->assertFalse($objWidget->hasErrors());
    }

    /**
     * Test maxlength rgxp for given html input
     *
     * @test
     */
    public function testMaxLengthRgxpForHtmlExceeded()
    {
        $strRegexp = 'maxlength::120';

        $varValue =
            '<h1>HTML Ipsum Presents</h1><p>Pellentesque habitant morbi tristique senectus et netus et malesuada[nbsp]fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante.</p>';

        $arrData = [
            'inputType' => 'textarea',
            'eval'      => ['rte' => 'tinyMCE'],
        ];

        $objWidget = new \TextArea(\Widget::getAttributesFromDca($arrData, 'teaser', $varValue, 'teaser', 'tl_news'));

        $objUtil = new Widget();
        $objUtil->addCustomRegexp($strRegexp, $varValue, $objWidget);

        if($objWidget->hasErrors())
        {
            $arrErrors = $objWidget->getErrors();

            $this->assertSame('Das Feld "teaser" darf höchstens 120 Zeichen lang sein!', $arrErrors[0]);
        }
    }
}