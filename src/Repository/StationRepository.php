<?php

namespace AcMarche\Issep\Repository;

class StationRepository
{
    private StationRemoteRepository $stationRemoteRepository;

    public function __construct()
    {
        $this->stationRemoteRepository = new StationRemoteRepository();
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
        return json_decode($this->stationRemoteRepository->fetchStations());
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getConfigs(): array
    {
        return json_decode($this->stationRemoteRepository->fetchConfigs());
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
        return json_decode($this->stationRemoteRepository->fetchStationData($idStation, $dateBegin, $dateEnd));
    }
}