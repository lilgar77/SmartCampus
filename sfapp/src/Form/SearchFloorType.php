<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Floor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Form Type for searching Floor entities
 *
 * This form is used to search floors within a building. The 'IdBuilding' field
 * allows the user to filter the floors based on a specific building.
 * The field is optional, and by default, it shows all buildings.
 *
 * @extends AbstractType<array<string, mixed>>
 */
class SearchFloorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 'IdBuilding' field: Allows filtering floors by building
            ->add('IdBuilding', EntityType::class, [
                'class' => Building::class,  // Links to the Building entity
                'placeholder' => 'Tous les bâtiments', // Default option: All buildings
                'required' => false, // Makes the field optional
            ]);
    }

    /**
     * Configures the form options.
     *
     * This method sets the default options for the form, including the
     * 'data_class' which is set to 'Floor::class' as the form relates to the
     * Floor entity. It also sets the 'method' to 'GET' to indicate that the
     * form will be used for filtering data through URL query parameters.
     *
     * @param OptionsResolver $resolver The options resolver instance
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Floor::class, // Specifies the entity the form relates to
            'method' => 'GET', // Specifies that the form will use GET method for filtering
        ]);
    }
}