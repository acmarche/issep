<?php

use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig) {
    $twig
        ->path('%kernel.project_dir%/src/AcMarche/Issep/templates', 'AcMarcheIssep');
};
