<?php

namespace App\Form;

use App\Entity\AcquisitionSystem;
use App\Entity\Floor;
use App\Entity\Room;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Building;

class RoomFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('id_AS', EntityType::class, [
                'class' => AcquisitionSystem::class,
                'choice_label' => 'mac_adress',
            ])
            ->add('floor', EntityType::class, [
                'class' => Floor::class,
                'choice_label' => 'numberFloor',
            ])
            ->add('building', EntityType::class, [
                'class' => Building::class,
                'choice_label' => 'NameBuilding',
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
