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
	 * Add a model to a collection
	 *
	 * @param \Model                 $objModel
	 * @param \Model\Collection|null $objCollection
	 *
	 * @return \Model\Collection|null
	 */
	public static function addModelToCollection(\Model $objModel, \Model\Collection $objCollection = null)
	{
		$arrRegistered = array();

		if ($objCollection !== null)
		{
			while ($objCollection->next())
			{
				if($objCollection->getTable() !== $objModel->getTable)
				{
					return null;
				}

				$intId                 = $objCollection->{$objModel::getPk()};
				$arrRegistered[$intId] = $objCollection->current();
			}
		}

		$arrRegistered[$objModel->{$objModel::getPk()}] = $objModel;

		return static::createCollectionFromDbResult(array_filter(array_values($arrRegistered)), $objModel->getTable());
	}
}