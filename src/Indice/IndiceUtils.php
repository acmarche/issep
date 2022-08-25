<?php

namespace AcMarche\Issep\Indice;

use AcMarche\Issep\Repository\StationRepository;

class IndiceUtils
{
    public function __construct(private StationRepository $stationRepository)
    {
    }

    public function setIndicesEnum(array $indices)
    {
        foreach ($indices as $indice) {
            $indice->indice = $this->createIndiceModel($indice);
        }
    }

    public function setIndices(array $stations, array $indices): void
    {
        array_map(function ($station) use ($indices) {
            $indices = $this->stationRepository->getIndicesByStation($station->id_configuration, $indices);
            $station->indice = null;
            if (count($indices) > 0) {
                $station->indice = $this->createIndiceModel($indices[0]);
            }
        }, $stations);
    }

    public function setColors(array $stations, array $indices): void
    {
        array_map(function ($station) use ($indices) {
            $indices = $this->stationRepository->getIndicesByStation($station->id_configuration, $indices);
            if (count($indices) > 0) {
                $station->color = 'black';
                $indice = IndiceEnum::colorByIndice($indices[0]->aqi_value);
                if ($indice) {
                    $station->color = $indice->color();
                }
            }
        }, $stations);
    }

    private function createIndiceModel(object $indice): IndiceModel
    {
        $t = IndiceEnum::colorByIndice($indice->aqi_value)->color();
        $d = IndiceEnum::labelByIndice($indice->aqi_value);

        return new IndiceModel($t, $d);
    }
}
