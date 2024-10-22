<?php

namespace AcMarche\Issep\Repository;

enum StationsEnum: int
{
    case CHAUSSEE_LIEGE = 15;
    case RUE_NERETTE = 17;
    case BRETELLE_N4 = 18;
    case BOULEVARD_NORD = 26;
    case AVENUE_FRANCE = 27;
    case CONTOURNEMENT_N839 = 30;
    case CIMETIERE_AYE = 66;
    case SINSIN = 1023;

    public static function stationsToKeep(): array
    {
        $stations = [];
        foreach (self::cases() as $station) {
            if ($station->value === 1023) {
                continue;
            }
            $stations[] = $station->value;
        }

        return $stations;
    }
}
