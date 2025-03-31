<?php

namespace AcMarche\Issep\Repository;

use AcMarche\Issep\Model\AirQualityData;
use AcMarche\Issep\Model\Indice;
use AcMarche\Issep\Model\Station;
use AcMarche\Issep\Utils\SortUtils;
use Exception;

class StationRepository
{
    public array $urlsExecuted = [];
    /**
     * @var Indice[] $lastAllBelAqui
     */
    public array $lastAllBelAqui = [];

    public function __construct(private readonly StationRemoteRepository $stationRemoteRepository)
    {
    }

    /**
     * @return Station[]
     * @throws \JsonException
     */
    public function getStations(): array
    {
        $stations = [];
        $stationsTmp = json_decode($this->stationRemoteRepository->fetchStations(), false, 512, JSON_THROW_ON_ERROR);

        $this->setUrlExecuted();

        foreach ($stationsTmp as $stationTmp) {
            if (in_array($stationTmp->id, StationsEnum::stationsToKeep())) {
                $station = Station::fromStd($stationTmp);
                $stations[] = $station;
            }
        }

        return SortUtils::sortStations($stations);
    }

    public function getStation(int $idStation): ?Station
    {
        $stations = $this->getStations();
        $key = array_search($idStation, array_column($stations, 'id'));
        if ($key === false) {
            return null;
        }

        return $stations[$key] ?? null;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getConfigs(): array
    {
        $configs = json_decode($this->stationRemoteRepository->fetchConfigs(), null, 512, JSON_THROW_ON_ERROR);
        $this->setUrlExecuted();

        return $configs;
    }

    public function getConfig(int $idConfiguration, array $configs = [])
    {
        if (count($configs) < 1) {
            $configs = $this->getConfigs();
        }

        $key = array_search($idConfiguration, array_column($configs, 'id_configuration'));

        return $configs[$key];
    }

    /**
     *
     * @param int $idConfiguration
     * @param string $dateBegin
     * @param string $dateEnd
     * @return AirQualityData[]
     * @throws \JsonException
     * @throws Exception
     */
    public function fetchStationData(int $idConfiguration, string $dateBegin, string $dateEnd): array
    {
        $data = json_decode(
            $this->stationRemoteRepository->fetchStationData($idConfiguration, $dateBegin, $dateEnd),
            null,
            512,
            JSON_THROW_ON_ERROR,
        );
        $this->setUrlExecuted();

        return $data;
    }

    /**
     * @return Indice[]
     */
    public function lastAllBelAqui(): array
    {
        $this->lastAllBelAqui = [];
        try {
            $data = json_decode($this->stationRemoteRepository->lastBelAqui(), flags: JSON_THROW_ON_ERROR);
            $this->setUrlExecuted();
            if (is_array($data)) {
                foreach ($data as $item) {
                    $this->lastAllBelAqui[] = Indice::createFromStd($item);
                }
            }
        } catch (Exception $e) {
            dump($e->getMessage());
        }

        return $this->lastAllBelAqui;
    }

    public function getLastBelAquiByStation(int $idConfig): ?Indice
    {
        if (count($this->lastAllBelAqui) === 0) {
            $this->lastAllBelAqui();
        }

        $data = array_filter($this->lastAllBelAqui, fn($station) => (int)$station->configId === $idConfig);
        if (count($data) === 0) {
            return null;
        } else {
            return $data[array_key_last($data)];
        }
    }

    private function setUrlExecuted(): void
    {
        $this->urlsExecuted[basename(
            $this->stationRemoteRepository->urlExecuted,
        )] = $this->stationRemoteRepository->urlExecuted;
    }
}
