<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'HeimrichHannot',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'HeimrichHannot\HastePlus\Classes'                      => 'system/modules/haste_plus/classes/Classes.php',
	'HeimrichHannot\HastePlus\Environment'                  => 'system/modules/haste_plus/classes/Environment.php',
	'HeimrichHannot\HastePlus\Utilities'                    => 'system/modules/haste_plus/classes/Utilities.php',
	'HeimrichHannot\HastePlus\Arrays'                       => 'system/modules/haste_plus/classes/Arrays.php',
	'HeimrichHannot\HastePlus\Files'                        => 'system/modules/haste_plus/classes/Files.php',
	'HeimrichHannot\HastePlus\DOM'                          => 'system/modules/haste_plus/classes/DOM.php',
	'HeimrichHannot\HastePlus\Strings'                      => 'system/modules/haste_plus/classes/Strings.php',

	// Library
	'HeimrichHannot\Haste\Pdf\MPdfTemplate'                 => 'system/modules/haste_plus/library/Haste/Pdf/MPdfTemplate.php',
	'HeimrichHannot\Haste\Pdf\PdfTemplate'                  => 'system/modules/haste_plus/library/Haste/Pdf/PdfTemplate.php',
	'HeimrichHannot\Haste\Visualization\GoogleChartWrapper' => 'system/modules/haste_plus/library/Haste/Visualization/GoogleChartWrapper.php',
	'HeimrichHannot\Haste\Database\QueryHelper'             => 'system/modules/haste_plus/library/Haste/Database/QueryHelper.php',
	'HeimrichHannot\Haste\Security\CodeGenerator'           => 'system/modules/haste_plus/library/Haste/Security/CodeGenerator.php',
	'HeimrichHannot\Haste\Security\HttpResponse'            => 'system/modules/haste_plus/library/Haste/Security/HttpResponse.php',
	'HeimrichHannot\Haste\DC_Table'                         => 'system/modules/haste_plus/library/Haste/Drivers/DC_Table.php',
	'HeimrichHannot\Haste\Map\GoogleMap'                    => 'system/modules/haste_plus/library/Haste/Maps/GoogleMap.php',
	'HeimrichHannot\Haste\Map\GoogleMapOverlay'             => 'system/modules/haste_plus/library/Haste/Maps/GoogleMapOverlay.php',
	'HeimrichHannot\Haste\Map\GoogleMapMarker'              => 'system/modules/haste_plus/library/Haste/Maps/GoogleMapMarker.php',
	'HeimrichHannot\Haste\Backend\Automator'                => 'system/modules/haste_plus/library/Haste/Backend/Automator.php',
	'HeimrichHannot\Haste\Cache\Cache'                      => 'system/modules/haste_plus/library/Haste/Cache/Cache.php',
	'HeimrichHannot\Haste\Cache\FileCache'                  => 'system/modules/haste_plus/library/Haste/Cache/FileCache.php',
	'HeimrichHannot\Haste\Cache\RemoteImageCache'           => 'system/modules/haste_plus/library/Haste/Cache/RemoteImageCache.php',
	'HeimrichHannot\Haste\Util\Widget'                      => 'system/modules/haste_plus/library/Haste/Util/Widget.php',
	'HeimrichHannot\Haste\Util\Classes'                     => 'system/modules/haste_plus/library/Haste/Util/Classes.php',
	'HeimrichHannot\Haste\Util\Salutations'                 => 'system/modules/haste_plus/library/Haste/Util/Salutations.php',
	'HeimrichHannot\Haste\Util\Environment'                 => 'system/modules/haste_plus/library/Haste/Util/Environment.php',
	'HeimrichHannot\Haste\Util\Numbers'                     => 'system/modules/haste_plus/library/Haste/Util/Numbers.php',
	'HeimrichHannot\Haste\Util\FormSubmission'              => 'system/modules/haste_plus/library/Haste/Util/FormSubmission.php',
	'HeimrichHannot\Haste\DateUtil'                         => 'system/modules/haste_plus/library/Haste/Util/DateUtil.php',
	'HeimrichHannot\Haste\Util\Arrays'                      => 'system/modules/haste_plus/library/Haste/Util/Arrays.php',
	'HeimrichHannot\Haste\Util\Files'                       => 'system/modules/haste_plus/library/Haste/Util/Files.php',
	'HeimrichHannot\Haste\Util\Curl'                        => 'system/modules/haste_plus/library/Haste/Util/Curl.php',
	'HeimrichHannot\Haste\Util\DOMUtil'                     => 'system/modules/haste_plus/library/Haste/Util/DOMUtil.php',
	'HeimrichHannot\Haste\Util\Module'                      => 'system/modules/haste_plus/library/Haste/Util/Module.php',
	'HeimrichHannot\Haste\Util\Validator'                   => 'system/modules/haste_plus/library/Haste/Util/Validator.php',
	'HeimrichHannot\Haste\Util\StringUtil'                  => 'system/modules/haste_plus/library/Haste/Util/StringUtil.php',
	'HeimrichHannot\Haste\Util\Url'                         => 'system/modules/haste_plus/library/Haste/Util/Url.php',
	'HeimrichHannot\Haste\Model\Model'                      => 'system/modules/haste_plus/library/Haste/Model/Model.php',
	'HeimrichHannot\Haste\Model\MemberModel'                => 'system/modules/haste_plus/library/Haste/Model/MemberModel.php',
	'HeimrichHannot\Haste\Model\UserModel'                  => 'system/modules/haste_plus/library/Haste/Model/UserModel.php',
	'HeimrichHannot\Haste\Dca\DC_HastePlus'                 => 'system/modules/haste_plus/library/Haste/Dca/DC_HastePlus.php',
	'HeimrichHannot\Haste\Dca\General'                      => 'system/modules/haste_plus/library/Haste/Dca/General.php',
	'HeimrichHannot\Haste\Dca\Event'                        => 'system/modules/haste_plus/library/Haste/Dca/Event.php',
	'HeimrichHannot\Haste\Dca\Notification'                 => 'system/modules/haste_plus/library/Haste/Dca/Notification.php',
	'HeimrichHannot\Haste\Dca\Member'                       => 'system/modules/haste_plus/library/Haste/Dca/Member.php',
	'HeimrichHannot\Haste\Dca\User'                         => 'system/modules/haste_plus/library/Haste/Dca/User.php',
	'HeimrichHannot\Haste\Image\Image'                      => 'system/modules/haste_plus/library/Haste/Image/Image.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'dlh_infowindow'       => 'system/modules/haste_plus/templates/maps/frontend',
	'dlh_googlemaps_haste' => 'system/modules/haste_plus/templates/maps/frontend',
	'google_chart'         => 'system/modules/haste_plus/templates/maps/charts',
	'google_chart_column'  => 'system/modules/haste_plus/templates/maps/charts',
	'dlh_marker'           => 'system/modules/haste_plus/templates/maps/elements',
	'dlh_polygon'          => 'system/modules/haste_plus/templates/maps/elements',
	'dlh_kml_geoxml'       => 'system/modules/haste_plus/templates/maps/elements',
	'dlh_kml'              => 'system/modules/haste_plus/templates/maps/elements',
));
