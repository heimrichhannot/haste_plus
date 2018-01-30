<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Dennis Patzer
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Pdf;


use HeimrichHannot\Haste\Util\Files;

class PdfTemplate extends \Controller
{
    const ORIENTATION_PORTRAIT  = 'P';
    const ORIENTATION_LANDSCAPE = 'L';

    /**
     * @var \mPDF
     */
    protected $objPdf;

    public function __construct(
        $strFormat = 'A4',
        $strOrientation = PdfTemplate::ORIENTATION_PORTRAIT,
        $intMarginLeft = 15,
        $intMarginRight = 15,
        $intMarginTop = 16,
        $intMarginBottom = 16,
        $intMarginHeader = 9,
        $intMarginFooter = 9
    ) {
        if (!class_exists('mPDF'))
        {
            throw new \Exception('Couldn\'t find mPDF. Please install it via "composer require mpdf/mpdf:6.1.*" since it\'s necessary for HeimrichHannot\Haste\Pdf\PdfTemplate.');
        }

        $this->objPdf = new MPdfTemplate(
            '', $strFormat, 0, '', $intMarginLeft, $intMarginRight, $intMarginTop, $intMarginBottom, $intMarginHeader, $intMarginFooter, $strOrientation
        );
    }

    /**
     * Makes the pdf use some other pdf as a template
     *
     * @param $strFilename string The filename including the path without TL_ROOT
     *
     * @throws \MpdfException
     */
    public function addTemplatePdf($strFilename)
    {
        $this->objPdf->setHeaderTemplate(TL_ROOT . '/' . ltrim($strFilename, '/'));
    }

    /**
     * Writes Html-Code into the pdf at the current position
     *
     * @param $strHtml
     *
     * @throws \MpdfException
     */
    public function writeHtml($strHtml, $strCss = '')
    {
        $strHtml = ($strCss ? '<style>' . $strCss . '</style>' : '') . $strHtml;

        $this->objPdf->WriteHTML(html_entity_decode($this->replaceInsertTags($strHtml)));
    }

    /**
     * Sends the pdf to the browser for download.
     *
     * @param string $strFilename The filename
     *
     * @throws \MpdfException
     */
    public function sendToBrowser($strFilename = '', $strDest = 'I')
    {
        $strFilename = $strFilename ?: $this->buildFilename();

        $this->objPdf->Output($strFilename, $strDest);
    }

    /**
     * Saves the pdf to file
     *
     * @param        $strFolder   string The folder as a path without TL_ROOT
     * @param string $strFilename The filename without the path
     *
     * @throws \MpdfException
     */
    public function saveToFile($strFolder, $strFilename = '', $blnSkipDb = false)
    {
        $strFilename = $strFilename ?: $this->buildFilename();

        // create if not exists
        new \Folder($strFolder);

        $this->objPdf->Output(TL_ROOT . '/' . trim($strFolder, '/') . '/' . $strFilename, 'F');

        // save database entity
        if (!$blnSkipDb)
        {
            $objFile = new \File(trim($strFolder, '/') . '/' . $strFilename);
            $objFile->close();
        }
    }

    /**
     * Adds the regular version of a font to the pdf
     *
     * @param $strFontName string The font name (remember to refer to it in the lower case non-whitespace version in your css, i.e. "Open Sans" -> "opensans")
     * @param $strFileName string The filename including path relative to the contao root (e.g. "files/pdfs/mypdf.pdf")
     */
    public function addRegularFont($strFontName, $strFileName)
    {
        $this->addFont($strFontName, $strFileName, 'R');
    }

    /**
     * Adds the italic version of a font to the pdf
     *
     * @param $strFontName string The filename including path relative to the contao root (e.g. "files/pdfs/mypdf.pdf")
     */
    public function addItalicFont($strFontName, $strFileName)
    {
        $this->addFont($strFontName, $strFileName, 'I');
    }

    /**
     * Adds the bold version of a font to the pdf
     *
     * @param $strFontName string The filename including path relative to the contao root (e.g. "files/pdfs/mypdf.pdf")
     */
    public function addBoldFont($strFontName, $strFileName)
    {
        $this->addFont($strFontName, $strFileName, 'B');
    }

    /**
     * Adds the bold/italic version of a font to the pdf
     *
     * @param $strFontName string The filename including path relative to the contao root (e.g. "files/pdfs/mypdf.pdf")
     */
    public function addBoldItalicFont($strFontName, $strFileName)
    {
        $this->addFont($strFontName, $strFileName, 'BI');
    }

    private function addFont($strFontName, $strFileName, $strType)
    {
        $strFontName = str_replace(' ', '', strtolower($strFontName));
        $strFileName = '../../../../' . (version_compare(VERSION, '4.0', '<') ? '../' : '')
                       . ltrim($strFileName, '/');

        if (!is_array($this->objPdf->fontdata[$strFontName]))
        {
            $this->objPdf->fontdata[$strFontName] = [];
        }

        // add to fontdata array
        $this->objPdf->fontdata[$strFontName][$strType] = $strFileName;

        // add to available fonts array
        switch ($strType)
        {
            case 'R':
                $this->objPdf->available_unifonts[] = $strFontName;
                break;
            case 'B':
                $this->objPdf->available_unifonts[] = $strFontName . 'B';
                break;
            case 'I':
                $this->objPdf->available_unifonts[] = $strFontName . 'I';
                break;
            case 'BI':
                $this->objPdf->available_unifonts[] = $strFontName . 'BI';
                break;
        }

        $this->objPdf->default_available_fonts = $this->objPdf->available_unifonts;
    }

    public function setTitle($strTitle)
    {
        $this->objPdf->SetTitle($strTitle);
    }

    public function setAuthor($strAuthor)
    {
        $this->objPdf->SetAuthor($strAuthor);
    }

    public function setCreator($strCreator)
    {
        $this->objPdf->SetCreator($strCreator);
    }

    public function setSubject($strSubject)
    {
        $this->objPdf->SetSubject($strSubject);
    }

    /**
     * Creates a timestamped filename
     *
     * @return string
     */
    private function buildFilename()
    {
        return 'pdf-' . date('Y-m-d_H-i', time()) . '.pdf';
    }

    public function getMPdf()
    {
        return $this->objPdf;
    }

    public function __get($strName)
    {
        return $this->{$strName};
    }


}
