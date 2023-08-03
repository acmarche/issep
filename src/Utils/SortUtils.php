<?php

namespace AcMarche\Issep\Utils;

class SortUtils
{
    public static function sortStations(array $stations): array
    {
        usort(
            $stations,
            fn($a, $b) => $a->nom <=> $b->nom
        );
        return $stations;
    }

    public static function sortByDate(array $indices, string $order = 'DESC')
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

    public static function filterByDate(array $indices, string $date): array
    {
        $data = [];
        foreach ($indices as $row) {
            if (str_contains((string) $row->ts, $date)) {
                $data[] = $row;
            }
        }

        return $data;
    }
}