<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Util;


class GeoLocation
{
    /**
     * Get distance between position A and position B as kilometer
     *
     * @param $latitudeA
     * @param $longitudeA
     * @param $latitudeB
     * @param $longitudeB
     *
     * @return int
     */
    public static function getDistance($latitudeA, $longitudeA, $latitudeB, $longitudeB)
    {
        $earth_radius = 6371;

        $dLat = deg2rad($latitudeB - $latitudeA);
        $dLon = deg2rad($longitudeB - $longitudeA);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($latitudeA)) * cos(deg2rad($latitudeB)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;

        return $d;
    }
}