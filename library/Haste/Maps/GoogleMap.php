<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @package haste_plus
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Map;

use HeimrichHannot\Haste\Util\Url;
use HeimrichHannot\Haste\Visualization\GoogleChartWrapper;

class GoogleMap
{
	/**
	 * Current object instance (do not remove)
	 *
	 * @var object
	 */
	protected $arrOptions = [];

	protected $arrOverlays = [];

	public function __construct()
	{
		$this->prepare();
	}

	public function setCenter($strCoordinates)
	{
		$this->center = $strCoordinates;

		return $this;
	}

	public function setScrollWheel($blnScrollWheel)
	{
		$this->scrollWheel = $blnScrollWheel;

		return $this;
	}

	public function setInfoWindowUnique($blnInfoWindowUnique)
	{
		$this->infoWindowUnique = $blnInfoWindowUnique;

		return $this;
	}

	/**
	 * Adds different sizes for specific viewport scales (0..$intBreakpointMax)
	 *
	 * @param $arrResponsiveSizes array e.g. array(767 => array(100, 200, 'px'))
	 *
	 * @return $this
	 */
	public function setResponsiveSizes(array $arrResponsiveSizes)
	{
		$this->responsiveSizes = $arrResponsiveSizes;

		return $this;
	}

	/**
	 * Adds a different size for a specific viewport scale (0..$intBreakpointMax)
	 *
	 * @param $intBreakpointMax
	 * @param $arrSize
	 *
	 * @return $this
	 */
	public function addResponsiveSize($intBreakpointMax, $arrSize)
	{
		$this->responsiveSizes[$intBreakpointMax] = $arrSize;

		return $this;
	}

	/**
	 * @deprecated 1.3 - use addOverlay
	 */
	public function addMarker(GoogleMapMarker $objMarker)
	{
		return $this->addOverlay($objMarker);
	}

	public function addOverlay(GoogleMapOverlay $objOverlay)
	{
		$this->arrOverlays[] = $objOverlay;
		// add options to elements, to provide a better individual map id
		$this->arrOptions['elements'][] = $objOverlay->getOptions();

		return $this;
	}

	public function initId()
	{
		$this->arrOptions['id'] = $this->arrOptions['id'] ?: substr(md5(implode('', $this->arrOptions) . rand(0, 10000)), 0, 8);
	}

	public function getId()
	{
		return $this->arrOptions['id'];
	}

	public function generate(array $arrOptions = [])
	{
		$this->arrOptions = array_merge($this->arrOptions, $arrOptions);

		$arrData = $this->getData($this->arrOptions);

		if (!$arrOptions['dlh_googlemap_nocss']) {
			\delahaye\googlemaps\Googlemap::CssInjection();
		}

		$this->arrOptions['staticMap'] = $this->generateStatic($this->arrOptions);

		$objTemplate = new \FrontendTemplate($this->arrOptions['dlh_googlemap_template']);

		if (!isset($GLOBALS['TL_JAVASCRIPT']['googlemaps'])) {

			$strUrl = '//maps.google.com/maps/api/js';
			$strUrl = Url::addQueryString('language=' . $this->arrOptions['language'], $strUrl);

			global $objPage;

			if (($objRootPage = \PageModel::findPublishedById($objPage->rootId)) !== null && $objRootPage->dlh_googlemaps_apikey) {
				$strUrl = Url::addQueryString('key=' . $objRootPage->dlh_googlemaps_apikey, $strUrl);
			}

			$GLOBALS['TL_JAVASCRIPT']['googlemaps'] = $strUrl;
		}

		$objTemplate->map    = $arrData;
		$objTemplate->tabs   = $arrData['dlh_googlemap_tabs'];
		$objTemplate->labels = $GLOBALS['TL_LANG']['dlh_googlemaps']['labels'];

		return $objTemplate->parse();
	}

	public function generateStatic(array $arrOptions = [])
	{
		$this->arrOptions = array_merge($this->arrOptions, $arrOptions);

		$arrData = $this->getData($this->arrOptions);

		$strMap =
			'<img src="http' . (\Environment::get('ssl') ? 's' : '') . '://maps.google.com/maps/api/staticmap?center=' . $arrData['center']
			. '&amp;zoom=' . $arrData['zoom'] . '&amp;maptype=' . strtolower($arrData['mapTypeId']) . '&amp;language=' . $arrData['language']
			. '&amp;size=';

		if ($arrData['mapSize'][2] == 'px') {
			$strMap .= $arrData['mapSize'][0] . 'x' . $arrData['mapSize'][1];
		} else {
			$strMap .= '800x600';
		}

		$arrIcons = [];

		if (!empty($arrData['elements'])) {
			foreach ($arrData['elements'] as $arrElement) {
				// icons
				if (is_array($arrElement['staticMapPart'])) {
					$arrIcons[key($arrElement['staticMapPart'])][] = $arrElement['staticMapPart'];
					continue;
				}

				$strMap .= $arrElement['staticMapPart'];
			}
		}

		$intIcons = 0;

		// bundle the markers to positions in static maps, max 5 icons
		foreach ($arrIcons as $k => $v) {
			if ($intIcons < 5) {
				$strMap .= '&amp;' . $k . implode('|', $v);
				$intIcons++;
			}
		}
		
		$strMap .= '" alt="' . $GLOBALS['TL_LANG']['tl_dlh_googlemaps']['labels']['noscript'] . '"' . $arrData['tagEnding'];

		return $strMap;
	}

	protected function getData(array $arrData)
	{
		$arrData['id'] = $arrData['id'] ?: substr(md5(implode('', $arrData) . rand(0, 10000)), 0, 8);

		// empty markers before
		$arrData['elements'] = [];

		foreach ($this->arrOverlays as $objOverlay) {
			$arrData['elements'][] = [
                'data'          => $objOverlay->getOptions(),
                'parsed'        => $objOverlay->generate(['map' => $arrData['id'], 'infoWindowUnique' => $arrData['infoWindowUnique']]),
                'staticMapPart' => $objOverlay->generateStatic(['map' => $arrData['id'], 'infoWindowUnique' => $arrData['infoWindowUnique']]),];
		}
		
		return $arrData;
	}

	protected function prepare(array $arrOptions = [])
	{
		global $objPage;

		$arrDefaults = [
            'center'                        => '51.163375,10.447683',
            'tagEnding'                     => ($objPage->outputFormat == 'xhtml') ? ' />' : '>',
            'elements'                      => [],
            'dlh_googlemap_nocss'           => false,
            'dlh_googlemap_template'        => 'dlh_googlemaps_haste',
            'dlh_googlemap_tabs'            => false,
            'mapSize'                       => [600, 400, 'px'],
            'responsiveSizes'               => [],
            'zoom'                          => 10,
            'language'                      => $GLOBALS['TL_LANGUAGE'],
            'mapTypeId'                     => 'ROADMAP',
            'mapTypesAvailable'             => ['HYBRID', 'ROADMAP', 'SATELLITE', 'TERRAIN'],
            'staticMapNoScript'             => 1,
            'infoWindowUnique'              => false,
            'useMapTypeControl'             => 1,
            'mapTypeControlStyle'           => 'DEFAULT',
            'mapTypeControlStyleAvailable'  => ['DEFAULT', 'DROPDOWN_MENU', 'HORIZONTAL_BAR'],
            'mapTypeControlPos'             => 'TOP_RIGHT',
            'mapTypeControlPosAvailable'    => [
				'TOP_LEFT',
				'TOP_CENTER',
				'TOP_RIGHT',
				'LEFT_TOP',
				'C1',
				'RIGHT_TOP',
				'LEFT_CENTER',
				'C2',
				'RIGHT_CENTER',
				'LEFT_BOTTOM',
				'C3',
				'RIGHT_BOTTOM',
				'BOTTOM_LEFT',
				'BOTTOM_CENTER',
				'BOTTOM_RIGHT',
            ],
            'useZoomControl'                => 1,
            'zoomControlStyle'              => 'SMALL',
            'zoomControlStyleAvailable'     => ['ANDROID', 'DEFAULT', 'SMALL', 'ZOOM_PAN'],
            'zoomControlPos'                => 'TOP_RIGHT',
            'zoomControlPosAvailable'       => [
				'TOP_LEFT',
				'TOP_CENTER',
				'TOP_RIGHT',
				'LEFT_TOP',
				'C1',
				'RIGHT_TOP',
				'LEFT_CENTER',
				'C2',
				'RIGHT_CENTER',
				'LEFT_BOTTOM',
				'C3',
				'RIGHT_BOTTOM',
				'BOTTOM_LEFT',
				'BOTTOM_CENTER',
				'BOTTOM_RIGHT',
            ],
            'useRotateControl'              => 1,
            'rotateControlPos'              => 'TOP_LEFT',
            'rotateControlPosAvailable'     => [
				'TOP_LEFT',
				'TOP_CENTER',
				'TOP_RIGHT',
				'LEFT_TOP',
				'C1',
				'RIGHT_TOP',
				'LEFT_CENTER',
				'C2',
				'RIGHT_CENTER',
				'LEFT_BOTTOM',
				'C3',
				'RIGHT_BOTTOM',
				'BOTTOM_LEFT',
				'BOTTOM_CENTER',
				'BOTTOM_RIGHT',
            ],
            'usePanControl'                 => 0,
            'panControlPos'                 => 'BOTTOM_LEFT',
            'panControlPosAvailable'        => [
				'TOP_LEFT',
				'TOP_CENTER',
				'TOP_RIGHT',
				'LEFT_TOP',
				'C1',
				'RIGHT_TOP',
				'LEFT_CENTER',
				'C2',
				'RIGHT_CENTER',
				'LEFT_BOTTOM',
				'C3',
				'RIGHT_BOTTOM',
				'BOTTOM_LEFT',
				'BOTTOM_CENTER',
				'BOTTOM_RIGHT',
            ],
            'useStreetViewControl'          => 0,
            'streetViewControlPos'          => 'TOP_LEFT',
            'streetViewControlPosAvailable' => [
				'TOP_LEFT',
				'TOP_CENTER',
				'TOP_RIGHT',
				'LEFT_TOP',
				'C1',
				'RIGHT_TOP',
				'LEFT_CENTER',
				'C2',
				'RIGHT_CENTER',
				'LEFT_BOTTOM',
				'C3',
				'RIGHT_BOTTOM',
				'BOTTOM_LEFT',
				'BOTTOM_CENTER',
				'BOTTOM_RIGHT',
            ],
            'useOverviewMapControl'         => 1,
            'overviewMapControlOpened'      => 1,
            'disableDoubleClickZoom'        => 1,
            'scrollwheel'                   => 0,
            'draggable'                     => 1,
            'useScaleControl'               => 1,
            'scaleControlPos'               => 'TOP_LEFT',
            'scaleControlPosAvailable'      => [
				'TOP_LEFT',
				'TOP_CENTER',
				'TOP_RIGHT',
				'LEFT_TOP',
				'C1',
				'RIGHT_TOP',
				'LEFT_CENTER',
				'C2',
				'RIGHT_CENTER',
				'LEFT_BOTTOM',
				'C3',
				'RIGHT_BOTTOM',
				'BOTTOM_LEFT',
				'BOTTOM_CENTER',
				'BOTTOM_RIGHT',
            ],
            'useCustomControl'              => 0,
            'customControlAction'           => 'NONE',
            'customControlActionsAvailable' => ['NONE', 'RESIZE'],
            'customControlPos'              => 'LEFT_CENTER',
            'customControlHTML'             => '',
            'customControlPosAvailable'     => [
                'TOP_LEFT',
                'TOP_CENTER',
                'TOP_RIGHT',
                'LEFT_TOP',
                'C1',
                'RIGHT_TOP',
                'LEFT_CENTER',
                'C2',
                'RIGHT_CENTER',
                'LEFT_BOTTOM',
                'C3',
                'RIGHT_BOTTOM',
                'BOTTOM_LEFT',
                'BOTTOM_CENTER',
                'BOTTOM_RIGHT',
            ],
            'parameter'                     => '',
            'moreParamter'                  => '',
        ];

		$this->arrOptions = array_merge($arrDefaults, $arrOptions);

		$this->arrOptions['mapSize'][2] = ($this->arrOptions['mapSize'][2] == 'pcnt' ? '%' : $this->arrOptions['mapSize'][2]);

		return $this;
	}

	/**
	 * @return bool true if dlh_googlemaps is available, otherwise false
	 */
	private static function init()
	{
		if (!in_array('dlh_googlemaps', \ModuleLoader::getActive())) {
			return false;
		}

		\Controller::loadLanguageFile('tl_dlh_googlemaps');

		return true;
	}

	/**
	 * Set an object property
	 *
	 * @param string $strKey
	 * @param mixed  $varValue
	 */
	public function __set($strKey, $varValue)
	{
		$this->arrOptions[$strKey] = $varValue;
	}


	/**
	 * Return an object property
	 *
	 * @param string $strKey
	 *
	 * @return mixed
	 */
	public function __get($strKey)
	{
		if (isset($this->arrOptions[$strKey])) {
			return $this->arrOptions[$strKey];
		}

		return parent::__get($strKey);
	}


	/**
	 * Check whether a property is set
	 *
	 * @param string $strKey
	 *
	 * @return boolean
	 */
	public function __isset($strKey)
	{
		return isset($this->arrOptions[$strKey]);
	}


	public function getOptions()
	{
		return $this->arrOptions;
	}

}
