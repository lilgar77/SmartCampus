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
 * @extends AbstractType<\App\Entity\Floor>
 */
class FloorType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numberFloor', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le numéro de l\'étage ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('IdBuilding', EntityType::class, [
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
            'data_class' => Floor::class,
        ]);
    }
}
