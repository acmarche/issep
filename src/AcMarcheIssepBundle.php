<?php

namespace AcMarche\Issep;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcMarcheIssepBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
