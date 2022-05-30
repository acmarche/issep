<?php

namespace AcMarche\Issep\Controller;

use AcMarche\Issep\Form\StationDataSearchType;
use AcMarche\Issep\Indice\Indice;
use AcMarche\Issep\Repository\StationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/')]
#[IsGranted(data: 'ROLE_CAPTEUR')]
class StationController extends AbstractController
{
    private StationRepository $stationRepository;

    public function __construct()
    {
        $this->stationRepository = new StationRepository();
    }

    #[Route(path: '/', name: 'issep_home')]
    public function index(): Response
    {
        $stations = $this->stationRepository->getStations();
        $indices = $this->stationRepository->getIndices();
        array_map(function ($station) use ($indices) {
            $data = $this->stationRepository->getIndice($station->id_configuration, $indices);
            $indice = Indice::colorByIndice($data->aqi_value);
            $station->indice = $indice;
        }, $stations);

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
        $data = $this->stationRepository->getIndice($station->id_configuration);
        $indice = Indice::colorByIndice($data->aqi_value);
        $urlExecuted = $this->stationRepository->urlExecuted;
        $colors = ['red' => '', 'yellow' => '', 'green' => ''];
        if (isset($colors[$indice->color()])) {
            $colors[$indice->color()] = $indice->color();
        }
        dump($colors);

        return $this->render(
            '@AcMarcheIssep/station/indice.html.twig',
            [
                'station' => $station,
                'indice' => $indice,
                'data' => $data,
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
}