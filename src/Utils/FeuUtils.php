<?php

namespace AcMarche\Issep\Utils;

class FeuUtils
{
    public static function color(int $aqiValue): string
    {
        return match ($aqiValue) {
            1, 2 => 'green',
            3, 4 => 'yellow',
            5, 6 => 'red',
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