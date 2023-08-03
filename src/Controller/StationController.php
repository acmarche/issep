<?php

namespace AcMarche\Issep\Controller;

use stdClass;
use DateTime;
use Exception;
use AcMarche\Issep\Form\StationDataSearchType;
use AcMarche\Issep\Indice\IndiceEnum;
use AcMarche\Issep\Indice\IndiceUtils;
use AcMarche\Issep\Repository\StationRepository;
use AcMarche\Issep\Utils\FeuUtils;
use AcMarche\Issep\Utils\SortUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/')]
#[IsGranted('ROLE_CAPTEUR')]
class StationController extends AbstractController
{
    public function __construct(private readonly StationRepository $stationRepository, private readonly IndiceUtils $indiceUtils)
    {
    }

    #[Route(path: '/', name: 'issep_home')]
    public function index(): Response
    {
        $stations = $this->stationRepository->getStations();
        $indices = $this->stationRepository->getIndices();
        $this->indiceUtils->setIndices($stations, $indices);

        return $this->render(
            '@AcMarcheIssep/station/index.html.twig',
            [
                'stations' => $stations,
                'urlsExecuted' => $this->stationRepository->urlsExecuted,
            ]
        );
    }

    #[Route(path: '/indice/{id}', name: 'issep_indice')]
    public function indice(string $id): Response
    {
        $station = $this->stationRepository->getStation($id);
        if (!$station instanceof stdClass) {
            $this->addFlash('danger', 'Station non trouvée');

            return $this->redirectToRoute('issep_home');
        }

        $indices = $this->stationRepository->getIndicesByStation($station->id_configuration);
        $this->indiceUtils->setIndicesEnum($indices);

        $lastIndice = null;
        $colors = ['red' => '', 'yellow' => '', 'green' => ''];
        if ($indices !== []) {
            $lastIndice = $indices[0];
            $colorClass = FeuUtils::color($lastIndice->aqi_value);
            if (isset($colors[$colorClass])) {
                $colors[$colorClass] = $colorClass;
            }
        }

        return $this->render(
            '@AcMarcheIssep/station/indice.html.twig',
            [
                'station' => $station,
                'lastIndice' => $lastIndice,
                'indices' => $indices,
                'colors' => $colors,
                'urlsExecuted' => $this->stationRepository->urlsExecuted,
            ]
        );
    }

    #[Route(path: '/config/{id}', name: 'issep_config')]
    public function config(string $id): Response
    {
        $station = $this->stationRepository->getStation($id);

        if (!$station instanceof stdClass) {
            $this->addFlash('danger', 'Station non trouvée');

            return $this->redirectToRoute('issep_home');
        }

        $config = $this->stationRepository->getConfig($station->id_configuration);

        return $this->render(
            '@AcMarcheIssep/station/config.html.twig',
            [
                'station' => $station,
                'config' => $config,
                'urlsExecuted' => $this->stationRepository->urlsExecuted,
            ]
        );
    }

    #[Route(path: '/data/{id}', name: 'issep_data')]
    public function data(Request $request, string $id): Response
    {
        $args = ['dateBegin' => new DateTime('-2 weeks'), 'dateEnd' => new DateTime()];

        $station = $this->stationRepository->getStation($id);
        if (!$station instanceof stdClass) {
            $this->addFlash('danger', 'Station non trouvée');

            return $this->redirectToRoute('issep_home');
        }

        $form = $this->createForm(StationDataSearchType::class, $args);

        $data = [];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            $dateBegin = $dataForm['dateBegin'];
            $dateEnd = $dataForm['dateEnd'];
            try {
                $data = $this->stationRepository->fetchStationData(
                    $station->id_configuration,
                    $dateBegin->format('Y-m-d'),
                    $dateEnd->format('Y-m-d')
                );
            } catch (Exception $exception) {
                $this->addFlash('danger', 'Erreur lors de la recherche: ' . $exception->getMessage());
            }
        }

        return $this->render(
            '@AcMarcheIssep/station/data.html.twig',
            [
                'station' => $station,
                'data' => $data,
                'urlsExecuted' => $this->stationRepository->urlsExecuted,
                'form' => $form->createView(),
                'search' => $form->isSubmitted(),
            ]
        );
    }

    #[Route(path: '/map', name: 'issep_map')]
    public function map(): Response
    {
        $stations = $this->stationRepository->getStations();
        $indices = $this->stationRepository->getIndices();
        $this->indiceUtils->setIndices($stations, $indices);
        foreach ($stations as $station) {
            $station->color = FeuUtils::colorGrey();
            if ($station->last_indice) {
                $station->color = FeuUtils::color($station->last_indice->aqi_value);
            }
        }

        return $this->render(
            '@AcMarcheIssep/station/map.html.twig',
            [
                'stations' => $stations,
                'urlsExecuted' => $this->stationRepository->urlsExecuted,
            ]
        );
    }

    #[Route(path: '/h24/{id}', name: 'issep_h24')]
    public function h24(string $id): Response
    {
        $station = $this->stationRepository->getStation($id);
        if (!$station instanceof stdClass) {
            $this->addFlash('danger', 'Station non trouvée');

            return $this->redirectToRoute('issep_home');
        }

        $today = date('Y-m-d');
        $indices = $this->stationRepository->getIndicesByStation($station->id_configuration);
        $indices = SortUtils::filterByDate($indices, $today);
        $this->indiceUtils->setIndicesEnum($indices);

        return $this->render(
            '@AcMarcheIssep/station/h24.html.twig',
            [
                'station' => $station,
                'indices' => $indices,
                'urlsExecuted' => $this->stationRepository->urlsExecuted,
            ]
        );
    }

    #[Route(path: '/legend', name: 'issep_legend')]
    public function legend(): Response
    {
        return $this->render(
            '@AcMarcheIssep/station/legend.html.twig',
            [
                'indices' => IndiceEnum::cases(),
            ]
        );
    }
}
