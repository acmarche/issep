<?php

namespace AcMarche\Issep\Model;

use Carbon\Carbon;

class Indice
{
    public ?\DateTime $ts = null;
    public ?string $color = null;
    public ?string $label = null;
    public bool $isFixed = false;

    public function __construct(public string $config_id, public string $aqi_value, public string $point_name) {}

    public static function createFromStd(\stdClass $data): Indice
    {
        $indice = new Indice($data->config_id, $data->aqi_value, $data->point_name);
        $ts = Carbon::parse($data->ts);
        $indice->ts = $ts->toDateTime();

        return $indice;
    }

}