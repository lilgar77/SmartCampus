<?php

namespace App\Form;

use App\Entity\AcquisitionSystem;
use App\Model\EtatAS;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchAquisitionSystemeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Etat' => null,
                    'Disponible' => EtatAS::Disponible,
                    'Désinstaller' => EtatAS::A_Desinstaller,
                    'Installer' => EtatAS::Installer,
                    'À réparer' => EtatAS::A_Reparer,
                    'En cours d\'installation' => EtatAS::En_Installation,
                ],
            ])
            ->add('Room', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Rechercher une salle...',
                ],
            ])
            ->add('Name', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Rechercher par nom...',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, // Pas de lien direct avec une entité pour ce formulaire
        ]);
    }
}
