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
            ])
            ->add('checkbox2', CheckboxType::class, [
                'label' => 'Option 2',
                'required' => false,
            ])
            ->add('checkbox3', CheckboxType::class, [
                'label' => 'Option 3',
                'required' => false,
            ])
            ->add('checkbox4', CheckboxType::class, [
                'label' => 'Option 4',
                'required' => false,
            ])
            ->add('checkbox5', CheckboxType::class, [
                'label' => 'Option 5',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}