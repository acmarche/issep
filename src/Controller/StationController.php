<?php

namespace AcMarche\Issep\Controller;

use AcMarche\Issep\Form\StationDataSearchType;
use AcMarche\Issep\Indice\IndiceEnum;
use AcMarche\Issep\Indice\IndiceUtils;
use AcMarche\Issep\Repository\StationRepository;
use AcMarche\Issep\Utils\FeuUtils;
use AcMarche\Issep\Utils\SortUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/')]
#[IsGranted(data: 'ROLE_CAPTEUR')]
class StationController extends AbstractController
{
    public function __construct(private StationRepository $stationRepository, private IndiceUtils $indiceUtils)
    {
    }

    #[Route(path: '/', name: 'issep_home')]
    public function index(): Response
    {
        $stations = $this->stationRepository->getStations();
        $indices = $this->stationRepository->getIndices();
        $this->indiceUtils->setIndices($stations, $indices);
        $urlExecuted = $this->stationRepository->urlExecuted;

        return $this->render(
            '@AcMarcheIssep/station/index.html.twig',
            [
                'stations' => $stations,
                'urlExecuted' => $urlExecuted,
            ]
        );
    }

    #[Route(path: '/indice/{id}', name: 'issep_indice')]
    public function indice(string $id): Response
    {
        $station = $this->stationRepository->getStation($id);
        if (!$station) {
            $this->addFlash('danger', 'Station non trouvée');

            return $this->redirectToRoute('issep_home');
        }

        $indices = $this->stationRepository->getIndicesByStation($station->id_configuration);
        $this->indiceUtils->setIndicesEnum($indices);

        $lastIndice = null;
        $colors = ['red' => '', 'yellow' => '', 'green' => ''];
        if (count($indices) > 0) {
            $lastIndice = $indices[0];
            $colorClass = FeuUtils::color($lastIndice->aqi_value);
            if (isset($colors[$colorClass])) {
                $colors[$colorClass] = $colorClass;
            }
        }
        $urlExecuted = $this->stationRepository->urlExecuted;

        return $this->render(
            '@AcMarcheIssep/station/indice.html.twig',
            [
                'station' => $station,
                'lastIndice' => $lastIndice,
                'indices' => $indices,
                'colors' => $colors,
                'urlExecuted' => $urlExecuted,
            ]
        );
    }

    #[Route(path: '/config/{id}', name: 'issep_config')]
    public function config(string $id): Response
    {
        $station = $this->stationRepository->getStation($id);
        if (!$station) {
            $this->addFlash('danger', 'Station non trouvée');

            return $this->redirectToRoute('issep_home');
        }
        $config = $this->stationRepository->getConfig($station->id_configuration);
        $urlExecuted = $this->stationRepository->urlExecuted;

        return $this->render(
            '@AcMarcheIssep/station/config.html.twig',
            [
                'station' => $station,
                'config' => $config,
                'urlExecuted' => $urlExecuted,
            ]
        );
    }

    #[Route(path: '/data/{id}', name: 'issep_data')]
    public function data(Request $request, string $id): Response
    {
        $args = ['dateBegin' => new \DateTime('-2 weeks'), 'dateEnd' => new \DateTime()];

        $station = $this->stationRepository->getStation($id);
        if (!$station) {
            $this->addFlash('danger', 'Station non trouvée');

            return $this->redirectToRoute('issep_home');
        }

        $form = $this->createForm(StationDataSearchType::class, $args);

        $data = [];
        $urlExecuted = null;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            $dateBegin = $dataForm['dateBegin'];
            $dateEnd = $dataForm['dateEnd'];
            try {
                $data = $this->stationRepository->fetchStationData(
                    $id,
                    $dateBegin->format('Y-m-d'),
                    $dateEnd->format('Y-m-d')
                );
                $urlExecuted = $this->stationRepository->urlExecuted;
            } catch (\Exception $exception) {
                $this->addFlash('danger', 'Erreur lors de la recherche: '.$exception->getMessage());
            }
        }

        return $this->render(
            '@AcMarcheIssep/station/data.html.twig',
            [
                'station' => $station,
                'data' => $data,
                'urlExecuted' => $urlExecuted,
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
        $this->indiceUtils->setColors($stations, $indices);

        return $this->render(
            '@AcMarcheIssep/station/map.html.twig',
            [
                'stations' => $stations,
            ]
        );
    }

    #[Route(path: '/h24/{id}', name: 'issep_h24')]
    public function h24(string $id): Response
    {
        $station = $this->stationRepository->getStation($id);
        if (!$station) {
            $this->addFlash('danger', 'Station non trouvée');

            return $this->redirectToRoute('issep_home');
        }

        $today = date('Y-m-d');
        $indices = $this->stationRepository->getIndicesByStation($station->id_configuration);
        $indices = SortUtils::filterByDate($indices, $today);
        $this->indiceUtils->setIndicesEnum($indices);
        $urlExecuted = $this->stationRepository->urlExecuted;

        return $this->render(
            '@AcMarcheIssep/station/h24.html.twig',
            [
                'station' => $station,
                'indices' => $indices,
                'urlExecuted' => $urlExecuted,
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
