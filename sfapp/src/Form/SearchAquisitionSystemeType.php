<?php

namespace App\Form;

use App\Model\EtatAS;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<array<string, mixed>>
 */
class SearchAquisitionSystemeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 'etat' field: Allows the user to filter acquisition systems by their state
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Tout les états' => null, // Option for no state filter
                    'Disponible' => EtatAS::Disponible,
                    'A désinstaller' => EtatAS::A_Desinstaller,
                    'Installé' => EtatAS::Installer,
                    'A réparer' => EtatAS::A_Reparer,
                    'En cours d\'installation' => EtatAS::En_Installation,
                ],
            ])
            // 'Name' field: Allows the user to search acquisition systems by name
            ->add('Name', TextType::class, [
                'required' => false, // Makes the field optional
                'attr' => [
                    'class' => 'form-control', // CSS class for styling the input field
                    'placeholder' => 'Cherchez par nom...', // Placeholder text
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, // No direct entity association for this form
        ]);
    }
}