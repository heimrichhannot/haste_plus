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

$strPalette = '{haste_legend},loadGoogleMapsAssetsOnDemand,headerAddXFrame,headerXFrameSkipPages,headerAllowOrigins,hpProxy;';

$dc['palettes']['default'] = str_replace('{chmod_legend', $strPalette . '{chmod_legend', $dc['palettes']['default']);

$arrFields = [
    'loadGoogleMapsAssetsOnDemand' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_settings']['loadGoogleMapsAssetsOnDemand'],
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
    ],
    'headerAddXFrame'       => [
        'label'     => &$GLOBALS['TL_LANG']['tl_settings']['headerAddXFrame'],
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
    ],
    'headerXFrameSkipPages' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_settings']['headerXFrameSkipPages'],
        'inputType' => 'pageTree',
        'eval'      => ['tl_class' => 'clr', 'fieldType' => 'checkbox', 'multiple' => true],
    ],
    'headerAllowOrigins'    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_settings']['headerAllowOrigins'],
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
    ],
    'hpProxy'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_settings']['hpProxy'],
        'inputType' => 'text',
        'eval'      => ['tl_class' => 'w50'],
    ],
];

$dc['fields'] = array_merge($dc['fields'], $arrFields);