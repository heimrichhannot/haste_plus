# Haste Plus

Haste plus extends [codefog/contao-haste](https://packagist.org/packages/codefog/contao-haste) as a collection of tools and classes to ease working with Contao.

*Module Preview*

## Features

### Cache
The PHP high-performance object caching system, [phpfastcache](https://github.com/PHPSocialNetwork/phpfastcache) is part of hast_plus.
Currently only File-Caching is supported, but Pull-Request are welcome.

#### Example : FileCache 
```
// MyClass.php

public function getSelectOptions(array $arrNewsArchives)
{
	// select a unique key
	$strCacheKey = 'my_select_options' . implode('_', $arrNewsArchives);
	
	if(FileCache::getInstance()->isExisting($strCacheKey))
	{
		return FileCache::getInstance()->get($strCacheKey);
	}
	
	$arrItems = array();
	
	// heavy sql queries or http-requests (just an example)
	$objItems = \NewsModel::findPublishedByPids($arrNewsArchives);
	
	if($objItems === null)
	{
		return $arrItems;
	}
	
	$arrItems = $objItems->fetchEach('headline');
	
	FileCache::getInstance()->set($strCacheKey, $arrItems);
  
	return $arrItems;
}

```

### Security
Add security headers to http request (configurable in tl_settings)

- IFRAME Clickjacking Protection: X-Frame-Options: SAMEORIGIN
- Allow Origins Check: Access-Control-Allow-Origins & Access-Control-Allow-Headers

### Input/Widget rgxp

The following regular expression can be used to validate widget input.

Name | Example |  Description
---- | ---- | ---- 
customDate | customDate::d.m | Validate custom date format against input. 
price | price | Validate price input.
posfloat | posfloat | Validate float numbers in input.

### Utils

The following Classes and Methods are helpers that ease the working with Contao.

Type | Name | Method | Description
---- | ---- | ---- | ----
Url | addScheme | \HeimrichHannot\Haste\Util\Url::addScheme($strUrl, $strScheme) | Add the given protocol/scheme (http://,https://,ftp://…) to the given url if not present.
Arrays | filterByPrefixes | \HeimrichHannot\Haste\Util\Arrays::filterByPrefixes($arrData, $arrayPrefixes) | Filter an array by given prefixes and return the filtered array.
Arrays | getListPositonCssClass | \HeimrichHannot\Haste\Util\Arrays::getListPositonCssClass($key, $arrList, $blnReturnAsArray) | Create the class names for an item within a array list

### Google Maps

If you want to add google maps with ease that are build from dynamic entities, and not withing dlh_googlemaps backend module, use the following code.

```
# news_full.html5
<?php $objMap = new \HeimrichHannot\Haste\Map\GoogleMap(); ?>

<?php foreach ($this->venues as $arrVenue): ?>
	<?php $objMap->setCenter($arrVenue['venueSingleCoords']); // lat and lon seperated by comma ?>
	<?php $objMarker = new \HeimrichHannot\Haste\Map\GoogleMapMarker(); ?>
	<?php $objMarker->setPosition($arrVenue['venueSingleCoords']); // lat and lon seperated by comma ?>
	<?php $objMarker->setTitle($arrVenue['venueName']); // for full list of marker options see \HeimrichHannot\Haste\Map\GoogleMapMarker::prepare()?>
	<?php $objMap->addMarker($objMarker); ?>
<?php endforeach; ?>

<?= $objMap->generate(
	array(
		'mapSize' => array('100%', '400px', ''),
		'zoom'    => 13,
	)
	// for full list of map options see \HeimrichHannot\Haste\Map\GoogleMap::prepare()
); ?>
```

## Developer notes

- provide a minimum of 3 unit test for each test case of a util method
