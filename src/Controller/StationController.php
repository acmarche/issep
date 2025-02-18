<?php

namespace AcMarche\Issep\Controller;

use AcMarche\Issep\Form\StationDataSearchType;
use AcMarche\Issep\Indice\IndiceEnum;
use AcMarche\Issep\Indice\IndiceUtils;
use AcMarche\Issep\Model\Indice;
use AcMarche\Issep\Model\Station;
use AcMarche\Issep\Repository\StationRepository;
use AcMarche\Issep\Utils\FeuUtils;
use AcMarche\Issep\Utils\SortUtils;
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
    ) {}

    #[Route(path: '/', name: 'issep_home')]
    public function index(): Response
    {
        try {
            $stations = $this->stationRepository->getStations();
            $this->indiceUtils->setLastBelAqiOnStations($stations);
        } catch (\Exception $e) {
            $stations = $indices = [];
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

    #[Route(path: '/indice/{id}', name: 'issep_indice')]
    public function indice(int $id): Response
    {
        $station = $this->stationRepository->getStation($id);
        if (!$station instanceof Station) {
            $this->addFlash('danger', 'Station non trouvée');

            return $this->redirectToRoute('issep_home');
        }

        $colors = ['red' => '', 'yellow' => '', 'green' => ''];
        $this->indiceUtils->setLastBelAqiOnStations([$station]);

        if ($station->lastBelAqi instanceof Indice) {
            $this->indiceUtils->setColorOnIndice($station->lastBelAqi);
            $colorClass = FeuUtils::color($station->lastBelAqi->aqiValue);
            if (isset($colors[$colorClass])) {
                $colors[$colorClass] = $colorClass;
            }
        }

        try {
            $this->indiceUtils->setLastData([$station]);
        } catch (\DateMalformedStringException $e) {
        }

        return $this->render(
            '@AcMarcheIssep/station/indice.html.twig',
            [
                'station' => $station,
                'colors' => $colors,
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

    #[Route(path: '/data/{id}', name: 'issep_data')]
    public function data(Request $request, int $id): Response
    {
        $station = $this->stationRepository->getStation($id);
        if (!$station instanceof Station) {
            $this->addFlash('danger', 'Station non trouvée');

            return $this->redirectToRoute('issep_home');
        }

        $args = ['dateBegin' => new DateTime('-1 weeks'), 'dateEnd' => new DateTime()];
        $form = $this->createForm(StationDataSearchType::class, $args);

        $data = [];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            $dateBegin = $dataForm['dateBegin'];
            $dateEnd = $dataForm['dateEnd'];

            try {
                $data = $this->stationRepository->fetchStationData(
                    $station->idConfiguration,
                    $dateBegin->format('Y-m-d'),
                    $dateEnd->format('Y-m-d'),
                );
            } catch (Exception $exception) {
                $this->addFlash('danger', 'Erreur lors de la recherche: '.$exception->getMessage());
            }
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@AcMarcheIssep/station/data.html.twig',
            [
                'station' => $station,
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
        $indices = $this->stationRepository->getLastBelAquiByStation($station->idConfiguration);

        $indices = SortUtils::filterByDate($indices, $today);
        $this->indiceUtils->setColorOnAllIndices($indices);

        return $this->render(
            '@AcMarcheIssep/station/h24.html.twig',
            [
                'station' => $station,
                'indices' => $indices,
                'today' => $today,
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
