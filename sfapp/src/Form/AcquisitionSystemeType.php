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
            ->add('name', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom est obligatoire.',
                    ],) ],
                ])
            ->add('wording')
            ->add('macAdress', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'L\'adresse MAC est obligatoire.',
                    ]),
                    new Regex([
                        'pattern' => '/^([0-9A-Fa-f]{2}([-:])){5}([0-9A-Fa-f]{2})$/',
                        'message' => 'L\'adresse MAC doit être au format valide (exemple : 01:23:45:67:89:AB ou 01-23-45-67-89-AB).',
                    ]),
                ],
                'label' => 'Adresse MAC',
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Dispo' => EtatAS::Disponible,
                    'Désinstaller' => EtatAS::A_Desinstaller,
                    'Installer' => EtatAS::Installer,
                    'À réparer' => EtatAS::A_Reparer,
                    'En cours d\'installation' => EtatAS::En_Installation,
                ],
                'choice_label' => function (EtatAS $choice): string {
                    return (string) ($choice->name ?? '');
                },
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
