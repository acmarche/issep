<?php

namespace AcMarche\Issep\Controller;

use AcMarche\Issep\Form\StationDataSearchType;
use AcMarche\Issep\Indice\IndiceEnum;
use AcMarche\Issep\Indice\IndiceUtils;
use AcMarche\Issep\Model\Station;
use AcMarche\Issep\Repository\StationRepository;
use AcMarche\Issep\Utils\FeuUtils;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/')]
#[IsGranted('ROLE_CAPTEUR')]
class StationController extends AbstractController
{
    public function __construct(
        private readonly StationRepository $stationRepository,
        private readonly IndiceUtils $indiceUtils,
    ) {
    }

    #[Route(path: '/', name: 'issep_home')]
    public function index(): Response
    {
        try {
            $stations = $this->stationRepository->getStations();
            $this->indiceUtils->setLastBelAqiOnStations($stations,true);
        } catch (\Exception $e) {
            $stations = [];
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->render(
            '@AcMarcheIssep/station/index.html.twig',
            [
                'stations' => $stations,
                'urlsExecuted' => $this->stationRepository->urlsExecuted,
            ],
        );
    }

    #[Route(path: '/config/{id}', name: 'issep_config')]
    public function config(int $id): Response
    {
        $station = $this->stationRepository->getStation($id);

        if (!$station instanceof Station) {
            $this->addFlash('danger', 'Station non trouvée');

            return $this->redirectToRoute('issep_home');
        }

        $config = $this->stationRepository->getConfig($station->idConfiguration);

        return $this->render(
            '@AcMarcheIssep/station/config.html.twig',
            [
                'station' => $station,
                'config' => $config,
                'urlsExecuted' => $this->stationRepository->urlsExecuted,
            ],
        );
    }

    #[Route(path: '/search', name: 'issep_search')]
    public function search(Request $request): Response
    {
        $args = ['dateBegin' => new DateTime('-4 days'), 'dateEnd' => new DateTime()];
        $form = $this->createForm(StationDataSearchType::class, $args);

        $data = [];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            $dateBegin = $dataForm['dateBegin'];
            $dateEnd = $dataForm['dateEnd'];
            $station = $dataForm['station'];

            try {
                $data = $this->stationRepository->fetchStationData(
                    $station,
                    $dateBegin->format('Y-m-d'),
                    $dateEnd->format('Y-m-d'),
                );
            } catch (Exception $exception) {
                $this->addFlash('danger', 'Erreur lors de la recherche: '.$exception->getMessage());
            }
        }
        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@AcMarcheIssep/station/search.html.twig',
            [
                'data' => $data,
                'urlsExecuted' => $this->stationRepository->urlsExecuted,
                'form' => $form,
                'search' => $form->isSubmitted(),
            ],
            $response,
        );
    }

    #[Route(path: '/map', name: 'issep_map')]
    public function map(): Response
    {
        $stations = $this->stationRepository->getStations();
        $this->indiceUtils->setLastBelAqiOnStations($stations);
        foreach ($stations as $station) {
            $station->color = FeuUtils::colorGrey();
            if ($station->lastBelAqi) {
                $station->color = FeuUtils::color($station->lastBelAqi->aqiValue);
            }
        }

        return $this->render(
            '@AcMarcheIssep/station/map.html.twig',
            [
                'stations' => $stations,
                'urlsExecuted' => $this->stationRepository->urlsExecuted,
            ],
        );
    }

    #[Route(path: '/h24/{id}', name: 'issep_h24')]
    public function h24(int $id): Response
    {
        $station = $this->stationRepository->getStation($id);
        if (!$station instanceof Station) {
            $this->addFlash('danger', 'Station non trouvée');

            return $this->redirectToRoute('issep_home');
        }

        $today = date('Y-m-d');
        $dateEnd = new DateTime();
        $dateEnd->modify('+1 day');

        $belAqi = $this->stationRepository->lastBelAqiByStation($station->idConfiguration);
        $this->indiceUtils->setColorOnIndice($belAqi);
        $belAqis = $this->stationRepository->belAqiByStation($station->idConfiguration);
        $this->indiceUtils->setColorOnAllIndices($belAqis);

        try {
            $data = $this->stationRepository->fetchStationData(
                $station->idConfiguration,
                $today,
                $dateEnd->format('Y-m-d')
            );
        } catch (\JsonException $e) {
            $this->addFlash('danger', 'Erreur lors de la recherche: '.$e->getMessage());
            $data = [];
        }

        return $this->render(
            '@AcMarcheIssep/station/h24.html.twig',
            [
                'station' => $station,
                'belAqi' => $belAqi,
                'belAqis' => $belAqis,
                'data' => $data,
                'urlsExecuted' => $this->stationRepository->urlsExecuted,
            ],
        );
    }

    #[Route(path: '/legend', name: 'issep_legend')]
    public function legend(): Response
    {
        return $this->render(
            '@AcMarcheIssep/station/legend.html.twig',
            [
                'indices' => IndiceEnum::cases(),
            ],
        );
    }
}
