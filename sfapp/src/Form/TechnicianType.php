<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TechnicianType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('checkbox1', CheckboxType::class, [
        ])
            ->add('checkbox2', CheckboxType::class, [
            ])
            ->add('checkbox3', CheckboxType::class, [
            ])
            ->add('checkbox4', CheckboxType::class, [
            ])
            ->add('checkbox5', CheckboxType::class, [
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
