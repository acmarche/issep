<?php

namespace AcMarche\Issep\Indice;

enum IndiceEnum: int
{
    case GOOD = 1;
    case FAIR = 2;
    case MODERATE = 3;
    case POOR = 4;
    case VERY_POOR = 5;
    case EXTREMELY_POOR = 6;
    case NO_DATA = 0;

    public function color(): string
    {
        return match ($this) {
            IndiceEnum::GOOD => '#138D75',
            IndiceEnum::FAIR => '#229954',
            IndiceEnum::MODERATE => '#D4AC0D',
            IndiceEnum::POOR => '#E67E22',
            IndiceEnum::VERY_POOR => '#D35400',
            IndiceEnum::EXTREMELY_POOR => '#E74C3C',
            IndiceEnum::NO_DATA => '#A6ACAF',
        };
    }

    public function label(): string
    {
        return match ($this) {
            IndiceEnum::GOOD => 'Bon',
            IndiceEnum::FAIR => 'Moyen',
            IndiceEnum::MODERATE => 'Dégradé',
            IndiceEnum::POOR => 'Mauvais',
            IndiceEnum::VERY_POOR => 'Très mauvais',
            IndiceEnum::EXTREMELY_POOR => 'Extrêmement mauvais',
            IndiceEnum::NO_DATA => 'Non valide',
        };
    }

    public static function colorByIndice(int $indice): IndiceEnum
    {
        return match ($indice) {
            1 => IndiceEnum::GOOD,
            2 => IndiceEnum::FAIR,
            3 => IndiceEnum::MODERATE,
            4 => IndiceEnum::POOR,
            5 => IndiceEnum::VERY_POOR,
            6 => IndiceEnum::EXTREMELY_POOR,
            default => IndiceEnum::NO_DATA,
        };
    }

    public static function labelByIndice(int $indice): string
    {
        return match ($indice) {
            1 => 'Bon',
            2 => 'Moyen',
            3 => 'Dégradé',
            4 => 'Mauvais',
            5 => 'Très mauvais',
            6 => 'Extrêmement mauvais',
            default => 'Non valide',
        };
    }
}
