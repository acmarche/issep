<?php

namespace AcMarche\Issep\Repository;

use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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

        $uri = $this->removeMarcheFromUrl();

        return $this->executeRequest($uri.'/config/'.$idCapteur.'/start/'.$dateBegin.'/end/'.$dateEnd);
    }

    public function lastBelAqi(): ?string
    {
        if (!$this->httpClient instanceof HttpClientInterface) {
            $this->connect();
        }

        return $this->executeRequest($this->base_uri.'/lastbelaqi');
    }

    public function belAqi(): ?string
    {
        if (!$this->httpClient instanceof HttpClientInterface) {
            $this->connect();
        }

        return $this->executeRequest($this->base_uri.'/belaqi');
    }

    private function removeMarcheFromUrl(): string
    {
        $parsedUrl = parse_url($this->base_uri, PHP_URL_PATH);
        $trimmedPath = dirname($parsedUrl);

        return parse_url($this->base_uri, PHP_URL_SCHEME)."://".parse_url($this->base_uri, PHP_URL_HOST).$trimmedPath;
    }
}
