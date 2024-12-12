<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TechnicianType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('checkbox1', CheckboxType::class, [
                'label' => 'Option 1',
                'required' => true,
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => 'Vous devez cocher toutes les cases pour valider.',
                    ]),
                ],
            ])
            ->add('checkbox2', CheckboxType::class, [
                'label' => 'Option 2',
                'required' => true,
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => 'Vous devez cocher toutes les cases pour valider.',
                    ]),
                ],
            ])
            ->add('checkbox3', CheckboxType::class, [
                'label' => 'Option 3',
                'required' => true,
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => 'Vous devez cocher toutes les cases pour valider.',
                    ]),
                ],
            ])
            ->add('checkbox4', CheckboxType::class, [
                'label' => 'Option 4',
                'required' => true,
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => 'Vous devez cocher toutes les cases pour valider.',
                    ]),
                ],
            ])
            ->add('checkbox5', CheckboxType::class, [
                'label' => 'Option 5',
                'required' => true,
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => 'Vous devez cocher toutes les cases pour valider.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}