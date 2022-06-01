<?php

namespace AcMarche\Issep\Indice;

use AcMarche\Issep\Repository\StationRepository;

class IndiceUtils
{
    private StationRepository $stationRepository;

    public function __construct()
    {
        $this->stationRepository = new StationRepository();
    }

    public static function setColors(array $indices)
    {
        foreach ($indices as $indice) {
            $indice->indice = Indice::colorByIndice($indice->aqi_value);
        }
    }

    public function setIndices(array $stations, array $indices): void
    {
        array_map(function ($station) use ($indices) {
            $indices = $this->stationRepository->getIndicesByStation($station->id_configuration, $indices);
            $station->indice = null;
            if (count($indices) > 0) {
                $station->indice = Indice::colorByIndice($indices[0]->aqi_value);
            }
        }, $stations);
    }

    public function setColors2(array $stations, array $indices): void
    {
        array_map(function ($station) use ($indices) {
            $indices = $this->stationRepository->getIndicesByStation($station->id_configuration, $indices);
            if (count($indices) > 0) {
                $station->color = 'black';
                $indice = Indice::colorByIndice($indices[0]->aqi_value);
                if ($indice) {
                    $station->color = $indice->color();
                }
            }
        }, $stations);
    }
}