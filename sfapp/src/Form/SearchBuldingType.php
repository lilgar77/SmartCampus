<?php

namespace App\Form;

use App\Entity\Building;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * @extends AbstractType<array<string, mixed>>
 */
class SearchBuldingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 'NameBuilding' field: Allows searching for buildings by their name
            ->add('NameBuilding', TextType::class, [
                'required' => false, // Makes the field optional
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Building::class, // Specifies the entity the form relates to
        ]);
    }
}