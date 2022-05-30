<?php

namespace AcMarche\Issep;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class AcMarcheIssepBundle extends  AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/packages/twig.php');
    }

}
