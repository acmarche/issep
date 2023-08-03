<?php

namespace AcMarche\Issep\Indice;

use AcMarche\Issep\Repository\StationRepository;

class IndiceUtils
{
    public function __construct(private readonly StationRepository $stationRepository)
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
            $station->indices = $this->stationRepository->getIndicesByStation($station->id_configuration, $indices);
            $station->indice = $station->last_indice = null;
            if ($station->indices !== []) {
                $station->indice = $this->createIndiceModel($station->indices[0]);
                $station->last_indice = $station->indices[0];
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
