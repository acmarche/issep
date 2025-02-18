<?php

namespace AcMarche\Issep\Model;

class Station
{
    /**
     * @var AirQualityData[] $airQualityData
     */
    public array $airQualityData = [];
    public ?Indice $lastBelAqi = null;
    public ?\DateTime $attribStart = null;
    public ?\DateTime $attribEnd = null;
    public ?string $color = null;

    public function __construct(
        public int $id,
        public string $nom,
        public int $idReseau,
        public string $x,
        public string $y,
        public float $lat,
        public float $lon,
        public ?string $altitude,
        public ?string $h,
        public int $idConfiguration,
    ) {}

    public static function fromStd(\stdClass $data): Station
    {
        $station = new Station(
            $data->id,
            $data->nom,
            $data->idReseau,
            $data->x,
            $data->y,
            $data->lat,
            $data->lon,
            $data->altitude,
            $data->h,
            $data->idConfiguration,
        );

        $attrib_start = \DateTime::createFromFormat('Y-m-d H:i:s.u', $data->attribStart);
        $attrib_end = \DateTime::createFromFormat('Y-m-d H:i:s.u', $data->attribStart);
        if ($attrib_start) {
            $station->attribStart = $attrib_start;
        }
        if ($attrib_end) {
            $station->attribEnd = $attrib_end;
        }

        return $station;
    }
}
