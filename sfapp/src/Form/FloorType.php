<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Floor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<array<string, mixed>>
 */
class FloorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 'numberFloor' field: Represents the floor number
            ->add('numberFloor', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le numéro d\'etage est obligatoire.',
                    ]),
                ],
            ])
            // 'IdBuilding' field: Represents the building the floor belongs to
            ->add('IdBuilding', EntityType::class, [
                'class' => Building::class, // Defines the related entity as Building
                'choice_label' => 'NameBuilding', // Displays the 'NameBuilding' property in the form
                'constraints' => [
                    new NotBlank([
                        'message' => 'Choisissez un bâtiment.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Floor::class, // Associates the form with the Floor entity class
        ]);
    }
}