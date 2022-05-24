<?php

namespace AcMarche\Issep\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class StationDataSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
