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
        $indices = $this->sinsinIndices();
        $lastSinsin = count($indices) ? $indices[0] : null;

        array_map(function ($station) use ($lastSinsin) {
            $station->indices = $this->stationRepository->getIndicesByStation($station->id_configuration);
            if ($station->indices !== []) {
                $last = $station->indices[0];
                $this->fixNoData($last, $lastSinsin);
                $station->last_indice = $this->setColorOnIndice($last);
            }
        }, $stations);
    }

    /**
     * @return array|Indice[]
     */
    public function sinsinIndices(): array
    {
        $sinsinStation = $this->stationRepository->getStation(StationsEnum::SINSIN->value);

        return $this->stationRepository->getIndicesByStation($sinsinStation->id_configuration);
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

    private function fixNoData(Indice $indice, ?Indice $sinsinindice): void
    {
        if (!$sinsinindice) {
            return;
        }
        if ($indice->aqi_value == IndiceEnum::NO_VALID->value) {
            $indice->isFixed = true;
            $indice->originalValue = $indice->aqi_value;
            $indice->aqi_value = $sinsinindice->aqi_value;
        }
    }
}
