<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Model;


class Model extends \Contao\Model
{

    /**
     * Set the entity defaults from dca config (for new model entry)
     *
     * @param \Model $objModel
     *
     * @return \Model The modified model, containing the default values from all dca fields
     */
    public static function setDefaultsFromDca(\Model $objModel)
    {
        $strTable = $objModel->getTable();

        \Controller::loadDataContainer($strTable);

        // Get all default values for the new entry
        foreach ($GLOBALS['TL_DCA'][$strTable]['fields'] as $k => $v)
        {
            // Use array_key_exists here (see #5252)
            if (array_key_exists('default', $v))
            {
                $objModel->{$k} = is_array($v['default']) ? serialize($v['default']) : $v['default'];

                // Encrypt the default value (see #3740)
                if ($GLOBALS['TL_DCA'][$strTable]['fields'][$k]['eval']['encrypt'])
                {
                    $objModel->{$k} = \Encryption::encrypt($objModel->{$k});
                }
            }
        }

        return $objModel;
    }


    /**
     * Remove a model from a collection
     *
     * @param \Model                 $objModel
     * @param \Model\Collection|null $objCollection
     *
     * @return \Model\Collection|null
     */
    public static function removeModelFromCollection(\Model $objModel, \Model\Collection $objCollection = null)
    {
        $arrRegistered = [];

        if ($objCollection !== null)
        {
            while ($objCollection->next())
            {
                if ($objCollection->getTable() !== $objModel->getTable)
                {
                    return $objCollection;
                }

                $intId = $objCollection->{$objModel::getPk()};

                if ($objModel->{$objModel::getPk()} == $intId)
                {
                    continue;
                }

                $arrRegistered[$intId] = $objCollection->current();
            }
        }

        return static::createCollection(array_filter(array_values($arrRegistered)), $objModel->getTable());
    }

    /**
     * Add a model to a collection
     *
     * @param \Model                 $objModel
     * @param \Model\Collection|null $objCollection
     *
     * @return \Model\Collection|null
     */
    public static function addModelToCollection(\Model $objModel, \Model\Collection $objCollection = null)
    {
        $arrRegistered = [];

        if ($objCollection !== null)
        {
            while ($objCollection->next())
            {
                if ($objCollection->getTable() !== $objModel->getTable)
                {
                    return $objCollection;
                }

                $intId                 = $objCollection->{$objModel::getPk()};
                $arrRegistered[$intId] = $objCollection->current();
            }
        }

        $arrRegistered[$objModel->{$objModel::getPk()}] = $objModel;

        return static::createCollection(array_filter(array_values($arrRegistered)), $objModel->getTable());
    }
}