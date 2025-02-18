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
    public function setLastBelAqiOnStations(array $stations): void
    {
        array_map(function ($station) {
            $data = $this->stationRepository->getLastBelAquiByStation($station->idConfiguration);
            if ($data !== []) {
                $last = $data[0];
                $station->lastBelAqi = $this->setColorOnIndice($last);
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
        $indice->color = IndiceEnum::colorByIndice($indice->aqiValue);
        $indice->label = IndiceEnum::labelByIndice($indice->aqiValue);

        return $indice;
    }
}
