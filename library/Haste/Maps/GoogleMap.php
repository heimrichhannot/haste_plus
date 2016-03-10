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

class GoogleMap
{
	/**
	 * Current object instance (do not remove)
	 *
	 * @var object
	 */
	protected static $objInstance;

	protected $arrOptions = array();

	protected $arrOverlays = array();

	protected function __construct()
	{
		$this->prepare();
	}

	/**
	 * Prevent cloning of the object (Singleton)
	 */
	final public function __clone()
	{
	}

	/**
	 * Instantiate a new user object (Factory)
	 *
	 * @return static The object instance
	 */
	public static function getInstance()
	{
		if (!static::init()) {
			return null;
		}

		if (static::$objInstance === null) {
			static::$objInstance = new static();
		}

		return static::$objInstance;
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

	public function generate(array $arrOptions = array())
	{
		$this->arrOptions = array_merge($this->arrOptions, $arrOptions);

		$arrData = $this->getData($this->arrOptions);

		if (!$arrOptions['dlh_googlemap_nocss']) {
			\delahaye\googlemaps\Googlemap::CssInjection();
		}

		$this->arrOptions['staticMap'] = $this->generateStatic($this->arrOptions);

		$objTemplate = new \FrontendTemplate($this->arrOptions['dlh_googlemap_template']);

		if (!isset($GLOBALS['TL_JAVASCRIPT']['googlemaps'])) {
			$GLOBALS['TL_JAVASCRIPT']['googlemaps'] =
				'http' . (\Environment::get('ssl') ? 's' : '') . '://maps.google.com/maps/api/js?language=' . $this->arrOptions['language'];
		}

		$objTemplate->map    = $arrData;
		$objTemplate->tabs   = $arrData['dlh_googlemap_tabs'];
		$objTemplate->labels = $GLOBALS['TL_LANG']['dlh_googlemaps']['labels'];

		return $objTemplate->parse();
	}

	public function generateStatic(array $arrOptions = array())
	{
		$this->arrOptions = array_merge($this->arrOptions, $arrOptions);

		$arrData = $this->getData($this->arrOptions);

		$strMap =
			'<img src="http' . (\Environment::get('ssl') ? 's' : '') . '://maps.google.com/maps/api/staticmap?center=' . $arrData['center']
			. '&amp;zoom=' . $arrData['zoom'] . '&amp;maptype=' . strtolower($arrData['mapTypeId']) . '&amp;language=' . $arrData['language'] . '&amp;size=';

		if ($arrData['mapSize'][2] == 'px') {
			$strMap .= $arrData['mapSize'][0] . 'x' . $arrData['mapSize'][1];
		} else {
			$strMap .= '800x600';
		}

		$arrIcons = array();

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
		$arrData['id'] = $arrData['id'] ?: substr(md5(implode('', $arrData)), 0, 8);

		// empty markers before
		$arrData['elements'] = array();

		foreach ($this->arrOverlays as $objOverlay) {
			$arrData['elements'][] = array
			(
				'data'          => $objOverlay->getOptions(),
				'parsed'        => $objOverlay->generate(array('map' => $arrData['id'], 'infoWindowUnique' => $arrData['infoWindowUnique'])),
				'staticMapPart' => $objOverlay->generateStatic(array('map' => $arrData['id'], 'infoWindowUnique' => $arrData['infoWindowUnique'])),
			);
		}
		
		return $arrData;
	}

	protected function prepare(array $arrOptions = array())
	{
		global $objPage;

		$arrDefaults = array
		(
			'center'                        => '51.163375,10.447683',
			'tagEnding'                     => ($objPage->outputFormat == 'xhtml') ? ' />' : '>',
			'elements'                      => array(),
			'dlh_googlemap_nocss'           => false,
			'dlh_googlemap_template'        => 'dlh_googlemaps_haste',
			'dlh_googlemap_tabs'            => false,
			'mapSize'                       => array(600, 400, 'px'),
			'zoom'                          => 10,
			'language'                      => $GLOBALS['TL_LANGUAGE'],
			'mapTypeId'                     => 'ROADMAP',
			'mapTypesAvailable'             => array('HYBRID', 'ROADMAP', 'SATELLITE', 'TERRAIN'),
			'staticMapNoScript'             => 1,
			'infoWindowUnique'              => false,
			'useMapTypeControl'             => 1,
			'mapTypeControlStyle'           => 'DEFAULT',
			'mapTypeControlStyleAvailable'  => array('DEFAULT', 'DROPDOWN_MENU', 'HORIZONTAL_BAR'),
			'mapTypeControlPos'             => 'TOP_RIGHT',
			'mapTypeControlPosAvailable'    => array(
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
			),
			'useZoomControl'                => 1,
			'zoomControlStyle'              => 'SMALL',
			'zoomControlStyleAvailable'     => array('ANDROID', 'DEFAULT', 'SMALL', 'ZOOM_PAN'),
			'zoomControlPos'                => 'TOP_RIGHT',
			'zoomControlPosAvailable'       => array(
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
			),
			'useRotateControl'              => 1,
			'rotateControlPos'              => 'TOP_LEFT',
			'rotateControlPosAvailable'     => array(
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
			),
			'usePanControl'                 => 0,
			'panControlPos'                 => 'BOTTOM_LEFT',
			'panControlPosAvailable'        => array(
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
			),
			'useStreetViewControl'          => 0,
			'streetViewControlPos'          => 'TOP_LEFT',
			'streetViewControlPosAvailable' => array(
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
			),
			'useOverviewMapControl'         => 1,
			'overviewMapControlOpened'      => 1,
			'disableDoubleClickZoom'        => 1,
			'scrollwheel'                   => 0,
			'draggable'                     => 1,
			'useScaleControl'               => 1,
			'scaleControlPos'               => 'TOP_LEFT',
			'scaleControlPosAvailable'      => array(
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
			),
			'parameter'                     => '',
			'moreParamter'                  => '',
		);

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
