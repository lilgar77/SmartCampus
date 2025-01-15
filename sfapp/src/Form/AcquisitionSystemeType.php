<?php

namespace App\Form;

use App\Entity\AcquisitionSystem;
use App\Model\EtatAS;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<array<string, mixed>>
 */
class AcquisitionSystemeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 'name' field: A required field with a NotBlank constraint
            ->add('name', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'The name is required.',
                    ]),
                ]
            ])
            // 'wording' field: An optional text field for additional information
            ->add('wording')
            // 'macAdress' field: A required field with MAC address format validation
            ->add('macAdress', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'The MAC address is required.',
                    ]),
                    new Regex([
                        'pattern' => '/^([0-9A-Fa-f]{2}([-:])){5}([0-9A-Fa-f]{2})$/',
                        'message' => 'The MAC address must be in a valid format (example: 01:23:45:67:89:AB or 01-23-45-67-89-AB).',
                    ]),
                ],
                'label' => 'MAC Address', // Label for the MAC address field
            ])
            // 'etat' field: A dropdown for the system's state, with predefined choices
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Available' => EtatAS::Disponible, // Available state
                    'To uninstall' => EtatAS::A_Desinstaller, // To uninstall state
                    'To install' => EtatAS::Installer, // To install state
                    'To repair' => EtatAS::A_Reparer, // To repair state
                    'In installation' => EtatAS::En_Installation, // In installation state
                ],
                'choice_label' => function (EtatAS $choice): string {
                    return (string) ($choice->name ?? ''); // Displays the name of each state
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AcquisitionSystem::class, // Associates the form with the AcquisitionSystem entity class
        ]);
    }
}
