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

/**
 * @extends AbstractType<array<string, mixed>>
 */
class RoomFormType extends AbstractType
{
    /**
     * @var AcquisitionSystemRepository Repository for the AcquisitionSystem entity
     */
    private AcquisitionSystemRepository $acquisitionSystemRepository;


    public function __construct(AcquisitionSystemRepository $acquisitionSystemRepository)
    {
        $this->acquisitionSystemRepository = $acquisitionSystemRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 'name' field: Represents the name of the room
            ->add('name', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'The name cannot be empty.',
                    ]),
                ],
            ])
            // 'id_AS' field: Represents the AcquisitionSystem associated with the room
            ->add('id_AS', EntityType::class, [
                'required' => false, // Makes the field optional
                'class' => AcquisitionSystem::class, // Specifies the related entity class
                'choice_label' => 'name', // Displays the 'name' property in the form
                'placeholder' => 'Choose a system', // Placeholder text
                'choices' => $this->acquisitionSystemRepository->findAvailableSystems(), // Fetches available systems from the repository
            ])
            // 'floor' field: Represents the floor where the room is located
            ->add('floor', EntityType::class, [
                'required' => true, // Makes the field mandatory
                'class' => Floor::class, // Specifies the related entity class
                'choice_label' => 'numberFloor', // Displays the 'numberFloor' property in the form
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select a floor.',
                    ]),
                ],
            ])
            // 'building' field: Represents the building where the room is located
            ->add('building', EntityType::class, [
                'required' => true, // Makes the field mandatory
                'class' => Building::class, // Specifies the related entity class
                'choice_label' => 'NameBuilding', // Displays the 'NameBuilding' property in the form
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select a building.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class, // Associates the form with the Room entity class
        ]);
    }
}