<?php

namespace HeimrichHannot\Haste\Pdf;

class MPdfTemplate extends \mPDF
{
    protected $intTemplateId;
    protected $strTemplate;

    public function Header($content = '')
    {
        if ($this->strTemplate)
        {
            $this->SetImportUse();
            $intPageCount = $this->SetSourceFile($this->strTemplate);
            $this->intTemplateId = $this->ImportPage($intPageCount);
            $this->UseTemplate($this->intTemplateId);
        }

        parent::Header($content);
    }

    public function setHeaderTemplate($strTemplate)
    {
        $this->strTemplate = $strTemplate;
    }

}