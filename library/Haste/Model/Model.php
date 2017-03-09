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


use HeimrichHannot\Haste\Dca\General;

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
        return General::setDefaultsFromDca($objModel->getTable(), $objModel);
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