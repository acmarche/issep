<?php

namespace AcMarche\Issep\Model;

use Carbon\Carbon;

class Indice
{
    public ?\DateTime $ts = null;
    public ?string $color = null;
    public ?string $label = null;
    public bool $isFixed = false;
    public ?int $originalValue = null;
    public ?int $networkId = null;

    public function __construct(public ?string $configId, public string $aqiValue, public string $pointName) {}

    public static function createFromStd(\stdClass $data): Indice
    {
        $indice = new Indice($data->configId, $data->aqiValue, $data->pointName);
        $ts = Carbon::parse($data->ts);
        $indice->ts = $ts->toDateTime();
        $indice->networkId = $data->networkId;

        return $indice;
    }

}