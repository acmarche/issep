<?php

namespace AcMarche\Issep\Controller;

use AcMarche\Issep\Repository\StationRemoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/brut')]
#[IsGranted('ROLE_CAPTEUR')]
class BrutController extends AbstractController
{
    public function __construct(
        private readonly StationRemoteRepository $stationRemoteRepository,
    ) {
    }

    #[Route(path: '/last', name: 'issep_brut')]
    public function index(): Response
    {
        $stations = $this->stationRemoteRepository->fetchStations();
        $indices = $this->stationRemoteRepository->lastBelAqui();

        return $this->render(
            '@AcMarcheIssep/indice/brut.html.twig',
            [
                'indices' => json_decode($indices),
                'stations' => json_decode($stations),
            ],
        );
    }
}
