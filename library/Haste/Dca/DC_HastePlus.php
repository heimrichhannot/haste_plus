<?php

namespace HeimrichHannot\Haste\Dca;


use Contao\Controller;

class DC_HastePlus extends \DC_Table
{
    /**
     * Initialize the object
     *
     * @param string $strTable
     * @param array  $arrModule
     */
    public function __construct($strTable, $arrModule=array())
    {
        \DataContainer::__construct();

        // Check the request token (see #4007)
        if (isset($_GET['act']))
        {
            if (!isset($_GET['rt']) || !\RequestToken::validate(\Input::get('rt')))
            {
                $this->Session->set('INVALID_TOKEN_URL', \Environment::get('request'));
                $this->redirect('contao/confirm.php');
            }
        }

        $this->intId = \Input::get('id');

        // Clear the clipboard
        if (isset($_GET['clipboard']))
        {
            $this->Session->set('CLIPBOARD', array());
            $this->redirect($this->getReferer());
        }

        Controller::loadDataContainer($strTable);

        // Check whether the table is defined
        if ($strTable == '' || !isset($GLOBALS['TL_DCA'][$strTable]))
        {
            $this->log('Could not load the data container configuration for "' . $strTable . '"', __METHOD__, TL_ERROR);
            trigger_error('Could not load the data container configuration', E_USER_ERROR);
        }

        // Set IDs and redirect
        if (\Input::post('FORM_SUBMIT') == 'tl_select')
        {
            $ids = \Input::post('IDS');

            if (empty($ids) || !is_array($ids))
            {
                $this->reload();
            }

            $session = $this->Session->getData();
            $session['CURRENT']['IDS'] = $ids;
            $this->Session->setData($session);

            if (isset($_POST['edit']))
            {
                $this->redirect(str_replace('act=select', 'act=editAll', \Environment::get('request')));
            }
            elseif (isset($_POST['delete']))
            {
                $this->redirect(str_replace('act=select', 'act=deleteAll', \Environment::get('request')));
            }
            elseif (isset($_POST['override']))
            {
                $this->redirect(str_replace('act=select', 'act=overrideAll', \Environment::get('request')));
            }
            elseif (isset($_POST['cut']) || isset($_POST['copy']))
            {
                $arrClipboard = $this->Session->get('CLIPBOARD');

                $arrClipboard[$strTable] = array
                (
                    'id' => $ids,
                    'mode' => (isset($_POST['cut']) ? 'cutAll' : 'copyAll')
                );

                $this->Session->set('CLIPBOARD', $arrClipboard);

                // Support copyAll in the list view (see #7499)
                if (isset($_POST['copy']) && $GLOBALS['TL_DCA'][$strTable]['list']['sorting']['mode'] < 4)
                {
                    $this->redirect(str_replace('act=select', 'act=copyAll', \Environment::get('request')));
                }

                $this->redirect($this->getReferer());
            }
        }

        $this->strTable = $strTable;
        $this->ptable = $GLOBALS['TL_DCA'][$this->strTable]['config']['ptable'] ?? null;
        $this->ctable = $GLOBALS['TL_DCA'][$this->strTable]['config']['ctable'] ?? null;
        $this->treeView = in_array($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['mode'], array(5, 6));
        $this->root = null;
        $this->arrModule = $arrModule;

        // FIX: don't call onload_callbacks

        // Get the IDs of all root records (tree view)
        if ($this->treeView)
        {
            $table = ($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['mode'] == 6) ? $this->ptable : $this->strTable;

            // Unless there are any root records specified, use all records with parent ID 0
            if (!isset($GLOBALS['TL_DCA'][$table]['list']['sorting']['root']) || $GLOBALS['TL_DCA'][$table]['list']['sorting']['root'] === false)
            {
                $objIds = $this->Database->prepare("SELECT id FROM " . $table . " WHERE pid=?" . ($this->Database->fieldExists('sorting', $table) ? ' ORDER BY sorting' : ''))
                    ->execute(0);

                if ($objIds->numRows > 0)
                {
                    $this->root = $objIds->fetchEach('id');
                }
            }

            // Get root records from global configuration file
            elseif (is_array($GLOBALS['TL_DCA'][$table]['list']['sorting']['root']))
            {
                $this->root = $this->eliminateNestedPages($GLOBALS['TL_DCA'][$table]['list']['sorting']['root'], $table, $this->Database->fieldExists('sorting', $table));
            }
        }

        // Get the IDs of all root records (list view or parent view)
        elseif (isset($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['root']) && is_array($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['root']))
        {
            $this->root = array_unique($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['root']);
        }

        // Store the current referer
        if (!empty($this->ctable) && !\Input::get('act') && !\Input::get('key') && !\Input::get('token') && TL_SCRIPT == 'contao/main.php' && !\Environment::get('isAjaxRequest'))
        {
            $session = $this->Session->get('referer');
            $session[TL_REFERER_ID][$this->strTable] = substr(\Environment::get('requestUri'), strlen(TL_PATH) + 1);
            $this->Session->set('referer', $session);
        }
    }
}
