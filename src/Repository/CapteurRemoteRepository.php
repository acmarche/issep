<?php

namespace AcMarche\Issep\Repository;

class CapteurRemoteRepository
{
    use ConnectionTrait;

    public function __construct()
    {
        $this->connect();
    }

    /**
     *
     */
    public function fetchCapteurs(): ?string
    {
        return $this->executeRequest($this->base_uri.'/point');
    }

    /**
     *
     * @return string|null
     * @throws \Exception
     */
    public function fetchConfigs(): ?string
    {
        return $this->executeRequest($this->base_uri.'/config/lastdata');
    }

}