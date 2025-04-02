<?php

namespace AcMarche\Issep\Form;

use AcMarche\Issep\Model\Station;
use AcMarche\Issep\Repository\StationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class StationDataSearchType extends AbstractType
{
    public function __construct(private readonly StationRepository $stationRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $stations = $this->stationRepository->getStations();
        $choices = [];
        foreach ($stations as $station) {
            $choices[$station->nom] = $station->idConfiguration;
        }
        $builder
            ->add(
                'station',
                ChoiceType::class,
                [
                    'label' => 'Station',
                    'choices' => $choices,
                    'required' => true,
                ]
            )
            ->add(
                'dateBegin',
                DateType::class,
                [
                    'label' => 'Date de début',
                    'required' => true,
                ]
            )
            ->add(
                'dateEnd',
                DateType::class,
                [
                    'label' => 'Date de fin',
                    'required' => true,
                ]
            );
    }
}
