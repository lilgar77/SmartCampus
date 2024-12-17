<?php

namespace App\Form;

use App\Entity\AcquisitionSystem;
use App\Entity\Floor;
use App\Entity\Room;
use App\Repository\AcquisitionSystemRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Building;
use Symfony\Component\Validator\Constraints\NotBlank;


class RoomFormType extends AbstractType
{

    private AcquisitionSystemRepository $acquisitionSystemRepository;

    public function __construct(AcquisitionSystemRepository $acquisitionSystemRepository)
    {
        $this->acquisitionSystemRepository = $acquisitionSystemRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('id_AS', EntityType::class, [
                'required' => false,
                'class' => AcquisitionSystem::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez un système',
                'choices' => $this->acquisitionSystemRepository->findAvailableSystems(),
            ])
            ->add('floor', EntityType::class, [
                'required' => true,
                'class' => Floor::class,
                'choice_label' => 'numberFloor',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un étage.',
                    ]),
                ],
            ])
            ->add('building', EntityType::class, [
                'required' => true,
                'class' => Building::class,
                'choice_label' => 'NameBuilding',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un bâtiment.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
