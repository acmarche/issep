<?php

namespace AcMarche\Issep\Indice;

enum Indice
{
    case GOOD;
    case FAIR;
    case MODERATE;
    case POOR;
    case VERY_POOR;
    case EXTREMELY_POOR;
    case NO_DATA;

    public function color(): string
    {
        return match ($this) {
            Indice::GOOD => 'green',
            Indice::FAIR => 'green',
            Indice::MODERATE => 'yellow',
            Indice::POOR => 'yellow',
            Indice::VERY_POOR => 'red',
            Indice::EXTREMELY_POOR => 'red',
            Indice::NO_DATA => 'blue',
        };
    }

    public static function colorByIndice(int $indice): Indice
    {
        return match ($indice) {
            1 => Indice::GOOD,
            2 => Indice::FAIR,
            3 => Indice::MODERATE,
            4 => Indice::POOR,
            5 => Indice::VERY_POOR,
            6 => Indice::EXTREMELY_POOR,
            default => Indice::NO_DATA,
        };
    }
}