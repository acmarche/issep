<?php

namespace AcMarche\Issep\Utils;

class FeuUtils
{
    public static function color(int $aqiValue): string
    {
        return match ($aqiValue) {
            1, 2, 3, 4 => 'green',
            5, 6, 7 => 'yellow',
            8, 9, 10 => 'red',
            default => 'grey',
        };
    }

    /**
     * Pour map
     * https://github.com/pointhi/leaflet-color-markers/tree/master/img
     * @return string
     */
    public static function colorGrey(): string
    {
        return 'grey';
    }
}
