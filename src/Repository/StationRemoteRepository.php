<?php

namespace AcMarche\Issep\Repository;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Exception;
class StationRemoteRepository
{
    use ConnectionTrait;

    public function fetchStations(): ?string
    {
        if (!$this->httpClient instanceof HttpClientInterface) {
            $this->connect();
        }

        return $this->executeRequest($this->base_uri . '/point');
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

        return $this->executeRequest($this->base_uri . '/point/config/lastdata');
    }

    /**
     *  config/58/data/start/2022-01-01/end/2022-05-18
     *
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

        return $this->executeRequest($this->base_uri . '/config/' . $idCapteur . '/data/start/' . $dateBegin . '/end/' . $dateEnd);
    }

    /**
     * @deprecated
     * @return string|null
     * @throws Exception
     */
    public function fetchIndices(): ?string
    {
        if (!$this->httpClient instanceof HttpClientInterface) {
            $this->connect();
        }

        return $this->executeRequest($this->base_uri . '/euaqi');
    }

    public function fetchIndicesBelAqi(): ?string
    {
        if (!$this->httpClient instanceof HttpClientInterface) {
            $this->connect();
        }

        return $this->executeRequest($this->base_uri . '/belaqi');
    }
}
