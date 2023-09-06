<?php

namespace AcMarche\Issep\Repository;

use stdClass;
use Exception;
use AcMarche\Issep\Utils\SortUtils;

class StationRepository
{
    public array $urlsExecuted = [];
    //public array $stationsToKeep = [1, 5, 10, 11, 13, 16, 18];
    public array $stationsToKeep = [15, 17, 26, 27, 13, 24, 66];

    public function __construct(private readonly StationRemoteRepository $stationRemoteRepository)
    {
    }

    /**
     *  +"id": "19"
     * +"nom": "Avenue de France (12)"
     * +"id_reseau": "12"
     * +"x": "219342"
     * +"y": "102043"
     * +"lat": "50.225957999999999"
     * +"lon": "5.3392559999999998"
     * +"altitude": null
     * +"h": null
     * +"id_configuration": "19"
     * +"config_start": "2021-06-14 00:00:00.000"
     * +"config_end": "2023-01-01 00:00:00.000"
     * @return array
     */
    public function getStations(): array
    {
        $stations = [];
        $stationsTmp = json_decode($this->stationRemoteRepository->fetchStations(), null, 512, JSON_THROW_ON_ERROR);
        $this->setUrlExecuted();

        $regex = "#\((\d{1,2})\)#";
        foreach ($stationsTmp as $station) {
            //preg_match($regex, (string) $station->nom, $x);
            //$station->number = $x[1];
            if (in_array($station->id, $this->stationsToKeep)) {
                $stations[] = $station;
            }
        }

        return SortUtils::sortStations($stations);
    }

    public function getStation(int $idStation): ?stdClass
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
     * @return array
     * @throws Exception
     */
    public function fetchStationData(int $idConfiguration, string $dateBegin, string $dateEnd): array
    {
        $data = json_decode($this->stationRemoteRepository->fetchStationData($idConfiguration, $dateBegin, $dateEnd), null, 512, JSON_THROW_ON_ERROR);
        $this->setUrlExecuted();

        return $data;
    }

    public function getIndices(): array
    {
        try {
            $data = json_decode($this->stationRemoteRepository->fetchIndices(), flags: JSON_THROW_ON_ERROR);
            $this->setUrlExecuted();
            if (is_array($data)) {
                return $data;
            }
        } catch (Exception) {
        }

        return [];
    }

    public function getIndicesByStation(int $idConfig, array $indices = []): array
    {
        if (count($indices) < 1) {
            $indices = $this->getIndices();
        }

        $data = array_filter($indices, fn ($station) => (int)$station->config_id === $idConfig);

        return SortUtils::sortByDate($data);
    }

    private function setUrlExecuted(): void
    {
        $this->urlsExecuted[] = $this->stationRemoteRepository->urlExecuted;
    }
}
