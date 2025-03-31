<?php

namespace AcMarche\Issep\Indice;

use AcMarche\Issep\Model\Indice;
use AcMarche\Issep\Model\Station;
use AcMarche\Issep\Repository\StationRepository;

class IndiceUtils
{
    public function __construct(private readonly StationRepository $stationRepository)
    {
    }

    /**
     * @param Station[] $stations
     * @return void
     */
    public function setLastBelAqiOnStations(array $stations): void
    {
        array_map(function ($station) {
            $data = $this->stationRepository->getLastBelAquiByStation($station->idConfiguration);
            $station->lastBelAqi = $this->setColorOnIndice($data);
        }, $stations);
    }

    public function setColorOnAllIndices(array $indices): void
    {
        foreach ($indices as $indice) {
            $indice->indice = $this->setColorOnIndice($indice);
        }
    }

    public function setColorOnIndice(?Indice $indice): ?Indice
    {
        if (!$indice) {
            return null;
        }

        $indice->color = IndiceEnum::colorByIndice($indice->aqiValue);
        $indice->label = IndiceEnum::labelByIndice($indice->aqiValue);

        return $indice;
    }

    /**
     * @param Station[] $stations
     * @param \DateTime|null $dateEnd
     * @return void
     * @throws \DateMalformedStringException
     */
    public function setLastData(array $stations, \DateTime $dateEnd = null): void
    {
        $dateBegin = date('Y-m-d');
        if (!$dateEnd) {
            $dateEnd = new \DateTime();
            $dateEnd->modify('+1 weeks');
        }

        foreach ($stations as $station) {
            try {
                $station->airQualityData = $this->stationRepository->fetchStationData(
                    $station->idConfiguration,
                    $dateBegin,
                    $dateEnd->format('Y-m-d'),
                );
            } catch (\JsonException|\Exception$e) {
                $station->airQualityData = [];
            }
        }
    }
}
