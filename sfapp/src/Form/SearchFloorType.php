<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Floor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * @extends AbstractType<array<string, mixed>>
 */
class SearchFloorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('IdBuilding', EntityType::class, [
                'class' => Building::class,
                'placeholder' => 'Tous les bâtiments',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Floor::class,
            'method' => 'GET',
        ]);
    }
}
