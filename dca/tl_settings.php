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

$strPalette = '{haste_legend},headerAddXFrame,headerXFrameSkipPages,headerAllowOrigins,hpProxy;';

$dc['palettes']['default'] = str_replace('defaultChmod;', 'defaultChmod;' . $strPalette, $dc['palettes']['default']);

$arrFields = [
    'headerAddXFrame' => [
		'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['headerAddXFrame'],
		'inputType'               => 'checkbox',
		'eval'                    => ['tl_class' =>'w50']],
    'headerXFrameSkipPages' => [
        'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['headerXFrameSkipPages'],
        'inputType'               => 'pageTree',
        'eval'                    => ['tl_class' =>'clr']],
    'headerAllowOrigins' => [
		'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['headerAllowOrigins'],
		'inputType'               => 'checkbox',
		'eval'                    => ['tl_class' =>'w50']],
    'hpProxy' => [
		'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['hpProxy'],
		'inputType'               => 'text',
		'eval'                    => ['tl_class' =>'w50']
    ]];

$dc['fields'] = array_merge($dc['fields'], $arrFields);