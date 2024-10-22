<?php

namespace AcMarche\Issep\Indice;

use AcMarche\Issep\Model\Indice;
use AcMarche\Issep\Model\Station;
use AcMarche\Issep\Repository\StationRepository;
use AcMarche\Issep\Repository\StationsEnum;

class IndiceUtils
{
    public function __construct(private readonly StationRepository $stationRepository) {}

    public function setIndicesEnum(array $indices)
    {
        foreach ($indices as $indice) {
            $indice->indice = $this->setColorOnIndice($indice);
        }
    }

    /**
     * @param Station[] $stations
     * @return void
     */
    public function setIndices(array $stations): void
    {
        $sinsinStation = $this->stationRepository->getStation(StationsEnum::SINSIN->value);
        array_map(function ($station) {
            $station->indices = $this->stationRepository->getIndicesByStation($station->id_configuration);

            if ($station->indices !== []) {
                $station->last_indice = $this->setColorOnIndice($station->indices[0]);
            }
        }, $stations);
    }

    private function setColorOnIndice(Indice $indice): Indice
    {
        $indice->color = IndiceEnum::colorByIndice($indice->aqi_value);
        $indice->label = IndiceEnum::labelByIndice($indice->aqi_value);

        return $indice;
    }
}
