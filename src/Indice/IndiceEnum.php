<?php

namespace AcMarche\Issep\Indice;

enum IndiceEnum: int
{
    case EXCELLENT = 1;
    case VERY_GOOD = 2;
    case GOOD = 3;
    case FAIRLY_GOOD = 4;
    case AVERAGE = 5;
    case INSUFFICIENT = 6;
    case QUITE_POOR = 7;
    case POOR = 8;
    case VERY_POOR = 9;
    case APPALLING = 10;
    case NO_DATA = 0;

    public function color(): string
    {
        return match ($this) {
            IndiceEnum::EXCELLENT => '#0000FF ',
            IndiceEnum::VERY_GOOD => '#00BFFF ',
            IndiceEnum::GOOD => '#00FF00',
            IndiceEnum::FAIRLY_GOOD => '#ADFF2F ',
            IndiceEnum::AVERAGE => '#FFFF00',
            IndiceEnum::INSUFFICIENT => '#FFA500',
            IndiceEnum::QUITE_POOR => '#FF4500',
            IndiceEnum:: POOR => '#FF0000',
            IndiceEnum::VERY_POOR => '#8B0000',
            IndiceEnum::APPALLING => '#8B008B',
            IndiceEnum::NO_DATA => '#A6ACAF',
        };
    }

    public function label(): string
    {
        return match ($this) {
            IndiceEnum::EXCELLENT => 'Excellent',
            IndiceEnum::VERY_GOOD => 'Très bon',
            IndiceEnum::GOOD => 'Bien',
            IndiceEnum::FAIRLY_GOOD => 'Assez bon',
            IndiceEnum::AVERAGE => 'Moyen',
            IndiceEnum::INSUFFICIENT => 'Insuffisant',
            IndiceEnum::QUITE_POOR => 'Assez mauvais',
            IndiceEnum::POOR => 'Mauvais',
            IndiceEnum::VERY_POOR => 'Très mauvais',
            IndiceEnum::APPALLING => 'Exécrable',
            IndiceEnum::NO_DATA => 'Non valide',
        };
    }

    public static function colorByIndice(int $indice): string
    {
        return match ($indice) {
            1 => IndiceEnum::EXCELLENT->color(),
            2 => IndiceEnum::VERY_GOOD->color(),
            3 => IndiceEnum::GOOD->color(),
            4 => IndiceEnum::FAIRLY_GOOD->color(),
            5 => IndiceEnum::AVERAGE->color(),
            6 => IndiceEnum::INSUFFICIENT->color(),
            7 => IndiceEnum::QUITE_POOR->color(),
            8 => IndiceEnum::POOR->color(),
            9 => IndiceEnum::VERY_POOR->color(),
            10 => IndiceEnum::APPALLING->color(),
            default => IndiceEnum::NO_DATA->color(),
        };
    }

    public static function labelByIndice(int $indice): string
    {
        return match ($indice) {
            1 => IndiceEnum::EXCELLENT->label(),
            2 => IndiceEnum::VERY_GOOD->label(),
            3 => IndiceEnum::GOOD->label(),
            4 => IndiceEnum::FAIRLY_GOOD->label(),
            5 => IndiceEnum::AVERAGE->label(),
            6 => IndiceEnum::INSUFFICIENT->label(),
            7 => IndiceEnum::QUITE_POOR->label(),
            8 => IndiceEnum::POOR->label(),
            9 => IndiceEnum::VERY_POOR->label(),
            10 => IndiceEnum::APPALLING->label(),
            default => IndiceEnum::NO_DATA->label(),
        };
    }
}
