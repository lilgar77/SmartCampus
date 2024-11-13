<?php

namespace App\Form;

use App\Entity\AcquisitionSystem;
use App\Entity\Room;
use App\Model\EtatAS;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AcquisitionSystemeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('temperature')
            ->add('CO2')
            ->add('humidity')
            ->add('wording')
            ->add('macAdress')
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Dispo' => EtatAS::AVAILABLE,
                    'désinstallé' => EtatAS::UNINSTALL,
                    'installé' => EtatAS::INSTALL,
                    'À réparer' => EtatAS::REPAIRED,
                ],
                'choice_label' => function($choice) {
                    return $choice->name;
                },
            ])
            ->add('room', EntityType::class, [
                'class' => Room::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AcquisitionSystem::class,
        ]);
    }
}
