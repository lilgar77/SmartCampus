<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Floor;
use App\Entity\Room;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<array<string, mixed>>
 */
class SearchRoomFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['include_name']) {
            $builder
                ->add('name', null, [
                'required' => false
                ])
                ->add('floor', EntityType::class, [
                    'class' => Floor::class,
                    'choice_label' => 'numberFloor',
                    'placeholder' => 'Tous les étages',
                    'required' => false,
                ])
                ->add('building', EntityType::class, [
                    'class' => Building::class,
                    'placeholder' => 'Tous les bâtiments',
                    'required' => false,
                ]);
        }
        else{
            $builder
                ->add('floor', EntityType::class, [
                    'class' => Floor::class,
                    'choice_label' => 'numberFloor',
                ])
                ->add('building', EntityType::class, [
                    'class' => Building::class,
                ]);
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
            'method' => 'GET',
            'include_name' => true,
        ]);
    }
}
