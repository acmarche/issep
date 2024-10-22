<?php

namespace AcMarche\Issep\Model;

class Station
{
    public array $indices = [];
    public ?Indice $last_indice = null;
    public ?\DateTime $attrib_start = null;
    public ?\DateTime $attrib_end = null;
    public ?string $color = null;

    public function __construct(
        public int $id,
        public string $nom,
        public int $id_reseau,
        public string $x,
        public string $y,
        public float $lat,
        public float $lon,
        public ?string $altitude,
        public ?string $h,
        public int $id_configuration,
    ) {}

    public static function fromStd(\stdClass $data): Station
    {
        $station = new Station(
            $data->id,
            $data->nom,
            $data->id_reseau,
            $data->x,
            $data->y,
            $data->lat,
            $data->lon,
            $data->altitude,
            $data->h,
            $data->id_configuration,
        );

        $attrib_start = \DateTime::createFromFormat('Y-m-d H:i:s.u', $data->attrib_start);
        $attrib_end = \DateTime::createFromFormat('Y-m-d H:i:s.u', $data->attrib_start);
        if ($attrib_start) {
            $station->attrib_start = $attrib_start;
        }
        if ($attrib_end) {
            $station->attrib_end = $attrib_end;
        }

        return $station;
    }
}
