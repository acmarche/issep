<?php

namespace AcMarche\Issep\Controller;

use AcMarche\Issep\Repository\StationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/indices')]
#[IsGranted('ROLE_CAPTEUR')]
class IndiceController extends AbstractController
{
    public function __construct(
        private readonly StationRepository $stationRepository,
    ) {}

    #[Route(path: '/last', name: 'issep_indice_last')]
    public function index(): Response
    {
        $this->stationRepository->lastBelAqui();
        $indices = $this->stationRepository->lastBelAqui;

        return $this->render(
            '@AcMarcheIssep/indice/last.html.twig',
            [
                'indices' => $indices,
                'urlsExecuted' => $this->stationRepository->urlsExecuted,
            ],
        );
    }
}
