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

namespace HeimrichHannot\Haste\Visualization;

class GoogleChartWrapper
{
	/**
	 * Current object instance (do not remove)
	 *
	 * @var object
	 */
	protected static $objInstance;

	protected $arrOptions = array();

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
		if (static::$objInstance === null) {
			static::$objInstance = new static();
		}

		return static::$objInstance;
	}

	public function generate(array $arrOptions = array())
	{
		$this->arrOptions = array_merge($this->arrOptions, $arrOptions);

		$arrData = $this->getData($this->arrOptions);

		$objTemplate = new \FrontendTemplate($this->arrOptions['google_chart_template']);

		if (!isset($GLOBALS['TL_JAVASCRIPT']['vendor_vis_charts_loader'])) {
			$GLOBALS['TL_JAVASCRIPT']['vendor_vis_charts_loader'] = 'https://www.gstatic.com/charts/loader.js';
		}

		$objTemplate->chart  = $arrData;
		$objTemplate->labels = $GLOBALS['TL_LANG']['google_charts']['labels'];

		return $objTemplate->parse();
	}

	protected function getData(array $arrData)
	{
		$arrData['id'] = $arrData['id'] ?: substr(md5(implode('', $arrData)), 0, 8);
		return $arrData;
	}

	protected function prepare(array $arrOptions = array())
	{
		$arrDefaults = array
		(
			'chartColumns'          => array(),
			'chartRows'             => array(),
			'chartSize'             => array(400, 300, 'px'),
			'chartType'             => 'LineChart',
			'chartTypeAvailable'    => array(
				'AnnotationChart',
				'AreaChart',
				'BarChart',
				'BubbleChart',
				'Calendar',
				'CandlestickChart',
				'ColumnChart',
				'ComboChart',
				'Sankey',
				'ScatterChart',
				'SteppedAreaChart',
				'Table',
				'Timeline',
				'TreeMap',
				'WordTree',
			),
			'dataSourceUrl'         => '',
			'google_chart_template' => 'google_chart',
			'options'               => array(),
			'query'                 => '',
		);

		$this->arrOptions = array_merge($arrDefaults, $arrOptions);

		$this->arrOptions['chartSize'][2] = ($this->arrOptions['chartSize'][2] == 'pcnt' ? '%' : $this->arrOptions['chartSize'][2]);

		return $this;
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
