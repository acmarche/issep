<?php

namespace AcMarche\Issep\Utils;

use AcMarche\Issep\Model\Indice;
use AcMarche\Issep\Model\Station;

class SortUtils
{
    /**
     * @param Station[] $stations
     * @return Station[]
     */
    public static function sortStations(array $stations): array
    {
        usort(
            $stations,
            fn ($a, $b) => $a->nom <=> $b->nom
        );
        return $stations;
    }

    /**
     * @param Indice[] $indices
     * @param string $order
     * @return Indice[]
     */
    public static function sortByDate(array $indices, string $order = 'DESC'): array
    {
        usort(
            $indices,
            function ($indiceA, $indiceB) use ($order) {
                $dateA = $indiceA->ts;
                $dateB = $indiceB->ts;

                if ($order == 'ASC') {
                    return $dateA <=> $dateB;
                } else {
                    return $dateB <=> $dateA;
                }
            }
        );

        return $indices;
    }

    /**
     * @param Indice[] $indices
     * @param string $date
     * @return Indice[]
     */
    public static function filterByDate(array $indices, string $date): array
    {
        $data = [];
        foreach ($indices as $row) {
            if (str_contains($row->ts->format('Y-m-d'), $date)) {
                $data[] = $row;
            }
        }

        return $data;
    }
}
