<?php

namespace AcMarche\Issep\Repository;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Exception;

class StationRemoteRepository
{
    use ConnectionTrait;

    private string $token;

    public function fetchStations(): ?string
    {
        if (!$this->httpClient instanceof HttpClientInterface) {
            $this->connect();
        }

        return $this->executeRequest($this->base_uri.'/locations');
    }

    /**
     *
     * @return string|null
     * @throws Exception
     */
    public function fetchConfigs(): ?string
    {
        if (!$this->httpClient instanceof HttpClientInterface) {
            $this->connect();
        }

        return $this->executeRequest($this->base_uri.'/lastdata');
    }

    /**
     * https://opendata.issep.be/env/air/api/microsensor/config/10388/start/2025-02-03/end/2025-02-04
     *
     * @param int $idCapteur
     * @param string $dateBegin 2022-01-01
     * @param string $dateEnd 2022-05-18
     *
     * @return string|null
     * @throws Exception
     */
    public function fetchStationData(int $idCapteur, string $dateBegin, string $dateEnd): ?string
    {
        if (!$this->httpClient instanceof HttpClientInterface) {
            $this->connect();
        }

        return $this->executeRequest($this->base_uri.'/config/'.$idCapteur.'/data/start/'.$dateBegin.'/end/'.$dateEnd);
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function lastData(): ?string
    {
        if (!$this->httpClient instanceof HttpClientInterface) {
            $this->connect();
        }

        return $this->executeRequest($this->base_uri.'/lastdata');
    }

    public function fetchIndicesBelAqi(): ?string
    {
        if (!$this->httpClient instanceof HttpClientInterface) {
            $this->connect();
        }

        return $this->executeRequest($this->base_uri.'/lastbelaqi');
    }
}
