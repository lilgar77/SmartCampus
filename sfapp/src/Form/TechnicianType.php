<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData
 */
class TechnicianType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('checkbox1', CheckboxType::class, [
                'label' => 'Option 1',
                'required' => false,
                'attr' => [
                    'style' => 'display: none;',
                ],
            ])
            ->add('checkbox2', CheckboxType::class, [
                'label' => 'Option 2',
                'required' => false,
                'attr' => [
                    'style' => 'display: none;',
                ],
            ])
            ->add('checkbox3', CheckboxType::class, [
                'label' => 'Option 3',
                'required' => false,
                'attr' => [
                    'style' => 'display: none;',
                ],
            ])
            ->add('checkbox4', CheckboxType::class, [
                'label' => 'Option 4',
                'required' => false,
                'attr' => [
                    'style' => 'display: none;',
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