<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$dc = &$GLOBALS['TL_DCA']['tl_settings'];

/**
 * Palettes
 */

$strPalette = '{haste_legend},headerAddXFrame,headerAllowOrigins,hpProxy;';

$dc['palettes']['default'] = str_replace('defaultChmod;', 'defaultChmod;' . $strPalette, $dc['palettes']['default']);

$arrFields = array
(
	'headerAddXFrame' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['headerAddXFrame'],
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class'=>'w50')
	),
	'headerAllowOrigins' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['headerAllowOrigins'],
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class'=>'w50')
	),
	'hpProxy' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['hpProxy'],
		'inputType'               => 'text',
		'eval'                    => array('tl_class'=>'w50')
	)
);

$dc['fields'] = array_merge($dc['fields'], $arrFields);