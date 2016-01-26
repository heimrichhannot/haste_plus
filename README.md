# Haste Plus

Haste plus extends [codefog/contao-haste](https://packagist.org/packages/codefog/contao-haste) as a collection of tools and classes to ease working with Contao.

*Module Preview*

## Features

### Utils

The following Classes and Methods are helpers that ease the working with Contao.

Type | Name | Method | Description
---- | ---- | ---- | ----
Url | addScheme | \HeimrichHannot\Haste\Util\Url::addScheme($strUrl, $strScheme) | Add the given protocol/scheme (http://,https://,ftp://…) to the given url if not present.

### Google Maps 

If you want to add google maps with ease that are build from dynamic entities, and not withing dlh_googlemaps backend module, use the following code.

```
# news_full.html5
<?php $objMap = \HeimrichHannot\Haste\Map\GoogleMap::getInstance(); ?>

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