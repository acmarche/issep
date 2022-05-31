<?php

namespace AcMarche\Issep\Indice;

class IndiceUtils
{
    public static function setColors(array $indices)
    {
        foreach ($indices as $indice) {
            $indice->indice = Indice::colorByIndice($indice->aqi_value);
        }
    }
}