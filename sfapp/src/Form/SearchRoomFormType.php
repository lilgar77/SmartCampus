<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Floor;
use App\Entity\Room;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type for searching Room entities.
 *
 * This form allows searching for rooms based on the following filters:
 * - Room name (optional)
 * - Floor (optional)
 * - Building (optional)
 * The 'include_name' option determines whether the 'name' field is included in the form.
 *
 * @extends AbstractType<array<string, mixed>>
 */
class SearchRoomFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['include_name']) {
            // If 'include_name' option is true, add 'name', 'floor', and 'building' fields to the form
            $builder
                ->add('name', null, [
                    'required' => false // The name field is optional
                ])
                ->add('floor', EntityType::class, [
                    'class' => Floor::class, // Links to the Floor entity
                    'choice_label' => 'numberFloor', // Displays floor number in the choices
                    'placeholder' => 'Tous les étages', // Placeholder text for the floor field
                    'required' => false, // The floor field is optional
                ])
                ->add('building', EntityType::class, [
                    'class' => Building::class, // Links to the Building entity
                    'placeholder' => 'Tous les bâtiments', // Placeholder text for the building field
                    'required' => false, // The building field is optional
                ]);
        }
        else {
            // If 'include_name' option is false, only include 'floor' and 'building' fields
            $builder
                ->add('floor', EntityType::class, [
                    'class' => Floor::class, // Links to the Floor entity
                    'choice_label' => 'numberFloor', // Displays floor number in the choices
                ])
                ->add('building', EntityType::class, [
                    'class' => Building::class, // Links to the Building entity
                ]);
        }
    }

    /**
     * Configures the form options.
     *
     * The 'data_class' is set to the Room entity, meaning that the form is used to filter Room entities.
     * The 'method' is set to 'GET', indicating that the form will be used for search/filtering.
     * The 'include_name' option determines whether the 'name' field is included in the form.
     *
     * @param OptionsResolver $resolver The options resolver instance
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class, // Specifies that the form is for the Room entity
            'method' => 'GET', // The form will use GET method for filtering
            'include_name' => true, // Default value for 'include_name' is true
        ]);
    }
}