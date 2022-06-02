<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @package haste_plus
 * @author  Dennis Patzer
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Util;


class Salutations
{
    /**
     * Creates complete names by inserting an array of the person's data
     *
     * Supported field names: firstname, lastname, academicTitle, additionalTitle, gender
     *
     * If some of the fields shouldn't go into the processed name, just leave them out of $arrData
     *
     * @param array $arrData
     */
    public static function createNameByFields($strLanguage, array $arrData)
    {
        if ($strLanguage)
        {
            \Controller::loadLanguageFile('default', $strLanguage, true);
        }

        $strName = '';

        if ($arrData['firstname'])
        {
            $strName = $arrData['firstname'] . ($arrData['lastname'] ? ' ' . $arrData['lastname'] : '');
        }
        elseif ($arrData['lastname'])
        {
            $strName = $arrData['lastname'];
        }

        if ($strName && $arrData['academicTitle'])
        {
            $strName = $arrData['academicTitle'] . ' ' . $strName;
        }

        if ($strName && $arrData['additionalTitle'])
        {
            $strName = $arrData['additionalTitle'] . ' ' . $strName;
        }

        if ($arrData['lastname'] && $arrData['gender'] && ($strLanguage != 'en' || !$arrData['academicTitle']))
        {
            $strGender = $GLOBALS['TL_LANG']['MSC']['haste_plus']['gender' . ($arrData['gender'] == 'female' ? 'Female' : 'Male')];

            $strName = $strGender . ' ' . $strName;
        }

        if ($strLanguage)
        {
            \Controller::loadLanguageFile('default', $GLOBALS['TL_LANGUAGE'], true);
        }

        return $strName;
    }

    /**
     * @param $strLanguage
     * @param $varEntity object|array
     *
     * @return string
     */
    public static function createSalutation($strLanguage, $varEntity, $blnInformal = false, $blnInformalFirstname = false)
    {
        if (is_array($varEntity))
        {
            $varEntity = Arrays::arrayToObject($varEntity);
        }

        $blnHasFirstname = $varEntity->firstname ?? false;
        $blnHasLastname  = $varEntity->lastname ?? false;
        $blnHasTitle     = ($varEntity->title ?? null) && $varEntity->title != '-' && $varEntity->title != 'Titel' && $varEntity->title != 'Title';

        if (!$blnHasTitle)
        {
            $blnHasTitle = $varEntity->academicTitle && $varEntity->academicTitle != '-' && $varEntity->academicTitle != 'Titel' && $varEntity->academicTitle != 'Title';
        }

        if ($strLanguage)
        {
            \Controller::loadLanguageFile('default', $strLanguage, true);
        }

        switch ($strLanguage) {
            case 'en':
                if ($varEntity->gender === 'divers') {
                    if ($blnInformal) {
                        if ($blnHasFirstname && $blnInformalFirstname) {
                            $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutationDivers'] .' '. $varEntity->firstname;
                        } elseif ($blnHasFirstname && $blnHasLastname && !$blnInformalFirstname) {
                            $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutationDivers'] .' '. $varEntity->firstname . ' ' . $varEntity->lastname;
                        } else {
                            $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutationDivers'];
                        }
                    } else {
                        $suffix = $varEntity->firstname && $varEntity->lastname ? ' '. $varEntity->firstname . ' ' . $varEntity->lastname : '';
                        $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutationDivers'] . $suffix;
                    }
                } else {
                    if ($blnInformal) {
                        if ($blnHasFirstname && $blnInformalFirstname) {
                            $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutation'].' '.$varEntity->firstname;
                        } elseif ($blnHasLastname) {
                            $strSalutationPart = $GLOBALS['TL_LANG']['MSC']['haste_plus']['gender' . ('female' == $varEntity->gender ? 'Female' : 'Male')];

                            $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutation'].' '.$strSalutationPart.' '.$varEntity->lastname;
                        } else {
                            $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutation'];
                        }
                    } elseif ($blnHasLastname) {
                        if ($blnHasTitle) {
                            $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutation'].' '.($varEntity->title ?: $varEntity->academicTitle);
                        } else {
                            $strSalutation =
                                $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutation'.('female' == $varEntity->gender ? 'Female' : 'Male')];
                        }

                        $strSalutation = $strSalutation.' '.$varEntity->lastname;
                    } else {
                        $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutation'];
                    }
                }

                break;

            default:
                // de
                if ($varEntity->gender === 'divers') {
                    if ($blnInformal) {
                        if ($blnHasFirstname && $blnInformalFirstname) {
                            $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutationGenericInformal'] .' '. $varEntity->firstname;
                        } elseif ($blnHasFirstname && $blnHasLastname && !$blnInformalFirstname) {
                            $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutationGenericInformal'] .' '. $varEntity->firstname . ' ' . $varEntity->lastname;
                        } else {
                            $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutationGenericInformal'];
                        }
                    } else {
                        $suffix = $blnHasFirstname && $blnHasLastname ? ', '. $varEntity->firstname . ' ' . $varEntity->lastname : '';
                        $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutationDivers'] . $suffix;
                    }
                } else {
                    if ($blnInformal) {
                        if ($blnHasFirstname && $blnInformalFirstname) {
                            $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutationGenericInformal'].' '.$varEntity->firstname;
                        } elseif ($blnHasLastname && !$blnInformalFirstname) {
                            $strSalutationPart = $GLOBALS['TL_LANG']['MSC']['haste_plus']['gender' . ('female' == $varEntity->gender ? 'Female' : 'Male')];
                            $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutationGenericInformal'].' '.$strSalutationPart.' '.$varEntity->lastname;
                        } else {
                            $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutationGenericInformal'];
                        }
                    } elseif ($blnHasLastname && !$blnInformal) {
                        $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutation'.('female' == $varEntity->gender ? 'Female' : 'Male')];

                        if ($blnHasTitle) {
                            $strSalutation .= ' '.($varEntity->title ?: $varEntity->academicTitle);
                        }

                        $strSalutation = $strSalutation.' '.$varEntity->lastname;
                    } else {
                        $strSalutation = $GLOBALS['TL_LANG']['MSC']['haste_plus']['salutationGeneric'];
                    }
                }

                break;
        }

        if ($strLanguage)
        {
            \Controller::loadLanguageFile('default', $GLOBALS['TL_LANGUAGE'], true);
        }

        return $strSalutation;
    }
}
