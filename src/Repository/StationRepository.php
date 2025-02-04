<?php

namespace AcMarche\Issep\Repository;

use AcMarche\Issep\Model\Indice;
use AcMarche\Issep\Model\Station;
use AcMarche\Issep\Utils\SortUtils;
use Carbon\Carbon;
use Exception;

class StationRepository
{
    public array $urlsExecuted = [];
    /**
     * @var Indice[] $indices
     */
    public array $indices = [];

    public function __construct(private readonly StationRemoteRepository $stationRemoteRepository) {}

    /**
     * @return Station[]
     * @throws \JsonException
     */
    public function getStations(): array
    {
        $stations = [];
        $stationsTmp = json_decode($this->stationRemoteRepository->fetchStations(), null, 512, JSON_THROW_ON_ERROR);
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
     * @return array
     * @throws \JsonException
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
    public function getIndices(): array
    {
        $this->indices = [];
        try {
            $sixMonthsAgo = new \DateTime();
            $sixMonthsAgo->modify('-6 MONTHS');
            $data = json_decode($this->stationRemoteRepository->fetchIndicesBelAqi(), flags: JSON_THROW_ON_ERROR);
            $this->setUrlExecuted();
            if (is_array($data)) {
                foreach ($data as $item) {
                    $date = Carbon::parse($item->ts)->toDateTime();
                    if ($date->format('Y-m-d') < $sixMonthsAgo->format('Y-m-d')) {
                        continue;
                    }
                    $this->indices[] = Indice::createFromStd($item);
                }
            }
        } catch (Exception $e) {
            dump($e->getMessage());
        }

        return $this->indices;
    }

    /**
     * @param int $idConfig
     * @return Indice[]
     */
    public function getIndicesByStation(int $idConfig): array
    {
        if (count($this->indices) === 0) {
            $this->getIndices();
        }
        $data = array_filter($this->indices, fn($station) => (int)$station->config_id === $idConfig);

        return SortUtils::sortByDate($data);
    }

    private function setUrlExecuted(): void
    {
        $this->urlsExecuted[] = $this->stationRemoteRepository->urlExecuted;
    }
}
