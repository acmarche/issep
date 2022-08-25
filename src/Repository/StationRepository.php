<?php

namespace AcMarche\Issep\Repository;

use AcMarche\Issep\Utils\SortUtils;

class StationRepository
{
    public ?string $urlExecuted = null;

    public function __construct(private StationRemoteRepository $stationRemoteRepository)
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
        $stationsTmp = json_decode($this->stationRemoteRepository->fetchStations());
        $this->setUrlExecuted();

        $regex = "#\((\d{1,2})\)#";
        foreach ($stationsTmp as $station) {
            preg_match($regex, $station->nom, $x);
            $station->number = $x[1];
            $stations[] = $station;
        }

        return SortUtils::sortStations($stations);
    }

    public function getStation(int $idStation): ?\stdClass
    {
        $stations = $this->getStations();

        if (!$key = array_search($idStation, array_column($stations, 'id'))) {
            return null;
        }

        return $stations[$key];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getConfigs(): array
    {
        $configs = json_decode($this->stationRemoteRepository->fetchConfigs());
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
     * @param int $idStation
     * @param string $dateBegin
     * @param string $dateEnd
     *
     * @return array
     * @throws \Exception
     */
    public function fetchStationData(int $idStation, string $dateBegin, string $dateEnd): array
    {
        $data = json_decode($this->stationRemoteRepository->fetchStationData($idStation, $dateBegin, $dateEnd));
        $this->setUrlExecuted();

        return $data;
    }

    public function getIndices(): array
    {
        $data = json_decode($this->stationRemoteRepository->fetchIndices());
        $this->setUrlExecuted();

        return $data;
    }

    public function getIndicesByStation(int $idConfig, array $indices = []): array
    {
        if (count($indices) < 1) {
            $indices = $this->getIndices();
        }

        $data = array_filter($indices, function ($station) use ($idConfig) {
            return (int)$station->config_id === $idConfig;
        });

        return SortUtils::sortByDate($data);
    }

    private function setUrlExecuted(): void
    {
        $this->urlExecuted = $this->stationRemoteRepository->urlExecuted;
    }
}
