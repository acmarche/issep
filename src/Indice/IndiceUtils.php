<?php

namespace AcMarche\Issep\Indice;

use AcMarche\Issep\Model\Indice;
use AcMarche\Issep\Model\Station;
use AcMarche\Issep\Repository\StationRepository;
use AcMarche\Issep\Repository\StationsEnum;

class IndiceUtils
{
    public function __construct(private readonly StationRepository $stationRepository) {}

    /**
     * @param Station[] $stations
     * @return void
     */
    public function setIndices(array $stations): void
    {
        $sinsinStation = $this->stationRepository->getStation(StationsEnum::SINSIN->value);
        $indices = $this->stationRepository->getIndicesByStation($sinsinStation->id_configuration);
        if(count($indices) === 0) {
            return;
        }
        $lastSinsin = $indices[0];

        array_map(function ($station) use ($lastSinsin) {
            $station->indices = $this->stationRepository->getIndicesByStation($station->id_configuration);
            if ($station->indices !== []) {
                $last = $station->indices[0];
                $this->fixNoData($last, $lastSinsin->aqi_value);
                $station->last_indice = $this->setColorOnIndice($last);
            }
        }, $stations);
    }

    public function setColorOnAllIndices(array $indices): void
    {
        foreach ($indices as $indice) {
            $indice->indice = $this->setColorOnIndice($indice);
        }
    }

    private function setColorOnIndice(Indice $indice): Indice
    {
        $indice->color = IndiceEnum::colorByIndice($indice->aqi_value);
        $indice->label = IndiceEnum::labelByIndice($indice->aqi_value);

        return $indice;
    }

    private function fixNoData(Indice $indice, int $aquiValueSinsinStation): void
    {
        if ($indice->aqi_value == IndiceEnum::NO_DATA->value) {
            $indice->aqi_value = $aquiValueSinsinStation;
            $indice->isFixed = true;
        }
    }
}
