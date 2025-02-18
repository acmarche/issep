<?php

namespace AcMarche\Issep\Model;

class Device
{
    public int $idConfiguration;
    public string $moment;
    public ?float $co;
    public float $no;
    public float $no2;
    public float $o3no2;
    public float $ppbno;
    public int $ppbnoStatut;
    public float $ppbno2;
    public int $ppbno2Statut;
    public float $ppbo3;
    public int $ppbo3Statut;
    public float $ugpcmno;
    public int $ugpcmnoStatut;
    public float $ugpcmno2;
    public int $ugpcmno2Statut;
    public float $ugpcmo3;
    public int $ugpcmo3Statut;
    public float $bmeT;
    public int $bmeTStatut;
    public float $bmePres;
    public int $bmePresStatut;
    public float $bmeRh;
    public int $bmeRhStatut;
    public float $pm1;
    public int $pm1Statut;
    public float $pm25;
    public int $pm25Statut;
    public float $pm4;
    public int $pm4Statut;
    public float $pm10;
    public int $pm10Statut;
    public float $vbat;
    public float $mwhBat;
    public int $mwhPv;
    public int $coRf;
    public float $noRf;
    public float $no2Rf;
    public float $o3no2Rf;
    public float $o3Rf;
    public float $pm10Rf;
    public int $vbatStatut;
    public int $idReseau;
    public int $userId;

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

}