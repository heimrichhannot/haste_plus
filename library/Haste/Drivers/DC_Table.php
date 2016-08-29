<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste;


class DC_Table extends \DataContainer
{
	public function getPalette()
	{
		// TODO: Implement getPalette() method.
	}

	protected function save($varValue)
	{
		// TODO: Implement save() method.
	}

	/**
	 * Create a \DataContainer instance from a given Model
	 * @param \Model $objModel
	 *
	 * @return static
	 */
	public static function getInstanceFromModel(\Model $objModel)
	{
		$objInstance = new static();
		$objInstance->strTable = $objModel->getTable();
		$objInstance->activeRecord = $objModel;
		$objInstance->intId = $objModel->id;
		return $objInstance;
	}
}